<?php

namespace App\Repositories;

use App\Models\Loyalty;
use App\Models\RedeemHistory;
use App\Models\Reward;
use App\Models\User;
use Carbon\Carbon;

class LoyaltyRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'workspace_id',
        'user_id',
        'point'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return Loyalty::class;
    }

    /**
     * @param array $workspaceIds
     * @return array
     */
    public function getHighestRewardLevels(array $workspaceIds)
    {
        $data = [];

        foreach ($workspaceIds as $workspaceId) {
            $data[$workspaceId] = \App\Models\Reward::where('workspace_id', $workspaceId)
                ->orderBy('score', 'DESC')
                ->first();
        }

        return $data;
    }

    /**
     * Create a redeem
     *
     * @param Loyalty $loyalty
     * @param int $rewardId
     * @param string|null $timezone
     * @return Loyalty
     * @throws \Exception
     */
    public function createRedeem(Loyalty $loyalty, int $rewardId, $timezone = null)
    {
        // Validate reward
        /** @var \App\Models\Reward $reward */
        $reward = \App\Models\Reward::where('id', $rewardId)
            ->first();

        // Invalid reward
        if (empty($reward)) {
            throw new \Exception(trans('messages.not_found'), 404);
        }
        
        // Validate expire date reward and update it
        if(!empty($loyalty->reward_level_id)){
            $currentRewardLoyalty = Reward::where('id', $loyalty->reward_level_id)->first();
            if(!empty($currentRewardLoyalty) && $currentRewardLoyalty->expire_date < Carbon::now()) {
                $loyalty->reward_level_id = null;
                $loyalty->save();
                return $loyalty;
            }
        }

        // Only check already redeem or not by Reward type is Discount
        if ($reward->type == \App\Models\Reward::KORTING && !empty($loyalty->reward_level_id)) {
            if($loyalty->reward_level_id == $reward->id) {
                throw new \Exception(trans('loyalty.message_already_redeem'), 500);
            }else {
                throw new \Exception(trans('loyalty.message_unable_re_redeemable'), 500);
            }
        }

        /*-------------------- Validate re-redeem --------------------*/

        $redeemHistory = \App\Models\RedeemHistory::where('loyalty_id', $loyalty->id)
            ->where('reward_level_id', $rewardId)
            ->count();

        if (!$reward->repeat && $redeemHistory) {
            throw new \Exception(trans('loyalty.message_unable_re_redeemable'), 500);
        }

        /*--------------------/ Validate re-redeem --------------------*/

        // Validate expire date
        $rewardRepo = new RewardRepository(app());
        $validReward = $rewardRepo->validateRewardExpireDate($reward);

        if (!$validReward) {
            throw new \Exception(trans('reward.message_reward_expired'), 500);
        }

        // Not enough point
        if ($reward->score > $loyalty->point) {
            $missingPoint = $reward->score - $loyalty->point;
            throw new \Exception(trans('loyalty.message_not_enough_point', ['point' => $missingPoint]), 500);
        }

        // Set currently reward (only apply for Discount reward)
        if ($reward->type == \App\Models\Reward::KORTING) {
            $loyalty->reward_level_id = $rewardId;
        }

        // Subtract point
        $loyalty->point -= $reward->score;

        \DB::beginTransaction();

        // Save current reward to loyalty data
        $loyalty->save();

        /*-------------------- Create redeem history --------------------*/

        $history = \App\Models\RedeemHistory::create([
            'loyalty_id' => $loyalty->id,
            'reward_level_id' => $rewardId,
            'reward_level_type' => $reward->type,
            'reward_data' => $reward->toArray(),
        ]);

        /*--------------------/ Create redeem history --------------------*/

        // When the user redeems a physical gift,
        // he will receive a confirmation e-mail when seeing the screen on the right.
        if ($reward->type == Reward::FYSIEK_CADEAU) {
            dispatch(new \App\Jobs\SendEmailRewardPhysicalGift($loyalty->user, $loyalty, $reward, $timezone));
        }

        \DB::commit();

        // Return to view (not save)
        $loyalty->reward_level_id = $rewardId;
        $loyalty->setRelation('reward', $reward);
        $loyalty->setRelation('last_redeem_history', $history);

        return $loyalty;
    }

    /**
     * Get redeem history from loyalty
     *
     * @param Loyalty $loyalty
     * @param int $rewardId
     * @param int|null $rewardType Get type from \App\Models\Reward
     * @return \App\Models\RedeemHistory
     * @throws \Exception
     */
    public function getLastReward(Loyalty $loyalty, int $rewardId, $rewardType = null)
    {
        /** @var \App\Models\RedeemHistory $redeem */
        $redeem = \App\Models\RedeemHistory::select('redeem_histories.*')
            ->where('loyalty_id', $loyalty->id)
            ->where('reward_level_id', $rewardId)
            ->orderBy('created_at', 'DESC');

        if (!empty($rewardType) && array_key_exists($rewardType, \App\Models\Reward::getTypes())) {
            $redeem->where('reward_level_type', $rewardType);
        }

        $redeem = $redeem->first();

        if (empty($redeem)) {
            throw new \Exception(trans('common.not_found'), 404);
        }

        return $redeem;
    }

    /**
     * @param int $workspaceId
     * @param User $user
     * @return \App\Models\RedeemHistory
     * @throws \Exception
     */
    public function getRedeemByUser(int $workspaceId, User $user)
    {
        /** @var \App\Models\RedeemHistory $redeem */
        $redeem = \App\Models\RedeemHistory::select('redeem_histories.*')
            ->join('loyalties', 'loyalties.id', '=', 'redeem_histories.loyalty_id')
            ->join('reward_levels', function ($join) {
                $join->on('reward_levels.id', '=', 'loyalties.reward_level_id')
                    ->whereRaw('redeem_histories.reward_level_id = loyalties.reward_level_id');
            })
            ->where('loyalties.workspace_id', $workspaceId)
            ->where('loyalties.user_id', $user->id)
            ->whereNotNull('loyalties.reward_level_id')
            ->where('reward_levels.type', Reward::KORTING)
            ->orderBy('redeem_histories.created_at', 'DESC')
            ->first();

        if (empty($redeem)) {
            throw new \Exception(trans('common.not_found'), 404);
        }

        return $redeem;
    }

    /**
     * Check usage rewards or not
     *
     * @param Loyalty $loyalty
     * @param array $rewardIds
     * @return array
     */
    public function checkUsage(Loyalty $loyalty, array $rewardIds)
    {
        $redeemHistories = RedeemHistory::where('loyalty_id', $loyalty->id)
            ->whereIn('reward_level_id', $rewardIds)
            ->pluck('reward_level_id')
            ->toArray();

        $result = [];

        foreach ($rewardIds as $rewardId) {
            $result[$rewardId] = in_array($rewardId, $redeemHistories);
        }

        return $result;
    }

    /**
     * Get full information of a loyalty
     *
     * @param \App\Models\Loyalty $loyalty
     * @return array
     */
    public function getFullInfo(Loyalty $loyalty)
    {
        $result = $loyalty->getFullInfo();

        // Highest reward level
        $highestRewardLevels = $this->getHighestRewardLevels([$loyalty->workspace_id]);

        if (array_key_exists($loyalty->workspace_id, $highestRewardLevels)) {
            /** @var \App\Models\Reward $highestRewardLevel */
            $highestRewardLevel = $highestRewardLevels[$loyalty->workspace_id];

            $result = array_merge($result, [
                'highest_point' => (!empty($highestRewardLevel)) ? $highestRewardLevel->score : 0,
            ]);
        }

        // Get reward levels
        $rewardRepo = new RewardRepository(app());
        /** @var \Illuminate\Support\Collection $rewards */
        $rewards = $rewardRepo
            ->where('reward_levels.workspace_id', $loyalty->workspace_id)
            ->where('reward_levels.expire_date', '>', \Carbon\Carbon::now())
            ->orderBy('reward_levels.score', 'ASC')
            ->with(['rewardAvatar'])
            ->select('reward_levels.*')
            ->get();

        // Redeem history
        $arrRedeemHistories = [];

        if ($rewards->count() > 0) {
            $redeemHistories = \App\Models\RedeemHistory::where('loyalty_id', $loyalty->id)
                ->whereIn('reward_level_id', $rewards->pluck('id')->toArray())
                ->groupBy('reward_level_id')
                ->get();

            if ($redeemHistories->count() > 0) {
                /** @var \App\Models\RedeemHistory $redeem */
                foreach ($redeemHistories as $redeem) {
                    $arrRedeemHistories[$redeem->reward_level_id] = $redeem;
                }
            }
        }

        // Get full info of rewards
        $arrRewards = $rewards->transform(function ($reward) use ($arrRedeemHistories, $loyalty) {
            /** @var \App\Models\Reward $reward */

            // Check is redeem or not
            /** @var \App\Models\RedeemHistory $redeem */
            $redeem = array_key_exists($reward->id, $arrRedeemHistories) ? $arrRedeemHistories[$reward->id] : null;

            if($reward->type == Reward::FYSIEK_CADEAU) {
                if($reward->repeat || !array_key_exists($reward->id, $arrRedeemHistories)) {
                    $is_redeem = false;
                } else {
                    $is_redeem = true;
                }
            } elseif($reward->type == Reward::KORTING) {
                $is_redeem = $reward->id == $loyalty->reward_level_id;
            }

            return array_merge($reward->getFullInfo(), [
                'is_redeem' => $is_redeem,
                'is_used' => !empty($redeem),
                'last_redeem_history' => !empty($redeem) ? $redeem->getTinyInfo() : null,
            ]);
        });

        //-------------------- Merge is_redeem property to selected reward

        if (!empty($result['reward']) && !empty($loyalty->reward)) {
            // Selected reward
            $reward = $loyalty->reward;
            // Check is redeem or not
            $redeem = array_key_exists($reward->id, $arrRedeemHistories) ? $arrRedeemHistories[$reward->id] : null;

            if($reward->type == Reward::FYSIEK_CADEAU) {
                if($reward->repeat || !array_key_exists($reward->id, $arrRedeemHistories)) {
                    $is_redeem = false;
                } else {
                    $is_redeem = true;
                }
            } elseif($reward->type == Reward::KORTING) {
                $is_redeem = $reward->id == $loyalty->reward_level_id;
            }

            $result['reward'] = array_merge($result['reward'], [
                'is_redeem' => $is_redeem,
                'is_used' => !empty($redeem),
                'last_redeem_history' => !empty($redeem) ? $redeem->getTinyInfo() : null,
            ]);
        }

        //-------------------- /Merge is_redeem property to selected reward

        // Merge reward list from the restaurant
        $result = array_merge($result, [
            'rewards' => $arrRewards,
        ]);

        return $result;
    }

}
