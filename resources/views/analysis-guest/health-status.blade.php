<!-- resources/views/analysis/health-status.blade.php -->
@extends('layouts.guest')

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
            @include('sidebars.health-sidebar', [
        'formAction' => route('landing.health')
    ])

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
                            <table class="table table-striped align-middle" id="healthDataTable">
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
                                    @foreach ($statuses as $s)
                                        <tr>
                                            <td>{{ $s->name ?? 'Unnamed' }}</td>
                                            <td>
                                                <span class="badge 
                                                    {{ $s->health_status == 'excellent' ? 'bg-success' : 
                                                       ($s->health_status == 'good' ? 'bg-primary' : 
                                                       ($s->health_status == 'fair' ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ ucfirst($s->health_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $s->disease_cases ?? 0 }}</td>
                                            <td>{{ $s->clinics_available ?? 0 }}</td>
                                            <td>{{ $s->land_use ?? 'N/A' }}</td>
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
<script>
document.addEventListener('DOMContentLoaded', async function() {
    var healthStatuses = @json($statuses);

    // Build GeoJSON
    var geojsonData = {
        type: "FeatureCollection",
        features: healthStatuses.map(s => ({
            type: "Feature",
            geometry: JSON.parse(s.geometry),
            properties: s
        }))
    };

    // Map init
    var map = L.map('map').setView([14.2096, 121.1656], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Legend
    var legend = L.control({ position: 'bottomright' });
    legend.onAdd = () => {
        var div = L.DomUtil.create('div', 'legend bg-white p-2 border');
        div.innerHTML = `
            <h6>Health Status Legend</h6>
            <div><span style="background:#00ff00;width:20px;height:20px;display:inline-block"></span> Excellent</div>
            <div><span style="background:#aaff00;width:20px;height:20px;display:inline-block"></span> Good</div>
            <div><span style="background:#ffff00;width:20px;height:20px;display:inline-block"></span> Fair</div>
            <div><span style="background:#ff0000;width:20px;height:20px;display:inline-block"></span> Poor</div>
        `;
        return div;
    };
    legend.addTo(map);

    document.getElementById('toggleLegend').addEventListener('change', e => {
        e.target.checked ? legend.addTo(map) : map.removeControl(legend);
    });

    var healthLayers = L.geoJSON(geojsonData, {
        style: f => getStyle(f.properties),
        onEachFeature: (f, layer) => layer.bindPopup(createPopupContent(f.properties))
    }).addTo(map);

    if (healthStatuses.length > 0) map.fitBounds(healthLayers.getBounds());

    // Load stats via API
    const params = new URLSearchParams(window.location.search);
    const statsRes = await fetch(`{{ route('health-status.stats') }}?${params.toString()}`);
    const stats = await statsRes.json();

    createCharts(stats);
    document.getElementById('clinicsTotal').textContent = stats.clinics;

    // Helpers
    function getStyle(p) {
        var color = '#3388ff';
        if (p.health_status === 'excellent') color = '#00ff00';
        else if (p.health_status === 'good') color = '#aaff00';
        else if (p.health_status === 'fair') color = '#ffff00';
        else if (p.health_status === 'poor') color = '#ff0000';
        return { color, weight: 2, opacity: 0.7, fillOpacity: 0.5 };
    }

    function createPopupContent(p) {
        return `<strong>${p.name}</strong><br>
        Health Status: ${p.health_status}<br>
        Disease Cases: ${p.disease_cases||0}<br>
        Clinics Available: ${p.clinics_available||0}<br>
        Land Use: ${p.land_use||'N/A'}`;
    }

    function createCharts(stats) {
        new Chart(document.getElementById('healthStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Excellent','Good','Fair','Poor'],
                datasets: [{
                    data: [
                        stats.healthStatus.excellent,
                        stats.healthStatus.good,
                        stats.healthStatus.fair,
                        stats.healthStatus.poor
                    ],
                    backgroundColor: ['#00ff00','#aaff00','#ffff00','#ff0000']
                }]
            }
        });

        new Chart(document.getElementById('diseaseChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(stats.landUse),
                datasets: [{
                    label: 'Areas',
                    data: Object.values(stats.landUse),
                    backgroundColor: '#3388ff'
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    // Export CSV
    document.getElementById('exportData').addEventListener('click', function() {
        var csv = "Area Name,Health Status,Disease Cases,Clinics Available,Land Use\n";
        healthStatuses.forEach(s => {
            csv += `"${s.name}","${s.health_status}",${s.disease_cases||0},${s.clinics_available||0},"${s.land_use||''}"\n`;
        });
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = "health_status.csv";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    // Print Map
    document.getElementById('printMap').addEventListener('click', function() {
        window.print();
    });
});
</script>
@endpush
