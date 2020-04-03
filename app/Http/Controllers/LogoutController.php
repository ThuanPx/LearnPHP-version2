<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{

    /**
     * Function logout
     * 
     * @return JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return response()->baseResponse(trans('messages.logout_success'));
    }
}
