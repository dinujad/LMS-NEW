@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-4">
    <div>
        <h2 class="fw-bold mb-0">Courses Management</h2>
        <p class="text-muted small">Manage your courses, curriculums, and students.</p>
    </div>
    <div>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> New Course
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Search courses...">
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <!-- Filters could go here -->
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Course</th>
                        <th class="py-3">Price</th>
                        <th class="py-3">Students</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($courses as $course)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 position-relative">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumb" class="rounded-3 object-fit-cover shadow-sm" style="width: 60px; height: 60px;">
                                    @else
                                        <div class="bg-soft-primary rounded-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-book fs-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">{{ $course->title }}</h6>
                                    <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                        {{ Str::limit($course->description, 50) }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($course->price > 0)
                                <span class="fw-bold text-dark">LKR {{ number_format($course->price, 2) }}</span>
                            @else
                                <span class="badge bg-soft-success">Free</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people me-2 text-muted"></i>
                                <span class="fw-bold">{{ $course->students_count }}</span>
                            </div>
                        </td>
                        <td>
                            @if($course->is_published)
                                <span class="badge bg-soft-success px-3 py-2 rounded-pill">Published</span>
                            @else
                                <span class="badge bg-soft-warning px-3 py-2 rounded-pill">Draft</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Manage Content">
                                    <i class="bi bi-gear me-1"></i> Manage
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-light text-muted border" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this course?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger border" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle p-4 mb-3">
                                    <i class="bi bi-journal-plus fs-1 text-muted"></i>
                                </div>
                                <h6 class="text-muted mb-3">No courses found</h6>
                                <a href="{{ route('admin.courses.create') }}" class="btn btn-sm btn-primary">Create Your First Course</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($courses->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $courses->links() }}
    </div>
    @endif
</div>
@endsection
