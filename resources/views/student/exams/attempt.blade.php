@extends('layouts.student')

@section('content')
<div class="sticky-top bg-white border-bottom py-3 mb-4 shadow-sm" style="z-index: 10;">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0">{{ $exam->title }}</h4>
        <div class="d-flex align-items-center">
             <i class="bi bi-stopwatch fs-4 text-danger me-2"></i>
             <span id="timer" class="fs-4 fw-bold text-danger">{{ $exam->duration_minutes }}:00</span>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <form id="examForm" action="{{ route('student.exams.submit', ['exam' => $exam, 'attempt' => $attempt]) }}" method="POST">
            @csrf
            
            @foreach($exam->questions as $index => $question)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title h6 text-muted mb-3">Question {{ $index + 1 }} <span class="float-end badge bg-light text-dark">{{ $question->marks }} Marks</span></h5>
                    <p class="lead mb-4">{{ $question->question_text }}</p>

                    <div class="list-group">
                        @foreach(['option_1', 'option_2', 'option_3', 'option_4'] as $opt)
                            <label class="list-group-item list-group-item-action cursor-pointer">
                                <input class="form-check-input me-3" type="radio" name="q_{{ $question->id }}" value="{{ $opt }}">
                                {{ $question->options[$opt] }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="text-center mb-5">
                <button type="submit" class="btn btn-primary btn-lg px-5" onclick="return confirm('Are you sure you want to submit?');">Submit Answers</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple timer logic
    let duration = {{ $exam->duration_minutes }} * 60;
    const display = document.getElementById('timer');
    
    const timer = setInterval(function () {
        let minutes = parseInt(duration / 60, 10);
        let seconds = parseInt(duration % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--duration < 0) {
            clearInterval(timer);
            alert('Time is up! Submitting your exam automatically.');
            document.getElementById('examForm').submit();
        }
    }, 1000);
</script>
@endsection
