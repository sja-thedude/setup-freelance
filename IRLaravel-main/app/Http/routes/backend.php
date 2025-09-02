<?php

/*
  |--------------------------------------------------------------------------
  | Backend Routes
  |--------------------------------------------------------------------------
  |
  | Here is where all Backend routes are defined.
  |
 */

$this->get('logout', ['as' => $this->admin.'.logout', 'uses' => 'Auth\AuthController@logout']);

// Change password
$this->get('password/change-password', ['as' => $this->admin.'.password.changePasswordForm', 'uses' => 'Auth\ChangePasswordController@changePasswordForm']);
$this->post('password/change-password', ['as' => $this->admin.'.password.changePassword', 'uses' => 'Auth\ChangePasswordController@changePassword']);

// Users
$this->put('users/status/{id}', ['as' => $this->admin.'.users.status', 'uses' => 'UserController@status']);
$this->post('users/update-status/{id}', ['as' => $this->admin.'.users.updateStatus', 'uses' => 'UserController@updateStatus']);
$this->get('users/profile', ['as' => $this->admin.'.users.profile', 'uses' => 'UserController@profile']);
$this->get('users/change-profile', ['as' => $this->admin.'.users.editProfile', 'uses' => 'UserController@editProfile']);
$this->put('users/update-profile', ['as' => $this->admin.'.users.updateProfile', 'uses' => 'UserController@updateProfile']);
$this->patch('users/update-profile', ['as' => $this->admin.'.users.updateProfile', 'uses' => 'UserController@updateProfile']);
//$this->resource('users', 'UserController', ['as' => $this->admin]);
Route::get('users', ['as' => $this->admin.'.users.index', 'uses' => 'UserController@indexUser']);
Route::post('users', ['as' => $this->admin.'.users.store', 'uses' => 'UserController@storeUser']);
//Route::get('users/{id}', ['as' => $this->admin.'.users.show', 'uses' => 'UserController@show']);
Route::put('users/{id}', ['as' => $this->admin.'.users.update', 'uses' => 'UserController@updateUser']);
Route::patch('users/{id}', ['as' => $this->admin.'.users.patch', 'uses' => 'UserController@updateUser']);
Route::delete('users/{id}', ['as' => $this->admin.'.users.destroy', 'uses' => 'UserController@destroyUser']);

// Orders
$this->any('orders', ['as' => $this->admin.'.orders.index', 'uses' => 'OrderController@index']);
$this->get('orders/{id}', ['as' => $this->admin.'.orders.show', 'uses' => 'OrderController@show']);
$this->post('orders/print-item/{type}/{orderId}', ['as' => $this->admin.'.orders.printItem', 'uses' => 'OrderController@printItem']);

// Managers
$this->post('managers/send-invitation/{id}', ['as' => $this->admin.'.managers.sendInvitation', 'uses' => 'UserController@sendInvitation']);
$this->resource('managers', 'UserController', ['as' => $this->admin]);

// Countries
$this->resource('countries', 'CountryController', ['as' => $this->admin]);

// Contacts
$this->resource('contacts', 'ContactController', ['as' => $this->admin]);

// PrintJobs
$this->any('print-jobs', ['as' => $this->admin.'.printjob.index', 'uses' => 'PrintJobController@index']);
$this->put('print-jobs/cancel/{id}', ['as' => $this->admin.'.printjob.cancel', 'uses' => 'PrintJobController@cancel']);

// Sms
$this->any('sms', ['as' => $this->admin.'.sms.index', 'uses' => 'SmsController@index']);
$this->post('sms/store', ['as' => $this->admin.'.sms.store', 'uses' => 'SmsController@store']);

// Roles
$this->resource('roles', 'RoleController', ['as' => $this->admin]);

// Banners
$this->put('banners/change-order', ['as' => $this->admin.'.banners.changeOrder', 'uses' => 'BannerController@changeOrder']);
$this->resource('banners', 'BannerController', ['as' => $this->admin]);

