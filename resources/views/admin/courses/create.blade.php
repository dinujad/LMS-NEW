@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-4">
            <div>
                <h2 class="fw-bold mb-0">Create New Course</h2>
                <p class="text-muted small">Add a new course to your curriculum.</p>
            </div>
            <div>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-light border text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-soft-primary rounded-3 p-3 me-3">
                        <i class="bi bi-journal-plus fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Course Details</h5>
                        <p class="text-muted small mb-0">Fill in the information below to create a new course.</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">Course Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Advanced Mathematics 2024" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control bg-light border-0" id="description" name="description" rows="5" placeholder="Enter a detailed description of the course...">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="price" class="form-label fw-bold">Price (LKR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-muted">Rs.</span>
                                <input type="number" step="0.01" class="form-control form-control-lg bg-light border-0" id="price" name="price" value="{{ old('price', '0.00') }}" required>
                            </div>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Set to 0 for free courses.</div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="thumbnail" class="form-label fw-bold">Thumbnail Image</label>
                            <input class="form-control form-control-lg bg-light border-0" type="file" id="thumbnail" name="thumbnail" accept="image/*">
                            <div class="form-text mt-2">Recommended size: 800x600px</div>
                        </div>
                    </div>

                    <hr class="my-4 border-light">

                    <div class="mb-4">
                        <div class="form-check form-switch ps-0">
                            <label class="form-check-label fw-bold ms-5" for="is_published">Publish Course Immediately</label>
                            <input class="form-check-input ms-0 fs-4" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} style="margin-left: -2.5em !important;">
                            <p class="text-muted small ms-5 mt-1">If enabled, this course will be visible to students immediately after creation.</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-light btn-lg px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="bi bi-check-lg me-2"></i> Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
