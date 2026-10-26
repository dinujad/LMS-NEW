@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manual Enrollment</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Add Student to Course</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.enrollments.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <select name="user_id" id="selectStudent" class="form-select" required>
                            <option value="">Select Student...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">
                                    [{{ $student->student_id ?? 'No ID' }}] - {{ $student->name }} - {{ $student->email }} - {{ $student->phone }}
                                </option>
                            @endforeach
                        </select>
                         <div class="form-text">Search by ID, Name, Email, or Phone.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="selectCourse" class="form-select" required>
                            <option value="">Select Course...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Enroll Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#selectStudent').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search for a student...',
            allowClear: true
        });

        $('#selectCourse').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search for a course...',
            allowClear: true
        });
    });
</script>
@endsection