// Restaurants
$this->get('restaurants/auto-login/{id}/{workspaceId}', ['as' => $this->admin.'.restaurants.autoLogin', 'uses' => 'WorkspaceController@autoLogin']);
$this->get('restaurants/get-roles', ['as' => $this->admin.'.restaurants.getRoles', 'uses' => 'WorkspaceController@ajaxGetRoles']);
$this->post('restaurants/update-status/{id}', ['as' => $this->admin.'.restaurants.updateStatus', 'uses' => 'WorkspaceController@updateStatus']);
$this->post('restaurants/assign-account-manager/{ids}', ['as' => $this->admin.'.restaurants.assignAccountManager', 'uses' => 'WorkspaceController@assignAccountManager']);
$this->post('restaurants/send-invitation/{id}', ['as' => $this->admin.'.restaurants.sendInvitation', 'uses' => 'WorkspaceController@sendInvitation']);
$this->resource('restaurants', 'WorkspaceController', ['as' => $this->admin]);

// Langs
$this->resource('langs', 'LangController', ['as' => $this->admin]);

// Dashboard
$this->resource('dashboard', 'DashboardController', ['as' => $this->admin]);

// Restaurant categories
$this->resource('type-zaak', 'RestaurantCategoryController', ['as' => $this->admin]);

// Notifications categories
$this->resource('notifications', 'NotificationController', ['as' => $this->admin]);

// Notification Plans categories
//$this->resource('notificationPlans', 'NotificationPlanController', ['as' => $this->admin]);

// Vats
$this->resource('vats', 'VatController', ['as' => $this->admin]);

//Group workspace
$this->resource('printergroup', 'PrinterGroupController', ['as' => $this->admin]);
$this->post('printergroup/updateStatus/{printerGroupId}', ['as' => $this->admin . '.printergroup.updateStatus', 'uses' => 'PrinterGroupController@updateStatus']);

//Group workspace
$this->resource('grouprestaurant', 'GroupRestaurantController', ['as' => $this->admin]);
$this->post('grouprestaurant/updateStatus/{group_restaurant_id}', ['as' => $this->admin . '.grouprestaurant.updateStatus', 'uses' => 'GroupRestaurantController@updateStatus']);

Route::group([
    'prefix' => 'workspace/{workspace}',
], function () {
    // Workspace Extras
    Route::post('workspace-extras/update-or-create/type/{id}', ['as' => $this->admin.'.workspaceExtras.updateOrCreate', 'uses' => 'WorkspaceExtraController@updateOrCreate']);
//    Route::resource('workspace-extras', 'WorkspaceExtraController', ['as' => $this->admin]);
});


/*
|--------------------------------------------------------------------------
| Routes of WorkspaceApp
|--------------------------------------------------------------------------
*/
//Route::get('workspaceApps', ['as'=> 'admin.workspaceApps.index', 'uses' => 'WorkspaceAppController@index']);
//Route::post('workspaceApps', ['as'=> 'admin.workspaceApps.store', 'uses' => 'WorkspaceAppController@store']);
//Route::get('workspaceApps/create', ['as'=> 'admin.workspaceApps.create', 'uses' => 'WorkspaceAppController@create']);
//Route::put('workspaceApps/{workspaceApp}', ['as'=> 'admin.workspaceApps.update', 'uses' => 'WorkspaceAppController@update']);
//Route::patch('workspaceApps/{workspaceApp}', ['as'=> 'admin.workspaceApps.update', 'uses' => 'WorkspaceAppController@update']);
//Route::delete('workspaceApps/{workspaceApp}', ['as'=> 'admin.workspaceApps.destroy', 'uses' => 'WorkspaceAppController@destroy']);
//Route::get('workspaceApps/{workspaceApp}', ['as'=> 'admin.workspaceApps.show', 'uses' => 'WorkspaceAppController@show']);
//Route::get('workspaceApps/{workspaceApp}/edit', ['as'=> 'admin.workspaceApps.edit', 'uses' => 'WorkspaceAppController@edit']);


