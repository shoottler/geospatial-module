<?php


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

Route::middleware('auth:api')->group(function () {
	Route::get('/areas','Area\AreaController@index');
	Route::get('/areas/{id}','Area\AreaController@show');
	Route::put('/areas/{id}/update','Area\AreaController@update');
	Route::post('areas/create','Area\AreaController@store');
	Route::delete('areas/{id}','Area\AreaController@destroy');
	Route::get('/zones','Zone\ZoneController@index');
	Route::get('/zones/{id}','Zone\ZoneController@show');
	Route::put('/zones/{id}/update','Zone\ZoneController@update');
	Route::post('zones/create','Zone\ZoneController@store');
	Route::delete('zones/{id}','Zone\ZoneController@destroy');
});