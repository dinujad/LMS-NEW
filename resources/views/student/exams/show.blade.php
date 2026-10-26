@extends('layouts.student')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card text-center shadow-lg border-0">
            <div class="card-body p-5">
                <h1 class="card-title text-primary mb-3">{{ $exam->title }}</h1>
                <h5 class="text-muted">{{ $exam->course->title }}</h5>
                
                <hr class="my-4 w-50 mx-auto">

                <div class="row justify-content-center mb-4">
                    <div class="col-md-4">
                         <div class="p-3 bg-light rounded">
                             <i class="bi bi-clock fs-2 text-primary"></i>
                             <p class="mt-2 mb-0"><strong>Duration</strong><br>{{ $exam->duration_minutes }} Mins</p>
                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                             <i class="bi bi-person-workspace fs-2 text-primary"></i>
                             <p class="mt-2 mb-0"><strong>Attempts</strong><br>{{ $previousAttempts }} / {{ $exam->max_attempts }}</p>
                        </div>
                   </div>
                </div>

                @if($exam->description)
                <div class="alert alert-info text-start">
                    <h5><i class="bi bi-info-circle me-2"></i>Instructions:</h5>
                    {{ $exam->description }}
                </div>
                @endif

                @if($previousAttempts < $exam->max_attempts)
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">Start Exam</button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        You have reached the maximum number of attempts for this exam.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
