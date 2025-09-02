<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLoyaltyAPIRequest;
use App\Http\Requests\API\UpdateLoyaltyAPIRequest;
use App\Models\Loyalty;
use App\Repositories\LoyaltyRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Auth::user();

        try {
            $this->loyaltyRepository->pushCriteria(new RequestCriteria($request));
            $this->loyaltyRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $loyalties = $this->loyaltyRepository
            ->join('workspaces', 'workspaces.id', '=', 'loyalties.workspace_id')
            ->join('workspace_extras', function ($join) {
                $join->on('workspace_extras.workspace_id', '=', 'workspaces.id')
                    ->where(function ($query) {
                        $query->where('workspace_extras.type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                            ->where('workspace_extras.active', true);
                    });
            })
            ->with(['workspace', 'workspace.workspaceAvatar', 'workspace.workspaceGalleries', 'user'])
            ->where('loyalties.user_id', $user->id)
            ->select('loyalties.*')
            ->paginate($limit);
        $highestRewardLevels = $this->loyaltyRepository
            ->getHighestRewardLevels($loyalties->pluck('workspace_id')->toArray());

        $loyalties->transform(function ($loyalty) use ($highestRewardLevels) {
            /** @var \App\Models\Loyalty $loyalty */

            $arrLoyalty = $loyalty->getFullInfo();

            if (array_key_exists($loyalty->workspace_id, $highestRewardLevels)) {
                /** @var \App\Models\Reward $highestRewardLevel */
                $highestRewardLevel = $highestRewardLevels[$loyalty->workspace_id];

                $arrLoyalty = array_merge($arrLoyalty, [
                    'highest_point' => (!empty($highestRewardLevel)) ? $highestRewardLevel->score : 0,
                ]);
            }

            return $arrLoyalty;
        });

        $result = $loyalties->toArray();

        return $this->sendResponse($result, trans('loyalty.message_retrieved_multiple_successfully'));
    }

    /**
     * @param CreateLoyaltyAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateLoyaltyAPIRequest $request)
    {
        $input = $request->all();

        $loyalty = $this->loyaltyRepository->create($input);

        return $this->sendResponse($loyalty->toArray(), trans('loyalty.message_saved_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var \App\Models\Loyalty $loyalty */
        $loyalty = $this->loyaltyRepository
            ->with(['workspace', 'user'])
            ->findWithoutFail($id);

        if (empty($loyalty)) {
            return $this->sendError(trans('loyalty.not_found'));
        }

        $result = $this->loyaltyRepository->getFullInfo($loyalty);

        return $this->sendResponse($result, trans('loyalty.message_retrieved_successfully'));
    }

    /**
     * @param UpdateLoyaltyAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateLoyaltyAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Loyalty $loyalty */
        $loyalty = $this->loyaltyRepository->findWithoutFail($id);

        if (empty($loyalty)) {
            return $this->sendError(trans('loyalty.not_found'));
        }

        $loyalty = $this->loyaltyRepository->update($input, $id);

        return $this->sendResponse($loyalty->toArray(), trans('loyalty.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Loyalty $loyalty */
        $loyalty = $this->loyaltyRepository->findWithoutFail($id);

        if (empty($loyalty)) {
            return $this->sendError(trans('loyalty.not_found'));
        }

        $loyalty->delete();

        return $this->sendResponse($id, trans('loyalty.message_deleted_successfully'));
    }

    /**
     * Create a redeem
     *
     * @param Request $request
     * @param Loyalty $loyalty
     * @param int $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRedeem(Request $request, Loyalty $loyalty, int $rewardId)
    {
        // Get timezone from request header
        $timezone = $request->header('Timezone', config('app.timezone'));

        try {
            $loyalty = $this->loyaltyRepository->createRedeem($loyalty, $rewardId, $timezone);

            $result = $loyalty->getFullInfo();

            // Add last redeem history
            if (!empty($result['reward']) && $loyalty->last_redeem_history) {
                $redeem = $loyalty->last_redeem_history;

                $result['reward'] = array_merge($result['reward'], [
                    'is_redeem' => !empty($redeem),
                    'last_redeem_history' => !empty($redeem) ? $redeem->getTinyInfo() : null,
                ]);
            }

            return $this->sendResponse($result, trans('loyalty.message_redeem_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            $data = [];

            // When users redeem a reward level, there would be 2 cases with error messages as below:
            // 1. Reward expired
            // 2. Discount redeemed but not used
            if ($ex->getMessage() == trans('reward.message_reward_expired')) {
                $data['code'] = ERROR_REWARD_REWARD_EXPIRED;
            } else if ($ex->getMessage() == trans('loyalty.message_already_redeem')) {
                $data['code'] = ERROR_LOYALTY_ALREADY_REDEEM;
            }

            return $this->sendError($ex->getMessage(), $errorCode, $data);
        }
    }

    /**
     * Get a redeem history by reward
     *
     * @param Loyalty $loyalty
     * @param int $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastReward(Loyalty $loyalty, int $rewardId)
    {
        try {
            $redeem = $this->loyaltyRepository->getLastReward($loyalty, $rewardId);

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
     * Get a redeem history by reward with type is physical
     *
     * @param Loyalty $loyalty
     * @param int $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastRewardPhysical(Loyalty $loyalty, int $rewardId)
    {
        try {
            $redeem = $this->loyaltyRepository->getLastReward($loyalty, $rewardId, \App\Models\Reward::FYSIEK_CADEAU);

            $result = array_merge($redeem->getFullInfo(), [
                'user' => $loyalty->user->getFullInfo(),
            ]);

            return $this->sendResponse($result, trans('loyalty.message_redeem_retrieved_successfully'));
        } catch (\Exception $ex) {
            $errorCode = (!empty($ex->getCode())) ? $ex->getCode() : 500;
            return $this->sendError($ex->getMessage(), $errorCode);
        }
    }

}
