<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'calculation_method',
        'amount',
        'max_amount',
        'min_amount',
        'is_taxable',
        'applies_to_federal',
        'applies_to_state',
        'applies_to_local',
        'is_active',
        'description',
        'eligibility_criteria'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_taxable' => 'boolean',
        'applies_to_federal' => 'boolean',
        'applies_to_state' => 'boolean',
        'applies_to_local' => 'boolean',
        'is_active' => 'boolean',
        'eligibility_criteria' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the tax deduction.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope for active tax deductions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for company-specific tax deductions.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for specific deduction type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for pre-tax deductions.
     */
    public function scopePreTax($query)
    {
        return $query->where('type', 'pre_tax');
    }

    /**
     * Scope for post-tax deductions.
     */
    public function scopePostTax($query)
    {
        return $query->where('type', 'post_tax');
    }

    /**
     * Scope for federal applicable deductions.
     */
    public function scopeFederalApplicable($query)
    {
        return $query->where('applies_to_federal', true);
    }

    /**
     * Scope for state applicable deductions.
     */
    public function scopeStateApplicable($query)
    {
        return $query->where('applies_to_state', true);
    }

    /**
     * Calculate deduction amount based on salary.
     */
    public function calculateDeduction($salary, $customAmount = null)
    {
        $amount = $customAmount ?? $this->amount;

        switch ($this->calculation_method) {
            case 'fixed':
                $deduction = $amount;
                break;
            
            case 'percentage':
                $deduction = $salary * ($amount / 100);
                break;
            
            case 'formula':
                // For complex formulas, you might want to implement specific logic
                $deduction = $this->calculateFormulaDeduction($salary, $amount);
                break;
            
            default:
                $deduction = $amount;
        }

        // Apply min/max limits
        if ($this->min_amount && $deduction < $this->min_amount) {
            $deduction = $this->min_amount;
        }

        if ($this->max_amount && $deduction > $this->max_amount) {
            $deduction = $this->max_amount;
        }

        return $deduction;
    }

    /**
     * Calculate formula-based deduction (override in specific implementations).
     */
    protected function calculateFormulaDeduction($salary, $amount)
    {
        // Default implementation - can be overridden for specific formulas
        return $amount;
    }

    /**
     * Check if employee is eligible for this deduction.
     */
    public function isEligible($employee)
    {
        if (!$this->eligibility_criteria) {
            return true;
        }

        foreach ($this->eligibility_criteria as $criterion => $value) {
            switch ($criterion) {
                case 'min_salary':
                    if ($employee->salary < $value) {
                        return false;
                    }
                    break;
                
                case 'max_salary':
                    if ($employee->salary > $value) {
                        return false;
                    }
                    break;
                
                case 'roles':
                    if (!$employee->hasAnyRole($value)) {
                        return false;
                    }
                    break;
                
                case 'departments':
                    if (!in_array($employee->department_id, $value)) {
                        return false;
                    }
                    break;
                
                case 'employment_status':
                    // Add employment status check if needed
                    break;
            }
        }

        return true;
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        if ($this->calculation_method === 'percentage') {
            return number_format($this->amount, 2) . '%';
        }
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get the type label for display.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'standard' => 'Standard Deduction',
            'itemized' => 'Itemized Deduction',
            'pre_tax' => 'Pre-Tax Deduction',
            'post_tax' => 'Post-Tax Deduction',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    /**
     * Get the calculation method label for display.
     */
    public function getCalculationMethodLabelAttribute()
    {
        return match($this->calculation_method) {
            'percentage' => 'Percentage of Salary',
            'fixed' => 'Fixed Amount',
            'formula' => 'Custom Formula',
            default => ucfirst($this->calculation_method)
        };
    }

    /**
     * Get applicable tax levels.
     */
    public function getApplicableTaxLevelsAttribute()
    {
        $levels = [];
        if ($this->applies_to_federal) $levels[] = 'Federal';
        if ($this->applies_to_state) $levels[] = 'State';
        if ($this->applies_to_local) $levels[] = 'Local';
        return implode(', ', $levels);
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
     * Default deductions for new companies.
     */
    public static function getDefaultDeductions()
    {
        return [
            [
                'name' => 'Health Insurance Premium',
                'type' => 'pre_tax',
                'calculation_method' => 'fixed',
                'amount' => 150.00,
                'max_amount' => 500.00,
                'min_amount' => 0.00,
                'is_taxable' => false,
                'applies_to_federal' => true,
                'applies_to_state' => true,
                'applies_to_local' => true,
                'description' => 'Employee health insurance premium contribution'
            ],
            [
                'name' => 'Dental Insurance Premium',
                'type' => 'pre_tax',
                'calculation_method' => 'fixed',
                'amount' => 25.00,
                'max_amount' => 100.00,
                'min_amount' => 0.00,
                'is_taxable' => false,
                'applies_to_federal' => true,
                'applies_to_state' => true,
                'applies_to_local' => true,
                'description' => 'Employee dental insurance premium contribution'
            ],
            [
                'name' => 'Vision Insurance Premium',
                'type' => 'pre_tax',
                'calculation_method' => 'fixed',
                'amount' => 10.00,
                'max_amount' => 50.00,
                'min_amount' => 0.00,
                'is_taxable' => false,
                'applies_to_federal' => true,
                'applies_to_state' => true,
                'applies_to_local' => true,
                'description' => 'Employee vision insurance premium contribution'
            ],
            [
                'name' => '401(k) Contribution',
                'type' => 'pre_tax',
                'calculation_method' => 'percentage',
                'amount' => 5.00, // 5% of salary
                'max_amount' => 23000.00, // 2025 401(k) limit
                'min_amount' => 0.00,
                'is_taxable' => false,
                'applies_to_federal' => true,
                'applies_to_state' => true,
                'applies_to_local' => true,
                'description' => 'Employee 401(k) retirement contribution'
            ],
            [
                'name' => 'Flexible Spending Account (FSA)',
                'type' => 'pre_tax',
                'calculation_method' => 'fixed',
                'amount' => 100.00,
                'max_amount' => 3200.00, // 2025 FSA limit
                'min_amount' => 0.00,
                'is_taxable' => false,
                'applies_to_federal' => true,
                'applies_to_state' => true,
                'applies_to_local' => true,
                'description' => 'Flexible Spending Account contribution'
            ],
            [
                'name' => 'Commuter Benefits',
                'type' => 'pre_tax',
                'calculation_method' => 'fixed',
                'amount' => 50.00,
                'max_amount' => 300.00, // Monthly limit
                'min_amount' => 0.00,
                'is_taxable' => false,
                'applies_to_federal' => true,
                'applies_to_state' => true,
                'applies_to_local' => true,
                'description' => 'Commuter benefits (transit/parking)'
            ],
            [
                'name' => 'Life Insurance Premium',
                'type' => 'post_tax',
                'calculation_method' => 'fixed',
                'amount' => 15.00,
                'max_amount' => 100.00,
                'min_amount' => 0.00,
                'is_taxable' => true,
                'applies_to_federal' => false,
                'applies_to_state' => false,
                'applies_to_local' => false,
                'description' => 'Supplemental life insurance premium'
            ],
            [
                'name' => 'Union Dues',
                'type' => 'post_tax',
                'calculation_method' => 'fixed',
                'amount' => 50.00,
                'max_amount' => null,
                'min_amount' => 0.00,
                'is_taxable' => true,
                'applies_to_federal' => false,
                'applies_to_state' => false,
                'applies_to_local' => false,
                'description' => 'Union membership dues'
            ]
        ];
    }
}