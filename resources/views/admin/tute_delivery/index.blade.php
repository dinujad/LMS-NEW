@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tute Delivery Management</h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.tute-delivery.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="course_id" class="form-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Address</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d') : '-' }}</td>
                        <td>
                            <div class="fw-bold">{{ $payment->user->name }}</div>
                            <div class="small text-muted">{{ $payment->user->phone }}</div>
                        </td>
                        <td>
                            <small>
                                {{ $payment->user->address_line_1 }}<br>
                                {{ $payment->user->address_line_2 }}<br>
                                {{ $payment->user->district }}
                            </small>
                        </td>
                        <td>{{ $payment->course->title }}</td>
                        <td>
                            @if($payment->delivery_status == 'delivered')
                                <span class="badge bg-success">Delivered</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.tute-delivery.update', $payment) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                    <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#addressModal{{ $payment->id }}">
                                        <i class="bi bi-eye"></i> View Address
                                    </button>
                                    
                                    @if($payment->delivery_status == 'pending')
                                        <input type="hidden" name="delivery_status" value="delivered">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Mark as Delivered</button>
                                    @else
                                        <input type="hidden" name="delivery_status" value="pending">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Mark as Pending</button>
                                    @endif
                                </form>

                                <!-- Address Modal -->
                                <div class="modal fade" id="addressModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delivery Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Student Name:</strong> {{ $payment->user->name }}</p>
                                                <p><strong>Phone:</strong> {{ $payment->user->phone }}</p>
                                                <hr>
                                                <h6 class="fw-bold">Address:</h6>
                                                <p class="mb-1">{{ $payment->user->address_line_1 }}</p>
                                                <p class="mb-1">{{ $payment->user->address_line_2 }}</p>
                                                <p class="mb-1"><strong>District:</strong> {{ $payment->user->district }}</p>
                                                <hr>
                                                <p><strong>Course:</strong> {{ $payment->course->title }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No payments found needing delivery.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $payments->links() }}
    </div>
</div>
@endsection
