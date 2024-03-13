<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::group(['prefix' => 'auth', 'middleware' => 'throttle'], function () {
        Route::post('/', 'AuthController@registrationOrAuthentication')->name('auth.store');
        Route::get('/logout', fn() => auth()->logout());
    });

    // Методы доступные только с авторизацией
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('get-user', fn() => auth()->guard('sanctum')->user());

        Route::group(['prefix' => 'requests'], function () {
            Route::post('/', 'ApplicationController@store');
            Route::get('/', 'ApplicationController@index');
            Route::get('/{id}', 'ApplicationController@show');
        });
    });
});
