<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Repositories\UserRepository;

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
     * Create a new controller instance.
     *
     * @return void
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
    public function showLinkRequestForm()
    {
        return view('admin.auth.passwords.email');
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
        $user = User::where('platform', User::PLATFORM_BACKOFFICE)
            ->where('email', $request->only('email'))
            ->first();

        if(empty($user)) {
            return $this->sendResetLinkFailedResponse($request, 'passwords.user');
        }

        if($user->status != User::STATUS_ACTIVE && !$user->isSuperAdmin()) {
            return $this->sendResetLinkFailedResponse($request, 'passwords.forgot_when_did_reset');
        }

        $this->userRepository->forgotPassword($user, route('admin.showlogin'));
            
        return $this->sendResetLinkResponse(Password::RESET_LINK_SENT);
    }
}
