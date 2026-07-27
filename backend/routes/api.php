<?php

use App\Http\Controllers\Api\V1\VacancyController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::apiResource(
            'vacancies',
            VacancyController::class)
            ->missing(
                static function (Request $_request): JsonResponse {
                    return response()->json([
                        'message' => 'Vacancy not found.',
                    ], 404);
                }
            );
    });
