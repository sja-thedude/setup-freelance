<?php

/*
  |--------------------------------------------------------------------------
  | Client View Routes
  |--------------------------------------------------------------------------
  |
  | Here is where all Client routes are defined.
  |
 */

$this->get('logout', ['as' => $this->manager . '.logout', 'uses' => 'Auth\AuthController@logout']);

// Dashboard
$this->resource('dashboard', 'DashboardController', ['as' => $this->manager]);
$this->post('restaurants/on-off/{id}', ['as' => $this->manager.'.restaurants.updateStatus', 'uses' => 'DashboardController@updateStatus']);

// Statistic
$this->any('statistic/per-product', ['as' => $this->manager . '.statistic.perProduct', 'uses' => 'StatisticController@perProduct']);
$this->any('statistic/discount', ['as' => $this->manager . '.statistic.discount', 'uses' => 'StatisticController@discount']);
$this->any('statistic/per-payment-method', ['as' => $this->manager . '.statistic.perPaymentMethod', 'uses' => 'StatisticController@perPaymentMethod']);

// Change password
$this->get('password/change-password', ['as' => $this->manager . '.password.changePasswordForm', 'uses' => 'Auth\ChangePasswordController@changePasswordForm']);
$this->post('password/change-password', ['as' => $this->manager . '.password.changePassword', 'uses' => 'Auth\ChangePasswordController@changePassword']);

// Users
$this->get('users/profile', ['as' => $this->manager . '.users.profile', 'uses' => 'UserController@profile']);
$this->get('users/change-profile', ['as' => $this->manager . '.users.editProfile', 'uses' => 'UserController@editProfile']);
$this->put('users/update-profile', ['as' => $this->manager . '.users.updateProfile', 'uses' => 'UserController@updateProfile']);
$this->patch('users/update-profile', ['as' => $this->manager . '.users.updateProfile', 'uses' => 'UserController@updateProfile']);
$this->resource('users', 'UserController', ['as' => $this->manager]);
$this->post('users/user-credit/{id}', ['as' => $this->manager.'.user.credit', 'uses' => 'UserController@updateCredit']);

// Categories
$this->resource('categories', 'CategoryController', ['as' => $this->manager]);
$this->post('categories/update-status/{id}', ['as' => $this->manager.'.categories.updateStatus', 'uses' => 'CategoryController@updateStatus']);
$this->post('categories/update-orders', ['as' => $this->manager.'.categories.updateOrder', 'uses' => 'CategoryController@updateOrder']);

// Products
$this->resource('products', 'ProductController', ['as' => $this->manager]);
$this->post('products/update-status/{id}', ['as' => $this->manager.'.products.updateStatus', 'uses' => 'ProductController@updateStatus']);
$this->post('products/update-orders', ['as' => $this->manager.'.products.updateOrder', 'uses' => 'ProductController@updateOrder']);
$this->get('products/get-option-by-category/{categoryId}/{useCategoryOption}', ['as' => $this->manager.'.products.getOptionByCategory', 'uses' => 'ProductController@getOptionByCategory']);
$this->put('products/updatePrice/{id}', ['as' => $this->manager.'.products.updatePrice', 'uses' => 'ProductController@updatePrice']);

// Options
$this->resource('options', 'OptionController', ['as' => $this->manager]);
$this->post('options/update-orders', ['as' => $this->manager.'.options.updateOrder', 'uses' => 'OptionController@updateOrder']);
$this->get('options/create/item', ['as' => $this->manager.'.options.createItem', 'uses' => 'OptionController@createItem']);
$this->get('options/items-references/{optionId}', ['as' => $this->manager.'.options.itemsReferences', 'uses' => 'OptionController@itemsReferences']);
$this->put('options/items-references/{optionId}', ['as' => $this->manager.'.options.updateItemsReferences', 'uses' => 'OptionController@updateItemsReferences']);


// Groups
$this->resource('groups', 'GroupController', ['as' => $this->manager])->middleware('accessGroupManager');
$this->post('groups/update-status/{id}', ['as' => $this->manager . '.groups.updateStatus', 'uses' => 'GroupController@updateStatus'])->middleware('accessGroupManager');
// Statistic
$this->any('groups/statistic/per-product/{groupId}', ['as' => $this->manager . '.groups.statistic.perProduct', 'uses' => 'GroupController@perProduct']);
$this->any('groups/statistic/discount/{groupId}', ['as' => $this->manager . '.groups.statistic.discount', 'uses' => 'GroupController@discount']);
$this->any('groups/statistic/per-payment-method/{groupId}', ['as' => $this->manager . '.groups.statistic.perPaymentMethod', 'uses' => 'GroupController@perPaymentMethod']);

