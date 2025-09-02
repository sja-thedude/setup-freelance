<?php

namespace App\Http\Controllers\API\Auth;

use App\Facades\Helper;
use App\Http\Controllers\API\AppBaseController;
use App\Http\Requests\API\ForgotPasswordAPIRequest;
use App\Models\EmailBehavior;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends AppBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    // Rewrite all function instead
    /*use SendsPasswordResetEmails;*/

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(ForgotPasswordAPIRequest $request)
    {
        // Create new email behavior
        $workspace = Helper::getWorkspaceFromAppToken($request);
        $groupRestaurant = Helper::getGroupRestaurantFromGroupToken($request);
        EmailBehavior::makeBehavior(
            EmailBehavior::ACTION_RESET_PASSWORD,
            $request->get('email'),
            ((!empty($workspace)) ? $workspace->id : null),
            ((!empty($groupRestaurant))? $groupRestaurant->id: null),
            !empty($request->get('is_next', 0)) ? EmailBehavior::ORIGIN_NEXT : EmailBehavior::ORIGIN_LARAVEL
        );

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse($response)
    {
        /*return back()->with('status', trans($response));*/
        return $this->sendResponse(null, trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        /*return back()->withErrors(
            ['email' => trans($response)]
        );*/
        return $this->sendError(trans($response));
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
