@extends('layouts.student')

@section('content')
<h2 class="mb-4">Browsing All Courses</h2>

<div class="row">
    @forelse($courses as $course)
    @php
        $isEnrolled = in_array($course->id, $enrolledCourseIds);
    @endphp
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
            @else
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                    <span class="fs-1">Course</span>
                </div>
            @endif
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold">{{ $course->title }}</h5>
                <p class="card-text text-muted small">{{ Str::limit($course->description, 100) }}</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary">{{ $course->price > 0 ? 'LKR '.number_format($course->price, 2) : 'Free' }}</span>
                    
                    @if($isEnrolled)
                        <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-play-circle me-1"></i> Access Course
                        </a>
                    @else
                        <!-- Trigger Modal for Payment -->
                        <button type="button" class="btn btn-outline-primary btn-sm px-4" 
                                onclick="openPaymentModal({{ $course->id }}, '{{ addslashes($course->title) }}', {{ $course->price }})">
                            <i class="bi bi-lock-fill me-1"></i> Enroll Now
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info py-5 text-center">
            <h3>No courses available yet.</h3>
            <p>Please check back later.</p>
        </div>
    </div>
    @endforelse
</div>

{{ $courses->links() }}

<!-- Payment Slip Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Enroll in <span id="modalCourseTitle" class="fw-bold"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        <i class="bi bi-info-circle me-1"></i> ඔබ මෙම පන්තිය සදහා ගාස්තු ගෙවා නොමැත. කරුණාකර ගාස්තු ගෙවා රිසිට් පත මෙතනට upload කරන්න.
                    </div>

                    <div class="mb-3 p-3 bg-light rounded text-start small">
                        <h6 class="text-center mb-3 border-bottom pb-2">Bank Transfer Details</h6>
                        
                        <div class="mb-2">
                            <strong>Sampath Bank</strong> - Anamaduwa Branch<br>
                            Name: A.H.M.L.C.S RATHNAYAKA<br>
                            A/C: <span class="text-primary fw-bold user-select-all">114 354 998 265</span>
                        </div>

                        <div class="mb-2">
                            <strong>BOC (Lanka Bank)</strong> - Anamaduwa Branch<br>
                            Name: A.H.M. RATHNAYAKA<br>
                            A/C: <span class="text-primary fw-bold user-select-all">81 966 575</span>
                        </div>

                        <div class="mb-0">
                            <strong>Peoples Bank</strong> - Anamaduwa Branch<br>
                            Name: A.H.M.L.C.S RATHNAYAK<br>
                            A/C: <span class="text-primary fw-bold user-select-all">267 200 12 00 92 707</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (LKR)</label>
                        <input type="number" class="form-control" id="amount" name="amount" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="proof_image" class="form-label">Upload Payment Slip (PDF, JPG, PNG)</label>
                        <input type="file" class="form-control" id="proof_image" name="proof_image" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Please ensure the slip is clear and readable.</small>
                    </div>

                    <div class="mb-3">
                        <label for="transaction_ref" class="form-label">Reference Number (Optional)</label>
                        <input type="text" class="form-control" id="transaction_ref" name="transaction_ref" placeholder="Enter bank reference if available">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit for Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPaymentModal(courseId, courseTitle, price) {
        // Set dynamic content
        document.getElementById('modalCourseTitle').innerText = courseTitle;
        document.getElementById('amount').value = price;
        
        // Set form action dynamically
        // Note: The route pattern is /student/courses/{course}/enroll
        // We need to construct this URL. 
        // Best way is to use a base route and append ID, or use a JS string replace if we had the route template.
        // Or simpler: hardcode the prefix if the route structure is stable.
        const actionUrl = "{{ url('/student/courses') }}/" + courseId + "/enroll";
        document.getElementById('paymentForm').action = actionUrl;

        // Show Modal
        var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        myModal.show();
    }

    // Client-side File Size Validation
    document.getElementById('paymentForm').addEventListener('submit', function(event) {
        const fileInput = document.getElementById('proof_image');
        const file = fileInput.files[0];
        
        if (file) {
            const fileSize = file.size / 1024 / 1024; // in MB
            const maxSize = 5; // 5MB limit
            
            if (fileSize > maxSize) {
                event.preventDefault();
                alert('File size exceeds ' + maxSize + 'MB. Please upload a smaller file.');
                return;
            }
        }
    });
</script>
@endsection
