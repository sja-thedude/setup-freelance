<?php

namespace App\Models;

class Reward extends AppModel
{
    const KORTING = 1;
    const FYSIEK_CADEAU = 2;
    const AVATAR = 'avatar';

    /**
     * @var string
     */
    public $table = 'reward_levels';

    /**
     * @var string[]
     */
    public $fillable = [
        'id',
        'workspace_id',
        'title',
        'description',
        'type',
        'score',
        'reward',
        'expire_date',
        'repeat',
        'discount_type',
        'percentage',
        'updated_at',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'reward_categories');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'reward_products');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rewardAvatar()
    {
        return $this->hasOne(
            \App\Models\Media::class,
            'foreign_id'
        )
            ->where('foreign_model', Reward::class)
            ->where('foreign_type', Reward::AVATAR);
    }

    /**
     * Mutator photo
     *
     * @return string
     */
    public function getPhotoAttribute()
    {
        /** @var \App\Models\Media $photo */
        $photo = $this->rewardAvatar;

        if (empty($photo)) {
            return null;
        }

        return $photo->full_path;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::KORTING => trans('reward.lb_korting'),
            static::FYSIEK_CADEAU => trans('reward.lb_fysiek_cadeau'),
        ];
    }

    /**
     * Mutator type_display
     *
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        $types = static::getTypes();

        return (array_key_exists($this->type, $types)) ? $types[$this->type] : $this->type;
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'type_display' => $this->type_display,
            'score' => $this->score,
            'reward' => $this->reward,
            'expire_date' => $this->expire_date,
            'repeat' => $this->repeat,
            'photo' => $this->photo,
            'discount_type' => $this->discount_type,
            'percentage' => $this->percentage
        ];
    }

    /**
     * @param $workspaceId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getRewardMax($workspaceId) {
       $reward = static::where('workspace_id', $workspaceId)->orderBy('score', 'desc')->get();

       return $reward;
    }

    public function categoriesRelation()
    {
        return $this->hasMany(CategoryRelation::class,'foreign_id')
            ->where('foreign_model', static::class);
    }

}
