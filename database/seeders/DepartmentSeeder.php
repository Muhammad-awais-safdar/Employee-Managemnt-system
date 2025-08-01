<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some departments with full contact information
        Department::factory()->withContact()->create([
            'name' => 'Human Resources',
            'description' => 'Responsible for employee relations, recruitment, training, and organizational development.',
            'status' => true,
        ]);

        Department::factory()->withContact()->create([
            'name' => 'Information Technology',
            'description' => 'Manages all technology infrastructure, software development, and technical support.',
            'status' => true,
        ]);

        Department::factory()->withContact()->create([
            'name' => 'Finance & Accounting',
            'description' => 'Handles financial planning, budgeting, accounting, and financial reporting.',
            'status' => true,
        ]);

        Department::factory()->withContact()->create([
            'name' => 'Marketing & Sales',
            'description' => 'Drives marketing campaigns, customer acquisition, and sales activities.',
            'status' => true,
        ]);

        // Create additional departments using factory defaults
        Department::factory(3)->create();

        // Create one inactive department
        Department::factory()->inactive()->create([
            'name' => 'Legacy Operations',
            'description' => 'Department currently being restructured and temporarily inactive.',
        ]);

        $this->command->info('Successfully created ' . Department::count() . ' departments.');
    }
}