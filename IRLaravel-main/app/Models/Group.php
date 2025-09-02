<?php

namespace App\Models;

use App\Facades\Helper;
use App\Helpers\GroupHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends AppModel
{
    use SoftDeletes;

    const NO_DISCOUNT = 0;
    const FIXED_AMOUNT = 1;
    const PERCENTAGE = 2;

    public $table = 'groups';

    public $fillable = [
        'created_at',
        'updated_at',
        'workspace_id',
        'name',
        'company_name',
        'company_street',
        'company_number',
        'company_vat_number',
        'company_city',
        'company_postcode',
        'payment_mollie',
        'payment_payconiq',
        'payment_cash',
        'payment_factuur',
        'close_time',
        'receive_time',
        'type',
        'contact_email',
        'contact_name',
        'contact_surname',
        'contact_gsm',
        'active',
        'discount_type',
        'is_product_limit',
        'discount',
        'percentage',
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'workspace_id' => 'integer',
        'name' => 'string',
        'company_name' => 'string',
        'company_street' => 'string',
        'company_number' => 'string',
        'company_vat_number' => 'string',
        'company_city' => 'string',
        'company_postcode' => 'string',
        'payment_mollie' => 'integer',
        'payment_payconiq' => 'integer',
        'payment_cash' => 'integer',
        'payment_factuur' => 'integer',
        'close_time' => 'string',
        'receive_time' => 'string',
        'type' => 'integer',
        'contact_email' => 'string',
        'contact_name' => 'string',
        'contact_surname' => 'string',
        'contact_gsm' => 'string',
        'active' => 'integer',
        'discount_type' => 'integer',
        'is_product_limit' => 'integer',
        'discount' => 'float',
        'percentage' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];

    /*public $summary_fields = [
        'id',
        'name',
    ];*/

    /**
     * @return array
     */
    public function getSummaryInfo()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'address_display' => $this->address_display,
            'is_product_limit' => $this->is_product_limit,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'percentage' => $this->percentage,
            'active' => $this->active,
        ];
    }

    public function getProducts()
    {
        $data = [];
        $limitProducts = GroupHelper::getLimitProducts($this);

        if (count($limitProducts) > 0) {
            $products = Product::whereIn('id', $limitProducts)->get();

            foreach ($products as $product) {
                $data[] = $product->getSummaryInfo();
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getProductsBasedOnToggle()
    {
        $data = [];
        if ($this->is_product_limit) {
            $limitProducts = $this->products->pluck('id')->toArray();

            // Apply for categories
            $limitProducts = \App\Helpers\Helper::getCategoryIds($this, $limitProducts);
            $groupProducts = Product::whereIn('id', $limitProducts)->get();

            foreach ($groupProducts as $groupProduct) {
                $data[] = $groupProduct->getSummaryInfo();
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getListInfo()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'address_display' => $this->address_display,
        ];
    }

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
        return $this->hasMany(\App\Models\Cart::class, 'group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'group_id');
    }

    const TYPE_TAKEOUT = 0;
    const TYPE_DELIVERY = 1;
    // const TYPE_IN_HOUSE = 2;

    /**
     * @param int|null $value
     * @return string|array
     */
    public static function getTypes($value = null)
    {
        $options = array(
            static::TYPE_TAKEOUT => trans('delivery_types.take_out'),
            static::TYPE_DELIVERY => trans('delivery_types.delivery'),
            // static::TYPE_IN_HOUSE => trans('delivery_types.in_house'),
        );
        return static::enum($value, $options);
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
     * Mutator type_display
     *
     * @return string
     */
    public function getAddressDisplayAttribute()
    {
        return implode(", ", array_filter([
            implode(" ", array_filter([$this->company_street, $this->company_number])),
            implode(" ", array_filter([$this->company_postcode, $this->company_city]))
        ]));
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'workspace_id' => $this->workspace_id,
            'workspace' => $this->workspace->getSummaryInfo(),
            'name' => $this->name,
            'company_name' => $this->company_name,
            'company_street' => $this->company_street,
            'company_number' => $this->company_number,
            'company_vat_number' => $this->company_vat_number,
            'company_city' => $this->company_city,
            'company_postcode' => $this->company_postcode,
            'address_display' => $this->address_display,
            'payment_mollie' => $this->payment_mollie,
            'payment_payconiq' => $this->payment_payconiq,
            'payment_cash' => $this->payment_cash,
            'payment_factuur' => $this->payment_factuur,
            'close_time' => $this->close_time,
            'receive_time' => $this->receive_time,
            'type' => $this->type,
            'type_display' => $this->type_display,
            'contact_email' => $this->contact_email,
            'contact_name' => $this->contact_name,
            'contact_surname' => $this->contact_surname,
            'contact_gsm' => $this->contact_gsm,
            'active' => $this->active,
            'is_product_limit' => $this->is_product_limit,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'percentage' => $this->percentage,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openTimeSlots()
    {
        return $this->hasMany(\App\Models\OpenTimeslot::class, 'foreign_id')
            ->where('foreign_model', static::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'group_products');
    }

    public function categoriesRelation()
    {
        return $this->hasMany(CategoryRelation::class,'foreign_id')
            ->where('foreign_model', static::class);
    }
}
