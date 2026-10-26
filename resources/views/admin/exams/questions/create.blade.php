@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add Question to: {{ $exam->title }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Marks/Points</label>
                        <input type="number" name="marks" class="form-control" value="1" required>
                    </div>

                    <hr>
                    <h6 class="mb-3">Answers (Multiple Choice)</h6>

                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-auto">
                            <input type="radio" name="correct_answer" value="option_1" required title="Mark as correct">
                        </div>
                        <div class="col">
                            <input type="text" name="option_1" class="form-control" placeholder="Option 1" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-auto">
                            <input type="radio" name="correct_answer" value="option_2" title="Mark as correct">
                        </div>
                        <div class="col">
                            <input type="text" name="option_2" class="form-control" placeholder="Option 2" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-auto">
                            <input type="radio" name="correct_answer" value="option_3" title="Mark as correct">
                        </div>
                        <div class="col">
                            <input type="text" name="option_3" class="form-control" placeholder="Option 3" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-auto">
                            <input type="radio" name="correct_answer" value="option_4" title="Mark as correct">
                        </div>
                        <div class="col">
                            <input type="text" name="option_4" class="form-control" placeholder="Option 4" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Question</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
