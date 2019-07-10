<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([], function () {
    Route::get('/', function () {
        return "This endpoint is no problem.";
    });
    // Route::get('items', 'ItemsController@index');
    // Route::post('items', 'ItemsController@store');
    // Route::get('categories', 'CategoriesController@index');
    // Route::post('categories', 'CategoriesController@store');

    Route::apiResource('categories', 'CategoriesController')->only(['index']);
    Route::apiResource('items', 'ItemsController')->only(['index']);
    Route::apiResource('orders', 'OrdersController')->only(['store','index']);



});
