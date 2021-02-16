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

Route::group(['prefix' => 'search', 'as' => 'group'], function () use ($router) {
    $router->get('/', 'UserController@authenticate');
});

Route::post('/user/{func}', 'UserController@index');
