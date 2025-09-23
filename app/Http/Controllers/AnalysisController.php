<?php

namespace App\Http\Controllers;

use App\Models\Metadata;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    /**
     * =====================
     * BLADE VIEWS (UI ONLY)
     * =====================
     */
    public function floodAreas()
    {
        return view('analysis.flood-areas');
    }

    public function healthStatus()
    {
        return view('analysis.health-status');
    }

    public function landUseAnalysis()
    {
        return view('analysis.land-use');
    }

    /**
     * =====================
     * API ROUTES (JSON DATA)
     * =====================
     */
    public function floodAreasJson()
    {
        $floodAreas = Metadata::with('layer')
            ->whereIn('flood_risk', ['medium', 'high'])
            ->get()
            ->map(function($metadata) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($metadata->layer->geojson_data)->geometry ?? null,
                    'properties' => [
                        'name' => $metadata->layer->name,
                        'flood_risk' => $metadata->flood_risk,
                        'land_use' => $metadata->land_use
                    ]
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $floodAreas,
        ]);
    }

    public function healthStatusJson()
    {
        $healthData = Metadata::with('layer')
            ->whereNotNull('health_status')
            ->get()
            ->map(function($metadata) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($metadata->layer->geojson_data)->geometry ?? null,
                    'properties' => [
                        'name' => $metadata->layer->name,
                        'health_status' => $metadata->health_status,
                        'disease_cases' => $metadata->disease_cases,
                        'clinics_available' => $metadata->clinics_available
                    ]
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $healthData,
        ]);
    }

    public function landUseJson()
    {
        $landUseData = Metadata::with('layer')
            ->whereNotNull('land_use')
            ->get()
            ->map(function($metadata) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($metadata->layer->geojson_data)->geometry ?? null,
                    'properties' => [
                        'name' => $metadata->layer->name,
                        'land_use' => $metadata->land_use,
                        'ownership' => $metadata->ownership,
                        'classification' => $metadata->classification
                    ]
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $landUseData,
        ]);
    }
}
