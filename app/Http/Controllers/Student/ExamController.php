<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get Enrolled Course IDs
        $enrolledCourseIds = \App\Models\Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('course_id');

        // Fetch Published Exams for these courses
        $exams = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->orderBy('start_time', 'desc')
            ->get();

        return view('student.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        // Instructions Page
        $user = Auth::user();
        $previousAttempts = ExamAttempt::where('user_id', $user->id)->where('exam_id', $exam->id)->count();
        
        return view('student.exams.show', compact('exam', 'previousAttempts'));
    }

    public function start(Exam $exam)
    {
        $user = Auth::user();
        
        // Check attempts
        $attemptsCount = ExamAttempt::where('user_id', $user->id)->where('exam_id', $exam->id)->count();
        if ($attemptsCount >= $exam->max_attempts) {
            return back()->with('error', 'Maximum attempts reached.');
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
        ]);

        return redirect()->route('student.exams.attempt', ['exam' => $exam, 'attempt' => $attempt]);
    }

    public function attempt(Exam $exam, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->submitted_at) {
            return redirect()->route('student.exams.show', $exam);
        }

        $exam->load('questions');
        return view('student.exams.attempt', compact('exam', 'attempt'));
    }

    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        if ($attempt->submitted_at) {
            return back();
        }

        $questions = $exam->questions;
        $totalScore = 0;
        $maxScore = $questions->sum('marks');

        // Logic to grade
        foreach($questions as $question) {
            $userAnswer = $request->input('q_' . $question->id);
            $isCorrect = ($userAnswer === $question->correct_answer);
            
            if ($isCorrect) {
                 $totalScore += $question->marks;
            }

            ExamAnswer::create([
                'exam_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_answer' => $userAnswer,
                'is_correct' => $isCorrect
            ]);
        }

        $attempt->update([
            'submitted_at' => now(),
            'score' => $totalScore // Store raw score, or percentage
        ]);

        return redirect()->route('student.exams.result', ['exam' => $exam, 'attempt' => $attempt]);
    }

    public function result(Exam $exam, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $maxScore = $exam->questions->sum('marks');
        $percentage = $maxScore > 0 ? ($attempt->score / $maxScore) * 100 : 0;

        return view('student.exams.result', compact('exam', 'attempt', 'maxScore', 'percentage'));
    }
}
