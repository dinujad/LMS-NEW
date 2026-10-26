@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Exam</h1>
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
                <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" required>
                             @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $exam->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Exam Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $exam->description }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration (Minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control" value="{{ $exam->duration_minutes }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Attempts</label>
                            <input type="number" name="max_attempts" class="form-control" value="{{ $exam->max_attempts }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="datetime-local" name="start_time" class="form-control" value="{{ $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Time</label>
                            <input type="datetime-local" name="end_time" class="form-control" value="{{ $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_published" value="1" {{ $exam->is_published ? 'checked' : '' }}>
                        <label class="form-check-label">Publish</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Exam Details</button>
                    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-outline-info ms-2">Manage Questions</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
