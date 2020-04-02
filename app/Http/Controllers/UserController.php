<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{

    public function getUser()
    {
        return response()->baseResponse(['user' => 'ThuanPx']);
    }

    public function editUserId($id)
    {
        return  response()->baseResponse(['id ' => $id]);
    }

    public function createUser(UserRequest $request)
    {
        return response()->baseResponse($request->validated());
    }
}
