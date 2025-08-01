<?php

namespace App\Services;

use App\Models\TaxRate;
use App\Models\TaxBracket;
use App\Models\TaxDeduction;
use App\Models\EmployeeTaxInfo;
use App\Models\User;

class TaxCalculationService
{
    /**
     * Calculate comprehensive tax withholding for an employee.
     */
    public function calculateEmployeeTaxes(User $employee, $grossSalary, $payPeriod = 'monthly')
    {
        $companyId = $employee->company_id;
        $taxInfo = EmployeeTaxInfo::getOrCreateForUser($employee->id);
        
        // Calculate annual salary for tax purposes
        $annualSalary = $this->convertToAnnualSalary($grossSalary, $payPeriod);
        
        // Calculate pre-tax deductions
        $preTaxDeductions = $this->calculatePreTaxDeductions($employee, $grossSalary, $taxInfo);
        
        // Calculate taxable income
        $taxableIncome = $annualSalary - $preTaxDeductions['annual_total'];
        $adjustedTaxableIncome = $this->adjustForAllowances($taxableIncome, $taxInfo);
        
        // Calculate federal taxes
        $federalTax = $this->calculateFederalTax($companyId, $adjustedTaxableIncome, $taxInfo);
        
        // Calculate state taxes
        $stateTax = $this->calculateStateTax($companyId, $adjustedTaxableIncome, $taxInfo);
        
        // Calculate local taxes
        $localTax = $this->calculateLocalTax($companyId, $adjustedTaxableIncome, $taxInfo);
        
        // Calculate FICA taxes (Social Security & Medicare)
        $ficaTaxes = $this->calculateFICATaxes($companyId, $annualSalary);
        
        // Calculate unemployment taxes (employer only)
        $unemploymentTaxes = $this->calculateUnemploymentTaxes($companyId, $annualSalary);
        
        // Calculate post-tax deductions
        $postTaxDeductions = $this->calculatePostTaxDeductions($employee, $grossSalary, $taxInfo);
        
        // Calculate period amounts (convert from annual to pay period)
        $periodMultiplier = $this->getPeriodMultiplier($payPeriod);
        
        return [
            'gross_salary' => $grossSalary,
            'annual_salary' => $annualSalary,
            'taxable_income' => $taxableIncome,
            'adjusted_taxable_income' => $adjustedTaxableIncome,
            
            // Pre-tax deductions
            'pre_tax_deductions' => $preTaxDeductions['period_total'],
            'pre_tax_breakdown' => $preTaxDeductions['breakdown'],
            
            // Tax withholdings (employee portion)
            'federal_tax' => $federalTax / $periodMultiplier,
            'federal_tax_annual' => $federalTax,
            'state_tax' => $stateTax / $periodMultiplier,
            'state_tax_annual' => $stateTax,
            'local_tax' => $localTax / $periodMultiplier,
            'local_tax_annual' => $localTax,
            'social_security_tax' => $ficaTaxes['employee']['social_security'] / $periodMultiplier,
            'medicare_tax' => $ficaTaxes['employee']['medicare'] / $periodMultiplier,
            
            // Total employee tax withholding
            'total_tax_withholding' => ($federalTax + $stateTax + $localTax + 
                                      $ficaTaxes['employee']['social_security'] + 
                                      $ficaTaxes['employee']['medicare'] + 
                                      ($taxInfo->additional_withholding * $periodMultiplier)) / $periodMultiplier,
            
            // Additional withholding
            'additional_withholding' => $taxInfo->additional_withholding,
            
            // Post-tax deductions
            'post_tax_deductions' => $postTaxDeductions['period_total'],
            'post_tax_breakdown' => $postTaxDeductions['breakdown'],
            
            // Employer contributions
            'employer_social_security' => $ficaTaxes['employer']['social_security'] / $periodMultiplier,
            'employer_medicare' => $ficaTaxes['employer']['medicare'] / $periodMultiplier,
            'employer_unemployment' => $unemploymentTaxes['total'] / $periodMultiplier,
            'employer_total' => ($ficaTaxes['employer']['social_security'] + 
                               $ficaTaxes['employer']['medicare'] + 
                               $unemploymentTaxes['total']) / $periodMultiplier,
            
            // Net salary calculation
            'net_salary' => $grossSalary - 
                          ($preTaxDeductions['period_total'] + 
                           ($federalTax + $stateTax + $localTax + 
                            $ficaTaxes['employee']['social_security'] + 
                            $ficaTaxes['employee']['medicare']) / $periodMultiplier + 
                           $taxInfo->additional_withholding + 
                           $postTaxDeductions['period_total']),
            
            // Tax rates used
            'tax_rates' => $this->getTaxRatesUsed($companyId),
            
            // Calculation details
            'calculation_details' => [
                'pay_period' => $payPeriod,
                'period_multiplier' => $periodMultiplier,
                'filing_status' => $taxInfo->filing_status,
                'allowances' => $taxInfo->allowances,
                'tax_year' => $taxInfo->tax_year
            ]
        ];
    }
    
