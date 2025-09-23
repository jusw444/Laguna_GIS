<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use Illuminate\Http\Request;

class LayerController extends Controller
{
    public function index($shapefileId)
    {
        $layers = Layer::with('metadata')->where('shapefile_id', $shapefileId)->get();
        return response()->json($layers);
    }

    public function show($id)
    {
        $layer = Layer::with('metadata', 'shapefile')->findOrFail($id);
        return response()->json($layer);
    }

    public function update(Request $request, $id)
    {
        $layer = Layer::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $layer->update($request->only(['name']));

        return response()->json([
            'message' => 'Layer updated successfully',
            'data' => $layer
        ]);
    }

    public function destroy($id)
    {
        $layer = Layer::findOrFail($id);
        $layer->delete();

        return response()->json(['message' => 'Layer deleted successfully']);
    }
}
