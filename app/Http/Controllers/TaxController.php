<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use App\Models\TaxBracket;
use App\Models\TaxDeduction;
use App\Models\EmployeeTaxInfo;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    /**
     * Display tax management dashboard.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TaxRate::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        // Get tax rates
        $taxRates = TaxRate::forCompany($companyId)->active()->orderBy('sort_order')->get();
        
        // Get tax brackets
        $taxBrackets = TaxBracket::forCompany($companyId)
            ->forTaxYear(date('Y'))
            ->active()
            ->orderBy('filing_status')
            ->orderBy('min_income')
            ->get();
        
        // Get tax deductions
        $taxDeductions = TaxDeduction::forCompany($companyId)->active()->get();
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.tax.dashboard', compact(
            'taxRates',
            'taxBrackets', 
            'taxDeductions',
            'companies',
            'companyId'
        ));
    }

    /**
     * Tax rates management.
     */
    public function taxRates(Request $request)
    {
        $this->authorize('viewAny', TaxRate::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $taxRates = TaxRate::forCompany($companyId)->orderBy('sort_order')->paginate(15);
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.tax.rates.index', compact(
            'taxRates',
            'companies',
            'companyId'
        ));
    }

    /**
     * Store new tax rate.
     */
    public function storeTaxRate(Request $request)
    {
        $this->authorize('create', TaxRate::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,social_security,medicare,unemployment,disability,state,local',
            'rate' => 'required|numeric|min:0|max:1',
            'min_income' => 'required|numeric|min:0',
            'max_income' => 'nullable|numeric|min:0|gt:min_income',
            'fixed_amount' => 'nullable|numeric|min:0',
            'calculation_method' => 'required|in:percentage,fixed,bracket',
            'is_employer_contribution' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? $request->company_id : $user->company_id;
            
            $taxRate = TaxRate::create([
                'company_id' => $companyId,
                'name' => $request->name,
                'type' => $request->type,
                'rate' => $request->rate,
                'min_income' => $request->min_income,
                'max_income' => $request->max_income,
                'fixed_amount' => $request->fixed_amount ?? 0,
                'calculation_method' => $request->calculation_method,
                'is_employer_contribution' => $request->boolean('is_employer_contribution'),
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => TaxRate::forCompany($companyId)->max('sort_order') + 1,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax rate created successfully!',
                'tax_rate' => $taxRate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tax rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tax rate.
     */
    public function updateTaxRate(Request $request, TaxRate $taxRate)
    {
        $this->authorize('update', $taxRate);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,social_security,medicare,unemployment,disability,state,local',
            'rate' => 'required|numeric|min:0|max:1',
            'min_income' => 'required|numeric|min:0',
            'max_income' => 'nullable|numeric|min:0|gt:min_income',
            'fixed_amount' => 'nullable|numeric|min:0',
            'calculation_method' => 'required|in:percentage,fixed,bracket',
            'is_employer_contribution' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $taxRate->update([
                'name' => $request->name,
                'type' => $request->type,
                'rate' => $request->rate,
                'min_income' => $request->min_income,
                'max_income' => $request->max_income,
                'fixed_amount' => $request->fixed_amount ?? 0,
                'calculation_method' => $request->calculation_method,
                'is_employer_contribution' => $request->boolean('is_employer_contribution'),
                'is_active' => $request->boolean('is_active', true),
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax rate updated successfully!',
                'tax_rate' => $taxRate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete tax rate.
     */
    public function destroyTaxRate(TaxRate $taxRate)
    {
        $this->authorize('delete', $taxRate);
        
        try {
            $taxRate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tax rate deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tax rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tax brackets management.
     */
    public function taxBrackets(Request $request)
    {
        $this->authorize('viewAny', TaxBracket::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        $taxYear = $request->get('tax_year', date('Y'));
        
        $taxBrackets = TaxBracket::forCompany($companyId)
            ->forTaxYear($taxYear)
            ->orderBy('filing_status')
            ->orderBy('min_income')
            ->paginate(15);
            
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.tax.brackets.index', compact(
            'taxBrackets',
            'companies',
            'companyId',
            'taxYear'
        ));
    }

    /**
     * Store new tax bracket.
     */
    public function storeTaxBracket(Request $request)
    {
        $this->authorize('create', TaxBracket::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_year' => 'required|integer|min:2020|max:2030',
            'filing_status' => 'required|in:single,married_jointly,married_separately,head_of_household',
            'min_income' => 'required|numeric|min:0',
            'max_income' => 'nullable|numeric|min:0|gt:min_income',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'base_tax' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? $request->company_id : $user->company_id;
            
            $taxBracket = TaxBracket::create([
                'company_id' => $companyId,
                'name' => $request->name,
                'tax_year' => $request->tax_year,
                'filing_status' => $request->filing_status,
                'min_income' => $request->min_income,
                'max_income' => $request->max_income,
                'tax_rate' => $request->tax_rate,
                'base_tax' => $request->base_tax,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax bracket created successfully!',
                'tax_bracket' => $taxBracket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tax bracket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tax bracket.
     */
    public function updateTaxBracket(Request $request, TaxBracket $taxBracket)
    {
        $this->authorize('update', $taxBracket);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_year' => 'required|integer|min:2020|max:2030',
            'filing_status' => 'required|in:single,married_jointly,married_separately,head_of_household',
            'min_income' => 'required|numeric|min:0',
            'max_income' => 'nullable|numeric|min:0|gt:min_income',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'base_tax' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        try {
            $taxBracket->update([
                'name' => $request->name,
                'tax_year' => $request->tax_year,
                'filing_status' => $request->filing_status,
                'min_income' => $request->min_income,
                'max_income' => $request->max_income,
                'tax_rate' => $request->tax_rate,
                'base_tax' => $request->base_tax,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax bracket updated successfully!',
                'tax_bracket' => $taxBracket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax bracket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete tax bracket.
     */
    public function destroyTaxBracket(TaxBracket $taxBracket)
    {
        $this->authorize('delete', $taxBracket);
        
        try {
            $taxBracket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tax bracket deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tax bracket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tax deductions management.
     */
    public function taxDeductions(Request $request)
    {
        $this->authorize('viewAny', TaxDeduction::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $taxDeductions = TaxDeduction::forCompany($companyId)->paginate(15);
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.tax.deductions.index', compact(
            'taxDeductions',
            'companies',
            'companyId'
        ));
    }

    /**
     * Store new tax deduction.
     */
    public function storeTaxDeduction(Request $request)
    {
        $this->authorize('create', TaxDeduction::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:standard,itemized,pre_tax,post_tax',
            'calculation_method' => 'required|in:percentage,fixed,formula',
            'amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'is_taxable' => 'boolean',
            'applies_to_federal' => 'boolean',
            'applies_to_state' => 'boolean',
            'applies_to_local' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? $request->company_id : $user->company_id;
            
            $taxDeduction = TaxDeduction::create([
                'company_id' => $companyId,
                'name' => $request->name,
                'type' => $request->type,
                'calculation_method' => $request->calculation_method,
                'amount' => $request->amount,
                'max_amount' => $request->max_amount,
                'min_amount' => $request->min_amount ?? 0,
                'is_taxable' => $request->boolean('is_taxable'),
                'applies_to_federal' => $request->boolean('applies_to_federal', true),
                'applies_to_state' => $request->boolean('applies_to_state', true),
                'applies_to_local' => $request->boolean('applies_to_local', false),
                'is_active' => $request->boolean('is_active', true),
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax deduction created successfully!',
                'tax_deduction' => $taxDeduction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tax deduction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tax deduction.
     */
    public function updateTaxDeduction(Request $request, TaxDeduction $taxDeduction)
    {
        $this->authorize('update', $taxDeduction);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:standard,itemized,pre_tax,post_tax',
            'calculation_method' => 'required|in:percentage,fixed,formula',
            'amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'is_taxable' => 'boolean',
            'applies_to_federal' => 'boolean',
            'applies_to_state' => 'boolean',
            'applies_to_local' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $taxDeduction->update([
                'name' => $request->name,
                'type' => $request->type,
                'calculation_method' => $request->calculation_method,
                'amount' => $request->amount,
                'max_amount' => $request->max_amount,
                'min_amount' => $request->min_amount ?? 0,
                'is_taxable' => $request->boolean('is_taxable'),
                'applies_to_federal' => $request->boolean('applies_to_federal', true),
                'applies_to_state' => $request->boolean('applies_to_state', true),
                'applies_to_local' => $request->boolean('applies_to_local', false),
                'is_active' => $request->boolean('is_active', true),
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax deduction updated successfully!',
                'tax_deduction' => $taxDeduction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax deduction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete tax deduction.
     */
    public function destroyTaxDeduction(TaxDeduction $taxDeduction)
    {
        $this->authorize('delete', $taxDeduction);
        
        try {
            $taxDeduction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tax deduction deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tax deduction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Employee tax information management.
     */
    public function employeeTaxInfo(Request $request)
    {
        $this->authorize('viewAny', EmployeeTaxInfo::class);
        
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        $taxYear = $request->get('tax_year', date('Y'));
        
        $employeeTaxInfo = EmployeeTaxInfo::with(['user', 'company'])
            ->forCompany($companyId)
            ->forTaxYear($taxYear)
            ->paginate(15);
            
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.tax.employee-info.index', compact(
            'employeeTaxInfo',
            'companies',
            'companyId',
            'taxYear'
        ));
    }

    /**
     * Update employee tax information.
     */
    public function updateEmployeeTaxInfo(Request $request, EmployeeTaxInfo $employeeTaxInfo)
    {
        $this->authorize('update', $employeeTaxInfo);
        
        $request->validate([
            'filing_status' => 'required|in:single,married_jointly,married_separately,head_of_household',
            'allowances' => 'required|integer|min:0|max:20',
            'additional_withholding' => 'nullable|numeric|min:0|max:9999.99',
            'exempt_from_federal' => 'boolean',
            'exempt_from_state' => 'boolean',
            'exempt_from_local' => 'boolean',
            'health_insurance_premium' => 'nullable|numeric|min:0|max:9999.99',
            'retirement_contribution_percent' => 'nullable|numeric|min:0|max:100'
        ]);

        try {
            $employeeTaxInfo->update([
                'filing_status' => $request->filing_status,
                'allowances' => $request->allowances,
                'additional_withholding' => $request->additional_withholding ?? 0,
                'exempt_from_federal' => $request->boolean('exempt_from_federal'),
                'exempt_from_state' => $request->boolean('exempt_from_state'),
                'exempt_from_local' => $request->boolean('exempt_from_local'),
                'health_insurance_premium' => $request->health_insurance_premium ?? 0,
                'retirement_contribution_percent' => $request->retirement_contribution_percent ?? 0,
                'effective_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee tax information updated successfully!',
                'employee_tax_info' => $employeeTaxInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee tax information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Setup default tax configuration for a company.
     */
    public function setupDefaults(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id'
        ]);

        try {
            DB::beginTransaction();
            
            $companyId = $request->company_id;
            
            // Create default tax rates
            foreach (TaxRate::getDefaultRates() as $rateData) {
                TaxRate::create(array_merge($rateData, ['company_id' => $companyId]));
            }
            
            // Create default tax brackets
            foreach (TaxBracket::getDefault2025Brackets() as $bracketData) {
                TaxBracket::create(array_merge($bracketData, ['company_id' => $companyId]));
            }
            
            // Create default tax deductions
            foreach (TaxDeduction::getDefaultDeductions() as $deductionData) {
                TaxDeduction::create(array_merge($deductionData, ['company_id' => $companyId]));
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default tax configuration created successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to setup default tax configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate tax for a given salary.
     */
    public function calculateTax(Request $request)
    {
        $request->validate([
            'salary' => 'required|numeric|min:0',
            'filing_status' => 'required|in:single,married_jointly,married_separately,head_of_household',
            'allowances' => 'nullable|integer|min:0|max:20',
            'additional_withholding' => 'nullable|numeric|min:0',
            'pre_tax_deductions' => 'nullable|numeric|min:0'
        ]);

        try {
            $user = Auth::user();
            $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
            
            $salary = $request->salary;
            $filingStatus = $request->filing_status;
            $allowances = $request->allowances ?? 0;
            $additionalWithholding = $request->additional_withholding ?? 0;
            $preTaxDeductions = $request->pre_tax_deductions ?? 0;
            
            // Calculate taxable income
            $allowanceValue = 4700; // 2025 allowance value
            $taxableIncome = $salary - ($allowances * $allowanceValue) - $preTaxDeductions;
            
            // Get tax rates for this company
            $taxRates = TaxRate::forCompany($companyId)->employeeTaxes()->active()->get();
            
            $taxCalculations = [];
            $totalTax = 0;
            
            foreach ($taxRates as $taxRate) {
                $taxAmount = $taxRate->calculateTax($taxableIncome);
                if ($taxAmount > 0) {
                    $taxCalculations[] = [
                        'name' => $taxRate->name,
                        'type' => $taxRate->type,
                        'rate' => $taxRate->formatted_rate,
                        'amount' => $taxAmount
                    ];
                    $totalTax += $taxAmount;
                }
            }
            
            // Add progressive tax calculation using brackets
            $federalTax = TaxBracket::calculateProgressiveTax($companyId, $taxableIncome, $filingStatus);
            if ($federalTax > 0) {
                $taxCalculations[] = [
                    'name' => 'Federal Income Tax (Progressive)',
                    'type' => 'federal_progressive',
                    'rate' => 'Progressive',
                    'amount' => $federalTax
                ];
                $totalTax += $federalTax;
            }
            
            $netSalary = $salary - $totalTax - $additionalWithholding;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'gross_salary' => $salary,
                    'taxable_income' => $taxableIncome,
                    'total_tax' => $totalTax + $additionalWithholding,
                    'net_salary' => $netSalary,
                    'tax_breakdown' => $taxCalculations,
                    'additional_withholding' => $additionalWithholding,
                    'pre_tax_deductions' => $preTaxDeductions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate tax: ' . $e->getMessage()
            ], 500);
        }
    }
}