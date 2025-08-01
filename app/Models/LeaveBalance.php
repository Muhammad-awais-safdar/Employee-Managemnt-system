<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'pending_days',
        'carried_forward',
        'bonus_days',
        'deducted_days',
        'transaction_log',
        'last_updated',
        'is_active'
    ];

    protected $casts = [
        'allocated_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'pending_days' => 'decimal:2',
        'carried_forward' => 'decimal:2',
        'bonus_days' => 'decimal:2',
        'deducted_days' => 'decimal:2',
        'available_days' => 'decimal:2',
        'total_entitled' => 'decimal:2',  
        'transaction_log' => 'array',
        'last_updated' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($balance) {
            $balance->last_updated = now()->toDateString();
        });
    }

    /**
     * Get the user that owns the leave balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the leave balance.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave type.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Calculate available days (computed field).
     */
    public function getAvailableDaysAttribute(): float
    {
        return $this->allocated_days + $this->carried_forward + $this->bonus_days - $this->used_days - $this->deducted_days;
    }

    /**
     * Calculate total entitled days (computed field).
     */
    public function getTotalEntitledAttribute(): float
    {
        return $this->allocated_days + $this->carried_forward + $this->bonus_days - $this->deducted_days;
    }

    /**
     * Get remaining days after pending leaves.
     */
    public function getRemainingDaysAttribute(): float
    {
        return $this->available_days - $this->pending_days;
    }

    /**
     * Get usage percentage.
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->total_entitled <= 0) {
            return 0;
        }
        
        return round(($this->used_days / $this->total_entitled) * 100, 1);
    }

    /**
     * Add a transaction to the log.
     */
    public function addTransaction($type, $amount, $description, $referenceId = null)
    {
        $log = $this->transaction_log ?? [];
        
        $log[] = [
            'type' => $type, // 'allocated', 'used', 'carried_forward', 'bonus', 'deducted', 'pending'
            'amount' => $amount,
            'description' => $description,
            'reference_id' => $referenceId,
            'timestamp' => now()->toISOString(),
            'created_by' => auth()->id()
        ];
        
        $this->transaction_log = $log;
        $this->save();
    }

    /**
     * Update used days based on approved leave.
     */
    public function useLeave($days, $leaveId)
    {
        $this->used_days += $days;
        $this->pending_days = max(0, $this->pending_days - $days);
        
        $this->addTransaction('used', $days, "Leave approved and used", $leaveId);
    }

    /**
     * Add pending days for leave application.
     */
    public function addPendingLeave($days, $leaveId)
    {
        $this->pending_days += $days;
        $this->addTransaction('pending', $days, "Leave application submitted", $leaveId);
    }

    /**
     * Remove pending days when leave is rejected/cancelled.
     */
    public function removePendingLeave($days, $leaveId)
    {
        $this->pending_days = max(0, $this->pending_days - $days);
        $this->addTransaction('pending_removed', -$days, "Leave application rejected/cancelled", $leaveId);
    }

    /**
     * Add bonus days (admin action).
     */
    public function addBonusDays($days, $description)
    {
        $this->bonus_days += $days;
        $this->addTransaction('bonus', $days, $description);
    }

    /**
     * Deduct days (admin action).
     */
    public function deductDays($days, $description)
    {
        $this->deducted_days += $days;
        $this->addTransaction('deducted', $days, $description);
    }

    /**
     * Check if user has sufficient balance for leave.
     */
    public function hasSufficientBalance($requestedDays): bool
    {
        return $this->remaining_days >= $requestedDays;
    }

    /**
     * Scope for active balances.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for company-specific balances.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get or create leave balance for user, leave type, and year.
     */
    public static function getOrCreateBalance($userId, $leaveTypeId, $year = null)
    {
        $year = $year ?: date('Y');
        
        $balance = self::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
            
        if (!$balance) {
            $user = \App\Models\User::find($userId);
            $leaveType = \App\Models\LeaveType::find($leaveTypeId);
            
            $balance = self::create([
                'user_id' => $userId,
                'company_id' => $user->company_id,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
                'allocated_days' => $leaveType->max_days_per_year,
                'used_days' => 0,
                'pending_days' => 0,
                'carried_forward' => 0,
                'bonus_days' => 0,
                'deducted_days' => 0,
                'is_active' => true
            ]);
            
            $balance->addTransaction('allocated', $leaveType->max_days_per_year, "Annual allocation for {$year}");
        }
        
        return $balance;
    }

    /**
     * Initialize leave balances for a new user.
     */
    public static function initializeForUser($userId, $companyId, $year = null)
    {
        $year = $year ?: date('Y');
        
        $leaveTypes = LeaveType::forCompany($companyId)->active()->get();
        
        foreach ($leaveTypes as $leaveType) {
            self::getOrCreateBalance($userId, $leaveType->id, $year);
        }
    }

    /**
     * Carry forward unused leave to next year.
     */
    public function carryForwardToNextYear()
    {
        $nextYear = $this->year + 1;
        $leaveType = $this->leaveType;
        
        // Calculate carry forward amount
        $carryForwardDays = min(
            $this->available_days,
            $leaveType->carry_forward_limit
        );
        
        if ($carryForwardDays > 0) {
            $nextYearBalance = self::getOrCreateBalance($this->user_id, $this->leave_type_id, $nextYear);
            $nextYearBalance->carried_forward += $carryForwardDays;
            $nextYearBalance->addTransaction('carried_forward', $carryForwardDays, "Carried forward from {$this->year}");
        }
        
        return $carryForwardDays;
    }

    /**
     * Get balance summary for user.
     */
    public static function getUserSummary($userId, $year = null)
    {
        $year = $year ?: date('Y');
        
        $balances = self::with('leaveType')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->active()
            ->get();
            
        return $balances->map(function($balance) {
            return [
                'leave_type' => $balance->leaveType->name,
                'leave_type_code' => $balance->leaveType->code,
                'allocated' => $balance->allocated_days,
                'used' => $balance->used_days,
                'pending' => $balance->pending_days,
                'available' => $balance->available_days,
                'remaining' => $balance->remaining_days,
                'usage_percentage' => $balance->usage_percentage,
                'carried_forward' => $balance->carried_forward,
                'bonus' => $balance->bonus_days,
                'deducted' => $balance->deducted_days
            ];
        });
    }
}