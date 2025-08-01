<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Human Resources',
                'Information Technology',
                'Finance & Accounting',
                'Marketing & Sales',
                'Operations',
                'Customer Support',
                'Research & Development',
                'Product Management',
                'Quality Assurance',
                'Legal & Compliance'
            ]),
            'description' => $this->faker->boolean(70) ? $this->faker->paragraph(3) : null,
            'status' => $this->faker->boolean(90), // 90% chance of being active
            'location' => $this->faker->boolean(60) ? $this->faker->randomElement([
                'Building A - Floor 1',
                'Building A - Floor 2',
                'Building B - Floor 1',
                'Building B - Floor 3',
                'Remote',
                'Main Office',
                'Branch Office',
                'Co-working Space'
            ]) : null,
            'phone' => $this->faker->boolean(50) ? $this->faker->phoneNumber() : null,
            'email' => $this->faker->boolean(40) ? $this->faker->companyEmail() : null,
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Indicate that the department is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the department has full contact information.
     */
    public function withContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'location' => $this->faker->randomElement([
                'Building A - Floor 1',
                'Building A - Floor 2',
                'Building B - Floor 1',
                'Building B - Floor 3',
                'Main Office',
                'Branch Office'
            ]),
        ]);
    }
}