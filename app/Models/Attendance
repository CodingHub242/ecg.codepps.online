<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'employee_code',
        'employee_name',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'synced',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'synced' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee that this attendance record belongs to.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope: filter by date.
     */
    public function scopeByDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope: filter by employee and date range.
     */
    public function scopeByEmployeeAndRange($query, string $employeeId, string $startDate, string $endDate)
    {
        return $query->where('employee_id', $employeeId)
                     ->where('date', '>=', $startDate)
                     ->where('date', '<=', $endDate);
    }
}
