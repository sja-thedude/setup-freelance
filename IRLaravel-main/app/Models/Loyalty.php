<?php

namespace App\Models;

use App\Facades\Helper;

class Loyalty extends AppModel
{
    public $table = 'loyalties';

    public $fillable = [
        'created_at',
        'updated_at',
        'workspace_id',
        'user_id',
        'point',
        'reward_level_id',
        'last_reward_level_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'point' => 'integer',
        'reward_level_id' => 'integer',
        'last_reward_level_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required',
        'user_id' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
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
        // Workspace info
        $workspace = null;

        if (!empty($this->workspace_id) && !empty($this->workspace)) {
            $workspace = array_merge($this->workspace->getSummaryInfo(), [
                'photo' => $this->workspace->photo,
                'gallery' => $this->workspace->full_gallery,
            ]);
        }

        // Full info
        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'workspace_id' => $this->workspace_id,
            'workspace' => $workspace,
            'user_id' => $this->user_id,
            'user' => (!empty($this->user_id) && !empty($this->user)) ? $this->user->getSummaryInfo() : null,
            'point' => $this->point,
            'reward_level_id' => (!empty($this->reward_level_id)) ? $this->reward_level_id : 0,
            'reward' => (!empty($this->reward_level_id) && !empty($this->reward)) ? $this->reward->getFullInfo() : null,
        ];
    }

    public function redemHistory()
    {
        return \App\Models\RedeemHistory::where('loyalty_id', $this->id)->count();
    }
}
