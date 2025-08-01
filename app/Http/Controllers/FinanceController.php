<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PayrollRecord;
use App\Models\SalaryHistory;
use App\Services\TaxCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    /**
     * Display finance dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        // Get financial statistics
        $financialStats = $this->getFinancialStatistics($companyId);
        
        // Get payroll summary
        $payrollSummary = $this->getPayrollSummary($companyId);
        
        // Get expense summary
        $expenseSummary = $this->getExpenseSummary($companyId);
        
        // Get recent financial activities
        $recentActivities = $this->getRecentActivities($companyId);
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.dashboard', compact(
            'financialStats',
            'payrollSummary', 
            'expenseSummary',
            'recentActivities',
            'companies',
            'companyId'
        ));
    }

    /**
     * Get financial statistics for company.
     */
    private function getFinancialStatistics($companyId)
    {
        $currentMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return [
            'total_employees' => User::where('company_id', $companyId)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
                })->count(),
            'monthly_payroll' => $this->calculateMonthlyPayroll($companyId),
            'monthly_expenses' => $this->calculateMonthlyExpenses($companyId),
            'pending_payments' => $this->getPendingPayments($companyId),
            'total_working_hours' => Attendance::forCompany($companyId)
                ->thisMonth()
                ->sum('total_hours'),
            'overtime_hours' => Attendance::forCompany($companyId)
                ->thisMonth()
                ->sum('overtime_hours')
        ];
    }

    /**
     * Calculate monthly payroll.
     */
    private function calculateMonthlyPayroll($companyId)
    {
        // This would be calculated based on employee salaries and attendance
        // For now, returning a sample calculation
        $employees = User::where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })->count();
            
        return $employees * 50000; // Sample average salary
    }

    /**
     * Calculate monthly expenses.
     */
    private function calculateMonthlyExpenses($companyId)
    {
        return Expense::forCompany($companyId)
            ->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->approved()
            ->sum('amount');
    }

    /**
     * Get pending payments count.
     */
    private function getPendingPayments($companyId)
    {
        return PayrollRecord::forCompany($companyId)
            ->pending()
            ->count();
    }

    /**
     * Get payroll summary.
     */
    private function getPayrollSummary($companyId)
    {
        $employees = User::with(['attendances' => function($q) {
                $q->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
            }])
            ->where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })
            ->get();

        return $employees->map(function($employee) {
            $monthlyAttendance = $employee->attendances;
            $totalHours = $monthlyAttendance->sum('total_hours');
            $overtimeHours = $monthlyAttendance->sum('overtime_hours');
            
            $basicSalary = $employee->salary ?? 50000;
            $overtimePay = $overtimeHours * 500;
            $grossSalary = $basicSalary + $overtimePay;
            
            // Calculate taxes for summary display
            $taxService = new TaxCalculationService();
            $taxCalculation = $taxService->calculateEmployeeTaxes($employee, $grossSalary, 'monthly');
            
            return [
                'employee' => $employee,
                'total_hours' => $totalHours,
                'overtime_hours' => $overtimeHours,
                'basic_salary' => $basicSalary,
                'overtime_pay' => $overtimePay,
                'gross_salary' => $grossSalary,
                'tax_withholding' => $taxCalculation['total_tax_withholding'],
                'net_salary' => $taxCalculation['net_salary'],
                'employer_cost' => $grossSalary + $taxCalculation['employer_total']
            ];
        });
    }

    /**
     * Get expense summary.
     */
    private function getExpenseSummary($companyId)
    {
        $expenses = Expense::with(['category', 'user'])
            ->forCompany($companyId)
            ->orderBy('expense_date', 'desc')
            ->get();

        if ($expenses->isEmpty()) {
            // Return sample data if no expenses exist
            return [
                ['category' => 'Office Supplies', 'amount' => 25000, 'status' => 'approved'],
                ['category' => 'Travel', 'amount' => 15000, 'status' => 'pending'],
                ['category' => 'Equipment', 'amount' => 75000, 'status' => 'approved'],
                ['category' => 'Utilities', 'amount' => 12000, 'status' => 'approved'],
                ['category' => 'Software', 'amount' => 30000, 'status' => 'pending']
            ];
        }

        return $expenses->map(function($expense) {
            return [
                'id' => $expense->id,
                'category' => $expense->category->name ?? $expense->title,
                'amount' => $expense->amount,
                'status' => $expense->status,
                'expense_date' => $expense->expense_date,
                'user' => $expense->user->name ?? 'Unknown'
            ];
        })->toArray();
    }

    /**
     * Get recent financial activities.
     */
    private function getRecentActivities($companyId)
    {
        // Sample activity data - this would come from activity logs
        return [
            [
                'activity' => 'Payroll processed for July 2025',
                'amount' => 500000,
                'type' => 'payroll',
                'date' => now()->subDays(2)
            ],
            [
                'activity' => 'Office supplies expense approved',
                'amount' => 25000,
                'type' => 'expense',
                'date' => now()->subDays(5)
            ],
            [
                'activity' => 'Equipment purchase approved',
                'amount' => 75000,
                'type' => 'expense',
                'date' => now()->subDays(7)
            ]
        ];
    }

    /**
     * Show salary management page.
     */
    public function salaries(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $employees = User::with(['department', 'roles'])
            ->where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })
            ->orderBy('name')
            ->get();

        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        return view('EmployeeManagemntsystem.Finance.salaries.index', compact('employees', 'companies', 'companyId'));
    }

    /**
     * Update individual employee salary.
     */
    public function updateSalary(Request $request, User $user)
    {
        $request->validate([
            'salary' => 'required|numeric|min:0|max:99999999.99',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if the user belongs to the same company (for non-SuperAdmin)
            $currentUser = Auth::user();
            if (!$currentUser->hasRole('superAdmin') && $user->company_id !== $currentUser->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update salaries for employees in your company.'
                ], 403);
            }

            $oldSalary = $user->salary;
            $user->salary = $request->salary;
            $user->save();

        
            return response()->json([
                'success' => true,
                'message' => 'Salary updated successfully!',
                'data' => [
                    'user_id' => $user->id,
                    'new_salary' => number_format($user->salary, 2),
                    'old_salary' => number_format($oldSalary, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update salary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update employee salaries.
     */
    public function bulkUpdateSalaries(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.user_id' => 'required|exists:users,id',
            'updates.*.salary' => 'required|numeric|min:0|max:99999999.99',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();
            
            $currentUser = Auth::user();
            $updatedCount = 0;
            $errors = [];

            foreach ($request->updates as $update) {
                $user = User::find($update['user_id']);
                
                // Check company scope for non-SuperAdmin users
                if (!$currentUser->hasRole('superAdmin') && $user->company_id !== $currentUser->company_id) {
                    $errors[] = "Cannot update salary for {$user->name} - different company";
                    continue;
                }

                $oldSalary = $user->salary;
                $user->salary = $update['salary'];
                $user->save();

                // Create salary history record
                SalaryHistory::createFromSalaryChange(
                    employee: $user,
                    oldSalary: $oldSalary,
                    newSalary: $update['salary'],
                    changedBy: $currentUser,
                    changeType: 'bulk_update',
                    reason: 'Finance bulk salary update',
                    notes: $request->notes,
                    effectiveDate: $request->effective_date ? Carbon::parse($request->effective_date) : now()
                );

                $updatedCount++;
            }
            
            DB::commit();
            
            $message = "Successfully updated {$updatedCount} salaries";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update salaries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export salary data.
     */
    public function exportSalaries(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $employees = User::with(['department', 'roles'])
            ->where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })
            ->orderBy('name')
            ->get();
        
        $filename = 'salary_data_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Department',
                'Role',
                'Current Salary',
                'Date of Joining',
                'Last Updated'
            ]);
            
            // CSV Data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->name,
                    $employee->employee_id ?? '-',
                    $employee->department->name ?? '-',
                    $employee->getRoleNames()->implode(', '),
                    $employee->salary ?? 0,
                    $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : '-',
                    $employee->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate individual payroll.
     */
    public function generateIndividualPayroll(Request $request, User $user)
    {
        $request->validate([
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if the user belongs to the same company (for non-SuperAdmin)
            $currentUser = Auth::user();
            if (!$currentUser->hasRole('superAdmin') && $user->company_id !== $currentUser->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only generate payroll for employees in your company.'
                ], 403);
            }

            // Check if payroll already exists for this period
            $existingPayroll = PayrollRecord::where('user_id', $user->id)
                ->where('pay_period_start', $request->pay_period_start)
                ->where('pay_period_end', $request->pay_period_end)
                ->first();

            if ($existingPayroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payroll already exists for this employee in the specified period.'
                ], 422);
            }

            // Calculate salary based on attendance
            $salaryData = $this->calculateEmployeeSalaryForPeriod($user, $request->pay_period_start, $request->pay_period_end);
            $taxCalc = $salaryData['tax_calculation'];
            
            // Create payroll record with comprehensive tax calculations
            $payroll = PayrollRecord::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'pay_period_start' => $request->pay_period_start,
                'pay_period_end' => $request->pay_period_end,
                'basic_salary' => $salaryData['basic_salary'],
                'overtime_hours' => $salaryData['overtime_hours'],
                'overtime_rate' => 500, // Default overtime rate
                'overtime_pay' => $salaryData['overtime_pay'],
                'bonus' => $request->bonus ?? 0,
                'gross_salary' => $salaryData['gross_salary'] + ($request->bonus ?? 0),
                
                // Tax withholdings
                'pre_tax_deductions' => $taxCalc['pre_tax_deductions'],
                'federal_tax' => $taxCalc['federal_tax'],
                'state_tax' => $taxCalc['state_tax'],
                'local_tax' => $taxCalc['local_tax'],
                'social_security_tax' => $taxCalc['social_security_tax'],
                'medicare_tax' => $taxCalc['medicare_tax'],
                'total_tax_withholding' => $taxCalc['total_tax_withholding'],
                'post_tax_deductions' => $taxCalc['post_tax_deductions'],
                
                // Employer contributions
                'employer_social_security' => $taxCalc['employer_social_security'],
                'employer_medicare' => $taxCalc['employer_medicare'],
                'employer_unemployment' => $taxCalc['employer_unemployment'],
                'employer_benefits' => 0,
                
                'deductions' => $request->deductions ?? 0,
                'other_deductions' => 0,
                'net_salary' => $taxCalc['net_salary'] - ($request->deductions ?? 0),
                'status' => 'processed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'payment_date' => now()->addDays(3), // Payment in 3 days
                'payment_method' => 'bank_transfer',
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payroll generated successfully!',
                'data' => [
                    'payroll_id' => $payroll->id,
                    'employee_name' => $user->name,
                    'net_salary' => number_format($payroll->net_salary, 2),
                    'gross_salary' => number_format($payroll->gross_salary, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate employee salary for a specific period.
     */
    private function calculateEmployeeSalaryForPeriod($employee, $startDate, $endDate)
    {
        $basicSalary = $employee->salary ?? 50000;
        $hourlyRate = 500; // Sample overtime rate
        
        $attendances = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
            
        $totalHours = $attendances->sum('total_hours');
        $overtimeHours = $attendances->sum('overtime_hours');
        
        $overtimePay = $overtimeHours * $hourlyRate;
        $grossSalary = $basicSalary + $overtimePay;
        
        // Calculate comprehensive tax information using TaxCalculationService
        $taxService = new TaxCalculationService();
        $taxCalculation = $taxService->calculateEmployeeTaxes($employee, $grossSalary, 'monthly');
        
        return [
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'gross_salary' => $grossSalary,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'tax_calculation' => $taxCalculation
        ];
    }

    /**
     * Show payroll management page.
     */
    public function payroll(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $payrollData = $this->getPayrollSummary($companyId);
        
        return view('EmployeeManagemntsystem.Finance.payroll.index', compact('payrollData', 'companyId'));
    }

    /**
     * Process payroll for employees.
     */
    public function processPayroll(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'pay_period' => 'required|string',
            'pay_period_start' => 'nullable|date',
            'pay_period_end' => 'nullable|date|after_or_equal:pay_period_start'
        ]);

        try {
            DB::beginTransaction();
            
            // Determine pay period dates
            $payPeriodStart = $request->pay_period_start ?? $this->getPayPeriodStart($request->pay_period);
            $payPeriodEnd = $request->pay_period_end ?? $this->getPayPeriodEnd($request->pay_period);
            
            $processedCount = 0;
            $totalAmount = 0;
            $errors = [];
            $currentUser = Auth::user();
            
            foreach ($request->employee_ids as $employeeId) {
                $employee = User::find($employeeId);
                
                // Check company scope for non-SuperAdmin users
                if (!$currentUser->hasRole('superAdmin') && $employee->company_id !== $currentUser->company_id) {
                    $errors[] = "Cannot process payroll for {$employee->name} - different company";
                    continue;
                }

                // Check if payroll already exists for this period
                $existingPayroll = PayrollRecord::where('user_id', $employee->id)
                    ->where('pay_period_start', $payPeriodStart)
                    ->where('pay_period_end', $payPeriodEnd)
                    ->first();

                if ($existingPayroll) {
                    $errors[] = "Payroll already exists for {$employee->name} in this period";
                    continue;
                }
                
                // Calculate salary based on attendance
                $salaryData = $this->calculateEmployeeSalaryForPeriod($employee, $payPeriodStart, $payPeriodEnd);
                $taxCalc = $salaryData['tax_calculation'];
                
                // Create payroll record with comprehensive tax calculations
                $payroll = PayrollRecord::create([
                    'user_id' => $employee->id,
                    'company_id' => $employee->company_id,
                    'pay_period_start' => $payPeriodStart,
                    'pay_period_end' => $payPeriodEnd,
                    'basic_salary' => $salaryData['basic_salary'],
                    'overtime_hours' => $salaryData['overtime_hours'],
                    'overtime_rate' => 500, // Default overtime rate
                    'overtime_pay' => $salaryData['overtime_pay'],
                    'bonus' => 0, // No bonus in bulk processing
                    'gross_salary' => $salaryData['gross_salary'],
                    
                    // Tax withholdings
                    'pre_tax_deductions' => $taxCalc['pre_tax_deductions'],
                    'federal_tax' => $taxCalc['federal_tax'],
                    'state_tax' => $taxCalc['state_tax'],
                    'local_tax' => $taxCalc['local_tax'],
                    'social_security_tax' => $taxCalc['social_security_tax'],
                    'medicare_tax' => $taxCalc['medicare_tax'],
                    'total_tax_withholding' => $taxCalc['total_tax_withholding'],
                    'post_tax_deductions' => $taxCalc['post_tax_deductions'],
                    
                    // Employer contributions
                    'employer_social_security' => $taxCalc['employer_social_security'],
                    'employer_medicare' => $taxCalc['employer_medicare'],
                    'employer_unemployment' => $taxCalc['employer_unemployment'],
                    'employer_benefits' => 0,
                    
                    'deductions' => 0, // No additional deductions in bulk processing
                    'other_deductions' => 0,
                    'net_salary' => $taxCalc['net_salary'],
                    'status' => 'processed',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'payment_date' => now()->addDays(3), // Payment in 3 days
                    'payment_method' => 'bank_transfer',
                    'notes' => "Bulk payroll processed for period {$payPeriodStart} to {$payPeriodEnd}"
                ]);
                
                $processedCount++;
                $totalAmount += $payroll->net_salary;
            }
            
            DB::commit();
            
            $message = "Payroll processed for {$processedCount} employees";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'processed_count' => $processedCount,
                'total_amount' => $totalAmount,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pay period start date based on period string.
     */
    private function getPayPeriodStart($period)
    {
        return match($period) {
            'current_month' => now()->startOfMonth(),
            'previous_month' => now()->subMonth()->startOfMonth(),
            'custom' => now()->startOfMonth(), // Default to current month
            default => now()->startOfMonth()
        };
    }

    /**
     * Get pay period end date based on period string.
     */
    private function getPayPeriodEnd($period)
    {
        return match($period) {
            'current_month' => now()->endOfMonth(),
            'previous_month' => now()->subMonth()->endOfMonth(),
            'custom' => now()->endOfMonth(), // Default to current month
            default => now()->endOfMonth()
        };
    }

    /**
     * Calculate employee salary based on attendance.
     */
    private function calculateEmployeeSalary($employee, $payPeriod)
    {
        // Use employee's actual salary or default
        $basicSalary = $employee->salary ?? 50000;
        $hourlyRate = 500; // Sample overtime rate
        
        $attendances = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();
            
        $totalHours = $attendances->sum('total_hours');
        $overtimeHours = $attendances->sum('overtime_hours');
        
        $overtimePay = $overtimeHours * $hourlyRate;
        $totalSalary = $basicSalary + $overtimePay;
        
        return [
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'total_salary' => $totalSalary,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours
        ];
    }

    /**
     * Show expense management page.
     */
    public function expenses(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $expenses = $this->getExpenseSummary($companyId);
        
        return view('EmployeeManagemntsystem.Finance.expenses.index', compact('expenses', 'companyId'));
    }

    /**
     * Show financial reports page.
     */
    public function reports(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $reportData = [
            'monthly_summary' => $this->getMonthlyFinancialSummary($companyId),
            'quarterly_summary' => $this->getQuarterlyFinancialSummary($companyId),
            'yearly_summary' => $this->getYearlyFinancialSummary($companyId)
        ];
        
        return view('EmployeeManagemntsystem.Finance.reports.index', compact('reportData', 'companyId'));
    }

    /**
     * Get monthly financial summary.
     */
    private function getMonthlyFinancialSummary($companyId)
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i)->format('M Y');
            $months[] = [
                'month' => $month,
                'payroll' => rand(400000, 600000), // Sample data
                'expenses' => rand(200000, 300000), // Sample data
                'net' => rand(100000, 300000) // Sample data
            ];
        }
        
        return array_reverse($months);
    }

    /**
     * Get quarterly financial summary.
     */
    private function getQuarterlyFinancialSummary($companyId)
    {
        return [
            ['quarter' => 'Q1 2025', 'payroll' => 1500000, 'expenses' => 750000, 'net' => 750000],
            ['quarter' => 'Q2 2025', 'payroll' => 1600000, 'expenses' => 800000, 'net' => 800000],
            ['quarter' => 'Q3 2025', 'payroll' => 1550000, 'expenses' => 725000, 'net' => 825000],
            ['quarter' => 'Q4 2024', 'payroll' => 1450000, 'expenses' => 700000, 'net' => 750000]
        ];
    }

    /**
     * Get yearly financial summary.
     */
    private function getYearlyFinancialSummary($companyId)
    {
        return [
            ['year' => '2025', 'payroll' => 6100000, 'expenses' => 3075000, 'net' => 3025000],
            ['year' => '2024', 'payroll' => 5800000, 'expenses' => 2900000, 'net' => 2900000],
            ['year' => '2023', 'payroll' => 5200000, 'expenses' => 2600000, 'net' => 2600000]
        ];
    }

    /**
     * Export financial report.
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $reportType = $request->get('report_type', 'monthly');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        
        $filename = "financial_report_{$reportType}_" . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($companyId, $reportType, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Period',
                'Payroll Amount',
                'Expenses Amount',
                'Net Amount',
                'Employee Count',
                'Generated Date'
            ]);
            
            // Get report data based on type
            $reportData = $this->getReportData($companyId, $reportType, $dateFrom, $dateTo);
            
            // CSV Data
            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['period'],
                    $row['payroll'],
                    $row['expenses'],
                    $row['net'],
                    $row['employee_count'] ?? 0,
                    now()->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get report data based on type.
     */
    private function getReportData($companyId, $reportType, $dateFrom, $dateTo)
    {
        switch ($reportType) {
            case 'monthly':
                return $this->getMonthlyFinancialSummary($companyId);
            case 'quarterly':
                return array_map(function($item) {
                    return [
                        'period' => $item['quarter'],
                        'payroll' => $item['payroll'],
                        'expenses' => $item['expenses'],
                        'net' => $item['net'],
                        'employee_count' => 0
                    ];
                }, $this->getQuarterlyFinancialSummary($companyId));
            case 'yearly':
                return array_map(function($item) {
                    return [
                        'period' => $item['year'],
                        'payroll' => $item['payroll'],
                        'expenses' => $item['expenses'],
                        'net' => $item['net'],
                        'employee_count' => 0
                    ];
                }, $this->getYearlyFinancialSummary($companyId));
            default:
                return [];
        }
    }

    /**
     * Approve expense.
     */
    public function approveExpense(Request $request, Expense $expense)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $expense->status = 'approved';
            $expense->approved_by = Auth::id();
            $expense->approved_at = now();
            $expense->notes = $request->notes;
            $expense->save();

            return response()->json([
                'success' => true,
                'message' => 'Expense approved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve expense: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject expense.
     */
    public function rejectExpense(Request $request, Expense $expense)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            $expense->status = 'rejected';
            $expense->approved_by = Auth::id();
            $expense->approved_at = now();
            $expense->rejection_reason = $request->rejection_reason;
            $expense->save();

            return response()->json([
                'success' => true,
                'message' => 'Expense rejected successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject expense: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reimburse expense.
     */
    public function reimburseExpense(Request $request, Expense $expense)
    {
        if (!$expense->canBeReimbursed()) {
            return response()->json([
                'success' => false,
                'message' => 'This expense cannot be reimbursed.'
            ], 422);
        }

        try {
            $expense->status = 'reimbursed';
            $expense->reimbursed_at = now();
            $expense->save();

            return response()->json([
                'success' => true,
                'message' => 'Expense reimbursed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reimburse expense: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export payroll data.
     */
    public function exportPayroll(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $payrollData = $this->getPayrollSummary($companyId);
        
        $filename = 'payroll_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payrollData) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Department',
                'Basic Salary',
                'Total Hours',
                'Overtime Hours',
                'Overtime Pay',
                'Total Salary',
                'Generated Date'
            ]);
            
            // CSV Data
            foreach ($payrollData as $record) {
                fputcsv($file, [
                    $record['employee']->name,
                    $record['employee']->employee_id ?? '-',
                    $record['employee']->department->name ?? '-',
                    $record['basic_salary'],
                    $record['total_hours'],
                    $record['overtime_hours'],
                    $record['overtime_pay'],
                    $record['total_salary'],
                    now()->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export expenses data.
     */
    public function exportExpenses(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $expenses = $this->getExpenseSummary($companyId);
        
        $filename = 'expenses_report_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Category',
                'Amount',
                'Status',
                'Generated Date'
            ]);
            
            // CSV Data
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense['category'],
                    $expense['amount'],
                    $expense['status'],
                    now()->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get payroll summary report.
     */
    public function payrollSummaryReport(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $summary = [
            'total_employees' => User::where('company_id', $companyId)->count(),
            'total_payroll' => $this->calculateMonthlyPayroll($companyId),
            'average_salary' => $this->calculateMonthlyPayroll($companyId) / max(1, User::where('company_id', $companyId)->count()),
            'total_overtime_pay' => 50000, // Sample data
            'payroll_by_department' => $this->getPayrollByDepartment($companyId)
        ];
        
        return response()->json(['success' => true, 'data' => $summary]);
    }

    /**
     * Get expense summary report.
     */
    public function expenseSummaryReport(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $expenses = Expense::forCompany($companyId)->get();
        
        $summary = [
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('amount'),
            'pending_expenses' => $expenses->where('status', 'pending')->count(),
            'approved_expenses' => $expenses->where('status', 'approved')->count(),
            'expenses_by_category' => $expenses->groupBy('category.name')->map->count()
        ];
        
        return response()->json(['success' => true, 'data' => $summary]);
    }

    /**
     * Get payroll by department.
     */
    private function getPayrollByDepartment($companyId)
    {
        return User::with('department')
            ->where('company_id', $companyId)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })
            ->get()
            ->groupBy('department.name')
            ->map(function($employees) {
                return [
                    'count' => $employees->count(),
                    'total_salary' => $employees->count() * 50000 // Sample calculation
                ];
            });
    }
}