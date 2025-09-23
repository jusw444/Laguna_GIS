<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layer extends Model
{
     use HasFactory;
    protected $table = 'layers';
    protected $fillable = [
        'name',
        'geojson_data',
        'shapefile_id'
    ];

    public function shapefile()
    {
        return $this->belongsTo(Shapefile::class);
    }

    public function metadata()
    {
        return $this->hasOne(Metadata::class);
    }
}
