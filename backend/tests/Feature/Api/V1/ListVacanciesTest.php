<?php

namespace Tests\Feature\Api\V1;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ListVacanciesTest extends TestCase
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

    public function test_it_returns_only_active_vacancies_by_default_in_newest_first_order(): void
    {
        $company = Company::factory()->create([
            'name' => 'Dicoding Indonesia',
            'slug' => 'dicoding-indonesia',
            'logo_path' => '/images/companies/dicoding-logo.svg',
        ]);

        $olderActiveVacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Product Engineer',
                'employment_type' => EmploymentType::FullTime->value,
                'minimum_experience' => MinimumExperience::OneToThreeYears->value,
                'expires_at' => '2026-07-27',
                'created_at' => '2026-07-25 10:00:00',
                'updated_at' => '2026-07-25 10:00:00',
            ]);

        $newerActiveVacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Backend Developer',
                'employment_type' => EmploymentType::FullTime->value,
                'minimum_experience' => MinimumExperience::OneToThreeYears->value,
                'expires_at' => '2026-08-30',
                'created_at' => '2026-07-26 10:00:00',
                'updated_at' => '2026-07-26 10:00:00',
            ]);

        Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Expired Developer',
                'expires_at' => '2026-07-26',
                'created_at' => '2026-07-27 09:00:00',
                'updated_at' => '2026-07-27 09:00:00',
            ]);

        $response = $this->getJson('/api/v1/vacancies');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath(
                'data.0.id',
                $newerActiveVacancy->id,
            )
            ->assertJsonPath(
                'data.1.id',
                $olderActiveVacancy->id,
            )
            ->assertJsonPath(
                'data.0.employment_type',
                EmploymentType::FullTime->value,
            )
            ->assertJsonPath(
                'data.0.minimum_experience',
                MinimumExperience::OneToThreeYears->value,
            )
            ->assertJsonPath(
                'data.0.company.id',
                $company->id,
            )
            ->assertJsonPath(
                'data.0.company.name',
                'Dicoding Indonesia',
            )
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'position',
                        'employment_type',
                        'employment_type_label',
                        'location',
                        'is_remote',
                        'minimum_experience',
                        'minimum_experience_label',
                        'expires_at',
                        'is_active',
                        'created_at',
                        'company' => [
                            'id',
                            'name',
                            'logo_url',
                        ],
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $firstVacancy = $response->json('data.0');

        $this->assertIsArray($firstVacancy);
        $this->assertArrayNotHasKey(
            'description',
            $firstVacancy,
        );
        $this->assertArrayNotHasKey(
            'salary_min',
            $firstVacancy,
        );
        $this->assertArrayNotHasKey(
            'salary_max',
            $firstVacancy,
        );
    }

    public function test_it_searches_vacancies_by_title_case_insensitively(): void
    {
        $company = Company::factory()->create();

        $backendDeveloper = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Backend Developer',
                'expires_at' => '2026-08-30',
                'created_at' => '2026-07-27 09:00:00',
            ]);

        $androidDeveloper = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Android Developer',
                'expires_at' => '2026-08-30',
                'created_at' => '2026-07-27 08:00:00',
            ]);

        Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Product Manager',
                'description' => '<p>Collaborate with developers.</p>',
                'expires_at' => '2026-08-30',
                'created_at' => '2026-07-27 07:00:00',
            ]);

        $response = $this->getJson(
            '/api/v1/vacancies?search=%20%20DEVELOPER%20%20',
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $vacancyIds = collect($response->json('data'))
            ->pluck('id')
            ->all();

        $this->assertSame(
            [
                $backendDeveloper->id,
                $androidDeveloper->id,
            ],
            $vacancyIds,
        );
    }

    public function test_it_filters_vacancies_by_status(): void
    {
        $company = Company::factory()->create();

        $activeVacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Active Engineer',
                'expires_at' => '2026-07-27',
            ]);

        $expiredVacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Expired Engineer',
                'expires_at' => '2026-07-26',
            ]);

        $expiredResponse = $this->getJson(
            '/api/v1/vacancies?status=expired',
        );

        $expiredResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $expiredVacancy->id,
            )
            ->assertJsonPath(
                'data.0.is_active',
                false,
            );

        $allResponse = $this->getJson(
            '/api/v1/vacancies?status=all',
        );

        $allResponse
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $allVacancyIds = collect($allResponse->json('data'))
            ->pluck('id')
            ->all();

        $this->assertContains(
            $activeVacancy->id,
            $allVacancyIds,
        );

        $this->assertContains(
            $expiredVacancy->id,
            $allVacancyIds,
        );
    }

    public function test_it_paginates_results_and_preserves_query_parameters(): void
    {
        $company = Company::factory()->create();

        Vacancy::factory()
            ->count(5)
            ->for($company)
            ->sequence(
                fn ($sequence): array => [
                    'title' => "Engineer {$sequence->index}",
                    'expires_at' => '2026-08-30',
                    'created_at' => Carbon::parse(
                        '2026-07-27 09:00:00',
                    )->subMinutes($sequence->index),
                ],
            )
            ->create();

        $response = $this->getJson(
            '/api/v1/vacancies'
            .'?search=Engineer'
            .'&status=active'
            .'&per_page=2'
            .'&page=2',
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.last_page', 3);

        $nextPageUrl = $response->json('links.next');

        $this->assertIsString($nextPageUrl);
        $this->assertStringContainsString(
            'search=Engineer',
            $nextPageUrl,
        );
        $this->assertStringContainsString(
            'status=active',
            $nextPageUrl,
        );
        $this->assertStringContainsString(
            'per_page=2',
            $nextPageUrl,
        );
        $this->assertStringContainsString(
            'page=3',
            $nextPageUrl,
        );
    }

    public function test_it_rejects_invalid_list_query_parameters(): void
    {
        $response = $this->getJson(
            '/api/v1/vacancies'
            .'?status=archived'
            .'&page=0'
            .'&per_page=51',
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'status',
                'page',
                'per_page',
            ]);
    }

    public function test_it_lists_multiple_vacancies_without_n_plus_one_queries(): void
    {
        $company = Company::factory()->create([
            'name' => 'Dicoding Indonesia',
            'slug' => 'dicoding-indonesia',
        ]);

        Vacancy::factory()
            ->count(10)
            ->for($company)
            ->create([
                'expires_at' => '2026-08-30',
            ]);

        $this->expectsDatabaseQueryCount(3);

        $response = $this->getJson(
            '/api/v1/vacancies?status=all&per_page=10',
        );

        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath(
                'data.0.company.id',
                $company->id,
            );
    }
}
