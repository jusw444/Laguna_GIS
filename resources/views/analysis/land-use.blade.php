<!-- resources/views/analysis/land-use.blade.php -->
@extends('layouts.app')

@section('title', 'Land Use Analysis')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-map-marked-alt"></i> Land Use Analysis</h2>
            <div class="btn-group">
                <button class="btn btn-outline-primary" id="exportData">
                    <i class="fas fa-download"></i> Export Data
                </button>
                <button class="btn btn-outline-info" id="printMap">
                    <i class="fas fa-print"></i> Print Map
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Analysis Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="landUseFilter" class="form-label">Land Use Type</label>
                            <select class="form-select" id="landUseFilter">
                                <option value="all">All Types</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="agricultural">Agricultural</option>
                                <option value="industrial">Industrial</option>
                                <option value="recreational">Recreational</option>
                                <option value="conservation">Conservation</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ownershipFilter" class="form-label">Ownership</label>
                            <select class="form-select" id="ownershipFilter">
                                <option value="all">All Types</option>
                                <option value="private">Private</option>
                                <option value="public">Public</option>
                                <option value="government">Government</option>
                                <option value="communal">Communal</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100" id="applyFilters">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="text-center">Land Use Distribution</h6>
                            <canvas id="landUseChart" height="200"></canvas>
                        </div>
                        <div>
                            <h6 class="text-center">Ownership Distribution</h6>
                            <canvas id="ownershipChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

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
                            <table class="table table-striped" id="landUseDataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Area Name</th>
                                        <th>Land Use</th>
                                        <th>Ownership</th>
                                        <th>Classification</th>
                                        <th>Flood Risk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- JS will populate -->
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Init Map
    var map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Legend
    var legend = L.control({position: 'bottomright'});
    legend.onAdd = function() {
        var div = L.DomUtil.create('div', 'legend bg-white p-2 rounded shadow');
        div.innerHTML = `
            <h6>Land Use Legend</h6>
            <div><span class="legend-color" style="background:#ff9999"></span> Residential</div>
            <div><span class="legend-color" style="background:#77ff77"></span> Commercial</div>
            <div><span class="legend-color" style="background:#ffff77"></span> Agricultural</div>
            <div><span class="legend-color" style="background:#ff77ff"></span> Industrial</div>
            <div><span class="legend-color" style="background:#77ffff"></span> Recreational</div>
            <div><span class="legend-color" style="background:#7777ff"></span> Conservation</div>
        `;
        return div;
    };
    legend.addTo(map);

    document.getElementById('toggleLegend').addEventListener('change', function() {
        this.checked ? legend.addTo(map) : map.removeControl(legend);
    });

    // Data vars
    var landUseData = [];
    var landUseLayers = null;
    var landUseChart = null, ownershipChart = null;

    // Fetch API data
    fetch("{{ route('api.analysis.land-use') }}")
        .then(res => res.json())
        .then(data => {
            landUseData = data.features;

            landUseLayers = L.geoJSON(data, {
                style: f => getStyle(f.properties),
                onEachFeature: (f, layer) => layer.bindPopup(createPopupContent(f.properties))
            }).addTo(map);

            if (landUseData.length > 0) {
                map.fitBounds(landUseLayers.getBounds());
            }

            populateDataTable(landUseData);
            createCharts(landUseData);
        });

    // Apply Filters
    document.getElementById('applyFilters').addEventListener('click', function() {
        var luFilter = document.getElementById('landUseFilter').value;
        var oFilter = document.getElementById('ownershipFilter').value;

        var filtered = landUseData.filter(f => {
            var luMatch = luFilter === 'all' || (f.properties.land_use && f.properties.land_use.toLowerCase() === luFilter);
            var oMatch = oFilter === 'all' || (f.properties.ownership && f.properties.ownership.toLowerCase() === oFilter);
            return luMatch && oMatch;
        });

        if (landUseLayers) map.removeLayer(landUseLayers);
        landUseLayers = L.geoJSON({ type: 'FeatureCollection', features: filtered }, {
            style: f => getStyle(f.properties),
            onEachFeature: (f, layer) => layer.bindPopup(createPopupContent(f.properties))
        }).addTo(map);

        populateDataTable(filtered);
        createCharts(filtered);
    });

    // Helpers
    function getStyle(p) {
        var color = '#3388ff';
        if (p.land_use) {
            switch(p.land_use.toLowerCase()) {
                case 'residential': color = '#ff9999'; break;
                case 'commercial': color = '#77ff77'; break;
                case 'agricultural': color = '#ffff77'; break;
                case 'industrial': color = '#ff77ff'; break;
                case 'recreational': color = '#77ffff'; break;
                case 'conservation': color = '#7777ff'; break;
            }
        }
        return { color, weight: 2, opacity: 0.7, fillOpacity: 0.5 };
    }

    function createPopupContent(p) {
        return `
            <div class="map-popup">
                <h6>${p.name || 'Unnamed Area'}</h6>
                <table class="table table-sm">
                    <tr><th>Land Use:</th><td>${p.land_use || 'N/A'}</td></tr>
                    <tr><th>Ownership:</th><td>${p.ownership || 'N/A'}</td></tr>
                    <tr><th>Classification:</th><td>${p.classification || 'N/A'}</td></tr>
                    <tr><th>Flood Risk:</th><td>${p.flood_risk || 'N/A'}</td></tr>
                </table>
            </div>
        `;
    }

    function populateDataTable(data) {
        var tbody = document.querySelector('#landUseDataTable tbody');
        tbody.innerHTML = '';
        data.forEach(f => {
            var p = f.properties;
            tbody.innerHTML += `
                <tr>
                    <td>${p.name || 'Unnamed'}</td>
                    <td><span class="badge bg-${getLandUseBadgeClass(p.land_use)}">${p.land_use || 'N/A'}</span></td>
                    <td>${p.ownership || 'N/A'}</td>
                    <td>${p.classification || 'N/A'}</td>
                    <td><span class="badge bg-${getRiskBadgeClass(p.flood_risk)}">${p.flood_risk || 'N/A'}</span></td>
                </tr>
            `;
        });
    }

    function getLandUseBadgeClass(lu) {
        if (!lu) return 'secondary';
        switch(lu.toLowerCase()) {
            case 'residential': return 'primary';
            case 'commercial': return 'success';
            case 'agricultural': return 'warning';
            case 'industrial': return 'danger';
            case 'recreational': return 'info';
            case 'conservation': return 'dark';
            default: return 'secondary';
        }
    }

    function getRiskBadgeClass(r) {
        if (!r) return 'secondary';
        switch(r.toLowerCase()) {
            case 'high': return 'danger';
            case 'medium': return 'warning';
            case 'low': return 'success';
            default: return 'secondary';
        }
    }

    function createCharts(data) {
        var luCounts = {}, oCounts = {};
        data.forEach(f => {
            luCounts[f.properties.land_use || 'Unknown'] = (luCounts[f.properties.land_use || 'Unknown'] || 0) + 1;
            oCounts[f.properties.ownership || 'Unknown'] = (oCounts[f.properties.ownership || 'Unknown'] || 0) + 1;
        });

        if (landUseChart) landUseChart.destroy();
        if (ownershipChart) ownershipChart.destroy();

        landUseChart = new Chart(document.getElementById('landUseChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(luCounts),
                datasets: [{ data: Object.values(luCounts), backgroundColor: ['#ff9999','#77ff77','#ffff77','#ff77ff','#77ffff','#7777ff','#cccccc'] }]
            }
        });

        ownershipChart = new Chart(document.getElementById('ownershipChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(oCounts),
                datasets: [{ label: 'Areas', data: Object.values(oCounts), backgroundColor: '#3388ff' }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    // Export CSV
    document.getElementById('exportData').addEventListener('click', function() {
        var csv = 'Area Name,Land Use,Ownership,Classification,Flood Risk\n';
        landUseData.forEach(f => {
            var p = f.properties;
            csv += `"${p.name || 'Unnamed'}",${p.land_use || 'N/A'},${p.ownership || 'N/A'},${p.classification || 'N/A'},${p.flood_risk || 'N/A'}\n`;
        });
        var blob = new Blob([csv], { type: 'text/csv' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'land_use_analysis.csv';
        link.click();
    });

    // Print Map
    document.getElementById('printMap').addEventListener('click', function() {
        window.print();
    });
});
</script>
@endpush
