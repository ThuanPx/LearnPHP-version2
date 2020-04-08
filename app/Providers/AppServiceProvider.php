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
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->createResponseMacro();
    }

    /**
     * Create response macro
     *
     * @return void
     */
    private function createResponseMacro()
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
            'baseResponseStatusCreated',
            function ($data) {
                return Response::json(
                    [
                        'status_code' => JsonResponse::HTTP_CREATED,
                        'data' => $data,
                    ],
                    JsonResponse::HTTP_CREATED
                );
            }
        );

        Response::macro(
            'baseResponseError',
            function (
                $errorMessage,
                $errors = '',
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
