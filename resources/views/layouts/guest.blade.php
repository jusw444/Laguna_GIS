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
            --light-gray: #f8f9fa;
            --border-gray: #dee2e6;
        }

        body {
            background-color: var(--light-gray);
            min-height: 100vh;
            padding-top: 70px; /* Adjusted for fixed navbar height */
        }

        .sidebar {
            min-height: calc(100vh - 70px);
            background-color: var(--light-gray);
            border-right: 1px solid var(--border-gray);
        }

        #map {
            height: calc(100vh - 70px);
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* ===== NAVBAR STYLES ===== */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            background: linear-gradient(135deg, var(--dark-red), var(--primary-red)) !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            min-height: 70px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--accent-red);
        }

        .navbar-nav .nav-item {
            margin: 0 5px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus,
        .navbar-nav .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        /* REMOVED the border effect on hover/click */
        .navbar-nav .nav-link::after {
            display: none; /* This removes the border effect */
        }

        /* Dropdown Styles */
        .navbar-nav .dropdown-menu {
            background: white;
            border: 1px solid var(--light-red);
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            margin-top: 5px !important;
            z-index: 1050;
        }

        .navbar-nav .dropdown-item {
            padding: 0.75rem 1.25rem;
            color: var(--dark-red);
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .navbar-nav .dropdown-item:hover,
        .navbar-nav .dropdown-item:focus {
            background-color: var(--light-red);
            color: white;
            border-left-color: var(--accent-red);
            padding-left: 1.5rem;
        }

        /* Fixed the active state for dropdown items */
        .navbar-nav .dropdown-item.active {
            background-color: var(--primary-red) !important;
            color: white !important;
            border-left-color: var(--accent-red);
        }

        .navbar-nav .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            color: var(--primary-red);
        }

        .navbar-nav .dropdown-item:hover i,
        .navbar-nav .dropdown-item:focus i,
        .navbar-nav .dropdown-item.active i {
            color: white;
        }

        .dropdown-toggle::after {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        /* User Section Styles */
        .navbar-text {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
        }

        .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: white;
            transform: translateY(-1px);
        }

        /* Navbar Toggler */
        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Main content area styling */
        .main-content {
            min-height: calc(100vh - 70px);
            padding: 2rem 0;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }

        .content-header {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 1.5rem;
            margin-bottom: 0;
        }

        .content-body {
            padding: 2rem;
        }

        .page-title {
            color: var(--dark-red);
            font-weight: 600;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--light-red);
            padding-bottom: 0.5rem;
        }

        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-red);
            margin-bottom: 1rem;
        }

        /* ===== MOBILE RESPONSIVE STYLES ===== */
        @media (max-width: 991.98px) {
            body {
                padding-top: 0;
            }
            
            .navbar {
                position: relative;
                min-height: 60px;
            }
            
            .navbar-collapse {
                background: linear-gradient(135deg, var(--dark-red), var(--primary-red));
                padding: 1rem;
                border-radius: 0 0 10px 10px;
                margin-top: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem !important;
                margin: 2px 0;
            }
            
            .navbar-nav .dropdown-menu {
                background: rgba(255, 255, 255, 0.95);
                border: none;
                box-shadow: none;
                margin: 5px 0 5px 15px;
            }
            
            .navbar-nav .dropdown-item {
                padding: 0.5rem 1rem;
            }
            
            .main-content {
                min-height: auto;
                padding: 1rem 0;
            }
        }

        @media (min-width: 992px) {
            .navbar-nav .nav-link {
                margin: 0 2px;
            }
            
            .dropdown-menu {
                animation: fadeInDown 0.3s ease;
            }
            
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        /* Welcome page specific */
        .welcome-hero {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 4rem 2rem;
            border-radius: 15px;
            margin-bottom: 3rem;
            text-align: center;
        }

        .welcome-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .welcome-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .welcome-hero {
                padding: 2rem 1rem;
            }
            
            .welcome-hero h1 {
                font-size: 2rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <i class="fas fa-globe-americas"></i> Laguna GIS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shapefiles.*') ? 'active' : '' }}" 
                           href="{{ route('welcome') }}">
                            <i class="fas fa-layer-group me-1"></i> Home
                        </a>
                    </li>
                    <!-- Analysis Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs(['flood-areas.*', 'health-status.*', 'land-use.*']) ? 'active' : '' }}" 
                           href="#" id="analysisDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-line me-1"></i> View Maps
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="analysisDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('flood-areas.*') ? 'active' : '' }}" 
                                   href="{{ route('landing.flood') }}">
                                    <i class="fas fa-water me-2"></i> Flood Areas
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('health-status.*') ? 'active' : '' }}" 
                                   href="{{ route('landing.health') }}">
                                    <i class="fas fa-heartbeat me-2"></i> Health Status
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('land-use.*') ? 'active' : '' }}" 
                                   href="{{ route('landing.land') }}">
                                    <i class="fas fa-map-marked-alt me-2"></i> Land Use
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="p-4">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns
            var dropdownElements = document.querySelectorAll('.dropdown-toggle');
            dropdownElements.forEach(function(dropdownToggle) {
                var dropdown = new bootstrap.Dropdown(dropdownToggle);
            });

            // Add active state management
            var currentPath = window.location.pathname;
            var navLinks = document.querySelectorAll('.nav-link:not(.dropdown-toggle)');
            
            navLinks.forEach(function(link) {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Close dropdowns when clicking outside (desktop only)
            if (window.innerWidth >= 992) {
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown')) {
                        var openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                        openDropdowns.forEach(function(dropdown) {
                            var dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.previousElementSibling);
                            if (dropdownInstance) {
                                dropdownInstance.hide();
                            }
                        });
                    }
                });

                // Hover functionality for desktop
                var dropdownItems = document.querySelectorAll('.nav-item.dropdown');
                
                dropdownItems.forEach(function(item) {
                    item.addEventListener('mouseenter', function() {
                        var dropdownInstance = bootstrap.Dropdown.getInstance(this.querySelector('.dropdown-toggle'));
                        if (dropdownInstance) {
                            dropdownInstance.show();
                        }
                    });
                    
                    item.addEventListener('mouseleave', function() {
                        var dropdownInstance = bootstrap.Dropdown.getInstance(this.querySelector('.dropdown-toggle'));
                        if (dropdownInstance) {
                            setTimeout(function() {
                                dropdownInstance.hide();
                            }, 200);
                        }
                    });
                });
            }

            // Mobile menu close on link click
            if (window.innerWidth < 992) {
                var navLinks = document.querySelectorAll('.navbar-nav .nav-link');
                var navbarToggler = document.querySelector('.navbar-toggler');
                var navbarCollapse = document.querySelector('.navbar-collapse');
                
                navLinks.forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (navbarCollapse.classList.contains('show')) {
                            bootstrap.Collapse.getInstance(navbarCollapse).hide();
                        }
                    });
                });
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            // Reinitialize hover functionality if needed
        });
    </script>
    
    @stack('scripts')
</body>
</html>