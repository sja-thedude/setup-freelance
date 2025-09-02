<?php

namespace App\Models;

use App\Helpers\GroupHelper;
use App\Helpers\Helper;

class Cart extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'carts';

    public $timestamps = true;

    const TYPE_TAKEOUT = 0;
    const TYPE_LEVERING = 1;

    const TAB_TAKEOUT = "afhaal";
    const TAB_LEVERING = "levering";

    const COUPON = 1;
    const REDEEM = 2;
    const GROUP = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'workspace_id',
        'user_id',
        'coupon_id',
        'redeem_history_id',
        'group_id',
        'type',
        'note',
        'address_type',
        'address',
        'lat',
        'long',
        'date',
        'time',
        'redeem_discount',
        'date_time',
        'timezone',
        'open_timeslot_id',
        'setting_timeslot_detail_id',
        'setting_payment_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class, 'workspace_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class, 'group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class, 'coupon_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function openTimeslot()
    {
        return $this->belongsTo(\App\Models\OpenTimeslot::class, 'open_timeslot_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingPayment()
    {
        return $this->belongsTo(\App\Models\SettingPayment::class, 'setting_payment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class, 'cart_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingTimeslotDetail()
    {
        return $this->belongsTo(\App\Models\SettingTimeslotDetail::class, 'setting_timeslot_detail_id');
    }

    /**
     *
     * Gia tien đã bao gồm coupon và redeem
     *
     * @return int
     */
    public function getTotalPriceAttribute()
    {
        $totalPrice            = 0;
        $priceDiscount         = 0;
        $totalCouponDiscount   = 0;
        $couponDiscount        = 0;
        $vatsProduct           = array();
        $discountProducts      = [];
        $isDeleveringAvailable = TRUE;

        foreach ($this->cartItems as $cartItems) {
            $totalOptions         = 0;
            $product              = $cartItems->product;
            $number               = $cartItems->total_number;
            $productOptions       = $cartItems->cartOptionItems;
            $groupCartOptionItems = $productOptions->groupBy('optie_id');

            if (!$product->category->available_delivery) {
                $isDeleveringAvailable = FALSE;
            }

            foreach ($groupCartOptionItems as $optId => $cartOptionItems) {
                $options = collect();

                foreach ($cartOptionItems as $cartOptionItem) {
                    $optionItem = $cartOptionItem->optionItem;
                    $options->push($optionItem);
                }
                $isMaster = $options
                    ->where('master', true)
                    ->first();

                if ($isMaster) {
                    $totalOptions += $number * $isMaster->price;
                } else {
                    $totalOptions += $number * $options->sum('price');
                }
            }
            $totalPrice += $totalOptions + $product->price * $number;

            // Get vat of product
            $field = "take_out";
            if ($this->type == self::TYPE_LEVERING) {
                $field = "delivery";
            }

            $vatsProduct[$product->id] = $product->vat->{$field};
        }

        if ($this->coupon) {
            $discountCouponProducts = Helper::calculateCouponDiscount($this, $this->coupon->id);
            $totalCouponDiscount = !empty($discountCouponProducts['totalDiscount']) ? $discountCouponProducts['totalDiscount'] : 0;
            $discountProducts = !empty($discountCouponProducts['discountProducts']) ? $discountCouponProducts['discountProducts'] : [];
            $discountProducts = Helper::getProductIdsFromKey(array_keys($discountProducts));
            $priceDiscount  = $totalCouponDiscount;

            $productPrices = Helper::calculatePriceFromCart($this, $this->coupon->products->pluck('id')->toArray());
            $couponDiscount = Helper::calculateCouponDiscountValue($this->coupon, $productPrices);
        }

        if (!is_null($this->redeem_history_id)) {
            $discountRedeemProducts = Helper::calculateRedeemDiscount($this, $this->redeem_history_id);
            $totalRedeemDiscount = !empty($discountRedeemProducts['totalDiscount']) ? $discountRedeemProducts['totalDiscount'] : 0;
            $discountProducts = !empty($discountRedeemProducts['discountProducts']) ? $discountRedeemProducts['discountProducts'] : [];
            $discountProducts = Helper::getProductIdsFromKey(array_keys($discountProducts));
            $priceDiscount  = $totalRedeemDiscount;
        }

        if (GroupHelper::isApplyGroupDiscount($this)) {
            $discountGroupProducts = GroupHelper::calculateTotalGroupDiscount($this);

            $totalGroupDiscount = !empty($discountGroupProducts['totalDiscount']) ? $discountGroupProducts['totalDiscount'] : 0;
            $discountProducts = !empty($discountGroupProducts['discountProducts']) ? $discountGroupProducts['discountProducts'] : [];
            $discountProducts = Helper::getProductIdsFromKey(array_keys($discountProducts));
            $priceDiscount  = $totalGroupDiscount;
        }

        $priceDiscount = $priceDiscount > 0 ? $priceDiscount : 0;

        $this->setAttribute('price_discount', $priceDiscount);
        $this->setAttribute('is_delevering_available', $isDeleveringAvailable);
        $this->setAttribute('product_ids', array_keys($vatsProduct));
        $this->setAttribute('discount_products', $discountProducts);
        $this->setAttribute('total_coupon_discount', $totalCouponDiscount);
        $this->setAttribute('coupon_discount', $couponDiscount);

        return number_format((float)($totalPrice - $priceDiscount), 2, '.', '');
    }

    /**
     * Gia tien chua bao gom fee ship, coupon va redeem
     *
     * @return int
     */
    public function getSubTotalPriceAttribute()
    {
        $totalPrice = 0;

        foreach ($this->cartItems as $cartItems) {

            $totalOptions         = 0;
            $product              = $cartItems->product;
            $number               = $cartItems->total_number;
            $productOptions       = $cartItems->cartOptionItems;
            $groupCartOptionItems = $productOptions->groupBy('optie_id');

            $failedOpties = Helper::getFailedOpties($this);
            foreach ($groupCartOptionItems as $optId => $cartOptionItems) {
                if (!empty($failedOpties[$product->id]) && in_array($optId, $failedOpties[$product->id])) {
                    continue;
                }

                $options = collect();

                foreach ($cartOptionItems as $cartOptionItem) {
                    $optionItem = $cartOptionItem->optionItem;
                    $options->push($optionItem);
                }

                $isMaster = $options
                    ->where('master', true)
                    ->first();

                if ($isMaster) {
                    $totalOptions += $number * $isMaster->price;
                } else {
                    $totalOptions += $number * $options->sum('price');
                }
            }

            $totalPrice += $totalOptions + $product->price * $number;
        }

        return number_format((float)($totalPrice), 2, '.', '');
    }
}
