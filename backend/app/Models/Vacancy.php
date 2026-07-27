<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Enums\VacancyStatusFilter;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'company_id',
    'title',
    'position',
    'employment_type',
    'candidate_count',
    'expires_at',
    'location',
    'is_remote',
    'description',
    'salary_min',
    'salary_max',
    'show_salary',
    'minimum_experience',
])]
class Vacancy extends Model
{
    /** @use HasFactory<VacancyFactory> */
    use HasFactory;

    /**
     * Get the company that owns the vacancy.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Determine whether the vacancy is still active.
     */
    public function isActive(): bool
    {
        return $this->expires_at->greaterThanOrEqualTo(
            Carbon::today(),
        );
    }

    /**
     * Scope the query to vacancies matching the given title.
     *
     * @param  Builder<Vacancy>  $query
     */
    #[Scope]
    protected function searchByTitle(
        Builder $query,
        ?string $searchTerm,
    ): void {
        if ($searchTerm === null) {
            return;
        }

        $query->whereLike(
            'title',
            "%{$searchTerm}%",
        );
    }

    /**
     * Scope the query using the selected status filter.
     *
     * @param  Builder<Vacancy>  $query
     */
    #[Scope]
    protected function filterByStatus(
        Builder $query,
        VacancyStatusFilter $status,
    ): void {
        $today = Carbon::today()->toDateString();

        switch ($status) {
            case VacancyStatusFilter::Active:
                $query->where('expires_at', '>=', $today);

                break;

            case VacancyStatusFilter::Expired:
                $query->where('expires_at', '<', $today);

                break;

            case VacancyStatusFilter::All:
                break;
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'candidate_count' => 'integer',
            'expires_at' => 'date:Y-m-d',
            'is_remote' => 'boolean',
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'show_salary' => 'boolean',
            'minimum_experience' => MinimumExperience::class,
        ];
    }
}
