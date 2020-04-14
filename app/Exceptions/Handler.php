<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Validators\TokenValidator;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable               $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            $statusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
            $messageError = $exception->getMessage();
            $errors = '';
            if ($exception instanceof ValidationException) {
                $messageError = $exception->getMessage();
                $errors = $exception->errors();
            } elseif ($exception instanceof AuthenticationException) {
                $messageError = trans('messages.token_invalid');
                $statusCode = JsonResponse::HTTP_UNAUTHORIZED;
            } elseif ($exception instanceof AuthException) {
                $messageError = $exception->getMessage();
                $statusCode = $exception->getCode();
            }
            return response()->baseResponseError($messageError, $errors, $statusCode);
        }
        return parent::render($request, $exception);
    }
}
