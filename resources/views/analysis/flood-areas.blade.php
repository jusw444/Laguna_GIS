<!-- resources/views/analysis/flood-areas.blade.php -->
@extends('layouts.app')

@section('title', 'Flood Area Analysis')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-water"></i> Flood Area Analysis</h2>
                <div class="btn-group">
                    <a href="{{ route('flood-areas.create') }}" class="btn btn-danger">
                        <i class="fas fa-plus"></i> Add Flood Area
                    </a>
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
                @include('sidebars.flood-sidebar')

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
                                <table class="table table-striped align-middle" id="floodDataTable">
                                    <thead>
                                        <tr>
                                            <th>Area Name</th>
                                            <th>Flood Risk</th>
                                            <th>Land Use</th>
                                            <th>Ownership</th>
                                            <th>Classification</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($floodAreas as $area)
                                            <tr>
                                                <td>{{ $area->name }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $area->flood_risk == 'high' ? 'danger' : ($area->flood_risk == 'medium' ? 'warning' : ($area->flood_risk == 'low' ? 'success' : 'secondary')) }}">
                                                        {{ ucfirst($area->flood_risk) }}
                                                    </span>
                                                </td>
                                                <td>{{ $area->land_use ?? 'N/A' }}</td>
                                                <td>{{ $area->ownership ?? 'N/A' }}</td>
                                                <td>{{ $area->classification ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('flood-areas.edit', $area->id) }}"
                                                        class="btn btn-sm text-white"
                                                        style="background-color:#0d47a1; border-color:#0d47a1;">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>

                                                    <form action="{{ route('flood-areas.destroy', $area->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this flood area?')">
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-image/0.4.0/leaflet-image.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var floodAreas = @json($floodAreas);

            var geojsonData = {
                type: "FeatureCollection",
                features: floodAreas.map(area => ({
                    type: "Feature",
                    geometry: JSON.parse(area.geometry),
                    properties: area
                }))
            };

            // Init map
            var map = L.map('map').setView([14.2822, 121.4163], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Legend
            var legend = L.control({ position: 'bottomright' });
            legend.onAdd = () => {
                var div = L.DomUtil.create('div', 'legend bg-white p-2 border');
                div.innerHTML = `
                    <h6>Flood Risk Legend</h6>
                    <div><span style="background:#ff0000;width:20px;height:20px;display:inline-block"></span> High Risk</div>
                    <div><span style="background:#ffff00;width:20px;height:20px;display:inline-block"></span> Medium Risk</div>
                    <div><span style="background:#00ff00;width:20px;height:20px;display:inline-block"></span> Low Risk</div>
                    <div><span style="background:#3388ff;width:20px;height:20px;display:inline-block"></span> No Risk</div>
                `;
                return div;
            };
            legend.addTo(map);

            document.getElementById('toggleLegend').addEventListener('change', e => {
                e.target.checked ? legend.addTo(map) : map.removeControl(legend);
            });

            var floodLayers = L.geoJSON(geojsonData, {
                style: f => getStyle(f.properties),
                onEachFeature: (f, layer) => layer.bindPopup(createPopupContent(f.properties))
            }).addTo(map);

            if (floodAreas.length > 0) map.fitBounds(floodLayers.getBounds());

            createCharts(geojsonData.features);

            // Helpers
            function getStyle(p) {
                var color = '#3388ff';
                if (p.flood_risk === 'high') color = '#ff0000';
                else if (p.flood_risk === 'medium') color = '#ffff00';
                else if (p.flood_risk === 'low') color = '#00ff00';
                return { color, weight: 2, opacity: 0.7, fillOpacity: 0.5 };
            }

            function createPopupContent(p) {
                return `<strong>${p.name}</strong><br>
                Flood Risk: ${p.flood_risk}<br>
                Land Use: ${p.land_use||'N/A'}<br>
                Ownership: ${p.ownership||'N/A'}<br>
                Classification: ${p.classification||'N/A'}`;
            }

            function createCharts(features) {
                var riskCounts = { high: 0, medium: 0, low: 0, none: 0 };
                var landUseCounts = {};
                features.forEach(f => {
                    var r = f.properties.flood_risk || 'none';
                    riskCounts[r] = (riskCounts[r] || 0) + 1;
                    var l = f.properties.land_use || 'Unknown';
                    landUseCounts[l] = (landUseCounts[l] || 0) + 1;
                });
                new Chart(document.getElementById('floodRiskChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['High', 'Medium', 'Low', 'None'],
                        datasets: [{
                            data: [riskCounts.high, riskCounts.medium, riskCounts.low, riskCounts.none],
                            backgroundColor: ['#ff0000', '#ffff00', '#00ff00', '#3388ff']
                        }]
                    }
                });
                new Chart(document.getElementById('landUseChart'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(landUseCounts),
                        datasets: [{
                            label: 'Areas',
                            data: Object.values(landUseCounts),
                            backgroundColor: '#3388ff'
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            }

            // ✅ Export Data (CSV)
            document.getElementById('exportData').addEventListener('click', function() {
                var csv = "Area Name,Flood Risk,Land Use,Ownership,Classification\n";
                floodAreas.forEach(area => {
                    csv += `"${area.name}","${area.flood_risk}","${area.land_use||''}","${area.ownership||''}","${area.classification||''}"\n`;
                });
                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.href = url;
                a.download = "flood_areas.csv";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });

            // ✅ Print Map
            document.getElementById('printMap').addEventListener('click', function() {
                leafletImage(map, function(err, canvas) {
                    var imgData = canvas.toDataURL("image/png");
                    var w = window.open("");
                    w.document.write("<html><head><title>Print Map</title></head><body>");
                    w.document.write("<h3>Flood Risk Map</h3>");
                    w.document.write("<img src='" + imgData + "' style='width:100%;border:1px solid #000;'/>");
                    w.document.write("<br><small>Generated from Flood Area Analysis</small>");
                    w.document.write("</body></html>");
                    w.document.close();
                    w.print();
                });
            });
        });
    </script>
@endpush
