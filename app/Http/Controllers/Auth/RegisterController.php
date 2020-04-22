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
     * Function register sser
     *
     * @return \Illuminate\Http\Response
     */
    public function registerUser(RegisterFormRequest $request)
    {
        $user = User::create(
            [
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'name' => $request->name
            ]
        );
        $token = Auth::fromUser($user);
        return response()->baseResponseStatusCreated([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
