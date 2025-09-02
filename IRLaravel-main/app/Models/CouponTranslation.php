<?php

namespace App\Models;

class CouponTranslation extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'coupon_translations';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'coupon_id',
        'locale',
        'promo_name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'coupon_id' => 'integer',
        'locale' => 'string',
        'promo_name' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'coupon_id' => 'required|integer',
        'locale' => 'required||max:3',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class, 'coupon_id');
    }
}
