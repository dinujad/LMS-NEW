@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create Notice</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.notices.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.notices.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Target Audience</label>
                        <select name="course_id" class="form-select">
                            <option value="">Global (All Students)</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select a specific course or leave blank for everyone.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expiry Date (Optional)</label>
                        <input type="date" name="expires_at" class="form-control">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_published" value="1" checked>
                        <label class="form-check-label">Publish Immediately</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Publish Notice</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
