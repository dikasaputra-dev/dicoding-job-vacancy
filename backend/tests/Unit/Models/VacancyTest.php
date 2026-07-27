<?php

namespace Tests\Unit\Models;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Vacancy;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class VacancyTest extends TestCase
{
    public function test_it_casts_vacancy_attributes_to_expected_types(): void
    {
        $vacancy = new Vacancy([
            'company_id' => 1,
            'title' => 'Product Engineer',
            'position' => 'Software Engineering',
            'employment_type' => EmploymentType::FullTime->value,
            'candidate_count' => '2',
            'expires_at' => '2026-08-30',
            'location' => 'Bandung',
            'is_remote' => 1,
            'description' => '<p>Job description</p>',
            'salary_min' => '8000000',
            'salary_max' => null,
            'show_salary' => 0,
            'minimum_experience' => MinimumExperience::OneToThreeYears->value,
        ]);

        $this->assertSame(
            EmploymentType::FullTime,
            $vacancy->employment_type,
        );

        $this->assertSame(
            MinimumExperience::OneToThreeYears,
            $vacancy->minimum_experience,
        );

        $this->assertSame(2, $vacancy->candidate_count);
        $this->assertTrue($vacancy->is_remote);
        $this->assertSame(8_000_000, $vacancy->salary_min);
        $this->assertNull($vacancy->salary_max);
        $this->assertFalse($vacancy->show_salary);

        $this->assertSame(
            '2026-08-30',
            $vacancy->expires_at->format('Y-m-d'),
        );
    }

    public function test_employment_type_provides_a_user_facing_label(): void
    {
        $this->assertSame(
            'Full-Time',
            EmploymentType::FullTime->label(),
        );

        $this->assertSame(
            'Kontrak',
            EmploymentType::Contract->label(),
        );
    }

    public function test_minimum_experience_provides_a_user_facing_label(): void
    {
        $this->assertSame(
            'Kurang dari 1 tahun',
            MinimumExperience::LessThanOneYear->label(),
        );

        $this->assertSame(
            '1-3 tahun',
            MinimumExperience::OneToThreeYears->label(),
        );
    }

    public function test_vacancy_is_active_until_its_expiration_date(): void
    {
        Carbon::setTestNow('2026-07-27 10:00:00');

        try {
            $activeToday = new Vacancy([
                'expires_at' => '2026-07-27',
            ]);

            $activeInTheFuture = new Vacancy([
                'expires_at' => '2026-08-01',
            ]);

            $expired = new Vacancy([
                'expires_at' => '2026-07-26',
            ]);

            $this->assertTrue($activeToday->isActive());
            $this->assertTrue($activeInTheFuture->isActive());
            $this->assertFalse($expired->isActive());
        } finally {
            Carbon::setTestNow();
        }
    }
}
