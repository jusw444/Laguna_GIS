<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>

    <!-- Bootstrap, Leaflet & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --danger-main: #dc3545;
            --danger-dark: #b71c1c;
            --danger-hover: #a71d2a;
            --light-bg: #f8f9fa;
            --sidebar-width: 250px;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar (Top) */
        .navbar {
            background: linear-gradient(135deg, var(--danger-dark), var(--danger-main));
            color: #fff;
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .navbar h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar .logout-btn {
            color: #fff;
            background: transparent;
            border: 1px solid #fff;
            border-radius: 5px;
            padding: 5px 10px;
            transition: 0.3s;
        }

        .navbar .logout-btn:hover {
            background-color: #fff;
            color: var(--danger-main);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--danger-dark);
            color: #fff;
            padding-top: 70px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            z-index: 999;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f8d7da;
            padding: 12px 20px;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a i {
            width: 20px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--danger-hover);
            color: #fff;
            border-left: 4px solid #fff;
        }

        /* Content */
        .content {
            margin-left: var(--sidebar-width);
            padding: calc(70px + 20px) 30px 30px;
            /* 70 for navbar, +20 spacing */
            transition: all 0.3s;
        }


        /* Map container (Leaflet) */
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
        }

        .legend {
            background: white;
            padding: 10px;
            border-radius: 5px;
            line-height: 1.4;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                left: -250px;
                transition: all 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            .toggle-btn {
                display: inline-block;
                cursor: pointer;
            }
        }

        .toggle-btn {
            display: none;
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        /* Fix Leaflet controls hidden behind navbar */
        .leaflet-top,
        .leaflet-bottom {
            z-index: 1100 !important;
            /* higher than navbar (1000) */
        }

        #map {
            position: relative;
            z-index: 100;
            /* keep map content below navbar but above sidebar */
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar">
        <div class="d-flex align-items-center gap-2">
            <span class="toggle-btn text-white me-2" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </span>
            <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h5>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span><i class="fas fa-user me-1"></i> Hello, {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="{{ route('flood-areas.index') }}" class="{{ request()->is('admin/flood-areas*') ? 'active' : '' }}">
            <i class="fas fa-water"></i> Flood Areas
        </a>
        <a href="{{ route('health-status.index') }}"
            class="{{ request()->is('admin/health-status*') ? 'active' : '' }}">
            <i class="fas fa-heartbeat"></i> Health Status
        </a>
        <a href="{{ route('land-use.index') }}" class="{{ request()->is('admin/land-use*') ? 'active' : '' }}">
            <i class="fas fa-map-marked-alt"></i> Land Use
        </a>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');

            // Toggle sidebar visibility (mobile)
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });

            // Highlight active nav link
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
