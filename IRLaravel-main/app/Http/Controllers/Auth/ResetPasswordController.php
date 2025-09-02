<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

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
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.confirmed' => trans('passwords.validation.password.confirmed'),
        ];
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug);
        $dataHomepage = [];
        if ($workspace) {
            $workspace = $workspace->refresh();
            $dataHomepage = $this->getHomepageData($workspace);
            // Config for deeplink
            $request->merge([
                'App-Token' => Helper::getAppTokenFromWorkspace($workspace),
            ]);
        }

        $config = Helper::getMobileConfig($request);
        $user = User::where(['email' => $request->email])->first();

        if (!$user || !Password::getRepository()->exists($user, $token)) {
            $data = array_merge([
                'config' => $config,
                'redirect_url' => route('login'),
            ], $dataHomepage);

            if ($workspace) {
                return view('auth.passwords.reset_failed')
                    ->with($data)
                    ->withErrors([
                        'verify_token' => trans('passwords.token')
                    ]);
            }

            return view('auth.passwords.oops');
        }

        $data = array_merge([
            'config' => $config,
            'token' => $token,
            'email' => $request->get('email'),
        ], $dataHomepage);

        // Redirect to specific url
        if ($request->has('redirect_url')) {
            return redirect($request->get('redirect_url'));
        }

        if ($workspace) {
            return view('auth.passwords.reset', $data);
        }

        $data['from_reset'] = true;
        return view('web.home.index_new', $data);
    }
}
