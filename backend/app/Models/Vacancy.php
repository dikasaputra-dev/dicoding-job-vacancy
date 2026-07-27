<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    /** @use HasFactory<\Database\Factories\VacancyFactory> */
    use HasFactory;

    /**
    * Get the company that owns the vacancy.
    */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
