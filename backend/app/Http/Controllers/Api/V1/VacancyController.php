<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexVacancyRequest;
use App\Http\Requests\Api\V1\StoreVacancyRequest;
use App\Http\Requests\Api\V1\UpdateVacancyRequest;
use App\Http\Resources\Api\V1\VacancyResource;
use App\Http\Resources\Api\V1\VacancySummaryResource;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class VacancyController extends Controller
{
    private const DEFAULT_COMPANY_SLUG = 'dicoding-indonesia';

    /**
     * Display a listing of the resource.
     */
    public function index(
        IndexVacancyRequest $request,
    ): AnonymousResourceCollection {
        $vacancies = Vacancy::query()
            ->select([
                'id',
                'company_id',
                'title',
                'position',
                'employment_type',
                'location',
                'is_remote',
                'minimum_experience',
                'expires_at',
                'created_at',
            ])
            ->with([
                'company:id,name,logo_path',
            ])
            ->searchByTitle($request->searchTerm())
            ->filterByStatus($request->vacancyStatus())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($request->perPage())
            ->withQueryString();

        return VacancySummaryResource::collection(
            $vacancies,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreVacancyRequest $request,
    ): JsonResponse {
        $company = Company::query()
            ->where(
                'slug',
                self::DEFAULT_COMPANY_SLUG,
            )
            ->firstOrFail();

        $vacancy = $company
            ->vacancies()
            ->create($request->validated());

        $vacancy->refresh();

        $vacancy->load([
            'company:id,name,slug,logo_path,business_sector,employee_size,headquarters_location,website_url',
        ]);

        $response = (new VacancyResource($vacancy))
            ->additional([
                'message' => 'Vacancy created successfully.',
            ])
            ->response();

        $response->setStatusCode(
            Response::HTTP_CREATED,
        );

        $response->headers->set(
            'Location',
            route(
                'api.v1.vacancies.show',
                $vacancy,
            ),
        );

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacancy $vacancy): VacancyResource
    {
        $vacancy->loadMissing([
            'company:id,name,slug,logo_path,business_sector,employee_size,headquarters_location,website_url',
        ]);

        return new VacancyResource($vacancy);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateVacancyRequest $request,
        Vacancy $vacancy,
    ): JsonResponse {
        $vacancy->update(
            $request->validated(),
        );

        $vacancy->refresh();

        $vacancy->load([
            'company:id,name,slug,logo_path,business_sector,employee_size,headquarters_location,website_url',
        ]);

        return (new VacancyResource($vacancy))
            ->additional([
                'message' => 'Vacancy updated successfully.',
            ])
            ->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacancy $vacancy)
    {
        //
    }
}
