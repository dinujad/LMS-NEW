@extends('layouts.admin')

@section('content')
<div class="row pt-3 pb-2 mb-3 border-bottom">
    <div class="col-md-8">
        <h1 class="h2">{{ $exam->title }} <small class="text-muted fs-5">({{ $exam->course->title }})</small></h1>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-outline-primary">Edit Settings</a>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Questions ({{ $exam->questions->count() }})</h5>
                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> Add Question
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Question</th>
                            <th>Correct Ans</th>
                            <th>Marks</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exam->questions as $index => $question)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ Str::limit($question->question_text, 80) }}</td>
                            <td><span class="badge bg-success">{{ $question->correct_answer }}</span></td>
                            <td>{{ $question->marks }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-muted mb-2">No questions added yet.</p>
                                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="btn btn-primary">Add First Question</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