    /**
     * Calculate federal income tax using progressive brackets.
     */
    private function calculateFederalTax($companyId, $taxableIncome, $taxInfo)
    {
        if ($taxInfo->exempt_from_federal) {
            return 0;
        }
        
        // Use progressive tax brackets
        $federalTax = TaxBracket::calculateProgressiveTax(
            $companyId, 
            $taxableIncome, 
            $taxInfo->filing_status, 
            $taxInfo->tax_year
        );
        
        // Apply additional federal tax rates if configured
        $federalRates = TaxRate::forCompany($companyId)
            ->byType('income')
            ->employeeTaxes()
            ->active()
            ->get();
            
        foreach ($federalRates as $rate) {
            if ($rate->appliesTo($taxableIncome)) {
                $federalTax += $rate->calculateTax($taxableIncome);
            }
        }
        
        return max(0, $federalTax);
    }
    
    /**
     * Calculate state income tax.
     */
    private function calculateStateTax($companyId, $taxableIncome, $taxInfo)
    {
        if ($taxInfo->exempt_from_state) {
            return 0;
        }
        
        $stateTax = 0;
        $stateRates = TaxRate::forCompany($companyId)
            ->byType('state')
            ->employeeTaxes()
            ->active()
            ->get();
            
        foreach ($stateRates as $rate) {
            if ($rate->appliesTo($taxableIncome)) {
                $stateTax += $rate->calculateTax($taxableIncome);
            }
        }
        
        return $stateTax;
    }
    
    /**
     * Calculate local taxes.
     */
    private function calculateLocalTax($companyId, $taxableIncome, $taxInfo)
    {
        if ($taxInfo->exempt_from_local) {
            return 0;
        }
        
        $localTax = 0;
        $localRates = TaxRate::forCompany($companyId)
            ->byType('local')
            ->employeeTaxes()
            ->active()
            ->get();
            
        foreach ($localRates as $rate) {
            if ($rate->appliesTo($taxableIncome)) {
                $localTax += $rate->calculateTax($taxableIncome);
            }
        }
        
        return $localTax;
    }
    
