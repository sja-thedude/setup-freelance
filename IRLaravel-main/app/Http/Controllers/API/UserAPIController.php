<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CheckLoginAPIRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Exception;
use JWTAuth;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserAPIController extends AppBaseController
{
    /** @var UserRepository $userRepository */
    protected $userRepository;

    /**
     * UserAPIController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        parent::__construct();

        $this->userRepository = $userRepo;
    }

    /**
     * Login of the User.
     * POST|HEAD /login
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // When the user clicks on “Login” while having entered an e-mail address not recognised by the system,
        // the e-mail field will be shown like this.

        /** @var \App\Models\User $user */
        $user = $this->userRepository->findWhere([
            'email' => array_get($credentials, 'email'),
            /*'active' => true,*/
            'deleted_at' => null
        ])->first();

        if (empty($user) || !$user->isRecognised()) {
            return $this->sendError(trans('auth.message_email_address_not_recognised'), 500);
        }

        // When the user clicks on “Login” while having entered an e-mail and password that not match,
        // the fields will be shown like this.

        $credentials = array_merge($credentials, [
            /*'active' => true,*/
            'deleted_at' => null
        ]);

        try {
            // Login fail
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->sendError(trans('auth.failed'), 401);
            }

            // Login success
            // Store api_token
            $user->api_token = $token;
            $user->save();
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }

        // Response user profile
        $result = $user->getFullInfo();
        $result['token'] = $token;

        return $this->sendResponse($result, trans('auth.success'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        return $this->sendResponse(null, trans('auth.logout_success'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->userRepository->pushCriteria(new RequestCriteria($request));
            $this->userRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $users = $this->userRepository->paginate($limit);

        $users->transform(function ($item) {
            return $item->getFullInfo();
        });
        $result = $users->toArray();

        return $this->sendResponse($result, trans('user.message_retrieved_list_successfully'));
    }

    /**
     * @param CheckLoginAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLogin(CheckLoginAPIRequest $request)
    {
        $email = $request->get('email');
        $user = \App\Models\User::where('email', $email)->first();

        $isLoggedIn = (!empty($user) && !empty($user->last_session));

        // Logged in
        if ($isLoggedIn) {
            return $this->sendError(trans('auth.confirm_single_session_login'), 422);
        }

        return $this->sendResponse(null, trans('auth.confirm_single_session_login'));
    }

}
