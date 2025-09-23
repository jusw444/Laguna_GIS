@extends('layouts.app')

@section('title', $shapefile->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Shapefile Details</h5>
            </div>
            <div class="card-body">
                <h6>{{ $shapefile->name }}</h6>
                <p>{{ $shapefile->description ?: 'No description provided.' }}</p>
                
                <div class="mb-3">
                    <strong>Type:</strong> 
                    <span class="badge bg-secondary">{{ $shapefile->type }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Uploaded by:</strong> {{ $shapefile->user->name }}
                </div>
                
                <div class="mb-3">
                    <strong>Uploaded on:</strong> {{ $shapefile->created_at->format('M d, Y H:i') }}
                </div>
                
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editShapefileModal">
                        <i class="fas fa-edit"></i> Edit Details
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Layers</h5>
                <span class="badge bg-primary">{{ count($shapefile->layers) }}</span>
            </div>
            <div class="card-body">
                <div class="layer-list">
                    @foreach($shapefile->layers as $layer)
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <h6 class="card-title mb-1">{{ $layer->name }}</h6>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">ID: {{ $layer->id }}</small>
                                    <div>
                                        <button class="btn btn-sm btn-outline-info view-layer" data-layer-id="{{ $layer->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form action="{{ route('layers.destroy', $layer->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this layer?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Map View</h5>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggleLegend">
                    <label class="form-check-label" for="toggleLegend">Show Legend</label>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Shapefile Modal -->
<div class="modal fade" id="editShapefileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shapefile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('shapefiles.update', $shapefile->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" value="{{ $shapefile->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3">{{ $shapefile->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Layer Details Modal -->
<div class="modal fade" id="layerDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Layer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="layerDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        var map = L.map('map').setView([0, 0], 2);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add shapefile layers to map
        var layers = {};
        var bounds = new L.LatLngBounds();
        
        @foreach($shapefile->layers as $layer)
            (function() {
                var layerData = {!! $layer->geojson_data !!};
                var layer = L.geoJSON(layerData, {
                    style: function(feature) {
                        return getStyle(feature.properties);
                    },
                    onEachFeature: function(feature, layer) {
                        layer.bindPopup(createPopupContent(feature.properties, {{ $layer->id }}));
                        // Extend bounds to include this layer
                        layer.eachLayer(function(l) {
                            bounds.extend(l.getBounds());
                        });
                    }
                }).addTo(map);
                
                layers[{{ $layer->id }}] = layer;
            })();
        @endforeach

        // Fit map to show all layers
        if (Object.keys(layers).length > 0) {
            map.fitBounds(bounds);
        }

        // Add legend
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'legend');
            div.innerHTML = '<h6>Legend</h6>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #ff0000"></span> High Flood Risk</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #ffff00"></span> Medium Flood Risk</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #00ff00"></span> Low Flood Risk</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #3388ff"></span> No Flood Risk</div>';
            return div;
        };
        
        // Toggle legend visibility
        document.getElementById('toggleLegend').addEventListener('change', function() {
            if (this.checked) {
                legend.addTo(map);
            } else {
                map.removeControl(legend);
            }
        });

        // View layer details
        document.querySelectorAll('.view-layer').forEach(button => {
            button.addEventListener('click', function() {
                var layerId = this.getAttribute('data-layer-id');
                fetch('/layers/' + layerId)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('layerDetailsContent').innerHTML = `
                            <h6>${data.name}</h6>
                            <pre class="bg-light p-3">${JSON.stringify(JSON.parse(data.geojson_data), null, 2)}</pre>
                            <div class="d-grid">
                                <a href="/layers/${data.id}/metadata" class="btn btn-outline-primary">View Metadata</a>
                            </div>
                        `;
                        var modal = new bootstrap.Modal(document.getElementById('layerDetailsModal'));
                        modal.show();
                    });
            });
        });

        function getStyle(properties) {
            var color = '#3388ff'; // Default color
            
            if (properties.flood_risk === 'high') {
                color = '#ff0000';
            } else if (properties.flood_risk === 'medium') {
                color = '#ffff00';
            } else if (properties.flood_risk === 'low') {
                color = '#00ff00';
            }
            
            return {
                color: color,
                weight: 2,
                opacity: 0.7,
                fillOpacity: 0.2
            };
        }

        function createPopupContent(properties, layerId) {
            var content = `<div class="map-popup">
                <h6>${properties.name || 'Unnamed Layer'}</h6>
                <table class="table table-sm">
                    <tr><th>Land Use:</th><td>${properties.land_use || 'N/A'}</td></tr>
                    <tr><th>Ownership:</th><td>${properties.ownership || 'N/A'}</td></tr>
                    <tr><th>Classification:</th><td>${properties.classification || 'N/A'}</td></tr>
                    <tr><th>Flood Risk:</th><td>${properties.flood_risk || 'N/A'}</td></tr>
                </table>
                <div class="d-grid">
                    <a href="/layers/${layerId}/metadata" class="btn btn-sm btn-outline-primary">View Details</a>
                </div>
            </div>`;
            return content;
        }
    });
</script>
@endpush