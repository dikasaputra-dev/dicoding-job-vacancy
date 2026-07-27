<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vacancy
 */
class VacancyResource extends JsonResource
{
    /**
     * Transform the vacancy into a detailed representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'position' => $this->position,

            'employment_type' => $this->employment_type->value,
            'employment_type_label' => $this->employment_type->label(),

            'candidate_count' => $this->candidate_count,

            'expires_at' => $this->expires_at->toDateString(),
            'is_active' => $this->isActive(),

            'location' => $this->location,
            'is_remote' => $this->is_remote,

            'description' => $this->description,

            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'show_salary' => $this->show_salary,

            'minimum_experience' => $this->minimum_experience->value,
            'minimum_experience_label' => $this->minimum_experience->label(),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'company' => $this->whenLoaded(
                'company',
                fn (): array => [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'slug' => $this->company->slug,
                    'logo_url' => $this->companyLogoUrl(),
                    'business_sector' => $this->company->business_sector,
                    'employee_size' => $this->company->employee_size,
                    'headquarters_location' => $this->company->headquarters_location,
                    'website_url' => $this->company->website_url,
                ],
            ),
        ];
    }

    /**
     * Generate the absolute company logo URL.
     */
    private function companyLogoUrl(): ?string
    {
        $logoPath = $this->company->logo_path;

        if ($logoPath === null) {
            return null;
        }

        return asset(ltrim($logoPath, '/'));
    }
}
