<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryIncrementRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'requested_by',
        'company_id',
        'current_salary',
        'requested_salary',
        'increment_amount',
        'increment_percentage',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
        'effective_date',
    ];

    protected $casts = [
        'current_salary' => 'decimal:2',
        'requested_salary' => 'decimal:2',
        'increment_amount' => 'decimal:2',
        'increment_percentage' => 'decimal:2',
        'approved_at' => 'datetime',
        'effective_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
