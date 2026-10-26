@extends('layouts.student')

@section('content')
<h2 class="mb-4">My Learning</h2>

<div class="row">
    @forelse($enrolledCourses as $course)
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-primary">{{ $course->title }}</h5>
                <p class="card-text small text-muted">Enrolled on: {{ $course->pivot->enrolled_at }}</p>
                <div class="mt-auto">
                    <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary w-100">Continue Learning</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <p class="text-muted fs-5">You haven't enrolled in any courses yet.</p>
        <a href="{{ route('student.courses.index') }}" class="btn btn-primary mt-2">Browse Courses</a>
    </div>
    @endforelse
</div>
@endsection
