<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = [
        'setting_key',
        'work_start_time',
        'work_end_time',
        'admin_password',
        'company_name',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Get the main settings record (setting_key = 'main').
     */
    public static function getMain(): ?self
    {
        return self::where('setting_key', 'main')->first();
    }
}
