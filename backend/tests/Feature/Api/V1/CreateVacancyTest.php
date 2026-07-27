<?php

namespace Tests\Feature\Api\V1;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CreateVacancyTest extends TestCase
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

    public function test_it_creates_a_vacancy_for_the_default_company(): void
    {
        $company = $this->createDefaultCompany();

        $response = $this->postJson(
            '/api/v1/vacancies',
            $this->validPayload(),
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'message',
                'Vacancy created successfully.',
            )
            ->assertJsonPath(
                'data.title',
                'Backend Developer',
            )
            ->assertJsonPath(
                'data.position',
                'Backend Development',
            )
            ->assertJsonPath(
                'data.employment_type',
                EmploymentType::FullTime->value,
            )
            ->assertJsonPath(
                'data.employment_type_label',
                'Full-Time',
            )
            ->assertJsonPath(
                'data.candidate_count',
                2,
            )
            ->assertJsonPath(
                'data.expires_at',
                '2026-09-30',
            )
            ->assertJsonPath(
                'data.is_active',
                true,
            )
            ->assertJsonPath(
                'data.location',
                'Bandung',
            )
            ->assertJsonPath(
                'data.is_remote',
                true,
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
                'data.show_salary',
                true,
            )
            ->assertJsonPath(
                'data.minimum_experience',
                MinimumExperience::OneToThreeYears->value,
            )
            ->assertJsonPath(
                'data.company.id',
                $company->id,
            )
            ->assertJsonPath(
                'data.company.name',
                'Dicoding Indonesia',
            )
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'position',
                    'employment_type',
                    'employment_type_label',
                    'candidate_count',
                    'expires_at',
                    'is_active',
                    'location',
                    'is_remote',
                    'description',
                    'salary_min',
                    'salary_max',
                    'show_salary',
                    'minimum_experience',
                    'minimum_experience_label',
                    'created_at',
                    'updated_at',
                    'company' => [
                        'id',
                        'name',
                        'slug',
                        'logo_url',
                        'business_sector',
                        'employee_size',
                        'headquarters_location',
                        'website_url',
                    ],
                ],
                'message',
            ]);

        $vacancyId = $response->json('data.id');

        $this->assertIsInt($vacancyId);

        $response->assertHeader(
            'Location',
            route(
                'api.v1.vacancies.show',
                $vacancyId,
            ),
        );

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancyId,
            'company_id' => $company->id,
            'title' => 'Backend Developer',
            'position' => 'Backend Development',
            'employment_type' => EmploymentType::FullTime->value,
            'candidate_count' => 2,
            'expires_at' => '2026-09-30',
            'location' => 'Bandung',
            'is_remote' => 1,
            'salary_min' => 8_000_000,
            'salary_max' => 12_000_000,
            'show_salary' => 1,
            'minimum_experience' => MinimumExperience::OneToThreeYears->value,
        ]);

        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('vacancies', 1);
    }

    public function test_it_uses_database_defaults_for_optional_fields(): void
    {
        $company = $this->createDefaultCompany();

        $payload = $this->validPayload();

        unset(
            $payload['is_remote'],
            $payload['salary_max'],
            $payload['show_salary'],
        );

        $response = $this->postJson(
            '/api/v1/vacancies',
            $payload,
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.is_remote',
                false,
            )
            ->assertJsonPath(
                'data.salary_max',
                null,
            )
            ->assertJsonPath(
                'data.show_salary',
                false,
            )
            ->assertJsonPath(
                'data.company.id',
                $company->id,
            );

        $vacancyId = $response->json('data.id');

        $this->assertDatabaseHas('vacancies', [
            'id' => $vacancyId,
            'company_id' => $company->id,
            'is_remote' => 0,
            'salary_max' => null,
            'show_salary' => 0,
        ]);
    }

    public function test_it_rejects_invalid_and_server_controlled_fields(): void
    {
        $this->createDefaultCompany();

        $payload = $this->validPayload();

        $payload['company_id'] = 999;
        $payload['employment_type'] = 'freelance';
        $payload['candidate_count'] = 0;
        $payload['expires_at'] = '2026-07-26';
        $payload['salary_min'] = 12_000_000;
        $payload['salary_max'] = 8_000_000;
        $payload['minimum_experience'] = 'expert';

        $response = $this->postJson(
            '/api/v1/vacancies',
            $payload,
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'company_id',
                'employment_type',
                'candidate_count',
                'expires_at',
                'salary_max',
                'minimum_experience',
            ]);

        $this->assertDatabaseCount('vacancies', 0);
    }

    /**
     * Create the company expected by the vacancy endpoint.
     */
    private function createDefaultCompany(): Company
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

    /**
     * Get a valid create vacancy payload.
     *
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'title' => 'Backend Developer',
            'position' => 'Backend Development',
            'employment_type' => EmploymentType::FullTime->value,
            'candidate_count' => 2,
            'expires_at' => '2026-09-30',
            'location' => 'Bandung',
            'is_remote' => true,
            'description' => '<h2>Job Description</h2><p>Develop and maintain backend services.</p>',
            'salary_min' => 8_000_000,
            'salary_max' => 12_000_000,
            'show_salary' => true,
            'minimum_experience' => MinimumExperience::OneToThreeYears->value,
        ];
    }
}
