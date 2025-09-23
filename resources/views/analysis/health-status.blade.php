<!-- resources/views/analysis/health-status.blade.php -->
@extends('layouts.app')

@section('title', 'Health Status Analysis')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-heartbeat"></i> Health Status Analysis</h2>
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
                            <label for="healthStatusFilter" class="form-label">Health Status</label>
                            <select class="form-select" id="healthStatusFilter">
                                <option value="all">All Statuses</option>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="diseaseFilter" class="form-label">Disease Cases</label>
                            <select class="form-select" id="diseaseFilter">
                                <option value="all">All Levels</option>
                                <option value="high">High (50+ cases)</option>
                                <option value="medium">Medium (10-49 cases)</option>
                                <option value="low">Low (1-9 cases)</option>
                                <option value="none">No Cases</option>
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
                            <h6>Health Status Distribution</h6>
                            <canvas id="healthStatusChart" height="200"></canvas>
                        </div>
                        <div class="mb-3">
                            <h6>Disease Cases</h6>
                            <canvas id="diseaseChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map + Table -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Health Status Map</h5>
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
                        <h5 class="card-title mb-0">Health Status Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="healthDataTable">
                                <thead>
                                    <tr>
                                        <th>Area Name</th>
                                        <th>Health Status</th>
                                        <th>Disease Cases</th>
                                        <th>Clinics Available</th>
                                        <th>Land Use</th>
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
    var map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Legend
    var legend = L.control({position: 'bottomright'});
    legend.onAdd = function(map) {
        var div = L.DomUtil.create('div', 'legend');
        div.innerHTML = '<h6>Health Status Legend</h6>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #00ff00"></span> Excellent</div>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #aaff00"></span> Good</div>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #ffff00"></span> Fair</div>' +
            '<div class="legend-item"><span class="legend-color" style="background-color: #ff0000"></span> Poor</div>';
        return div;
    };
    legend.addTo(map);

    document.getElementById('toggleLegend').addEventListener('change', function() {
        this.checked ? legend.addTo(map) : map.removeControl(legend);
    });

    var healthData = [];
    var healthLayers = [];

    // ✅ FIXED: use API route for JSON
    fetch("{{ route('api.analysis.health-status') }}")
        .then(response => response.json())
        .then(data => {
            healthData = data.features;

            healthLayers = L.geoJSON(data, {
                style: f => getStyle(f.properties),
                onEachFeature: (f, layer) => {
                    layer.bindPopup(createPopupContent(f.properties));
                }
            }).addTo(map);

            if (healthData.length > 0) {
                map.fitBounds(healthLayers.getBounds());
            }

            populateDataTable(healthData);
            createCharts(healthData);
        });

    document.getElementById('applyFilters').addEventListener('click', function() {
        var statusFilter = document.getElementById('healthStatusFilter').value;
        var diseaseFilter = document.getElementById('diseaseFilter').value;

        var filtered = healthData.filter(f => {
            var s = statusFilter === 'all' || f.properties.health_status === statusFilter;
            var d = diseaseFilter === 'all' || checkDiseaseLevel(f.properties.disease_cases, diseaseFilter);
            return s && d;
        });

        map.removeLayer(healthLayers);
        healthLayers = L.geoJSON({
            type: 'FeatureCollection',
            features: filtered
        }, {
            style: f => getStyle(f.properties),
            onEachFeature: (f, layer) => {
                layer.bindPopup(createPopupContent(f.properties));
            }
        }).addTo(map);

        populateDataTable(filtered);
    });

    function checkDiseaseLevel(cases, level) {
        cases = cases || 0;
        switch(level) {
            case 'high': return cases >= 50;
            case 'medium': return cases >= 10 && cases < 50;
            case 'low': return cases > 0 && cases < 10;
            case 'none': return cases === 0;
            default: return true;
        }
    }

    function getStyle(p) {
        var color = '#3388ff';
        if (p.health_status === 'excellent') color = '#00ff00';
        else if (p.health_status === 'good') color = '#aaff00';
        else if (p.health_status === 'fair') color = '#ffff00';
        else if (p.health_status === 'poor') color = '#ff0000';
        return { color: color, weight: 2, opacity: 0.7, fillOpacity: 0.5 };
    }

    function createPopupContent(p) {
        return `<div class="map-popup">
            <h6>${p.name || 'Unnamed Area'}</h6>
            <table class="table table-sm">
                <tr><th>Health Status:</th><td>${p.health_status || 'N/A'}</td></tr>
                <tr><th>Disease Cases:</th><td>${p.disease_cases || 0}</td></tr>
                <tr><th>Clinics Available:</th><td>${p.clinics_available || 0}</td></tr>
                <tr><th>Land Use:</th><td>${p.land_use || 'N/A'}</td></tr>
            </table>
        </div>`;
    }

    function populateDataTable(data) {
        var tbody = document.querySelector('#healthDataTable tbody');
        tbody.innerHTML = '';
        data.forEach(f => {
            var p = f.properties;
            var row = document.createElement('tr');
            row.innerHTML = `
                <td>${p.name || 'Unnamed'}</td>
                <td><span class="badge bg-${getBadge(p.health_status)}">${p.health_status || 'N/A'}</span></td>
                <td>${p.disease_cases || 0}</td>
                <td>${p.clinics_available || 0}</td>
                <td>${p.land_use || 'N/A'}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function getBadge(status) {
        switch(status) {
            case 'excellent': return 'success';
            case 'good': return 'primary';
            case 'fair': return 'warning';
            case 'poor': return 'danger';
            default: return 'secondary';
        }
    }

    function createCharts(data) {
        var statusCounts = { excellent: 0, good: 0, fair: 0, poor: 0 };
        var diseaseLevels = { none: 0, low: 0, medium: 0, high: 0 };

        data.forEach(f => {
            var s = f.properties.health_status || 'fair';
            statusCounts[s] = (statusCounts[s] || 0) + 1;

            var cases = f.properties.disease_cases || 0;
            if (cases >= 50) diseaseLevels.high++;
            else if (cases >= 10) diseaseLevels.medium++;
            else if (cases > 0) diseaseLevels.low++;
            else diseaseLevels.none++;
        });

        new Chart(document.getElementById('healthStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Excellent','Good','Fair','Poor'],
                datasets: [{ data: [statusCounts.excellent,statusCounts.good,statusCounts.fair,statusCounts.poor], backgroundColor: ['#00ff00','#aaff00','#ffff00','#ff0000'] }]
            }
        });

        new Chart(document.getElementById('diseaseChart'), {
            type: 'bar',
            data: {
                labels: ['No Cases','Low (1-9)','Medium (10-49)','High (50+)'],
                datasets: [{ label: 'Areas', data: [diseaseLevels.none,diseaseLevels.low,diseaseLevels.medium,diseaseLevels.high], backgroundColor: '#3388ff' }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    document.getElementById('exportData').addEventListener('click', function() {
        var csv = 'Area Name,Health Status,Disease Cases,Clinics Available,Land Use\n';
        healthData.forEach(f => {
            var p = f.properties;
            csv += `"${p.name || 'Unnamed'}",${p.health_status},${p.disease_cases || 0},${p.clinics_available || 0},${p.land_use || 'N/A'}\n`;
        });
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'health_analysis_data.csv';
        link.click();
    });

    document.getElementById('printMap').addEventListener('click', function() {
        window.print();
    });
});
</script>
@endpush
