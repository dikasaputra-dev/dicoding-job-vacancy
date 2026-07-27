<?php

namespace Database\Seeders;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Company;
use Illuminate\Database\Seeder;

class VacancySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()
            ->where('slug', 'dicoding-indonesia')
            ->firstOrFail();

        $vacancies = [
            [
                'title' => 'Product Engineer',
                'position' => 'Software Engineering',
                'employment_type' => EmploymentType::FullTime->value,
                'candidate_count' => 1,
                'expires_at' => now()->addDays(30)->toDateString(),
                'location' => 'Bandung',
                'is_remote' => false,
                'description' => $this->description('Product Engineer'),
                'salary_min' => 8_000_000,
                'salary_max' => 12_000_000,
                'show_salary' => false,
                'minimum_experience' => MinimumExperience::OneToThreeYears->value,
            ],
            [
                'title' => 'Android Developer',
                'position' => 'Mobile Development',
                'employment_type' => EmploymentType::FullTime->value,
                'candidate_count' => 2,
                'expires_at' => now()->addDays(35)->toDateString(),
                'location' => 'Bandung',
                'is_remote' => false,
                'description' => $this->description('Android Developer'),
                'salary_min' => 7_000_000,
                'salary_max' => 11_000_000,
                'show_salary' => false,
                'minimum_experience' => MinimumExperience::FourToFiveYears->value,
            ],
            [
                'title' => 'iOS Developer',
                'position' => 'Mobile Development',
                'employment_type' => EmploymentType::FullTime->value,
                'candidate_count' => 1,
                'expires_at' => now()->addDays(40)->toDateString(),
                'location' => 'Bandung',
                'is_remote' => false,
                'description' => $this->description('iOS Developer'),
                'salary_min' => 7_000_000,
                'salary_max' => 11_000_000,
                'show_salary' => false,
                'minimum_experience' => MinimumExperience::OneToThreeYears->value,
            ],
            [
                'title' => 'Code Reviewer',
                'position' => 'Code Review',
                'employment_type' => EmploymentType::PartTime->value,
                'candidate_count' => 3,
                'expires_at' => now()->addDays(45)->toDateString(),
                'location' => 'Bandung',
                'is_remote' => true,
                'description' => $this->description('Code Reviewer'),
                'salary_min' => 5_000_000,
                'salary_max' => null,
                'show_salary' => false,
                'minimum_experience' => MinimumExperience::LessThanOneYear->value,
            ],
        ];

        foreach ($vacancies as $vacancy) {
            $company->vacancies()->updateOrCreate(
                [
                    'title' => $vacancy['title'],
                ],
                $vacancy,
            );
        }
    }

    private function description(string $title): string
    {
        return <<<HTML
<h2>Job Description</h2>
<p>As a {$title}, you will join the Product and Engineering team to build impactful products for Dicoding users.</p>

<p>You will collaborate with team members to deliver reliable, maintainable, and user-focused solutions.</p>

<h2>Responsibilities</h2>
<ul>
    <li>Collaborate with designers and other stakeholders when analyzing problems and solutions.</li>
    <li>Develop and maintain products used by Dicoding users.</li>
    <li>Ensure application components work correctly and efficiently.</li>
    <li>Write maintainable, well-tested, and understandable code.</li>
</ul>

<h2>Requirements</h2>
<ul>
    <li>Good understanding of web or software development fundamentals.</li>
    <li>Comfortable working with Git and collaborative development workflows.</li>
    <li>Strong willingness to learn and solve user problems.</li>
    <li>Good communication and teamwork skills.</li>
</ul>
HTML;
    }
}
