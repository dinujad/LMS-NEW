<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // --- Exam CRUD ---

    public function index()
    {
        $exams = Exam::with('course')->withCount('questions')->latest()->paginate(10);
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'duration_minutes' => 'required|integer|min:5',
            'max_attempts' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
            'is_published' => 'boolean'
        ]);

        $validated['is_published'] = $request->has('is_published');
        
        $exam = Exam::create($validated);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Exam created. Now add questions.');
    }

    public function show(Exam $exam)
    {
        $exam->load('questions');
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $courses = Course::all();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'duration_minutes' => 'required|integer',
            'max_attempts' => 'required|integer',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
        ]);
        
        $validated['is_published'] = $request->has('is_published');
        $exam->update($validated);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted.');
    }

    // --- Question CRUD ---

    public function createQuestion(Exam $exam)
    {
        return view('admin.exams.questions.create', compact('exam'));
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'marks' => 'required|integer|min:1',
            'option_1' => 'required|string',
            'option_2' => 'required|string',
            'option_3' => 'required|string',
            'option_4' => 'required|string',
            'correct_answer' => 'required|in:option_1,option_2,option_3,option_4'
        ]);

        $options = [
            'option_1' => $request->option_1,
            'option_2' => $request->option_2,
            'option_3' => $request->option_3,
            'option_4' => $request->option_4,
        ];

        Question::create([
            'exam_id' => $exam->id,
            'question_text' => $validated['question_text'],
            'type' => 'mcq',
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'marks' => $validated['marks']
        ]);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Question added.');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Question remove.');
    }
}
