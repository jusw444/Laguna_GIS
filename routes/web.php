<?php

use App\Http\Controllers\FloodAreaController;
use App\Http\Controllers\HealthStatusController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LandUseController;
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

// Flood Area routes
Route::get('/flood-areas/stats', [FloodAreaController::class, 'stats'])->name('flood-areas.stats');
Route::get('/flood-analysis', [LandingController::class, 'flood'])->name('landing.flood');
Route::resource('flood-areas', FloodAreaController::class);

// Health Status routes
Route::get('analysis/health-status', [HealthStatusController::class, 'apiIndex'])->name('api.analysis.health-status');
Route::get('/health-status/stats', [HealthStatusController::class, 'stats'])
    ->name('health-status.stats');
Route::get('/health-analysis', [LandingController::class, 'health'])->name('landing.health');
Route::resource('health-status', HealthStatusController::class);

// Land Use routes
Route::get('/land-use/stats', [LandUseController::class, 'stats'])->name('land-use.stats');
Route::get('analysis/land-use', [LandUseController::class, 'apiIndex'])->name('land-use.api');
Route::get('/land-analysis', [LandingController::class, 'land'])->name('landing.land');
Route::resource('land-use', LandUseController::class);

require __DIR__.'/auth.php';
