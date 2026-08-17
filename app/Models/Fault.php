<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fault extends Model
{
    //
    use HasFactory;

     protected $fillable = [
        'fulname',
        'location',
        'fault',
        'lat',
        'requestID',
        'lng',
        'status',
        'images',
    ];
     protected $casts = ['images' => 'array'];
}
