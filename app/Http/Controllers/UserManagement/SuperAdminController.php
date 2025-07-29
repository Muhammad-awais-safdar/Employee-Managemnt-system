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
        $users = $this->getBaseQuery()
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->orderBy('roles.name', 'desc')
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);
            

            // return $users;
        return view($this->viewPrefix . 'index', [
            'users' => $users,
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
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
        ]);
        
        $user->syncRoles($request->roles);
        
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
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // Update user details
        $user->update($data);
        
        // Get the selected roles from the request
        $selectedRoles = $request->roles ?? [];
        
        // Ensure it's an array
        if (is_string($selectedRoles)) {
            $selectedRoles = [$selectedRoles];
        }
        
        // Get the user's current roles
        $currentRoles = $user->getRoleNames()->toArray();
        
        // Only update roles if they've changed
        if (array_diff($selectedRoles, $currentRoles) || array_diff($currentRoles, $selectedRoles)) {
            // Use syncRoles to update the roles for this specific user only
            $user->syncRoles($selectedRoles);
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
