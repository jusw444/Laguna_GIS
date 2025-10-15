<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HealthStatus;

class HealthStatisticsService
{
    /**
     * Statistics API (with filters applied).
     */
    public function stats(Request $request)
{
    $query = HealthStatus::query();

    // Apply filters kung meron sa request
    if ($request->health_status && $request->health_status !== 'all') {
        $query->where('health_status', $request->health_status);
    }

    if ($request->disease_level && $request->disease_level !== 'all') {
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

    $healthStatus = [
        'excellent' => (clone $query)->where('health_status', 'excellent')->count(),
        'good'      => (clone $query)->where('health_status', 'good')->count(),
        'fair'      => (clone $query)->where('health_status', 'fair')->count(),
        'poor'      => (clone $query)->where('health_status', 'poor')->count(),
    ];

    $landUse = (clone $query)
        ->select('land_use', DB::raw('COUNT(*) as total'))
        ->groupBy('land_use')
        ->pluck('total', 'land_use');

    $clinics = (clone $query)->sum('clinics_available');

    return response()->json([
        'healthStatus' => $healthStatus,
        'landUse' => $landUse,
        'clinics' => $clinics,
    ]);
}
}
