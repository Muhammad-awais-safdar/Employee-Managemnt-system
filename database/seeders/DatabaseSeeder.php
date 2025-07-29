<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolePermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
            RolePermissionSeeder::class,
       ]);


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
