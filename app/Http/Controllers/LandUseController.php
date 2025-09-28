<?php

namespace App\Http\Controllers;

use App\Models\LandUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandUseController extends Controller
{
    public function index(Request $request)
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

        return view('analysis.land-use', compact('landUses'));
    }

    public function create()
    {
        return view('land-use.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'land_use' => 'required|string|max:255',
            'ownership' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:255',
            'flood_risk' => 'nullable|string|max:255',
            'geometry' => 'required|json',
        ]);

        LandUse::create($request->only([
            'name', 'land_use', 'ownership', 'classification', 'flood_risk', 'geometry'
        ]));

        return redirect()->route('land-use.index')->with('success', 'Land use data added successfully.');
    }

    public function edit(LandUse $landUse)
    {
        return view('land-use.edit', compact('landUse'));
    }

    public function update(Request $request, LandUse $landUse)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'land_use' => 'required|string|max:255',
            'ownership' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:255',
            'flood_risk' => 'nullable|string|max:255',
            'geometry' => 'required|json',
        ]);

        $landUse->update($request->only([
            'name', 'land_use', 'ownership', 'classification', 'flood_risk', 'geometry'
        ]));

        return redirect()->route('land-use.index')->with('success', 'Land use data updated successfully.');
    }

    public function destroy(LandUse $landUse)
    {
        $landUse->delete();
        return redirect()->route('land-use.index')->with('success', 'Land use data deleted successfully.');
    }

    public function apiIndex()
    {
        $data = LandUse::all()->map(function ($lu) {
            return [
                'type' => 'Feature',
                'geometry' => $lu->geometry ? json_decode($lu->geometry, true) : null,
                'properties' => [
                    'id' => $lu->id,
                    'name' => $lu->name,
                    'land_use' => $lu->land_use,
                    'ownership' => $lu->ownership,
                    'classification' => $lu->classification,
                    'flood_risk' => $lu->flood_risk,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $data,
        ]);
    }

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
