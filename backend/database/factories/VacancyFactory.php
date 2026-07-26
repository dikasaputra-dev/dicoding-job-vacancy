<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $minimumSalary = fake()->numberBetween(4_000_000, 12_000_000);

        /** @var EmploymentType $employmentType */
        $employmentType = fake()->randomElement(EmploymentType::cases());

        /** @var MinimumExperience $minimumExperience */
        $minimumExperience = fake()->randomElement(MinimumExperience::cases());

        return [
            'company_id' => Company::factory(),
            'title' => fake()->jobTitle(),
            'position' => fake()->randomElement([
                'Software Engineering',
                'Mobile Development',
                'Frontend Development',
                'Backend Development',
                'Code Review',
            ]),
            'employment_type' => $employmentType->value,
            'candidate_count' => fake()->numberBetween(1, 10),
            'expires_at' => fake()
                ->dateTimeBetween('+1 week', '+3 months')
                ->format('Y-m-d'),
            'location' => fake()->city(),
            'is_remote' => fake()->boolean(30),
            'description' => sprintf(
                '<h2>Job Description</h2><p>%s</p><h2>Responsibilities</h2><ul><li>%s</li><li>%s</li></ul>',
                fake()->paragraph(),
                fake()->sentence(),
                fake()->sentence(),
            ),
            'salary_min' => $minimumSalary,
            'salary_max' => $minimumSalary
                + fake()->numberBetween(1_000_000, 5_000_000),
            'show_salary' => fake()->boolean(),
            'minimum_experience' => $minimumExperience->value,
        ];
    }
}
