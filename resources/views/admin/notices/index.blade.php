@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Notices</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.notices.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Create Notice
        </a>
    </div>
</div>

<div class="row">
    @forelse($notices as $notice)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="badge bg-{{ $notice->course ? 'info' : 'primary' }}">
                    {{ $notice->course ? $notice->course->title : 'Global Notice' }}
                </span>
                <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $notice->title }}</h5>
                <p class="card-text">{{ $notice->content }}</p>
                @if($notice->expires_at)
                    <p class="card-text"><small class="text-danger">Expires: {{ $notice->expires_at->format('Y-m-d') }}</small></p>
                @endif
            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" onsubmit="return confirm('Delete this notice?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">No notices published yet.</div>
    </div>
    @endforelse
</div>
<div class="mt-3">
    {{ $notices->links() }}
</div>
@endsection
