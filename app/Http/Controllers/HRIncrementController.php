<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalaryIncrementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HRIncrementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Hr|Admin|superAdmin']);
    }

    public function index()
    {
        $companyId = Auth::user()->company_id;
        
        // Get employees for increment requests (excluding admins and superadmins)
        $employees = User::where('company_id', $companyId)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'superAdmin']);
            })
            ->with(['department'])
            ->orderBy('name')
            ->get();

        // Get increment requests made by this HR user
        $incrementRequests = SalaryIncrementRequest::where('company_id', $companyId)
            ->where('requested_by', Auth::id())
            ->with(['employee', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('EmployeeManagemntsystem.HR.increment-requests.index', compact('employees', 'incrementRequests'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:users,id',
                'requested_salary' => 'required|numeric|min:0|max:99999999.99',
                'reason' => 'required|string|max:2000'
            ]);

            $employee = User::findOrFail($request->employee_id);

            // Check if HR can request for this employee (same company)
            if ($employee->company_id !== Auth::user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to request increment for this employee'
                ], 403);
            }

            // Check if employee is not an admin or superadmin
            if ($employee->hasAnyRole(['Admin', 'superAdmin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot request increment for admin users'
                ], 403);
            }

            // Check if there's already a pending request for this employee
            $existingRequest = SalaryIncrementRequest::where('employee_id', $request->employee_id)
                ->where('status', 'pending')
                ->exists();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a pending increment request for this employee'
                ], 400);
            }

            $currentSalary = $employee->salary ?? 0;
            $requestedSalary = $request->requested_salary;

            // Validate that requested salary is higher than current
            if ($requestedSalary <= $currentSalary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested salary must be higher than current salary'
                ], 400);
            }

            $incrementAmount = $requestedSalary - $currentSalary;
            $incrementPercentage = $currentSalary > 0 ? round(($incrementAmount / $currentSalary) * 100, 2) : 0;

            // Create the increment request
            SalaryIncrementRequest::create([
                'employee_id' => $request->employee_id,
                'requested_by' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'current_salary' => $currentSalary,
                'requested_salary' => $requestedSalary,
                'increment_amount' => $incrementAmount,
                'increment_percentage' => $incrementPercentage,
                'reason' => $request->reason,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Salary increment request submitted successfully'
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
                'message' => 'Error submitting request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeeDetails($id)
    {
        try {
            $employee = User::where('id', $id)
                ->where('company_id', Auth::user()->company_id)
                ->whereDoesntHave('roles', function ($query) {
                    $query->whereIn('name', ['Admin', 'superAdmin']);
                })
                ->with(['department'])
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found or unauthorized'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'salary' => $employee->salary ?? 0,
                    'department' => $employee->department->name ?? 'N/A'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading employee details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMyRequests()
    {
        try {
            $companyId = Auth::user()->company_id;
            
            $requests = SalaryIncrementRequest::where('company_id', $companyId)
                ->where('requested_by', Auth::id())
                ->with(['employee', 'approvedBy'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'employee' => [
                            'name' => $request->employee->name,
                            'email' => $request->employee->email
                        ],
                        'current_salary' => $request->current_salary,
                        'requested_salary' => $request->requested_salary,
                        'increment_amount' => $request->increment_amount,
                        'increment_percentage' => $request->increment_percentage,
                        'reason' => $request->reason,
                        'status' => $request->status,
                        'created_at' => $request->created_at->format('M d, Y'),
                        'approved_at' => $request->approved_at?->format('M d, Y'),
                        'approved_by' => $request->approvedBy?->name,
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
                'message' => 'Error loading requests: ' . $e->getMessage()
            ], 500);
        }
    }
}