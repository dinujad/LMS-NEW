@extends('layouts.student')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card text-center border-0 shadow-lg">
            <div class="card-body p-5">
                <div class="mb-4">
                    @if($percentage >= 50)
                        <i class="bi bi-trophy-fill text-warning display-1"></i>
                        <h2 class="mt-3 text-success">Great Job!</h2>
                    @else
                        <i class="bi bi-emoji-frown text-secondary display-1"></i>
                        <h2 class="mt-3 text-secondary">Keep Trying!</h2>
                    @endif
                </div>

                <h4 class="mb-1">{{ $exam->title }}</h4>
                <p class="text-muted">Result Summary</p>

                <div class="py-4 my-4 bg-light rounded">
                    <h1 class="display-3 fw-bold">{{ $attempt->score }} <span class="fs-4 text-muted">/ {{ $maxScore }}</span></h1>
                    <p class="mb-0 fs-5 {{ $percentage >= 50 ? 'text-success' : 'text-danger' }}">{{ number_format($percentage, 0) }}%</p>
                </div>

                <a href="{{ route('student.courses.show', $exam->course_id) }}" class="btn btn-outline-primary btn-lg">Back to Course</a>
            </div>
        </div>
    </div>
</div>
@endsection
