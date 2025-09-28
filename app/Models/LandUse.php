<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandUse extends Model
{
    protected $fillable = [
        'name',
        'land_use',
        'ownership',
        'classification',
        'flood_risk',
        'geometry',
    ];

    protected $casts = [
        'geometry' => 'array', // Cast geometry to array for GeoJSON
    ];
}
