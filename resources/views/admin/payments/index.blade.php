@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Payment Approvals</h1>
    <div class="btn-group">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="btn btn-sm btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">Approved</a>
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
                        <th>Slip/Ref</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $payment->user->name }}</div>
                            <small class="text-muted">{{ $payment->user->email }}</small>
                        </td>
                        <td>{{ $payment->course->title }}</td>
                        <td>
                            @if($payment->proof_image)
                                <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank" class="btn btn-sm btn-link">View Slip</a>
                            @else
                                <span class="text-muted">{{ $payment->transaction_ref ?? 'No Ref' }}</span>
                            @endif
                        </td>
                        <td>LKR {{ number_format($payment->amount, 2) }}</td>
                        <td>
                             <span class="badge bg-{{ $payment->status == 'approved' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($payment->status == 'pending')
                            <button class="btn btn-sm btn-success" onclick="approvePayment({{ $payment->id }})"><i class="bi bi-check-lg"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="rejectPayment({{ $payment->id }})"><i class="bi bi-x-lg"></i></button>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $payments->links() }}
    </div>
</div>

<!-- Scripts for forms -->
<form id="update-form" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="form-status">
    <input type="hidden" name="admin_note" id="form-note">
</form>

<script>
    function approvePayment(id) {
        if(confirm('Approve this payment and enroll student?')) {
            let form = document.getElementById('update-form');
            form.action = '/admin/payments/' + id;
            document.getElementById('form-status').value = 'approved';
            form.submit();
        }
    }

    function rejectPayment(id) {
        let note = prompt("Enter reason for rejection (optional):");
        if(note !== null) {
            let form = document.getElementById('update-form');
            form.action = '/admin/payments/' + id;
            document.getElementById('form-status').value = 'rejected';
            document.getElementById('form-note').value = note;
            form.submit();
        }
    }
</script>
@endsection
