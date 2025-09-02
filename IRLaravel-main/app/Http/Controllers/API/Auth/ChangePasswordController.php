<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppBaseController;
use App\Http\Requests\API\ChangePasswordAPIRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth, Hash;

class ChangePasswordController extends AppBaseController
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
     * Reset the given user's password.
     *
     * @param ChangePasswordAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordAPIRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $password = $request->input('new_password');
        $currentPassword = $request->input('current_password');

        // Validate current password
        if (!Hash::check($currentPassword, $user->password)) {
            return $this->sendError(trans('messages.user.invalid_current_password'));
        }

        // Change password
        $this->resetPassword($user, $password);

        $result = $user->getSummaryInfo();
        return $this->sendResponse($result, trans('messages.user.changed_password_successfully'));
    }

}
