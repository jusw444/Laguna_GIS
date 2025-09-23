<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shapefile extends Model
{
     use HasFactory;
    protected $table = 'shapefiles';
    protected $fillable = [
        'name',
        'original_name',
        'file_path',
        'type',
        'description',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function layers()
    {
        return $this->hasMany(Layer::class);
    }
}
