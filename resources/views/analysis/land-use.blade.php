@extends('layouts.app')

@section('title', 'Land Use Analysis')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-map-marked-alt"></i> Land Use Analysis</h2>
            <div class="btn-group">
                <a href="{{ route('land-use.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus"></i> Add Land Use
                </a>
                <button class="btn btn-outline-primary" id="exportData">
                    <i class="fas fa-download"></i> Export Data (PNG)
                </button>
                <button class="btn btn-outline-info" id="printMap">
                    <i class="fas fa-print"></i> Print Map
                </button>
            </div>
        </div>

        <div class="row" id="exportContainer">
            <!-- Sidebar -->
            @include('sidebars.land-sidebar')

            <!-- Map + Data -->
            <div class="col-md-9">
                <!-- Map -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Land Use Map</h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="toggleLegend" checked>
                            <label class="form-check-label" for="toggleLegend">Show Legend</label>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 600px;"></div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Land Use Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle" id="landUseDataTable">
                                <thead>
                                    <tr>
                                        <th>Area Name</th>
                                        <th>Land Use</th>
                                        <th>Ownership</th>
                                        <th>Classification</th>
                                        <th>Flood Risk</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($landUses as $lu)
                                        <tr>
                                            <td>{{ $lu->name ?? 'Unnamed' }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ getLandUseColor($lu->land_use) }}">
                                                    {{ ucfirst($lu->land_use) ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $lu->ownership ?? 'N/A' }}</td>
                                            <td>{{ $lu->classification ?? 'N/A' }}</td>
                                            <td>{{ $lu->flood_risk ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('land-use.edit', $lu->id) }}" class="btn btn-sm text-white" style="background-color:#0d47a1;">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('land-use.destroy', $lu->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getLandUseColor($type) {
    switch ($type) {
        case 'residential': return '#007bff';
        case 'commercial': return '#28a745';
        case 'agricultural': return '#ffc107';
        case 'industrial': return '#dc3545';
        case 'institutional': return '#6f42c1';
        case 'recreational': return '#20c997';
        case 'conservation': return '#fd7e14';
        default: return '#999999';
    }
}
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/leaflet-image/leaflet-image.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    var landUses = @json($landUses);

    var geojsonData = {
        type: "FeatureCollection",
        features: landUses.map(lu => ({
            type: "Feature",
            geometry: lu.geometry ? JSON.parse(lu.geometry) : null,
            properties: lu
        }))
    };

    // Map Init
    window.map = L.map('map').setView([14.2096, 121.1656], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(window.map);

    var legend = L.control({ position: 'bottomright' });
    legend.onAdd = () => {
        var div = L.DomUtil.create('div', 'legend bg-white p-2 border');
        div.innerHTML = `
            <h6>Land Use Legend</h6>
            <div><span style="background:#007bff;width:20px;height:20px;display:inline-block"></span> Residential</div>
            <div><span style="background:#28a745;width:20px;height:20px;display:inline-block"></span> Commercial</div>
            <div><span style="background:#ffc107;width:20px;height:20px;display:inline-block"></span> Agricultural</div>
            <div><span style="background:#dc3545;width:20px;height:20px;display:inline-block"></span> Industrial</div>
            <div><span style="background:#6f42c1;width:20px;height:20px;display:inline-block"></span> Institutional</div>
            <div><span style="background:#20c997;width:20px;height:20px;display:inline-block"></span> Recreational</div>
            <div><span style="background:#fd7e14;width:20px;height:20px;display:inline-block"></span> Conservation</div>
        `;
        return div;
    };
    legend.addTo(window.map);

    document.getElementById('toggleLegend').addEventListener('change', e => {
        e.target.checked ? legend.addTo(window.map) : window.map.removeControl(legend);
    });

    var landUseLayers = L.geoJSON(geojsonData, {
        style: f => ({
            color: getLandUseColor(f.properties.land_use),
            fillColor: getLandUseColor(f.properties.land_use),
            weight: 2, opacity: 0.8, fillOpacity: 0.5
        }),
        onEachFeature: (f, layer) => layer.bindPopup(createPopupContent(f.properties))
    }).addTo(window.map);

    if (landUses.length > 0) window.map.fitBounds(landUseLayers.getBounds());

    // Load stats
    try {
        let params = new URLSearchParams(window.location.search);
        let res = await fetch("{{ route('land-use.stats') }}?" + params.toString());
        let stats = await res.json();

        new Chart(document.getElementById('landUseChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stats.landUse),
                datasets: [{
                    data: Object.values(stats.landUse),
                    backgroundColor: Object.keys(stats.landUse).map(t => getLandUseColor(t))
                }]
            }
        });

        new Chart(document.getElementById('ownershipChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stats.ownership),
                datasets: [{
                    data: Object.values(stats.ownership),
                    backgroundColor: ['#3366cc', '#ff9900', '#109618', '#990099']
                }]
            }
        });

        new Chart(document.getElementById('floodRiskChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(stats.floodRisk),
                datasets: [{
                    label: 'Flood Risk Count',
                    data: Object.values(stats.floodRisk),
                    backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#6c757d']
                }]
            }
        });

    } catch (err) {
        console.error("Failed to load stats:", err);
    }

    // === EXPORT MAP + STATS AS PNG ===
    document.getElementById("exportData").addEventListener("click", async () => {
        let container = document.getElementById("exportContainer");
        let canvas = await html2canvas(container, { scale: 2 });
        let link = document.createElement("a");
        link.download = "land_use_analysis.png";
        link.href = canvas.toDataURL("image/png");
        link.click();
    });

    // Print map only
    document.getElementById("printMap").addEventListener("click", () => {
        window.print();
    });
});

function getLandUseColor(type) {
    switch (type) {
        case 'residential': return '#007bff';
        case 'commercial': return '#28a745';
        case 'agricultural': return '#ffc107';
        case 'industrial': return '#dc3545';
        case 'institutional': return '#6f42c1';
        case 'recreational': return '#20c997';
        case 'conservation': return '#fd7e14';
        default: return '#999999';
    }
}

function createPopupContent(props) {
    return `
        <b>${props.name ?? 'Unnamed Area'}</b><br>
        Land Use: ${props.land_use}<br>
        Ownership: ${props.ownership ?? 'N/A'}<br>
        Classification: ${props.classification ?? 'N/A'}<br>
        Flood Risk: ${props.flood_risk ?? 'N/A'}
    `;
}
</script>
@endpush
