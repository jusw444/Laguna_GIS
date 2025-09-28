<?php

namespace App\Http\Controllers;

use App\Models\HealthStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthStatusController extends Controller
{
    /**
     * Display a listing of health statuses with optional filters.
     */
    public function index(Request $request)
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

        return view('analysis.health-status', compact('statuses'));
    }

    /**
     * Show form for creating new health status.
     */
    public function create()
    {
        return view('health-status.create');
    }

    /**
     * Store a new health status.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'nullable|string|max:255',
            'health_status'     => 'required|in:excellent,good,fair,poor',
            'disease_cases'     => 'nullable|integer|min:0',
            'clinics_available' => 'nullable|integer|min:0',
            'land_use'          => 'nullable|string|max:255',
            'geometry'          => 'required|json',
        ]);

        HealthStatus::create($request->all());

        return redirect()
            ->route('health-status.index')
            ->with('success', 'Health status added successfully.');
    }

    /**
     * Edit form.
     */
    public function edit(HealthStatus $healthStatus)
    {
        return view('health-status.edit', compact('healthStatus'));
    }

    /**
     * Update a health status.
     */
    public function update(Request $request, HealthStatus $healthStatus)
    {
        $request->validate([
            'name'              => 'nullable|string|max:255',
            'health_status'     => 'required|in:excellent,good,fair,poor',
            'disease_cases'     => 'nullable|integer|min:0',
            'clinics_available' => 'nullable|integer|min:0',
            'land_use'          => 'nullable|string|max:255',
            'geometry'          => 'required|json',
        ]);

        $healthStatus->update($request->all());

        return redirect()
            ->route('health-status.index')
            ->with('success', 'Health status updated successfully.');
    }

    /**
     * Delete health status.
     */
    public function destroy(HealthStatus $healthStatus)
    {
        $healthStatus->delete();

        return redirect()
            ->route('health-status.index')
            ->with('success', 'Health status deleted successfully.');
    }

    /**
     * GeoJSON API.
     */
    public function apiIndex()
    {
        $data = HealthStatus::all()->map(function ($hs) {
            return [
                'type' => 'Feature',
                'geometry' => $hs->geometry ? json_decode($hs->geometry, true) : null,
                'properties' => [
                    'id'                => $hs->id,
                    'name'              => $hs->name,
                    'health_status'     => $hs->health_status,
                    'disease_cases'     => $hs->disease_cases,
                    'clinics_available' => $hs->clinics_available,
                    'land_use'          => $hs->land_use,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $data,
        ]);
    }

    /**
     * Statistics API (with filters applied).
     */
    public function stats(Request $request)
{
    $query = \App\Models\HealthStatus::query();

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
