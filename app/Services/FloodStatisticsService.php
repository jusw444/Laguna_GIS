<?php

namespace App\Services;

use App\Models\FloodArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FloodStatisticsService
{
    /**
     * API for chart statistics (used in Blade AJAX)
     */
    public function stats(Request $request)
{
    try {
        $query = FloodArea::query();

        // Filters
        if ($request->filled('flood_risk') && $request->flood_risk !== 'all') {
            $query->where('flood_risk', $request->flood_risk);
        }
        if ($request->filled('land_use') && $request->land_use !== 'all') {
            $query->where('land_use', $request->land_use);
        }

        $floodAreas = $query->get();

        // Flood Risk
        $floodRiskStats = [
            'high' => $floodAreas->where('flood_risk', 'high')->count(),
            'medium' => $floodAreas->where('flood_risk', 'medium')->count(),
            'low' => $floodAreas->where('flood_risk', 'low')->count(),
            'none' => $floodAreas->where('flood_risk', 'none')->count(),
        ];

        // Land Use (ignore null)
        $landUseStats = $floodAreas
            ->filter(fn($a) => !empty($a->land_use))
            ->groupBy('land_use')
            ->map(fn($group) => $group->count());

        return response()->json([
            'floodRisk' => $floodRiskStats,
            'landUse' => $landUseStats,
            'total' => $floodAreas->count(),
        ]);

    } catch (\Throwable $e) {
        // Prevent 500 crash and log it for you
        Log::error('Flood stats error: ' . $e->getMessage());
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}