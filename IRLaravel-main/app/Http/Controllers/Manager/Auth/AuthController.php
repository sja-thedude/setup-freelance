<?php

namespace App\Http\Controllers\Manager\Auth;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\AuthenticatesAndRegistersUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

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

    protected $guard = 'manager';

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
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response|void
     * @throws ValidationException
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

        //Update workspace
        if (!empty($user->workspace)) {
            $user->workspace->status = Workspace::FIRST_LOGIN;
            $user->workspace->first_login = date(config('datetime.dateTimeDb'));
            $user->workspace->save();
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
            
            session(['auth_temp' => $user]);
            
            return $this->sendLoginResponse($request);
        }

        if(!empty($user->password_tmp) &&
            !empty($user->verify_expired_at) &&
            strtotime(date(config('datetime.dateTimeDb'))) > strtotime(date(config('datetime.dateTimeDb'), strtotime($user->verify_expired_at)))) {
            $user->password_tmp = null;
            $user->verify_expired_at = null;
            $user->save();
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

            $user = $this->guard()->getLastAttempted();
            session(['auth_temp' => $user]);
            
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
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
            'platform' => User::PLATFORM_MANAGER
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
        if(!empty($user->workspace) && !empty($user->workspace->language)) {
            session(['locale' => $user->workspace->language]);
        } elseif (!empty($user->locale)) {
            session(['locale' => $user->locale]);
        }

        // Redirect back URL
        $referer = $this->redirectTo;
        $path = trim(str_replace(url('/'), '', $referer), '/');
        $params = explode('/', $path);
        $locale = session('locale');

        if (count($params) > 0 && app('laravellocalization')->checkLocaleInSupportedLocales($params[0])) {
            if ($locale && app('laravellocalization')->checkLocaleInSupportedLocales($locale)
                && !(app('laravellocalization')->getDefaultLocale() === $locale && app('laravellocalization')->hideDefaultLocaleInURL())) {
                $redirection = app('laravellocalization')->getLocalizedURL($locale, $referer);
                return new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);
            }
        }

        $this->redirectTo = $this->redirectTo;
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

        return redirect(route($this->guard.'.login'));
    }
}
