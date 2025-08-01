<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'pay_period_start',
        'pay_period_end',
        'basic_salary',
        'overtime_hours',
        'overtime_rate',
        'overtime_pay',
        'bonus',
        'deductions',
        'gross_salary',
        'pre_tax_deductions',
        'federal_tax',
        'state_tax',
        'local_tax',
        'social_security_tax',
        'medicare_tax',
        'total_tax_withholding',
        'post_tax_deductions',
        'other_deductions',
        'net_salary',
        'employer_social_security',
        'employer_medicare',
        'employer_unemployment',
        'employer_benefits',
        'status',
        'processed_by',
        'processed_at',
        'payment_date',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'basic_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'pre_tax_deductions' => 'decimal:2',
        'federal_tax' => 'decimal:2',
        'state_tax' => 'decimal:2',
        'local_tax' => 'decimal:2',
        'social_security_tax' => 'decimal:2',
        'medicare_tax' => 'decimal:2',
        'total_tax_withholding' => 'decimal:2',
        'post_tax_deductions' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'employer_social_security' => 'decimal:2',
        'employer_medicare' => 'decimal:2',
        'employer_unemployment' => 'decimal:2',
        'employer_benefits' => 'decimal:2',
        'processed_at' => 'datetime',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the payroll record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the payroll record.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who processed the payroll.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for company-specific payroll records.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for specific pay period.
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->where('pay_period_start', '>=', $startDate)
                    ->where('pay_period_end', '<=', $endDate);
    }

    /**
     * Scope for pending payroll records.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for processed payroll records.
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Get the status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'processed' => 'bg-success',
            'paid' => 'bg-primary',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'processed' => 'Processed',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Calculate gross salary.
     */
    public function calculateGrossSalary(): float
    {
        return $this->basic_salary + $this->overtime_pay + $this->bonus;
    }

    /**
     * Calculate net salary.
     */
    public function calculateNetSalary(): float
    {
        $gross = $this->calculateGrossSalary();
        $totalTaxWithholding = ($this->federal_tax ?? 0) + ($this->state_tax ?? 0) + ($this->local_tax ?? 0) + 
                              ($this->social_security_tax ?? 0) + ($this->medicare_tax ?? 0);
        return $gross - ($this->pre_tax_deductions ?? 0) - $totalTaxWithholding - 
               ($this->post_tax_deductions ?? 0) - ($this->other_deductions ?? 0) - ($this->deductions ?? 0);
    }

    /**
     * Calculate total employer cost.
     */
    public function calculateEmployerCost(): float
    {
        return $this->calculateGrossSalary() + ($this->employer_social_security ?? 0) + 
               ($this->employer_medicare ?? 0) + ($this->employer_unemployment ?? 0) + ($this->employer_benefits ?? 0);
    }

    /**
     * Get total tax withholding amount.
     */
    public function getTotalTaxWithholdingAttribute(): float
    {
        return ($this->federal_tax ?? 0) + ($this->state_tax ?? 0) + ($this->local_tax ?? 0) + 
               ($this->social_security_tax ?? 0) + ($this->medicare_tax ?? 0);
    }
    
    /**
     * Get total employer tax contributions.
     */
    public function getTotalEmployerTaxAttribute(): float
    {
        return ($this->employer_social_security ?? 0) + ($this->employer_medicare ?? 0) + ($this->employer_unemployment ?? 0);
    }
    
    /**
     * Boot the model and set up event listeners.
     */ 
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($payroll) {
            // Auto-calculate gross and net salary only if not already set
            if (!$payroll->gross_salary) {
                $payroll->gross_salary = $payroll->calculateGrossSalary();
            }
            if (!$payroll->net_salary) {
                $payroll->net_salary = $payroll->calculateNetSalary();
            }
        });
    }
}