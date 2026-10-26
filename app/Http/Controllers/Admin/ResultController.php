<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::orderBy('created_at', 'desc')->get();
        $attempts = collect(); // Empty collection by default
        $searched = false;

        if ($request->has('exam_id') && $request->has('student_id')) {
            $searched = true;
            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'student_id' => 'required|string',
            ]);

            // Find student first
            $student = User::where('student_id', $request->student_id)->first();

            if ($student) {
                $attempts = ExamAttempt::with(['user', 'exam'])
                    ->where('exam_id', $request->exam_id)
                    ->where('user_id', $student->id)
                    ->latest()
                    ->get();
            }
        }

        return view('admin.results.index', compact('exams', 'attempts', 'searched'));
    }
}
