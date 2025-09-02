<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Backend\BaseController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth, Hash;
use Illuminate\Support\Str;
use App\Http\Requests\AdminChangePasswordRequest;

class ChangePasswordController extends BaseController
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

    protected $linkRequestView;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware($this->guard);
        $this->linkRequestView = $this->guard.'.auth.passwords.change';
    }

    /**
     * Reset the given user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePasswordForm()
    {
        return view($this->linkRequestView);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(AdminChangePasswordRequest $request)
    {
        $user = Auth::guard($this->guard)->user();
        $password = $request->input('new_password');
        // Change password
        $this->resetPassword($user, $password);

        return $this->sendResponse(compact('user'), trans('messages.user.changed_password_successfully'));
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
        $user->password_tmp = null;
        $user->verify_expired_at = null;
        $user->setRememberToken(Str::random(60));
        $user->save();
    }
}
