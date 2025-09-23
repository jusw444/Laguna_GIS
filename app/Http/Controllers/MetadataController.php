<?php

namespace App\Http\Controllers;

use App\Models\Metadata;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    public function show($layerId)
    {
        $metadata = Metadata::with('layer')->where('layer_id', $layerId)->firstOrFail();
        return response()->json($metadata);
    }

    public function update(Request $request, $id)
    {
        $metadata = Metadata::findOrFail($id);
        
        $request->validate([
            'land_use' => 'nullable|string|max:255',
            'ownership' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:255',
            'flood_risk' => 'nullable|in:none,low,medium,high',
            'health_status' => 'nullable|in:poor,fair,good,excellent',
            'disease_cases' => 'nullable|integer|min:0',
            'clinics_available' => 'nullable|integer|min:0',
            'additional_info' => 'nullable|string'
        ]);

        $metadata->update($request->all());

        return response()->json([
            'message' => 'Metadata updated successfully',
            'data' => $metadata
        ]);
    }
}
