<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('login', 'Auth\LoginController@login');

Route::post('register', 'Auth\RegisterController@registerUser');

Route::group(['middleware' => ['auth']], function () {
    Route::group(
        ['prefix' => 'user'],
        function () {
            Route::get('', 'UserController@getUser')->name('getUser');

            Route::post('', 'UserController@createUser')->name('createUser');

            Route::put('{id}', 'UserController@editUserId')->middleware('checkUser');

            Route::delete('{id}', 'UserController@deleteUser')->middleware('checkUser');
        }
    );

    Route::get('logout', 'LogoutController@logout');
});
