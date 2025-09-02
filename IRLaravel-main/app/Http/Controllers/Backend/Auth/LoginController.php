<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Facades\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest')->except('logout');
    }

    /**
     * Overwrite method for Specifying Additional Conditions
     * @link https://laravel.com/docs/5.5/authentication#authenticating-users
     *
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $data = $request->only($this->username(), 'password');
        // Specifying Additional Conditions with "active" field
        $data = array_merge($data, [
            'active' => User::IS_YES
        ]);

        return $data;
    }

    /**
     * Redirect to Admin panel (CMS)
     *
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $adminGuard = 'admin';
        $adminPrefix = config("module.backend");
        $auth = \Auth::guard($adminGuard)->user();

        if (!empty($auth)) {
            return redirect(Admin::route('contentManager.dashboard'));
        }

        return redirect($adminPrefix);
    }

}
