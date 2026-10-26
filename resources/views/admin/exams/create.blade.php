@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Exam</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.exams.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" required>
                             @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Exam Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description / Instructions</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control" value="60" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Attempts <span class="text-danger">*</span></label>
                            <input type="number" name="max_attempts" class="form-control" value="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Time (Optional)</label>
                            <input type="datetime-local" name="start_time" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Time (Optional)</label>
                            <input type="datetime-local" name="end_time" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_published" value="1">
                        <label class="form-check-label">Publish (Students can see it immediately)</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Create & Add Questions</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
