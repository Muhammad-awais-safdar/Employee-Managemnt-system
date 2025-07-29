<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseUserController
{
    protected $viewPrefix = 'EmployeeManagemntsystem.Admin.Users.';
    
    public function index()
    {
        // Get allowed roles for Admin (hr, finance, TeamLead, employee)
        $allowedRoleNames = $this->getAllowedRoles()->pluck('name')->toArray();
        $roles = \Spatie\Permission\Models\Role::whereIn('name', $allowedRoleNames)->orderBy('name')->get();
        
        // Get users grouped by roles that Admin can manage (within their company)
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
        $roles = $this->getAllowedRoles();
        $company = $this->user->company ?? (object)['company_name' => 'Unknown Company'];
        
        return view($this->viewPrefix . 'create', [
            'roles' => $roles,
            'company' => $company
        ]);
    }
    
    public function store(StoreUserRequest $request)
    {
        \Log::info('Admin store method called', [
            'admin_id' => $this->user->id,
            'admin_email' => $this->user->email,
            'admin_roles' => $this->user->getRoleNames()->toArray(),
            'admin_company_id' => $this->user->company_id,
            'request_data' => $request->except(['password', '_token']),
            'request_roles' => $request->roles,
            'roles_type' => gettype($request->roles)
        ]);

        try {
            // Log before creating user
            \Log::debug('Attempting to create user', [
                'name' => $request->name,
                'email' => $request->email,
                'company_id' => $this->user->company_id
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $this->user->company_id,
            ]);

            \Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'company_id' => $user->company_id
            ]);

            // Log before syncing roles
            \Log::debug('Attempting to sync roles', [
                'user_id' => $user->id,
                'roles' => $request->roles
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
                    \Log::info('Admin assigning role', [
                        'user_id' => $user->id,
                        'role_id' => $request->role,
                        'role_name' => $role->name
                    ]);
                    $user->assignRole($role->name);
                }
            } else {
                \Log::warning('Admin store - No role provided', [
                    'role' => $request->role
                ]);
                throw new \Exception('A role must be selected');
            }
            
            \Log::info('Admin user creation completed', [
                'user_id' => $user->id,
                'admin_id' => $this->user->id
            ]);

            // Log successful completion
            \Log::info('User creation process completed', [
                'user_id' => $user->id,
                'admin_id' => $this->user->id
            ]);
            
            return redirect()->route('Admin.users.index')
                ->with('success', 'User created successfully');
                
        } catch (\Exception $e) {
            // Log the error with stack trace
            \Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', '_token']),
                'admin_id' => $this->user->id
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
            'user' => $user->load('roles')
        ]);
    }
    
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $roles = $this->getAllowedRoles();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        $company = $this->user->company ?? (object)['company_name' => 'Unknown Company'];
        
        return view($this->viewPrefix . 'edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'company' => $company
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        // Handle single role update
        if ($request->role) {
            $allowedRoleIds = $this->getAllowedRoles()->pluck('id')->toArray();
            
            if (in_array($request->role, $allowedRoleIds)) {
                $role = \Spatie\Permission\Models\Role::find($request->role);
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
            }
        }
        
        return redirect()->route('Admin.users.index')
            ->with('success', 'User updated successfully');
    }
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === $this->user->id) {
            return back()->with('error', 'You cannot delete your own account');
        }
        
        $user->delete();
        
        return redirect()->route('Admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
