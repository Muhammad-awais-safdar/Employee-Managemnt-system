<?php

namespace App\Http\Controllers\UserManagement;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\UserManagement\BaseUserController;

class SuperAdminController extends BaseUserController
{
    protected $viewPrefix = 'EmployeeManagemntsystem.SuperAdmin.Users.';
    
    public function index()
    {
        // Get all roles to create tabs
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        
        // Get all users with their roles and company
        $users = $this->getBaseQuery()
            ->with(['roles', 'company'])
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
        $companies = Company::all();
        $roles = $this->getAllowedRoles();
        
        return view($this->viewPrefix . 'create', [
            'companies' => $companies,
            'roles' => $roles,
            'canAssignCompanies' => true
        ]);
    }
    
    public function store(StoreUserRequest $request)
    {
        \Log::info('SuperAdmin store - Raw request data', [
            'all_data' => $request->all(),
            'roles' => $request->roles,
            'roles_type' => gettype($request->roles),
            'roles_count' => is_array($request->roles) ? count($request->roles) : 'not_array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'team_lead_id' => $request->team_lead_id,
            'department_id' => $request->department_id,
        ]);
        
        // Assign single role
        if ($request->role) {
            $role = \Spatie\Permission\Models\Role::find($request->role);
            if ($role) {
                \Log::info('SuperAdmin store - Assigning role', [
                    'role_id' => $request->role,
                    'role_name' => $role->name
                ]);
                $user->assignRole($role->name);
            } else {
                \Log::warning('SuperAdmin store - Invalid role ID', [
                    'role_id' => $request->role
                ]);
            }
        } else {
            \Log::warning('SuperAdmin store - No role provided', [
                'role' => $request->role
            ]);
        }
        
        return redirect()->route('superAdmin.users.index')
            ->with('success', 'User created successfully');
    }
    
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        return view($this->viewPrefix . 'show', [
            'user' => $user->load('roles', 'company')
        ]);
    }
    
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $companies = Company::all();
        $roles = $this->getAllowedRoles();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return view($this->viewPrefix . 'edit', [
            'user' => $user,
            'companies' => $companies,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'canAssignCompanies' => true
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company_id,
            'team_lead_id' => $request->team_lead_id,
            'department_id' => $request->department_id,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // Update user details
        $user->update($data);
        
        // Handle single role update
        if ($request->role) {
            $role = \Spatie\Permission\Models\Role::find($request->role);
            if ($role) {
                // Get current role
                $currentRole = $user->getRoleNames()->first();
                
                // Only update if role has changed
                if ($currentRole !== $role->name) {
                    $user->syncRoles([$role->name]);
                }
            }
        }

        return redirect()->route('superAdmin.users.index')->with('success', 'User updated successfully');
    }
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        // Prevent deleting yourself
        if ($user->id === $this->user->id) {
            return back()->with('error', 'You cannot delete your own account');
        }
        
        $user->delete();
        
        return redirect()->route('superAdmin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
