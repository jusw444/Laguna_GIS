<?php

namespace App\Http\Controllers;

use App\Models\FloodArea;
use App\Services\FloodStatisticsService;
use Illuminate\Http\Request;

class FloodAreaController extends Controller
{

    protected $floodStats;
    public function __construct(FloodStatisticsService $floodStats)
    {
        $this->floodStats = $floodStats;
    }

    /**
     * Display main page (Flood Analysis)
     */
    public function index(Request $request)
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

        return view('analysis.flood-areas', compact('floodAreas'));
    }

    /**
     * Create new flood area
     */
    public function create()
    {
        return view('flood-areas.create');
    }

    /**
     * Store new record
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'nullable|string|max:255',
            'flood_risk'    => 'required|in:high,medium,low,none',
            'land_use'      => 'nullable|string|max:255',
            'ownership'     => 'nullable|string|max:255',
            'classification'=> 'nullable|string|max:255',
            'geometry'      => 'required|json',
        ]);

        FloodArea::create($request->all());

        return redirect()->route('flood-areas.index')
            ->with('success', 'Flood area added successfully.');
    }

    /**
     * Edit record
     */
    public function edit(FloodArea $floodArea)
    {
        return view('flood-areas.edit', compact('floodArea'));
    }

    /**
     * Update record
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

        return redirect()->route('flood-areas.index')
            ->with('success', 'Flood area updated successfully.');
    }

    /**
     * Delete record
     */
    public function destroy(FloodArea $floodArea)
    {
        $floodArea->delete();

        return redirect()->route('flood-areas.index')
            ->with('success', 'Flood area deleted successfully.');
    }
    /**
     * API endpoint to get all flood areas as GeoJSON.
     */
    public function stats(Request $request)
{
    return $this->floodStats->stats($request);
}
}
