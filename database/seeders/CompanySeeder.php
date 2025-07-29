<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all users who have the 'Admin' role
        $adminUsers = User::role('Admin')->pluck('id')->toArray();

        if (empty($adminUsers)) {
            $this->command->warn('No Admin users found. Skipping company seeding.');
            return;
        }

        // Create 20 companies, randomly assigned to admin users
        foreach (range(1, 20) as $i) {
            Company::create([
                'company_name'   => $faker->company,
                'contact_person' => $faker->name,
                'email'          => $faker->unique()->companyEmail,
                'phone'          => $faker->phoneNumber,
                'website'        => $faker->url,
                'logo'           => null, // Or use a default/logo.png
                'address'        => $faker->address,
                'city'           => $faker->city,
                'state'          => $faker->state,
                'country'        => $faker->country,
                'postal_code'    => $faker->postcode,
                'status'         => $faker->randomElement(['active', 'inactive']),
                'notes'          => $faker->optional()->paragraph,
                'user_id'        => $faker->randomElement($adminUsers),
            ]);
        }
    }
}