// Order
$this->resource('orders', 'OrderController', ['as' => $this->manager]);
$this->post('orders/mark-no-show/{id}', ['as' => $this->manager.'.orders.markNoShow', 'uses' => 'OrderController@markNoShow']);
$this->post('orders/manual-confirmed/{id}', ['as' => $this->manager.'.orders.manualConfirmed', 'uses' => 'OrderController@manualConfirmed']);
$this->post('orders/manual-checked-fully-paid-cash/{id}', ['as' => $this->manager.'.orders.manualCheckedFullyPaidCash', 'uses' => 'OrderController@manualCheckedFullyPaidCash']);
$this->post('orders/print-item/{type}/{orderId}', ['as' => $this->manager.'.orders.printItem', 'uses' => 'OrderController@printItem']);
$this->post('orders/print-multiple/{type}', ['as' => $this->manager.'.orders.printMultiple', 'uses' => 'OrderController@printMultiple']);
$this->post('orders/trigger-connectors/{orderId}', ['as' => $this->manager.'.orders.triggerConnectors', 'uses' => 'OrderController@triggerConnectors']);
$this->post('orders/send-sms', ['as' => $this->manager.'.orders.sendSms', 'uses' => 'OrderController@sendSms']);

// General
$this->get('settings/general', ['as' => $this->manager.'.settings.general', 'uses' => 'SettingController@general']);
$this->post('settings/update-workspace/{id}', ['as' => $this->manager.'.settings.updateWorkspace', 'uses' => 'SettingController@updateWorkspace']);
$this->get('settings/payment-methods', ['as' => $this->manager.'.settings.paymentMethods', 'uses' => 'SettingController@paymentMethods']);
$this->get('settings/preferences', ['as' => $this->manager.'.settings.preferences', 'uses' => 'SettingController@preferences']);
$this->get('settings/delivery-conditions', ['as' => $this->manager.'.settings.deliveryConditions', 'uses' => 'SettingController@deliveryConditions']);
$this->get('settings/print', ['as' => $this->manager.'.settings.print', 'uses' => 'SettingController@print']);
$this->get('settings/opening-hours', ['as' => $this->manager.'.settings.openingHours', 'uses' => 'SettingController@openingHours']);
$this->get('settings/time-slots', ['as' => $this->manager.'.settings.timeSlots', 'uses' => 'SettingController@timeSlots']);
$this->post('settings/upload-gallery/{id}', ['as' => $this->manager.'.settings.uploadGallery', 'uses' => 'SettingController@uploadGallery']);
$this->post('settings/update-gallery-order/{id}', ['as' => $this->manager.'.settings.updateGalleryOrder', 'uses' => 'SettingController@updateGalleryOrder']);

// Setting Generals
$this->post('settingGenerals/update-or-create/workspace/{id}', ['as' => $this->manager.'.settingGenerals.updateOrCreate', 'uses' => 'SettingGeneralController@updateOrCreate']);
//$this->resource('settingGenerals', 'SettingGeneralController', ['as' => $this->manager]);

// Setting Payments
$this->post('settingPayments/update-or-create/workspace/{id}', ['as' => $this->manager.'.settingPayments.updateOrCreate', 'uses' => 'SettingPaymentController@updateOrCreate']);
//$this->resource('settingPayments', 'SettingPaymentController', ['as' => $this->manager]);

// Setting Preferences
$this->post('settingPreferences/update-or-create/workspace/{id}', ['as' => $this->manager.'.settingPreferences.updateOrCreate', 'uses' => 'SettingPreferenceController@updateOrCreate']);
//$this->resource('settingPreferences', 'settingPreferenceController', ['as' => $this->manager]);

// Setting Delivery Conditions
$this->post('settingDeliveryConditions/update-or-create/workspace/{id}', ['as' => $this->manager.'.settingDeliveryConditions.updateOrCreate', 'uses' => 'SettingDeliveryConditionsController@updateOrCreate']);
//$this->resource('settingDeliveryConditions', 'SettingDeliveryConditionsController', ['as' => $this->manager]);

// Setting Print
$this->post('settingPrint/update-or-create/workspace/{id}', ['as' => $this->manager.'.settingPrint.updateOrCreate', 'uses' => 'SettingPrintController@updateOrCreate']);
//$this->resource('settingPrint', 'SettingPrintController', ['as' => $this->manager]);

// Setting Open Hours
$this->post('workspace/{workspaceId}/setting-open-hour/update/{settingId}', ['as' => $this->manager.'.settingOpenHour.update', 'uses' => 'SettingOpenHourController@update']);
$this->post('workspace/{workspaceId}/setting-holiday-exception', ['as' => $this->manager.'.settingOpenHour.storeHolidayException', 'uses' => 'SettingOpenHourController@storeHolidayException']);

