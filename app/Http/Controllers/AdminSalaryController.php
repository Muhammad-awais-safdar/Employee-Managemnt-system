<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalaryIncrementRequest;
use App\Models\SalaryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AdminSalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin|superAdmin']);
    }

    public function getEmployees()
    {
        try {
            // Get all employees in the admin's company (excluding admins and superadmins)
            $companyId = Auth::user()->company_id;
            
            $employees = User::where('company_id', $companyId)
                ->whereDoesntHave('roles', function ($query) {
                    $query->whereIn('name', ['Admin', 'superAdmin']);
                })
                ->with(['department'])
                ->select('id', 'name', 'email', 'salary', 'department_id', 'updated_at')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'salary' => $employee->salary ?? 0,
                        'department' => $employee->department->name ?? null,
                        'salary_updated_at' => $employee->updated_at ? $employee->updated_at->format('M d, Y') : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading employees: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSalary(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:users,id',
                'salary' => 'required|numeric|min:0|max:99999999.99',
                'effective_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000'
            ]);

            $employee = User::findOrFail($request->employee_id);

            // Check if admin can manage this employee (same company)
            if ($employee->company_id !== Auth::user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to manage this employee'
                ], 403);
            }

            // Check if employee is not an admin or superadmin
            if ($employee->hasAnyRole(['Admin', 'superAdmin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot set salary for admin users'
                ], 403);
            }

            $oldSalary = $employee->salary;
            
            DB::transaction(function () use ($employee, $request, $oldSalary) {
                // Update employee salary
                $employee->update([
                    'salary' => $request->salary,
                    'updated_at' => now()
                ]);

                // Create salary history record
                SalaryHistory::createFromSalaryChange(
                    employee: $employee,
                    oldSalary: $oldSalary,
                    newSalary: $request->salary,
                    changedBy: Auth::user(),
                    changeType: 'direct_update',
                    reason: 'Admin salary update',
                    notes: $request->notes,
                    effectiveDate: $request->effective_date ? Carbon::parse($request->effective_date) : now()
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Employee salary updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating salary: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getIncrementRequests()
    {
        try {
            $companyId = Auth::user()->company_id;
            
            $requests = SalaryIncrementRequest::where('company_id', $companyId)
                ->with(['employee', 'requestedBy'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'employee' => [
                            'name' => $request->employee->name,
                            'email' => $request->employee->email
                        ],
                        'requested_by' => [
                            'name' => $request->requestedBy->name
                        ],
                        'current_salary' => $request->current_salary,
                        'requested_salary' => $request->requested_salary,
                        'increment_amount' => $request->increment_amount,
                        'increment_percentage' => $request->increment_percentage,
                        'reason' => $request->reason,
                        'status' => $request->status,
                        'created_at' => $request->created_at->toISOString(),
                        'approved_at' => $request->approved_at?->format('M d, Y'),
                        'admin_notes' => $request->admin_notes
                    ];
                });

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading increment requests: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reviewIncrementRequest(Request $request)
    {
        try {
            $request->validate([
                'request_id' => 'required|exists:salary_increment_requests,id',
                'decision' => 'required|in:approved,rejected',
                'admin_notes' => 'nullable|string|max:1000',
                'effective_date' => 'nullable|date'
            ]);

            $incrementRequest = SalaryIncrementRequest::findOrFail($request->request_id);

            // Check if admin can review this request (same company)
            if ($incrementRequest->company_id !== Auth::user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to review this request'
                ], 403);
            }

            // Check if request is still pending
            if ($incrementRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been reviewed'
                ], 400);
            }

            DB::transaction(function () use ($incrementRequest, $request) {
                // Update the increment request
                $incrementRequest->update([
                    'status' => $request->decision,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'admin_notes' => $request->admin_notes,
                    'effective_date' => $request->effective_date
                ]);

                // If approved, update the employee's salary
                if ($request->decision === 'approved') {
                    $employee = $incrementRequest->employee;
                    $employee->update([
                        'salary' => $incrementRequest->requested_salary,
                        'updated_at' => now()
                    ]);

                    // Create salary history record for increment request approval
                    SalaryHistory::createFromSalaryChange(
                        employee: $employee,
                        oldSalary: $incrementRequest->current_salary,
                        newSalary: $incrementRequest->requested_salary,
                        changedBy: Auth::user(),
                        changeType: 'increment_request',
                        reason: $incrementRequest->reason,
                        notes: $request->admin_notes,
                        effectiveDate: $request->effective_date ? Carbon::parse($request->effective_date) : now(),
                        incrementRequestId: $incrementRequest->id
                    );
                }
            });

            $message = $request->decision === 'approved' 
                ? 'Increment request approved and salary updated successfully'
                : 'Increment request rejected';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reviewing request: ' . $e->getMessage()
            ], 500);
        }
    }
}