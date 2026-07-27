<?php

namespace Tests\Feature\Api\V1;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpdateVacancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-07-27 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_partially_updates_a_vacancy(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [
                'title' => 'Senior Product Engineer',
                'candidate_count' => 3,
                'is_remote' => true,
                'show_salary' => false,
            ],
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'message',
                'Vacancy updated successfully.',
            )
            ->assertJsonPath(
                'data.id',
                $vacancy->id,
            )
            ->assertJsonPath(
                'data.title',
                'Senior Product Engineer',
            )
            ->assertJsonPath(
                'data.candidate_count',
                3,
            )
            ->assertJsonPath(
                'data.is_remote',
                true,
            )
            ->assertJsonPath(
                'data.show_salary',
                false,
            )
            ->assertJsonPath(
                'data.position',
                'Software Engineering',
            )
            ->assertJsonPath(
                'data.description',
                '<p>Original vacancy description.</p>',
            )
            ->assertJsonPath(
                'data.salary_min',
                8_000_000,
            )
            ->assertJsonPath(
                'data.salary_max',
                12_000_000,
            )
            ->assertJsonPath(
                'data.company.id',
                $company->id,
            )
            ->assertJsonPath(
                'data.company.name',
                'Dicoding Indonesia',
            );

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'company_id' => $company->id,
            'title' => 'Senior Product Engineer',
            'position' => 'Software Engineering',
            'candidate_count' => 3,
            'is_remote' => 1,
            'show_salary' => 0,
            'description' => '<p>Original vacancy description.</p>',
            'salary_min' => 8_000_000,
            'salary_max' => 12_000_000,
        ]);
    }

    public function test_it_can_remove_the_maximum_salary_value(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [
                'salary_max' => null,
            ],
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.salary_min',
                8_000_000,
            )
            ->assertJsonPath(
                'data.salary_max',
                null,
            );

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'salary_min' => 8_000_000,
            'salary_max' => null,
        ]);
    }

    public function test_it_rejects_an_empty_update_payload(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [],
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'vacancy',
            ])
            ->assertJsonPath(
                'errors.vacancy.0',
                'At least one vacancy field must be provided.',
            );

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'title' => 'Product Engineer',
        ]);
    }

    public function test_it_rejects_a_maximum_salary_below_the_existing_minimum(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [
                'salary_max' => 7_000_000,
            ],
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'salary_max',
            ])
            ->assertJsonPath(
                'errors.salary_max.0',
                'The salary max field must be greater than or equal to salary min.',
            );

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'salary_min' => 8_000_000,
            'salary_max' => 12_000_000,
        ]);
    }

    public function test_it_rejects_a_minimum_salary_above_the_existing_maximum(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [
                'salary_min' => 13_000_000,
            ],
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'salary_max',
            ]);

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'salary_min' => 8_000_000,
            'salary_max' => 12_000_000,
        ]);
    }

    public function test_it_rejects_server_controlled_fields(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $otherCompany = Company::factory()->create([
            'slug' => 'other-company',
        ]);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [
                'company_id' => $otherCompany->id,
            ],
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'company_id',
            ]);

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_it_returns_not_found_when_updating_an_unknown_vacancy(): void
    {
        $response = $this->patchJson(
            '/api/v1/vacancies/999999',
            [
                'title' => 'Unknown Vacancy',
            ],
        );

        $response
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Vacancy not found.',
            ]);
    }

    public function test_it_sanitizes_description_during_update(): void
    {
        $company = $this->createCompany();
        $vacancy = $this->createVacancy($company);

        $response = $this->patchJson(
            "/api/v1/vacancies/{$vacancy->id}",
            [
                'description' => <<<'HTML'
<h2 onmouseover="alert('xss')">Updated Description</h2>
<p style="color:red">
    Build <em onclick="alert('xss')">secure</em> products.
</p>
HTML,
            ],
        );

        $response->assertOk();

        $description = $response->json(
            'data.description',
        );

        $this->assertIsString($description);

        $this->assertStringContainsString(
            '<h2>Updated Description</h2>',
            $description,
        );

        $this->assertStringContainsString(
            '<em>secure</em>',
            $description,
        );

        $this->assertStringNotContainsString(
            'onmouseover',
            $description,
        );

        $this->assertStringNotContainsString(
            'onclick',
            $description,
        );

        $this->assertStringNotContainsString(
            'style=',
            $description,
        );

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancy->id,
            'description' => $description,
        ]);
    }

    private function createCompany(): Company
    {
        return Company::factory()->create([
            'name' => 'Dicoding Indonesia',
            'slug' => 'dicoding-indonesia',
            'logo_path' => '/images/companies/dicoding-logo.svg',
            'business_sector' => 'Technology',
            'employee_size' => '51-200',
            'headquarters_location' => 'Bandung',
            'website_url' => 'https://www.dicoding.com',
        ]);
    }

    private function createVacancy(
        Company $company,
    ): Vacancy {
        return Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Product Engineer',
                'position' => 'Software Engineering',
                'employment_type' => EmploymentType::FullTime->value,
                'candidate_count' => 1,
                'expires_at' => '2026-08-30',
                'location' => 'Bandung',
                'is_remote' => false,
                'description' => '<p>Original vacancy description.</p>',
                'salary_min' => 8_000_000,
                'salary_max' => 12_000_000,
                'show_salary' => true,
                'minimum_experience' => MinimumExperience::OneToThreeYears->value,
            ]);
    }
}
