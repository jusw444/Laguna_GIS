@extends('layouts.app')

@section('title', 'Add Land Use')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card border-danger">
                <div class="card-header bg-gray text-black">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add Land Use</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('land-use.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Area Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="land_use" class="form-label">Land Use Type</label>
                            <select name="land_use" id="land_use" class="form-select" required>
                                <option value="" disabled selected>-- Select Land Use --</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="agricultural">Agricultural</option>
                                <option value="industrial">Industrial</option>
                                <option value="institutional">Institutional</option>
                                <option value="recreational">Recreational</option>
                                <option value="conservation">Conservation</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ownership" class="form-label">Ownership</label>
                            <select name="ownership" id="ownership" class="form-select">
                                <option value="" disabled selected>-- Select Ownership --</option>
                                <option value="private">Private</option>
                                <option value="public">Public</option>
                                <option value="government">Government</option>
                                <option value="communal">Communal</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="classification" class="form-label">Classification</label>
                            <input type="text" name="classification" id="classification" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="flood_risk" class="form-label">Flood Risk</label>
                            <select name="flood_risk" id="flood_risk" class="form-select">
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        <!-- Map Drawing -->
                        <div class="mb-3">
                            <label class="form-label">Draw Land Use Area on Map</label>
                            <div id="map" style="height: 400px; border: 2px solid #198754;"></div>
                            <input type="hidden" name="geometry" id="geometry">
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save"></i> Save Land Use
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map').setView([14.2822, 121.4163], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            var drawControl = new L.Control.Draw({
                edit: {
                    featureGroup: drawnItems
                },
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

            // Color function based on land use
            function getLandUseColor(type) {
                if (type === 'residential') return '#007bff'; // blue
                if (type === 'commercial') return '#28a745'; // green
                if (type === 'agricultural') return '#ffc107'; // yellow
                if (type === 'industrial') return '#dc3545'; // red
                if (type === 'institutional') return '#6f42c1'; // purple
                if (type === 'recreational') return '#20c997'; // teal
                return '#3388ff'; // default
            }

            function updateGeometry() {
                var geojson = drawnItems.toGeoJSON();
                if (geojson.features.length > 0) {
                    document.getElementById('geometry').value = JSON.stringify(geojson.features[0].geometry);
                }
            }

            // On create shape
            map.on(L.Draw.Event.CREATED, function(event) {
                drawnItems.clearLayers(); // only one shape at a time
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

            // On edit shape
            map.on('draw:edited', function() {
                drawnItems.eachLayer(function(layer) {
                    var type = document.getElementById('land_use').value;
                    layer.setStyle({
                        color: getLandUseColor(type),
                        fillColor: getLandUseColor(type)
                    });
                });
                updateGeometry();
            });

            // Change color if land use type changes
            document.getElementById('land_use').addEventListener('change', function() {
                var type = this.value;
                drawnItems.eachLayer(function(layer) {
                    layer.setStyle({
                        color: getLandUseColor(type),
                        fillColor: getLandUseColor(type)
                    });
                });
            });
        });
    </script>
@endpush
