<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'tusers';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'device_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'admin_id');
    }

    public function taskReports()
    {
        return $this->hasMany(TaskReport::class, 'employee_id');
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }
}
