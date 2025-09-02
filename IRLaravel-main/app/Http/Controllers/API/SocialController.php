<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\LoginSocialAPIRequest;
use JWTAuth, JWTAuthException;
use App\Repositories\SocialRepository;

class SocialController extends AppBaseController
{
    private $socialRepository;

    public function __construct(SocialRepository $socialRepo) {
        parent::__construct();

        $this->socialRepository = $socialRepo;
    }

    public function login(LoginSocialAPIRequest $request)
    {
        try {
            \DB::beginTransaction();
            $accessToken = $request->get('access_token');
            $provider = $request->get('provider');
            $workspaceId = $request->get('workspace_id', null);
            $groupId = $request->get('group_id', null);
            $getDriver = $this->socialRepository->getDriver($provider, $workspaceId, $groupId);

            if(empty($getDriver['status'])) {
                \DB::rollBack();
                return $this->sendError($getDriver['message'], 401);
            }

            $driver = $getDriver['driver'];
            $socialUser = $driver->userFromToken($accessToken);
            $getUser = $this->socialRepository->syncSocialUser($request, $provider, $socialUser);
            $user = $getUser['user'];

            if (!$token = JWTAuth::fromUser($user)) {
                \DB::rollBack();
                return $this->sendError(trans('social.token_invalid'), 401);
            }

            $result = $user->getFullInfo();
            $result['token'] = $token;
            $result['first_login'] = $getUser['firstLogin'];
            \DB::commit();

            return $this->sendResponse($result, trans('social.login_success'));
        } catch (\Exception $e) {
            \DB::rollBack();
            $errorCode = (!empty($e->getCode())) ? $e->getCode() : 500;

            return $this->sendError($e->getMessage(), $errorCode);
        }
    }
}
