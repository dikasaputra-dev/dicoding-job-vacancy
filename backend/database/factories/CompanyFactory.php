<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo_path' => null,
            'business_sector' => fake()->randomElement([
                'Technology',
                'Education',
                'Financial Technology',
                'Digital Services',
            ]),
            'employee_size' => fake()->randomElement([
                '1-10',
                '11-50',
                '51-200',
                '201-500',
            ]),
            'headquarters_location' => fake()->city(),
            'website_url' => fake()->optional()->url(),
        ];
    }
}
