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
            // Super Admin
            'manage companies',
            'manage plans',
            'view billing',
            'global config',

            // Admin
            'manage employees',
            'manage departments',
            'view company data',

            // HR
            'onboard employee',
            'offboard employee',
            'manage documents',
            'manage attendance',
            'manage leave',

            // Team Lead
            'assign tasks',
            'track tasks',
            'give feedback',

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
                'manage companies',
                'manage plans',
                'view billing',
                'global config',
            ],
            'admin' => [
                'manage employees',
                'manage departments',
                'view company data',
            ],
            'hr' => [
                'onboard employee',
                'offboard employee',
                'manage documents',
                'manage attendance',
                'manage leave',
            ],
            'teamLead' => [
                'assign tasks',
                'track tasks',
                'give feedback',
            ],
            'finance' => [
                'manage payroll',
                'deduct salary',
                'release salary',
            ],
            'employee' => [
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
            ['name' => 'Company Admin', 'email' => 'admin@gmail.com', 'role' => 'admin'],
            ['name' => 'HR User', 'email' => 'hr@gmail.com', 'role' => 'hr'],
            ['name' => 'Team Lead', 'email' => 'teamlead@gmail.com', 'role' => 'teamLead'],
            ['name' => 'Finance User', 'email' => 'finance@gmail.com', 'role' => 'finance'],
            ['name' => 'Employee User', 'email' => 'employee@gmail.com', 'role' => 'employee'],
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
    }
}
