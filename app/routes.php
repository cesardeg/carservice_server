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
Route::get('user/{user_id}', 'UserController@user');

Route::get('carowner/{user_id}/{car_owner_id}', 
	'CarOwnerController@carOwner');
Route::post('createowner/{user_id}', 
	'CarOwnerController@store');
Route::get('listcarowners/{user_id}', 
	'CarOwnerController@index');

Route::get('findcarid/{user_id}/{epc}', 
    'CarController@findCarId');
Route::get('car/{user_id}/{car_id}',
    'CarController@car');
Route::post('createcar/{user_id}', 
	'CarController@store');


Route::get('states/', 
	'StateController@index');
Route::get('towns/{state_id}', 
	'TownController@index');

Route::get('carbrands/', 
	'CarBrandController@index');
Route::get('carmodels/{carbrand}', 
	'CarModelController@index');


Route::post('createserviceorder/{user_id}', 
	'ServiceOrderController@store');
Route::get('serviceorder/{user_id}/{service_order_id}', 
	'ServiceOrderController@serviceorder');
Route::post('updateserviceorder/{user_id}/{service_order_id}', 
	'ServiceOrderController@update');
Route::get('closeserviceorder/{user_id}/{service_order_id}',
	'ServiceOrderController@close');

Route::post('createservicediagnostic/{user_id}/{service_order_id}', 
	'ServiceDiagnosticController@store');
Route::get('servicediagnostic/{user_id}/{service_diagnostic_id}', 
	'ServiceDiagnosticController@servicediagnostic');
Route::post('updateservicediagnostic/{user_id}/{service_diagnostic_id}', 
	'ServiceDiagnosticController@update');
Route::get('closeservicediagnostic/{user_id}/{service_diagnostic_id}',
	'ServiceDiagnosticController@close');

Route::post('createservicedelivery/{user_id}/{service_diagnostic_id}', 
	'ServiceDeliveryController@store');

Route::post('post', function () {
    return "prueba post";
});

//RFC: [A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?


?>