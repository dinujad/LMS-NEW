<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LMS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS (Reusing Student Theme) -->
    <link rel="stylesheet" href="{{ asset('css/student-kanakku.css') }}">
    
    <style>
        /* Admin Specific Overrides if needed */
        .sidebar .nav-link.active {
            color: #7638ff; /* Ensure active state is visible */
            background: rgba(118, 56, 255, 0.08); 
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon bg-dark"> <!-- Darker logo for Admin -->
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <span class="logo-text">Admin Panel</span>
        </div>

        <div class="nav flex-column">
            
            <div class="nav-section-title">Main</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-title">Management</div>
            
            <a href="{{ route('admin.courses.index') }}" class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                <span>Courses</span>
            </a>
            
            <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Students</span>
            </a>

            <a href="{{ route('admin.enrollments.index') }}" class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                <i class="bi bi-person-check-fill"></i>
                <span>Enrollments</span>
            </a>

            <div class="nav-section-title">Finance & Delivery</div>

            <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <i class="bi bi-currency-dollar"></i>
                <span>Payments</span>
            </a>
            
             <a href="{{ route('admin.tute-delivery.index') }}" class="nav-link {{ request()->routeIs('admin.tute-delivery.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>Tute Delivery</span>
            </a>

            <div class="nav-section-title">Academic</div>

            <a href="{{ route('admin.exams.index') }}" class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i>
                <span>Exams</span>
            </a>

            <a href="{{ route('admin.results.index') }}" class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Exam Results</span>
            </a>
            
            <a href="{{ route('admin.notices.index') }}" class="nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i>
                <span>Notices</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4 mb-4">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
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
                
                <div class="search-bar d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search anything...">
                </div>
            </div>

            <div class="user-profile">
                <div class="notification-bell">
                    <i class="bi bi-bell"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=333&color=fff" alt="admin" class="profile-avatar me-2">
                        <span class="d-none d-lg-block fw-bold text-dark">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Sign out</button>
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
    </script>
</body>
</html>
