<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Traits\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'admin/users/profile';

    protected $loginView = 'auth.login';

    protected $guard = 'admin';

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        $this->loginView = $this->guard .'.'. $this->loginView;
        $this->redirectTo = route($this->guard.'.dashboard.index');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }
    
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $email = $request->get('email', null);
        $repository = new \App\Repositories\UserRepository(app());
        $user = $repository->findWhere(['email' => $email])->first();
        
        if(empty($user)) {
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }

        // Check forgot password
        if(!empty($user->password_tmp) &&
            !empty($user->verify_expired_at) &&
            strtotime(date(config('datetime.dateTimeDb'))) <= strtotime(date(config('datetime.dateTimeDb'), strtotime($user->verify_expired_at))) &&
            Hash::check($request->get('password'), $user->password_tmp)) {
            auth($this->guard)->login($user, $request->filled('remember'));
            $user->password = $user->password_tmp;
            $user->password_tmp = null;
            $user->verify_expired_at = null;
            $user->save();
            return $this->sendLoginResponse($request);
        }

        if(!empty($user->password_tmp) &&
            !empty($user->verify_expired_at) &&
            strtotime(date(config('datetime.dateTimeDb'))) > strtotime(date(config('datetime.dateTimeDb'), strtotime($user->verify_expired_at)))) {
            $user->password_tmp = null;
            $user->verify_expired_at = null;
            $user->save();
        }
        
        // Check active
        if(!$user->isSuperAdmin()) {
            if($user->status == User::STATUS_INVITATION_SENT &&
                !empty($user->verify_expired_at) &&
                strtotime(date(config('datetime.dateTimeDb'))) > strtotime(date(config('datetime.dateTimeDb'), strtotime($user->verify_expired_at)))) {
                $user->verify_expired_at = null;
                $user->status = User::STATUS_INVITATION_EXPIRED;
                $user->save();
            }
            
            if($user->status == User::STATUS_INVITATION_EXPIRED) {
                $this->incrementLoginAttempts($request);
                return $this->sendFailedLoginResponse($request);
            }
        }
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            /*-------------------- Login with web user --------------------*/
            Auth::guard(config('module.web'))->attempt(
                $this->credentials($request), $request->filled('remember')
            );
            /*-------------------- /Login with web user --------------------*/

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $email = $request->get('email', null);
        $repository = new \App\Repositories\UserRepository(app());

        if(empty($repository->checkUserBan($email))) {
            throw ValidationException::withMessages([
                'common' => [trans('auth.banned')],
            ]);
        } else {
            throw ValidationException::withMessages([
                'common' => [trans('auth.failed')],
            ]);
        }
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
            'active' => User::IS_YES,
            'platform' => User::PLATFORM_BACKOFFICE,
            'is_admin' => true
        ]);
        
        return $data;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Push locale setting of user to session
        if (!empty($user->locale)) {
            Session::put('locale', $user->locale);
        }

        $now = now();
        $user->last_login = $now;
        
        if($user->status != User::STATUS_ACTIVE) {
            $user->status = User::STATUS_ACTIVE;
            $user->is_verified = true;
            $user->verify_expired_at = null;
        }
        
        if(empty($user->first_login)) {
            $user->first_login = $now;
        }

        $user->save();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();

        return redirect($this->guard.'/login');
    }
}
