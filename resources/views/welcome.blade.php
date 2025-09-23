@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="row py-4">
    <div class="col-md-8 mx-auto">
        <div class="card shadow">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #d32f2f, #b71c1c);">
                <h3 class="card-title mb-0"><i class="fas fa-globe-americas"></i> Laguna Province GIS Application</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Welcome to Laguna Province GIS Platform</h4>
                        <p>This application allows you to manage geographic data, visualize spatial information, and perform analysis specifically for Laguna Province, Philippines.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('shapefiles.index') }}" class="btn text-white" style="background-color: #d32f2f; border-color: #b71c1c;">
                                <i class="fas fa-layer-group"></i> Manage Shapefiles
                            </a>
                            <a href="{{ route('legends.index') }}" class="btn text-white" style="background-color: #388e3c; border-color: #2e7d32;">
                                <i class="fas fa-palette"></i> Manage Legends
                            </a>
                            <a href="{{ route('analysis.flood-areas') }}" class="btn text-white" style="background-color: #0288d1; border-color: #0277bd;">
                                <i class="fas fa-water"></i> Flood Analysis
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="welcomeMap" style="height: 300px; width: 100%; border: 2px solid #d32f2f; border-radius: 8px;"></div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Laguna Province, Philippines</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm" style="border-top: 4px solid #d32f2f !important;">
                    <div class="card-body">
                        <i class="fas fa-upload fa-3x mb-3" style="color: #d32f2f;"></i>
                        <h5>Upload Data</h5>
                        <p>Upload shapefiles and define land boundaries for Laguna Province</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm" style="border-top: 4px solid #388e3c !important;">
                    <div class="card-body">
                        <i class="fas fa-map-marked-alt fa-3x mb-3" style="color: #388e3c;"></i>
                        <h5>Visualize</h5>
                        <p>View Laguna's geographic data on interactive maps with custom legends</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm" style="border-top: 4px solid #0288d1 !important;">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-3x mb-3" style="color: #0288d1;"></i>
                        <h5>Analyze</h5>
                        <p>Perform spatial analysis for flood risk and health status in Laguna</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection


@push('styles')
<style>
    .btn:hover {
        transform: translateY(-2px);
        transition: transform 0.2s;
    }
    
    .card {
        transition: transform 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
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
            [14.6000, 121.8000]  // Northeast bounds
        ];

        // Initialize map centered on Laguna Province
        var map = L.map('welcomeMap').setView(lagunaCenter, 10);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add bounding box for Laguna Province with red color
        L.rectangle(lagunaBounds, {
            color: "#d32f2f",
            weight: 2,
            fillOpacity: 0.1
        }).addTo(map).bindPopup("Laguna Province Boundary");

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
        var cities = [
            {name: "Santa Cruz", coords: [14.2814, 121.4167]},
            {name: "San Pablo", coords: [14.0669, 121.3258]},
            {name: "Calamba", coords: [14.2117, 121.1653]},
            {name: "Los Baños", coords: [14.1667, 121.2167]},
            {name: "Biñan", coords: [14.3333, 121.0833]}
        ];

        cities.forEach(function(city) {
            L.marker(city.coords, {icon: redIcon})
                .addTo(map)
                .bindPopup("<strong>" + city.name + "</strong><br>Laguna Province");
        });
    });
</script>
@endpush