<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TeamLeadController extends BaseUserController
{
    protected $viewPrefix = 'EmployeeManagemntsystem.TeamLead.Users.';
    
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
            'canCreate' => false, // Team leads can't create users
            'canEdit' => true,
            'canDelete' => true
        ]);
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
        
        return view($this->viewPrefix . 'edit', [
            'user' => $user
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
        
        return redirect()->route('teamlead.users.index')
            ->with('success', 'Team member updated successfully');
    }
    
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === $this->user->id) {
            return back()->with('error', 'You cannot delete your own account');
        }
        
        // Team leads can only soft delete team members
        $user->delete();
        
        return redirect()->route('teamlead.users.index')
            ->with('success', 'Team member removed successfully');
    }
}
