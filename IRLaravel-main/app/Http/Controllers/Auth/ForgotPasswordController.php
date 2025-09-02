<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailBehavior;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /** @var UserRepository $userRepository */
    private $userRepository;

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

    use SendsPasswordResetEmails;

    /**
     * ForgotPasswordController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->middleware('guest');
        $this->userRepository = $userRepo;
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm(Request $request)
    {
        if (!empty(auth()->user())) {
            return redirect($this->redirectTo);
        }

        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug)->refresh();
        $data = $this->getHomepageData($workspace);

        return view('auth.passwords.email', $data);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        // Create new email behavior
        $workspace = \App\Facades\Helper::getWorkspaceFromAppToken($request);
        $groupRestaurant = \App\Facades\Helper::getGroupRestaurantFromGroupToken($request);
        EmailBehavior::makeBehavior(
            EmailBehavior::ACTION_RESET_PASSWORD,
            $request->get('email'),
            ((!empty($workspace)) ? $workspace->id : null),
            ((!empty($groupRestaurant))? $groupRestaurant->id: null)
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
