<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppBaseController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

class ResetPasswordController extends AppBaseController
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

    protected function rules()
    {
        return [
            'password' => 'required|confirmed|min:6',
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
     * Get the response for a successful password reset.
     *
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse($response)
    {
        $data = ['status' => trans($response)];

        return $this->sendResponse($data, trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        $data = ['email' => trans($response)];

        return $this->sendError(trans($response), 422, $data);
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
        $email = $request->get('email');
        $record = (array)\DB::table('password_resets')->where('email', $email)->first();

        if ($record &&
            !Carbon::parse($record['created_at'])->addSeconds(config('auth.passwords.users.expire') * 60)->isPast() &&
            (new \Illuminate\Hashing\BcryptHasher())->check($token, $record['token'])) {
            $data = [
                'token' => $token,
                'email' => $email,
            ];

            return $this->sendResponse($data, trans('auth.message_verified_successfully'));
        }

        return $this->sendError(trans('auth.token_is_expired'), 500);
    }

    /**
     * Change password api
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        try {
            return $this->reset($request);
        } catch (\Exception $exception) {
            $message = $exception->validator->errors()->first();
            return $this->sendError($message, 500);
        }
    }
}
