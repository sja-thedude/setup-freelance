<?php

namespace App\Models;

use App\Facades\Helper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Workspace extends AppModel
{
    use SoftDeletes;

    const INACTIVE = 0;
    const ACTIVE = 1;

    const IS_OFFLINE = 0;
    const IS_ONLINE = 1;

    const STATUS_INVITATION_SENT = 0;
    const FIRST_LOGIN = 1;

    const MINIMUM_DISTANCE = 40;
    const DISTANCE = 'distance';
    const MINIMUM_AMOUNT = 'minimum_amount';
    const DELIVERY_COST = 'delivery_cost';
    const MINIMUM_WAITING_TIME = 'minimum_waiting_time';
    const NAME = 'name';
    /**
     * @var string
     */
    public $table = 'workspaces';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'first_login'];

    /**
     * @var array
     */
    public $fillable = [
        'slug',
        'token',
        'user_id',
        'name',
        'surname',
        'active',
        'providers',
        'created_at',
        'updated_at',
        'deleted_at',
        'account_manager_id',
        'gsm',
        'manager_name',
        'address',
        'btw_nr',
        'email',
        'language',
        'country_id',
        'first_login',
        'status',
        'address_lat',
        'address_long',
        'is_online',
        'is_test_mode',
        'failed_at_kassabon',
        'failed_at_werkbon',
        'failed_at_sticker',
        'email_to',
        'template_app_ios',
        'template_app_android',
        'address_line_1',
        'address_line_2',
        'firebase_project',
        'facebook_enabled',
        'facebook_id',
        'facebook_key',
        'google_enabled',
        'google_id',
        'google_key',
        'apple_enabled',
        'apple_id',
        'apple_key',
        'active_languages',
        'order_access_key'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'slug' => 'string',
        'token' => 'string',
        'user_id' => 'integer',
        'name' => 'string',
        'surname' => 'string',
        'active' => 'boolean',
        'providers' => 'array',
        'account_manager_id' => 'integer',
        'gsm' => 'string',
        'manager_name' => 'string',
        'address' => 'string',
        'btw_nr' => 'string',
        'email' => 'string',
        'language' => 'string',
        'country_id' => 'integer',
        'status' => 'integer',
        'address_lat' => 'string',
        'address_long' => 'string',
        'is_online' => 'boolean',
        'is_test_mode' => 'boolean',
        'failed_at_kassabon' => 'datetime',
        'failed_at_werkbon' => 'datetime',
        'failed_at_sticker' => 'datetime',
        'email_to' => 'string',
        'template_app_ios' => 'string',
        'template_app_android' => 'string',
        'address_line_1' => 'string',
        'address_line_2' => 'string',
        'firebase_project' => 'string',
        'facebook_enabled' => 'int',
        'facebook_id' => 'string',
        'facebook_key' => 'string',
        'google_enabled' => 'int',
        'google_id' => 'string',
        'google_key' => 'string',
        'apple_enabled' => 'int',
        'apple_id' => 'string',
        'apple_key' => 'string',
        'order_access_key' => 'string',
    ];

    /**
     * @var string[] $summary_fields
     */
    public $summary_fields = [
        'id',
        'name',
    ];

    /**
     * @overwrite
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            /** @var \App\Models\Workspace $model */

            // Set created by user
            $model->user_id = (!empty(\Auth::guard('admin')) && !empty(\Auth::guard('admin')->user()))
                ? \Auth::guard('admin')->user()->id : 1;

            // Generate a unique key for Workspace
            $model->token = strtoupper(Helper::createNewToken());
            $model->order_access_key = strtoupper(\Webpatser\Uuid\Uuid::generate()->uuid_ordered);
        });
    }

    /**
     * Get default workspace
     *
     * @return \App\Models\Workspace
     */
    public static function getDefault()
    {
        $workspace = static::where('active', static::IS_YES)->first();

        return $workspace;
    }

    /**
     * Scope a query to join with workspace_objects
     *
     * @param \Illuminate\Database\Eloquent\Builder $model
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUser(\Illuminate\Database\Eloquent\Builder $model, int $userId)
    {
        $thisInstance = $this;
        $model->rightJoin('workspace_objects', function ($join) use ($thisInstance, $userId) {
            $join->on('workspace_objects.workspace_id', '=', $thisInstance->getTable() . '.' . $thisInstance->getKeyName());
            $join->where('workspace_objects.active', \App\Models\WorkspaceObject::IS_YES);
            $join->where('workspace_objects.model', \App\Models\User::class);
            $join->where('workspace_objects.foreign_key', $userId);
        });

        // Prevent duplicate
        $model->groupBy($thisInstance->getTable() . '.' . $thisInstance->getKeyName());

        return $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class, 'workspace_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class, 'workspace_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function userManager()
    {
        return $this->belongsTo(\App\Models\User::class, 'id', 'workspace_id');
    }

    /**
     * Get the workspace owner
     *
     * @return \App\Models\User|null
     */
    public function getOwner()
    {
        return $this->user;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workspaceExtras()
    {
        return $this->hasMany(
            \App\Models\WorkspaceExtra::class,
            'workspace_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingPayments()
    {
        return $this->hasMany(
            \App\Models\SettingPayment::class,
            'workspace_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function workspaceAccount()
    {
        return $this->hasOne(
            \App\Models\User::class,
            'id',
            'account_manager_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function settingGeneral()
    {
        return $this->hasOne(
            \App\Models\SettingGeneral::class,
            'workspace_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function workspaceAvatar()
    {
        return $this->hasOne(
            \App\Models\Media::class,
            'foreign_id'
        )
        ->where('foreign_model', Media::MODEL_WORKSPACE)
        ->where('foreign_type', Media::AVATAR);
    }

    public static function getPrinterGroupWorkspaceIds(int $workspaceId): array
    {
        $printerGroup = PrinterGroup::whereHas('printerGroupWorkspaces', function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        })->first();
        if (empty($printerGroup)) {
            return [$workspaceId];
        }

        return $printerGroup->printerGroupWorkspaces->pluck('id')->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workspaceGalleries()
    {
        return $this->hasMany(
            \App\Models\Media::class,
            'foreign_id'
        )
        ->where('foreign_model', Media::MODEL_WORKSPACE)
        ->where('foreign_type', Media::GALLERIES)
        ->orderBy('order');
    }

    public function workspaceAPIGalleries()
    {
        return $this->hasMany(
          \App\Models\Media::class,
          'foreign_id'
        )
        ->where('foreign_model', Media::MODEL_WORKSPACE)
        ->where('foreign_type', Media::API_GALLERIES)
        ->orderBy('order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingOpenHours()
    {
        return $this->hasMany(\App\Models\SettingOpenHour::class)->with('openTimeSlots');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingTimeslots()
    {
        return $this->hasMany(\App\Models\SettingTimeslot::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingTimeslotDetails()
    {
        return $this->hasMany(\App\Models\SettingTimeslotDetail::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function settingPreference()
    {
        return $this->hasOne(
            \App\Models\SettingPreference::class,
            'workspace_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function workspaceApp()
    {
        return $this->hasOne(\App\Models\WorkspaceApp::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingDeliveryConditions()
    {
        return $this->hasMany(\App\Models\SettingDeliveryConditions::class);
    }

    /**
     * @return array[]
     */
    public function getSummaryInfo()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'title' => !empty($this->settingGeneral) ? $this->settingGeneral->title : $this->name,
            'slug' => $this->slug,
        ];
    }

    /**
     * @return array[]
     */
    public function getFullInfo($dataLocation = [])
    {
        //Get only active gallery
        $galleries = $this->gallery;
        $activeGalleries = [];
        foreach ($galleries as $key => $gallery) {
            if ($gallery['active'] == 1) {
                $activeGalleries[] = $gallery;
            }
        }

        if (!empty($dataLocation)) {
            $this->distance = Helper::calculateDistance($this, $dataLocation);
        }

        return array_merge($this->getSummaryInfo(), [
            'gsm'                => $this->gsm,
            'address'            => $this->address,
            'email'              => $this->email,
            'country_id'         => $this->country_id,
            'country'            => (!empty($this->country)) ? $this->country->getSummaryInfo() : NULL,
            'lat'                => $this->address_lat,
            'lng'                => $this->address_long,
            'is_online'          => $this->is_online,
            'is_test_mode'       => $this->is_test_mode,
            'distance'           => $this->getDistanceFormat($this->distance),
            'photo'              => $this->photo,
            'gallery'            => $activeGalleries,
            'api_gallery'        => $this->api_gallery,
            'categories'         => $this->getCategories(),
            'extras'             => $this->getExtras(),
            'setting_open_hours' => $this->settingOpenHours,
            'setting_preference' => $this->settingPreference,
            'setting_delivery_conditions' => $this->settingDeliveryConditions,
            'setting_generals' => $this->settingGeneral,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'facebook_enabled' => $this->facebook_enabled,
            'facebook_id' => $this->facebook_id,
            'facebook_key' => $this->facebook_key,
            'google_enabled' => $this->google_enabled,
            'google_id' => $this->google_id,
            'google_key' => $this->google_key,
            'apple_enabled' => $this->apple_enabled,
            'apple_id' => $this->apple_id,
            'apple_key' => $this->apple_key,
            'btw_nr' => $this->btw_nr
        ]);
    }

    /**
     * @param float $distance
     * @return string
     */
    public function getDistanceFormat($distance)
    {
        if (empty($distance)) {
            return 0;
        }

        return Helper::formatDistanceNumber($distance);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function workspaceCategories()
    {
        return $this->belongsToMany(
            \App\Models\RestaurantCategory::class,
            'workspace_category',
            'workspace_id',
            'restaurant_category_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function category()
    {
        return $this->hasMany(
            \App\Models\Category::class,
            'workspace_id'
        )
        ->where('active', Category::ACTIVE)
        ->orderBy('order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingExceptHours()
    {
        $today = date('Y-m-d');
        $startDate = $today;// . ' ' . '00:00:00';
        $endDate = $today;// . ' ' . '23:59:59';

        return $this->hasMany(
            \App\Models\SettingExceptHour::class,
            'workspace_id'
        )
        ->where('start_time', '<=', \App\Helpers\Helper::convertDateTimeToUTC($startDate, 'UTC'))
        ->where('end_time', '>=', \App\Helpers\Helper::convertDateTimeToUTC($endDate, 'UTC'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingExceptHoursExtend()
    {
        $today = date('Y-m-d');
        $startDate = $today;// . ' ' . '00:00:00';
        $endDate = $today;// . ' ' . '23:59:59';

        return $this->hasMany(
            \App\Models\SettingExceptHour::class,
            'workspace_id'
        );
    }

    /**
     * @return array
     */
    public function getListCategories()
    {
        $data = [];
        $categories = $this->category()->with(['workspace', 'translations', 'categoryAvatar'])->get();

        if(!$categories->isEmpty()) {
            foreach ($categories as $category) {
                $data[] = $category->getFullInfo();
            }
        }

        return $data;
    }

    public function getSettingGeneral() {
        return $this->settingGeneral;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $data = [];

        foreach ($this->workspaceCategories as $category) {
            $data[] = $category->getSummaryInfo();
        }

        return $data;
    }

    /**
     * Mutator photo
     *
     * @return string
     */
    public function getPhotoAttribute()
    {
        /** @var \App\Models\Media $photo */
        $photo = $this->workspaceAvatar;

        if (empty($photo)) {
            return null;
        }

        return $photo->full_path;
    }

    /**
     * Active languages
     *
     * @return array
     */
    public function getActiveLanguagesAttribute($value)
    {
        return $value ? collect(explode(',', $value))->merge(['nl'])->unique()->all() : ['nl'];
    }

    /**
     * Active languages
     *
     * @return array
     */
    public function setActiveLanguagesAttribute($value)
    {
        $this->attributes['active_languages'] = collect(explode(',', is_array($value) ? implode(',', $value) : $value))
            ->merge(['nl'])
            ->unique()
            ->implode(',');
    }

    /**
     * Mutator gallery
     *
     * @return array
     */
    public function getGalleryAttribute()
    {
        $gallery = $this->workspaceGalleries->sortBy('order')->toArray();

        return $gallery;
    }

    /**
     * Mutator full_gallery
     *
     * @return array
     */
    public function getFullGalleryAttribute()
    {
        $gallery = $this->workspaceGalleries->transform(function ($item) {
            /** @var \App\Models\Media $item */
            return $item->getFullInfo();
        });

        return $gallery->toArray();
    }

    /**
     * Mutator api_gallery
     * @return array
     */
    public function getApiGalleryAttribute()
    {
        $apiGallery = $this->workspaceAPIGalleries->transform(function ($item) {
            return $item->getFullInfo();
        });

        $apiGalleryFilters = $apiGallery->filter(function ($value, $key) {
            return $value['active'] == 1;
        })->sortBy('order');

        $result = [];
        foreach ($apiGalleryFilters as $apiGalleryFilter) {
            $result[] = $apiGalleryFilter;
        }

        return $result;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExtras()
    {
        return $this->workspaceExtras->transform(function ($extra) {
            /** @var \App\Models\WorkspaceExtra $extra */

            return $extra->getFullInfo();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function connectors()
    {
        return $this->hasMany(\App\Models\SettingConnector::class);
    }

    public function groupRestaurants()
    {
        return $this->belongsToMany(
            GroupRestaurant::class,
            'group_restaurant_workspace',
            'workspace_id',
            'group_restaurant_id'
        );
    }

    public function workspaceExtraLoyalty()
    {
        return $this->hasMany(WorkspaceExtra::class, 'workspace_id')
        ->where('type', WorkspaceExtra::CUSTOMER_CARD)
        ->where('active', WorkspaceExtra::ACTIVE);
    }

    /**
     * Check is enable/disable Table Ordering
     *
     * @param Workspace $workspace
     * @return bool
     */
    public function enableTableOrdering(Workspace $workspace = null)
    {
        if ($workspace === null) {
            // Default is current workspace
            $workspace = $this;
        }

        if (empty($workspace->id)) {
            // Default is disable
            return false;
        }

        $record = WorkspaceExtra::where('type', WorkspaceExtra::TABLE_ORDERING)
            ->where('workspace_id', $workspace->id)
            ->where('active', WorkspaceExtra::ACTIVE)
            ->first();

        return !empty($record);
    }

    /**
     * Check is enable/disable In House
     *
     * @param Workspace $workspace
     * @return bool
     */
    public function enableInHouse(Workspace $workspace = null)
    {
        if ($workspace === null) {
            // Default is current workspace
            $workspace = $this;
        }

        if (empty($workspace->id)) {
            // Default is disable
            return false;
        }

        $enableTableOrdering = $this->enableTableOrdering();

        return $enableTableOrdering;
    }

    /**
     * Check is enable/disable Self Ordering
     *
     * @param Workspace $workspace
     * @return bool
     */
    public function enableSelfOrdering(Workspace $workspace = null)
    {
        if ($workspace === null) {
            // Default is current workspace
            $workspace = $this;
        }

        if (empty($workspace->id)) {
            // Default is disable
            return false;
        }

        $record = WorkspaceExtra::where('type', WorkspaceExtra::SELF_ORDERING)
            ->where('workspace_id', $workspace->id)
            ->where('active', WorkspaceExtra::ACTIVE)
            ->first();

        return !empty($record);
    }

    public function getLocale() {
        return $this->language ?: \App::getLocale();
    }
}
