<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the enrollments.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course']);

        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        $enrollments = $query->latest()->paginate(20);
        $courses = Course::all(); // For filter dropdown

        return view('admin.enrollments.index', compact('enrollments', 'courses'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create()
    {
        // Fetch only students and published courses
        $students = User::where('role', 'student')->where('is_active', true)->get();
        $courses = Course::all();

        return view('admin.enrollments.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if already enrolled
        $exists = Enrollment::where('user_id', $validated['user_id'])
                            ->where('course_id', $validated['course_id'])
                            ->exists();

        if ($exists) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        $enrollment = Enrollment::create([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // Send SMS Notification
        try {
            $user = User::find($validated['user_id']);
            $course = Course::find($validated['course_id']);
            
            if ($user && $user->phone) {
                $smsService = new \App\Services\SmsService();
                $message = "Enrollment Active! You have been manually enrolled in '{$course->title}'. Student ID: {$user->student_id}. Log in to start learning.";
                $smsService->send($user->phone, $message);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Manual Enrollment SMS Failed for User {$validated['user_id']}: " . $e->getMessage());
        }

        return redirect()->route('admin.enrollments.index')->with('success', 'Student enrolled successfully.');
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return back()->with('success', 'Enrollment removed.');
    }
}
