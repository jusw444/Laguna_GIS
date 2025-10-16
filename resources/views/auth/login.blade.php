@extends('layouts.auth')

@section('title', 'Login - GIS Application')

@section('content')
<div class="min-h-screen d-flex align-items-center justify-content-center bg-gray-100 py-4 py-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-globe-americas fa-3x text-primary"></i>
                    </div>
                    <h2 class="h3 fw-bold text-gray-900 mb-2">
                        Sign in to GIS Application
                    </h2>
                    <p class="text-muted mb-0">
                        Laguna Province Geographic Information System
                    </p>
                </div>

                <!-- Session Status -->
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Login Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white text-center py-3">
                        <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Account Login</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input id="email" name="email" type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="Enter your email" 
                                           value="{{ old('email') }}" 
                                           required autocomplete="email" autofocus>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input id="password" name="password" type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Enter your password" 
                                           required autocomplete="current-password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label text-muted" for="remember_me">
                                        Remember me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none text-primary small">
                                        Forgot your password?
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign in
                                </button>
                            </div>

                            <!-- Register Link -->
                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Don't have an account?
                                    <a href="{{ route('register') }}" class="text-decoration-none fw-medium text-primary">
                                        Sign up here
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <div class="mt-4 p-3 bg-light border rounded text-center">
                    <h6 class="fw-medium text-primary mb-2">Demo Credentials:</h6>
                    <div class="small text-muted">
                        <div><span class="fw-medium">Email:</span> demo@lagunagis.ph</div>
                        <div><span class="fw-medium">Password:</span> password</div>
                    </div>
                </div>

                <!-- Weather Info -->
                <div class="mt-4 text-center">
                    <p class="text-muted small mb-2">2 cm of rain Wednesday</p>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gray-100 {
        background-color: #f8f9fa !important;
    }
    
    .text-gray-900 {
        color: #212529 !important;
    }
    
    .min-h-screen {
        min-height: 100vh;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .form-control:focus {
        border-color: #d32f2f;
        box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #d32f2f, #b71c1c);
        border-color: #d32f2f;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #b71c1c, #981b1b);
        border-color: #b71c1c;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(211, 47, 47, 0.3);
    }
    
    .btn-outline-primary {
        color: #d32f2f;
        border-color: #d32f2f;
    }
    
    .btn-outline-primary:hover {
        background-color: #d32f2f;
        border-color: #d32f2f;
        color: white;
    }
    
    .text-primary {
        color: #d32f2f !important;
    }
    
    .border-primary {
        border-color: #d32f2f !important;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endsection