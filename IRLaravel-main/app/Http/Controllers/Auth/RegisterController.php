<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Helper;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use InfyOm\Generator\Utils\ResponseUtil;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @return string
     */
    protected function redirectTo()
    {
        return route('register.successful');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        if (!empty(auth()->user())) {
            return redirect($this->redirectTo);
        }

        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug)->refresh();
        $data = $this->getHomepageData($workspace);

        return view('auth.register', $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $registerRequest = new \App\Http\Requests\RegisterRequest();
        $rules = $registerRequest->rules();
        $messages = $registerRequest->messages();

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // Get all attributes from request
        $attributes = $request->all();

        // Custom attributes
        $maxVerifyExpired = (int)config('auth.registered_confirmations.users.expire');
        $attributes = array_merge($attributes, [
            'is_verified' => false,
            'verify_token' => Helper::createNewToken(),
            'verify_expired_at' => (new \Carbon\Carbon())->addMinutes($maxVerifyExpired),
        ]);

        // Create new user
        $user = $this->create($attributes);

        $evtRegisteredOptions = [
            'locale' => \App::getLocale(),
            'domain' => url('/'),
        ];
        event(new \App\Events\Registered($user, $evtRegisteredOptions));

//        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'role_id' => \App\Models\Role::ROLE_USER,
            'platform' => User::PLATFORM_FRONTEND,
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'birthday' => array_get($data, 'birthday'),
            'gender' => array_get($data, 'gender'),
            'address' => array_get($data, 'address'),
            'lng' => array_get($data, 'lng'),
            'lat' => array_get($data, 'lat'),
            'phone' => array_get($data, 'phone'),
            'gsm' => array_get($data, 'gsm'),
            'is_verified' => array_get($data, 'is_verified'),
            'verify_token' => array_get($data, 'verify_token'),
            'verify_expired_at' => array_get($data, 'verify_expired_at'),
            'locale' => \App::getLocale(),
        ]);
    }

    /**
     * Show the application registration confirmation form.
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function showConfirmationForm(Request $request, $token)
    {
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = session('workspace_'.$workspaceSlug);
        $dataHomepage = [];
        if ($workspace) {
            $workspace = session('workspace_'.$workspaceSlug)->refresh();
            $dataHomepage = $this->getHomepageData($workspace);
            $request->merge([
                'App-Token' => Helper::getAppTokenFromWorkspace($workspace),
            ]);
        }

        // Validate token
        $user = \App\Models\User::whereNotNull('verify_token')
            ->where('verify_token', $token)
            // Allow to verify multiple times
//            ->where('is_verified', false)
            ->first();

        // Config for deeplink
        $config = Helper::getMobileConfig($request);

        if (empty($user) || (!empty($user) && Carbon::now()->greaterThan($user->verify_expired_at))) {
            if (!empty($user) && Carbon::now()->greaterThan($user->verify_expired_at)) {
                $maxVerifyExpired = (int)config('auth.registered_confirmations.users.expire');
                $user->verify_token = Helper::createNewToken();
                $user->verify_expired_at = (new \Carbon\Carbon())->addMinutes($maxVerifyExpired);
                $user->save();

                $evtRegisteredOptions = [
                    'locale' => \App::getLocale(),
                    'domain' => url('/'),
                ];
                event(new \App\Events\Registered($user, $evtRegisteredOptions));

                // Delete when token is expired
                // $user->forceDelete();
            }

            $data = array_merge([
                'config' => $config,
                'redirect_url' => route('login'),
            ], $dataHomepage);

            if ($workspace) {
                return view('auth.registered_confirmation_failed')
                    ->with($data)
                    ->withErrors([
                        'verify_token' => trans('auth.token_is_expired')
                    ]);
            }

            return view('auth.passwords.oops');
        }

        // Verify
        $user->is_verified = true;
        // Allow to verify multiple times
//        $user->verify_token = null;
//        $user->verify_expired_at = null;
        $user->save();

        $data = $user->getFullInfo();
        // Redirect to Login with token
        $token = JWTAuth::fromUser($user);
        $data = array_merge($data, [
            'config' => $config,
            'token' => $token,
            'verify_token' => $user->verify_token,
            'redirect_url' => route('auth.login_with_token', ['token' => urlencode($token)]),
        ]);

        // Merge data from user and homepage
        $data = array_merge($data, $dataHomepage);

        // Redirect to specific url
        if ($request->has('redirect_url')) {
            return redirect($request->get('redirect_url'));
        }

        if ($workspace) {
            return view('auth.registered_confirmation', $data);
        }

        $data['from_register'] = true;
        return view('web.home.index_new', $data);

    }

    /**
     * Show the registered successful view.
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function showRegisteredSuccessful(Request $request)
    {
        if (!empty(auth()->user())) {
            return redirect($this->redirectTo);
        }

        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug)->refresh();
        $data = $this->getHomepageData($workspace);

        return view('auth.registered_successful', $data);
    }

    public function autoLogin(Request $request)
    {
        $postData = $request->all();

        if (isset($postData['verifyToken'])) {
            $user = User::where('verify_token', $postData['verifyToken'])->first();
            if ($user) {
                \Auth::loginUsingId($user->id, true);
                return $this->sendResponse(['verify_token' => $postData['verifyToken']], 'Successfully');
            }
        }

        return $this->sendError('Error', 500);
    }

    public function sendResponse($result, $message) {
        return \Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404, $data = []) {
        return \Response::json(ResponseUtil::makeError($error, $data), $code);
    }
}
