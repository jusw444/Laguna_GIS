<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laguna GIS - @yield('title', 'Guest')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-red: #d32f2f;
            --dark-red: #b71c1c;
            --light-red: #f44336;
            --accent-red: #ff5252;
        }
        
        .min-h-screen {
            min-height: 100vh;
        }
        
        .bg-gray-100 {
            background-color: #f8f9fa;
        }
        
        .text-gray-900 {
            color: #212529;
        }
        
        .font-sans {
            font-family: system-ui, -apple-system, sans-serif;
        }
        
        .antialiased {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .flex {
            display: flex;
        }
        
        .flex-col {
            flex-direction: column;
        }
        
        .items-center {
            align-items: center;
        }
        
        .justify-center {
            justify-content: center;
        }
        
        .pt-6 {
            padding-top: 1.5rem;
        }
        
        .w-20 {
            width: 5rem;
        }
        
        .h-20 {
            height: 5rem;
        }
        
        .fill-current {
            fill: currentColor;
        }
        
        .text-gray-500 {
            color: #6c757d;
        }
        
        /* Red theme for GIS */
        .gis-primary {
            background: linear-gradient(135deg, var(--dark-red), var(--primary-red)) !important;
        }
        
        .btn-gis-primary {
            background-color: var(--primary-red);
            border-color: var(--dark-red);
            color: white;
        }
        
        .btn-gis-primary:hover {
            background-color: var(--dark-red);
            border-color: var(--primary-red);
            color: white;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--dark-red), var(--primary-red));
            color: white;
            border-bottom: none;
            font-weight: 600;
        }
    </style>
    
    @stack('styles')
</head>

<body class="font-sans text-gray-900 antialiased">
    <!-- Optional: Simple navbar for guest pages -->
    <nav class="navbar navbar-expand-lg navbar-dark gis-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-globe-americas"></i> Laguna GIS
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('login') }}">Login</a>
                <a class="nav-link" href="{{ route('register') }}">Register</a>
            </div>
        </div>
    </nav>

        <div>
            @yield('content')
        </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @stack('scripts')
</body>

</html>