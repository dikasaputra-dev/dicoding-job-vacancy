<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vacancy
 */
class VacancySummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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

            'location' => $this->location,
            'is_remote' => $this->is_remote,

            'minimum_experience' => $this->minimum_experience->value,
            'minimum_experience_label' => $this->minimum_experience->label(),

            'expires_at' => $this->expires_at->toDateString(),
            'is_active' => $this->isActive(),

            'created_at' => $this->created_at?->toISOString(),

            'company' => $this->whenLoaded(
                'company',
                fn (): array => [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'logo_url' => $this->companyLogoUrl(),
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