    /**
     * Calculate FICA taxes (Social Security and Medicare).
     */
    private function calculateFICATaxes($companyId, $annualSalary)
    {
        $socialSecurityRate = TaxRate::forCompany($companyId)
            ->byType('social_security')
            ->employeeTaxes()
            ->active()
            ->first();
            
        $medicareRate = TaxRate::forCompany($companyId)
            ->byType('medicare')
            ->employeeTaxes()
            ->active()
            ->first();
        
        $socialSecurityTax = 0;
        $medicareTax = 0;
        
        if ($socialSecurityRate && $socialSecurityRate->appliesTo($annualSalary)) {
            $socialSecurityTax = $socialSecurityRate->calculateTax($annualSalary);
        }
        
        if ($medicareRate && $medicareRate->appliesTo($annualSalary)) {
            $medicareTax = $medicareRate->calculateTax($annualSalary);
        }
        
        return [
            'employee' => [
                'social_security' => $socialSecurityTax,
                'medicare' => $medicareTax,
                'total' => $socialSecurityTax + $medicareTax
            ],
            'employer' => [
                'social_security' => $socialSecurityTax, // Employer matches employee contribution
                'medicare' => $medicareTax, // Employer matches employee contribution
                'total' => $socialSecurityTax + $medicareTax
            ]
        ];
    }
    
    /**
     * Calculate unemployment taxes (employer only).
     */
    private function calculateUnemploymentTaxes($companyId, $annualSalary)
    {
        $unemploymentRates = TaxRate::forCompany($companyId)
            ->byType('unemployment')
            ->employerTaxes()
            ->active()
            ->get();
            
        $totalUnemployment = 0;
        $breakdown = [];
        
        foreach ($unemploymentRates as $rate) {
            if ($rate->appliesTo($annualSalary)) {
                $tax = $rate->calculateTax($annualSalary);
                $totalUnemployment += $tax;
                $breakdown[] = [
                    'name' => $rate->name,
                    'rate' => $rate->rate,
                    'amount' => $tax
                ];
            }
        }
        
        return [
            'total' => $totalUnemployment,
            'breakdown' => $breakdown
        ];
    }
    
    /**
     * Calculate pre-tax deductions.
     */
    private function calculatePreTaxDeductions($employee, $grossSalary, $taxInfo)
    {
        $annualSalary = $this->convertToAnnualSalary($grossSalary, 'monthly');
        $preTaxDeductions = TaxDeduction::forCompany($employee->company_id)
            ->preTax()
            ->active()
            ->get();
            
        $totalDeductions = 0;
        $breakdown = [];
        
        // Fixed deductions from tax info
        $healthInsurance = $taxInfo->health_insurance_premium ?? 0;
        $retirementContribution = $taxInfo->calculateRetirementContribution($annualSalary);
        
        if ($healthInsurance > 0) {
            $totalDeductions += $healthInsurance * 12; // Monthly to annual
            $breakdown[] = [
                'name' => 'Health Insurance Premium',
                'amount_monthly' => $healthInsurance,
                'amount_annual' => $healthInsurance * 12
            ];
        }
        
        if ($retirementContribution > 0) {
            $totalDeductions += $retirementContribution;
            $breakdown[] = [
                'name' => '401(k) Retirement Contribution',
                'amount_monthly' => $retirementContribution / 12,
                'amount_annual' => $retirementContribution
            ];
        }
        
        // Additional deductions from tax_deductions table
        foreach ($preTaxDeductions as $deduction) {
            if ($deduction->isEligible($employee)) {
                $deductionAmount = $deduction->calculateDeduction($annualSalary);
                $totalDeductions += $deductionAmount;
                $breakdown[] = [
                    'name' => $deduction->name,
                    'amount_monthly' => $deductionAmount / 12,
                    'amount_annual' => $deductionAmount
                ];
            }
        }
        
        return [
            'annual_total' => $totalDeductions,
            'period_total' => $totalDeductions / 12, // Monthly
            'breakdown' => $breakdown
        ];
    }
    
