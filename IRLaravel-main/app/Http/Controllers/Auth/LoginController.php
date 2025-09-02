<?php

namespace App\Http\Controllers\Auth;

use App\Traits\APIResponse;
use App\Http\Controllers\Frontend\InitCartController;
use App\Models\SettingOpenHour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\SocialRepository;
use JWTAuthException;

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
    use APIResponse;

    private $socialRepository;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SocialRepository $socialRepo)
    {
        parent::__construct();

        $this->socialRepository = $socialRepo;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @param Request $request
     * @param int $workspaceId
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(Request $request)
    {
        if (!empty(auth()->user())) {
            return redirect($this->redirectTo);
        }

        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug)->refresh();
        $data = $this->getHomepageData($workspace);

        return view('auth.login', $data);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $loginRequest = new \App\Http\Requests\LoginRequest();
        $rules = $loginRequest->rules();
        $messages = $loginRequest->messages();

        $this->validate($request, $rules, $messages);
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
            'active' => true,
            'is_verified' => true,
        ]);

        return $data;
    }

    /**
     * Login with token
     *
     * @param Request $request
     * @param string $token
     * @return mixed
     */
    public function loginWithToken(Request $request, string $token)
    {
        $redirectTo = $request->get('redirect', '/');

        try {
            /** @var \App\Models\User $user */
            $user = JWTAuth::toUser($token);

            if (empty($user)) {
                // Redirect to the login page with a error message
                return redirect($redirectTo)
                    ->withErrors(trans('auth.token_is_expired'));
            } else {
                $activeGuard = '';
                // Auth by token
                $user = JWTAuth::authenticate($token);

                if (!empty($user)) {
                    // Login to backend
                    Auth::guard($activeGuard)->login($user);
                }

                // Clear token
                JWTAuth::invalidate($token);

                // Push locale setting of user to session
                if (!empty($user->locale)) {
                    Session::put('locale', $user->locale);
                }

                // An administrator can only be logged in one time at the same moment.
                // So when I am an administrator and I am logged in into the system,
                // another person cannot login with my email address and password (same account as me).
                // This is a kind of security check to be sure that everyone is fair.
                $user->last_session = Session::getId();
                $user->save();

                return redirect($redirectTo);
            }
        } catch (\Exception $ex) {
            return redirect($redirectTo)
                ->withErrors($ex->getMessage());
        }
    }

    protected function authenticated(Request $request, $user) {
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug)->refresh();
        $request->merge(['userId' => $user->id]);

        \App::call('App\Http\Controllers\Frontend\InitCartController@storeWithoutLogin', [
            'workspaceId' => $workspace->id,
            'userId' => $user->id
        ]);
    }

    public function redirect(Request $request) {
        try {
            session(['social_redirect' => url()->previous()]);

            $provider = $request->get('provider', null);
            $workspaceId = $request->get('workspace_id', null);
            $getDriver = $this->socialRepository->getDriver($provider, $workspaceId);

            if(empty($getDriver['status'])) {
                \DB::rollBack();
                return $this->sendError($getDriver['message'], 401);
            }

            $driver = $getDriver['driver'];

            return $driver->redirect();
        } catch (\Exception $e) {
            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;

            return $this->sendError($e->getMessage(), $errorCode);
        }
    }

    public function callback(Request $request, $provider) {
        try {
            $workspaceId = $request->get('workspaceId', null);
            $getDriver = $this->socialRepository->getDriver($provider, $workspaceId);

            if(empty($getDriver['status'])) {
                \DB::rollBack();
                return $this->sendError($getDriver['message'], 401);
            }

            $driver = $getDriver['driver'];

            if($provider == 'apple') {
                $socialUser = $driver->userFromToken($request->get('id_token'));
            } else {
                $socialUser = $driver->user();
            }

            $getUser = $this->socialRepository->syncSocialUser($request, $provider, $socialUser);
            $user = $getUser['user'];
            auth()->login($user);

            if (!empty($user->locale)) {
                Session::put('locale', $user->locale);
            }

            $user->last_session = Session::getId();
            $user->save();
            $request->merge(['userId' => $user->id]);

            $host = $request->getHost();
            $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
            session('workspace_'.$workspaceSlug)->refresh();
            \App::call('App\Http\Controllers\Frontend\InitCartController@storeWithoutLogin', [
                'workspaceId' => $workspaceId,
                'userId' => $user->id
            ]);

            if(!empty($getUser['firstLogin'])) {
                if(!empty(session('social_redirect'))) {
                    $redirect = session('social_redirect');

                    if(str_contains($redirect, '?')) {
                        $redirect .= '&profile=1';
                    } else {
                        $redirect .= '?profile=1';
                    }

                    return redirect($redirect);
                }

                return redirect(route('web.index', ['profile' => 1]));
            }

            if(!empty(session('social_redirect'))) {
                return redirect(session('social_redirect'));
            }

            return redirect(route('web.index'));
        } catch (\Exception $e) {
            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;

            return $this->sendError($e->getMessage(), $errorCode);
        }
    }
}
