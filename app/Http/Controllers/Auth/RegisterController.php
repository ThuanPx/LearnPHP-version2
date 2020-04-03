<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register User
     * 
     * @return token
     */
    public function registerUser(RegisterFormRequest $request)
    {
        $user = User::create(
            [
                'email' => $request->only('email'),
                'password' => Hash::make($request->only('password')),
                'name' => $request->only('name')
            ]
        );
        $token = Auth::fromUser($user);
        return response()->baseResponseStatusCreated([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
