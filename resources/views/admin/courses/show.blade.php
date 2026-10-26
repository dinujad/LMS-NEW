@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <h2 class="fw-bold mb-0">{{ $course->title }}</h2>
             @if($course->is_published)
                <span class="badge bg-soft-success rounded-pill px-3">Published</span>
            @else
                <span class="badge bg-soft-warning rounded-pill px-3">Draft</span>
            @endif
        </div>
        <p class="text-muted small mb-0">{{ Str::limit($course->description, 100) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.index') }}" class="btn btn-light border text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i> Edit Details
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Course Content (Tutes) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                     <div class="bg-soft-info rounded-3 p-2 me-3">
                        <i class="bi bi-collection-play fs-4 text-info"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Course Structure</h5>
                        <p class="text-muted small mb-0">Manage lessons, videos, and downloads.</p>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addTuteModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Material
                </button>
            </div>
            <div class="card-body p-0">
                 <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Title</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Content</th>
                                <th class="py-3">Visibility</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($course->tutes as $index => $tute)
                            <tr>
                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $tute->title }}</td>
                                <td>
                                    @if($tute->type == 'file')
                                        <span class="badge bg-soft-primary text-primary"><i class="bi bi-file-earmark-text me-1"></i> Document</span>
                                    @elseif($tute->type == 'live_class')
                                        <span class="badge bg-soft-danger text-danger"><i class="bi bi-camera-video me-1"></i> Live Class</span>
                                    @elseif($tute->type == 'recording_class')
                                        <span class="badge bg-soft-warning text-warning"><i class="bi bi-play-btn me-1"></i> Recording</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if($tute->video_url)
                                            @if($tute->type == 'live_class')
                                                <a href="{{ $tute->video_url }}" target="_blank" class="btn btn-sm btn-light text-danger border rounded-pill px-3">
                                                    <i class="bi bi-camera-video me-1"></i> Join Live
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-light text-danger border rounded-pill px-3 video-trigger" data-url="{{ $tute->video_url }}" data-title="{{ $tute->title }}">
                                                    <i class="bi bi-youtube me-1"></i> Watch Video
                                                </button>
                                            @endif
                                        @endif
                                        
                                        @if($tute->file_path)
                                            <a href="{{ asset('storage/' . $tute->file_path) }}" target="_blank" class="btn btn-sm btn-light text-primary border rounded-pill px-3">
                                                <i class="bi bi-download me-1"></i> Download Tute
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($tute->is_published)
                                        <span class="badge bg-soft-success text-success"><i class="bi bi-eye me-1"></i> Visible</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-muted"><i class="bi bi-eye-slash me-1"></i> Hidden</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                     <form action="{{ route('admin.tutes.destroy', $tute) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this material?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger border rounded-circle" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-files display-4 mb-3 d-block opacity-25"></i>
                                        No learning materials uploaded yet.
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

    <!-- Exams Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
             <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                     <div class="bg-soft-warning rounded-3 p-2 me-3">
                        <i class="bi bi-trophy fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Exams & Quizzes</h5>
                        <p class="text-muted small mb-0">Manage course assessments.</p>
                    </div>
                </div>
                 <a href="{{ route('admin.exams.create') }}?course_id={{ $course->id }}" class="btn btn-light btn-sm border fw-bold text-dark">
                    <i class="bi bi-plus-lg me-1"></i> Create Exam
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Exam Title</th>
                                <th class="py-3">Duration</th>
                                <th class="py-3">Attempts</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                             @forelse($course->exams as $exam)
                             <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $exam->title }}</td>
                                <td><i class="bi bi-clock me-1 text-muted"></i> {{ $exam->duration_minutes }} mins</td>
                                <td>{{ $exam->max_attempts }} Max</td>
                                <td>
                                    @if($exam->is_published)
                                        <span class="badge bg-soft-success">Published</span>
                                    @else
                                        <span class="badge bg-soft-secondary">Draft</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-light text-primary border me-1">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>
                                </td>
                             </tr>
                             @empty
                             <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-clipboard-x display-4 mb-3 d-block opacity-25"></i>
                                        No exams have been created for this course.
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
</div>

