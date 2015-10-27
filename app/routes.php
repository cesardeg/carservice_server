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

Route::get('/', function() {
	return View::make('hello');
});

Route::get('states/', 
	'StateController@index');
Route::get('towns/{state_id}', 
	'TownController@index');

Route::get('carbrands/', 
	'CarBrandController@index');
Route::get('carmodels/{carbrand}', 
	'CarModelController@index');

/*
 |------------------------------------------------------------------------
 | Operator
 |------------------------------------------------------------------------
*/ 

/*Operator App*/
Route::post('login', 
	'UserController@login');
Route::get('user/{user_id}', 
	'UserController@user');
/*Admin App*/
Route::post('post_login', 
	'UserController@loginAdmin');
Route::get('loginadm', function() {
    return View::make('login.login');
});
Route::get('logoutadm', function() {
	Auth::logout();
    return View::make('login.login');
});
Route::get('operators_list',
	array('before' => 'auth', 'uses' => 'OperatorController@operators_list'));
Route::post('saveoperator/{operatorId}',
	'OperatorController@store');
Route::get('getoperator/{operatorId}',
	'OperatorController@get');
Route::delete('deleteoperator/{operatorId?}','OperatorController@delete');


/*
 |------------------------------------------------------------------------
 | Car Owner
 |------------------------------------------------------------------------
*/ 

Route::post('carownerlogin',
	'CarOwnerController@login');
Route::post('registertoken/{car_owner_id}',
	'CarOwnerController@registerToken');
Route::post('unregistertoken/{car_owner_id}',
	'CarOwnerController@unregisterToken');
Route::get('ownername/{car_owner_id}',
	'CarOwnerController@ownerName');
Route::get('profile/{car_owner_id}',
	'CarOwnerController@getByOwner');
Route::get('carowner/{user_id}/{car_owner_id}',
	'CarOwnerController@carOwner');
Route::post('createowner/{user_id}',
	'CarOwnerController@store');
Route::post('saveowner/{car_owner_id}',
	'CarOwnerController@save');
Route::get('listcarowners/{user_id}',
	'CarOwnerController@index');
Route::get('carowneradmin/{car_owner_id}', 
	'CarOwnerController@getByAdmin');

/*
 |------------------------------------------------------------------------
 | Car
 |------------------------------------------------------------------------
*/ 
 
/*Operator App*/
Route::get('findcarid/{user_id}/{key}/{value}', 
    'CarController@findCarId');
Route::get('car/{user_id}/{car_id}',
    'CarController@car');
Route::post('savecar/{user_id}', 
	'CarController@store');
Route::post('savephoto/{user_id}/{car_id}', 
	'CarController@savePhoto');
Route::get('getreminderkmcapture/{user_id}/{car_id}', 
	'CarController@getReminderKmCapture');
Route::post('remindkmcapture/{user_id}/{car_id}/', 
	'CarController@remindKMCapture');
Route::post('updatereminderkmcapture/{user_id}/{car_id}/', 
	'CarController@updateReminderKmCapture');
Route::get('scheduledservices/{user_id}/{car_id}', 
	'CarController@scheduledServices');
Route::post('addscheduledservice/{user_id}/{car_id}', 
	'CarController@AddScheduledService');
Route::post('removescheduledservice/{user_id}', 
	'CarController@removeScheduledService');
/* Client App*/
Route::get('cars/{car_owner_id}', 
	'CarController@cars');
Route::get('carcarowner/{car_owner_id}/{car_id}',
    'CarController@getByOwner');
Route::get('kmcapture/{car_owner_id}/{car_id}', 
	'CarController@kmCapture');
Route::post('updatekm/{car_owner_id}/{car_id}', 
	'CarController@updateKm');
/*Admin App*/
Route::get('caradmin/{car_owner_id}', 
	'CarController@getByAdmin');


/*
 |------------------------------------------------------------------------
 | Service Order
 |------------------------------------------------------------------------
*/ 
 
/*Operator App*/
Route::post('createserviceorder/{user_id}', 
	'ServiceOrderController@store');
Route::get('completeserviceorder/{user_id}/{service_order_id}', 
	'ServiceOrderController@completeService');
/* Client App*/
Route::get('carhistory/{car_owner_id}/{car_id}', 
	'ServiceOrderController@carHistory');
Route::get('completedserviceowner/{car_owner_id}/{car_id}', 
	'ServiceOrderController@completedServiceByOwner');
Route::post('knowcompleteservice/{car_owner_id}/{car_id}', 
	'ServiceOrderController@knowCompleteService');
/*Web Admin*/
Route::get('exitworkshop/{service_order_id}', 
	'ServiceOrderController@exitWorkshop');
Route::get('carsinworkshop',  
	array('before' => 'auth', 'uses' => 'ServiceOrderController@carsInWorkshop'));
Route::get('workshophistory',
	array('before' => 'auth', 'uses' => 'ServiceOrderController@workshopHistory'));


