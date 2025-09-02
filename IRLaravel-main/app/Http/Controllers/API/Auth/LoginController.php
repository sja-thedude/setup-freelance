<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppBaseController;
use App\Utils\Jwt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class LoginController
 * @package App\Http\Controllers\API\Auth
 */
class LoginController extends AppBaseController
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
            'active' => true,
            'is_verified' => true,
        ]);

        return $data;
    }

    /**
     * Login with token
     *
     * @param string $token
     * @return mixed
     */
    public function loginWithToken(string $token)
    {
        /** @var \App\Models\User $user */
        $user = JWTAuth::toUser($token);

        if (empty($user)) {
            // Redirect to the login page with a error message
            /*return redirect('/')
                ->withErrors(trans('auth.token_is_expired'));*/
            return $this->sendError(trans('auth.token_is_expired'), 500);
        } else {
            $activeGuard = 'admin';
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

            /*return redirect('/');*/
            // Response user profile
            $result = $user->getFullInfo();
            $result['token'] = JWTAuth::fromUser($user);

            return $this->sendResponse($result, trans('auth.success'));
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        try {
            $jwtToken = JWTAuth::parseToken();
            $oldToken = $jwtToken->getToken();
            $refreshToken = JWTAuth::refresh($oldToken);
            $payload = JWTAuth::getPayload($refreshToken);

            $issuedAt = Carbon::createFromTimestamp($payload->get('iat'));
            $expiredAt = Carbon::createFromTimestamp($payload->get('exp'));

            $result = [
                'token' => $refreshToken,
                'issued_at' => $issuedAt->toDateTimeString(),
                'expired_at' => $expiredAt->toDateTimeString(),
            ];

            return $this->sendResponse($result, trans('auth.success'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }

    /**
     * Generate token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken()
    {
        $jwt = new Jwt();
        $id = config('app.key');
        $token = $jwt->signToken($id);
        $result = [
            'token' => $token->get(),
        ];

        return $this->sendResponse($result, trans('messages.success'));
    }

}
