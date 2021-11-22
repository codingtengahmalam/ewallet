<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router)
{
    $router->post('login', 'AuthController@login');
});

Route::group([
    'middleware' => 'auth',
    'prefix' => 'api'
], function ($router) {
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'ProfileController@profile');
    Route::post('wallet', 'ProfileController@registerWallet');
    Route::post('wallet/topup', 'WalletController@topup');

});
