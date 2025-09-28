@extends('layouts.app')

@section('title', 'Edit Flood Area')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Flood Area</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('flood-areas.update', $floodArea->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Area Name</label>
                        <input type="text" name="name" id="name" class="form-control" 
                            value="{{ old('name', $floodArea->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="flood_risk" class="form-label">Flood Risk Level</label>
                        <select name="flood_risk" id="flood_risk" class="form-select" required>
                            <option value="high" {{ $floodArea->flood_risk == 'high' ? 'selected' : '' }}>High Risk</option>
                            <option value="medium" {{ $floodArea->flood_risk == 'medium' ? 'selected' : '' }}>Medium Risk</option>
                            <option value="low" {{ $floodArea->flood_risk == 'low' ? 'selected' : '' }}>Low Risk</option>
                            <option value="none" {{ $floodArea->flood_risk == 'none' ? 'selected' : '' }}>No Risk</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="land_use" class="form-label">Land Use</label>
                        <select name="land_use" id="land_use" class="form-select">
                            <option value="residential" {{ $floodArea->land_use == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ $floodArea->land_use == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="agricultural" {{ $floodArea->land_use == 'agricultural' ? 'selected' : '' }}>Agricultural</option>
                            <option value="industrial" {{ $floodArea->land_use == 'industrial' ? 'selected' : '' }}>Industrial</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ownership" class="form-label">Ownership</label>
                        <input type="text" name="ownership" id="ownership" class="form-control"
                            value="{{ old('ownership', $floodArea->ownership) }}">
                    </div>

                    <div class="mb-3">
                        <label for="classification" class="form-label">Classification</label>
                        <input type="text" name="classification" id="classification" class="form-control"
                            value="{{ old('classification', $floodArea->classification) }}">
                    </div>

                    <!-- Map Drawing -->
                    <div class="mb-3">
                        <label class="form-label">Edit Flood Area on Map</label>
                        <div id="map" style="height: 400px; border: 2px solid #dc3545;"></div>
                        <input type="hidden" name="geometry" id="geometry" 
                               value="{{ old('geometry', $floodArea->geometry) }}">
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Update Flood Area
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

    function getFloodRiskColor(risk) {
        if (risk === 'high') return '#ff0000';
        if (risk === 'medium') return '#ffff00';
        if (risk === 'low') return '#00ff00';
        return '#3388ff';
    }

    function updateGeometry() {
        var geojson = drawnItems.toGeoJSON();
        if (geojson.features.length > 0) {
            document.getElementById('geometry').value = JSON.stringify(geojson.features[0].geometry);
        }
    }

    // Load existing geometry if available
    var existingGeometry = document.getElementById('geometry').value;
    if (existingGeometry) {
        var geom = JSON.parse(existingGeometry);
        var layer = L.geoJSON(geom, {
            style: {
                color: getFloodRiskColor("{{ $floodArea->flood_risk }}"),
                fillColor: getFloodRiskColor("{{ $floodArea->flood_risk }}"),
                weight: 2,
                opacity: 0.8,
                fillOpacity: 0.5
            }
        }).getLayers()[0];
        drawnItems.addLayer(layer);
        map.fitBounds(layer.getBounds());
    }

    map.on(L.Draw.Event.CREATED, function (event) {
        drawnItems.clearLayers();
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
