<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS Application - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary-red: #d32f2f;
            --dark-red: #b71c1c;
            --light-red: #f44336;
            --accent-red: #ff5252;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        #map {
            height: calc(100vh - 56px);
            width: 100%;
        }
        .legend {
            padding: 10px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border: 1px solid #ccc;
        }
        .layer-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .shapefile-card {
            transition: all 0.3s;
        }
        .shapefile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        /* Red theme overrides */
        .navbar-dark.bg-primary {
            background: linear-gradient(135deg, var(--dark-red), var(--primary-red)) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-red);
            border-color: var(--dark-red);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-red);
            border-color: var(--primary-red);
        }
        
        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #fff;
        }
        
        .dropdown-menu {
            border: 1px solid var(--light-red);
        }
        
        .dropdown-item:hover {
            background-color: var(--light-red);
            color: white;
        }
        
        .nav-link:hover {
            color: var(--accent-red) !important;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-globe-americas"></i> GIS Application
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shapefiles.index') }}">Shapefiles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('legends.index') }}">Legends</a>
                    </li>
                    <!-- Analysis Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="analysisDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Analysis
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="analysisDropdown">
                            <li><a class="dropdown-item" href="{{ route('analysis.flood-areas') }}">Flood Areas</a></li>
                            <li><a class="dropdown-item" href="{{ route('analysis.health-status') }}">Health Status</a></li>
                            <li><a class="dropdown-item" href="{{ route('analysis.land-use') }}">Land Use</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <span class="navbar-text me-3">Hello, {{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div>
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
