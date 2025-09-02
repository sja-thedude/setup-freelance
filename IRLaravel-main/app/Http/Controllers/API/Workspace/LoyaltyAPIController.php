<?php

namespace App\Http\Controllers\API\Workspace;

use App\Http\Controllers\API\AppBaseController;
use App\Repositories\LoyaltyRepository;

/**
 * Class LoyaltyController
 * @package App\Http\Controllers\API
 */
class LoyaltyAPIController extends AppBaseController
{
    /**
     * @var LoyaltyRepository $loyaltyRepository
     */
    protected $loyaltyRepository;

    /**
     * LoyaltyAPIController constructor.
     * @param LoyaltyRepository $loyaltyRepo
     */
    public function __construct(LoyaltyRepository $loyaltyRepo)
    {
        parent::__construct();

        $this->loyaltyRepository = $loyaltyRepo;
    }

    /**
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getMyLoyalty(int $workspaceId)
    {
        $user = \Auth::user();

        try {
            $redeem = $this->loyaltyRepository->getRedeemByUser($workspaceId, $user);
            $loyalty = $redeem->loyalty;

            $result = array_merge($redeem->getFullInfo(), [
                'user' => $loyalty->user->getFullInfo(),
            ]);

            return $this->sendResponse($result, trans('loyalty.message_redeem_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

    /**
     * @param int $workspaceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getLoyaltyOfWorkspace(int $workspaceId)
    {
        $user = \Auth::user();

        /** @var \App\Models\Loyalty $loyalty */
        $loyalty = $this->loyaltyRepository
            ->with(['workspace', 'user'])
            ->firstOrCreate([
                'workspace_id' => $workspaceId,
                'user_id' => $user->id
            ]);

        if (empty($loyalty)) {
            return $this->sendError(trans('loyalty.not_found'));
        }

        $result = $this->loyaltyRepository->getFullInfo($loyalty);

        return $this->sendResponse($result, trans('loyalty.message_retrieved_successfully'));
    }

}
