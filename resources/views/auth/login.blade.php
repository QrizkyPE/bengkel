@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Bengkel Logo">
        </div>
        <h1 class="login-title">Bengkel</h1>
        <p class="login-subtitle">CV. Arbella Lebak Sejahtera</p>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
                </div>
                @error('email')
                    <span class="invalid-feedback d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </div>
                @error('password')
                    <span class="invalid-feedback d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </div>

            {{-- @if (Route::has('password.request'))
                <div class="text-center mt-3">
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        Forgot Your Password?
                    </a>
                </div>
            @endif --}}
        </form>
    </div>
    
    {{-- <div class="login-footer">
        <p>&copy; {{ date('Y') }} Bengkel. All rights reserved.</p>
    </div> --}}
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #f5f8fb;
        background-image: linear-gradient(135deg, #f5f8fb 0%, #e0e9f5 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .login-container {
        width: 100%;
        max-width: 450px;
        padding: 15px;
        margin: auto;
    }
    
    .login-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .login-logo {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .login-logo img {
        height: 80px;
        width: auto;
    }
    
    .login-title {
        text-align: center;
        font-weight: 700;
        font-size: 24px;
        color: var(--primary-color);
        margin-bottom: 5px;
    }
    
    .login-subtitle {
        text-align: center;
        color: #6c757d;
        margin-bottom: 25px;
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
    }
    
    .input-group-text {
        background-color: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        font-weight: 600;
        padding: 10px 15px;
    }
    
    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    
    .login-footer {
        text-align: center;
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Hide navbar and container elements only on login page */
    body.login-page .navbar,
    body.login-page .container-fluid > .row > div.sidebar {
        display: none;
    }
    
    body.login-page .container-fluid {
        padding: 0;
    }
    
    body.login-page .container-fluid > .row > div.col-md-12 {
        padding: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add login-page class to body when on login page
        document.body.classList.add('login-page');
    });
</script>
@endpush
