<!-- resources/views/analysis/flood-areas.blade.php -->
@extends('layouts.guest')

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
            @include('sidebars.flood-sidebar', [
        'formAction' => route('landing.flood')
    ])

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

    // ✅ Fetch stats from controller
    fetch("{{ route('flood-areas.stats') }}")
  .then(async res => {
      if (!res.ok) {
          const text = await res.text();
          console.error("Stats fetch failed:", text);
          throw new Error(text);
      }
      return res.json();
  })
  .then(stats => {
      console.log("✅ Stats received:", stats);
      createCharts(stats);
  })
  .catch(err => console.error("❌ Stats error:", err));

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

    // ✅ Updated charts (data from controller)
    function createCharts(stats) {
        new Chart(document.getElementById('floodRiskChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stats.floodRisk),
                datasets: [{
                    data: Object.values(stats.floodRisk),
                    backgroundColor: ['#ff0000', '#ffff00', '#00ff00', '#3388ff']
                }]
            }
        });

        new Chart(document.getElementById('landUseChart'), {
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
});
</script>
@endpush
