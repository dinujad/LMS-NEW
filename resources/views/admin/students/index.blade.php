@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Students Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus"></i> Add Student
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.students.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by ID, name, email or phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td><span class="badge bg-light text-dark border">{{ $student->student_id ?? '-' }}</span></td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone ?? '-' }}</td>
                        <td>
                             <span class="badge bg-{{ $student->is_active ? 'success' : 'secondary' }}">
                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $student->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <div class="btn-group">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info" title="View Profile"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Are you sure?')) document.getElementById('delete-{{ $student->id }}').submit()"><i class="bi bi-trash"></i></button>
                            </div>
                            <form id="delete-{{ $student->id }}" action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $students->links() }}
    </div>
</div>
@endsection
