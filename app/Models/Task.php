<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'employee_id',
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'sms_sent',
        'pending_approval',
        'approved_by_admin',
        'denial_reason',
        'completed_at',
    ];
    
// variables    :root {
//  background:
//     radial-gradient(
//       circle at top left,
//       rgba(155,109,255,0.15),
//       transparent 40%
//     ),
//     radial-gradient(
//       circle at bottom right,
//       rgba(255,77,210,0.15),
//       transparent 40%
//     ),
//     radial-gradient(
//       circle at center,
//       rgba(91,108,255,0.12),
//       transparent 50%
//     ),
//     #0f1029;

    protected $casts = [
        'due_date' => 'date',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    // Relationships
    public function admin(): BelongsTo
    {
        return $this->belongsTo(TUser::class, 'admin_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(TUser::class, 'employee_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(TaskReport::class);
    }

    // Scopes
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('due_date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
