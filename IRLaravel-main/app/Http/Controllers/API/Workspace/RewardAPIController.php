<?php

namespace App\Http\Controllers\API\Workspace;

use App\Http\Controllers\API\AppBaseController;
use App\Http\Requests\API\ValidateRewardProductsAPIRequest;
use App\Models\Reward;
use App\Repositories\RewardRepository;

/**
 * Class RewardController
 * @package App\Http\Controllers\API
 */
class RewardAPIController extends AppBaseController
{
    /**
     * @var RewardRepository $rewardRepository
     */
    protected $rewardRepository;

    /**
     * RewardAPIController constructor.
     * @param RewardRepository $rewardRepo
     */
    public function __construct(RewardRepository $rewardRepo)
    {
        parent::__construct();

        $this->rewardRepository = $rewardRepo;
    }

    /**
     * @param ValidateRewardProductsAPIRequest $request
     * @param int $workspaceId
     * @param int $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateRewardProducts(ValidateRewardProductsAPIRequest $request, int $workspaceId, int $rewardId)
    {
        $productIds = $request->get('product_id');

        try {
            $result = $this->rewardRepository->validateRewardProducts($rewardId, $productIds);

            return $this->sendResponse($result, trans('reward.message_reward_validate_successful'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

}
