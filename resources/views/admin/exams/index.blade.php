@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Exams</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.exams.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New Exam
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Duration</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->course->title }}</td>
                        <td>{{ $exam->duration_minutes }} mins</td>
                        <td>{{ $exam->questions_count }}</td>
                        <td>
                            <span class="badge bg-{{ $exam->is_published ? 'success' : 'secondary' }}">
                                {{ $exam->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-outline-info" title="Manage Qs"><i class="bi bi-question-circle"></i> Questions</a>
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this exam?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No exams created.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $exams->links() }}
    </div>
</div>
@endsection
