<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\EmploymentType;
use App\Enums\MinimumExperience;
use App\Models\Vacancy;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

#[FailOnUnknownFields]
class UpdateVacancyRequest extends FormRequest
{
    /**
     * Fields that may be changed through the update endpoint.
     *
     * @var list<string>
     */
    private const MUTABLE_FIELDS = [
        'title',
        'position',
        'employment_type',
        'candidate_count',
        'expires_at',
        'location',
        'is_remote',
        'description',
        'min_salary',
        'max_salary',
        'show_salary',
        'minimum_experience',
    ];

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
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],
            'position' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'employment_type' => [
                'sometimes',
                'required',
                Rule::enum(EmploymentType::class),
            ],
            'candidat_count' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],
            'expires_at' => [
                'sometimes',
                'required',
                Rule::date()
                    ->format('Y-m-d')
                    ->todayOrAfter(),
            ],
            'location' => [
                'sometimes',
                'required',
                'string',
                'max:120',
            ],
            'is_remote' => [
                'sometimes',
                'sometimes',
                'boolean',
            ],
            'description' => [
                'sometimes',
                'required',
                'string',
            ],
            'salary_min' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],
            'salary_max' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
            ],
            'show_salary' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'minimum_experience' => [
                'sometimes',
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
     * Get the additional validation callables.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->validateAtLeastOneMutableField($validator);
                $this->validateSalaryRange($validator);
            },
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function message(): array
    {
        return [
            'vacancy.required' => 'At least one vacancy field must be provided.',
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

    /**
     * Ensure the update request contains at least one mutable field.
     */
    private function validateAtLeastOneMutableField(
        Validator $validator
    ): void {
        $input = $this->all();

        foreach (self::MUTABLE_FIELDS as $field) {
            if (array_key_exists($field, $input)) {
                return;
            }
        }

        $validator->errors()->add(
            'vacancy',
            'At least one vacancy field must be provided',
        );
    }

    /**
     * Ensure the effective maximum salary is not lower than
     * the effective minimum salary.
     */
    private function validateSalaryRange(
        Validator $validator,
    ): void {
        if (
            $validator->errors()->has('salary_min')
            || $validator->errors()->has('salary_max')
        ) {
            return;
        }

        $vacancy = $this->route('vacancy');

        if (! $vacancy instanceof Vacancy) {
            return;
        }

        $input = $this->all();

        $minimumSalary = array_key_exists('salary_min', $input)
            ? $input['salary_min']
            : $vacancy->salary_min;

        $maximumSalary = array_key_exists('salary_max', $input)
            ? $input['salary_max']
            : $vacancy->salary_max;

        if ($maximumSalary === null) {
            return;
        }

        if ((int) $maximumSalary >= (int) $minimumSalary) {
            return;
        }

        $validator->errors()->add(
            'salary_max',
            'The salary max field must be greater than or equal to salary min.',
        );
    }
}
