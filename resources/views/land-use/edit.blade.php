<!-- resources/views/land-use/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Land Use')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-danger">
            <div class="card-header bg-gray text-black">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Land Use</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('land-use.update', $landUse->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Area Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="{{ old('name', $landUse->name) }}">
                    </div>

                    <div class="mb-3">
                        <label for="land_use" class="form-label">Land Use Type</label>
                        <select name="land_use" id="land_use" class="form-select" required>
                            <option value="residential" {{ $landUse->land_use == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ $landUse->land_use == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="agricultural" {{ $landUse->land_use == 'agricultural' ? 'selected' : '' }}>Agricultural</option>
                            <option value="industrial" {{ $landUse->land_use == 'industrial' ? 'selected' : '' }}>Industrial</option>
                            <option value="institutional" {{ $landUse->land_use == 'institutional' ? 'selected' : '' }}>Institutional</option>
                            <option value="recreational" {{ $landUse->land_use == 'recreational' ? 'selected' : '' }}>Recreational</option>
                            <option value="conservation" {{ $landUse->land_use == 'conservation' ? 'selected' : '' }}>Conservation</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ownership" class="form-label">Ownership</label>
                        <select name="ownership" id="ownership" class="form-select">
                            <option value="" disabled>-- Select Ownership --</option>
                            <option value="private" {{ $landUse->ownership == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="public" {{ $landUse->ownership == 'public' ? 'selected' : '' }}>Public</option>
                            <option value="government" {{ $landUse->ownership == 'government' ? 'selected' : '' }}>Government</option>
                            <option value="communal" {{ $landUse->ownership == 'communal' ? 'selected' : '' }}>Communal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="classification" class="form-label">Classification</label>
                        <input type="text" name="classification" id="classification" class="form-control"
                               value="{{ old('classification', $landUse->classification) }}">
                    </div>

                    <div class="mb-3">
                        <label for="flood_risk" class="form-label">Flood Risk</label>
                        <select name="flood_risk" id="flood_risk" class="form-select">
                            <option value="high" {{ $landUse->flood_risk == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ $landUse->flood_risk == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ $landUse->flood_risk == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="none" {{ $landUse->flood_risk == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <!-- Map Drawing -->
                    <div class="mb-3">
                        <label class="form-label">Edit Land Use Area on Map</label>
                        <div id="map" style="height: 400px; border: 2px solid #198754;"></div>
                        <input type="hidden" name="geometry" id="geometry"
                               value="{{ old('geometry', $landUse->geometry) }}">
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Update Land Use
                    </button>
                    <a href="{{ route('land-use.index') }}" class="btn btn-secondary">Cancel</a>
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
            polygon: true,
            polyline: false,
            circle: false,
            marker: false,
            circlemarker: false
        }
    });
    map.addControl(drawControl);

    function getLandUseColor(type) {
        if (type === 'residential') return '#007bff';
        if (type === 'commercial') return '#28a745';
        if (type === 'agricultural') return '#ffc107';
        if (type === 'industrial') return '#dc3545';
        if (type === 'institutional') return '#6f42c1';
        if (type === 'recreational') return '#20c997';
        if (type === 'conservation') return '#17a2b8';
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
                color: getLandUseColor("{{ $landUse->land_use }}"),
                fillColor: getLandUseColor("{{ $landUse->land_use }}"),
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
        var type = document.getElementById('land_use').value;
        layer.setStyle({
            color: getLandUseColor(type),
            fillColor: getLandUseColor(type),
            weight: 2,
            opacity: 0.8,
            fillOpacity: 0.5
        });
        drawnItems.addLayer(layer);
        updateGeometry();
    });

    map.on('draw:edited', function () {
        drawnItems.eachLayer(function (layer) {
            var type = document.getElementById('land_use').value;
            layer.setStyle({
                color: getLandUseColor(type),
                fillColor: getLandUseColor(type)
            });
        });
        updateGeometry();
    });

    document.getElementById('land_use').addEventListener('change', function () {
        var type = this.value;
        drawnItems.eachLayer(function (layer) {
            layer.setStyle({
                color: getLandUseColor(type),
                fillColor: getLandUseColor(type)
            });
        });
    });
});
</script>
@endpush
