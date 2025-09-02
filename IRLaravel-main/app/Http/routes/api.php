<?php

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where all API routes are defined.
  |
 */

Route::group([
    'prefix' => 'api/v1',
    'middleware' => ['api', 'localization', 'cors', 'handleCors', 'nonPost']
], function () {

    // Get workspace by token
    Route::get('workspaces/token/{token}', [
        'as' => 'api.workspaces.token',
        'uses' => 'WorkspaceAPIController@getByToken'
    ]);

    Route::get('workspaces/domain/{domain}', [
        'as' => 'api.workspaces.domain',
        'uses' => 'WorkspaceAPIController@getByDomain'
    ]);

    // Get group restaurant by token
    Route::get('grouprestaurant/token/{token}', [
        'as' => 'api.grouprestaurant.token',
        'uses' => 'GroupRestaurantAPIController@getByToken'
    ]);

    // Get workspace by token
    Route::get('workspaces/token/{token}/settings', [
        'as' => 'api.workspaces.app_settings',
        'uses' => 'WorkspaceAPIController@getAppSettings'
    ]);

    Route::group([
        'prefix' => 'workspaces/{workspaceId}',
    ], function () {
        // Printer
        // STAR
        Route::post('printer/star', ['as' => 'api.printer_jobs.printerStarAskJob', 'uses' => 'PrinterJobAPIController@printerStarAskJob']);
        Route::get('printer/star', ['as' => 'api.printer_jobs.printerStarProcessJob', 'uses' => 'PrinterJobAPIController@printerStarProcessJob']);
        Route::delete('printer/star', ['as' => 'api.printer_jobs.printerStarConfirmJob', 'uses' => 'PrinterJobAPIController@printerStarConfirmJob']);
        // EPSON
        Route::post('printer/epson', ['as' => 'api.printer_jobs.printerEpson', 'uses' => 'PrinterJobAPIController@printerEpson']);
        // Validate key
        Route::post('validate-order-access-key', ['as' => 'api.workspaces.validateOrderAccessKey', 'uses' => 'WorkspaceAPIController@validateOrderAccessKey']);
    });

    Route::group([
        'prefix' => 'grouprestaurant/{group_restaurant_id}'
    ], function () {
        Route::get('getRestaurantList', ['as' => 'api.grouprestaurant.getRestaurantList', 'uses' => 'GroupRestaurantAPIController@getRestaurantList']);
    });

    /*
    | API routes of Auth
    |--------------------------------------------------------------------------
    */
    /*// Check the auth route in here
    Route::auth();*/
    Route::post('login/social', 'SocialController@login')->name('api.login_social');
    Route::post('login', 'UserAPIController@login')->name('api.submit_login');
    Route::match(['get', 'post'], 'logout', 'UserAPIController@logout')->name('logout');
    Route::post('register', 'Auth\RegisterController@register')->name('api.submit_register');
    Route::get('register/verify/{token}', [
        'as' => 'auth.register.verify',
        'uses' => 'Auth\RegisterController@verifyToken'
    ]);

    // Refresh token
    Route::get('auth/token/refresh', [
        'as' => 'api.auth.refresh_token',
        'uses' => 'Auth\LoginController@refreshToken'
    ]);

    // Refresh token
    Route::get('auth/token/generate', [
        'as' => 'api.auth.token.generate',
        'uses' => 'Auth\LoginController@generateToken'
    ]);

    // Login with token
    Route::get('auth/token/{token}', [
        'as' => 'auth.login_with_token',
        'uses' => 'Auth\LoginController@loginWithToken'
    ]);

    // Check logged in or not
    Route::get('auth/check_login', [
        'as' => 'api.auth.check_login',
        'uses' => 'UserAPIController@checkLogin'
    ]);

    // Send email to reset password
    Route::post('password/email', [
        'as' => 'api.password.email',
        'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail'
    ]);

    Route::post('contacts/to_admin', ['as' => 'api.contacts.toAdmin', 'uses' => 'ContactAPIController@toAdmin']);

    // Verify Reset password token
    Route::get('password/reset/{token}/verify', [
        'as' => 'auth.password.verify',
        'uses' => 'Auth\ResetPasswordController@verifyToken'
    ]);
    // Reset password
    Route::post('password/reset', [
        'as' => 'api.password.resetPost',
        'uses' => 'Auth\ResetPasswordController@reset'
    ]);

    // Change password
    Route::post('password/change', [
        'as' => 'api.password.changePost',
        'uses' => 'Auth\ResetPasswordController@changePassword'
    ]);


    /*
    | API routes of Commons
    |--------------------------------------------------------------------------
    */
    // Timezones
    Route::get('common/timezones', 'CommonAPIController@getTimezones');

    // Check system is online
    Route::get('common/is-online', 'CommonAPIController@isOnline');

    Route::group([
        'middleware' => ['jwt.auth']
    ], function () {
        /*
        | API routes of Permission
        |--------------------------------------------------------------------------
        */
        // User permissions
        Route::get('permissions', 'Auth\PermissionController@getPermissions');

        /*
        | API routes of User
        |--------------------------------------------------------------------------
        */
        // Change avatar
        Route::post('profile/change_avatar', 'Auth\ProfileController@changeAvatar');
        Route::post('profile/change_language', 'Auth\ProfileController@changeLanguage');
        Route::post('profile/remove_avatar', 'Auth\ProfileController@removeAvatar');
        // Show profile
        Route::get('profile/{id?}', 'Auth\ProfileController@show');
        // Update profile
        Route::match(['post', 'put'], 'profile', 'Auth\ProfileController@update');
        // Change password
        Route::match(['post', 'put'], 'change-password', [
            'as' => 'api.auth.changePassword',
            'uses' => 'Auth\ChangePasswordController@changePassword'
        ]);
        Route::delete('profile/delete', 'Auth\ProfileController@destroy');


        /*
        |--------------------------------------------------------------------------
        | API routes of Workspace
        |--------------------------------------------------------------------------
        */
        // list of all the restaurants in the system where the user has ordered in the past.
        Route::get('workspaces/ordered', [
            'as' => 'api.workspaces.ordered',
            'uses' => 'WorkspaceAPIController@ordered',
        ]);
        // list of all the restaurants in the system where the user liked at least one of the restaurant products.
        Route::get('workspaces/liked', [
            'as' => 'api.workspaces.liked',
            'uses' => 'WorkspaceAPIController@liked',
        ]);


        /*
        |--------------------------------------------------------------------------
        | API routes of Workspace group
        |--------------------------------------------------------------------------
        */
        Route::group([
            'prefix' => 'workspaces/{workspace}',
        ], function () {

            /*
            |--------------------------------------------------------------------------
            | API routes of Loyalty
            |--------------------------------------------------------------------------
            */
            // Get my redeem
            Route::get('loyalties/my_redeem', [
                'as' => 'api.workspaces.loyalties.my_redeem',
                'uses' => 'Workspace\LoyaltyAPIController@getMyLoyalty'
            ]);
            // Get loyalty of workspace
            Route::get('loyalties/of_workspace', [
                'as' => 'api.workspaces.loyalties.of_workspace',
                'uses' => 'Workspace\LoyaltyAPIController@getLoyaltyOfWorkspace'
            ]);

            // Validate reward by product list
            Route::get('rewards/{reward}/validate_products', [
                'as' => 'api.rewards.validate_products',
                'uses' => 'Workspace\RewardAPIController@validateRewardProducts'
            ]);

        });


        /*
        |--------------------------------------------------------------------------
        | Auth - API routes of Product
        |--------------------------------------------------------------------------
        */
        Route::match(['get', 'post', 'put'], 'products/{product}/like', [
            'as' => 'api.products.like',
            'uses' => 'ProductAPIController@like'
        ]);
        Route::match(['get', 'post', 'put'], 'products/{product}/unlike', [
            'as' => 'api.products.unlike',
            'uses' => 'ProductAPIController@unlike'
        ]);
        Route::match(['get', 'post', 'put'], 'products/{product}/toggle_like', [
            'as' => 'api.products.toggle_like',
            'uses' => 'ProductAPIController@toggleLike'
        ]);


        /*
        |--------------------------------------------------------------------------
        | API routes of Order
        |--------------------------------------------------------------------------
        */
        Route::get('orders', ['as' => 'api.orders.index', 'uses' => 'OrderAPIController@index']);
        Route::post('orders', ['as' => 'api.orders.store', 'uses' => 'OrderAPIController@store']);
        Route::get('orders/history', ['as' => 'api.orders.history', 'uses' => 'OrderAPIController@history']);
        Route::get('orders/{order}', ['as' => 'api.orders.show', 'uses' => 'OrderAPIController@show']);
//        Route::put('orders/{order}', ['as' => 'api.orders.update', 'uses' => 'OrderAPIController@update']);
//        Route::patch('orders/{order}', ['as' => 'api.orders.patch', 'uses' => 'OrderAPIController@update']);
//        Route::delete('orders/{order}', ['as' => 'api.orders.destroy', 'uses' => 'OrderAPIController@destroy']);

        // Update payment method, payment status
        Route::put('orders/{order}/update_payment', [
            'as' => 'api.orders.update_payment',
            'uses' => 'OrderAPIController@updatePayment'
        ]);

        // Cancel a order
        Route::put('orders/{order}/cancel', [
            'as' => 'api.orders.cancel',
            'uses' => 'OrderAPIController@cancel'
        ]);


        /*
        |--------------------------------------------------------------------------
        | API routes of Loyalty
        |--------------------------------------------------------------------------
        */
        Route::get('loyalties', ['as' => 'api.loyalties.index', 'uses' => 'LoyaltyAPIController@index']);
//        Route::post('loyalties', ['as' => 'api.loyalties.store', 'uses' => 'LoyaltyAPIController@store']);
        Route::get('loyalties/{loyalty}', ['as' => 'api.loyalties.show', 'uses' => 'LoyaltyAPIController@show']);
//        Route::put('loyalties/{loyalty}', ['as' => 'api.loyalties.update', 'uses' => 'LoyaltyAPIController@update']);
//        Route::patch('loyalties/{loyalty}', ['as' => 'api.loyalties.patch', 'uses' => 'LoyaltyAPIController@update']);
//        Route::delete('loyalties/{loyalty}', ['as' => 'api.loyalties.destroy', 'uses' => 'LoyaltyAPIController@destroy']);

        // Create a redeem with reward
        Route::post('loyalties/{loyalty}/redeem/{reward_id}', [
            'as' => 'api.loyalties.redeem',
            'uses' => 'LoyaltyAPIController@createRedeem'
        ]);

        // Get a redeem history by reward
        Route::get('loyalties/{loyalty}/redeem/{reward_id}/last_reward', [
            'as' => 'api.loyalties.redeem.last_reward',
            'uses' => 'LoyaltyAPIController@getLastReward'
        ]);

        // Get a redeem history by reward with type is physical
        Route::get('loyalties/{loyalty}/redeem/{reward_id}/last_reward_physical', [
            'as' => 'api.loyalties.redeem.last_reward_physical',
            'uses' => 'LoyaltyAPIController@getLastRewardPhysical'
        ]);


        /*
        |--------------------------------------------------------------------------
        | API routes of Notification
        |--------------------------------------------------------------------------
        */
        Route::get('notifications', ['as' => 'api.notifications.index', 'uses' => 'NotificationAPIController@index']);
//        Route::post('notifications', ['as' => 'api.notifications.store', 'uses' => 'NotificationAPIController@store']);
        // Submit Device ID
        Route::post('notifications/device', [
            'as' => 'api.notifications.device',
            'uses' => 'NotificationAPIController@device'
        ]);
        // Unsubscribe Device ID
        Route::post('notifications/device/unsubscribe', [
            'as' => 'api.notifications.unsubscribe_device',
            'uses' => 'NotificationAPIController@unsubscribeDevice'
        ]);
        // Enable Device ID (allow push notification when exist device)
        Route::post('notifications/device/enable', [
            'as' => 'api.notifications.enable_device',
            'uses' => 'NotificationAPIController@enableDevice'
        ]);
        // Disable Device ID (prevent push notification when exist device)
        Route::post('notifications/device/disable', [
            'as' => 'api.notifications.disable_device',
            'uses' => 'NotificationAPIController@disableDevice'
        ]);
        // Mark as read all notification of user
        Route::get('notifications/read', [
            'as' => 'api.notifications.read',
            'uses' => 'NotificationAPIController@markAsRead'
        ]);
        Route::get('notifications/{notification}', ['as' => 'api.notifications.show', 'uses' => 'NotificationAPIController@show']);
//        Route::put('notifications/{notification}', ['as' => 'api.notifications.update', 'uses' => 'NotificationAPIController@update']);
//        Route::patch('notifications/{notification}', ['as' => 'api.notifications.patch', 'uses' => 'NotificationAPIController@update']);
        Route::delete('notifications/{notification}', ['as' => 'api.notifications.destroy', 'uses' => 'NotificationAPIController@destroy']);

    });


    /*
    |--------------------------------------------------------------------------
    | API routes of Workspace
    |--------------------------------------------------------------------------
    */
    // Get category (type) list
    Route::get('workspaces/categories', [
        'as' => 'api.workspaces.categories',
        'uses' => 'WorkspaceAPIController@categories',
    ]);

    Route::get('workspaces', [
        'as' => 'api.workspaces.index',
        'uses' => 'WorkspaceAPIController@index',
    ]);
    Route::get('workspaces/{workspace}', [
        'as' => 'api.workspaces.show',
        'uses' => 'WorkspaceAPIController@show',
    ]);
    Route::get('workspaces/{workspace}/languages', [
        'as' => 'api.workspaces.languages',
        'uses' => 'WorkspaceAPIController@languages',
    ]);


    /*
    |--------------------------------------------------------------------------
    | API routes of Workspace group
    |--------------------------------------------------------------------------
    */
    Route::group([
        'prefix' => 'workspaces/{workspace}',
    ], function () {
        /*
        |--------------------------------------------------------------------------
        | API routes of Workspace > Contacts
        |--------------------------------------------------------------------------
        */
        Route::get('contacts', ['as' => 'api.workspaces.contacts.index', 'uses' => 'Workspace\ContactAPIController@index']);
        Route::post('contacts', ['as' => 'api.workspaces.contacts.store', 'uses' => 'Workspace\ContactAPIController@store']);


        /*
        |--------------------------------------------------------------------------
        | API routes of SettingPayment
        |--------------------------------------------------------------------------
        */
        Route::post('jobs', ['as' => 'api.workspaces.jobs.store', 'uses' => 'Workspace\JobAPIController@store']);


        /*
        |--------------------------------------------------------------------------
        | API routes of Workspace > Settings
        |--------------------------------------------------------------------------
        */
        Route::group([
            'prefix' => 'settings',
            'as' => 'api.workspaces.settings.'
        ], function () {
            // List of delivery condition
            Route::get('delivery_conditions', [
                'as' => 'delivery_conditions',
                'uses' => 'Workspace\SettingAPIController@deliveryConditions'
            ]);

            // Get workspace settings by id
            Route::get('/', [
                'as' => 'api.workspaces.app_settings_by_id',
                'uses' => 'WorkspaceAPIController@getAppSettingsById'
            ]);

            // Min delivery condition
            Route::get('delivery_conditions/min', [
                'as' => 'min_delivery_condition',
                'uses' => 'Workspace\SettingAPIController@minDeliveryCondition'
            ]);

            // Open hour settings
            Route::get('opening_hours', [
                'as' => 'opening_hours',
                'uses' => 'Workspace\SettingAPIController@openingHours'
            ]);

            // Get holidays from settings
            Route::get('holiday_exceptions', [
                'as' => 'holiday_exceptions',
                'uses' => 'Workspace\SettingAPIController@holidayExceptions'
            ]);

            // Get timeslots from settings
            Route::get('timeslots', [
                'as' => 'timeslots',
                'uses' => 'Workspace\SettingAPIController@timeslots'
            ]);

            // Check timeslot by order days
            Route::get('timeslot_order_days', [
                'as' => 'timeslot_order_days',
                'uses' => 'Workspace\SettingAPIController@checkTimeslotOrderDays'
            ]);

            // Preference settings
            Route::get('preferences', [
                'as' => 'preferences',
                'uses' => 'Workspace\SettingAPIController@preferences'
            ]);

            // Service cost setting
            Route::get('service-cost', [
                'as' => 'preferences_service_cost',
                'uses' => 'Workspace\SettingAPIController@serviceCostSetting'
            ]);

            /*
            |--------------------------------------------------------------------------
            | API routes of SettingPayment
            |--------------------------------------------------------------------------
            */
            Route::get('payment_methods', [
                'as' => 'payment_methods.index',
                'uses' => 'Workspace\PaymentMethodAPIController@index'
            ]);
            Route::get('payment_methods/{setting_payment}', [
                'as' => 'payment_methods.show',
                'uses' => 'Workspace\PaymentMethodAPIController@show'
            ]);

        });

    });


    /*
    |--------------------------------------------------------------------------
    | API routes of Group
    |--------------------------------------------------------------------------
    */
    Route::get('groups', ['as' => 'api.groups.index', 'uses' => 'GroupAPIController@index']);
//    Route::post('groups', ['as' => 'api.groups.store', 'uses' => 'GroupAPIController@store']);
    Route::get('groups/{group}', ['as' => 'api.groups.show', 'uses' => 'GroupAPIController@show']);
//    Route::put('groups/{group}', ['as' => 'api.groups.update', 'uses' => 'GroupAPIController@update']);
//    Route::patch('groups/{group}', ['as' => 'api.groups.patch', 'uses' => 'GroupAPIController@update']);
//    Route::delete('groups/{group}', ['as' => 'api.groups.destroy', 'uses' => 'GroupAPIController@destroy']);


    /*
    |--------------------------------------------------------------------------
    | API routes of Category
    |--------------------------------------------------------------------------
    */
    Route::get('categories', ['as' => 'api.categories.index', 'uses' => 'CategoryAPIController@index']);
    Route::get('categories/products', ['as' => 'api.categories.products', 'uses' => 'CategoryAPIController@products']);
    Route::get('categories/check_available', ['as' => 'api.categories.check_available', 'uses' => 'CategoryAPIController@checkAvailable']);
//    Route::post('categories', ['as' => 'api.categories.store', 'uses' => 'CategoryAPIController@store']);
    Route::get('categories/{category}', ['as' => 'api.categories.show', 'uses' => 'CategoryAPIController@show']);
//    Route::put('categories/{category}', ['as' => 'api.categories.update', 'uses' => 'CategoryAPIController@update']);
//    Route::patch('categories/{category}', ['as' => 'api.categories.patch', 'uses' => 'CategoryAPIController@update']);
//    Route::delete('categories/{category}', ['as' => 'api.categories.destroy', 'uses' => 'CategoryAPIController@destroy']);
    Route::get('categories/{category}/suggestion_products',
        ['as' => 'api.categories.suggestion_products', 'uses' => 'CategoryAPIController@getSuggestionProducts']);


    /*
    |--------------------------------------------------------------------------
    | API routes of Product
    |--------------------------------------------------------------------------
    */
    Route::get('products', ['as' => 'api.products.index', 'uses' => 'ProductAPIController@index']);
//    Route::post('products', ['as' => 'api.products.store', 'uses' => 'ProductAPIController@store']);
    Route::get('products/list', ['as' => 'api.products.list', 'uses' => 'ProductAPIController@list']);
    Route::get('products/liked', ['as' => 'api.products.liked', 'uses' => 'ProductAPIController@liked']);
    Route::get('products/validate_available_delivery', ['as' => 'api.products.validate_available_delivery',
        'uses' => 'ProductAPIController@validateAvailableDelivery']);
    Route::get('products/validate_available_timeslot', ['as' => 'api.products.validate_available_timeslot',
        'uses' => 'ProductAPIController@validateAvailableTimeslot']);
    Route::get('products/validate_available_timeslot_date_time', ['as' => 'api.products.validate_available_timeslot_date_time',
        'uses' => 'ProductAPIController@validateAvailableTimeslotsDateAndTime']);
    Route::get('products/validate_coupon', ['as' => 'api.products.validate_coupon',
        'uses' => 'ProductAPIController@validateCoupon']);
    Route::get('products/check_available', ['as' => 'api.products.check_available', 'uses' => 'ProductAPIController@checkAvailable']);
    Route::get('products/{product}', ['as' => 'api.products.show', 'uses' => 'ProductAPIController@show']);
//    Route::put('products/{product}', ['as' => 'api.products.update', 'uses' => 'ProductAPIController@update']);
//    Route::patch('products/{product}', ['as' => 'api.products.patch', 'uses' => 'ProductAPIController@update']);
//    Route::delete('products/{product}', ['as' => 'api.products.destroy', 'uses' => 'ProductAPIController@destroy']);
    Route::get('products/{product}/options', ['as' => 'api.products.options', 'uses' => 'ProductAPIController@options']);

    // Validate timeslot of product
    Route::get('products/{product}/validate_timeslot', [
        'as' => 'api.products.validate_timeslot',
        'uses' => 'ProductAPIController@validateTimeslot'
    ]);

    /*
    |--------------------------------------------------------------------------
    | API routes of Coupon
    |--------------------------------------------------------------------------
    */
    Route::get('coupons', ['as' => 'api.coupons.index', 'uses' => 'CouponAPIController@index']);
//    Route::post('coupons', ['as' => 'api.coupons.store', 'uses' => 'CouponAPIController@store']);
    Route::get('coupons/validate_code', ['as' => 'api.coupons.validate_code', 'uses' => 'CouponAPIController@validateCode']);
    Route::get('coupons/{coupon}', ['as' => 'api.coupons.show', 'uses' => 'CouponAPIController@show']);
//    Route::put('coupons/{coupon}', ['as' => 'api.coupons.update', 'uses' => 'CouponAPIController@update']);
//    Route::patch('coupons/{coupon}', ['as' => 'api.coupons.patch', 'uses' => 'CouponAPIController@update']);
//    Route::delete('coupons/{coupon}', ['as' => 'api.coupons.destroy', 'uses' => 'CouponAPIController@destroy']);


    /*
    |--------------------------------------------------------------------------
    | API routes of VAT
    |--------------------------------------------------------------------------
    */
    Route::get('vats', ['as' => 'api.vats.index', 'uses' => 'VatAPIController@index']);
//    Route::post('vats', ['as' => 'api.vats.store', 'uses' => 'VatAPIController@store']);
    Route::get('vats/{vat}', ['as' => 'api.vats.show', 'uses' => 'VatAPIController@show']);
//    Route::put('vats/{vat}', ['as' => 'api.vats.update', 'uses' => 'VatAPIController@update']);
//    Route::patch('vats/{vat}', ['as' => 'api.vats.patch', 'uses' => 'VatAPIController@update']);
//    Route::delete('vats/{vat}', ['as' => 'api.vats.destroy', 'uses' => 'VatAPIController@destroy']);

    /*
    |--------------------------------------------------------------------------
    | API routes of Mollie
    |--------------------------------------------------------------------------
    */
    Route::any('mollie', ['as' => 'api.mollie.index', 'uses' => 'MollieGateController@transaction']);
    Route::any('mollie/callback/{orderId}', ['as' => 'api.mollie.callback', 'uses' => 'MollieGateController@callback']);
    Route::any('mollie/webhook/{orderId}', ['as' => 'api.mollie.webhook', 'uses' => 'MollieGateController@webhook']);


    /*
    |--------------------------------------------------------------------------
    | API routes of City
    |--------------------------------------------------------------------------
    */
    Route::get('cities', ['as' => 'api.cities.index', 'uses' => 'CityAPIController@index']);
    //Route::post('cities', ['as' => 'api.cities.store', 'uses' => 'CityAPIController@store']);
    Route::get('cities/{city}', ['as' => 'api.cities.show', 'uses' => 'CityAPIController@show']);
    //Route::put('cities/{city}', ['as' => 'api.cities.update', 'uses' => 'CityAPIController@update']);
    //Route::patch('cities/{city}', ['as' => 'api.cities.patch', 'uses' => 'CityAPIController@update']);
    //Route::delete('cities/{city}', ['as' => 'api.cities.destroy', 'uses' => 'CityAPIController@destroy']);


    /*
    |--------------------------------------------------------------------------
    | API routes of Address
    |--------------------------------------------------------------------------
    */
    Route::get('addresses', ['as' => 'api.addresses.index', 'uses' => 'AddressAPIController@index']);
    //Route::post('addresses', ['as' => 'api.addresses.store', 'uses' => 'AddressAPIController@store']);
    Route::get('addresses/{address}', ['as' => 'api.addresses.show', 'uses' => 'AddressAPIController@show']);
    //Route::put('addresses/{address}', ['as' => 'api.addresses.update', 'uses' => 'AddressAPIController@update']);
    //Route::patch('addresses/{address}', ['as' => 'api.addresses.patch', 'uses' => 'AddressAPIController@update']);
    //Route::delete('addresses/{address}', ['as' => 'api.addresses.destroy', 'uses' => 'AddressAPIController@destroy']);
    Route::put('addresses/{address}/location', ['as' => 'api.addresses.update_location', 'uses' => 'AddressAPIController@updateLocation']);

    Route::get('pages', ['as' => 'api.page.index', 'uses' => 'PageAPIController@index']);
    Route::get('pages/{slug}', ['as' => 'api.page.bySlug', 'uses' => 'PageAPIController@bySlug']);
});


