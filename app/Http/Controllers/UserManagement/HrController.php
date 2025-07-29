<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HrController extends BaseUserController
{
    protected $viewPrefix = 'EmployeeManagemntsystem.HR.Users.';
    
    public function index()
    {
        $users = $this->getBaseQuery()
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->orderBy('roles.name', 'desc')
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);
            
        return view($this->viewPrefix . 'index', [
            'users' => $users,
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => true
        ]);
    }
    
    public function create()
    {
        $roles = $this->getAllowedRoles();
        $teamLeads = User::role('team_lead')
            ->where('company_id', $this->user->company_id)
            ->pluck('name', 'id');
            
        return view($this->viewPrefix . 'create', [
            'roles' => $roles,
            'teamLeads' => $teamLeads,
            'company' => $this->user->company
        ]);
    }
    
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $this->user->company_id,
            'team_lead_id' => $request->team_lead_id,
        ]);
        
        $user->syncRoles($request->roles);
        
        return redirect()->route('hr.users.index')
            ->with('success', 'User created successfully');
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
        $this->authorize('update', $user);
        
        $roles = $this->getAllowedRoles();
        $userRoles = $user->roles->pluck('name')->toArray();
        $teamLeads = User::role('team_lead')
            ->where('company_id', $this->user->company_id)
            ->pluck('name', 'id');
        
        return view($this->viewPrefix . 'edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'teamLeads' => $teamLeads,
            'company' => $this->user->company
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'team_lead_id' => $request->team_lead_id,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        $user->syncRoles($request->roles);
        
        return redirect()->route('hr.users.index')
            ->with('success', 'User updated successfully');
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
