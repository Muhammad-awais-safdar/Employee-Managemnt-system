<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of leave types.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? $request->get('company_id') : $user->company_id;
        
        $query = LeaveType::with('company')
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('company_id', $companyId);
            });
            
        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $leaveTypes = $query->orderBy('name')->paginate(15);
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        // Determine view based on user role
        $viewName = 'EmployeeManagemntsystem.Admin.leave-types.index';
        if ($user->hasRole('HR')) {
            $viewName = 'EmployeeManagemntsystem.HR.leave-types.index';
        }
        
        return view($viewName, compact('leaveTypes', 'companies', 'companyId'));
    }

    /**
     * Show the form for creating a new leave type.
     */
    public function create()
    {
        $user = Auth::user();
        $companyId = $user->hasRole('superAdmin') ? null : $user->company_id;
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        // Define available roles
        $availableRoles = ['Employee', 'TeamLead', 'HR', 'Finance', 'Admin'];
        
        // Determine view based on user role
        $viewName = 'EmployeeManagemntsystem.Admin.leave-types.create';
        if ($user->hasRole('HR')) {
            $viewName = 'EmployeeManagemntsystem.HR.leave-types.create';
        }
        
        return view($viewName, compact('companies', 'companyId', 'availableRoles'));
    }

    /**
     * Store a newly created leave type.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'company_id' => $user->hasRole('superAdmin') ? 'required|exists:companies,id' : 'nullable',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|regex:/^[A-Z]+$/',
            'description' => 'nullable|string|max:500',
            'max_days_per_year' => 'required|integer|min:1|max:365',
            'carry_forward_limit' => 'nullable|integer|min:0',
            'min_notice_days' => 'nullable|integer|min:0',
            'max_consecutive_days' => 'nullable|integer|min:0',
            'deduction_rate' => 'required|numeric|min:0|max:1',
            'applicable_roles' => 'nullable|array',
            'applicable_roles.*' => 'string|in:Employee,TeamLead,HR,Finance,Admin',
            'requires_medical_certificate' => 'boolean',
            'is_paid' => 'boolean',
            'weekend_included' => 'boolean',
            'holiday_included' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Set company_id based on user role
        $companyId = $user->hasRole('superAdmin') ? $request->company_id : $user->company_id;
        
        // Check for duplicate code within company
        $exists = LeaveType::where('company_id', $companyId)
            ->where('code', $request->code)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Leave type code already exists for this company.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $leaveType = new LeaveType();
            $leaveType->fill($request->all());
            $leaveType->company_id = $companyId;
            $leaveType->requires_medical_certificate = $request->boolean('requires_medical_certificate');
            $leaveType->is_paid = $request->boolean('is_paid');
            $leaveType->weekend_included = $request->boolean('weekend_included');
            $leaveType->holiday_included = $request->boolean('holiday_included');
            $leaveType->is_active = $request->boolean('is_active', true);
            
            $leaveType->save();
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave type created successfully!',
                    'leaveType' => $leaveType
                ]);
            }
            
            return redirect()->route('Admin.leave-types.index')
                ->with('success', 'Leave type created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create leave type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create leave type: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified leave type.
     */
    public function show(LeaveType $leaveType)
    {
        $this->authorize('view', $leaveType);
        
        $leaveType->load(['company', 'leaves', 'leaveBalances']);
        
        // Get usage statistics
        $statistics = [
            'total_applications' => $leaveType->leaves()->count(),
            'approved_applications' => $leaveType->leaves()->where('status', 'approved')->count(),
            'pending_applications' => $leaveType->leaves()->where('status', 'pending')->count(),
            'rejected_applications' => $leaveType->leaves()->where('status', 'rejected')->count(),
            'total_days_used' => $leaveType->leaves()->where('status', 'approved')->sum('total_days'),
            'employees_with_balance' => $leaveType->leaveBalances()->count()
        ];
        
        // Determine view based on user role
        $user = Auth::user();
        $viewName = 'EmployeeManagemntsystem.Admin.leave-types.show';
        if ($user->hasRole('HR')) {
            $viewName = 'EmployeeManagemntsystem.HR.leave-types.show';
        }
        
        return view($viewName, compact('leaveType', 'statistics'));
    }

    /**
     * Show the form for editing the specified leave type.
     */
    public function edit(LeaveType $leaveType)
    {
        $this->authorize('update', $leaveType);
        
        $user = Auth::user();
        
        // Get companies for SuperAdmin
        $companies = $user->hasRole('superAdmin') ? Company::active()->get() : null;
        
        // Define available roles
        $availableRoles = ['Employee', 'TeamLead', 'HR', 'Finance', 'Admin'];
        
        // Determine view based on user role
        $viewName = 'EmployeeManagemntsystem.Admin.leave-types.edit';
        if ($user->hasRole('HR')) {
            $viewName = 'EmployeeManagemntsystem.HR.leave-types.edit';
        }
        
        return view($viewName, compact('leaveType', 'companies', 'availableRoles'));
    }

    /**
     * Update the specified leave type.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $this->authorize('update', $leaveType);
        
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|regex:/^[A-Z]+$/',
            'description' => 'nullable|string|max:500',
            'max_days_per_year' => 'required|integer|min:1|max:365',
            'carry_forward_limit' => 'nullable|integer|min:0',
            'min_notice_days' => 'nullable|integer|min:0',
            'max_consecutive_days' => 'nullable|integer|min:0',
            'deduction_rate' => 'required|numeric|min:0|max:1',
            'applicable_roles' => 'nullable|array',
            'applicable_roles.*' => 'string|in:Employee,TeamLead,HR,Finance,Admin',
            'requires_medical_certificate' => 'boolean',
            'is_paid' => 'boolean',
            'weekend_included' => 'boolean',
            'holiday_included' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Check for duplicate code within company (excluding current record)
        $exists = LeaveType::where('company_id', $leaveType->company_id)
            ->where('code', $request->code)
            ->where('id', '!=', $leaveType->id)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Leave type code already exists for this company.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $leaveType->fill($request->all());
            $leaveType->requires_medical_certificate = $request->boolean('requires_medical_certificate');
            $leaveType->is_paid = $request->boolean('is_paid');
            $leaveType->weekend_included = $request->boolean('weekend_included');
            $leaveType->holiday_included = $request->boolean('holiday_included');
            $leaveType->is_active = $request->boolean('is_active');
            
            $leaveType->save();
            
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Leave type updated successfully!',
                    'leaveType' => $leaveType
                ]);
            }
            
            return redirect()->route('Admin.leave-types.index')
                ->with('success', 'Leave type updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update leave type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update leave type: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified leave type.
     */
    public function destroy(LeaveType $leaveType)
    {
        $this->authorize('delete', $leaveType);
        
        // Check if leave type is being used
        $hasActiveLeaves = $leaveType->leaves()->whereIn('status', ['pending', 'approved'])->exists();
        $hasBalances = $leaveType->leaveBalances()->where('total_entitled', '>', 0)->exists();
        
        if ($hasActiveLeaves || $hasBalances) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete leave type that has active applications or employee balances. Consider deactivating it instead.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Delete associated leave balances first
            $leaveType->leaveBalances()->delete();
            
            // Delete the leave type
            $leaveType->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Leave type deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete leave type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle leave type status.
     */
    public function toggleStatus(LeaveType $leaveType)
    {
        $this->authorize('update', $leaveType);
        
        try {
            $leaveType->is_active = !$leaveType->is_active;
            $leaveType->save();
            
            $status = $leaveType->is_active ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Leave type {$status} successfully!",
                'status' => $leaveType->is_active
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update leave type status.'
            ], 500);
        }
    }

    /**
     * Create default leave types for a company.
     */
    public function createDefaults(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole(['superAdmin', 'Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }
        
        $request->validate([
            'company_id' => $user->hasRole('superAdmin') ? 'required|exists:companies,id' : 'nullable'
        ]);
        
        $companyId = $user->hasRole('superAdmin') ? $request->company_id : $user->company_id;
        
        // Check if default types already exist
        $existingTypes = LeaveType::where('company_id', $companyId)->count();
        
        if ($existingTypes > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Default leave types already exist for this company.'
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            LeaveType::createDefaultForCompany($companyId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Default leave types created successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create default leave types: ' . $e->getMessage()
            ], 500);
        }
    }
}