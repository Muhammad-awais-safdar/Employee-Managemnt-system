<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'rate',
        'min_income',
        'max_income',
        'fixed_amount',
        'calculation_method',
        'is_employer_contribution',
        'is_active',
        'sort_order',
        'description'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'is_employer_contribution' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the tax rate.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope for active tax rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for company-specific tax rates.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for specific tax type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for employee taxes (non-employer contributions).
     */
    public function scopeEmployeeTaxes($query)
    {
        return $query->where('is_employer_contribution', false);
    }

    /**
     * Scope for employer taxes (employer contributions).
     */
    public function scopeEmployerTaxes($query)
    {
        return $query->where('is_employer_contribution', true);
    }

    /**
     * Calculate tax amount based on income.
     */
    public function calculateTax($income)
    {
        // Check if income falls within this tax rate's range
        if ($income < $this->min_income) {
            return 0;
        }

        if ($this->max_income && $income > $this->max_income) {
            $taxableIncome = $this->max_income - $this->min_income;
        } else {
            $taxableIncome = $income - $this->min_income;
        }

        switch ($this->calculation_method) {
            case 'fixed':
                return $this->fixed_amount;
            
            case 'percentage':
                return $taxableIncome * $this->rate;
            
            case 'bracket':
                // For bracket method, calculate based on the portion of income in this bracket
                return $taxableIncome * $this->rate + $this->fixed_amount;
            
            default:
                return $taxableIncome * $this->rate;
        }
    }

    /**
     * Check if this tax rate applies to the given income.
     */
    public function appliesTo($income)
    {
        if ($income < $this->min_income) {
            return false;
        }

        if ($this->max_income && $income > $this->max_income) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted rate as percentage.
     */
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate * 100, 2) . '%';
    }

    /**
     * Get formatted min income.
     */
    public function getFormattedMinIncomeAttribute()
    {
        return '$' . number_format($this->min_income, 2);
    }

    /**
     * Get formatted max income.
     */
    public function getFormattedMaxIncomeAttribute()
    {
        return $this->max_income ? '$' . number_format($this->max_income, 2) : 'No Limit';
    }

    /**
     * Get the type label for display.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'income' => 'Income Tax',
            'social_security' => 'Social Security',
            'medicare' => 'Medicare',
            'unemployment' => 'Unemployment',
            'disability' => 'Disability',
            'state' => 'State Tax',
            'local' => 'Local Tax',
            default => ucfirst($this->type)
        };
    }

    /**
     * Get the calculation method label for display.
     */
    public function getCalculationMethodLabelAttribute()
    {
        return match($this->calculation_method) {
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount',
            'bracket' => 'Tax Bracket',
            default => ucfirst($this->calculation_method)
        };
    }

    /**
     * Get the status badge class for UI.
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_active ? 'bg-success' : 'bg-secondary';
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Default tax rates for new companies.
     */
    public static function getDefaultRates()
    {
        return [
            [
                'name' => 'Federal Income Tax',
                'type' => 'income',
                'rate' => 0.1200,
                'min_income' => 0,
                'max_income' => null,
                'calculation_method' => 'bracket',
                'is_employer_contribution' => false,
                'sort_order' => 1,
                'description' => 'Federal income tax withholding'
            ],
            [
                'name' => 'Social Security',
                'type' => 'social_security',
                'rate' => 0.0620,
                'min_income' => 0,
                'max_income' => 160200, // 2025 wage base
                'calculation_method' => 'percentage',
                'is_employer_contribution' => false,
                'sort_order' => 2,
                'description' => 'Social Security tax (employee portion)'
            ],
            [
                'name' => 'Medicare',
                'type' => 'medicare',
                'rate' => 0.0145,
                'min_income' => 0,
                'max_income' => null,
                'calculation_method' => 'percentage',
                'is_employer_contribution' => false,
                'sort_order' => 3,
                'description' => 'Medicare tax (employee portion)'
            ],
            [
                'name' => 'State Income Tax',
                'type' => 'state',
                'rate' => 0.0500,
                'min_income' => 0,
                'max_income' => null,
                'calculation_method' => 'percentage',
                'is_employer_contribution' => false,
                'sort_order' => 4,
                'description' => 'State income tax (adjust based on state)'
            ],
            // Employer contributions
            [
                'name' => 'Social Security (Employer)',
                'type' => 'social_security',
                'rate' => 0.0620,
                'min_income' => 0,
                'max_income' => 160200,
                'calculation_method' => 'percentage',
                'is_employer_contribution' => true,
                'sort_order' => 5,
                'description' => 'Social Security tax (employer portion)'
            ],
            [
                'name' => 'Medicare (Employer)',
                'type' => 'medicare',
                'rate' => 0.0145,
                'min_income' => 0,
                'max_income' => null,
                'calculation_method' => 'percentage',
                'is_employer_contribution' => true,
                'sort_order' => 6,
                'description' => 'Medicare tax (employer portion)'
            ],
            [
                'name' => 'Federal Unemployment (FUTA)',
                'type' => 'unemployment',
                'rate' => 0.0060,
                'min_income' => 0,
                'max_income' => 7000,
                'calculation_method' => 'percentage',
                'is_employer_contribution' => true,
                'sort_order' => 7,
                'description' => 'Federal unemployment tax (employer only)'
            ],
            [
                'name' => 'State Unemployment (SUTA)',
                'type' => 'unemployment',
                'rate' => 0.0270,
                'min_income' => 0,
                'max_income' => 9000,
                'calculation_method' => 'percentage',
                'is_employer_contribution' => true,
                'sort_order' => 8,
                'description' => 'State unemployment tax (employer only)'
            ]
        ];
    }
}