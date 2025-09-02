<?php

namespace App\Models;

use App\Facades\Helper;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupRestaurant extends AppModel
{
    use SoftDeletes, Translatable;

    protected $table = 'group_restaurant';

    protected $fillable = [
        'active',
        'name',
        'description',
        'token',
        'color',
        'firebase_project',
        'facebook_id',
        'facebook_key',
        'google_id',
        'google_key',
        'apple_id',
        'apple_key'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
    ];

    public $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

    public static $rule = [
        'name' => 'required'
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            /* var App\Models\GroupRestaurant */
            $model->token = strtoupper(Helper::createNewToken());
        });
    }

    public function groupRestaurantWorkspaces()
    {
        return $this->belongsToMany(
            Workspace::class,
            'group_restaurant_workspace',
            'group_restaurant_id',
            'workspace_id'
        );
    }

    public function groupRestaurantAvatar()
    {
        return $this->hasOne(
            Media::class,
            'foreign_id'
        )
        ->where('foreign_model', Media::MODEL_GROUP_RESTAURANT)
        ->where('foreign_type', Media::AVATAR);
    }

    public function getFullInfo($request = [])
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'active' => $this->active,
            'color' => $this->color,
            'description' => $this->description,
            'restaurants' => $this->getRestaurants($request),
            'group_restaurant_avatar' => $this->restaurant_avatar,
        ];
    }

    public function getRestaurants($request)
    {
        $lat = $request->get('lat');
        if (!$request->has('lat') && $request->hasHeader('lat')) {
            $lat = $request->header('lat');
        }

        $lng = $request->get('lng');
        if (!$request->has('lng') && $request->hasHeader('lng')) {
            $lng = $request->header('lng');
        }

        $dataLocation = [
            'lat' => $lat,
            'lng' => $lng
        ];

        $data = [];

        foreach ($this->groupRestaurantWorkspaces as $workspace) {
            $data[] = $workspace->getFullInfo($dataLocation);
        }

        return $data;
    }

    /**
     * Mutator
     *
     * @return mixed
     */
    public function getRestaurantAvatarAttribute()
    {
        $groupAvatar = $this->groupRestaurantAvatar;

        if (empty($groupAvatar)) {
            return null;
        }

        return $groupAvatar->full_path;
    }
}