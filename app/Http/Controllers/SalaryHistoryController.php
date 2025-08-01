<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalaryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class SalaryHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Get salary history for a specific employee.
     */
    public function getEmployeeSalaryHistory(Request $request, $employeeId)
    {
        try {
            $employee = User::findOrFail($employeeId);
            $currentUser = Auth::user();

            // Authorization checks
            if (!$this->canViewSalaryHistory($currentUser, $employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view salary history'
                ], 403);
            }

            $perPage = $request->get('per_page', 15);
            $sortBy = $request->get('sort_by', 'effective_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $changeType = $request->get('change_type');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $query = SalaryHistory::with(['changedBy', 'incrementRequest'])
                ->forEmployee($employeeId);

            // Apply filters
            if ($changeType) {
                $query->byChangeType($changeType);
            }

            if ($startDate && $endDate) {
                $query->inDateRange($startDate, $endDate);
            }

            $salaryHistories = $query->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);

            $salaryHistories->getCollection()->transform(function ($history) {
                return [
                    'id' => $history->id,
                    'old_salary' => $history->old_salary,
                    'new_salary' => $history->new_salary,
                    'change_amount' => $history->change_amount,
                    'change_percentage' => $history->change_percentage,
                    'formatted_change_amount' => $history->formatted_change_amount,
                    'formatted_change_percentage' => $history->formatted_change_percentage,
                    'change_type' => $history->change_type,
                    'reason' => $history->reason,
                    'notes' => $history->notes,
                    'effective_date' => $history->effective_date->format('M d, Y'),
                    'created_at' => $history->created_at->format('M d, Y H:i'),
                    'changed_by' => [
                        'name' => $history->changedBy->name,
                        'role' => $history->changedBy->roles->first()?->name
                    ],
                    'is_increase' => $history->is_increase,
                    'is_decrease' => $history->is_decrease,
                    'increment_request' => $history->incrementRequest ? [
                        'id' => $history->incrementRequest->id,
                        'status' => $history->incrementRequest->status
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $salaryHistories,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'current_salary' => $employee->salary,
                    'department' => $employee->department?->name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching salary history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary history statistics for an employee.
     */
    public function getEmployeeSalaryStats($employeeId)
    {
        try {
            $employee = User::findOrFail($employeeId);
            $currentUser = Auth::user();

            if (!$this->canViewSalaryHistory($currentUser, $employee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view salary statistics'
                ], 403);
            }

            $histories = SalaryHistory::forEmployee($employeeId)->get();

            if ($histories->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'stats' => [
                        'total_changes' => 0,
                        'total_increases' => 0,
                        'total_decreases' => 0,
                        'highest_salary' => $employee->salary,
                        'lowest_salary' => $employee->salary,
                        'average_change_amount' => 0,
                        'first_salary_date' => null,
                        'last_change_date' => null
                    ]
                ]);
            }

            $increases = $histories->where('change_amount', '>', 0);
            $decreases = $histories->where('change_amount', '<', 0);
            
            $stats = [
                'total_changes' => $histories->count(),
                'total_increases' => $increases->count(),
                'total_decreases' => $decreases->count(),
                'highest_salary' => $histories->max('new_salary'),
                'lowest_salary' => $histories->min('new_salary'),
                'average_change_amount' => round($histories->avg('change_amount'), 2),
                'total_growth' => $employee->salary - $histories->first()->old_salary ?? 0,
                'first_salary_date' => $histories->sortBy('effective_date')->first()?->effective_date->format('M d, Y'),
                'last_change_date' => $histories->sortByDesc('effective_date')->first()?->effective_date->format('M d, Y'),
                'change_types' => $histories->groupBy('change_type')->map->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching salary statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company-wide salary history (Admin/Finance only).
     */
    public function getCompanySalaryHistory(Request $request)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser->hasAnyRole(['Admin', 'superAdmin', 'Finance'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view company salary history'
                ], 403);
            }

            $companyId = $currentUser->hasRole('superAdmin') ? 
                $request->get('company_id') : $currentUser->company_id;

            $perPage = $request->get('per_page', 20);
            $changeType = $request->get('change_type');
            $employeeId = $request->get('employee_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $query = SalaryHistory::with(['employee', 'changedBy'])
                ->forCompany($companyId);

            // Apply filters
            if ($changeType) {
                $query->byChangeType($changeType);
            }

            if ($employeeId) {
                $query->forEmployee($employeeId);
            }

            if ($startDate && $endDate) {
                $query->inDateRange($startDate, $endDate);
            }

            $salaryHistories = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $salaryHistories->getCollection()->transform(function ($history) {
                return [
                    'id' => $history->id,
                    'employee' => [
                        'id' => $history->employee->id,
                        'name' => $history->employee->name,
                        'department' => $history->employee->department?->name
                    ],
                    'old_salary' => $history->old_salary,
                    'new_salary' => $history->new_salary,
                    'formatted_change_amount' => $history->formatted_change_amount,
                    'formatted_change_percentage' => $history->formatted_change_percentage,
                    'change_type' => $history->change_type,
                    'reason' => $history->reason,
                    'effective_date' => $history->effective_date->format('M d, Y'),
                    'changed_by' => $history->changedBy->name,
                    'is_increase' => $history->is_increase
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $salaryHistories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching company salary history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export salary history data.
     */
    public function exportSalaryHistory(Request $request, $employeeId = null)
    {
        try {
            $currentUser = Auth::user();
            
            if ($employeeId) {
                $employee = User::findOrFail($employeeId);
                if (!$this->canViewSalaryHistory($currentUser, $employee)) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
                $histories = SalaryHistory::with(['changedBy'])->forEmployee($employeeId)->get();
                $filename = "salary_history_{$employee->name}_{now()->format('Y-m-d')}.csv";
            } else {
                if (!$currentUser->hasAnyRole(['Admin', 'superAdmin', 'Finance'])) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
                $companyId = $currentUser->hasRole('superAdmin') ? 
                    $request->get('company_id') : $currentUser->company_id;
                $histories = SalaryHistory::with(['employee', 'changedBy'])->forCompany($companyId)->get();
                $filename = "company_salary_history_{now()->format('Y-m-d')}.csv";
            }

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($histories, $employeeId) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                if ($employeeId) {
                    fputcsv($file, ['Date', 'Old Salary', 'New Salary', 'Change Amount', 'Change %', 'Change Type', 'Reason', 'Changed By']);
                } else {
                    fputcsv($file, ['Employee', 'Department', 'Date', 'Old Salary', 'New Salary', 'Change Amount', 'Change %', 'Change Type', 'Reason', 'Changed By']);
                }

                foreach ($histories as $history) {
                    if ($employeeId) {
                        fputcsv($file, [
                            $history->effective_date->format('Y-m-d'),
                            $history->old_salary,
                            $history->new_salary,
                            $history->change_amount,
                            $history->change_percentage . '%',
                            $history->change_type,
                            $history->reason,
                            $history->changedBy->name
                        ]);
                    } else {
                        fputcsv($file, [
                            $history->employee->name,
                            $history->employee->department?->name ?? 'N/A',
                            $history->effective_date->format('Y-m-d'),
                            $history->old_salary,
                            $history->new_salary,
                            $history->change_amount,
                            $history->change_percentage . '%',
                            $history->change_type,
                            $history->reason,
                            $history->changedBy->name
                        ]);
                    }
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting salary history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can view salary history for an employee.
     */
    private function canViewSalaryHistory(User $currentUser, User $employee): bool
    {
        // SuperAdmin can view all
        if ($currentUser->hasRole('superAdmin')) {
            return true;
        }

        // Admin/Finance/HR can view employees in their company
        if ($currentUser->hasAnyRole(['Admin', 'Finance', 'HR'])) {
            return $currentUser->company_id === $employee->company_id;
        }

        // TeamLead can view their team members
        if ($currentUser->hasRole('TeamLead')) {
            return $employee->team_lead_id === $currentUser->id && 
                   $currentUser->company_id === $employee->company_id;
        }

        // Employee can only view their own history
        if ($currentUser->hasRole('Employee')) {
            return $currentUser->id === $employee->id;
        }

        return false;
    }
}