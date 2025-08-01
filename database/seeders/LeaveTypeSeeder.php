<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;
use App\Models\Company;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all companies and create default leave types for each
        $companies = Company::all();
        
        foreach ($companies as $company) {
            LeaveType::createDefaultForCompany($company->id);
        }
    }
}