<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LandUse;

class LandStatisticsService
{
    public function stats(Request $request)
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

        return response()->json([
            'landUse' => $query->clone()
                ->select('land_use', DB::raw('count(*) as total'))
                ->groupBy('land_use')
                ->pluck('total', 'land_use'),

            'ownership' => $query->clone()
                ->select('ownership', DB::raw('count(*) as total'))
                ->groupBy('ownership')
                ->pluck('total', 'ownership'),

            'floodRisk' => $query->clone()
                ->select('flood_risk', DB::raw('count(*) as total'))
                ->groupBy('flood_risk')
                ->pluck('total', 'flood_risk'),
        ]);
    }
}