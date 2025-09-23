<?php

namespace App\Http\Controllers;

use App\Models\Shapefile; // Correct model name
use App\Models\Layer;     // Correct model name  
use App\Models\Metadata;  // Correct model name
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShapefileController extends Controller
{
    public function index()
    {
        $shapefiles = Shapefile::with('user', 'layers.metadata')->get();
        return view('shapefiles.index', compact('shapefiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shapefile' => 'nullable|file|mimes:zip',
            'type' => 'required|in:point,line,polygon',
            'description' => 'nullable|string'
        ]);

        try {
            if ($request->hasFile('shapefile')) {
                $file = $request->file('shapefile');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('shapefiles', $filename);
                $originalName = $file->getClientOriginalName();
            } else {
                // Use dummy values for testing
                $path = 'dummy/path/to/shapefile.zip';
                $originalName = 'dummy_shapefile.zip';
            }

            $shapefile = Shapefile::create([
                'name' => $request->name,
                'original_name' => $originalName,
                'file_path' => $path,
                'type' => $request->type,
                'description' => $request->description,
                'user_id' => auth()->id()
            ]);

            // Process sample layers/metadata
            $this->processShapefile($shapefile);

            // Redirect with success message for web requests
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Shapefile uploaded successfully',
                    'data' => $shapefile->load('layers.metadata')
                ], 201);
            }

            return redirect()->route('shapefiles.index')
                ->with('success', 'Shapefile uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Shapefile upload error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Failed to upload shapefile',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to upload shapefile: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $shapefile = Shapefile::with('layers.metadata')->findOrFail($id);
        return view('shapefiles.show', compact('shapefile'));
    }

    public function update(Request $request, $id)
    {
        $shapefile = Shapefile::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $shapefile->update($request->only(['name', 'description']));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Shapefile updated successfully',
                'data' => $shapefile
            ]);
        }

        return redirect()->route('shapefiles.index')
            ->with('success', 'Shapefile updated successfully!');
    }

    public function destroy($id)
    {
        $shapefile = Shapefile::findOrFail($id);
        
        // Delete associated file if it exists and is not a dummy path
        if ($shapefile->file_path && !str_contains($shapefile->file_path, 'dummy/')) {
            Storage::delete($shapefile->file_path);
        }
        
        $shapefile->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Shapefile deleted successfully']);
        }

        return redirect()->route('shapefiles.index')
            ->with('success', 'Shapefile deleted successfully!');
    }

    private function processShapefile(Shapefile $shapefile)
    {
        // Sample data for demonstration
        $sampleData = [
            [
                'name' => 'Residential Area 1',
                'geojson' => [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [[
                            [120.123, 14.456],
                            [120.124, 14.456], 
                            [120.124, 14.457],
                            [120.123, 14.457],
                            [120.123, 14.456]
                        ]]
                    ],
                    'properties' => [
                        'land_use' => 'Residential',
                        'ownership' => 'Private',
                        'classification' => 'Urban',
                    ]
                ]
            ],
            [
                'name' => 'Commercial District',
                'geojson' => [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [[
                            [120.125, 14.458],
                            [120.127, 14.458],
                            [120.127, 14.460],
                            [120.125, 14.460],
                            [120.125, 14.458]
                        ]]
                    ],
                    'properties' => [
                        'land_use' => 'Commercial',
                        'ownership' => 'Public',
                        'classification' => 'Business',
                    ]
                ]
            ],
            [
                'name' => 'Agricultural Zone',
                'geojson' => [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [[
                            [120.120, 14.450],
                            [120.122, 14.450],
                            [120.122, 14.452],
                            [120.120, 14.452],
                            [120.120, 14.450]
                        ]]
                    ],
                    'properties' => [
                        'land_use' => 'Agricultural',
                        'ownership' => 'Private',
                        'classification' => 'Rural',
                    ]
                ]
            ]
        ];

        foreach ($sampleData as $data) {
            $layer = Layer::create([
                'name' => $data['name'],
                'geojson_data' => json_encode($data['geojson']),
                'shapefile_id' => $shapefile->id
            ]);

            Metadata::create([
                'layer_id' => $layer->id,
                'land_use' => $data['geojson']['properties']['land_use'] ?? null,
                'ownership' => $data['geojson']['properties']['ownership'] ?? null,
                'classification' => $data['geojson']['properties']['classification'] ?? null,
                'flood_risk' => ['none', 'low', 'medium', 'high'][rand(0, 3)],
                'health_status' => ['poor', 'fair', 'good', 'excellent'][rand(0, 3)],
                'disease_cases' => rand(0, 100),
                'clinics_available' => rand(0, 5)
            ]);
        }
    }
}