@extends('layouts.student')

@section('content')
<style>
    /* Learning Mode Overrides */
    .sidebar { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 20px; max-width: 100%; }
    .topbar .mobile-toggle { display: block !important; }
    
    /* Layout Styles */
    .learning-container { height: calc(100vh - 100px); }
    .lesson-list-col { height: 100%; overflow-y: auto; border-right: 1px solid #eee; }
    .player-col { height: 100%; overflow-y: auto; }
    
    .lesson-item { cursor: pointer; transition: all 0.2s; border-radius: 8px; margin-bottom: 8px; }
    .lesson-item:hover { background-color: #f8f9fa; }
    .lesson-item.active { background-color: rgba(118, 56, 255, 0.1); border-left: 4px solid var(--primary-color); }
    .lesson-item.active .lesson-title { color: var(--primary-color); font-weight: 700; }
    
    .video-wrapper { 
        position: relative; 
        padding-bottom: 56.25%; 
        height: 0; 
        background: #000; 
        border-radius: 12px; 
        overflow: hidden; 
        margin-bottom: 20px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
    }
    
    .video-wrapper .player-surface {
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    #ytPlayerMount {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }
    .video-wrapper .player-surface iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Blocks accidental clicks on YouTube chrome corners */
    .yt-blocker {
        position: absolute;
        bottom: 52px;
        right: 0;
        width: 100px;
        height: 40px;
        background: transparent;
        z-index: 12;
        pointer-events: auto;
    }

    /* Click layer: play/pause on tap; blocks reaching YT UI (no share link) */
    .video-click-layer {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 52px;
        z-index: 10;
        cursor: pointer;
        background: transparent;
    }
    .video-wrapper.mode-vimeo .video-click-layer { display: none; }

    .student-player-chrome {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 52px;
        padding: 6px 10px;
        z-index: 25;
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(transparent, rgba(0,0,0,0.88));
        border-radius: 0 0 12px 12px;
    }
    .student-player-chrome.d-none { display: none !important; }
    .student-player-chrome .btn-chrome {
        background: rgba(255,255,255,0.12);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 6px;
        width: 38px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
        flex-shrink: 0;
    }
    .student-player-chrome .btn-chrome:hover {
        background: rgba(255,255,255,0.22);
    }
    .student-player-chrome .progress-wrap {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .student-player-chrome input[type="range"].timeline {
        flex: 1;
        height: 6px;
        cursor: pointer;
        accent-color: var(--primary-color, #7638ff);
    }
    .student-player-chrome .time-label {
        color: rgba(255,255,255,0.85);
        font-size: 11px;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        min-width: 88px;
        text-align: right;
    }
    .student-player-chrome input[type="range"].vol {
        width: 72px;
        height: 6px;
        cursor: pointer;
        accent-color: #fff;
    }
    
    /* Watermark Style */
    .video-watermark {
        position: absolute;
        z-index: 100;
        color: rgba(255, 255, 255, 0.25); /* Stronger as requested */
        font-size: 16px;
        font-weight: bold;
        pointer-events: none;
        user-select: none;
        white-space: nowrap;
        opacity: 0; /* Hidden by default */
        transition: opacity 1s ease-in-out; /* Smooth fade */
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    .video-watermark.visible {
        opacity: 1; /* relative to the color alpha */
    }

    .video-wrapper:fullscreen .video-click-layer {
        bottom: 52px;
    }
    :-webkit-full-screen .video-wrapper { width: 100%; height: 100%; }
    :fullscreen .video-wrapper { width: 100%; height: 100%; }

    .speed-select {
        background: rgba(255,255,255,0.12);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 6px;
        padding: 4px 6px;
        font-size: 12px;
        cursor: pointer;
        outline: none;
        height: 34px;
        max-width: 76px;
    }
    .speed-select option { background: #222; color: #fff; }

    .video-wrapper:fullscreen {
        padding-bottom: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #000;
    }
    .video-wrapper:fullscreen .player-surface,
    .video-wrapper:fullscreen .player-surface iframe {
        height: 100%;
    }

    .video-wrapper .placeholder-content {
        position: absolute;
        inset: 0;
        z-index: 40;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #0d0d0d;
        text-align: center;
        padding: 24px;
        border-radius: 12px;
    }
    .video-wrapper .placeholder-content.d-none { display: none !important; }

    @media print {
        body { display: none !important; }
    }
</style>

<div class="container-fluid learning-container">
    <div class="row h-100">
        <!-- Left Column: Lesson List -->
        <div class="col-lg-4 col-md-5 lesson-list-col pe-md-4">
            <div class="mb-4">
                <a href="{{ route('student.courses.my') }}" class="text-muted text-decoration-none small mb-2 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Back to Courses
                </a>
                <h4 class="fw-bold">{{ $course->title }}</h4>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">0% Complete</small>
            </div>

            @if($isEnrolled)
            <div class="list-group list-group-flush">
                @forelse($course->tutes as $index => $tute)
                    <div class="lesson-item p-3 {{ $index === 0 ? 'active' : '' }}"
                         onclick="playLesson(this)"
                         data-tute-id="{{ $tute->id }}">
                         
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-3 small">{{ $index + 1 }}</span>
                                <div>
                                    <div class="lesson-title mb-1 text-dark small">{{ $tute->title }}</div>
                                    <div class="small">
                                        @if($tute->type == 'file')
                                            <span class="badge bg-soft-secondary text-secondary" style="font-size: 0.65rem;">Document</span>
                                        @elseif($tute->type == 'live_class')
                                            <span class="badge bg-soft-danger text-danger" style="font-size: 0.65rem;">Live Class</span>
                                        @else
                                            <span class="badge bg-soft-primary text-primary" style="font-size: 0.65rem;">Video</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <i class="bi bi-play-circle text-muted fs-5"></i>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted small">No lessons available.</div>
                @endforelse
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-8 col-md-7 player-col ps-md-4 pt-3 pt-md-0">
            @if($isEnrolled)
                <!-- Video Player Area -->
                <div class="video-wrapper shadow-sm mode-youtube" id="courseVideoWrapper" oncontextmenu="return false;">

                    <div class="player-surface">
                        <div id="ytPlayerMount"></div>
                        <iframe id="vimeoPlayer" class="d-none" title="Vimeo" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                    </div>

                    <div id="videoClickLayer" class="video-click-layer" title="Tap to play / pause"></div>
                    <div class="yt-blocker" aria-hidden="true"></div>

                    <div id="watermark" class="video-watermark">{{ Auth::user()->name }} ({{ Auth::user()->id }})</div>

                    <div id="studentPlayerChrome" class="student-player-chrome d-none">
                        <button type="button" class="btn-chrome" id="studentPlayBtn" onclick="studentTogglePlay()" title="Play / Pause">
                            <i class="bi bi-play-fill fs-5" id="studentPlayIcon"></i>
                        </button>
                        <div class="progress-wrap">
                            <input type="range" class="timeline" id="studentTimeline" min="0" max="1000" value="0" step="1"
                                title="Seek">
                            <span class="time-label" id="studentTimeLabel">0:00 / 0:00</span>
                        </div>
                        <input type="range" class="vol" id="studentVolume" min="0" max="100" value="100" title="Volume"
                            aria-label="Volume">
                        <select id="videoSpeed" class="speed-select" onchange="changeVideoSpeed(this.value)" title="Speed">
                            <option value="0.5">0.5x</option>
                            <option value="1" selected>1x</option>
                            <option value="1.25">1.25x</option>
                            <option value="1.5">1.5x</option>
                            <option value="2">2x</option>
                        </select>
                        <button type="button" class="btn-chrome" id="fsBtn" onclick="toggleFullscreen()" title="Fullscreen">
                            <i class="bi bi-arrows-fullscreen" id="fsBtnIcon"></i>
                        </button>
                    </div>
                    
                    <div id="placeholderScreen" class="placeholder-content d-none">
                        <i class="bi bi-camera-video-off display-4 mb-3 text-muted"></i>
                        <h4 id="placeholderTitle">Select a Lesson</h4>
                        <p id="placeholderMsg" class="text-muted">Choose a lesson from the left.</p>
                        <a id="actionButton" href="#" target="_blank" class="btn btn-primary mt-3 d-none">Action</a>
                    </div>
                </div>

                <!-- Info -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="fw-bold mb-1" id="playingTitle">Select a Lesson</h3>
                                <p class="text-muted small mb-0" id="playingType">Course Introduction</p>
                            </div>
                            <div id="fileDownloadArea" class="d-none">
                                <a href="#" id="downloadBtn" target="_blank" class="btn btn-light border text-primary">
                                    <i class="bi bi-download me-1"></i> Download Material
                                </a>
                            </div>
                        </div>
                        <div class="text-muted small">
                            {!! nl2br(e($course->description)) !!}
                        </div>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    <div class="text-center">
                        <i class="bi bi-lock-fill display-1 opacity-25"></i>
                        <h3>Content Locked</h3>
                        <p>Enroll in the course to access.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($isEnrolled)
@php
    $studentLessonPathTpl = parse_url(
        route('student.courses.tute.lesson', ['course' => $course, 'tute' => '___TUTE___']),
        PHP_URL_PATH
    );
@endphp
<script>
    /* Path-only from Laravel (correct subdirectory) + current browser host (fixes www / HTTPS). */
    window.studentTutePayloadUrl = function (tuteId) {
        return @json($studentLessonPathTpl).split('___TUTE___').join(String(tuteId));
    };
</script>
@endif

<script>
    window.studentYtPlayer = null;
    window.studentPendingYoutubeId = null;
    window.studentProgressTimer = null;
    window.studentIsScrubbing = false;
    window.studentPlayerMode = 'none';

    window.onYouTubeIframeAPIReady = function () {
        if (window.studentPendingYoutubeId) {
            var id = window.studentPendingYoutubeId;
            window.studentPendingYoutubeId = null;
            studentBootstrapYoutube(id);
        }
    };

    function studentDestroyYoutubePlayer() {
        stopStudentProgressLoop();
        if (window.studentYtPlayer) {
            try { window.studentYtPlayer.destroy(); } catch (e) {}
            window.studentYtPlayer = null;
        }
        var mount = document.getElementById('ytPlayerMount');
        if (mount) mount.innerHTML = '';
    }

    function stopStudentProgressLoop() {
        if (window.studentProgressTimer) {
            clearInterval(window.studentProgressTimer);
            window.studentProgressTimer = null;
        }
    }

    function studentFormatTime(sec) {
        if (!isFinite(sec) || sec < 0) return '0:00';
        var m = Math.floor(sec / 60);
        var s = Math.floor(sec % 60);
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    function studentUpdateProgressUi() {
        var p = window.studentYtPlayer;
        if (!p || !p.getCurrentTime || window.studentIsScrubbing) return;
        var cur = p.getCurrentTime();
        var dur = p.getDuration();
        if (!dur || !isFinite(dur)) return;
        var tl = document.getElementById('studentTimeline');
        var lab = document.getElementById('studentTimeLabel');
        if (tl) tl.value = String(Math.round((cur / dur) * 1000));
        if (lab) lab.textContent = studentFormatTime(cur) + ' / ' + studentFormatTime(dur);
    }

    function startStudentProgressLoop() {
        stopStudentProgressLoop();
        window.studentProgressTimer = setInterval(studentUpdateProgressUi, 400);
    }

    function studentSyncPlayIcon(state) {
        var icon = document.getElementById('studentPlayIcon');
        if (!icon) return;
        var playing = state === 1;
        icon.classList.toggle('bi-play-fill', !playing);
        icon.classList.toggle('bi-pause-fill', playing);
    }

    function onStudentYtState(event) {
        studentSyncPlayIcon(event.data);
        if (event.data === 0) {
            studentSyncPlayIcon(2);
        }
    }

    function onStudentYtReady() {
        var vol = document.getElementById('studentVolume');
        if (vol && window.studentYtPlayer && window.studentYtPlayer.setVolume) {
            window.studentYtPlayer.setVolume(parseInt(vol.value, 10) || 0);
        }
        var sp = document.getElementById('videoSpeed');
        if (sp && window.studentYtPlayer && window.studentYtPlayer.setPlaybackRate) {
            window.studentYtPlayer.setPlaybackRate(parseFloat(sp.value) || 1);
        }
        startStudentProgressLoop();
        studentUpdateProgressUi();
    }

    function studentBootstrapYoutube(videoId) {
        var chrome = document.getElementById('studentPlayerChrome');
        var layer = document.getElementById('videoClickLayer');
        var wrap = document.getElementById('courseVideoWrapper');
        if (chrome) chrome.classList.remove('d-none');
        if (layer) layer.classList.remove('d-none');
        if (wrap) {
            wrap.classList.add('mode-youtube');
            wrap.classList.remove('mode-vimeo');
        }
        window.studentPlayerMode = 'youtube';

        if (window.studentYtPlayer && window.studentYtPlayer.loadVideoById) {
            window.studentYtPlayer.loadVideoById({ videoId: videoId, startSeconds: 0 });
            window.studentYtPlayer.playVideo();
            startStudentProgressLoop();
            return;
        }

        if (typeof YT === 'undefined' || !YT.Player) {
            window.studentPendingYoutubeId = videoId;
            return;
        }

        window.studentYtPlayer = new YT.Player('ytPlayerMount', {
            videoId: videoId,
            width: '100%',
            height: '100%',
            playerVars: {
                autoplay: 1,
                controls: 0,
                rel: 0,
                modestbranding: 1,
                fs: 0,
                disablekb: 1,
                playsinline: 1,
                iv_load_policy: 3,
                origin: window.location.origin
            },
            events: {
                onReady: onStudentYtReady,
                onStateChange: onStudentYtState
            }
        });
    }

    function studentTogglePlay() {
        if (window.studentPlayerMode !== 'youtube' || !window.studentYtPlayer || !window.studentYtPlayer.getPlayerState) return;
        var st = window.studentYtPlayer.getPlayerState();
        if (st === 1) {
            window.studentYtPlayer.pauseVideo();
        } else {
            window.studentYtPlayer.playVideo();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var wrapper = document.querySelector('.video-wrapper');
        var clickLayer = document.getElementById('videoClickLayer');
        var watermark = document.getElementById('watermark');

        var blockRightClick = function (e) {
            e.preventDefault();
            return false;
        };
        if (wrapper) wrapper.addEventListener('contextmenu', blockRightClick);
        if (clickLayer) clickLayer.addEventListener('contextmenu', blockRightClick);
        var blocker = document.querySelector('.yt-blocker');
        if (blocker) blocker.addEventListener('contextmenu', blockRightClick);
        var chrome = document.getElementById('studentPlayerChrome');
        if (chrome) chrome.addEventListener('contextmenu', blockRightClick);

        if (clickLayer) {
            clickLayer.addEventListener('click', function () {
                studentTogglePlay();
            });
        }

        var timeline = document.getElementById('studentTimeline');
        if (timeline) {
            timeline.addEventListener('pointerdown', function () {
                window.studentIsScrubbing = true;
            });
            timeline.addEventListener('pointerup', function () {
                window.studentIsScrubbing = false;
                studentUpdateProgressUi();
            });
            ['pointerup', 'pointercancel'].forEach(function (ev) {
                document.addEventListener(ev, function () {
                    window.studentIsScrubbing = false;
                });
            });
            timeline.addEventListener('input', function () {
                if (!window.studentYtPlayer || !window.studentYtPlayer.seekTo) return;
                var dur = window.studentYtPlayer.getDuration();
                if (!dur || !isFinite(dur)) return;
                var t = (parseFloat(timeline.value) / 1000) * dur;
                window.studentYtPlayer.seekTo(t, true);
            });
        }

        var volEl = document.getElementById('studentVolume');
        if (volEl) {
            volEl.addEventListener('input', function () {
                if (window.studentYtPlayer && window.studentYtPlayer.setVolume) {
                    window.studentYtPlayer.setVolume(parseInt(volEl.value, 10) || 0);
                }
            });
        }

        document.addEventListener('fullscreenchange', function () {
            var icon = document.getElementById('fsBtnIcon');
            if (!icon) return;
            if (document.fullscreenElement) {
                icon.classList.remove('bi-arrows-fullscreen');
                icon.classList.add('bi-fullscreen-exit');
            } else {
                icon.classList.remove('bi-fullscreen-exit');
                icon.classList.add('bi-arrows-fullscreen');
            }
        });

        function cycleWatermark() {
            if (!watermark || !wrapper) return;
            var positions = [
                { top: '20px', left: '20px', right: 'auto', bottom: 'auto' },
                { top: '20px', right: '20px', left: 'auto', bottom: 'auto' },
                { bottom: '62px', left: '20px', top: 'auto', right: 'auto' },
                { bottom: '62px', right: '120px', top: 'auto', left: 'auto' },
                { top: '50%', left: '50%', transform: 'translate(-50%, -50%)', right: 'auto', bottom: 'auto' }
            ];
            watermark.classList.remove('visible');
            setTimeout(function () {
                var pos = positions[Math.floor(Math.random() * positions.length)];
                watermark.style.transform = pos.transform || 'none';
                watermark.style.top = pos.top;
                watermark.style.left = pos.left;
                watermark.style.right = pos.right;
                watermark.style.bottom = pos.bottom;
                watermark.classList.add('visible');
            }, 1000);
        }
        setInterval(cycleWatermark, 5000);
        cycleWatermark();

        var firstLesson = document.querySelector('.lesson-item');
        if (firstLesson) setTimeout(function () { playLesson(firstLesson); }, 500);
    });

    function toggleFullscreen() {
        var wrapper = document.getElementById('courseVideoWrapper') || document.querySelector('.video-wrapper');
        if (!wrapper) return;
        if (!document.fullscreenElement) {
            wrapper.requestFullscreen().catch(function (err) {
                alert('Fullscreen error: ' + err.message);
            });
        } else {
            document.exitFullscreen();
        }
    }

    function playLesson(element) {
        var tuteId = element.getAttribute('data-tute-id');
        if (!tuteId || typeof window.studentTutePayloadUrl !== 'function') {
            return;
        }
        document.querySelectorAll('.lesson-item').forEach(function (e) { e.classList.remove('active'); });
        element.classList.add('active');

        fetch(window.studentTutePayloadUrl(tuteId), {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (res) {
                return res.text().then(function (text) {
                    var data = null;
                    try {
                        data = text ? JSON.parse(text) : null;
                    } catch (e) {
                        throw new Error('bad_json');
                    }
                    if (!res.ok) {
                        var msg = (data && (data.message || data.error)) ? data.message || data.error : ('HTTP ' + res.status);
                        throw new Error(msg);
                    }
                    return data;
                });
            })
            .then(function (data) {
                applyLessonPayload(data);
            })
            .catch(function (err) {
                var hint = err && err.message ? err.message : 'Network error';
                if (hint === 'bad_json') {
                    hint = 'Server returned an invalid response (try logging in again or clear cache).';
                }
                alert('Could not load this lesson.\n\n' + hint);
            });
    }

    function applyLessonPayload(data) {
        var type = data.type || '';
        var url = data.video_url || '';
        var title = data.title || '';
        var file = data.file_url || '';

        document.getElementById('playingTitle').innerText = title;
        document.getElementById('playingType').innerText = type.replace('_', ' ').toUpperCase();

        var ph = document.getElementById('placeholderScreen');
        var vimeoFrame = document.getElementById('vimeoPlayer');
        var wrap = document.getElementById('courseVideoWrapper');

        if (file) {
            document.getElementById('fileDownloadArea').classList.remove('d-none');
            document.getElementById('downloadBtn').href = file;
        } else {
            document.getElementById('fileDownloadArea').classList.add('d-none');
        }

        if (type === 'recording_class' || (type === 'video_link' && url)) {
            ph.classList.add('d-none');

            var isVimeo = false;
            var vid = '';
            var vimeoMatch = url.match(/(?:vimeo\.com\/|player\.vimeo\.com\/video\/)([0-9]+)/);
            if (vimeoMatch && vimeoMatch[1]) {
                isVimeo = true;
                vid = vimeoMatch[1];
            }
            if (!vid) {
                var ytMatch = url.match(/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/);
                if (ytMatch && ytMatch[1]) vid = ytMatch[1];
            }

            if (isVimeo && vid) {
                studentDestroyYoutubePlayer();
                window.studentPlayerMode = 'vimeo';
                if (wrap) {
                    wrap.classList.add('mode-vimeo');
                    wrap.classList.remove('mode-youtube');
                }
                document.getElementById('studentPlayerChrome').classList.add('d-none');
                document.getElementById('videoClickLayer').classList.add('d-none');
                vimeoFrame.classList.remove('d-none');
                vimeoFrame.src = 'https://player.vimeo.com/video/' + vid + '?autoplay=1&title=0&byline=0&portrait=0';
            } else if (vid) {
                vimeoFrame.classList.add('d-none');
                vimeoFrame.src = '';
                studentBootstrapYoutube(vid);
            } else {
                studentDestroyYoutubePlayer();
                vimeoFrame.classList.add('d-none');
                vimeoFrame.src = '';
                document.getElementById('studentPlayerChrome').classList.add('d-none');
                document.getElementById('videoClickLayer').classList.add('d-none');
                window.studentPlayerMode = 'none';
            }
        } else {
            studentDestroyYoutubePlayer();
            vimeoFrame.classList.add('d-none');
            vimeoFrame.src = '';
            document.getElementById('studentPlayerChrome').classList.add('d-none');
            document.getElementById('videoClickLayer').classList.add('d-none');
            window.studentPlayerMode = 'none';
            ph.classList.remove('d-none');

            var ab = document.getElementById('actionButton');
            var pt = document.getElementById('placeholderTitle');
            var pm = document.getElementById('placeholderMsg');

            if (type === 'live_class') {
                pt.innerText = 'Live Session';
                pm.innerText = 'Join via Zoom';
                ab.classList.remove('d-none');
                ab.href = url || '#';
            } else {
                pt.innerText = 'Document';
                pm.innerText = 'Download file';
                ab.classList.add('d-none');
            }
        }
    }

    function changeVideoSpeed(speed) {
        var speedVal = parseFloat(speed);
        if (window.studentPlayerMode === 'youtube' && window.studentYtPlayer && window.studentYtPlayer.setPlaybackRate) {
            window.studentYtPlayer.setPlaybackRate(speedVal);
            return;
        }
        var vf = document.getElementById('vimeoPlayer');
        if (vf && vf.src && vf.src.includes('vimeo.com')) {
            vf.contentWindow.postMessage(JSON.stringify({ method: 'setPlaybackRate', value: speedVal }), '*');
        }
    }

    if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
        setInterval(function () {
            try {
                if (navigator.mediaDevices.displayMedia) {
                    var s = document.querySelector('.player-surface');
                    if (s) s.style.display = 'none';
                    alert('Screen Recording detected! Video paused.');
                }
            } catch (err) {}
        }, 3000);
    }

    document.addEventListener('keyup', function (e) {
        if (e.key === 'PrintScreen') {
            navigator.clipboard.writeText('');
            alert('Screenshots are disabled on this page.');
        }
    });

    window.addEventListener('blur', function () {
        var s = document.querySelector('.player-surface');
        if (s) s.style.opacity = '0';
    });
    window.addEventListener('focus', function () {
        var s = document.querySelector('.player-surface');
        if (s) s.style.opacity = '1';
    });
</script>
<script src="https://www.youtube.com/iframe_api"></script>
@endsection
