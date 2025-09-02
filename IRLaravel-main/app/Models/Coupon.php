<?php

namespace App\Models;

use App\Facades\Helper;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends AppModel
{
    use Translatable;
    use SoftDeletes;

    const DISCOUNT_FIXED_AMOUNT = 1;
    const DISCOUNT_PERCENTAGE = 2;

    public $table = 'coupons';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'created_at',
        'updated_at',
        'active',
        'workspace_id',
        'code',
        'max_time_all',
        'max_time_single',
        'currency',
        'discount',
        'expire_time',
        'discount_type',
        'percentage',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'workspace_id' => 'integer',
        'code' => 'string',
        'max_time_all' => 'integer',
        'max_time_single' => 'integer',
        'currency' => 'string',
        'discount' => 'float',
        'expire_time' => 'datetime',
        'discount_type' => 'integer',
        'percentage' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required|max:255',
    ];

    /**
     * @var string
     */
    public $translationModel = 'App\Models\CouponTranslation';

    /**
     * @var string
     */
    public $translationForeignKey = 'coupon_id';

    /**
     * @var array
     */
    public $translatedAttributes = [
        'promo_name',
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class, 'coupon_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'coupon_categories');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'coupon_products');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\CouponTranslation::class);
    }

    /**
     * Mutator type_display
     *
     * @return string
     */
    public function getActiveDisplayAttribute()
    {
        return $this->active ? trans('option.ja') : trans('option.nee');
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return array_merge(parent::getFullInfo(), [
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'code' => $this->code,
            'promo_name' => $this->promo_name, // in the translation table
            'workspace_id' => $this->workspace_id,
            'workspace' => $this->workspace->getSummaryInfo(),
            'max_time_all' => $this->max_time_all,
            'max_time_single' => $this->max_time_single,
            'currency' => $this->currency,
            'discount' => Helper::formatCurrencyNumber($this->discount),
            'expire_time' => Helper::getDatetimeFromFormat($this->expire_time, 'Y-m-d H:i:s'),
            'discount_type' => $this->discount_type,
            'percentage' => $this->percentage
        ]);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'coupon_id');
    }

    public function categoriesRelation()
    {
        return $this->hasMany(CategoryRelation::class,'foreign_id')
            ->where('foreign_model', static::class);
    }
}
