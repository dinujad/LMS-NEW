@extends('layouts.student')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fc;">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold" style="font-family: 'Outfit', sans-serif;">My Marks Analyst</h1>
            <p class="text-muted small mb-0">Track and visualize your academic performance</p>
        </div>
        <div>
           <a href="{{ route('student.dashboard') }}" class="btn btn-white shadow-sm btn-sm px-3 rounded-pill text-muted fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
           </a>
        </div>
    </div>

    <!-- Chart & Form Row -->
    <div class="row g-4 mb-4">
        <!-- Chart Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        <span class="icon-circle bg-primary-soft me-2 text-primary" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-graph-up"></i>
                        </span>
                        Performance Trend
                    </h6>
                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2" style="background: rgba(13, 110, 253, 0.1);">Analytics</span>
                </div>
                <div class="card-body p-4">
                    <div class="chart-area" style="position: relative; height: 380px;">
                        <canvas id="marksChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions & Form Column -->
        <div class="col-lg-4">
            <!-- Sinhala Instructions -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-primary text-white overflow-hidden" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body p-4 position-relative">
                    <i class="bi bi-info-circle-fill position-absolute text-white" style="opacity: 0.15; font-size: 8rem; right: -20px; top: -30px;"></i>
                    <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="bi bi-lightbulb-fill me-2"></i> දැනුවත් කිරීමටයි</h5>
                    <ul class="mb-0 ps-3 small text-white-50" style="opacity: 0.9;">
                        <li class="mb-2">මෙමගින් ඔබගේ ඔන්ලයින් සහ භෞතික පන්තිවල ප්‍රගතිය එකම තැනකදී විශ්ලේෂණය කරයි.</li>
                        <li class="mb-2">පන්තියට සහභාගී වී ලියන Paper වල ලකුණු පහත පෝරමය මගින් ඇතුළත් කරන්න.</li>
                        <li>ප්‍රස්ථාරයේ රේඛාව ඉහළට ගමන් කිරීම ඔබගේ දියුණුවේ ලකුණකි.</li>
                    </ul>
                </div>
            </div>

            <!-- Add Marks Form -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="m-0 font-weight-bold text-success d-flex align-items-center">
                         <span class="icon-circle bg-success-soft me-2 text-success" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-plus-lg"></i>
                        </span>
                        Add Physical Marks
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show small shadow-sm border-0 border-start border-success border-4 rounded-3" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('student.marks-analyst.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="exam_name" class="form-label small text-uppercase fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Exam Name</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="exam_name" name="exam_name" placeholder="e.g. Unit Test 05" required style="font-size: 0.95rem;">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="mcq_marks" class="form-label small text-uppercase fw-bold text-muted text-warning" style="font-size: 0.75rem; letter-spacing: 0.5px;">MCQ Marks</label>
                                <input type="number" step="0.5" class="form-control form-control-lg bg-light border-0" id="mcq_marks" name="mcq_marks" placeholder="00" required>
                            </div>
                            <div class="col-6">
                                <label for="essay_marks" class="form-label small text-uppercase fw-bold text-muted text-primary" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #4b0082 !important;">Essay Marks</label>
                                <input type="number" step="0.5" class="form-control form-control-lg bg-light border-0" id="essay_marks" name="essay_marks" placeholder="00" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="total_marks" class="form-label small text-uppercase fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Possible Marks</label>
                            <input type="number" step="1" class="form-control form-control-lg bg-light border-0" id="total_marks" name="total_marks" value="100" required>
                        </div>
                        <div class="mb-4">
                            <label for="exam_date" class="form-label small text-uppercase fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">Date</label>
                            <input type="date" class="form-control form-control-lg bg-light border-0" id="exam_date" name="exam_date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg shadow-sm fw-bold rounded-3">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Save Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <!-- ... (Table Header kept same) ... -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="m-0 font-weight-bold text-gray-800 d-flex align-items-center">
                <i class="bi bi-clock-history me-2 text-muted"></i> Activity History
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small text-uppercase fw-bold border-0" style="font-size: 0.75rem;">Date</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold border-0" style="font-size: 0.75rem;">Exam Name</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold border-0" style="font-size: 0.75rem;">Type</th>
                            <th class="text-center py-3 text-secondary small text-uppercase fw-bold border-0" style="font-size: 0.75rem;">Breakdown</th>
                            <th class="text-center py-3 text-secondary small text-uppercase fw-bold border-0" style="font-size: 0.75rem;">Total</th>
                            <th class="text-end py-3 pe-4 text-secondary small text-uppercase fw-bold border-0" style="font-size: 0.75rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($allMarks->sortByDesc('exam_date') as $exam)
                        <tr>
                            <td class="ps-4 text-nowrap fw-medium text-dark">{{ $exam['exam_date'] }}</td>
                            <td class="fw-bold text-dark">{{ $exam['exam_name'] }}</td>
                            <td>
                                @if($exam['type'] == 'Online')
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-1 fw-normal" style="background: rgba(13, 110, 253, 0.1);">
                                        <i class="bi bi-laptop me-1"></i> Online
                                    </span>
                                @else
                                    <span class="badge bg-success-soft text-success rounded-pill px-3 py-1 fw-normal" style="background: rgba(25, 135, 84, 0.1);">
                                        <i class="bi bi-file-text me-1"></i> Physical
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-secondary small">
                                <span class="text-warning fw-bold">MCQ: {{ $exam['mcq_marks'] ?? 0 }}</span> | 
                                <span class="text-primary fw-bold" style="color: #4b0082 !important;">Essay: {{ $exam['essay_marks'] ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $percentage = ($exam['total_marks'] > 0) ? ($exam['marks_obtained'] / $exam['total_marks']) * 100 : 0;
                                    $color = $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                @endphp
                                <span class="fw-bold text-{{ $color }}">{{ number_format($exam['marks_obtained'], 1) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                @if($exam['type'] == 'Physical')
                                    <button class="btn btn-sm btn-white text-primary border-0 shadow-sm me-1" 
                                        onclick="editMark({{ json_encode($exam) }})"
                                        data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('student.marks-analyst.destroy', $exam['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white text-danger border-0 shadow-sm" data-bs-toggle="tooltip" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small"><i class="bi bi-lock-fill opacity-25"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <h6 class="fw-bold text-dark">No records found</h6>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editMarkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-white border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Marks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editMarkForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Exam Name</label>
                        <input type="text" class="form-control bg-light border-0" id="edit_exam_name" name="exam_name" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-uppercase fw-bold text-muted text-warning" style="font-size: 0.75rem;">MCQ Marks</label>
                            <input type="number" step="0.5" class="form-control bg-light border-0" id="edit_mcq_marks" name="mcq_marks" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-uppercase fw-bold text-muted text-primary" style="font-size: 0.75rem; color: #4b0082 !important;">Essay Marks</label>
                            <input type="number" step="0.5" class="form-control bg-light border-0" id="edit_essay_marks" name="essay_marks" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Total Possible Marks</label>
                        <input type="number" step="1" class="form-control bg-light border-0" id="edit_total_marks" name="total_marks" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Date</label>
                        <input type="date" class="form-control bg-light border-0" id="edit_exam_date" name="exam_date" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold shadow-sm rounded-3">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function editMark(exam) {
        document.getElementById('edit_exam_name').value = exam.exam_name;
        document.getElementById('edit_mcq_marks').value = exam.mcq_marks || 0;
        document.getElementById('edit_essay_marks').value = exam.essay_marks || 0;
        document.getElementById('edit_total_marks').value = exam.total_marks;
        document.getElementById('edit_exam_date').value = exam.exam_date; // Using Y-m-d format directly
        
        // Update Form Action
        let updateUrl = "{{ route('student.marks-analyst.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', exam.id);
        document.getElementById('editMarkForm').action = updateUrl;
        
        // Show Modal
        new bootstrap.Modal(document.getElementById('editMarkModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('marksChart').getContext('2d');
        
        // Data from Controller
        const labels = @json($labels);
        const mcqData = @json($mcqPoints);
        const essayData = @json($essayPoints);

        // Chart Configuration
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'ESSAY MARKS',
                        data: essayData,
                        borderColor: '#4b0082', // Purple
                        backgroundColor: '#4b0082',
                        borderWidth: 3,
                        pointBackgroundColor: '#4b0082',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#4b0082',
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'MCQ MARKS',
                        data: mcqData,
                        borderColor: '#ff6384', // Orange/Pink
                        backgroundColor: '#ff6384',
                        borderWidth: 3,
                        pointBackgroundColor: '#ff6384',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#ff6384',
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Outfit', sans-serif", size: 12, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#2c3e50',
                        bodyColor: '#2c3e50',
                        borderColor: '#e9ecef',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 105,
                        grid: {
                            color: '#f0f0f0',
                            borderDash: [2, 4],
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: "'Outfit', sans-serif" }
                        }
                    },
                    x: {
                        grid: {
                            display: true, 
                            color: '#f0f0f0',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: "'Outfit', sans-serif" }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    /* Modern Tweaks */
    body { font-family: 'Outfit', sans-serif; }
    .btn-white { background-color: #fff; border: 1px solid #e3e6f0; }
    .btn-white:hover { background-color: #f8f9fa; }
    .rounded-4 { border-radius: 1rem !important; }
    
    .form-control:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        background-color: #fff !important;
    }
</style>
@endsection
