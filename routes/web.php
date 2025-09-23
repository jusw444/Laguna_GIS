<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\LegendController;
use App\Http\Controllers\MetadataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShapefileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Shapefile routes
Route::get('/shapefiles', [ShapefileController::class, 'index'])->name('shapefiles.index');
Route::get('/shapefiles/{id}', [ShapefileController::class, 'show'])->name('shapefiles.show');
Route::post('/shapefiles', [ShapefileController::class, 'store'])->name('shapefiles.store');
Route::put('/shapefiles/{id}', [ShapefileController::class, 'update'])->name('shapefiles.update');
Route::patch('/shapefiles/{id}', [ShapefileController::class, 'update'])->name('shapefiles.patch');
Route::delete('/shapefiles/{id}', [ShapefileController::class, 'destroy'])->name('shapefiles.destroy');

// Layer routes
Route::get('shapefiles/{shapefile}/layers', [LayerController::class, 'index'])->name('shapefiles.layers.index');
Route::get('layers/{layer}', [LayerController::class, 'show'])->name('layers.show');
Route::put('layers/{layer}', [LayerController::class, 'update'])->name('layers.update');
Route::delete('layers/{layer}', [LayerController::class, 'destroy'])->name('layers.destroy');

// Metadata routes
Route::get('layers/{layer}/metadata', [MetadataController::class, 'show'])->name('layers.metadata.show');
Route::put('metadata/{metadata}', [MetadataController::class, 'update'])->name('metadata.update');

// Legend routes
Route::get('/legends', [LegendController::class, 'index'])->name('legends.index');
Route::post('/legends', [LegendController::class, 'store'])->name('legends.store');
Route::get('/legends/{id}', [LegendController::class, 'show'])->name('legends.show');
Route::put('/legends/{id}', [LegendController::class, 'update'])->name('legends.update');
Route::patch('/legends/{id}', [LegendController::class, 'update'])->name('legends.patch');
Route::delete('/legends/{id}', [LegendController::class, 'destroy'])->name('legends.destroy');

// Analysis routes
Route::get('analysis/flood-areas', [AnalysisController::class, 'floodAreas'])->name('analysis.flood-areas');
Route::get('analysis/health-status', [AnalysisController::class, 'healthStatus'])->name('analysis.health-status');
Route::get('analysis/land-use', [AnalysisController::class, 'landUseAnalysis'])->name('analysis.land-use');

require __DIR__.'/auth.php';
