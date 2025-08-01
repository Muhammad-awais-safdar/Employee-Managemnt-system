<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Department;
use App\Models\EmployeeTaxInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HrController extends BaseUserController
{
    protected $viewPrefix = 'EmployeeManagemntsystem.HR.Users.';
    
    public function index()
    {
        // Get allowed roles for HR (TeamLead and employee)
        $allowedRoleNames = $this->getAllowedRoles()->pluck('name')->toArray();
        $roles = \Spatie\Permission\Models\Role::whereIn('name', $allowedRoleNames)->orderBy('name')->get();
        
        // Get users grouped by roles that HR can manage
        $users = $this->getBaseQuery()
            ->with(['roles', 'company', 'teamLead'])
            ->get()
            ->groupBy(function($user) {
                return $user->roles->first()->name ?? 'No Role';
            });
            
        // Sort the user groups by role name
        $sortedUsers = [];
        foreach ($roles as $role) {
            if (isset($users[$role->name])) {
                $sortedUsers[$role->name] = $users[$role->name];
            }
        }
        
        // Add users with no role to the end
        if (isset($users['No Role'])) {
            $sortedUsers['No Role'] = $users['No Role'];
        }

        return view($this->viewPrefix . 'index', [
            'usersByRole' => $sortedUsers,
            'roles' => $roles,
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => true
        ]);
    }
    
    public function create()
    {
        $roles = $this->getAllowedRoles();
        $teamLeads = User::role('TeamLead')
            ->where('company_id', $this->user->company_id)
            ->pluck('name', 'id');
            
        $company = $this->user->company ?? (object)['company_name' => 'Unknown Company'];
        
        // Get departments for this company
        $departments = Department::forCompany($this->user->company_id)->active()->get();
        
        return view($this->viewPrefix . 'create', [
            'roles' => $roles,
            'teamLeads' => $teamLeads,
            'company' => $company,
            'departments' => $departments
        ]);
    }
    
    public function store(StoreUserRequest $request)
    {
        \Log::info('HR store method called', [
            'hr_id' => $this->user->id,
            'hr_email' => $this->user->email,
            'hr_roles' => $this->user->getRoleNames()->toArray(),
            'hr_company_id' => $this->user->company_id,
            'request_data' => $request->except(['password', '_token']),
            'request_roles' => $request->roles,
            'roles_type' => gettype($request->roles)
        ]);

        try {
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $this->user->company_id,
                'team_lead_id' => $request->team_lead_id,
                
                // Employee Information
                'employee_id' => $request->employee_id,
                'salary' => $request->salary,
                'date_of_joining' => $request->date_of_joining,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'qualification' => $request->qualification,
                'experience_years' => $request->experience_years,
                'skills' => $request->skills,
                'department_id' => $request->department_id,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);
            
            \Log::info('HR user created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'company_id' => $user->company_id
            ]);
            
            // Assign single role
            if ($request->role) {
                // Get allowed roles to validate against
                $allowedRoleIds = $this->getAllowedRoles()->pluck('id')->toArray();
                
                if (!in_array($request->role, $allowedRoleIds)) {
                    $allowedRoleNames = $this->getAllowedRoles()->pluck('name')->toArray();
                    throw new \Exception('Invalid role selected. Allowed roles: ' . implode(', ', $allowedRoleNames));
                }

                $role = \Spatie\Permission\Models\Role::find($request->role);
                if ($role) {
                    \Log::info('HR assigning role', [
                        'user_id' => $user->id,
                        'role_id' => $request->role,
                        'role_name' => $role->name
                    ]);
                    $user->assignRole($role->name);
                }
            } else {
                \Log::warning('HR store - No role provided', [
                    'role' => $request->role
                ]);
                throw new \Exception('A role must be selected');
            }
            
            // Create employee tax information
            EmployeeTaxInfo::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'filing_status' => $request->filing_status ?? 'single',
                'allowances' => $request->allowances ?? 0,
                'additional_withholding' => $request->additional_withholding ?? 0.00,
                'exempt_from_federal' => $request->boolean('exempt_from_federal'),
                'exempt_from_state' => $request->boolean('exempt_from_state'),
                'exempt_from_local' => $request->boolean('exempt_from_local'),
                'health_insurance_premium' => $request->health_insurance_premium ?? 0.00,
                'retirement_contribution_percent' => $request->retirement_contribution_percent ?? 0.00,
                'tax_year' => date('Y'),
                'is_active' => true,
                'effective_date' => now()
            ]);
            
            DB::commit();
            
            \Log::info('HR user creation completed', [
                'user_id' => $user->id,
                'hr_id' => $this->user->id
            ]);
            
            return redirect()->route('HR.users.index')
                ->with('success', 'Employee created successfully with salary and tax information');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating user by HR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', '_token']),
                'hr_id' => $this->user->id
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }
    
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        return view($this->viewPrefix . 'show', [
            'user' => $user->load('roles', 'teamLead')
        ]);
    }
    
    public function edit(User $user)
    {
        \Log::info('HR attempting to edit user', [
            'hr_id' => $this->user->id,
            'hr_email' => $this->user->email,
            'hr_company_id' => $this->user->company_id,
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'target_user_company_id' => $user->company_id,
            'target_user_roles' => $user->roles->pluck('name')->toArray()
        ]);
        
        try {
            $this->authorize('update', $user);
            \Log::info('HR authorization successful for edit');
        } catch (\Exception $e) {
            \Log::error('HR authorization failed for edit', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        $roles = $this->getAllowedRoles();
        $userRoles = $user->roles->pluck('name')->toArray();
        $teamLeads = User::role('TeamLead')
            ->where('company_id', $this->user->company_id)
            ->pluck('name', 'id');
        
        $company = $this->user->company ?? (object)['company_name' => 'Unknown Company'];
        
        return view($this->viewPrefix . 'edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'teamLeads' => $teamLeads,
            'company' => $company
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user)
    {
        \Log::info('HR attempting to update user', [
            'hr_id' => $this->user->id,
            'target_user_id' => $user->id,
            'request_data' => $request->except(['password', '_token'])
        ]);
        
        try {
            $this->authorize('update', $user);
            \Log::info('HR authorization successful for update');
        } catch (\Exception $e) {
            \Log::error('HR authorization failed for update', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'team_lead_id' => $request->team_lead_id,
            ];
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            
            $user->update($data);
            \Log::info('HR user data updated successfully', ['user_id' => $user->id]);
            
            // Handle single role update
            if ($request->role) {
                $allowedRoleIds = $this->getAllowedRoles()->pluck('id')->toArray();
                
                if (in_array($request->role, $allowedRoleIds)) {
                    $role = \Spatie\Permission\Models\Role::find($request->role);
                    if ($role) {
                        $user->syncRoles([$role->name]);
                        \Log::info('HR role updated successfully', [
                            'user_id' => $user->id,
                            'new_role' => $role->name
                        ]);
                    }
                } else {
                    \Log::warning('HR attempted to assign invalid role', [
                        'requested_role_id' => $request->role,
                        'allowed_role_ids' => $allowedRoleIds
                    ]);
                }
            }
            
            return redirect()->route('HR.users.index')
                ->with('success', 'User updated successfully');
                
        } catch (\Exception $e) {
            \Log::error('HR user update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === $this->user->id) {
            return back()->with('error', 'You cannot delete your own account');
        }
        
        $user->delete();
        
        return redirect()->route('hr.users.index')
            ->with('success', 'User deleted successfully');
    }
}
