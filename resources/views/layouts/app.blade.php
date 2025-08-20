<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Bengkel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    @stack('styles')
    <style>
        :root {
            --primary-color: #3b7ddd;
            --primary-hover: #2d6bcc;
            --sidebar-bg: #222e3c;
            --sidebar-color: #a2b5cd;
            --sidebar-active-bg: #2c3b4f;
            --sidebar-active-color: #ffffff;
            --sidebar-hover-bg: #2c3b4f;
            --sidebar-hover-color: #ffffff;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Navbar styles */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(34, 39, 46, 0.15);
            padding: 0.75rem 1rem;
            z-index: 1030;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.25rem;
        }
        
        .navbar-brand:hover {
            color: var(--primary-hover);
        }
        
        .navbar .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 0.35rem;
        }
        
        .navbar .dropdown-item {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        
        .navbar .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .navbar .dropdown-item i {
            margin-right: 0.5rem;
        }
        
        .navbar .nav-link {
            color: #6e7d91;
            font-weight: 600;
            padding: 0.5rem 1rem;
        }
        
        .navbar .nav-link:hover {
            color: var(--primary-color);
        }
        
        .navbar .dropdown-toggle::after {
            margin-left: 0.5rem;
            vertical-align: middle;
        }
        
        .navbar .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .navbar .user-dropdown {
            display: flex;
            align-items: center;
        }
        
        .navbar .user-name {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .navbar .user-role {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1040;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(34, 39, 46, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background-color: var(--primary-hover);
            transform: scale(1.05);
        }
        
        .sidebar-toggle:focus {
            outline: none;
        }

        /* Sidebar styles */
        .sidebar {
            min-height: calc(100vh - 62px);
            background-color: var(--sidebar-bg);
            padding-top: 1.5rem;
            transition: all 0.3s ease;
            width: 250px;
            position: fixed;
            left: 0;
            top: 61px;
            bottom: 0;
            z-index: 1020;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar .nav-item {
            width: 100%;
        }

        .sidebar .nav-link {
            color: var(--sidebar-color);
            padding: 0.75rem 1.25rem;
            margin: 0.2rem 0.75rem;
            border-radius: 0.35rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            font-size: 1rem;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            padding: 0.75rem;
            margin: 0.2rem 0.5rem;
            text-align: center;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.1rem;
        }
        
        .sidebar-header {
            color: #ffffff;
            padding: 0.75rem 1.25rem;
            margin: 0.2rem 0.75rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            font-weight: 600;
            opacity: 0.6;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar.collapsed .sidebar-header {
            text-align: center;
            padding: 0.75rem 0.5rem;
            margin: 0.2rem 0.5rem;
        }

        .main-content {
            transition: all 0.3s ease;
            padding: 1.5rem;
            background-color: #f8f9fa;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
        
        /* Responsive sidebar - improved for mobile */
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1050;
            }
            
            .sidebar.mobile-visible {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                width: 70px;
                transform: translateX(-100%);
            }
            
            .sidebar.collapsed.mobile-visible {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                transition: margin-left 0.3s ease, width 0.3s ease;
            }
            
            .main-content.sidebar-visible {
                margin-left: 0;
            }
            
            /* Mobile overlay */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            /* Position the toggle button in a better place for mobile */
            .sidebar-toggle {
                bottom: 20px;
                left: 20px;
            }
        }
    </style>
</head>
<body>
    {{-- <script>
        // Diagnostic script to check if required libraries are loaded
        window.addEventListener('DOMContentLoaded', function() {
            console.log('DIAGNOSTIC: DOMContentLoaded fired');
            console.log('DIAGNOSTIC: jQuery available?', typeof jQuery !== 'undefined');
            console.log('DIAGNOSTIC: Bootstrap available?', typeof bootstrap !== 'undefined');
            
            // Check if modal functionality works
            setTimeout(function() {
                console.log('DIAGNOSTIC: Bootstrap Modal available?', typeof bootstrap.Modal !== 'undefined');
                console.log('DIAGNOSTIC: resetPasswordModal element exists?', !!document.getElementById('resetPasswordModal'));
                
                // Add global click handler to debug button clicking
                document.addEventListener('click', function(e) {
                    if (e.target.id === 'resetPasswordSubmit' || e.target.closest('#resetPasswordSubmit')) {
                        console.log('DIAGNOSTIC: Reset button clicked via global handler');
                    }
                });
            }, 1000);
        });
    </script> --}}
    <div id="app">
        @if(!request()->routeIs('login') && !request()->routeIs('password.request') && !request()->routeIs('password.reset'))
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-tools me-2"></i>Bengkel
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item d-none d-md-inline-block">
                                <span class="nav-link">
                                    <i class="fas fa-user-circle"></i> Role: 
                                    <span class="badge bg-primary">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                </span>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('Login') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary d-flex align-items-center justify-content-center text-white">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <div class="ms-2 d-none d-lg-block">
                                            <p class="user-name">{{ Auth::user()->name }}</p>
                                            <p class="user-role">{{ ucfirst(Auth::user()->role) }}</p>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                        <i class="fas fa-key me-1"></i> Reset Password
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @endif

        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        <div class="container-fluid p-0">
            <div class="row g-0">
                @auth
                    @if(!request()->routeIs('login') && !request()->routeIs('password.request') && !request()->routeIs('password.reset'))
                    <div id="sidebar" class="sidebar">
                        <div class="position-sticky">
                            <ul class="nav flex-column">
                                @if(auth()->user()->role === 'admin')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt"></i> Dashboard
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.work-orders*') ? 'active' : '' }}" href="{{ route('admin.work-orders') }}">
                                            <i class="fas fa-clipboard-list"></i> All Work Orders
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.estimations') ? 'active' : '' }}" href="{{ route('admin.estimations') }}">
                                            <i class="fas fa-calculator"></i> All Estimations
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.invoices') ? 'active' : '' }}" href="{{ route('admin.invoices') }}">
                                            <i class="fas fa-file-invoice-dollar"></i> All Invoices
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                            <i class="fas fa-users"></i> User Management
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->role === 'estimator')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('requests.index') ? 'active' : '' }}" href="{{ route('requests.index') }}">
                                            <i class="fas fa-home"></i> Home
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('unfilled.work.orders') ? 'active' : '' }}" href="{{ route('unfilled.work.orders') }}">
                                            <i class="fas fa-file-alt"></i> <span>Unfilled WO</span>
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('work.orders.history') ? 'active' : '' }}" href="{{ route('work.orders.history') }}">
                                            <i class="fas fa-history"></i> <span>History</span>
                                        </a>
                                    </li> --}}
                                @endif

                                @if(auth()->user()->role === 'estimator')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('estimations.index') ? 'active' : '' }}" href="{{ route('estimations.index') }}">
                                            <i class="fas fa-file-invoice-dollar"></i> <span>Estimasi</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('estimations.history') ? 'active' : '' }}" href="{{ route('estimations.history') }}">
                                            <i class="fas fa-history"></i> <span>History</span>
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->role === 'billing')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('billing.index') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                                            <i class="fas fa-file-invoice-dollar"></i> <span>Billing</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('billing.invoices.index') ? 'active' : '' }}" href="{{ route('billing.invoices.index') }}">
                                            <i class="fas fa-file-invoice"></i> <span>Invoice</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('billing.history') ? 'active' : '' }}" href="{{ route('billing.history') }}">
                                            <i class="fas fa-history"></i> <span>History</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Sidebar Toggle Button -->
                    <button id="sidebarToggle" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div id="mainContent" class="main-content">
                        @yield('content')
                    </div>
                    @else
                    <div class="col-md-12">
                        @yield('content')
                    </div>
                    @endif
                @else
                    <div class="col-md-12">
                        @yield('content')
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    @stack('scripts')
    
    @auth
        @if(!request()->routeIs('login') && !request()->routeIs('password.request') && !request()->routeIs('password.reset'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('mainContent');
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                const isMobile = window.innerWidth <= 768;
                
                // Check if sidebar state is saved in localStorage
                const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                
                // Initialize sidebar state
                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
                
                // On mobile, hide sidebar by default
                if (isMobile) {
                    sidebar.classList.remove('mobile-visible');
                }
                
                // Toggle sidebar when button is clicked
                sidebarToggle.addEventListener('click', function() {
                    if (isMobile) {
                        sidebar.classList.toggle('mobile-visible');
                        sidebarOverlay.classList.toggle('active');
                    } else {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                        
                        // Change icon based on sidebar state
                        const icon = this.querySelector('i');
                        if (sidebar.classList.contains('collapsed')) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        } else {
                            icon.classList.remove('fa-bars');
                            icon.classList.add('fa-times');
                        }
                        
                        // Save sidebar state to localStorage (only for desktop)
                        localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                    }
                });
                
                // Close sidebar when clicking overlay
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-visible');
                    sidebarOverlay.classList.remove('active');
                });
                
                // Close sidebar when menu item is clicked on mobile
                const mobileMenuItems = sidebar.querySelectorAll('.nav-link');
                mobileMenuItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (isMobile && sidebar.classList.contains('mobile-visible')) {
                            sidebar.classList.remove('mobile-visible');
                            sidebarOverlay.classList.remove('active');
                        }
                    });
                });
                
                // Set the correct icon on page load for desktop
                if (!isMobile) {
                    const toggleIcon = sidebarToggle.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        toggleIcon.classList.remove('fa-times');
                        toggleIcon.classList.add('fa-bars');
                    } else {
                        toggleIcon.classList.remove('fa-bars');
                        toggleIcon.classList.add('fa-times');
                    }
                }
                
                // Add responsive behavior
                function handleResize() {
                    const currentIsMobile = window.innerWidth <= 768;
                    
                    // If switching between mobile and desktop
                    if (currentIsMobile !== isMobile) {
                        // Reload the page to reset everything properly
                        window.location.reload();
                    }
                }
                
                // Handle resize events - throttled to improve performance
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(handleResize, 250);
                });
            });
        </script>
        @endif
    @endauth

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="resetPasswordAlert" class="alert d-none" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <div id="resetPasswordMessage"></div>
                    </div>
                    <form id="resetPasswordForm" method="POST" action="{{ route('reset.password') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <div class="invalid-feedback" id="current_password_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="invalid-feedback" id="new_password_error"></div>
                            <div class="form-text">Password must be at least 8 characters long and different from current password.</div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <div class="invalid-feedback" id="new_password_confirmation_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="resetPasswordSubmit">Reset Password</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @endpush

    <!-- Standalone Reset Password Script -->
    <script>
        // Wait for document to fully load
        window.addEventListener('load', function() {
            // console.log('Window loaded - initializing reset password functionality');
            
            // Get elements
            const resetBtn = document.getElementById('resetPasswordSubmit');
            const resetForm = document.getElementById('resetPasswordForm');
            const currentPasswordInput = document.getElementById('current_password');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');
            const resetAlert = document.getElementById('resetPasswordAlert');
            const resetMessage = document.getElementById('resetPasswordMessage');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const resetModal = document.getElementById('resetPasswordModal');
            
            // Log element detection
            // console.log('Reset password elements found:', {
            //     resetBtn: !!resetBtn,
            //     resetForm: !!resetForm,
            //     currentPasswordInput: !!currentPasswordInput,
            //     newPasswordInput: !!newPasswordInput,
            //     confirmPasswordInput: !!confirmPasswordInput,
            //     resetAlert: !!resetAlert,
            //     resetMessage: !!resetMessage,
            //     csrfToken: !!csrfToken,
            //     resetModal: !!resetModal
            // });
            
            if (!resetBtn || !resetForm) {
                console.error('Critical reset password elements not found!');
                return;
            }
            
            // Clear form on modal hide
            if (resetModal) {
                resetModal.addEventListener('hidden.bs.modal', function() {
                    resetForm.reset();
                    hideAlert();
                    clearValidationErrors();
                });
            }
            
            // Password match validation
            if (newPasswordInput && confirmPasswordInput) {
                [newPasswordInput, confirmPasswordInput].forEach(input => {
                    input.addEventListener('input', validatePasswordMatch);
                });
            }
            
            // Submit button click handler
            resetBtn.addEventListener('click', function() {
                console.log('Reset button clicked!');
                submitResetForm();
            });
            
            // Functions
            function validatePasswordMatch() {
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity("Passwords don't match");
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }
            
            function clearValidationErrors() {
                const invalidElements = resetForm.querySelectorAll('.is-invalid');
                const errorMessages = resetForm.querySelectorAll('.invalid-feedback');
                
                invalidElements.forEach(el => el.classList.remove('is-invalid'));
                errorMessages.forEach(el => el.textContent = '');
            }
            
            function showAlert(type, message) {
                resetAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
                resetAlert.classList.add(`alert-${type}`);
                resetMessage.textContent = message;
            }
            
            function hideAlert() {
                resetAlert.classList.add('d-none');
            }
            
            function submitResetForm() {
                // Validate form
                if (!resetForm.checkValidity()) {
                    resetForm.reportValidity();
                    return;
                }
                
                // Show loading state
                resetBtn.disabled = true;
                resetBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
                // Clear previous errors
                clearValidationErrors();
                
                // Collect form data
                const formData = new FormData(resetForm);
                
                // Log form data for debugging
                const formDataObject = {};
                formData.forEach((value, key) => {
                    formDataObject[key] = key.includes('password') ? '******' : value;
                });
                console.log('Submitting form data:', formDataObject);
                
                // Create serialized data as fallback
                const urlEncodedData = new URLSearchParams(formData).toString();
                
                // Send fetch request
                fetch(resetForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: urlEncodedData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    // Parse JSON regardless of status code to get validation errors
                    return response.json().then(data => ({
                        status: response.status,
                        body: data
                    }));
                })
                .then(({ status, body }) => {
                    console.log('Response data received:', body);
                    
                    if (status === 200 && body.success) {
                        // Success case
                        showAlert('success', body.success);
                        
                        // Show success alert box
                        alert('Password has been reset successfully!');
                        
                        // Close modal after delay
                        setTimeout(() => {
                            const bsModal = bootstrap.Modal.getInstance(resetModal);
                            if (bsModal) {
                                bsModal.hide();
                            }
                        }, 300);
                    } else if (status === 422 && body.errors) {
                        // Validation errors
                        showAlert('danger', 'Please correct the errors below.');
                        
                        // Display each validation error
                        Object.entries(body.errors).forEach(([field, messages]) => {
                            const input = document.getElementById(field);
                            const errorDisplay = document.getElementById(`${field}_error`);
                            
                            if (input && errorDisplay) {
                                input.classList.add('is-invalid');
                                errorDisplay.textContent = messages[0];
                                
                                // If it's a current password error, also show an alert
                                if (field === 'current_password') {
                                    alert('Current password is incorrect!');
                                }
                            }
                        });
                    } else {
                        // Unexpected response format
                        showAlert('danger', 'An unexpected error occurred.');
                        alert('An error occurred while resetting your password.');
                    }
                })
                .catch(error => {
                    console.error('Error during form submission:', error);
                    showAlert('danger', 'Failed to reset password. Please try again.');
                    alert('Failed to reset password. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    resetBtn.disabled = false;
                    resetBtn.textContent = 'Reset Password';
                });
            }
        });
    </script>
</body>
</html>
