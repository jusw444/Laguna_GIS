<?php

namespace App\Http\Controllers;

use App\Models\Legend;
use Illuminate\Http\Request;

class LegendController extends Controller
{
    public function index()
    {
        $legends = Legend::all();
        return view('legends.index', compact('legends'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'color' => 'required|string|max:7', // HEX color
            'type' => 'required|in:land_use,hazard,health,other',
            'description' => 'nullable|string'
        ]);

        $legend = Legend::create($request->all());

        return response()->json([
            'message' => 'Legend created successfully',
            'data' => $legend
        ], 201);
    }

    public function show($id)
    {
        $legend = Legend::findOrFail($id);
        return response()->json($legend);
    }

    public function update(Request $request, $id)
    {
        $legend = Legend::findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:7',
            'type' => 'sometimes|required|in:land_use,hazard,health,other',
            'description' => 'nullable|string'
        ]);

        $legend->update($request->all());

        return response()->json([
            'message' => 'Legend updated successfully',
            'data' => $legend
        ]);
    }

    public function destroy($id)
    {
        $legend = Legend::findOrFail($id);
        $legend->delete();

        return redirect()->route('legends.index')->with('success', 'Legend deleted successfully');
    }
}
