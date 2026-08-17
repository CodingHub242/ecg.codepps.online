<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'employee_id',
        'report_content',
        'image_path',
        'status',
    ];

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(TUser::class, 'employee_id');
    }

    // Scopes
    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Helper to get image URL
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? url('storage/' . $this->image_path) : null;
    }
}
