<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Theft extends Model
{
    //
    use HasFactory;

     protected $fillable = [
        'fulname',
        'location',
        'items',
        'lat',
        'lng',
        'status',
        'requestID',
        'images',
        'date_stolen',
        'time_stolen',
    ];
    protected $casts = ['images' => 'array'];
}
