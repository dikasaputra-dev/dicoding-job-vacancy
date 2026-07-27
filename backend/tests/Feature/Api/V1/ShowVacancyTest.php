<?php

namespace Tests\Feature\Api\V1;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ShowVacancyTest extends TestCase
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

    public function test_it_returns_vacancy_details_with_company_information(): void
    {
        $company = Company::factory()->create([
            'name' => 'Dicoding Indonesia',
            'slug' => 'dicoding-indonesia',
            'logo_path' => '/images/companies/dicoding-logo.svg',
            'business_sector' => 'Technology',
            'employee_size' => '51-200',
            'headquarters_location' => 'Bandung',
            'website_url' => 'https://www.dicoding.com',
        ]);

        $vacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Product Engineer',
                'position' => 'Software Engineering',
                'employment_type' => EmploymentType::FullTime->value,
                'candidate_count' => 2,
                'expires_at' => '2026-08-30',
                'location' => 'Bandung',
                'is_remote' => false,
                'description' => '<h2>Job Description</h2><p>Build impactful products.</p>',
                'salary_min' => 8_000_000,
                'salary_max' => 12_000_000,
                'show_salary' => true,
                'minimum_experience' => MinimumExperience::OneToThreeYears->value,
            ]);

        $response = $this->getJson(
            "/api/v1/vacancies/{$vacancy->id}",
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $vacancy->id)
            ->assertJsonPath(
                'data.title',
                'Product Engineer',
            )
            ->assertJsonPath(
                'data.position',
                'Software Engineering',
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
                '2026-08-30',
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
                false,
            )
            ->assertJsonPath(
                'data.description',
                '<h2>Job Description</h2><p>Build impactful products.</p>',
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
                'data.minimum_experience_label',
                '1-3 tahun',
            )
            ->assertJsonPath(
                'data.company.id',
                $company->id,
            )
            ->assertJsonPath(
                'data.company.name',
                'Dicoding Indonesia',
            )
            ->assertJsonPath(
                'data.company.slug',
                'dicoding-indonesia',
            )
            ->assertJsonPath(
                'data.company.logo_url',
                asset('images/companies/dicoding-logo.svg'),
            )
            ->assertJsonPath(
                'data.company.business_sector',
                'Technology',
            )
            ->assertJsonPath(
                'data.company.employee_size',
                '51-200',
            )
            ->assertJsonPath(
                'data.company.headquarters_location',
                'Bandung',
            )
            ->assertJsonPath(
                'data.company.website_url',
                'https://www.dicoding.com',
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
            ]);

        $responseData = $response->json('data');
        $companyData = $response->json('data.company');

        $this->assertIsArray($responseData);
        $this->assertIsArray($companyData);

        $this->assertArrayNotHasKey(
            'company_id',
            $responseData,
        );

        $this->assertArrayNotHasKey(
            'logo_path',
            $companyData,
        );
    }

    public function test_it_returns_expired_vacancy_details_with_inactive_status(): void
    {
        $company = Company::factory()->create();

        $vacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Expired Product Engineer',
                'expires_at' => '2026-07-26',
            ]);

        $response = $this->getJson(
            "/api/v1/vacancies/{$vacancy->id}",
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $vacancy->id,
            )
            ->assertJsonPath(
                'data.expires_at',
                '2026-07-26',
            )
            ->assertJsonPath(
                'data.is_active',
                false,
            );
    }

    public function test_it_returns_a_consistent_json_response_when_vacancy_is_not_found(): void
    {
        $response = $this->getJson(
            '/api/v1/vacancies/999999',
        );

        $response
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Vacancy not found.',
            ]);
    }
}
