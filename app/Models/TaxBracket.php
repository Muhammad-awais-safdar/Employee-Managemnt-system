<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'tax_year',
        'filing_status',
        'min_income',
        'max_income',
        'tax_rate',
        'base_tax',
        'is_active'
    ];

    protected $casts = [
        'tax_year' => 'integer',
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'base_tax' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the tax bracket.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope for active tax brackets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for company-specific tax brackets.
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
     * Scope for specific filing status.
     */
    public function scopeForFilingStatus($query, $filingStatus)
    {
        return $query->where('filing_status', $filingStatus);
    }

    /**
     * Calculate tax for given income using this bracket.
     */
    public function calculateTax($income)
    {
        if ($income < $this->min_income) {
            return 0;
        }

        $taxableIncome = $income;
        if ($this->max_income && $income > $this->max_income) {
            $taxableIncome = $this->max_income;
        }

        $taxableAmount = $taxableIncome - $this->min_income;
        return $this->base_tax + ($taxableAmount * $this->tax_rate);
    }

    /**
     * Check if this bracket applies to the given income.
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
     * Get formatted tax rate as percentage.
     */
    public function getFormattedTaxRateAttribute()
    {
        return number_format($this->tax_rate * 100, 2) . '%';
    }

    /**
     * Get formatted min income.
     */
    public function getFormattedMinIncomeAttribute()
    {
        return '$' . number_format($this->min_income, 0);
    }

    /**
     * Get formatted max income.
     */
    public function getFormattedMaxIncomeAttribute()
    {
        return $this->max_income ? '$' . number_format($this->max_income, 0) : 'No Limit';
    }

    /**
     * Get formatted base tax.
     */
    public function getFormattedBaseTaxAttribute()
    {
        return '$' . number_format($this->base_tax, 2);
    }

    /**
     * Get the filing status label for display.
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
     * Get income range display.
     */
    public function getIncomeRangeAttribute()
    {
        if ($this->max_income) {
            return $this->formatted_min_income . ' - ' . $this->formatted_max_income;
        }
        return $this->formatted_min_income . ' and above';
    }

    /**
     * Calculate progressive tax using all applicable brackets.
     */
    public static function calculateProgressiveTax($companyId, $income, $filingStatus = 'single', $taxYear = null)
    {
        $taxYear = $taxYear ?? date('Y');
        
        $brackets = self::forCompany($companyId)
            ->forTaxYear($taxYear)
            ->forFilingStatus($filingStatus)
            ->active()
            ->orderBy('min_income')
            ->get();

        $totalTax = 0;
        $remainingIncome = $income;

        foreach ($brackets as $bracket) {
            if ($remainingIncome <= 0) {
                break;
            }

            if ($remainingIncome > $bracket->min_income) {
                $taxableInThisBracket = min(
                    $remainingIncome - $bracket->min_income,
                    $bracket->max_income ? $bracket->max_income - $bracket->min_income : $remainingIncome
                );

                if ($taxableInThisBracket > 0) {
                    $totalTax += $taxableInThisBracket * $bracket->tax_rate;
                }
            }
        }

        return $totalTax;
    }

    /**
     * Get default tax brackets for 2025.
     */
    public static function getDefault2025Brackets()
    {
        return [
            // Single Filing Status
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 0,
                'max_income' => 11000,
                'tax_rate' => 0.10,
                'base_tax' => 0
            ],
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 11000,
                'max_income' => 44725,
                'tax_rate' => 0.12,
                'base_tax' => 1100
            ],
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 44725,
                'max_income' => 95375,
                'tax_rate' => 0.22,
                'base_tax' => 5147
            ],
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 95375,
                'max_income' => 182050,
                'tax_rate' => 0.24,
                'base_tax' => 16290
            ],
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 182050,
                'max_income' => 231250,
                'tax_rate' => 0.32,
                'base_tax' => 37104
            ],
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 231250,
                'max_income' => 578125,
                'tax_rate' => 0.35,
                'base_tax' => 52832
            ],
            [
                'name' => '2025 Federal Tax Brackets - Single',
                'tax_year' => 2025,
                'filing_status' => 'single',
                'min_income' => 578125,
                'max_income' => null,
                'tax_rate' => 0.37,
                'base_tax' => 174238
            ],
            
            // Married Filing Jointly
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 0,
                'max_income' => 22000,
                'tax_rate' => 0.10,
                'base_tax' => 0
            ],
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 22000,
                'max_income' => 89450,
                'tax_rate' => 0.12,
                'base_tax' => 2200
            ],
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 89450,
                'max_income' => 190750,
                'tax_rate' => 0.22,
                'base_tax' => 10294
            ],
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 190750,
                'max_income' => 364200,
                'tax_rate' => 0.24,
                'base_tax' => 32580
            ],
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 364200,
                'max_income' => 462500,
                'tax_rate' => 0.32,
                'base_tax' => 74208
            ],
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 462500,
                'max_income' => 693750,
                'tax_rate' => 0.35,
                'base_tax' => 105664
            ],
            [
                'name' => '2025 Federal Tax Brackets - Married Filing Jointly',
                'tax_year' => 2025,
                'filing_status' => 'married_jointly',
                'min_income' => 693750,
                'max_income' => null,
                'tax_rate' => 0.37,
                'base_tax' => 186601
            ]
        ];
    }
}