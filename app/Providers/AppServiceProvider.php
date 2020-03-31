<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro(
            'baseResponse',
            function ($data, $statusCode = JsonResponse::HTTP_OK) {
                return Response::json(
                    [
                        'status_code' => $statusCode,
                        'data' => $data,
                    ],
                    $statusCode
                );
            }
        );

        Response::macro(
            'baseResponseError',
            function (
                $errorMessage,
                $errors,
                $statusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ) {
                return Response::json(
                    [
                        'status_code' => $statusCode,
                        'error_message' => $errorMessage,
                        'errors' => $errors
                    ],
                    $statusCode
                );
            }
        );
    }
}
