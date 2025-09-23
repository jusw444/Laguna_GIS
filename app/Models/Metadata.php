<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    use HasFactory;
    protected $table = 'metadata';
    protected $fillable = [
        'layer_id',
        'land_use',
        'ownership',
        'classification',
        'flood_risk',
        'health_status',
        'disease_cases',
        'clinics_available',
        'additional_info'
    ];

    public function layer()
    {
        return $this->belongsTo(Layer::class);
    }
}
