<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // STEP 1: Define permissions
        $permissions = [
            // User Management
            'create users',
            'view users',
            'edit users',
            'delete users',
            
            // Super Admin
            'manage companies',
            'manage plans',
            'view billing',
            'global config',
            'create superAdmins',
            'create admins',

            // Admin
            'manage employees',
            'manage departments',
            'view company data',
            'create hr',
            'create teamLead',
            'create finance',
            'create employee',

            // HR
            'onboard employee',
            'offboard employee',
            'manage documents',
            'manage attendance',
            'manage leave',
            'create teamLead from hr',
            'create employee from hr',

            // Team Lead
            'assign tasks',
            'track tasks',
            'give feedback',
            'manage team members',

            // Finance
            'manage payroll',
            'deduct salary',
            'release salary',

            // Employee
            'mark attendance',
            'view tasks',
            'apply leave',
            'receive messages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // STEP 2: Create roles and assign permissions
        $roles = [
            'superAdmin' => [
                'create users',
                'view users',
                'edit users',
                'delete users',
                'manage companies',
                'manage plans',
                'view billing',
                'global config',
                'create superAdmins',
                'create admins',
            ],
            'Admin' => [
                'create users',
                'view users',
                'edit users',
                'delete users',
                'manage employees',
                'manage departments',
                'view company data',
                'create hr',
                'create teamLead',
                'create finance',
                'create employee',
            ],
            'HR' => [
                'create users',
                'view users',
                'edit users',
                'delete users',
                'onboard employee',
                'offboard employee',
                'manage documents',
                'manage attendance',
                'manage leave',
                'create teamLead from hr',
                'create employee from hr',
            ],
            'TeamLead' => [
                'view users',
                'edit users',
                'delete users',
                'assign tasks',
                'track tasks',
                'give feedback',
                'manage team members',
            ],
            'Finance' => [
                'manage payroll',
                'deduct salary',
                'release salary',
            ],
            'Employee' => [
                'mark attendance',
                'view tasks',
                'apply leave',
                'receive messages',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }

        // STEP 3: Create one user per role and assign role
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@gmail.com', 'role' => 'superAdmin'],
            ['name' => 'Company Admin', 'email' => 'admin@gmail.com', 'role' => 'Admin'],
            ['name' => 'HR User', 'email' => 'hr@gmail.com', 'role' => 'HR'],
            ['name' => 'Team Lead', 'email' => 'teamlead@gmail.com', 'role' => 'TeamLead'],
            ['name' => 'Finance User', 'email' => 'finance@gmail.com', 'role' => 'Finance'],
            ['name' => 'Employee User', 'email' => 'employee@gmail.com', 'role' => 'Employee'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('12345678'), // Default password
                ]
            );

            $user->assignRole($userData['role']);
        }



        User::factory()
            ->count(2)
            ->create([
                'password' => Hash::make('password'),
            ])
            ->each(fn($user) => $user->assignRole('superAdmin'));

        // Admins + their teams
        $admins = User::factory()
            ->count(5)
            ->create([
                'password' => Hash::make('password'),
            ]);

        foreach ($admins as $admin) {
            $admin->assignRole('Admin');

            // HR
            $hr = User::factory()->create([
                'password' => Hash::make('password'),
            ]);
            $hr->assignRole('HR');

            // Team Lead
            $teamLead = User::factory()->create([
                'password' => Hash::make('password'),
            ]);
            $teamLead->assignRole('TeamLead');

            // Finance (Dual role: Finance + Employee)
            $finance = User::factory()->create([
                'password' => Hash::make('password'),
            ]);
            $finance->assignRole('Finance');
            $finance->assignRole('Employee');

            // Employees under this Team Lead
            $employees = User::factory()
                ->count(5)
                ->create([
                    'team_lead_id' => $teamLead->id,
                    'password' => Hash::make('password'),
                ]);

            foreach ($employees as $employee) {
                $employee->assignRole('Employee');
            }
        }
    }
}
