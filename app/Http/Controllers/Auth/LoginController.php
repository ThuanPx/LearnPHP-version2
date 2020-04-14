<?php

namespace App\Http\Controllers\Auth;

use App\Events\LoginUserEvent;
use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Function login user
     *
     * @return JsonResponse token
     */
    public function login(UserFormRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::guard()->attempt($credentials);
        
        if (!$token) {
            throw new AuthException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, trans('messages.email_or_password_wrong'));
        }

        event(new LoginUserEvent(Auth::user()));

        return response()->baseResponseStatusCreated([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
