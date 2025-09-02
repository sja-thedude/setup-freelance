<?php

namespace App\Http\Controllers\API\Auth;

use App\Facades\Helper;
use App\Models\User;
use App\Http\Controllers\API\AppBaseController;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;

class RegisterController extends AppBaseController
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

//        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (!isset($data['checkbox-register'])) {
            $data['checkbox-register'] = null;
        }

        if (isset($data['birthday']) && $data['birthday'] == 'Invalid date') {
            $data['birthday'] = NULL;
        }
        
        $registerRequest = new \App\Http\Requests\RegisterRequest();
        $rules = $registerRequest->rules();
        $messages = $registerRequest->messages();

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validation = $this->validator($request->all());

        if ($validation->fails()) {
            return $this->sendError($validation->errors()->first(), 422, $validation->errors()->toArray());
        }

        // Get all attributes from request
        $attributes = $request->all();

        // Custom attributes
        $maxVerifyExpired = (int)config('auth.registered_confirmations.users.expire');
        $attributes = array_merge($attributes, [
            'is_verified' => false,
            'verify_token' => $this->createNewToken(),
            'verify_expired_at' => (new \Carbon\Carbon())->addMinutes($maxVerifyExpired),
        ]);

        // Create new user
        $user = $this->create($attributes);

        // Current domain
        $domain = url('/');
        // Get base URL from config
        $baseUrl = config('app.url');

        // Get referer domain
        $domainReferer = $request->server('HTTP_REFERER');
        $domainDiff = false;
        $domainRedirect = $domain;

        if (!empty($domainReferer) && $domainReferer != $domain) {
            $domainDiff = true;
            $domainRedirect = rtrim($domainReferer, '/');
        }

        if ($domain == $baseUrl) {
            // Get domain from workspace
            $workspace = Helper::getWorkspaceFromAppToken($request);
            $domain = (!empty($workspace)) ? Helper::getSubDomainOfWorkspace($workspace->id) : null;
        }

        $groupRestaurant = Helper::getGroupRestaurantFromGroupToken($request);

        // Create confirmation link
        $routeParams = ['token' => $user->verify_token];

        if (!empty($groupRestaurant)) {
            $routeParams['Group-Token'] = $groupRestaurant->token;
        }

        $redirectUrl = Helper::getUrlWithDefaultRestaurant(route('register.confirm', $routeParams), $domainRedirect);
        $fromNext = $request->get('origin', 'app');
        $evtRegisteredOptions = [
            'locale' => \App::getLocale(),
            'domain' => $domain,
            'group_restaurant' => $groupRestaurant,
            'from_next' => $fromNext
        ];

        // Only redirect if domain is different
        if ($domainDiff) {
            $evtRegisteredOptions['redirect_url'] = $redirectUrl;
        }

        event(new \App\Events\Registered($user, $evtRegisteredOptions));

//        $this->guard()->login($user);

        return $this->registered($request, $user);
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
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $data = $user->getFullInfo();

        return $this->sendResponse($data, trans('auth.message_register_successfully'));
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));
    }

    /**
     * Show the application registration confirmation form.
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function verifyToken(Request $request, $token)
    {
        $data = ['redirect_url' => route('login')];
        $verifyFromNext = $request->get('verify', null);
        $workspaceId = $request->get('workspace_id', null);
        // Validate token
        $user = \App\Models\User::whereNotNull('verify_token')
            ->where('verify_token', $token)
            ->first();

        if (empty($user)) {
            return $this->sendError(trans('auth.token_is_expired'), 500, $data);
        }

        $workspace = Workspace::find($workspaceId);

        if(!empty($workspace)) {
            $request->merge([
                'App-Token' => Helper::getAppTokenFromWorkspace($workspace),
            ]);
        }

        // Config for deeplink
        $config = Helper::getMobileConfig($request);
        $data['config'] = $config;

        if(!empty($workspace)) {
            $data = array_merge($data, $this->getHomepageData($workspace));
        }

        // Delete when token is expired
        if (Carbon::now()->greaterThan($user->verify_expired_at)) {
            if($verifyFromNext == 1) {
                $maxVerifyExpired = (int)config('auth.registered_confirmations.users.expire');
                $user->verify_token = Helper::createNewToken();
                $user->verify_expired_at = (new \Carbon\Carbon())->addMinutes($maxVerifyExpired);
                $user->save();

                // Current domain
                $domain = url('/');
                // Get base URL from config
                $baseUrl = config('app.url');

                if ($domain == $baseUrl) {
                    // Get domain from workspace
                    $workspaceId = $request->get('workspace_id', null);
                    $domain = (!empty($workspaceId)) ? Helper::getSubDomainOfWorkspace($workspaceId) : null;
                }

                $evtRegisteredOptions = [
                    'locale' => \App::getLocale(),
                    'domain' => $domain,
                ];

                event(new \App\Events\Registered($user, $evtRegisteredOptions));
            } else {
                $user->forceDelete();
            }

            return $this->sendError(trans('auth.token_is_expired'), 500, $data);
        }

        // Verify
        $user->is_verified = true;
        $user->save();
        $token = \JWTAuth::fromUser($user);
        $data = array_merge($data, $user->getFullInfo(), [
            'token' => $token,
            'verify_token' => $user->verify_token,
            'redirect_url' => route('auth.login_with_token', ['token' => urlencode($token)]),
        ]);

        return $this->sendResponse($data, trans('auth.message_verified_successfully'));
    }
}