/*
 |------------------------------------------------------------------------
 | Service Inventory
 |------------------------------------------------------------------------
*/ 

/*Operator App*/
Route::get('inventoryoperator/{user_id}/{service_order_id}', 
	'ServiceInventoryController@getByOperator');
Route::post('inventoryoperator/{user_id}/{service_order_id}', 
	'ServiceInventoryController@update');
Route::post('closeinventory/{user_id}/{service_order_id}',
	'ServiceInventoryController@close');
/*Client App*/
Route::get('inventoryowner/{car_owner_id}/{service_order_id}', 
	'ServiceInventoryController@getByOwner');
Route::post('agreeinventoryowner/{car_owner_id}/{service_order_id}',
	'ServiceInventoryController@agreeByOwner');
Route::post('disagreeinventoryowner/{car_owner_id}/{service_order_id}',
	'ServiceInventoryController@disagreeByOwner');
/*Administrator App*/
Route::get('inventoryadmin/{service_order_id}',
	'ServiceInventoryController@getByAdmin');
Route::post('redoinventoryadmin/{service_order_id}',
	'ServiceInventoryController@redoByAdmin');
Route::post('agreeinventoryadmin/{service_order_id}',
	'ServiceInventoryController@agreeByAdmin');



/*
 |------------------------------------------------------------------------
 | Service Diagnostic
 |------------------------------------------------------------------------
*/ 

/*Operator App*/
Route::post('createservicediagnostic/{user_id}/{service_order_id}', 
	'ServiceDiagnosticController@store');
Route::get('servicediagnostic/{user_id}/{service_diagnostic_id}', 
	'ServiceDiagnosticController@getByOperator');
Route::post('updateservicediagnostic/{user_id}/{service_order_id}', 
	'ServiceDiagnosticController@update');
Route::post('closeservicediagnostic/{user_id}/{service_order_id}',
	'ServiceDiagnosticController@close');
/*Client App*/
Route::get('diagnosticowner/{car_owner_id}/{service_diagnostic_id}', 
	'ServiceDiagnosticController@getByOwner');
Route::post('agreediagnosticowner/{car_owner_id}/{service_diagnostic_id}',
	'ServiceDiagnosticController@agreeByOwner');
Route::post('disagreediagnosticowner/{car_owner_id}/{service_diagnostic_id}',
	'ServiceDiagnosticController@disagreeByOwner');
/*Adiministrator App*/
Route::get('diagnosticadmin/{service_order_id}',
	'ServiceDiagnosticController@getByAdmin');
Route::post('agreediagnosticadmin/{service_order_id}',
	'ServiceDiagnosticController@agreeByAdmin');
Route::post('redodiagnosticadmin/{service_order_id}',
	'ServiceDiagnosticController@redoByAdmin');


/*
 |------------------------------------------------------------------------
 | Service Quote
 |------------------------------------------------------------------------
*/

 /*Client App*/
Route::get('quoteowner/{car_owner_id}/{service_diagnostic_id}', 
	'ServiceQuoteController@getByOwner');
Route::post('agreequoteowner/{car_owner_id}/{service_diagnostic_id}',
	'ServiceQuoteController@agreeByOwner');
Route::post('disagreequoteowner/{car_owner_id}/{service_diagnostic_id}',
	'ServiceQuoteController@disagreeByOwner');
/*Adiministrator App*/
Route::get('quoteadmin/{service_order_id}',
	'ServiceQuoteController@getByAdmin');
Route::post('savequoteitem/{quote_item_id}',
	'ServiceQuoteController@saveQuoteItem');
Route::post('deletequoteitem/{quote_item_id}',
	'ServiceQuoteController@deleteQuoteItem');
Route::post('closequote/{service_order_id}',
	'ServiceQuoteController@closeByAdmin');
Route::post('agreequoteadmin/{service_order_id}',
	'ServiceQuoteController@agreeByAdmin');
Route::post('redoquoteadmin/{service_order_id}',
	'ServiceQuoteController@redoByAdmin');



/*
 |------------------------------------------------------------------------
 | Notifications
 |------------------------------------------------------------------------
*/

Route::get('notifications/{car_owner_id}', 
	'NotificationController@notifications');

Route::get('scheduledservice/{car_owner_id}/{scheduled_service_id}', 
	'ScheduledServiceController@scheduledService');


/*
 |------------------------------------------------------------------------
 | Photos
 |------------------------------------------------------------------------
*/

Route::get('listphotos/{user_id}/{service_order_id}/{type}', 
	'ServicePhotoController@listPhotos');
Route::post('addphoto/{user_id}/{service_order_id}/{type}', 
	'ServicePhotoController@add');
Route::post('removephoto/{user_id}/{photo_id}', 
	'ServicePhotoController@remove');
Route::get('listphotosowner/{car_owner_id}/{service_order_id}/{type}', 
	'ServicePhotoController@listPhotosOwner');
Route::get('listphotosadmin/{service_order_id}/{type}', 
	'ServicePhotoController@listPhotosAdmin');

?>