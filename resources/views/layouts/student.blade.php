<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - LMS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/student-kanakku.css') }}">
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <span class="logo-text">BIO LMS</span>
        </div>

        <div class="nav flex-column">
            
            <div class="nav-section-title">Main</div>
            
            <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="nav-section-title">Learning</div>

            <a href="{{ route('student.study-time.index') }}" class="nav-link {{ request()->routeIs('student.study-time.index') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>My Study Time</span>
            </a>

            <a href="{{ route('student.biology-ai') }}" class="nav-link {{ request()->routeIs('student.biology-ai') ? 'active' : '' }}">
                <i class="bi bi-robot"></i>
                <span>Biology AI</span>
            </a>

            <a href="{{ route('student.marks-analyst.index') }}" class="nav-link {{ request()->routeIs('student.marks-analyst.index') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>My Marks Analyst</span>
            </a>

            <a href="{{ route('student.courses.my') }}" class="nav-link {{ request()->routeIs('student.courses.my') ? 'active' : '' }}">
                <i class="bi bi-book-half"></i>
                <span>My Courses</span>
            </a>
            
            <a href="{{ route('student.courses.index') }}" class="nav-link {{ request()->routeIs('student.courses.index') ? 'active' : '' }}">
                <i class="bi bi-search"></i>
                <span>Browse Courses</span>
            </a>

             <a href="{{ route('student.exams.index') }}" class="nav-link {{ request()->routeIs('student.exams.index') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i>
                <span>My Exams</span>
            </a>

            <div class="nav-section-title">Account</div>
            
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('student.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>

        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center">
                <i class="bi bi-list mobile-toggle" id="sidebarToggle"></i>
                
                <!-- Breadcrumbs or Page Title could go here -->
                <div class="search-bar d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search courses...">
                    </div>
            </div>

            <!-- Learning Timer -->
            <div class="d-none d-md-flex align-items-center me-4 px-3 py-2 rounded-pill bg-soft-primary text-primary" style="background: rgba(118, 56, 255, 0.1); color: #7638ff;">
                <i class="bi bi-clock-history me-2"></i>
                <span class="small fw-bold text-uppercase me-2" style="font-size: 0.75rem; opacity: 0.8;">Session</span>
                <span id="learningTimer" class="fw-bold font-monospace">00m 00s</span>
            </div>

            <div class="user-profile">
                <!-- Notifications -->
                <div class="notification-bell">
                    <i class="bi bi-bell"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <!-- Language/Settings placeholders (optional) -->
                
                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=7638ff&color=fff" alt="mdo" class="profile-avatar me-2">
                        <span class="d-none d-lg-block fw-bold text-dark">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('student.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Dynamic Content -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
        
        <footer class="mt-5 text-muted text-center text-small pb-4">
            <p class="mb-0 fw-medium">&copy; {{ date('Y') }} Developed By <span class="fw-bold">Dinuja D</span> @ E media solution (pvt) Ltd</p>
        </footer>

    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Simple Sidebar Toggle Logic for Mobile
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        
        if(toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // Session Timer Logic
        (function() {
            const timerDisplay = document.getElementById('learningTimer');
            if(timerDisplay) {
                // Get or Set Start Time
                let start = sessionStorage.getItem('learningSessionStart');
                if (!start) {
                    start = Date.now();
                    sessionStorage.setItem('learningSessionStart', start);
                }

                function updateTimer() {
                    const now = Date.now();
                    const diff = Math.floor((now - start) / 1000); // seconds

                    const hrs = Math.floor(diff / 3600);
                    const mins = Math.floor((diff % 3600) / 60);
                    const secs = diff % 60;

                    let text = '';
                    if(hrs > 0) text += `${hrs}h `;
                    text += `${mins}m ${secs}s`;

                    timerDisplay.innerText = text;
                }

                setInterval(updateTimer, 1000);
                updateTimer();
            }
        })();

        // Heartbeat Logic for Study Time Tracking (Backend)
        (function() {
            // Send heartbeat every 60 seconds
            setInterval(() => {
                // Only send if tab is active/visible to ensure accurate "study" time?
                // For now, simpler: send if page is open.
                
                fetch("{{ route('student.study-time.heartbeat') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(err => console.error('Heartbeat failed', err));

            }, 60000); // 1 Minute
        })();
    </script>
</body>
</html>
