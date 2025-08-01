<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SalaryHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'company_id',
        'changed_by',
        'old_salary',
        'new_salary',
        'change_amount',
        'change_percentage',
        'change_type',
        'reason',
        'notes',
        'effective_date',
        'increment_request_id',
        'payroll_record_id',
        'metadata',
    ];

    protected $casts = [
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'change_percentage' => 'decimal:4',
        'effective_date' => 'date',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function incrementRequest(): BelongsTo
    {
        return $this->belongsTo(SalaryIncrementRequest::class);
    }

    public function payrollRecord(): BelongsTo
    {
        return $this->belongsTo(PayrollRecord::class);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByChangeType($query, $changeType)
    {
        return $query->where('change_type', $changeType);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    public function getIsIncreaseAttribute(): bool
    {
        return $this->change_amount > 0;
    }

    public function getIsDecreaseAttribute(): bool
    {
        return $this->change_amount < 0;
    }

    public function getFormattedChangeAmountAttribute(): string
    {
        $prefix = $this->change_amount >= 0 ? '+' : '';
        return $prefix . number_format($this->change_amount, 2);
    }

    public function getFormattedChangePercentageAttribute(): string
    {
        if (!$this->change_percentage) {
            return 'N/A';
        }
        
        $prefix = $this->change_percentage >= 0 ? '+' : '';
        return $prefix . number_format($this->change_percentage, 2) . '%';
    }

    public static function createFromSalaryChange(
        User $employee,
        ?float $oldSalary,
        float $newSalary,
        User $changedBy,
        string $changeType = 'direct_update',
        ?string $reason = null,
        ?string $notes = null,
        ?Carbon $effectiveDate = null,
        ?int $incrementRequestId = null,
        ?int $payrollRecordId = null,
        ?array $metadata = null
    ): self {
        $changeAmount = $newSalary - ($oldSalary ?? 0);
        $changePercentage = $oldSalary > 0 ? (($changeAmount / $oldSalary) * 100) : null;

        return self::create([
            'employee_id' => $employee->id,
            'company_id' => $employee->company_id,
            'changed_by' => $changedBy->id,
            'old_salary' => $oldSalary,
            'new_salary' => $newSalary,
            'change_amount' => $changeAmount,
            'change_percentage' => $changePercentage,
            'change_type' => $changeType,
            'reason' => $reason,
            'notes' => $notes,
            'effective_date' => $effectiveDate ?? now()->toDateString(),
            'increment_request_id' => $incrementRequestId,
            'payroll_record_id' => $payrollRecordId,
            'metadata' => $metadata,
        ]);
    }
}