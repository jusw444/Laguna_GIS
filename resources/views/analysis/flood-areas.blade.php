<!-- resources/views/analysis/flood-areas.blade.php -->
@extends('layouts.app')

@section('title', 'Flood Area Analysis')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-water"></i> Flood Area Analysis</h2>
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
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Analysis Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="floodRiskFilter" class="form-label">Flood Risk Level</label>
                            <select class="form-select" id="floodRiskFilter">
                                <option value="all">All Levels</option>
                                <option value="high">High Risk</option>
                                <option value="medium">Medium Risk</option>
                                <option value="low">Low Risk</option>
                                <option value="none">No Risk</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="landUseFilter" class="form-label">Land Use</label>
                            <select class="form-select" id="landUseFilter">
                                <option value="all">All Types</option>
                                <option value="residential">Residential</option>
                                <option value="commercial">Commercial</option>
                                <option value="agricultural">Agricultural</option>
                                <option value="industrial">Industrial</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Flood Risk Distribution</h6>
                            <canvas id="floodRiskChart" height="200"></canvas>
                        </div>
                        <div class="mb-3">
                            <h6>Areas by Land Use</h6>
                            <canvas id="landUseChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map + Table -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Flood Risk Map</h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="toggleLegend" checked>
                            <label class="form-check-label" for="toggleLegend">Show Legend</label>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 600px;"></div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Flood Risk Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="floodDataTable">
                                <thead>
                                    <tr>
                                        <th>Area Name</th>
                                        <th>Flood Risk</th>
                                        <th>Land Use</th>
                                        <th>Ownership</th>
                                        <th>Area (approx.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be populated via JavaScript -->
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
    // Initialize map
    var map = L.map('map').setView([0, 0], 2);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add legend
    var legend = L.control({position: 'bottomright'});
    legend.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'legend');
        div.innerHTML = '<h6>Flood Risk Legend</h6>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #ff0000"></span> High Risk</div>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #ffff00"></span> Medium Risk</div>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #00ff00"></span> Low Risk</div>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #3388ff"></span> No Risk</div>';
        return div;
    };
    legend.addTo(map);

    // Toggle legend
    document.getElementById('toggleLegend').addEventListener('change', function() {
        if (this.checked) {
            legend.addTo(map);
        } else {
            map.removeControl(legend);
        }
    });

    // Load flood data from API route (JSON)
    var floodData = [];
    var floodLayers = [];

    fetch("{{ route('api.analysis.flood-areas') }}")  // ✅ FIXED: use API route
        .then(response => response.json())
        .then(data => {
            floodData = data.features;

            // Add data to map
            floodLayers = L.geoJSON(data, {
                style: feature => getStyle(feature.properties),
                onEachFeature: (feature, layer) => {
                    layer.bindPopup(createPopupContent(feature.properties));
                }
            }).addTo(map);

            if (floodData.length > 0) {
                map.fitBounds(floodLayers.getBounds());
            }

            populateDataTable(floodData);
            createCharts(floodData);
        });

    // Filters
    document.getElementById('applyFilters').addEventListener('click', function() {
        var floodRiskFilter = document.getElementById('floodRiskFilter').value;
        var landUseFilter = document.getElementById('landUseFilter').value;

        var filteredData = floodData.filter(function(feature) {
            var matchesFloodRisk = floodRiskFilter === 'all' || feature.properties.flood_risk === floodRiskFilter;
            var matchesLandUse = landUseFilter === 'all' || (feature.properties.land_use && feature.properties.land_use.toLowerCase() === landUseFilter);
            return matchesFloodRisk && matchesLandUse;
        });

        map.removeLayer(floodLayers);
        floodLayers = L.geoJSON({
            type: 'FeatureCollection',
            features: filteredData
        }, {
            style: feature => getStyle(feature.properties),
            onEachFeature: (feature, layer) => {
                layer.bindPopup(createPopupContent(feature.properties));
            }
        }).addTo(map);

        populateDataTable(filteredData);
    });

    // Export CSV
    document.getElementById('exportData').addEventListener('click', function() {
        var csv = 'Area Name,Flood Risk,Land Use,Ownership,Area (approx.)\n';
        floodData.forEach(function(feature) {
            var p = feature.properties;
            csv += `"${p.name || 'Unnamed'}",${p.flood_risk},${p.land_use || 'N/A'},${p.ownership || 'N/A'},N/A\n`;
        });
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'flood_analysis_data.csv';
        link.click();
    });

    // Print map
    document.getElementById('printMap').addEventListener('click', function() {
        window.print();
    });

    function getStyle(p) {
        var color = '#3388ff';
        if (p.flood_risk === 'high') color = '#ff0000';
        else if (p.flood_risk === 'medium') color = '#ffff00';
        else if (p.flood_risk === 'low') color = '#00ff00';
        return { color: color, weight: 2, opacity: 0.7, fillOpacity: 0.5 };
    }

    function createPopupContent(p) {
        return `<div class="map-popup">
            <h6>${p.name || 'Unnamed Area'}</h6>
            <table class="table table-sm">
                <tr><th>Flood Risk:</th><td>${p.flood_risk || 'N/A'}</td></tr>
                <tr><th>Land Use:</th><td>${p.land_use || 'N/A'}</td></tr>
                <tr><th>Ownership:</th><td>${p.ownership || 'N/A'}</td></tr>
                <tr><th>Classification:</th><td>${p.classification || 'N/A'}</td></tr>
            </table>
        </div>`;
    }

    function populateDataTable(data) {
        var tbody = document.querySelector('#floodDataTable tbody');
        tbody.innerHTML = '';
        data.forEach(function(feature) {
            var p = feature.properties;
            var row = document.createElement('tr');
            row.innerHTML = `
                <td>${p.name || 'Unnamed'}</td>
                <td><span class="badge bg-${getRiskBadgeClass(p.flood_risk)}">${p.flood_risk || 'N/A'}</span></td>
                <td>${p.land_use || 'N/A'}</td>
                <td>${p.ownership || 'N/A'}</td>
                <td>N/A</td>
            `;
            tbody.appendChild(row);
        });
    }

    function getRiskBadgeClass(risk) {
        switch(risk) {
            case 'high': return 'danger';
            case 'medium': return 'warning';
            case 'low': return 'success';
            default: return 'secondary';
        }
    }

    function createCharts(data) {
        var riskCounts = { high: 0, medium: 0, low: 0, none: 0 };
        var landUseCounts = {};
        data.forEach(function(f) {
            var r = f.properties.flood_risk || 'none';
            riskCounts[r] = (riskCounts[r] || 0) + 1;
            var l = f.properties.land_use || 'Unknown';
            landUseCounts[l] = (landUseCounts[l] || 0) + 1;
        });
        new Chart(document.getElementById('floodRiskChart'), {
            type: 'doughnut',
            data: {
                labels: ['High', 'Medium', 'Low', 'None'],
                datasets: [{ data: [riskCounts.high, riskCounts.medium, riskCounts.low, riskCounts.none], backgroundColor: ['#ff0000','#ffff00','#00ff00','#3388ff'] }]
            }
        });
        new Chart(document.getElementById('landUseChart'), {
            type: 'bar',
            data: { labels: Object.keys(landUseCounts), datasets: [{ label: 'Areas', data: Object.values(landUseCounts), backgroundColor: '#3388ff' }] },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }
});
</script>
@endpush
