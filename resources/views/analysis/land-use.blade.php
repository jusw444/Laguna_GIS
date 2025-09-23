<!-- resources/views/analysis/land-use.blade.php -->
@extends('layouts.app')

@section('title', 'Land Use Analysis')

@section('content')
<div class="row">
    <div class="col-md-12">
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
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Analysis Options</h5>
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
                        <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Land Use Distribution</h6>
                            <canvas id="landUseChart" height="200"></canvas>
                        </div>
                        <div class="mb-3">
                            <h6>Ownership Distribution</h6>
                            <canvas id="ownershipChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
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

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Land Use Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="landUseDataTable">
                                <thead>
                                    <tr>
                                        <th>Area Name</th>
                                        <th>Land Use</th>
                                        <th>Ownership</th>
                                        <th>Classification</th>
                                        <th>Flood Risk</th>
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
            div.innerHTML = '<h6>Land Use Legend</h6>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #ff9999"></span> Residential</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #77ff77"></span> Commercial</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #ffff77"></span> Agricultural</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #ff77ff"></span> Industrial</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #77ffff"></span> Recreational</div>' +
                '<div class="legend-item"><span class="legend-color" style="background-color: #7777ff"></span> Conservation</div>';
            return div;
        };
        legend.addTo(map);

        // Toggle legend visibility
        document.getElementById('toggleLegend').addEventListener('change', function() {
            if (this.checked) {
                legend.addTo(map);
            } else {
                map.removeControl(legend);
            }
        });

        // Load land use data
        var landUseData = [];
        var landUseLayers = [];

        fetch("{{ route('analysis.land-use') }}")
            .then(response => response.json())
            .then(data => {
                landUseData = data.features;
                
                // Add data to map
                landUseLayers = L.geoJSON(data, {
                    style: function(feature) {
                        return getStyle(feature.properties);
                    },
                    onEachFeature: function(feature, layer) {
                        layer.bindPopup(createPopupContent(feature.properties));
                    }
                }).addTo(map);

                // Fit map to show all data
                if (landUseData.length > 0) {
                    map.fitBounds(landUseLayers.getBounds());
                }

                // Populate data table
                populateDataTable(landUseData);

                // Create charts
                createCharts(landUseData);
            });

        // Apply filters
        document.getElementById('applyFilters').addEventListener('click', function() {
            var landUseFilter = document.getElementById('landUseFilter').value;
            var ownershipFilter = document.getElementById('ownershipFilter').value;

            // Filter data
            var filteredData = landUseData.filter(function(feature) {
                var matchesLandUse = landUseFilter === 'all' || 
                    (feature.properties.land_use && 
                     feature.properties.land_use.toLowerCase() === landUseFilter);
                
                var matchesOwnership = ownershipFilter === 'all' || 
                    (feature.properties.ownership && 
                     feature.properties.ownership.toLowerCase() === ownershipFilter);
                
                return matchesLandUse && matchesOwnership;
            });

            // Update map
            map.removeLayer(landUseLayers);
            landUseLayers = L.geoJSON({
                type: 'FeatureCollection',
                features: filteredData
            }, {
                style: function(feature) {
                    return getStyle(feature.properties);
                },
                onEachFeature: function(feature, layer) {
                    layer.bindPopup(createPopupContent(feature.properties));
                }
            }).addTo(map);

            // Update data table
            populateDataTable(filteredData);
        });

        function getStyle(properties) {
            var color = '#3388ff'; // Default color
            
            if (properties.land_use) {
                var landUse = properties.land_use.toLowerCase();
                if (landUse.includes('residential')) {
                    color = '#ff9999';
                } else if (landUse.includes('commercial')) {
                    color = '#77ff77';
                } else if (landUse.includes('agricultural')) {
                    color = '#ffff77';
                } else if (landUse.includes('industrial')) {
                    color = '#ff77ff';
                } else if (landUse.includes('recreational')) {
                    color = '#77ffff';
                } else if (landUse.includes('conservation')) {
                    color = '#7777ff';
                }
            }
            
            return {
                color: color,
                weight: 2,
                opacity: 0.7,
                fillOpacity: 0.5
            };
        }

        function createPopupContent(properties) {
            return `<div class="map-popup">
                <h6>${properties.name || 'Unnamed Area'}</h6>
                <table class="table table-sm">
                    <tr><th>Land Use:</th><td>${properties.land_use || 'N/A'}</td></tr>
                    <tr><th>Ownership:</th><td>${properties.ownership || 'N/A'}</td></tr>
                    <tr><th>Classification:</th><td>${properties.classification || 'N/A'}</td></tr>
                    <tr><th>Flood Risk:</th><td>${properties.flood_risk || 'N/A'}</td></tr>
                </table>
            </div>`;
        }

        function populateDataTable(data) {
            var tableBody = document.querySelector('#landUseDataTable tbody');
            tableBody.innerHTML = '';

            data.forEach(function(feature) {
                var properties = feature.properties;
                var row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${properties.name || 'Unnamed'}</td>
                    <td><span class="badge bg-${getLandUseBadgeClass(properties.land_use)}">${properties.land_use || 'N/A'}</span></td>
                    <td>${properties.ownership || 'N/A'}</td>
                    <td>${properties.classification || 'N/A'}</td>
                    <td><span class="badge bg-${getRiskBadgeClass(properties.flood_risk)}">${properties.flood_risk || 'N/A'}</span></td>
                `;
                
                tableBody.appendChild(row);
            });
        }

        function getLandUseBadgeClass(landUse) {
            if (!landUse) return 'secondary';
            
            landUse = landUse.toLowerCase();
            if (landUse.includes('residential')) return 'primary';
            if (landUse.includes('commercial')) return 'success';
            if (landUse.includes('agricultural')) return 'warning';
            if (landUse.includes('industrial')) return 'danger';
            if (landUse.includes('recreational')) return 'info';
            if (landUse.includes('conservation')) return 'dark';
            return 'secondary';
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
            // Land use distribution chart
            var landUseCounts = {};
            var ownershipCounts = {};

            data.forEach(function(feature) {
                var landUse = feature.properties.land_use || 'Unknown';
                landUseCounts[landUse] = (landUseCounts[landUse] || 0) + 1;

                var ownership = feature.properties.ownership || 'Unknown';
                ownershipCounts[ownership] = (ownershipCounts[ownership] || 0) + 1;
            });

            // Land use chart
            var landUseCtx = document.getElementById('landUseChart').getContext('2d');
            new Chart(landUseCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(landUseCounts),
                    datasets: [{
                        data: Object.values(landUseCounts),
                        backgroundColor: ['#ff9999', '#77ff77', '#ffff77', '#ff77ff', '#77ffff', '#7777ff', '#cccccc']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Ownership chart
            var ownershipCtx = document.getElementById('ownershipChart').getContext('2d');
            new Chart(ownershipCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(ownershipCounts),
                    datasets: [{
                        label: 'Number of Areas',
                        data: Object.values(ownershipCounts),
                        backgroundColor: '#3388ff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Export data
        document.getElementById('exportData').addEventListener('click', function() {
            // Convert data to CSV
            var csv = 'Area Name,Land Use,Ownership,Classification,Flood Risk\n';
            
            landUseData.forEach(function(feature) {
                var properties = feature.properties;
                csv += `"${properties.name || 'Unnamed'}",${properties.land_use || 'N/A'},${properties.ownership || 'N/A'},${properties.classification || 'N/A'},${properties.flood_risk || 'N/A'}\n`;
            });

            // Download CSV
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'land_use_analysis_data.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Print map
        document.getElementById('printMap').addEventListener('click', function() {
            window.print();
        });
    });
</script>
@endpush