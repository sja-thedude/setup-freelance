<?php

namespace App\Http\Controllers\Manager\Auth;

use App\Http\Controllers\Controller;
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
    public function showLinkRequestForm()
    {
        return view('manager.auth.passwords.email');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $user = User::where('platform', User::PLATFORM_MANAGER)
            ->where('email', $request->only('email'))
            ->where('status', User::STATUS_ACTIVE)
            ->first();
        
        if(empty($user)) {
            return $this->sendResetLinkFailedResponse($request, 'passwords.user');
        }

        $this->userRepository->forgotPassword($user, route('manager.showlogin'));
            
        return $this->sendResetLinkResponse(Password::RESET_LINK_SENT);
    }
}