/*
|--------------------------------------------------------------------------
| API routes of Country
|--------------------------------------------------------------------------
*/
$this->group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    $this->get('countries', ['as' => 'api.countries.index', 'uses' => 'CountryAPIController@index']);
    $this->post('countries', ['as' => 'api.countries.store', 'uses' => 'CountryAPIController@store']);
    $this->get('countries/{countries}', ['as' => 'api.countries.show', 'uses' => 'CountryAPIController@show']);
    $this->put('countries/{countries}', ['as' => 'api.countries.update', 'uses' => 'CountryAPIController@update']);
    $this->patch('countries/{countries}', ['as' => 'api.countries.patch', 'uses' => 'CountryAPIController@update']);
    $this->delete('countries{countries}', ['as' => 'api.countries.destroy', 'uses' => 'CountryAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of Contact
|--------------------------------------------------------------------------
*/
$this->group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    $this->get('contacts', ['as' => 'api.contacts.index', 'uses' => 'ContactAPIController@index']);
    $this->post('contacts', ['as' => 'api.contacts.store', 'uses' => 'ContactAPIController@store']);
    $this->get('contacts/{contacts}', ['as' => 'api.contacts.show', 'uses' => 'ContactAPIController@show']);
    $this->put('contacts/{contacts}', ['as' => 'api.contacts.update', 'uses' => 'ContactAPIController@update']);
    $this->patch('contacts/{contacts}', ['as' => 'api.contacts.patch', 'uses' => 'ContactAPIController@update']);
    $this->delete('contacts{contacts}', ['as' => 'api.contacts.destroy', 'uses' => 'ContactAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of Role
|--------------------------------------------------------------------------
*/
$this->group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    $this->get('roles', ['as' => 'api.roles.index', 'uses' => 'RoleAPIController@index']);
    $this->post('roles', ['as' => 'api.roles.store', 'uses' => 'RoleAPIController@store']);
    $this->get('roles/{roles}', ['as' => 'api.roles.show', 'uses' => 'RoleAPIController@show']);
    $this->put('roles/{roles}', ['as' => 'api.roles.update', 'uses' => 'RoleAPIController@update']);
    $this->patch('roles/{roles}', ['as' => 'api.roles.patch', 'uses' => 'RoleAPIController@update']);
    $this->delete('roles{roles}', ['as' => 'api.roles.destroy', 'uses' => 'RoleAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of Banner
|--------------------------------------------------------------------------
*/
$this->group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    $this->get('banners', ['as' => 'api.banners.index', 'uses' => 'BannerAPIController@index']);
    $this->post('banners', ['as' => 'api.banners.store', 'uses' => 'BannerAPIController@store']);
    $this->get('banners/{banners}', ['as' => 'api.banners.show', 'uses' => 'BannerAPIController@show']);
    $this->put('banners/{banners}', ['as' => 'api.banners.update', 'uses' => 'BannerAPIController@update']);
    $this->patch('banners/{banners}', ['as' => 'api.banners.patch', 'uses' => 'BannerAPIController@update']);
    $this->delete('banners{banners}', ['as' => 'api.banners.destroy', 'uses' => 'BannerAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of WorkspaceExtra
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('workspace_extras', ['as' => 'api.workspace_extras.index', 'uses' => 'WorkspaceExtraAPIController@index']);
    Route::post('workspace_extras', ['as' => 'api.workspace_extras.store', 'uses' => 'WorkspaceExtraAPIController@store']);
    Route::get('workspace_extras/{workspace_extra}', ['as' => 'api.workspace_extras.show', 'uses' => 'WorkspaceExtraAPIController@show']);
    Route::put('workspace_extras/{workspace_extra}', ['as' => 'api.workspace_extras.update', 'uses' => 'WorkspaceExtraAPIController@update']);
    Route::patch('workspace_extras/{workspace_extra}', ['as' => 'api.workspace_extras.patch', 'uses' => 'WorkspaceExtraAPIController@update']);
    Route::delete('workspace_extras/{workspace_extra}', ['as' => 'api.workspace_extras.destroy', 'uses' => 'WorkspaceExtraAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of RestaurantCategory
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('restaurant_categories', ['as' => 'api.restaurant_categories.index', 'uses' => 'RestaurantCategoryAPIController@index']);
    Route::post('restaurant_categories', ['as' => 'api.restaurant_categories.store', 'uses' => 'RestaurantCategoryAPIController@store']);
    Route::get('restaurant_categories/{restaurant_category}', ['as' => 'api.restaurant_categories.show', 'uses' => 'RestaurantCategoryAPIController@show']);
    Route::put('restaurant_categories/{restaurant_category}', ['as' => 'api.restaurant_categories.update', 'uses' => 'RestaurantCategoryAPIController@update']);
    Route::patch('restaurant_categories/{restaurant_category}', ['as' => 'api.restaurant_categories.patch', 'uses' => 'RestaurantCategoryAPIController@update']);
    Route::delete('restaurant_categories/{restaurant_category}', ['as' => 'api.restaurant_categories.destroy', 'uses' => 'RestaurantCategoryAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingGeneral
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_generals', ['as' => 'api.setting_generals.index', 'uses' => 'SettingGeneralAPIController@index']);
    Route::post('setting_generals', ['as' => 'api.setting_generals.store', 'uses' => 'SettingGeneralAPIController@store']);
    Route::get('setting_generals/{setting_general}', ['as' => 'api.setting_generals.show', 'uses' => 'SettingGeneralAPIController@show']);
    Route::put('setting_generals/{setting_general}', ['as' => 'api.setting_generals.update', 'uses' => 'SettingGeneralAPIController@update']);
    Route::patch('setting_generals/{setting_general}', ['as' => 'api.setting_generals.patch', 'uses' => 'SettingGeneralAPIController@update']);
    Route::delete('setting_generals/{setting_general}', ['as' => 'api.setting_generals.destroy', 'uses' => 'SettingGeneralAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingPayment
|--------------------------------------------------------------------------
*/
//Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
//    Route::get('setting_payments', ['as' => 'api.setting_payments.index', 'uses' => 'SettingPaymentAPIController@index']);
//    Route::post('setting_payments', ['as' => 'api.setting_payments.store', 'uses' => 'SettingPaymentAPIController@store']);
//    Route::get('setting_payments/{setting_payment}', ['as' => 'api.setting_payments.show', 'uses' => 'SettingPaymentAPIController@show']);
//    Route::put('setting_payments/{setting_payment}', ['as' => 'api.setting_payments.update', 'uses' => 'SettingPaymentAPIController@update']);
//    Route::patch('setting_payments/{setting_payment}', ['as' => 'api.setting_payments.patch', 'uses' => 'SettingPaymentAPIController@update']);
//    Route::delete('setting_payments/{setting_payment}', ['as' => 'api.setting_payments.destroy', 'uses' => 'SettingPaymentAPIController@destroy']);
//});


/*
|--------------------------------------------------------------------------
| API routes of SettingPreference
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_preferences', ['as' => 'api.setting_preferences.index', 'uses' => 'SettingPreferenceAPIController@index']);
    Route::post('setting_preferences', ['as' => 'api.setting_preferences.store', 'uses' => 'SettingPreferenceAPIController@store']);
    Route::get('setting_preferences/{setting_preference}', ['as' => 'api.setting_preferences.show', 'uses' => 'SettingPreferenceAPIController@show']);
    Route::put('setting_preferences/{setting_preference}', ['as' => 'api.setting_preferences.update', 'uses' => 'SettingPreferenceAPIController@update']);
    Route::patch('setting_preferences/{setting_preference}', ['as' => 'api.setting_preferences.patch', 'uses' => 'SettingPreferenceAPIController@update']);
    Route::delete('setting_preferences/{setting_preference}', ['as' => 'api.setting_preferences.destroy', 'uses' => 'SettingPreferenceAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingPrint
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_prints', ['as' => 'api.setting_prints.index', 'uses' => 'SettingPrintAPIController@index']);
    Route::post('setting_prints', ['as' => 'api.setting_prints.store', 'uses' => 'SettingPrintAPIController@store']);
    Route::get('setting_prints/{setting_print}', ['as' => 'api.setting_prints.show', 'uses' => 'SettingPrintAPIController@show']);
    Route::put('setting_prints/{setting_print}', ['as' => 'api.setting_prints.update', 'uses' => 'SettingPrintAPIController@update']);
    Route::patch('setting_prints/{setting_print}', ['as' => 'api.setting_prints.patch', 'uses' => 'SettingPrintAPIController@update']);
    Route::delete('setting_prints/{setting_print}', ['as' => 'api.setting_prints.destroy', 'uses' => 'SettingPrintAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingDeliveryConditions
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_delivery_conditions', ['as' => 'api.setting_delivery_conditions.index', 'uses' => 'SettingDeliveryConditionsAPIController@index']);
    Route::post('setting_delivery_conditions', ['as' => 'api.setting_delivery_conditions.store', 'uses' => 'SettingDeliveryConditionsAPIController@store']);
    Route::get('setting_delivery_conditions/{setting_delivery_conditions}', ['as' => 'api.setting_delivery_conditions.show', 'uses' => 'SettingDeliveryConditionsAPIController@show']);
    Route::put('setting_delivery_conditions/{setting_delivery_conditions}', ['as' => 'api.setting_delivery_conditions.update', 'uses' => 'SettingDeliveryConditionsAPIController@update']);
    Route::patch('setting_delivery_conditions/{setting_delivery_conditions}', ['as' => 'api.setting_delivery_conditions.patch', 'uses' => 'SettingDeliveryConditionsAPIController@update']);
    Route::delete('setting_delivery_conditions/{setting_delivery_conditions}', ['as' => 'api.setting_delivery_conditions.destroy', 'uses' => 'SettingDeliveryConditionsAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingOpenHour
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_open_hours', ['as' => 'api.setting_open_hours.index', 'uses' => 'SettingOpenHourAPIController@index']);
    Route::post('setting_open_hours', ['as' => 'api.setting_open_hours.store', 'uses' => 'SettingOpenHourAPIController@store']);
    Route::get('setting_open_hours/{setting_open_hour}', ['as' => 'api.setting_open_hours.show', 'uses' => 'SettingOpenHourAPIController@show']);
    Route::put('setting_open_hours/{setting_open_hour}', ['as' => 'api.setting_open_hours.update', 'uses' => 'SettingOpenHourAPIController@update']);
    Route::patch('setting_open_hours/{setting_open_hour}', ['as' => 'api.setting_open_hours.patch', 'uses' => 'SettingOpenHourAPIController@update']);
    Route::delete('setting_open_hours/{setting_open_hour}', ['as' => 'api.setting_open_hours.destroy', 'uses' => 'SettingOpenHourAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingTimeslot
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_timeslots', ['as' => 'api.setting_timeslots.index', 'uses' => 'SettingTimeslotAPIController@index']);
    Route::post('setting_timeslots', ['as' => 'api.setting_timeslots.store', 'uses' => 'SettingTimeslotAPIController@store']);
    Route::get('setting_timeslots/{setting_timeslot}', ['as' => 'api.setting_timeslots.show', 'uses' => 'SettingTimeslotAPIController@show']);
    Route::put('setting_timeslots/{setting_timeslot}', ['as' => 'api.setting_timeslots.update', 'uses' => 'SettingTimeslotAPIController@update']);
    Route::patch('setting_timeslots/{setting_timeslot}', ['as' => 'api.setting_timeslots.patch', 'uses' => 'SettingTimeslotAPIController@update']);
    Route::delete('setting_timeslots/{setting_timeslot}', ['as' => 'api.setting_timeslots.destroy', 'uses' => 'SettingTimeslotAPIController@destroy']);
});


/*
|--------------------------------------------------------------------------
| API routes of SettingTimeslotDetail
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'jwt.auth']], function () {
    Route::get('setting_timeslot_details', ['as' => 'api.setting_timeslot_details.index', 'uses' => 'SettingTimeslotDetailAPIController@index']);
    Route::post('setting_timeslot_details', ['as' => 'api.setting_timeslot_details.store', 'uses' => 'SettingTimeslotDetailAPIController@store']);
    Route::get('setting_timeslot_details/{setting_timeslot_detail}', ['as' => 'api.setting_timeslot_details.show', 'uses' => 'SettingTimeslotDetailAPIController@show']);
    Route::put('setting_timeslot_details/{setting_timeslot_detail}', ['as' => 'api.setting_timeslot_details.update', 'uses' => 'SettingTimeslotDetailAPIController@update']);
    Route::patch('setting_timeslot_details/{setting_timeslot_detail}', ['as' => 'api.setting_timeslot_details.patch', 'uses' => 'SettingTimeslotDetailAPIController@update']);
    Route::delete('setting_timeslot_details/{setting_timeslot_detail}', ['as' => 'api.setting_timeslot_details.destroy', 'uses' => 'SettingTimeslotDetailAPIController@destroy']);
});

/*
|--------------------------------------------------------------------------
| API routes of Connector
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'api/v1/connectors', 'middleware' => ['api']], function () {
    Route::post('{connectorId}/auth', ['as' => 'api.connectors.auth', 'uses' => 'ConnectorAPIController@auth']);

    Route::group(['middleware' => ['connector']], function () {
        Route::get('{connectorId}/orders', ['as' => 'api.connectors.orders', 'uses' => 'ConnectorAPIController@orders']);
        Route::get('{connectorId}/categories', ['as' => 'api.connectors.categories', 'uses' => 'ConnectorAPIController@categories']);
        Route::get('{connectorId}/products', ['as' => 'api.connectors.products', 'uses' => 'ConnectorAPIController@products']);
    });
});

Route::group(['prefix' => 'api/v1', 'middleware' => ['api', 'cors', 'handleCors', 'nonPost']], function () {
    Route::get('deeplink/configuration', ['as' => 'api.orders.deeplinkConfiguration', 'uses' => 'OrderAPIController@deeplinkConfiguration']);
    Route::get('orders/{order}/detail', ['as' => 'api.orders.show', 'uses' => 'OrderAPIController@show']);
    //Route::put('orders/{order}/payment', ['as' => 'api.orders.update_payment', 'uses' => 'OrderAPIController@updatePayment']);
    //Route::put('orders/{order}/payment/cancel', ['as' => 'api.orders.cancel', 'uses' => 'OrderAPIController@cancel']);
});
