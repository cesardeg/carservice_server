<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::post('login', 'UserController@login');

Route::get('username/{username}', 
	'CarOwnerController@isAvailableUsername');
Route::post('createowner', 
	'CarOwnerController@store');
Route::get('listcarowners/{client_id}', 
	'CarOwnerController@index');

Route::get('findcarid/{user_id}/{epc}', 
    'CarController@findCarId');
Route::get('carbyepc/{client_id}/{epc}', 
	'CarController@carbyepc');
Route::post('createcar', 
	'CarController@store');

Route::get('carbrands/', 
	'CarBrandController@index');

Route::get('carlines/{carbrand}', 
	'CarLineController@index');

Route::post('storeserviceorder', 
	'ServiceOrderController@store');

Route::post('post', function () {
    return "prueba post";
});

//RFC: [A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?


?>