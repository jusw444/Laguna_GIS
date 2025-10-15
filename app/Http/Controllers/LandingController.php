<?php

namespace App\Http\Controllers;

use App\Models\FloodArea;
use App\Models\HealthStatus;
use App\Models\LandUse;
use App\Services\FloodStatisticsService;
use App\Services\HealthStatisticsService;
use App\Services\LandStatisticsService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display main page (Flood Analysis)
     */
    public function flood(Request $request)
    {
        $query = FloodArea::query();

        // Apply filters
        if ($request->filled('flood_risk') && $request->flood_risk !== 'all') {
            $query->where('flood_risk', $request->flood_risk);
        }

        if ($request->filled('land_use') && $request->land_use !== 'all') {
            $query->where('land_use', $request->land_use);
        }

        $floodAreas = $query->get();

        return view('analysis-guest.flood-areas', compact('floodAreas'));
    }

    public function health(Request $request)
    {
        $query = HealthStatus::query();

        // Apply Health Status filter
        if ($request->filled('health_status') && $request->health_status !== 'all') {
            $query->where('health_status', $request->health_status);
        }

        // Apply Disease Level filter
        if ($request->filled('disease_level') && $request->disease_level !== 'all') {
            if ($request->disease_level === 'high') {
                $query->where('disease_cases', '>=', 50);
            } elseif ($request->disease_level === 'medium') {
                $query->whereBetween('disease_cases', [10, 49]);
            } elseif ($request->disease_level === 'low') {
                $query->whereBetween('disease_cases', [1, 9]);
            } elseif ($request->disease_level === 'none') {
                $query->where('disease_cases', 0);
            }
        }

        $statuses = $query->get();

        return view('analysis-guest.health-status', compact('statuses'));
    }

    public function land(Request $request)
    {
        $query = LandUse::query();

        // Apply filters
        if ($request->filled('land_use') && $request->land_use !== 'all') {
            $query->where('land_use', $request->land_use);
        }

        if ($request->filled('ownership') && $request->ownership !== 'all') {
            $query->where('ownership', $request->ownership);
        }

        if ($request->filled('classification') && $request->classification !== 'all') {
            $query->where('classification', $request->classification);
        }

        if ($request->filled('flood_risk') && $request->flood_risk !== 'all') {
            $query->where('flood_risk', $request->flood_risk);
        }

        $landUses = $query->get();

        return view('analysis-guest.land-use', compact('landUses'));
    }
}
