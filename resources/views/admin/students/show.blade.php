@extends('layouts.admin')

@section('content')
<div class="row">
    <!-- Header / Status -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 24px;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $student->name }}</h4>
                        <div class="text-muted small">{{ $student->email }} | {{ $student->student_id }}</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="small text-muted text-uppercase fw-bold">Current Status</div>
                        @if($isOnline)
                            <span class="badge bg-success"><i class="bi bi-circle-fill small me-1"></i> Online Now</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-moon-fill small me-1"></i> Offline</span>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                Last seen: {{ $student->last_seen_at ? \Carbon\Carbon::parse($student->last_seen_at)->diffForHumans() : 'Never' }}
                            </div>
                        @endif
                    </div>
                    
                     <div class="vr mx-2"></div>
                     
                    <div class="text-end">
                         <div class="small text-muted text-uppercase fw-bold">Total Study Time</div>
                         <h4 class="fw-bold mb-0 text-primary">{{ $totalHours }} Hours</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Info -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Personal Details</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Phone</span>
                        <span class="fw-medium">{{ $student->phone ?? '-' }}</span>
                    </li>
                     <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">WhatsApp</span>
                        <span class="fw-medium">{{ $student->whatsapp_number ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">School</span>
                        <span class="fw-medium">{{ $student->school ?? '-' }}</span>
                    </li>
                     <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Exam Year</span>
                        <span class="fw-medium">{{ $student->exam_year ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Guardian</span>
                        <span class="fw-medium">{{ $student->guardian_name ?? '-' }} ({{ $student->guardian_phone }})</span>
                    </li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-light btn-sm w-100">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Performance -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                 <h5 class="fw-bold"><i class="bi bi-graph-up me-2 text-success"></i>Exam Performance</h5>
            </div>
            <div class="card-body">
                @if($allMarks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Exam</th>
                                    <th>Type</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allMarks as $mark)
                                <tr>
                                    <td class="small">{{ \Carbon\Carbon::parse($mark['date'])->format('Y-M-d') }}</td>
                                    <td class="fw-medium">{{ $mark['title'] }}</td>
                                    <td>
                                        @if($mark['type'] == 'Online')
                                            <span class="badge bg-info-soft text-info small">Online</span>
                                        @else
                                            <span class="badge bg-warning-soft text-warning small">Physical</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ $mark['score'] }} 
                                        <span class="text-muted small fw-normal">/ {{ $mark['total_possible'] }}</span>
                                    </td>
                                    <td class="text-center">
                                         @php $pct = floatval($mark['percentage']); @endphp
                                         @if($pct >= 75)
                                            <span class="badge bg-success">A</span>
                                         @elseif($pct >= 65)
                                             <span class="badge bg-primary">B</span>
                                         @elseif($pct >= 55)
                                              <span class="badge bg-secondary">C</span>
                                         @elseif($pct >= 35)
                                               <span class="badge bg-warning text-dark">S</span>
                                         @else
                                               <span class="badge bg-danger">F</span>
                                         @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No exam records found for this student.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
