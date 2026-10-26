@extends('layouts.student')

@section('content')
<div class="row">
    <!-- Welcome Banner -->
    <div class="col-12">
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Good Morning, {{ Auth::user()->name }}!</h1>
                <p class="welcome-subtitle">
                    You have <span class="fw-bold text-warning">{{ $my_courses_count }}</span> active courses. 
                    Keep up the great work and continue your learning journey today!
                </p>
                <div class="d-flex align-items-center gap-3 text-white-50 small">
                    <span class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> {{ date('l, d M Y') }}</span>
                    <span class="d-flex align-items-center"><i class="bi bi-clock me-2"></i> {{ date('h:i A') }}</span>
                </div>
            </div>
            <!-- Illustration Placeholder -->
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/student-studying-online-2974958-2477389.png" alt="Welcome" class="banner-img">
        </div>
    </div>
</div>

<!-- Overview Section -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3 fw-bold ps-1"><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Overview</h5>
    </div>

    <!-- Active Courses -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stats-card">
            <div class="stats-icon-wrapper stats-icon-primary">
                <i class="bi bi-book"></i>
            </div>
            <span class="stats-value">{{ $my_courses_count }}</span>
            <span class="stats-label">Active Courses</span>
            <a href="{{ route('student.courses.my') }}" class="stretched-link"></a>
        </div>
    </div>

    <!-- Completed Exams (Placeholder Data) -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stats-card">
            <div class="stats-icon-wrapper stats-icon-success">
                <i class="bi bi-journal-check"></i>
            </div>
            <span class="stats-value">0</span> <!-- Placeholder for now -->
            <span class="stats-label">Exams Completed</span>
            <a href="#" class="stretched-link"></a>
        </div>
    </div>
    
    <!-- BIO-BUDDY AI Card -->
    <div class="col-md-12 col-lg-6 mb-4">
        <a href="{{ route('student.biology-ai') }}" class="text-decoration-none">
            <div class="stats-card h-100 border-0 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #c026d3 0%, #7e22ce 100%); transition: transform 0.2s;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                    <div>
                        <div class="text-uppercase fw-bold opacity-75 small mb-1" style="letter-spacing: 1px; font-size: 0.8rem;">Ask me anything...</div>
                        <h2 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif;">BIO-BUDDY AI</h2>
                    </div>
                    <div class="display-3 opacity-25">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                </div>
                <!-- Decorative Circles -->
                <div class="position-absolute rounded-circle bg-white opacity-10" style="width: 150px; height: 150px; top: -50px; right: -50px;"></div>
                <div class="position-absolute rounded-circle bg-white opacity-10" style="width: 100px; height: 100px; bottom: -20px; left: -20px;"></div>
            </div>
        </a>
    </div>
</div>

<!-- Content Section: Notices & Actions -->
<div class="row">
    <!-- Upcoming Exams -->
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold ps-1 mb-0"><i class="bi bi-clock-history me-2 text-danger"></i>Upcoming Exams</h5>
            @if($exams->count() > 0)
                <span class="badge bg-danger-soft text-danger small px-3">Active Now</span>
            @endif
        </div>
        
        @if($exams->count() > 0)
            <div class="row">
                @foreach($exams as $exam)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 12px; border-left: 5px solid #dc3545 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light text-dark border">{{ $exam->course->title ?? 'General' }}</span>
                                <small class="text-danger fw-bold"><i class="bi bi-hourglass-split me-1"></i> Ends {{ $exam->end_time->diffForHumans() }}</small>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $exam->title }}</h6>
                            <p class="small text-muted mb-2 text-truncate">{{ $exam->description ?? 'No description.' }}</p>
                            
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> {{ $exam->start_time->format('M d, h:i A') }}</small>
                                <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-sm btn-danger rounded-pill px-3">Start Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light border-0 shadow-sm rounded-3 d-flex align-items-center text-muted">
                <i class="bi bi-check-circle-fill me-3 text-success fs-5"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">No Pending Exams</h6>
                    <small>You are all caught up! Check back later for new tests.</small>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">Quick Actions</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('student.courses.index') }}" class="d-flex align-items-center p-3 rounded-3 text-decoration-none bg-light text-dark h-100 hover-sc">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                <i class="bi bi-search fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Browse Courses</h6>
                                <small class="text-muted">Find new classes to join</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('profile.edit') }}" class="d-flex align-items-center p-3 rounded-3 text-decoration-none bg-light text-dark h-100 hover-sc">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-info">
                                <i class="bi bi-person-gear fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Update Profile</h6>
                                <small class="text-muted">Change password or details</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="col-lg-4 mb-4">
         <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #7638ff 0%, #5d22e0 100%);">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 text-white">
                <h5 class="fw-bold mb-0"><i class="bi bi-megaphone me-2"></i>Announcements</h5>
            </div>
            <div class="card-body p-3">
                @forelse($notices as $notice)
                    <div class="bg-white bg-opacity-10 rounded-3 p-2 mb-2 text-white border border-light border-opacity-10">
                        <div class="d-flex justify-content-between">
                            <small class="badge bg-white text-primary rounded-pill mb-1" style="font-size: 0.65rem;">{{ $notice->course->code ?? 'NOTICE' }}</small>
                            <small class="text-white-50" style="font-size: 0.7rem;">{{ $notice->created_at->diffForHumans() }}</small>
                        </div>
                        <h6 class="fw-bold mb-1 small text-truncate">{{ $notice->title }}</h6>
                        <p class="small text-white-50 mb-0 text-truncate">{{ Str::limit($notice->content, 50) }}</p>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-bell-slash display-4 mb-2 text-white-50"></i>
                        <p class="small text-white-50">No new notices at this time.</p>
                    </div>
                @endforelse
            </div>
            @if($notices->count() > 0)
            <div class="card-footer bg-transparent border-0 text-center pb-3 pt-0">
                <a href="#" class="btn btn-light btn-sm rounded-pill fw-bold text-primary w-100">View All</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
