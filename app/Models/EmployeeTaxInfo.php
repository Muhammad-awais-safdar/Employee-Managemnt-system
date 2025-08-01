<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTaxInfo extends Model
{
    use HasFactory;

    protected $table = 'employee_tax_info';

    protected $fillable = [
        'user_id',
        'company_id',
        'filing_status',
        'allowances',
        'additional_withholding',
        'exempt_from_federal',
        'exempt_from_state',
        'exempt_from_local',
        'ssn',
        'tax_id',
        'state_tax_id',
        'tax_address',
        'tax_city',
        'tax_state',
        'tax_zip',
        'pre_tax_deductions',
        'post_tax_deductions',
        'health_insurance_premium',
        'retirement_contribution',
        'retirement_contribution_percent',
        'tax_year',
        'is_active',
        'effective_date'
    ];

    protected $casts = [
        'allowances' => 'integer',
        'additional_withholding' => 'decimal:2',
        'exempt_from_federal' => 'boolean',
        'exempt_from_state' => 'boolean',
        'exempt_from_local' => 'boolean',
        'pre_tax_deductions' => 'array',
        'post_tax_deductions' => 'array',
        'health_insurance_premium' => 'decimal:2',
        'retirement_contribution' => 'decimal:2',
        'retirement_contribution_percent' => 'decimal:2',
        'tax_year' => 'integer',
        'is_active' => 'boolean',
        'effective_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the tax info.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the tax info.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope for active tax info.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for company-specific tax info.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for specific tax year.
     */
    public function scopeForTaxYear($query, $taxYear)
    {
        return $query->where('tax_year', $taxYear);
    }

    /**
     * Get or create tax info for user.
     */
    public static function getOrCreateForUser($userId, $taxYear = null)
    {
        $taxYear = $taxYear ?? date('Y');
        $user = User::find($userId);
        
        return self::firstOrCreate([
            'user_id' => $userId,
            'tax_year' => $taxYear
        ], [
            'company_id' => $user->company_id,
            'filing_status' => 'single',
            'allowances' => 0,
            'additional_withholding' => 0.00,
            'exempt_from_federal' => false,
            'exempt_from_state' => false,
            'exempt_from_local' => false,
            'is_active' => true,
            'effective_date' => now()
        ]);
    }

    /**
     * Calculate federal withholding allowance.
     */
    public function calculateFederalAllowance()
    {
        // 2025 allowance value (this should be configurable)
        $allowanceValue = 4700; // Approximate value per allowance
        return $this->allowances * $allowanceValue;
    }

    /**
     * Get total pre-tax deductions amount.
     */
    public function getTotalPreTaxDeductions()
    {
        $total = 0;
        
        if ($this->pre_tax_deductions) {
            foreach ($this->pre_tax_deductions as $deductionId => $amount) {
                $total += $amount;
            }
        }
        
        // Add fixed deductions
        $total += $this->health_insurance_premium;
        $total += $this->retirement_contribution;
        
        return $total;
    }

    /**
     * Get total post-tax deductions amount.
     */
    public function getTotalPostTaxDeductions()
    {
        $total = 0;
        
        if ($this->post_tax_deductions) {
            foreach ($this->post_tax_deductions as $deductionId => $amount) {
                $total += $amount;
            }
        }
        
        return $total;
    }

    /**
     * Calculate retirement contribution based on salary.
     */
    public function calculateRetirementContribution($salary)
    {
        $percentageContribution = 0;
        if ($this->retirement_contribution_percent > 0) {
            $percentageContribution = $salary * ($this->retirement_contribution_percent / 100);
        }
        
        return $this->retirement_contribution + $percentageContribution;
    }

    /**
     * Get filing status label for display.
     */
    public function getFilingStatusLabelAttribute()
    {
        return match($this->filing_status) {
            'single' => 'Single',
            'married_jointly' => 'Married Filing Jointly',
            'married_separately' => 'Married Filing Separately',
            'head_of_household' => 'Head of Household',
            default => ucfirst(str_replace('_', ' ', $this->filing_status))
        };
    }

    /**
     * Get exemption status summary.
     */
    public function getExemptionStatusAttribute()
    {
        $exemptions = [];
        if ($this->exempt_from_federal) $exemptions[] = 'Federal';
        if ($this->exempt_from_state) $exemptions[] = 'State';
        if ($this->exempt_from_local) $exemptions[] = 'Local';
        
        return empty($exemptions) ? 'None' : implode(', ', $exemptions);
    }

    /**
     * Get formatted SSN (masked for security).
     */
    public function getMaskedSsnAttribute()
    {
        if (!$this->ssn) {
            return 'Not Provided';
        }
        
        return 'XXX-XX-' . substr($this->ssn, -4);
    }

    /**
     * Get tax address formatted.
     */
    public function getFormattedTaxAddressAttribute()
    {
        $address = [];
        if ($this->tax_address) $address[] = $this->tax_address;
        if ($this->tax_city) $address[] = $this->tax_city;
        if ($this->tax_state) $address[] = $this->tax_state;
        if ($this->tax_zip) $address[] = $this->tax_zip;
        
        return implode(', ', $address);
    }

    /**
     * Check if W-4 information is complete.
     */
    public function isW4Complete()
    {
        return !empty($this->filing_status) && 
               $this->allowances >= 0 && 
               !is_null($this->exempt_from_federal);
    }

    /**
     * Get W-4 completion status for display.
     */
    public function getW4StatusAttribute()
    {
        return $this->isW4Complete() ? 'Complete' : 'Incomplete';
    }

    /**
     * Get W-4 status badge class.
     */
    public function getW4StatusBadgeClassAttribute()
    {
        return $this->isW4Complete() ? 'bg-success' : 'bg-warning';
    }

    /**
     * Calculate estimated annual tax withholding.
     */
    public function calculateEstimatedAnnualWithholding($annualSalary)
    {
        // This is a simplified calculation - you might want to implement more complex logic
        $federalWithholding = 0;
        $stateWithholding = 0;
        $localWithholding = 0;
        
        if (!$this->exempt_from_federal) {
            // Simplified federal calculation
            $taxableIncome = $annualSalary - $this->calculateFederalAllowance() - $this->getTotalPreTaxDeductions();
            $federalWithholding = TaxBracket::calculateProgressiveTax(
                $this->company_id, 
                $taxableIncome, 
                $this->filing_status, 
                $this->tax_year
            );
        }
        
        if (!$this->exempt_from_state) {
            // Simplified state calculation (you'd implement state-specific logic)
            $stateWithholding = $annualSalary * 0.05; // Example 5% state tax
        }
        
        return [
            'federal' => $federalWithholding + $this->additional_withholding * 12, // Monthly additional * 12
            'state' => $stateWithholding,
            'local' => $localWithholding,
            'total' => $federalWithholding + $stateWithholding + $localWithholding + ($this->additional_withholding * 12)
        ];
    }
}