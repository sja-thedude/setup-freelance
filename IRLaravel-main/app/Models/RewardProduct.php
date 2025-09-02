<?php

namespace App\Models;

class RewardProduct extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'reward_products';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'product_id',
        'reward_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function reward()
    {
        return $this->belongsTo(\App\Models\Reward::class, 'reward_id');
    }
}
