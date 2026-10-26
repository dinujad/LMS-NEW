@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Enrollments</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus-fill"></i> Manual Enroll
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Filter by Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Enrolled Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $enrollment->user->name }}</div>
                            <small class="text-muted">{{ $enrollment->user->email }}</small>
                        </td>
                        <td>{{ $enrollment->course->title }}</td>
                        <td>
                            <span class="badge bg-{{ $enrollment->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d') : '-' }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Start Un-enroll process? This will remove access.');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Remove Access"><i class="bi bi-person-x"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No enrollments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $enrollments->links() }}
    </div>
</div>
@endsection
