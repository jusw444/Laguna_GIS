<?php

namespace App\Http\Controllers;

use App\Models\FloodArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FloodAreaController extends Controller
{
    /**
     * Display a listing of flood areas with optional filters.
     */
    public function index(Request $request)
    {
        $query = FloodArea::query();

        // Apply Flood Risk filter
        if ($request->filled('flood_risk') && $request->flood_risk !== 'all') {
            $query->where('flood_risk', $request->flood_risk);
        }

        // Apply Land Use filter
        if ($request->filled('land_use') && $request->land_use !== 'all') {
            $query->where('land_use', $request->land_use);
        }

        $floodAreas = $query->get();

        return view('analysis.flood-areas', compact('floodAreas'));
    }

    /**
     * Show form for creating a new flood area.
     */
    public function create()
    {
        return view('flood-areas.create');
    }

    /**
     * Store a new flood area.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'nullable|string|max:255',
            'flood_risk'    => 'required|in:high,medium,low,none',
            'land_use'      => 'nullable|string|max:255',
            'ownership'     => 'nullable|string|max:255',
            'classification'=> 'nullable|string|max:255',
            'geometry'      => 'required|json', // geometry must be valid JSON
        ]);

        FloodArea::create($request->all());

        return redirect()
            ->route('flood-areas.index')
            ->with('success', 'Flood area added successfully.');
    }

    /**
     * Edit form.
     */
    public function edit(FloodArea $floodArea)
    {
        return view('flood-areas.edit', compact('floodArea'));
    }

    /**
     * Update flood area.
     */
    public function update(Request $request, FloodArea $floodArea)
    {
        $request->validate([
            'name'          => 'nullable|string|max:255',
            'flood_risk'    => 'required|in:high,medium,low,none',
            'land_use'      => 'nullable|string|max:255',
            'ownership'     => 'nullable|string|max:255',
            'classification'=> 'nullable|string|max:255',
            'geometry'      => 'required|json',
        ]);

        $floodArea->update($request->all());

        return redirect()
            ->route('flood-areas.index')
            ->with('success', 'Flood area updated successfully.');
    }

    /**
     * Delete flood area.
     */
    public function destroy(FloodArea $floodArea)
    {
        $floodArea->delete();

        return redirect()
            ->route('flood-areas.index')
            ->with('success', 'Flood area deleted successfully.');
    }

    /**
     * Statistics API (with filters applied).
     */
    public function stats(Request $request)
    {
        $query = FloodArea::query();

        // Apply filters
        if ($request->filled('flood_risk') && $request->flood_risk !== 'all') {
            $query->where('flood_risk', $request->flood_risk);
        }
        if ($request->filled('land_use') && $request->land_use !== 'all') {
            $query->where('land_use', $request->land_use);
        }

        // Flood Risk Stats
        $riskStats = (clone $query)
            ->select('flood_risk', DB::raw('COUNT(*) as total'))
            ->groupBy('flood_risk')
            ->pluck('total', 'flood_risk');

        $riskResult = [
            'high'   => $riskStats['high'] ?? 0,
            'medium' => $riskStats['medium'] ?? 0,
            'low'    => $riskStats['low'] ?? 0,
            'none'   => $riskStats['none'] ?? 0,
        ];

        // Land Use Stats
        $landUseStats = (clone $query)
            ->select('land_use', DB::raw('COUNT(*) as total'))
            ->groupBy('land_use')
            ->pluck('total', 'land_use');

        return response()->json([
            'floodRisk' => $riskResult,
            'landUse'   => $landUseStats,
        ]);
    }
}
