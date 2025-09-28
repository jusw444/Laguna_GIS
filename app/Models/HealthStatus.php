<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthStatus extends Model
{
    protected $fillable = [
        'name',
        'health_status',
        'disease_cases',
        'clinics_available',
        'land_use',
        'geometry',
    ];

    protected $casts = [
        'geometry' => 'array',
    ];
}
