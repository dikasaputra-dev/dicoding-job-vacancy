<?php

namespace Tests\Feature\Api\V1;

use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteVacancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_a_vacancy_and_returns_no_content(): void
    {
        $company = Company::factory()->create([
            'name' => 'Dicoding Indonesia',
            'slug' => 'dicoding-indonesia',
        ]);

        $vacancy = Vacancy::factory()
            ->for($company)
            ->create([
                'title' => 'Product Engineer',
            ]);

        $response = $this->deleteJson(
            "/api/v1/vacancies/{$vacancy->id}",
        );

        $response->assertNoContent();

        $this->assertDatabaseMissing('vacancies', [
            'id' => $vacancy->id,
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);

        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('vacancies', 0);
    }

    public function test_it_returns_not_found_when_deleting_an_unknown_vacancy(): void
    {
        $response = $this->deleteJson(
            '/api/v1/vacancies/999999',
        );

        $response
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Vacancy not found.',
            ]);
    }
}
