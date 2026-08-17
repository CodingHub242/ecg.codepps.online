<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectricityRequest extends Model
{
    //
    use HasFactory;

     protected $fillable = [
        'fulname',
        'location',
        'lat',
        'lng',
        'status',
        'requestID',
    ];

    //protected $casts = ['images' => 'array'];
}
