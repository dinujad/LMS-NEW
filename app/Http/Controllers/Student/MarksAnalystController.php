<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalExamMark;
use App\Models\ExamAttempt;

class MarksAnalystController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Fetch Online Exam Marks (from ExamAttempt)
        // Assuming 'score' is the marks obtained. 
        // Note: We might need to know the 'total_marks' for the online exam. 
        // If Exam model has total_marks, we can use that. 
        // For now, let's assume online exams are out of 100 or check relationship.
        $onlineMarks = ExamAttempt::with('exam')
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->get()
            ->toBase() // Convert to base collection to avoid Eloquent merge issues
            ->map(function ($attempt) {
                // Determine total marks. 
                // If Exam model doesn't have total_marks column, assume 100 or calculate from questions.
                // Let's assume 100 for safety or check if Exam model has it.
                // Ideally: $total = $attempt->exam->total_marks ?? 100;
                $total = 100; // Defaulting to 100 as per common LMS patterns if column unknown
                $score = (float) $attempt->score;
                
                return [
                    'exam_name' => AsString($attempt->exam->title ?? 'Online Exam'),
                    'marks_obtained' => $score,
                    'mcq_marks' => $score, // Treat Online as MCQ
                    'essay_marks' => 0,    // No Essay component
                    'total_marks' => $total,
                    'exam_date' => $attempt->submitted_at->format('Y-m-d'), // Use date part
                    'type' => 'Online'
                ];
            });

        // 2. Fetch Physical Exam Marks
        $physicalMarks = PhysicalExamMark::where('user_id', $user->id)
            ->get()
            ->toBase() // Convert to base collection
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'exam_name' => $record->exam_name,
                    'marks_obtained' => (float) $record->marks_obtained,
                    'mcq_marks' => (float) $record->mcq_marks,
                    'essay_marks' => (float) $record->essay_marks,
                    'total_marks' => (float) $record->total_marks,
                    'exam_date' => $record->exam_date->format('Y-m-d'),
                    'type' => 'Physical'
                ];
            });

        // 3. Merge and Sort
        $allMarks = $onlineMarks->merge($physicalMarks)->sortBy(function($item) {
            return strtotime($item['exam_date']);
        })->values();

        // 4. Prepare Chart Data (Separate Arrays)
        $labels = $allMarks->map(function($item) {
            return $item['exam_date'] . ' - ' . $item['exam_name'];
        });

        $mcqPoints = $allMarks->map(function ($item) {
            return $item['mcq_marks'] > 0 ? $item['mcq_marks'] : 0;
        });

        $essayPoints = $allMarks->map(function ($item) {
            return $item['essay_marks'] > 0 ? $item['essay_marks'] : 0;
        });

        // Old total percentage logic (optional, keeping for table)
        // We can pass $allMarks to view for the table.

        return view('student.marks_analyst', compact('allMarks', 'labels', 'mcqPoints', 'essayPoints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_name' => 'required|string|max:255',
            'mcq_marks' => 'nullable|numeric|min:0',
            'essay_marks' => 'nullable|numeric|min:0',
            'total_marks' => 'required|numeric|min:1',
            'exam_date' => 'required|date',
        ]);

        // Auto-calculate total obtained if separate marks provided
        $obtained = 0;
        if($request->mcq_marks) $obtained += $request->mcq_marks;
        if($request->essay_marks) $obtained += $request->essay_marks;
        
        // If user entered a specific total obtained, use it, otherwise use sum
        // Actually, let's trust the components sum if provided.
        // Update: User prompt implies filling components.
        
        PhysicalExamMark::create([
            'user_id' => Auth::id(),
            'exam_name' => $request->exam_name,
            'mcq_marks' => $request->mcq_marks ?? 0,
            'essay_marks' => $request->essay_marks ?? 0,
            'marks_obtained' => $obtained,
            'total_marks' => $request->total_marks,
            'exam_date' => $request->exam_date,
        ]);

        return redirect()->route('student.marks-analyst.index')->with('success', 'Exam marks added successfully!');
    }

    public function update(Request $request, $id)
    {
        $mark = PhysicalExamMark::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'exam_name' => 'required|string|max:255',
            'mcq_marks' => 'nullable|numeric|min:0',
            'essay_marks' => 'nullable|numeric|min:0',
            'total_marks' => 'required|numeric|min:1',
            'exam_date' => 'required|date',
        ]);

        $obtained = 0;
        if($request->mcq_marks) $obtained += $request->mcq_marks;
        if($request->essay_marks) $obtained += $request->essay_marks;

        $mark->update([
            'exam_name' => $request->exam_name,
            'mcq_marks' => $request->mcq_marks ?? 0,
            'essay_marks' => $request->essay_marks ?? 0,
            'marks_obtained' => $obtained,
            'total_marks' => $request->total_marks,
            'exam_date' => $request->exam_date,
        ]);

        return redirect()->route('student.marks-analyst.index')->with('success', 'Exam marks updated successfully!');
    }

    public function destroy($id)
    {
        $mark = PhysicalExamMark::where('user_id', Auth::id())->findOrFail($id);
        $mark->delete();

        return redirect()->route('student.marks-analyst.index')->with('success', 'Record deleted successfully!');
    }
}

function AsString($val) {
    return is_string($val) ? $val : strval($val);
}
