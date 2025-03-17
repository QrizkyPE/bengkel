<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @stack('styles')
    <style>
        /* Add these styles for the sidebar */
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            margin: 0.2rem 0;
        }

        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                @auth
                    <div class="col-md-2 sidebar">
                        <div class="position-sticky">
                            <ul class="nav flex-column">
                                @if(auth()->user()->role === 'service')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}" href="{{ route('requests.index') }}">
                                            <i class="fas fa-clipboard-list"></i> Work Orders
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('unfilled.work.orders') ? 'active' : '' }}" href="{{ route('unfilled.work.orders') }}">
                                            <i class="fas fa-file-alt"></i> Unfilled Work Orders
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('work.orders.history') ? 'active' : '' }}" href="{{ route('work.orders.history') }}">
                                            <i class="fas fa-history"></i> History
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->role === 'estimator')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('estimations.index') ? 'active' : '' }}" href="{{ route('estimations.index') }}">
                                            <i class="fas fa-file-invoice-dollar"></i> Estimasi
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('estimations.history') ? 'active' : '' }}" href="{{ route('estimations.history') }}">
                                            <i class="fas fa-history"></i> History
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->role === 'billing')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('billing.index') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                                            <i class="fas fa-file-invoice-dollar"></i> Billing
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('billing.history') ? 'active' : '' }}" href="{{ route('billing.history') }}">
                                            <i class="fas fa-history"></i> History
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-10 main-content">
                        @yield('content')
                    </div>
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
    @stack('scripts')
</body>
</html>
