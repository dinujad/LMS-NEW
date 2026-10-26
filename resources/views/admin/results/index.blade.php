@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Exam Results</h1>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        Search Student Result
    </div>
    <div class="card-body">
        <form action="{{ route('admin.results.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Select Exam</label>
                <select name="exam_id" class="form-select" required>
                    <option value="">Choose Exam...</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->title }} ({{ $exam->course->title ?? 'General' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Student ID (e.g. SID1000)</label>
                <input type="text" name="student_id" class="form-control" placeholder="Enter Student ID" value="{{ request('student_id') }}" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search Result</button>
            </div>
        </form>
    </div>
</div>

@if($searched)
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            Search Results
        </div>
        <div class="card-body">
            @if($attempts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Exam</th>
                                <th>Score</th>
                                <th>Grade</th>
                                <th>Date Taken</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                            <tr>
                                <td>
                                    {{ $attempt->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $attempt->user->student_id }}</small>
                                </td>
                                <td>{{ $attempt->exam->title }}</td>
                                <td>
                                    <h4 class="mb-0 fw-bold {{ $attempt->score >= 40 ? 'text-success' : 'text-danger' }}">
                                        {{ $attempt->score }}%
                                    </h4>
                                </td>
                                <td>
                                    @if($attempt->score >= 75) <span class="badge bg-success">A</span>
                                    @elseif($attempt->score >= 65) <span class="badge bg-primary">B</span>
                                    @elseif($attempt->score >= 55) <span class="badge bg-info">C</span>
                                    @elseif($attempt->score >= 40) <span class="badge bg-warning">S</span>
                                    @else <span class="badge bg-danger">F</span>
                                    @endif
                                </td>
                                <td>{{ $attempt->created_at->format('Y-m-d H:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    No results found for this Student ID in the selected Exam. 
                    <br>Make sure the Student ID is correct and the student has actually attempted this exam.
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
