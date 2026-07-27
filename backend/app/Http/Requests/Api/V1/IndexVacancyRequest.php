<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\VacancyStatusFilter;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class IndexVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::enum(VacancyStatusFilter::class),
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'between:1,50',
            ],
        ];
    }

    /**
     * Get the validated vacancy search term.
     */
    public function searchTerm(): ?string
    {
        $validated = $this->validated();
        $search = $validated['search'] ?? null;

        if (! is_string($search)) {
            return null;
        }

        $search = trim($search);

        return $search === ''
            ? null
            : $search;
    }

    /**
     * Get the validated vacancy status filter.
     */
    public function vacancyStatus(): VacancyStatusFilter
    {
        $validated = $this->validated();
        $status = $validated['status'] ?? null;

        if (! is_string($status)) {
            return VacancyStatusFilter::Active;
        }

        return VacancyStatusFilter::from($status);
    }

    /**
     * Get the validated number of records per page.
     */
    public function perPage(): int
    {
        $validated = $this->validated();

        return (int) ($validated['per_page'] ?? 10);
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'per_page' => 'records per page',
        ];
    }
}