<!-- Add Tute Modal -->
<div class="modal fade" id="addTuteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.courses.tutes.store', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Course Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Title</label>
                        <input type="text" name="title" class="form-control bg-light border-0" placeholder="Lecture Title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Material Type</label>
                        <select name="type" id="material_type" class="form-select bg-light border-0" onchange="toggleType(this.value)">
                            <option value="recording_class">Recording Class (Video + Tute)</option>
                            <option value="live_class">Live Class (Zoom + Tute)</option>
                            <option value="file">Tute Only (Document)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="video_input">
                        <label class="form-label fw-bold small text-uppercase" id="video_label">Video URL</label>
                        <input type="url" name="video_url" class="form-control bg-light border-0" placeholder="Paste link here...">
                    </div>

                    <div class="mb-3" id="file_input">
                        <label class="form-label fw-bold small text-uppercase">Tute Attachment (Optional)</label>
                        <input type="file" name="file_path" class="form-control bg-light border-0">
                        <div class="form-text">Supported formats: PDF, DOCX, Images</div>
                    </div>

                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" checked>
                        <label class="form-check-label fw-bold" for="flexSwitchCheckChecked">Publish Immediately</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Upload Content</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="videoModalTitle">Video Player</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="videoFrame" src="" title="Video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.youtube.com/iframe_api"></script>

<script>
    let player;
    let updateTimer;
    let isPlayerReady = false;
    let queuedVideoId = null;

    // Initialize YouTube Player
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('videoFrame', { // Admin uses 'videoFrame' logic slightly differently, but wait, the admin view uses a Bootstrap Modal with an iframe.
            // Wait, previous admin implementation used a simple bootstrap modal with a generic iframe for the "Watch Video" button.
            // But user asked for the "Popup" earlier. 
            // Actually, for consistency, if the user liked the "Learning Mode" layout for the student, they might want it for admin too?
            // BUT the request was specifically about the video not loading in the STUDENT view (implied by the screenshot contexts).
            // However, the previous step I implemented a "Popup Player" for Admin.
            // Let's stick to fixing the POPUP logic in Admin if necessary, but the Admin view sends a URL to a simple iframe src.
            // The Student view uses the Custom API Overlay which is complex.
            // The Admin view logic I wrote in Step 270 uses `embedUrl = 'https://www.youtube.com/embed/' ...` directly assigned to `videoFrame.src`.
            // That is simple and robust usually. The ISSUE user reported was likely about the CUSTOM PLAYER in Student View.
            
            // So for Admin, I will just keep it simple unless requested. 
            // However, the `replace_file_content` I am calling right now is for `admin/courses/show.blade.php`.
            // Wait, I should double check what I wrote for Admin.
            // Admin view currently uses a Bootstrap Modal with a standard iframe src update. It does NOT use the complex API + Custom Controls.
            // So video loading issues there are less likely unless the Regex failed.
            // I will update the Regex in Admin view just to be safe, but NOT full API logic unless needed.
        });
    }
</script>

<!-- RE-READING CONTEXT -->
<!-- The user reported "me vidiya hodai eth video eka load wen neeeee" (This way is good but video is not loading) -->
<!-- The screenshot provided was of the STUDENT view (Learning Mode). -->
<!-- So fixing Student View (previous tool call) was the priority. -->
<!-- I should probably leave Admin View alone if it's working, OR update its Regex. -->
<!-- Let's just update the Regex in Admin View to be safe, but keep the simple Modal logic as it's for previewing. -->

<script>
    function toggleType(val) {
        const videoInput = document.getElementById('video_input');
        const videoLabel = document.getElementById('video_label');
        const fileInput = document.getElementById('file_input');
        const fileField = document.querySelector('[name="file_path"]');
        const videoField = document.querySelector('[name="video_url"]');

        if(val === 'file') {
            videoInput.classList.add('d-none');
            videoField.required = false;
        } else {
            videoInput.classList.remove('d-none');
            videoField.required = true;
            
            if(val === 'live_class') {
                videoLabel.innerText = 'Zoom/Meeting Link';
                videoField.placeholder = 'https://zoom.us/j/...';
            } else {
                videoLabel.innerText = 'Video URL (YouTube / Vimeo)';
                videoField.placeholder = 'https://vimeo.com/... or https://youtube.com/...';
            }
        }
    }
    
    // Initialize default state
    document.addEventListener('DOMContentLoaded', function() {
        toggleType(document.getElementById('material_type').value);

        const videoModal = document.getElementById('videoModal');
        const videoFrame = document.getElementById('videoFrame');
        const videoModalTitle = document.getElementById('videoModalTitle');
        
        document.querySelectorAll('.video-trigger').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const title = this.getAttribute('data-title');
                
                let embedUrl = url;
                
                // Improved Regex for Admin Preview as well
                const ytRegExp = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/;
                const match = url.match(ytRegExp);
                
                if (match && match[1]) {
                    embedUrl = 'https://www.youtube.com/embed/' + match[1] + '?autoplay=1';
                }
                
                videoFrame.src = embedUrl;
                videoModalTitle.textContent = title;
                
                const bsModal = new bootstrap.Modal(videoModal);
                bsModal.show();
            });
        });
        
        // Stop video when modal is hidden
        videoModal.addEventListener('hidden.bs.modal', function () {
            videoFrame.src = "";
        });
    });
</script>
@endsection
