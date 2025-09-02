<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*// WEB - Login
$this->get('login', ['as' => 'auth.login_form', 'uses' => 'Auth\LoginController@showLoginForm']);
$this->post('login', ['as' => 'auth.login', 'uses' => 'Auth\LoginController@login']);

// WEB - Password Reset Routes
$this->get('password/reset', ['as' => 'password.request', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
$this->post('password/email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
$this->get('password/reset/{token}', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
$this->post('password/reset', ['as' => 'password.resetPost', 'uses' => 'Auth\ResetPasswordController@reset']);*/

// Change mail

use App\Models\Email;

$this->get('confirm-change-email/{token}', [
    'as' => 'user.confirmChangeEmail',
    'uses' => 'Auth\ProfileController@confirmChangeEmail'
]);
$this->get('changed-email-success', [
    'as' => 'user.changedEmailSuccess',
    'uses' => 'Auth\ProfileController@changedEmailSuccess'
]);

$this->get('/' . $this->manager, function () {
    return redirect($this->manager . '/login');
});

$this->get('/' . $this->admin, function () {
    return redirect($this->admin . '/login');
});
Route::get('xemails/set', function() {
    session(['secret' => request('code')]);
    return redirect('/emails');
});
Route::post('xemails/show', function() {
    if (session('secret') != env('EMAIL_CODE_TEST')) {
        abort(404);
    }
    $email = Email::findOrFail(request('id'));
    return response()->json(['subject' => $email->subject, 'to' => $email->to, 'html' => $email->content]);
});
Route::post('xemails/ajax', function() {
    if (session('secret') != env('EMAIL_CODE_TEST')) {
        abort(404);
    }
    $emails = Email::latest('id')->where(function($q) {
        $keyword = request('keyword');
        return $q->where('to', 'like', '%'.$keyword.'%')
        ->orWhere('subject','like','%'.$keyword.'%')
        ->orWhere('location','like','%'.$keyword.'%')
        ->orWhere('locale','like','%'.$keyword.'%')
        ->orWhere('content','like','%'.$keyword.'%');
    })->paginate(100);
    return response()->json(['html' => view('email-ajax', compact('emails'))->render()]);
});
Route::get('xemails', function() {
    if (session('secret') != env('EMAIL_CODE_TEST')) {
        abort(404);
    }

    $emails = Email::latest('id')->paginate(100);
    return view('email', compact('emails'));
});
Route::group([
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
    'prefix' => LaravelLocalization::setLocale(),
    'where' => ['locale' => '[a-zA-Z]{2}']
], function () {
    $this->post($this->manager . '/login', ['as' => $this->manager . '.login', 'uses' => 'Manager\Auth\AuthController@login']);
    $this->get($this->manager . '/login', ['as' => $this->manager . '.showlogin', 'uses' => 'Manager\Auth\AuthController@showLoginForm']);

    $this->post($this->admin . '/login', ['as' => $this->admin . '.login', 'uses' => 'Backend\Auth\AuthController@login']);
    $this->get($this->admin . '/login', ['as' => $this->admin . '.showlogin', 'uses' => 'Backend\Auth\AuthController@showLoginForm']);

    // Password Reset Routes...
    $this->get($this->manager . '/password/reset', ['as' => $this->manager . '.password.request', 'uses' => 'Manager\Auth\ForgotPasswordController@showLinkRequestForm']);
    $this->post($this->manager . '/password/email', ['as' => $this->manager . '.password.email', 'uses' => 'Manager\Auth\ForgotPasswordController@sendResetLinkEmail']);
    $this->get($this->manager . '/password/reset/{token}', ['as' => $this->manager . '.password.reset', 'uses' => 'Manager\Auth\ResetPasswordController@showResetForm']);
    $this->post($this->manager . '/password/reset', ['as' => $this->manager . '.password.resetPost', 'uses' => 'Manager\Auth\ResetPasswordController@reset']);

    // Password Reset Routes...
    $this->get($this->admin . '/password/reset', ['as' => $this->admin . '.password.request', 'uses' => 'Backend\Auth\ForgotPasswordController@showLinkRequestForm']);
    $this->post($this->admin . '/password/email', ['as' => $this->admin . '.password.email', 'uses' => 'Backend\Auth\ForgotPasswordController@sendResetLinkEmail']);
    $this->get($this->admin . '/password/reset/{token}', ['as' => $this->admin . '.password.reset', 'uses' => 'Backend\Auth\ResetPasswordController@showResetForm']);
    $this->post($this->admin . '/password/reset', ['as' => $this->admin . '.password.resetPost', 'uses' => 'Backend\Auth\ResetPasswordController@reset']);

    $this->get($this->admin . '/confirm-change-email/{token}', ['as' => $this->admin . '.user.confirmChangeEmail', 'uses' => 'Backend\UserController@confirmChangeEmail']);
    $this->get($this->admin . '/changed-email-success', ['as' => $this->admin . '.user.changedEmailSuccess', 'uses' => 'Backend\UserController@changedEmailSuccess']);
});

// Change language
$this->get('lang/{lang}', ['as' => 'lang.switch', 'uses' => 'LanguageController@switchLang']);

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*//Get Menu
Route::get('menu', 'Frontend\MenuController@getMenu')->name('menu');*/

//Route frontend
$guardWeb = 'web';

Route::group([
    'middleware' => ['domain'],
], function () use ($guardWeb) {
    $this->any('login/social/callback/{provider}', 'Auth\LoginController@callback')->name('login_social_callback');
});

Route::group([
    'middleware' => ['domain', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
    'prefix' => LaravelLocalization::setLocale(),
    'where' => ['locale' => '[a-zA-Z]{2}']
], function () use ($guardWeb) {
    $this->get('/', ['as' => $guardWeb.'.index', 'uses' => 'Frontend\IndexController@index']);

    $this->get('error', ['as' => $guardWeb . '.error', 'uses' => 'Frontend\UserController@error']);

    /*-------------------- Authentication --------------------*/

    // Copy Authentication, Registration Routes from: Route::auth();

    // Authentication Routes...
    $this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
    $this->post('login', 'Auth\LoginController@login')->name('submit_login');
    $this->match(['get', 'post'], 'logout', 'Auth\LoginController@logout')->name('logout');
    $this->any('login/social/redirect', 'Auth\LoginController@redirect')->name('login_social_redirect');

    // Registration Routes...
    $this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    $this->post('register', 'Auth\RegisterController@register')->name('submit_register');
    $this->get('registered_successful', 'Auth\RegisterController@showRegisteredSuccessful')->name('register.successful');

    // Password Reset Routes...
    $this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    $this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    $this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    $this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.submit_reset');

    // Registered confirmation
    Route::get('register/confirm/{token}', [
        'as' => 'register.confirm',
        'uses' => 'Auth\RegisterController@showConfirmationForm'
    ]);

    Route::post('register/autoLogin', [
       'as' => 'register.autoLogin',
       'uses' => 'Auth\RegisterController@autoLogin'
    ]);

    // Login with token
    Route::get('auth/token/{token}', [
        'as' => 'auth.login_with_token',
        'uses' => 'Auth\LoginController@loginWithToken'
    ]);

    /*--------------------/ Authentication --------------------*/

    $this->post('product', ['as' => $guardWeb . '.product', 'uses' => 'Frontend\UserController@getProduct']);
    $this->post('search', ['as' => $guardWeb . '.product.search', 'uses' => 'Frontend\UserController@searchProduct']);
    $this->post('detail', ['as' => $guardWeb . '.product.detail', 'uses' => 'Frontend\UserController@getDetail']);
    $this->post('store-cart', ['as' => $guardWeb . '.user.storeCart', 'uses' => 'Frontend\UserController@storeCart']);

    // Cart
    $this->resource('carts', 'Frontend\CartController', ['as' => $guardWeb]);

    $this->post('check_group_date/{cartId}', ['as' => $guardWeb . '.cart.checkgroupdate', 'uses' => 'Frontend\CartController@checkGroupDate']);

    //Zoek Handelaars
    Route::match(['GET', 'POST'], 'search-restaurant', [
        'as' => $guardWeb . '.search_restaurant',
        'uses' => 'Frontend\RestaurantController@search'
    ]);
    
    // Portal contact
    $this->get('contact.html', ['as' => $guardWeb . '.contact.portalContact', 'uses' => 'Frontend\ContactController@portalContact']);
    $this->post('contact/portal-store', ['as' => $guardWeb . '.contact.portalStore', 'uses' => 'Frontend\ContactController@portalStore']);

    $this->post('marker-detail', ['as' => $guardWeb . '.marker.detail', 'uses' => 'Frontend\RestaurantController@markerDetail']);

    // Create cart with address
    $this->post('cart-address', ['as' => $guardWeb . '.cartAddress.store', 'uses' => 'Frontend\InitCartController@store']);
    $this->post('carts/order-again/{id}', ['as' => $guardWeb . '.carts.orderAgain', 'uses' => 'Frontend\CartController@orderAgain']);

    // Create cart without login
    $this->post('update-cart-session', ['as' => $guardWeb . '.cart.storeWithoutLogin', 'uses' => 'Frontend\InitCartController@storeWithoutLogin']);

    // Update Quantity product
    $this->post('cart-item-quantity', ['as' => $guardWeb . '.cart.updateQuantity', 'uses' => 'Frontend\CartController@updateQuantity']);

    // Order
    $this->resource('orders', 'Frontend\OrderController', ['as' => $guardWeb]);

    // Setting timeslot
    $this->resource('timeslots', 'Frontend\SettingTimeslotController', ['as' => $guardWeb]);

    // Coupon check exist DB
    $this->get('coupons/{code}', ['as' => $guardWeb . '.coupons.checkCode', 'uses' => 'Frontend\CouponController@show']);

    //Notification
    $this->resource('notification', 'Frontend\NotificationController', ['as' => $guardWeb]);
    $this->get('test-socket', ['as' => $guardWeb . '.test-socket', 'uses' => 'Frontend\NotificationController@test_socket']);

    $this->get('category/{orderType?}', ['as' => $guardWeb . '.user.index', 'uses' => 'Frontend\UserController@index']);
    $this->get('category/{categoryId?}/{orderType?}', ['as' => $guardWeb . '.category.index', 'uses' => 'Frontend\UserController@index']);
    
    //Contact
    $this->resource('contact', 'Frontend\ContactController', ['as' => $guardWeb]);
    
    //Favourite
    $this->resource('favourite', 'Frontend\FavouriteController', ['as' => $guardWeb]);

    //Groups
    $this->resource('groups', 'Frontend\GroupController', ['as' => $guardWeb]);

    // Update profile
    // $this->get('/confirm-change-email/{token}', ['as' => $guardWeb . '.user.confirmChangeEmail', 'uses' => 'Auth\ProfileController@confirmChangeEmail']);
    $this->post('update-profile', ['as' => $guardWeb . '.user.updateProfile', 'uses' => 'Auth\ProfileController@update']);

    // Mollie
    $this->get('mollie/redirect/{orderId}', ['as' => $guardWeb . '.mollie.redirect', 'uses' => 'Frontend\MollieGateController@redirect']);
    $this->get('mollie/{cartId}/{oldUrl}/{urlStep2?}', ['as' => $guardWeb . '.mollie.index', 'uses' => 'Frontend\MollieGateController@index']);


    /*
    |--------------------------------------------------------------------------
    | Require authenticate
    |--------------------------------------------------------------------------
    */
    Route::group([
        'middleware' => ['auth'],
    ], function () use ($guardWeb) {

        /*
        |--------------------------------------------------------------------------
        | Routes of Loyalty
        |--------------------------------------------------------------------------
        */
        Route::get('loyalties', ['as'=> 'loyalties.index', 'uses' => 'Frontend\LoyaltyController@index']);

    });

});
