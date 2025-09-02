<?php

namespace App\Models;

use App\Facades\Helper;

class RedeemHistory extends AppModel
{
    public $table = 'redeem_histories';

    const DISCOUNT_FIXED_AMOUNT = 1;
    const DISCOUNT_PERCENTAGE = 2;

    public $fillable = [
        'created_at',
        'updated_at',
        'loyalty_id',
        'reward_level_id',
        'reward_level_type',
        'reward_data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'reward_level_type' => 'integer',
        'reward_data' => 'array',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'loyalty_id' => 'required',
        'reward_level_id' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loyalty()
    {
        return $this->belongsTo(\App\Models\Loyalty::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reward()
    {
        return $this->belongsTo(\App\Models\Reward::class, 'reward_level_id');
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        // Customize reward data (backlog)
        $rewardData = $this->reward_data;

        if (!empty($this->reward_level_id) && !empty($this->reward)
            && (!empty($rewardData) && is_array($rewardData) && !array_key_exists('photo', $rewardData))) {
            $rewardData['photo'] = $this->reward->photo;
        }

        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'loyalty_id' => $this->loyalty_id,
            'reward_level_id' => $this->reward_level_id,
            'reward_level_type' => $this->reward_level_type,
            'reward_data' => $rewardData,
        ];
    }

    /**
     * @return array
     */
    public function getTinyInfo()
    {
        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'loyalty_id' => $this->loyalty_id,
            'reward_level_id' => $this->reward_level_id,
        ];
    }

    /**
     * @param $workspaceId
     * @param $rangeStartDate
     * @param $rangeEndDate
     * @return array
     */
    public static function getListByWorkspace($workspaceId, $rangeStartDate = null, $rangeEndDate = null) {
        $model = static::join('loyalties', 'loyalties.id', '=', 'redeem_histories.loyalty_id')
            ->where('loyalties.workspace_id', $workspaceId)
            ->where('redeem_histories.reward_level_type', Reward::FYSIEK_CADEAU);
        
            // Filter by start date & end date
            if (!empty($rangeStartDate) && !empty($rangeEndDate)) {
                $model = $model->whereBetween('redeem_histories.created_at', [$rangeStartDate, $rangeEndDate]);
            }
        
            $model = $model->select('redeem_histories.*')
            ->addSelect(\DB::raw('COUNT(redeem_histories.reward_level_id) AS totalReward'))
           ->groupBy('reward_level_id')
            ->get()->toArray();
        
        return $model;
    }

}
