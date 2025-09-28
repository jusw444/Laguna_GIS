@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="main-content">
        <div class="container">
            <!-- Main Content Card -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="content-card">
                        <div class="content-header">
                            <h3 class="mb-0"><i class="fas fa-info-circle me-2"></i>About the Application</h3>
                        </div>
                        <div class="content-body">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <h4 class="page-title">Welcome to Laguna GIS</h4>
                                    <p class="mb-4">This comprehensive GIS platform provides powerful tools for managing
                                        geographic data, creating interactive maps, and conducting spatial analysis
                                        specifically designed for Laguna Province.</p>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <a href="{{ route('shapefiles.index') }}"
                                                class="btn btn-primary w-100 d-flex align-items-center justify-content-center py-3">
                                                <i class="fas fa-layer-group me-2"></i>
                                                <span>Manage Shapefiles</span>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ route('legends.index') }}"
                                                class="btn btn-success w-100 d-flex align-items-center justify-content-center py-3">
                                                <i class="fas fa-palette me-2"></i>
                                                <span>Manage Legends</span>
                                            </a>
                                        </div>
                                        <div class="col-12">
                                            <a href="{{ route('flood-areas.index') }}"
                                                class="btn btn-info w-100 d-flex align-items-center justify-content-center py-3">
                                                <i class="fas fa-water me-2"></i>
                                                <span>Flood Analysis</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-4 mt-lg-0">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-0">
                                            <div id="welcomeMap" style="height: 300px; width: 100%; border-radius: 8px;">
                                            </div>
                                            <div class="text-center py-2 bg-light">
                                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>Laguna
                                                    Province, Philippines</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="row">
                <div class="col-12 mb-4">
                    <h2 class="page-title text-center">Key Features</h2>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center p-4 rounded-3">
                        <div class="feature-icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Upload Data</h5>
                        <p class="text-muted">Upload shapefiles and define land boundaries for Laguna Province with
                            easy-to-use tools</p>
                        <div class="mt-3">
                            <span class="badge bg-primary">Supported formats: .shp, .kml, .geojson</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center p-4 rounded-3">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Visualize</h5>
                        <p class="text-muted">View Laguna's geographic data on interactive maps with customizable legends
                            and layers</p>
                        <div class="mt-3">
                            <span class="badge bg-success">Interactive Maps</span>
                            <span class="badge bg-success">Custom Legends</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center p-4 rounded-3">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Analyze</h5>
                        <p class="text-muted">Perform spatial analysis for flood risk assessment, health status monitoring,
                            and land use planning</p>
                        <div class="mt-3">
                            <span class="badge bg-info">Flood Analysis</span>
                            <span class="badge bg-info">Health Status</span>
                            <span class="badge bg-info">Land Use</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

    <!-- Include Footer -->
    @include('layouts.footer')
@endsection

@push('styles')
    <style>
        .btn {
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
        }

        .btn-success {
            background: linear-gradient(135deg, #388e3c, #2e7d32);
        }

        .btn-info {
            background: linear-gradient(135deg, #0288d1, #0277bd);
        }

        .feature-card {
            background: white;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: #d32f2f;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.05);
        }

        .badge {
            font-size: 0.7rem;
            margin: 0.1rem;
        }

        .welcome-hero {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 3rem 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
        }

        .welcome-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .welcome-hero .lead {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .welcome-hero {
                padding: 2rem 1rem;
            }

            .welcome-hero h1 {
                font-size: 2rem;
            }

            .welcome-hero .lead {
                font-size: 1.1rem;
            }

            .btn {
                font-size: 0.9rem;
                padding: 0.75rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Laguna Province coordinates (centered around Santa Cruz/San Pablo area)
            var lagunaCenter = [14.2781, 121.4162];
            var lagunaBounds = [
                [13.9000, 120.9000], // Southwest bounds
                [14.6000, 121.8000] // Northeast bounds
            ];

            // Initialize map centered on Laguna Province
            var map = L.map('welcomeMap').setView(lagunaCenter, 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            // Add bounding box for Laguna Province with red color
            L.rectangle(lagunaBounds, {
                color: "#d32f2f",
                weight: 2,
                fillOpacity: 0.1,
                fillColor: "#d32f2f"
            }).addTo(map).bindPopup("<strong>Laguna Province Boundary</strong><br>Philippines");

            // Create a custom red icon for markers
            var redIcon = L.icon({
                iconUrl: 'data:image/svg+xml;base64,' + btoa(`
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32">
                    <circle cx="16" cy="16" r="14" fill="#d32f2f" stroke="#fff" stroke-width="2"/>
                    <circle cx="16" cy="16" r="6" fill="#fff"/>
                </svg>
            `),
                iconSize: [25, 25],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });

            // Add marker for major cities in Laguna
            var cities = [{
                    name: "Santa Cruz",
                    coords: [14.2814, 121.4167],
                    description: "Provincial Capital"
                },
                {
                    name: "San Pablo",
                    coords: [14.0669, 121.3258],
                    description: "City of Seven Lakes"
                },
                {
                    name: "Calamba",
                    coords: [14.2117, 121.1653],
                    description: "Historic City"
                },
                {
                    name: "Los Baños",
                    coords: [14.1667, 121.2167],
                    description: "Special Science City"
                },
                {
                    name: "Biñan",
                    coords: [14.3333, 121.0833],
                    description: "Industrial City"
                }
            ];

            cities.forEach(function(city) {
                L.marker(city.coords, {
                        icon: redIcon
                    })
                    .addTo(map)
                    .bindPopup("<strong>" + city.name + "</strong><br>" + city.description +
                        "<br>Laguna Province");
            });

            // Fit map to show all markers and bounds
            var group = new L.featureGroup([L.rectangle(lagunaBounds)]);
            map.fitBounds(group.getBounds().pad(0.1));
        });
    </script>
@endpush
