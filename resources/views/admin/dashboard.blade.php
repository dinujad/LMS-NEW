@extends('layouts.admin')

@section('content')
<div class="row">
    <!-- Welcome Banner -->
    <div class="col-12">
        <div class="welcome-banner bg-dark" style="background: linear-gradient(135deg, #343a40 0%, #212529 100%); box-shadow: 0 15px 30px rgba(0,0,0,0.2);">
            <div class="welcome-content">
                <h1 class="welcome-title text-white">Admin Overview</h1>
                <p class="welcome-subtitle text-white-50">
                    You have <span class="fw-bold text-warning">{{ $data['pending_payments'] }}</span> pending payments requiring attention.
                </p>
                <div class="d-flex align-items-center gap-3 text-white-50 small">
                    <span class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> {{ date('l, d M Y') }}</span>
                </div>
            </div>
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-analysis-4506548-3739054.png" alt="Admin" class="banner-img" style="max-width: 30%;">
        </div>
    </div>
</div>

<!-- Stats Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3 fw-bold ps-1"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Platform Stats</h5>
    </div>

    <!-- Total Students -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stats-card">
            <div class="stats-icon-wrapper stats-icon-primary">
                <i class="bi bi-people"></i>
            </div>
            <span class="stats-value">{{ $data['total_students'] }}</span>
            <span class="stats-label">Total Students</span>
            <a href="{{ route('admin.students.index') }}" class="stretched-link"></a>
        </div>
    </div>

    <!-- Active Courses -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stats-card">
            <div class="stats-icon-wrapper stats-icon-info">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <span class="stats-value">{{ $data['total_courses'] }}</span>
            <span class="stats-label">Active Courses</span>
            <a href="{{ route('admin.courses.index') }}" class="stretched-link"></a>
        </div>
    </div>
    
    <!-- Pending Payments -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stats-card">
            <div class="stats-icon-wrapper stats-icon-warning">
                <i class="bi bi-cash-stack"></i>
            </div>
            <span class="stats-value">{{ $data['pending_payments'] }}</span>
            <span class="stats-label">Pending Payments</span>
            <a href="{{ route('admin.payments.index') }}" class="stretched-link"></a>
        </div>
    </div>

    <!-- Exam Status -->
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="stats-card">
            <div class="stats-icon-wrapper stats-icon-success">
                <i class="bi bi-clock-history"></i>
            </div>
            <span class="stats-value">{{ $data['upcoming_exams'] }}</span>
            <span class="stats-label">Upcoming Exams</span>
            <a href="{{ route('admin.exams.index') }}" class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Payments Table -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Recent Payments</h5>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-light btn-sm rounded-pill text-primary fw-bold">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Student</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recent_payments'] as $payment)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center me-2 text-primary fw-bold" style="width:35px; height:35px;">
                                            {{ substr($payment->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark small">{{ $payment->user->name }}</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">{{ $payment->created_at->format('M d') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $payment->course->title }}</td>
                                <td class="fw-bold">LKR {{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    @if($payment->status == 'approved')
                                        <span class="badge bg-soft-success text-success bg-opacity-10 py-2 px-3 rounded-pill">Paid</span>
                                    @elseif($payment->status == 'pending')
                                        <span class="badge bg-soft-warning text-warning bg-opacity-10 py-2 px-3 rounded-pill">Pending</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger bg-opacity-10 py-2 px-3 rounded-pill">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No recent payments.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- New Students List -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Newest Students</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($data['recent_users'] as $user)
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-3">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="rounded-circle me-3" width="40" height="40">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                        <span class="small text-muted">{{ $user->created_at->diffForHumans() }}</span>
                    </li>
                    @empty
                    <li class="text-center text-muted py-3">No new students joined recently.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
