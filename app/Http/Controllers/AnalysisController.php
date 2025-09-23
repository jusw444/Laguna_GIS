<?php

namespace App\Http\Controllers;

use App\Models\Metadata;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
     public function floodAreas()
    {
        $floodAreas = Metadata::with('layer')
            ->whereIn('flood_risk', ['medium', 'high'])
            ->get()
            ->map(function($metadata) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($metadata->layer->geojson_data)->geometry,
                    'properties' => [
                        'name' => $metadata->layer->name,
                        'flood_risk' => $metadata->flood_risk,
                        'land_use' => $metadata->land_use
                    ]
                ];
            });
            
        return view('analysis.flood-areas', compact('floodAreas'));
    }

    public function healthStatus()
    {
        $healthData = Metadata::with('layer')
            ->get()
            ->map(function($metadata) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($metadata->layer->geojson_data)->geometry,
                    'properties' => [
                        'name' => $metadata->layer->name,
                        'health_status' => $metadata->health_status,
                        'disease_cases' => $metadata->disease_cases,
                        'clinics_available' => $metadata->clinics_available
                    ]
                ];
            });
            
        return view('analysis.health-status', compact('healthData'));
    }

    public function landUseAnalysis()
    {
        $landUseData = Metadata::with('layer')
            ->get()
            ->map(function($metadata) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($metadata->layer->geojson_data)->geometry,
                    'properties' => [
                        'name' => $metadata->layer->name,
                        'land_use' => $metadata->land_use,
                        'ownership' => $metadata->ownership,
                        'classification' => $metadata->classification
                    ]
                ];
            });
            
        return view('analysis.land-use', compact('landUseData'));
    }
}
