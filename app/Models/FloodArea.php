<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloodArea extends Model
{
    protected $fillable = [
        'name',
        'flood_risk',
        'land_use',
        'ownership',
        'classification',
        'geometry',
    ];

    protected $casts = [
        'geometry' => 'array', // Cast geometry to array for GeoJSON
    ];
}
