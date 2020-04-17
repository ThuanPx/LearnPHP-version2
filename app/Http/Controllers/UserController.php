<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    /**
     * Function get user
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser()
    {
        /** @var \Illuminate\Database\Eloquent\Model */
        $user = Auth::user();

        if ($user->cant('viewAny', $user)) {
            return response()->baseResponseError(trans('messages.you_not_admin'));
        }

        return response()->baseResponse(['user' => $user->email]);
    }

    /**
     * Function edit user
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function editUserId($id)
    {
        return  response()->baseResponse(['id ' => $id]);
    }

    /**
     * Function create user
     *
     * @param \App\Http\Requests\UserFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function createUser(UserFormRequest $request)
    {
        return response()->baseResponse($request->validated());
    }

    /**
     * Function delete user
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteUser()
    {
        if (Gate::denies('delete-user', Auth::user())) {
            return response()->baseResponseError(trans('messages.you_not_admin'));
        }

        return response()->baseResponse(['message' => 'messages.delete_user_success']);
    }
}
