<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedEmployee extends Model
{
    protected $table = 'deleted_employees';

    public $timestamps = false;

    protected $fillable = [
        'employee_code',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
