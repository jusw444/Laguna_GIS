@extends('layouts.auth')

@section('title', 'Register - GIS Application')

@section('content')
<div class="min-h-screen d-flex align-items-center justify-content-center bg-gray-100 py-4 py-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-globe-americas fa-3x text-primary"></i>
                    </div>
                    <h2 class="h2 fw-bold text-gray-900 mb-2">
                        Create your account
                    </h2>
                    <p class="text-muted">
                        Join the Laguna Province GIS Platform
                    </p>
                </div>

                <!-- Register Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white text-center py-3">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Create New Account</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <!-- Name -->
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label fw-medium">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input id="name" name="name" type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               placeholder="Full Name" 
                                               value="{{ old('name') }}" 
                                               required autocomplete="name" autofocus>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email Address -->
                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label fw-medium">Email address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input id="email" name="email" type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               placeholder="Email address" 
                                               value="{{ old('email') }}" 
                                               required autocomplete="email">
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-medium">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input id="password" name="password" type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               placeholder="Password" 
                                               required autocomplete="new-password">
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input id="password_confirmation" name="password_confirmation" type="password" 
                                               class="form-control" 
                                               placeholder="Confirm Password" 
                                               required autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading mb-2"><i class="fas fa-info-circle me-2"></i>Password Requirements:</h6>
                                <ul class="mb-0 small">
                                    <li>Minimum 8 characters</li>
                                    <li>At least one uppercase letter</li>
                                    <li>At least one number</li>
                                    <li>At least one special character</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-medium text-primary">
                                        Sign in here
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Benefits Section -->
                <div class="mt-4 p-3 bg-light border rounded">
                    <h6 class="fw-medium text-primary mb-2"><i class="fas fa-star me-2"></i>Benefits of registering:</h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-unstyled small text-muted mb-2 mb-sm-0">
                                <li class="mb-1">
                                    <i class="fas fa-map-marked-alt text-primary me-2"></i>
                                    Access to interactive maps
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-layer-group text-primary me-2"></i>
                                    Manage shapefiles and layers
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-unstyled small text-muted">
                                <li class="mb-1">
                                    <i class="fas fa-chart-line text-primary me-2"></i>
                                    Perform spatial analysis
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-download text-primary me-2"></i>
                                    Export data and reports
                                </li>
                            </ul>
                        </div>
                    </div>
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
    
    .alert-info {
        background-color: #e3f2fd;
        border-color: #b3e0ff;
        color: #055160;
    }
    
    /* Smooth transitions */
    input, button {
        transition: all 0.2s ease-in-out;
    }
    
    input:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(211, 47, 47, 0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
    }
</style>
@endsection