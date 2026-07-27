<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Rules\ContainsReadableText;
use App\Support\VacancyDescriptionSanitizer;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[FailOnUnknownFields]
class StoreVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('description')) {
            return;
        }

        $description = $this->input('description');

        if (! is_string($description)) {
            return;
        }

        $this->merge([
            'description' => app(
                VacancyDescriptionSanitizer::class,
            )->sanitize($description),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:50',
            ],
            'position' => [
                'required',
                'string',
                'max:100',
            ],
            'employment_type' => [
                'required',
                Rule::enum(EmploymentType::class),
            ],
            'candidate_count' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],
            'expires_at' => [
                'required',
                Rule::date()
                    ->format('Y-m-d')
                    ->todayOrAfter(),
            ],
            'location' => [
                'required',
                'string',
                'max:120',
            ],
            'is_remote' => [
                'sometimes',
                'boolean',
            ],
            'description' => [
                'required',
                'string',
                'max:20000',
                new ContainsReadableText,
            ],
            'salary_min' => [
                'required',
                'integer',
                'min:0',
            ],
            'salary_max' => [
                'integer',
                'min:0',
                'gte:salary_min',
            ],
            'show_salary' => [
                'sometimes',
                'boolean',
            ],
            'minimum_experience' => [
                'required',
                Rule::enum(MinimumExperience::class),
            ],

            'id' => [
                'prohibited',
            ],
            'company_id' => [
                'prohibited',
            ],
            'created_at' => [
                'prohibited',
            ],
            'updated_at' => [
                'prohibited',
            ],
            'is_active' => [
                'prohibited',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'salary_max.gte' => 'The salary max field must be greater than or equal to salary min.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'employment_type' => 'employment type',
            'candidate_count' => 'candidate count',
            'expires_at' => 'expiration date',
            'is_remote' => 'remote status',
            'salary_min' => 'salary min',
            'salary_max' => 'salary max',
            'show_salary' => 'salary visibility',
            'minimum_experience' => 'minimum experience',
            'company_id' => 'company id',
            'created_at' => 'created at',
            'updated_at' => 'updated at',
            'is_active' => 'active status',
        ];
    }
}
