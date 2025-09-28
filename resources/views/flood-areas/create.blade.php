@extends('layouts.app')

@section('title', 'Add Flood Area')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-danger">
            <div class="card-header bg-gray text-black">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add Flood Area</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('flood-areas.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Area Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="flood_risk" class="form-label">Flood Risk Level</label>
                        <select name="flood_risk" id="flood_risk" class="form-select" required>
                            <option value="high">High Risk</option>
                            <option value="medium">Medium Risk</option>
                            <option value="low">Low Risk</option>
                            <option value="none">No Risk</option>
                        </select>
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

                    <div class="mb-3">
                        <label for="ownership" class="form-label">Ownership</label>
                        <input type="text" name="ownership" id="ownership" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="classification" class="form-label">Classification</label>
                        <input type="text" name="classification" id="classification" class="form-control">
                    </div>

                    <!-- Map Drawing -->
                    <div class="mb-3">
                        <label class="form-label">Draw Flood Area on Map</label>
                        <div id="map" style="height: 400px; border: 2px solid #dc3545;"></div>
                        <input type="hidden" name="geometry" id="geometry">
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Save Flood Area
                    </button>
                    <a href="{{ route('flood-areas.index') }}" class="btn btn-secondary">Cancel</a>
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

    // Color function
    function getFloodRiskColor(risk) {
        if (risk === 'high') return '#ff0000';   // red
        if (risk === 'medium') return '#ffff00'; // yellow
        if (risk === 'low') return '#00ff00';    // green
        return '#3388ff';                        // blue (none)
    }

    // Save geometry to hidden field
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

        var risk = document.getElementById('flood_risk').value;
        layer.setStyle({
            color: getFloodRiskColor(risk),
            fillColor: getFloodRiskColor(risk),
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
            var risk = document.getElementById('flood_risk').value;
            layer.setStyle({
                color: getFloodRiskColor(risk),
                fillColor: getFloodRiskColor(risk)
            });
        });
        updateGeometry();
    });

    // Change color if select changes
    document.getElementById('flood_risk').addEventListener('change', function () {
        var risk = this.value;
        drawnItems.eachLayer(function (layer) {
            layer.setStyle({
                color: getFloodRiskColor(risk),
                fillColor: getFloodRiskColor(risk)
            });
        });
    });
});
</script>
@endpush
