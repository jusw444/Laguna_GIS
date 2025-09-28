@extends('layouts.app')

@section('title', 'Add Health Status')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-danger">
            <div class="card-header bg-gray text-black">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add Health Status</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('health-status.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Area Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="health_status" class="form-label">Health Status</label>
                        <select name="health_status" id="health_status" class="form-select" required>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="disease_cases" class="form-label">Disease Cases</label>
                        <input type="number" name="disease_cases" id="disease_cases" class="form-control" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="clinics_available" class="form-label">Clinics Available</label>
                        <input type="number" name="clinics_available" id="clinics_available" class="form-control" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="land_use" class="form-label">Land Use</label>
                        <select name="land_use" id="land_use" class="form-select">
                            <option value="all">All Types</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="agricultural">Agricultural</option>
                            <option value="industrial">Industrial</option>
                        </select>
                    </div>

                    <!-- Map Drawing -->
                    <div class="mb-3">
                        <label class="form-label">Draw Health Area on Map</label>
                        <div id="map" style="height: 400px; border: 2px solid #198754;"></div>
                        <input type="hidden" name="geometry" id="geometry">
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Save Health Status
                    </button>
                    <a href="{{ route('health-status.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet + Draw -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css"/>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var map = L.map('map').setView([14.2822, 121.4163], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: { featureGroup: drawnItems },
        draw: {
            rectangle: true,
            polygon: false,
            polyline: false,
            circle: false,
            marker: false,
            circlemarker: false
        }
    });
    map.addControl(drawControl);

    // Color function based on health status
    function getHealthColor(status) {
        if (status === 'excellent') return '#28a745'; // green
        if (status === 'good') return '#007bff';      // blue
        if (status === 'fair') return '#ffc107';      // yellow
        if (status === 'poor') return '#dc3545';      // red
        return '#3388ff';                             // default
    }

    function updateGeometry() {
        var geojson = drawnItems.toGeoJSON();
        if (geojson.features.length > 0) {
            document.getElementById('geometry').value = JSON.stringify(geojson.features[0].geometry);
        }
    }

    // On create rectangle
    map.on(L.Draw.Event.CREATED, function (event) {
        drawnItems.clearLayers(); // only one rectangle
        var layer = event.layer;

        var status = document.getElementById('health_status').value;
        layer.setStyle({
            color: getHealthColor(status),
            fillColor: getHealthColor(status),
            weight: 2,
            opacity: 0.8,
            fillOpacity: 0.5
        });

        drawnItems.addLayer(layer);
        updateGeometry();
    });

    // On edit rectangle
    map.on('draw:edited', function () {
        drawnItems.eachLayer(function (layer) {
            var status = document.getElementById('health_status').value;
            layer.setStyle({
                color: getHealthColor(status),
                fillColor: getHealthColor(status)
            });
        });
        updateGeometry();
    });

    // Change color if select changes
    document.getElementById('health_status').addEventListener('change', function () {
        var status = this.value;
        drawnItems.eachLayer(function (layer) {
            layer.setStyle({
                color: getHealthColor(status),
                fillColor: getHealthColor(status)
            });
        });
    });
});
</script>
@endpush
