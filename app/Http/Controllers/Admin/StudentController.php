<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean'
        ]);

        $validated['role'] = 'student';
        $validated['password'] = Hash::make($request->password);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $student)
    {
        // 1. Online Status
        $isOnline = false;
        if ($student->last_seen_at) {
            $isOnline = \Carbon\Carbon::parse($student->last_seen_at)->gt(now()->subMinutes(5));
        }

        // 2. Study Time Stats
        $totalStudySeconds = \Illuminate\Support\Facades\DB::table('study_sessions')
            ->where('user_id', $student->id)
            ->sum('duration_seconds');
        
        $totalHours = round($totalStudySeconds / 3600, 1);

        // 3. Exam Marks
        // Physical
        $physicalMarks = \App\Models\PhysicalExamMark::where('user_id', $student->id)
            ->get()
            ->map(function ($mark) {
                return [
                    'type' => 'Physical',
                    'title' => $mark->exam_name,
                    'date' => $mark->exam_date,
                    'score' => $mark->marks_obtained, // Total
                    'mcq' => $mark->mcq_marks,
                    'essay' => $mark->essay_marks,
                    'total_possible' => $mark->total_marks,
                    'percentage' => $mark->total_marks > 0 ? ($mark->marks_obtained / $mark->total_marks) * 100 : 0,
                ];
            });

        // Online
        $onlineMarks = \App\Models\ExamAttempt::with('exam')
            ->where('user_id', $student->id)
            ->whereNotNull('submitted_at')
            ->get()
            ->map(function ($attempt) {
                $maxScore = $attempt->exam->questions()->sum('marks'); // Calculate if not stored
                // Note: Ideally maxScore should be stored on attempt or exam snapshot to avoid query heavy logic loop
                // For now, assuming $attempt->exam->questions is too heavy, let's just use what we have or a placeholder if expensive
                // Optimized: We can assume percentage is score (if score created as percentage) OR calculate if we trust relationship.
                // Let's rely on attempt->score for now.
                
                return [
                    'type' => 'Online',
                    'title' => $attempt->exam->title ?? 'Online Exam',
                    'date' => $attempt->submitted_at,
                    'score' => $attempt->score,
                    'mcq' => $attempt->score, // Online treated as MCQ usually
                    'essay' => '-',
                    'total_possible' => '-', // Dynamic
                    'percentage' => $attempt->score, // Assuming score is stored? Or need calc. 
                    // Let's refine: Result calculation usually happens at submit. 
                    // Let's assume attempt store raw score.
                ];
            });

        // Merge & Sort
        $allMarks = $physicalMarks->concat($onlineMarks)->sortByDesc('date');

        return view('admin.students.show', compact('student', 'isOnline', 'totalHours', 'allMarks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            // New Fields
            'whatsapp_number' => 'nullable|string|max:15',
            'nic' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'school' => 'nullable|string|max:255',
            'exam_year' => 'nullable|string|max:4',
            'medium' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:15',
        ]);

        $student->fill($validated); // Mass assign all validated fields
        
        // Handle boolean separately if not in fillable or checkbox logic
        $student->is_active = $request->has('is_active');

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return redirect()->route('admin.students.index')->with('success', 'Student profile updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $student)
    {
        if($student->role !== 'student') {
             return back()->with('error', 'Cannot delete admin users.');
        }
        
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }
}
