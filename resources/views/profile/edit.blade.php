@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">Manage Profile</h4>
                </div>
                <div class="card-body p-4">
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Profile updated successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <!-- Basic / Personal -->
                        <h5 class="text-primary mb-3">Personal Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number (Login)</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->phone }}" readonly disabled>
                                <small class="text-muted">Cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $user->whatsapp_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIC</label>
                                <input type="text" name="nic" class="form-control" value="{{ old('nic', $user->nic) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob) }}">
                            </div>
                        </div>

                        <!-- Academic -->
                        <h5 class="text-primary mb-3">Academic Details</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">School</label>
                                <input type="text" name="school" class="form-control" value="{{ old('school', $user->school) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Exam Year</label>
                                <input type="text" name="exam_year" class="form-control" value="{{ old('exam_year', $user->exam_year) }}">
                            </div>

                             <div class="col-md-4">
                                <label class="form-label">Medium</label>
                                <select name="medium" class="form-select">
                                    <option value="">Select...</option>
                                    @foreach(['Sinhala', 'English', 'Tamil'] as $m)
                                        <option value="{{ $m }}" {{ old('medium', $user->medium) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Contact -->
                        <h5 class="text-primary mb-3">Contact & Guardian</h5>
                        <div class="row g-3 mb-4">
                             <div class="col-md-12">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1', $user->address_line_1) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Address Line 2 (City)</label>
                                <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2', $user->address_line_2) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">District</label>
                                <input type="text" name="district" class="form-control" value="{{ old('district', $user->district) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Guardian Name</label>
                                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $user->guardian_name) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Guardian Phone</label>
                                <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone', $user->guardian_phone) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
