@extends('layouts.student')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">My Exams</h1>
        <p class="text-muted">View and attend all your available exams.</p>
    </div>
</div>

<div class="row">
    @forelse($exams as $exam)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 16px;">
                 <!-- Status Badge Logic -->
                 @php
                    $isActive = $exam->end_time > now() && $exam->start_time <= now();
                    $isUpcoming = $exam->start_time > now();
                    $isExpired = $exam->end_time <= now();
                    
                    $statusColor = $isActive ? 'success' : ($isUpcoming ? 'warning' : 'secondary');
                    $statusText = $isActive ? 'Active' : ($isUpcoming ? 'Upcoming' : 'Expired');
                    $borderColor = $isActive ? '#198754' : ($isUpcoming ? '#ffc107' : '#6c757d');
                 @endphp

                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-light text-primary border shadow-sm px-3 py-2 rounded-pill fw-normal">
                            <i class="bi bi-book me-1"></i> {{ $exam->course->title ?? 'General' }}
                        </span>
                        <span class="badge bg-{{ $statusColor }}-soft text-{{ $statusColor }} border border-{{ $statusColor }} rounded-pill px-3 py-1">
                            {{ $statusText }}
                        </span>
                    </div>

                    <h5 class="fw-bold mb-2">{{ $exam->title }}</h5>
                    <p class="text-muted small mb-3 text-truncate-3" style="min-height: 40px;">
                        {{ $exam->description ?? 'No description provided.' }}
                    </p>

                    <div class="d-flex align-items-center mb-2 text-muted small">
                        <i class="bi bi-clock me-2"></i> 
                        <span>{{ $exam->duration_minutes }} Mins</span>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4 text-muted small">
                        <i class="bi bi-calendar-event me-2"></i>
                        <span>
                            {{ $exam->start_time->format('M d, h:i A') }} - 
                            {{ $exam->end_time->format('M d, h:i A') }}
                        </span>
                    </div>

                    <div class="d-grid">
                        @if($isActive)
                             <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-primary fw-bold shadow-sm rounded-3 py-2">
                                <i class="bi bi-play-circle me-2"></i> Attend Exam
                            </a>
                        @elseif($isUpcoming)
                             <button disabled class="btn btn-light text-muted fw-bold shadow-sm rounded-3 py-2 border">
                                <i class="bi bi-lock me-2"></i> Starts Soon
                            </button>
                        @else
                             <button disabled class="btn btn-secondary fw-bold shadow-sm rounded-3 py-2">
                                <i class="bi bi-x-circle me-2"></i> Expired
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Decorative Border Left -->
                <div class="position-absolute start-0 top-0 bottom-0" style="width: 5px; background-color: {{ $borderColor }};"></div>
            </div>
        </div>
    @empty
        <div class="col-12">
             <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                 <div class="mb-3">
                     <i class="bi bi-clipboard-x display-1 text-light-gray opacity-25"></i>
                 </div>
                 <h4 class="fw-bold text-dark">No Exams Available</h4>
                 <p class="text-muted">You don't have any exams assigned to you at the moment.</p>
             </div>
        </div>
    @endforelse
</div>
@endsection