// Setting Time Slots
$this->post('workspace/{workspaceId}/setting-time-slot/update/{settingId}', ['as' => $this->manager.'.settingTimeSlot.update', 'uses' => 'SettingTimeslotController@update']);
$this->post('workspace/{workspaceId}/setting-time-slot/render-time-slot-detail/{settingId}', ['as' => $this->manager.'.settingTimeSlot.renderTimeSlotDetail', 'uses' => 'SettingTimeslotController@renderTimeSlotDetail']);
$this->post('workspace/{workspaceId}/setting-time-slot/update-time-slot-detail/{settingId}', ['as' => $this->manager.'.settingTimeSlot.updateTimeSlotDetail', 'uses' => 'SettingTimeslotController@updateTimeSlotDetail']);

// Setting Connectors
$this->get('settings/connectors', ['as' => $this->manager.'.settings.connector.index', 'uses' => 'SettingConnectorController@index']);
$this->get('settings/connectors/create', ['as' => $this->manager.'.settings.connector.create', 'uses' => 'SettingConnectorController@create']);
$this->post('settings/connectors/store', ['as' => $this->manager.'.settings.connector.store', 'uses' => 'SettingConnectorController@store']);
$this->get('settings/connectors/edit/{id}', ['as' => $this->manager.'.settings.connector.edit', 'uses' => 'SettingConnectorController@edit']);
$this->put('settings/connectors/update/{id}', ['as' => $this->manager.'.settings.connector.update', 'uses' => 'SettingConnectorController@update']);
$this->delete('settings/connectors/destroy/{id}', ['as' => $this->manager.'.settings.connector.destroy', 'uses' => 'SettingConnectorController@destroy']);
$this->get('settings/connectors/test/{id}', ['as' => $this->manager.'.settings.connector.test', 'uses' => 'SettingConnectorController@test']);
$this->post('settings/connectors/test-ajax/{id}', ['as' => $this->manager.'.settings.connector.test_ajax', 'uses' => 'SettingConnectorController@testAjax']);

// Coupon
$this->resource('coupons', 'CouponController', ['as' => $this->manager]);

// Reward
$this->resource('rewards', 'RewardController', ['as' => $this->manager])->middleware('accessKlantenkaartenManager');
$this->post('rewards/setting', ['as' => $this->manager.'.rewards.setting', 'uses' => 'RewardController@settingInstellingen'])->middleware('accessKlantenkaartenManager');

// Notifications categories
$this->resource('notifications', 'NotificationController', ['as' => $this->manager])->middleware('accessNotificationManager');

// Excel category
$this->get('excel/export', 'Excel\CategoryController@export')->name($this->manager.'.excel.export.category');
$this->post('excel/import', 'Excel\CategoryController@import')->name($this->manager.'.excel.import.category');

$this->group(['middleware' => 'manager.expire'], function() {

});


/*
|--------------------------------------------------------------------------
| API routes of Workspace App group
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'apps',
], function () {

    // Show theme list
    Route::get('theme', [
        'as' => $this->manager . '.apps.theme',
        'uses' => 'WorkspaceAppController@theme'
    ]);

    // Choose and update a theme by theme number
    Route::put('theme/{theme}', [
        'as' => $this->manager . '.apps.change_theme',
        'uses' => 'WorkspaceAppController@changeTheme'
    ]);

    // Show app settings
    Route::get('settings', [
        'as' => $this->manager . '.apps.settings',
        'uses' => 'WorkspaceAppController@settings'
    ]);

    // Add new app settings
    Route::post('settings', [
        'as' => $this->manager . '.apps.settings.store',
        'uses' => 'WorkspaceAppController@storeSetting'
    ]);

    // Get new app setting item
    Route::get('settings/create', [
        'as' => $this->manager . '.apps.settings.create',
        'uses' => 'WorkspaceAppController@createSetting'
    ]);

    // Change order of app settings
    Route::put('settings/change_orders', [
        'as' => $this->manager . '.apps.settings.orders',
        'uses' => 'WorkspaceAppController@changeSettingOrders'
    ]);

    // Update app settings
    Route::put('settings/{id}', [
        'as' => $this->manager . '.apps.change_settings',
        'uses' => 'WorkspaceAppController@changeSettings'
    ]);

    // Delete a app setting
    Route::delete('settings/{id}', [
        'as' => $this->manager . '.apps.settings.destroy',
        'uses' => 'WorkspaceAppController@destroySetting'
    ]);

    // Update app settings
    Route::put('settings/{id}/change_status', [
        'as' => $this->manager . '.apps.settings.change_status',
        'uses' => 'WorkspaceAppController@changeSettingStatus'
    ]);

});
