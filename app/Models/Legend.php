<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legend extends Model
{
    use HasFactory;
    protected $table = 'legends';
    protected $fillable = [
        'title',
        'color',
        'type',
        'description'
    ];
}