    /**
     * Calculate post-tax deductions.
     */
    private function calculatePostTaxDeductions($employee, $grossSalary, $taxInfo)
    {
        $annualSalary = $this->convertToAnnualSalary($grossSalary, 'monthly');
        $postTaxDeductions = TaxDeduction::forCompany($employee->company_id)
            ->postTax()
            ->active()
            ->get();
            
        $totalDeductions = 0;
        $breakdown = [];
        
        foreach ($postTaxDeductions as $deduction) {
            if ($deduction->isEligible($employee)) {
                $deductionAmount = $deduction->calculateDeduction($annualSalary);
                $totalDeductions += $deductionAmount;
                $breakdown[] = [
                    'name' => $deduction->name,
                    'amount_monthly' => $deductionAmount / 12,
                    'amount_annual' => $deductionAmount
                ];
            }
        }
        
        return [
            'annual_total' => $totalDeductions,
            'period_total' => $totalDeductions / 12, // Monthly
            'breakdown' => $breakdown
        ];
    }
    
    /**
     * Adjust taxable income for allowances.
     */
    private function adjustForAllowances($taxableIncome, $taxInfo)
    {
        $allowanceValue = 4700; // 2025 standard allowance value
        return max(0, $taxableIncome - ($taxInfo->allowances * $allowanceValue));
    }
    
    /**
     * Convert salary to annual amount.
     */
    private function convertToAnnualSalary($salary, $payPeriod)
    {
        return match($payPeriod) {
            'weekly' => $salary * 52,
            'bi-weekly' => $salary * 26,
            'semi-monthly' => $salary * 24,
            'monthly' => $salary * 12,
            'quarterly' => $salary * 4,
            'annually' => $salary,
            default => $salary * 12 // Default to monthly
        };
    }
    
    /**
     * Get period multiplier for converting annual to period amounts.
     */
    private function getPeriodMultiplier($payPeriod)
    {
        return match($payPeriod) {
            'weekly' => 52,
            'bi-weekly' => 26,
            'semi-monthly' => 24,
            'monthly' => 12,
            'quarterly' => 4,
            'annually' => 1,
            default => 12 // Default to monthly
        };
    }
    
    /**
     * Get tax rates used in calculation for reference.
     */
    private function getTaxRatesUsed($companyId)
    {
        return TaxRate::forCompany($companyId)
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(function($rate) {
                return [
                    'name' => $rate->name,
                    'type' => $rate->type,
                    'rate' => $rate->rate,
                    'is_employer_contribution' => $rate->is_employer_contribution
                ];
            });
    }
    
    /**
     * Calculate YTD (Year-to-Date) tax information.
     */
    public function calculateYTDTaxes(User $employee, $currentYearPayrolls)
    {
        $ytdGross = $currentYearPayrolls->sum('basic_salary');
        $ytdFederalTax = $currentYearPayrolls->sum('federal_tax');
        $ytdStateTax = $currentYearPayrolls->sum('state_tax');
        $ytdLocalTax = $currentYearPayrolls->sum('local_tax');
        $ytdSocialSecurity = $currentYearPayrolls->sum('social_security_tax');
        $ytdMedicare = $currentYearPayrolls->sum('medicare_tax');
        $ytdPreTaxDeductions = $currentYearPayrolls->sum('pre_tax_deductions');
        $ytdPostTaxDeductions = $currentYearPayrolls->sum('post_tax_deductions');
        
        return [
            'ytd_gross' => $ytdGross,
            'ytd_federal_tax' => $ytdFederalTax,
            'ytd_state_tax' => $ytdStateTax,
            'ytd_local_tax' => $ytdLocalTax,
            'ytd_social_security' => $ytdSocialSecurity,
            'ytd_medicare' => $ytdMedicare,
            'ytd_total_tax' => $ytdFederalTax + $ytdStateTax + $ytdLocalTax + $ytdSocialSecurity + $ytdMedicare,
            'ytd_pre_tax_deductions' => $ytdPreTaxDeductions,
            'ytd_post_tax_deductions' => $ytdPostTaxDeductions,
            'ytd_net' => $ytdGross - ($ytdFederalTax + $ytdStateTax + $ytdLocalTax + 
                                     $ytdSocialSecurity + $ytdMedicare + 
                                     $ytdPreTaxDeductions + $ytdPostTaxDeductions)
        ];
    }
}