/*
|--------------------------------------------------------------------------
| Routes of WorkspaceAppMeta
|--------------------------------------------------------------------------
*/
//Route::get('workspaceAppMetas', ['as'=> 'admin.workspaceAppMetas.index', 'uses' => 'WorkspaceAppMetaController@index']);
//Route::post('workspaceAppMetas', ['as'=> 'admin.workspaceAppMetas.store', 'uses' => 'WorkspaceAppMetaController@store']);
//Route::get('workspaceAppMetas/create', ['as'=> 'admin.workspaceAppMetas.create', 'uses' => 'WorkspaceAppMetaController@create']);
//Route::put('workspaceAppMetas/{workspaceAppMeta}', ['as'=> 'admin.workspaceAppMetas.update', 'uses' => 'WorkspaceAppMetaController@update']);
//Route::patch('workspaceAppMetas/{workspaceAppMeta}', ['as'=> 'admin.workspaceAppMetas.update', 'uses' => 'WorkspaceAppMetaController@update']);
//Route::delete('workspaceAppMetas/{workspaceAppMeta}', ['as'=> 'admin.workspaceAppMetas.destroy', 'uses' => 'WorkspaceAppMetaController@destroy']);
//Route::get('workspaceAppMetas/{workspaceAppMeta}', ['as'=> 'admin.workspaceAppMetas.show', 'uses' => 'WorkspaceAppMetaController@show']);
//Route::get('workspaceAppMetas/{workspaceAppMeta}/edit', ['as'=> 'admin.workspaceAppMetas.edit', 'uses' => 'WorkspaceAppMetaController@edit']);


/*
|--------------------------------------------------------------------------
| Routes of WorkspaceJob
|--------------------------------------------------------------------------
*/
//Route::get('workspaceJobs', ['as'=> 'admin.workspaceJobs.index', 'uses' => 'WorkspaceJobController@index']);
//Route::post('workspaceJobs', ['as'=> 'admin.workspaceJobs.store', 'uses' => 'WorkspaceJobController@store']);
//Route::get('workspaceJobs/create', ['as'=> 'admin.workspaceJobs.create', 'uses' => 'WorkspaceJobController@create']);
//Route::put('workspaceJobs/{workspaceJob}', ['as'=> 'admin.workspaceJobs.update', 'uses' => 'WorkspaceJobController@update']);
//Route::patch('workspaceJobs/{workspaceJob}', ['as'=> 'admin.workspaceJobs.update', 'uses' => 'WorkspaceJobController@update']);
//Route::delete('workspaceJobs/{workspaceJob}', ['as'=> 'admin.workspaceJobs.destroy', 'uses' => 'WorkspaceJobController@destroy']);
//Route::get('workspaceJobs/{workspaceJob}', ['as'=> 'admin.workspaceJobs.show', 'uses' => 'WorkspaceJobController@show']);
//Route::get('workspaceJobs/{workspaceJob}/edit', ['as'=> 'admin.workspaceJobs.edit', 'uses' => 'WorkspaceJobController@edit']);


/*
|--------------------------------------------------------------------------
| Routes of City
|--------------------------------------------------------------------------
*/
//Route::get('cities', ['as'=> 'admin.cities.index', 'uses' => 'CityController@index']);
//Route::post('cities', ['as'=> 'admin.cities.store', 'uses' => 'CityController@store']);
//Route::get('cities/create', ['as'=> 'admin.cities.create', 'uses' => 'CityController@create']);
//Route::put('cities/{city}', ['as'=> 'admin.cities.update', 'uses' => 'CityController@update']);
//Route::patch('cities/{city}', ['as'=> 'admin.cities.update', 'uses' => 'CityController@update']);
//Route::delete('cities/{city}', ['as'=> 'admin.cities.destroy', 'uses' => 'CityController@destroy']);
//Route::get('cities/{city}', ['as'=> 'admin.cities.show', 'uses' => 'CityController@show']);
//Route::get('cities/{city}/edit', ['as'=> 'admin.cities.edit', 'uses' => 'CityController@edit']);


/*
|--------------------------------------------------------------------------
| Routes of Address
|--------------------------------------------------------------------------
*/
//Route::get('addresses', ['as'=> 'admin.addresses.index', 'uses' => 'AddressController@index']);
//Route::post('addresses', ['as'=> 'admin.addresses.store', 'uses' => 'AddressController@store']);
//Route::get('addresses/create', ['as'=> 'admin.addresses.create', 'uses' => 'AddressController@create']);
//Route::put('addresses/{address}', ['as'=> 'admin.addresses.update', 'uses' => 'AddressController@update']);
//Route::patch('addresses/{address}', ['as'=> 'admin.addresses.update', 'uses' => 'AddressController@update']);
//Route::delete('addresses/{address}', ['as'=> 'admin.addresses.destroy', 'uses' => 'AddressController@destroy']);
//Route::get('addresses/{address}', ['as'=> 'admin.addresses.show', 'uses' => 'AddressController@show']);
//Route::get('addresses/{address}/edit', ['as'=> 'admin.addresses.edit', 'uses' => 'AddressController@edit']);
