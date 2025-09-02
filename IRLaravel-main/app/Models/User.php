<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Utils\Token;
use App\Traits\UserACL;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends AppModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, Notifiable, CanResetPassword, UserACL, SoftDeletes;

    const SUPER_ADMIN_ID = 1; // User ID
    const DEFAULT_ROLE_ID = 2; // Normal user

    const GENDER_NONE = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;

    const PLATFORM_BACKOFFICE = 0; // Admin, Backend
    const PLATFORM_MANAGER = 1; // Manager
    const PLATFORM_FRONTEND = 2; // Frontend

    const STATUS_ACTIVE = 0;
    const STATUS_INVITATION_EXPIRED = 1;
    const STATUS_INVITATION_SENT = 2;

    protected $dates = ['deleted_at'];

    public $summary_fields = [
        'id',
        'name',
        'first_name',
        'last_name',
        'email',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'platform',
        'name',
        'email',
        'email_tmp',
        'password',
        'password_tmp',
        'photo',
        'description',
        'is_super_admin',
        'is_admin',
        'role_id',
        'active',
        'is_verified',
        'verify_token',
        'verify_expired_at',
        'first_name',
        'last_name',
        'default_password',
        'api_token',
        'birthday',
        'gender',
        'address',
        'lat',
        'lng',
        'phone',
        'fb_id',
        'fb_token',
        'gg_id',
        'gg_token',
        'tw_id',
        'tw_token',
        'last_login',
        'locale',
        'timezone',
        'gsm',
        'status',
        'first_login',
        'credit',
        'workspace_id',
    ];

    protected $cast = [
        'id' => 'integer',
        'platform' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'email_tmp' => 'string',
        'password' => 'string',
        'password_tmp' => 'string',
        'photo' => 'string',
        'description' => 'string',
        'is_super_admin' => 'boolean',
        'is_admin' => 'boolean',
        'remember_token' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'role_id' => 'integer',
        'deleted_at' => 'datetime',
        'active' => 'boolean',
        'is_verified' => 'boolean',
        'verify_token' => 'string',
        'verify_expired_at' => 'datetime',
        'first_name' => 'string',
        'last_name' => 'string',
        'default_password' => 'string',
        'api_token' => 'string',
        'birthday' => 'date',
        'gender' => 'integer',
        'address' => 'string',
        'lat' => 'string',
        'lng' => 'string',
        'phone' => 'string',
        'fb_id' => 'string',
        'fb_token' => 'string',
        'gg_id' => 'string',
        'gg_token' => 'string',
        'tw_id' => 'string',
        'tw_token' => 'string',
        'last_login' => 'datetime',
        'locale' => 'string',
        'timezone' => 'string',
        'gsm' => 'string',
        'status' => 'integer',
        'first_login' => 'datetime',
        'credit' => 'integer',
        'workspace_id' => 'integer',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'default_password',
        'remember_token'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function meta()
    {
        return $this->hasMany('App\Modules\ContentManager\Models\UserMeta', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class);
    }

    /**
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isAdmin($user = null) {
        if ($user === null) {
            $user = $this;
        }

        return ($user->is_admin);
    }

    /**
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isSuperAdmin($user = null) {
        if ($user === null) {
            $user = $this;
        }

        return ($user->id == \App\Models\User::SUPER_ADMIN_ID || !empty($user->is_super_admin));
    }

    /**
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isRecognised($user = null) {
        if ($user === null) {
            $user = $this;
        }

        return ($user->active && $user->is_verified);
    }

    /**
     * Get first user which is Admin
     *
     * @return \App\Models\User
     */
    public function getActiveAdmin() {
        $user = static::where('active', static::IS_YES)
            ->whereNull('deleted_at')
            ->where('is_admin', static::IS_YES)
            ->first();

        return $user;
    }

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param integer|null $value
     * @return string|array
     */
    public static function genders($value = null)
    {
        $options = array(
            static::GENDER_NONE => trans('strings.none'),
            static::GENDER_FEMALE => trans('strings.female'),
            static::GENDER_MALE => trans('strings.male'),
            static::GENDER_OTHER => trans('strings.other'),
        );
        return static::enum($value, $options);
    }

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param integer|null $value
     * @return string
     */
    public static function active_statuses($value = null) {
        $options = array(
            static::IS_NO => trans('strings.inactive'),
            static::IS_YES => trans('strings.active'),
        );
        return static::enum($value, $options);
    }

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param integer|null $value
     * @return string
     */
    public static function verify_statuses($value = null) {
        $options = array(
            static::IS_NO => trans('strings.unverified'),
            static::IS_YES => trans('strings.verified'),
        );
        return static::enum($value, $options);
    }

    const USER_TYPE_NORMAL = 0;
    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_ALL = -1;

    // Login Types
    const LOGIN_WITH_FACEBOOK = 'fb';
    const LOGIN_WITH_GOOGLE = 'gg';
    const LOGIN_WITH_TWITTER = 'tw';

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param integer|null $value
     * @return string
     */
    public static function login_types($value = null)
    {
        $options = array(
            static::LOGIN_WITH_FACEBOOK => 'Facebook',
            static::LOGIN_WITH_GOOGLE => 'Google',
            static::LOGIN_WITH_TWITTER => 'Twitter',
        );
        return static::enum($value, $options);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->id,
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-d-m H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-d-m H:i:s'),
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'description' => $this->description,
            'phone' => $this->phone,
            'gsm' => $this->gsm,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'gender_display' => ($this->gender === null) ? '' : User::genders($this->gender),
            'photo' => (!empty($this->getOriginal('photo'))) ? $this->photo : null,
            'address' => $this->address,
            'lng' => $this->lng,
            'lat' => $this->lat,
            'last_login' => Helper::getDatetimeFromFormat($this->last_login, 'Y-d-m H:i:s'),
            'locale' => $this->locale,
            'timezone' => $this->timezone,
        ];
    }

    /**
     * Fire events when create, update, delete teams
     * The "booting" method of the model.
     * @link https://stackoverflow.com/a/38685534
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Add validations
        // Validate gender
        Validator::extend('in_genders', function ($attribute, $value, $parameters, $validator) {
            $valid = (array_key_exists($value, User::genders()));

            return $valid;
        });

        static::creating(function ($model) {
            $model->name = $model->first_name .' '. $model->last_name;
        });

        static::created(function ($model) {
            $tokenService = new Token();
            $model->api_token = $tokenService->signToken($model->id);
        });

        static::updating(function ($model) {
            $model->name = $model->first_name .' '. $model->last_name;
        });

        static::saving(function ($model) {
            $model->name = $model->first_name .' '. $model->last_name;
        });

        // When saved
        static::saved(function ($model) {
            if(empty($model->api_token)) {
                $tokenService = new Token();
                $model->api_token = $tokenService->signToken($model->id);
                $model->save();
            }
        });
    }

    /**
     * @link https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php#answer-6101969
     *
     * @param int $length
     * @return string
     */
    public static function randomPassword($length = 8) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return \App\Models\User
     */
    public function getUserByEmail($email)
    {
        $user = static::where('email', $email)
            ->whereNull('deleted_at')
            ->orderBy($this->getKeyName(), static::ORDER_DESC)
            ->first();

        return $user;
    }

    /**
     * Get user by SNS: facebook, google, tweeter,...
     *
     * @param array $needle - [$sns_id => $ref_id, $sns_token, $ref_token]
     * @return \App\Models\User
     */
    public function getUserBySNS($needle)
    {
        // Init query builder
        $builder = static::whereNotNull($this->primaryKey);

        foreach ($needle as $k => $v) {
            $builder->where($k, $v);
        }

        $user = $builder->first();

        return $user;
    }

    /**
     * static enum: Model::function()
     *
     * @access static
     * @param string|null $value
     * @return string|array
     */
    public static function timezones($value = null)
    {
        $helper = new Helper();
        $options = $helper->getTimezones();

        return static::enum($value, $options);
    }

    /**
     * Get role list
     *
     * @param int $userType User type. 0: User, 1: Admin.
     * @return \Illuminate\Support\Collection
     */
    public function getRoleList($userType = 0)
    {
        /** @var \App\Models\Role $roleInstance */
        $roleInstance = Role::getInstance();
        $roles = Role::active();

        // With User List, Role = User
        if ($userType == User::USER_TYPE_ADMIN) {
            // Other admin roles
            $roles->whereNotIn($roleInstance->getKeyName(), $roleInstance->getNormalUserRoles());
        } else {
            // User
            $roles->whereIn($roleInstance->getKeyName(), $roleInstance->getNormalUserRoles());
        }

        // Get list
        /** @var \Illuminate\Support\Collection $roles */
        $roles = $roles->pluck('name', $roleInstance->getKeyName());
        // Translate
        $roles->transform(function ($item, $key) {
            return Helper::trans($item, [], 'role');
        });

        return $roles;
    }

    /**
     * Get the user's photo attribute.
     *
     * @param string $value
     * @return string
     */
    public function getPhotoAttribute($value)
    {
        // Default avatar from config
        $defaultAvatar = config('common.default_avatar');

        if (empty($value)) {
            // Default photo
            return Helper::getLinkFromDataSource(null, $defaultAvatar);
        }

        // Photo from db
        return Helper::getLinkFromDataSource($value, $defaultAvatar, 'storage/');
    }

    /**
     * Set the user's photo attribute.
     *
     * @param string $value
     */
    public function setPhotoAttribute($value)
    {
        $this->attributes['photo'] = Helper::getRelativeResource($value);
    }

    /**
     * Set the user's first_name attribute.
     *
     * @param string $value
     */
    public function setFirstNameAttribute($value)
    {
        if (empty($value)) {
            $value = $this->name;
        }

        $this->attributes['first_name'] = $value;
    }

    /**
     * Send the password reset notification.
     * Overwrite this method for include email to Reset password link in the Email
     * The ResetPasswordNotification class is implemented in App\Notifications package
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $locale = session('locale', \App::getLocale());
        $this->notify(new ResetPasswordNotification($token, $locale));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class, 'workspace_id');
    }

    /**
     * @param $workspaceId
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loyalties($workspaceId)
    {
        return $this->hasOne(\App\Models\Loyalty::class, 'user_id')
            ->where('workspace_id', $workspaceId);
    }

    public function social()
    {
        return $this->hasMany(\App\Models\UserSocial::class, 'user_id');
    }

    public static function getPlatforms()
    {
        return [
            static::PLATFORM_BACKOFFICE => 'Admin',
            static::PLATFORM_MANAGER => 'Manager',
            static::PLATFORM_FRONTEND => 'Client',
        ];
    }
    
    public function getLocale() {
        return $this->locale ?: \App::getLocale();
    }
}
