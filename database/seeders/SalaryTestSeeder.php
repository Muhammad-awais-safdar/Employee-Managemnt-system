<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\SalaryIncrementRequest;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SalaryTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin first
        $superAdminRole = Role::firstOrCreate(['name' => 'superAdmin']);
        
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'company_id' => null,
                'status' => 1
            ]
        );
        if (!$superAdmin->hasRole('superAdmin')) {
            $superAdmin->assignRole('superAdmin');
        }
        
        // Get or create test company
        $company = Company::firstOrCreate(
            ['company_name' => 'Test Company'],
            [
                'contact_person' => 'Test Manager',
                'email' => 'test@company.com',
                'phone' => '1234567890',
                'address' => '123 Test St',
                'city' => 'Test City',
                'state' => 'Test State',
                'country' => 'Test Country',
                'postal_code' => '12345',
                'website' => 'https://test.com',
                'logo' => null,
                'status' => 1,
                'notes' => 'Test company for salary management',
                'user_id' => $superAdmin->id
            ]
        );

        // Get or create test department
        $department = Department::firstOrCreate(
            ['name' => 'IT Department', 'company_id' => $company->id],
            ['description' => 'Information Technology Department']
        );

        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $hrRole = Role::firstOrCreate(['name' => 'Hr']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        // Create test admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'department_id' => $department->id,
                'salary' => 80000.00,
                'status' => 1
            ]
        );
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        // Create test HR user
        $hr = User::updateOrCreate(
            ['email' => 'hr@test.com'],
            [
                'name' => 'Test HR',
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'department_id' => $department->id,
                'salary' => 60000.00,
                'status' => 1
            ]
        );
        if (!$hr->hasRole('Hr')) {
            $hr->assignRole('Hr');
        }

        // Create test employees with different salaries
        $employees = [
            ['name' => 'John Doe', 'email' => 'john@test.com', 'salary' => 45000.00],
            ['name' => 'Jane Smith', 'email' => 'jane@test.com', 'salary' => 50000.00],
            ['name' => 'Mike Johnson', 'email' => 'mike@test.com', 'salary' => 42000.00],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@test.com', 'salary' => 48000.00],
        ];

        foreach ($employees as $empData) {
            $employee = User::updateOrCreate(
                ['email' => $empData['email']],
                [
                    'name' => $empData['name'],
                    'password' => bcrypt('password'),
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'salary' => $empData['salary'],
                    'status' => 1
                ]
            );
            if (!$employee->hasRole('Employee')) {
                $employee->assignRole('Employee');
            }
        }

        // Create some test increment requests
        $employees = User::where('company_id', $company->id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'Employee');
            })
            ->get();

        foreach ($employees->take(2) as $employee) {
            $currentSalary = $employee->salary;
            $requestedSalary = $currentSalary * 1.15; // 15% increase
            $incrementAmount = $requestedSalary - $currentSalary;
            $incrementPercentage = ($incrementAmount / $currentSalary) * 100;

            SalaryIncrementRequest::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'requested_by' => $hr->id,
                    'company_id' => $company->id
                ],
                [
                    'current_salary' => $currentSalary,
                    'requested_salary' => $requestedSalary,
                    'increment_amount' => $incrementAmount,
                    'increment_percentage' => round($incrementPercentage, 2),
                    'reason' => 'Performance-based increment for excellent work and contributions to the team.',
                    'status' => 'pending'
                ]
            );
        }

        $this->command->info('Test data for salary management created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@test.com / password');
        $this->command->info('HR: hr@test.com / password');
        $this->command->info('Employees: john@test.com, jane@test.com, mike@test.com, sarah@test.com / password');
    }
}