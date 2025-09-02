<?php

namespace App\Models;

use App\Facades\Helper;
use App\Helpers\OrderHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends AppModel
{
    use SoftDeletes;

    const TYPE_TAKEOUT = 0;
    const TYPE_DELIVERY = 1;
    const TYPE_IN_HOUSE = 2;
    const TYPE_SELF_ORDERING = 3;

    /**
     * Payment methods
     */
    const PAYMENT_METHOD_CASH = 0;
    const PAYMENT_METHOD_PAID_ONLINE = 1;
    const PAYMENT_METHOD_FOR_INVOICE = 2;

    /**
     * Payment statuses
     */
    const PAYMENT_STATUS_UNKOWN = 0;
    const PAYMENT_STATUS_PENDING = 1;
    const PAYMENT_STATUS_PAID = 2;
    const PAYMENT_STATUS_CANCELLED = 3;
    const PAYMENT_STATUS_FAILED = 4;
    const PAYMENT_STATUS_EXPIRED = 5;

    const ORDER_TYPE_INDIVIDUAL = 0;
    const ORDER_TYPE_GROUP = 1;

    const ORDER_TIME_FUTURE = 0;
    const ORDER_TIME_PAST = 1;

    const IS_TRUST_ACCOUNT = 0;
    const IS_TEST_ACCOUNT = 1;

    const STICKER_PRINT_REMAINING = 0;
    const STICKER_PRINT_ALLOW_ALL_IF_DONE = 1;

    const A4_PRINT_MULTI = 0;
    const A4_PRINT_FORCE = 1;

    const LOYALTY_ADDED = 1;

    // Filter by printing status
    const NOT_PRINTED = 0;
    const PRINTED = 1;
    const TO_BE_PRINTED = 2;

    const NOT_PRINTED_AUTO_ENABLED = 3;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public $table = 'orders';

    public $fillable = [
        'workspace_id',
        'template_id',
        'user_id',
        'parent_id',
        'setting_payment_id',
        'open_timeslot_id',
        'setting_timeslot_detail_id',
        'setting_delivery_condition_id',
        'group_id',
        'daily_id',
        'no_show',
        'printed',
        'printed_werkbon',
        'printed_kassabon',
        'printed_sticker',
        'printed_a4',
        'payment_method',
        'payment_status',
        'payed_at',
        'timezone',
        'date_time',
        'time',
        'date',
        'address',
        'address_type',
        'lat',
        'lng',
        'type',
        'meta_data',
        'note',
        'subtotal',
        'total_price',
        'total_paid',
        'currency',
        'coupon_id',
        'coupon_code',
        'coupon_discount',
        'redeem_history_id',
        'redeem_discount',
        'ship_price',
        'status',
        'mollie_id',
        'push_notification_reminder',
        'email_confirmations_manager',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_test_account',
        'auto_print',
        'run_crontab',
        'metas',
        'printed_sticker_multi',
        'group_restaurant_id',
        'auto_print_sticker',
        'auto_print_werkbon',
        'auto_print_kassabon',
        'group_discount',
        'trigger_auto_scan',
        'deleted_at',
        'deleted_timeslot',
        'loyalty_added',
        'extra_code',
        'table_number',
        'table_last_person',
        'contact_id',
        'service_cost'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'workspace_id' => 'integer',
        'template_id' => 'integer',
        'daily_id' => 'integer',
        'no_show' => 'boolean',
        'printed' => 'boolean',
        'printed_werkbon' => 'boolean',
        'printed_kassabon' => 'boolean',
        'printed_sticker' => 'boolean',
        'printed_a4' => 'boolean',
        'payment_method' => 'integer',
        'payment_status' => 'integer',
        'timezone' => 'string',
        'date_time' => 'datetime',
        'address' => 'string',
        'address_type' => 'integer',
        'lat' => 'string',
        'lng' => 'string',
        'type' => 'integer',
        'meta_data' => 'string',
        'note' => 'string',
        'subtotal' => 'decimal',
        'total_price' => 'decimal',
        'currency' => 'string',
        'coupon_code' => 'string',
        'coupon_discount' => 'decimal',
        'redeem_discount' => 'decimal',
        'ship_price' => 'decimal',
        'status' => 'integer',
        'mollie_id' => 'string',
        'push_notification_reminder' => 'boolean',
        'email_confirmations_manager' => 'boolean',
        'is_test_account' => 'boolean',
        'auto_print' => 'boolean',
        'run_crontab' => 'boolean',
        'metas' => 'string',
        'printed_sticker_multi' => 'boolean',
        'group_restaurant_id' => 'integer',
        'auto_print_sticker' => 'boolean',
        'auto_print_werkbon' => 'boolean',
        'auto_print_kassabon' => 'boolean',
        'trigger_auto_scan' => 'boolean',
        'deleted_timeslot' => 'boolean',
        'loyalty_added' => 'boolean',
        'extra_code' => 'integer',
        'table_number' => 'integer',
        'table_last_person' => 'boolean',
        'contact_id' => 'integer',
        'service_cost' => 'decimal',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required|integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules_raw = [
        'workspace_id' => 'required|integer',
        'items' => 'required|array',
        'items.*.product_id' => 'required|integer',
        'items.*.quantity' => 'required|integer',
        'items.*.option_items' => 'array',
        'items.*.option_items.*.option_item_id' => 'integer|required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupOrders()
    {
        return $this->hasMany(\App\Models\Order::class, 'parent_id')
            ->where(function($query) {
                $query->whereIn('status', [self::PAYMENT_STATUS_PAID])
                    ->orWhereIn('payment_method', [SettingPayment::TYPE_CASH, SettingPayment::TYPE_FACTUUR]);
            })
            ->withTrashed()
            ->with('orderItems');
    }

    public function childrenOrders()
    {
        return $this->hasMany(Order::class, 'parent_id');
    }
    
    /**
     * Check if order is paid
     *
     * @return bool
     */
    public function checkOrderIsPaid()
    {
        return (in_array($this->status, [static::PAYMENT_STATUS_PAID])
            || in_array($this->payment_method, [SettingPayment::TYPE_CASH, SettingPayment::TYPE_FACTUUR]));
    }

    public function checkOrderIsPaidTableOrderingCash()
    {
        return ($this->status == static::PAYMENT_STATUS_PAID
            && in_array($this->payment_method, [SettingPayment::TYPE_CASH, SettingPayment::TYPE_MOLLIE]));
    }

    public function parentOrder()
    {
        return $this->belongsTo(\App\Models\Order::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function redeemHistory()
    {
        return $this->belongsTo(\App\Models\RedeemHistory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function openTimeslot()
    {
        return $this->belongsTo(\App\Models\OpenTimeslot::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingPayment()
    {
        return $this->belongsTo(\App\Models\SettingPayment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingDeliveryCondition()
    {
        return $this->belongsTo(\App\Models\SettingDeliveryConditions::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
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
    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class)
//            ->with('optionItems')
            ->with('product')
            ->with('category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderReferences()
    {
        return $this->hasMany(\App\Models\OrderReference::class, 'local_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingTimeslotDetail()
    {
        return $this->belongsTo(\App\Models\SettingTimeslotDetail::class, 'setting_timeslot_detail_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class);
    }

    /**
     * Generate unique code for Order ID
     *
     * @return array
     */
    public function createUniqueCode()
    {
        $number = $extraNumber = 0;
        $code = $extraCode = null;
        $order = null;

        if ($this->isTableOrdering()) {
            // Check to get code
            $parentOrderSameGroup = static::where('workspace_id', $this->workspace_id)
                ->whereDate('date', $this->date)
                ->where('type', OrderHelper::TYPE_IN_HOUSE)
                // ->where('table_number', $this->table_number)
                ->whereNull('parent_id')
                ->orderBy('code', 'DESC')
                ->first();

            if (!empty($parentOrderSameGroup)) {
                $code = sprintf('%03d', (int)$parentOrderSameGroup->code + 1);
            } else {
                $order = static::where('workspace_id', $this->workspace_id)
                    ->whereDate('date', $this->date)
                    ->where('type', OrderHelper::TYPE_IN_HOUSE)
                    // ->where('table_number', '!=', $this->table_number)
                    ->whereNull('parent_id')
                    ->orderBy('code', 'DESC')
                    ->first();
            }

            // Check to get extra code
            $childOrderInGroup = static::where('workspace_id', $this->workspace_id)
                ->whereDate('date', $this->date)
                ->where('type', OrderHelper::TYPE_IN_HOUSE)
                ->where('table_number', $this->table_number)
                ->whereNotNull('parent_id')
                ->orderBy('extra_code', 'DESC')
                ->first();

            if (!empty($childOrderInGroup)) {
                $extraNumber = $childOrderInGroup->extra_code;
            }
        } else if (!empty($this->group_id)) {
            // Check to get code
            $parentOrderSameGroup = static::where('workspace_id', $this->workspace_id)
                ->whereDate('date', $this->date)
                ->where('group_id', $this->group_id)
                ->whereNull('parent_id')
                ->first();

            if (!empty($parentOrderSameGroup)) {
                $code = $parentOrderSameGroup->code;
            } else {
                $order = static::where('workspace_id', $this->workspace_id)
                    ->whereDate('date', $this->date)
                    ->where('group_id', '!=', $this->group_id)
                    ->whereNull('parent_id')
                    ->orderBy('code', 'DESC')
                    ->first();
            }

            // Check to get extra code
            $childOrderInGroup = static::where('workspace_id', $this->workspace_id)
                ->whereDate('date', $this->date)
                ->where('group_id', $this->group_id)
                ->whereNotNull('parent_id')
                ->orderBy('extra_code', 'DESC')
                ->first();

            if (!empty($childOrderInGroup)) {
                $extraNumber = $childOrderInGroup->extra_code;
            }
        } else {
            // Last order have the biggest code
            $order = static::where('workspace_id', $this->workspace_id)
                ->whereDate('date', $this->date)
                ->whereNull('group_id')
                ->orderBy('code', 'DESC')
                ->first();
        }

        // Get number from order code
        if (!empty($order)) {
            $number = (int)$order->code;
        }

        // Calculate code from number
        if (empty($code)) {
            $code = sprintf('%03d', ($number + 1));
        }
        if (empty($extraCode)) {
            $extraCode = $extraNumber + 1;
        }

        return [
            'code' => $code,
            'extra_code' => $extraCode
        ];
    }

    /**
     * Fire events when create, update roles
     * The "booting" method of the model.
     * @link https://stackoverflow.com/a/38685534
     *
     * @overwrite
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // When creating
        static::creating(function ($model) {
            /** @var \App\Models\Order $model */
            $orderCodes = $model->createUniqueCode();
            $model->code = $orderCodes['code'];
            $model->extra_code = $orderCodes['extra_code'];

            return $model;
        });

        static::created(function ($model) {
            if((!empty($model->group_id) || $model->type == static::TYPE_IN_HOUSE) && !empty($model->parent_id) && !empty($model->parentOrder)) {
                if(in_array($model->status, [self::PAYMENT_STATUS_PAID])
                || in_array($model->payment_method, [SettingPayment::TYPE_CASH, SettingPayment::TYPE_FACTUUR])
                ) {
                    $parentOrder = $model->parentOrder;
                    $parentOrder->printed = false;
                    $parentOrder->printed_a4 = false;
                    $parentOrder->printed_sticker = false;
                    $parentOrder->printed_kassabon = false;
                    $parentOrder->printed_werkbon = false;
                    $parentOrder->auto_print = false;
                    $parentOrder->auto_print_sticker = false;
                    $parentOrder->auto_print_werkbon = false;
                    $parentOrder->auto_print_kassabon = false;
                    $parentOrder->printed_sticker_multi = false;
                    $parentOrder->save();
                }
            }
        });

        static::saved(function ($model) {
            /** @var \App\Models\Order $model */
            if(empty($model->auto_print) &&
                !empty($model->auto_print_sticker) &&
                !empty($model->auto_print_werkbon) &&
                !empty($model->auto_print_kassabon)) {
                $model->auto_print = true;
                $model->save();
            }

            if((!empty($model->group_id) || $model->type == static::TYPE_IN_HOUSE) && empty($model->parent_id)) {
                if(empty($model->printed) &&
                    !empty($model->printed_a4) &&
                    !empty($model->printed_sticker) &&
                    !empty($model->printed_kassabon) &&
                    !empty($model->printed_werkbon)) {
                    $model->printed = true;
                    $model->save();
                }

                if(!$model->groupOrders->isEmpty()) {
                    if(!empty($model->printed)) {
                        $model->groupOrders()->update([
                            'printed' => true,
                            'printed_a4' => true,
                            'printed_sticker' => true,
                            'printed_kassabon' => true,
                            'printed_werkbon' => true
                        ]);
                    }
                    if (!empty($model->printed_sticker_multi)) {
                        $model->groupOrders()->update(['printed_sticker_multi' => true]);
                    }
                    if(!empty($model->printed_a4)) {
                        $model->groupOrders()->update(['printed_a4' => true]);
                    }
                    if(!empty($model->printed_sticker)) {
                        $model->groupOrders()->update(['printed_sticker' => true]);
                    }
                    if(!empty($model->printed_kassabon)) {
                        $model->groupOrders()->update(['printed_kassabon' => true]);
                    }
                    if(!empty($model->printed_werkbon)) {
                        $model->groupOrders()->update(['printed_werkbon' => true]);
                    }
                    if(!empty($model->auto_print)) {
                        $model->groupOrders()->update(['auto_print' => true]);
                    }
                    if(!empty($model->auto_print_sticker)) {
                        $model->groupOrders()->update(['auto_print_sticker' => true]);
                    }
                    if(!empty($model->auto_print_werkbon)) {
                        $model->groupOrders()->update(['auto_print_werkbon' => true]);
                    }
                    if(!empty($model->auto_print_kassabon)) {
                        $model->groupOrders()->update(['auto_print_kassabon' => true]);
                    }
                }
            }

            // Update parent data if order is group and has parent
            if ((!empty($model->group_id) || $model->type == static::TYPE_IN_HOUSE) && !empty($model->parent_id)) {
                $model->updateParent($model->parent_id);
            }
        });

        self::deleted(function ($model) {
            /** @var \App\Models\Order $model */

            // Update parent data if order is group and has parent
            if (!empty($model) && !empty($model->group_id) && !empty($model->parent_id)) {
                $model->updateParent($model->parent_id);
            }
        });
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        // Custom coupon discount from order history
        $coupon = null;

        if (!empty($this->coupon_id) && !empty($this->coupon)) {
            $this->coupon->discount = $this->coupon_discount;
            $coupon = $this->coupon->getSummaryInfo();
        }

        // Custom redeem discount from order history
        $redeem = null;

        if (!empty($this->redeem_history_id) && !empty($this->redeemHistory)) {
            $redeem = $this->redeemHistory->getSummaryInfo();
        }

        // Get setting delivery condition detail
        $delivery = null;

        if (!empty($this->setting_delivery_condition_id) && !empty($this->settingDeliveryCondition)) {
            $delivery = $this->settingDeliveryCondition->getSummaryInfo();
        }

        return array_merge($this->getListItemInfo(), [
            'items' => $this->getOrderItems(),
            'coupon_id' => $this->coupon_id,
            'coupon' => $coupon,
            'coupon_discount' => $this->coupon_discount,
            'redeem_history_id' => $this->redeem_history_id,
            'redeem_history' => $redeem,
            'redeem_discount' => $this->redeem_discount,
            'setting_delivery_condition_id' => $this->setting_delivery_condition_id,
            'setting_delivery_condition' => $delivery,
            'ship_price' => $this->ship_price,
            'service_cost' => $this->service_cost,
        ]);
    }

    /**
     * @return array
     */
    public function getListItemInfo()
    {
        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'date_time' => Helper::getDatetimeFromFormat($this->date_time, 'Y-m-d H:i:s'),
            'code' => $this->code,
            'extra_code' => $this->extra_code,
            'date' => $this->date,
            'time' => $this->time,
            'workspace_id' => $this->workspace_id,
            'workspace' => (!empty($this->workspace_id) && !empty($this->workspace)) ? $this->workspace->getSummaryInfo() : null,
            'user_id' => $this->user_id,
            'is_test_account' => $this->is_test_account,
            'user' => (!empty($this->user_id) && !empty($this->user)) ? $this->user->getSummaryInfo() : null,
            'parent_id' => $this->parent_id,
            'parent' => (!empty($this->parent_id) && !empty($this->parentOrder)) ? $this->parentOrder->getFullInfo() : null,
            'parent_code' => $this->parent_code,
            'group_id' => $this->group_id,
            'group' => (!empty($this->group_id) && $this->group) ? $this->group->getSummaryInfo() : null,
            'payment_method' => $this->payment_method,
            'payment_method_display' => $this->payment_method_display,
            'payment_status' => $this->payment_status,
            'payment_status_display' => $this->payment_status_display,
            'address' => $this->address,
            'address_type' => $this->address_type,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'type' => $this->type,
            'note' => $this->note,
            'subtotal' => Helper::formatCurrencyNumber($this->subtotal),
            'total_price' => Helper::formatCurrencyNumber($this->total_price),
            'total_paid' => Helper::formatCurrencyNumber($this->total_paid),
            'currency' => $this->currency,
            'status' => $this->status,
            'status_display' => $this->status_display,
            'items_count' => $this->order_items_count,
            'group_discount' => $this->group_discount,
        ];
    }

    /**
     * @param Order|null $order
     * @return array
     */
    public function getOrderItems(Order $order = null)
    {
        if (empty($order)) {
            $order = $this;
        }

        if (empty($order)) {
            return [];
        }

        $items = [];

        // G et all order items and related data with trashed (soft deleted)
        $orderItems = $order->orderItems()
            ->with([
                /* Order options items */
                'optionItems',
                /* Product option item info */
                'optionItems.optionItem' => function ($query) {
                    $query->withTrashed();
                },
                /* Product option info */
                'optionItems.optionItem.option' => function ($query) {
                    $query->withTrashed();
                },
                'optionItems.optionItem.option.workspace',
                'optionItems.optionItem.option.translations',
            ])
            ->get();

        /** @var \App\Models\OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            // Get meta data
            $orderItemMeta = $orderItem->metas;

            // Convert meta data to array
            if (!empty($orderItemMeta) && is_string($orderItemMeta)) {
                $tmpJson = json_decode($orderItemMeta, true);

                // Fastest way to check if a string is JSON in PHP
                /** @link https://stackoverflow.com/a/15198925 */
                if (json_last_error() === JSON_ERROR_NONE) {
                    // JSON is valid
                    $orderItemMeta = $tmpJson;
                }
            }

            // Origin product from relation
            $product = $orderItem->product;
            $currentProductName = $product->name;

            // Get product from meta data
            if (array_key_exists('product', $orderItemMeta)) {
                $product = new \App\Models\Product($orderItemMeta['product']);
            }

            $product->name = $currentProductName;
            $arrOrderItem = [
                'product_id' => $orderItem->product_id,
                'product' => $product->getFullInfo(),
                'price' => Helper::formatCurrencyNumber($orderItem->price),
                'quantity' => $orderItem->total_number,
                'subtotal' => Helper::formatCurrencyNumber($orderItem->subtotal),
                'total_price' => Helper::formatCurrencyNumber($orderItem->total_price),
                'vat_percent' => $orderItem->vat_percent,
                'paid' => $orderItem->paid,
                'coupon_id' => $orderItem->coupon_id,
                'coupon' => (!empty($orderItem->coupon_id) && !empty($orderItem->coupon)) ? $orderItem->coupon : null,
                'coupon_discount' => $orderItem->coupon_discount,
                'redeem_history_id' => $orderItem->redeem_history_id,
                'redeem_history' => (!empty($orderItem->redeem_history_id) && !empty($orderItem->redeemHistory)) ? $orderItem->redeemHistory : null,
                'redeem_discount' => $orderItem->redeem_discount,
                'group_discount' => $orderItem->group_discount,
                'available_discount' => $orderItem->available_discount,
            ];

            // All product options
            // Indexing by key
            $idxProductOptions = [];

            /** @var \App\Models\OrderOptionItem $optionItem */
            foreach ($orderItem->optionItems as $optionItem) {
                // Get meta data
                $orderOptionItemMeta = $optionItem->metas;

                // Convert meta data to array
                if (!empty($orderOptionItemMeta) && is_string($orderOptionItemMeta)) {
                    $tmpJson = json_decode($orderOptionItemMeta, true);

                    // Fastest way to check if a string is JSON in PHP
                    /** @link https://stackoverflow.com/a/15198925 */
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // JSON is valid
                        $orderOptionItemMeta = $tmpJson;
                    }
                }

                $productOptionItem = $optionItem->optionItem;
                $productOption = $productOptionItem->option;

                // Get product option item from meta data
                if (array_key_exists('option_item', $orderOptionItemMeta)) {
                    $productOptionItem = new \App\Models\OptionItem($orderOptionItemMeta['option_item']);
                }

                // Get product option from meta data
                if (array_key_exists('option', $orderOptionItemMeta)) {
                    $productOption = new \App\Models\Option($orderOptionItemMeta['option'][0]);
                }

                if (!array_key_exists($productOption->id, $idxProductOptions)) {
                    $idxProductOptions[$productOption->id] = [
                        'option_id' => $productOptionItem->opties_id,
                        'option' => $productOption->getFullInfo(),
                        'option_items' => [],
                    ];
                }

                $idxProductOptions[$productOption->id]['option_items'][] = [
                    'option_item_id' => $optionItem->optie_item_id,
                    'option_item' => array_merge($productOptionItem->getFullInfo(), [
                        'type' => $productOption->type,
                        'type_display' => $productOption->type_display,
                    ]),
                ];
            }

            $arrOrderItem['options'] = array_values($idxProductOptions);

            // Push a item to the order
            $items[] = $arrOrderItem;
        }

        return $items;
    }

    /**
     * @return array
     */
    public function getPaymentStatuses()
    {
        return [
            static::PAYMENT_STATUS_PENDING => trans('order.payment_statuses.' . static::PAYMENT_STATUS_PENDING),
            static::PAYMENT_STATUS_PAID => trans('order.payment_statuses.' . static::PAYMENT_STATUS_PAID),
            static::PAYMENT_STATUS_CANCELLED => trans('order.payment_statuses.' . static::PAYMENT_STATUS_CANCELLED),
            static::PAYMENT_STATUS_FAILED => trans('order.payment_statuses.' . static::PAYMENT_STATUS_FAILED),
            static::PAYMENT_STATUS_EXPIRED => trans('order.payment_statuses.' . static::PAYMENT_STATUS_EXPIRED),
        ];
    }

    /**
     * Mutator payment_method_display
     *
     * @return string
     */
    public function getPaymentMethodDisplayAttribute()
    {
        if ($this->payment_method === null) {
            return '';
        }

        if ($this->isBuyYourSelf() && SettingPayment::isOnlinePayment($this->payment_method)) {
            // When order type is table ordering or self ordering and payment method is online
            return trans('cart.success_online');
        }

        $methods = SettingPayment::getTypes();

        return array_get($methods, $this->payment_method, $this->payment_method);
    }

    /**
     * Mutator payment_status_display
     *
     * @return string
     */
    public function getPaymentStatusDisplayAttribute()
    {
        if ($this->payment_status === null) {
            return '';
        }

        $statuses = $this->getPaymentStatuses();

        return array_get($statuses, $this->payment_status, $this->payment_status);
    }

    /**
     * Mutator payment_status_display
     *
     * @return string
     */
    public function getStatusDisplayAttribute()
    {
        if ($this->status === null) {
            return '';
        }

        $statuses = $this->getPaymentStatuses();

        return array_get($statuses, $this->status, $this->status);
    }

    /**
     * Mutator payment_method_show
     *
     * @return string
     */
    public function getPaymentMethodShowAttribute()
    {
        $methods = '';
        $paymentMethods = [];
        $key = 'order.payment_method_show.';
        $splitChar = ', ';

        if(!empty($this->group_id) || ($this->type == static::TYPE_IN_HOUSE && empty($this->parent_id))) {
            if(!$this->groupOrders->isEmpty()) {
                foreach($this->groupOrders as $subOrder) {
                    if(isset($subOrder->payment_method)
                        && $subOrder->payment_method !== ''
                        && !is_null($subOrder->payment_method)
                        && !in_array($subOrder->payment_method, $paymentMethods)) {
                        $paymentMethods[] = $subOrder->payment_method;
                    }
                }
            }
        } else {
            if(isset($this->payment_method)
                && $this->payment_method !== ''
                && !is_null($this->payment_method)) {
                $paymentMethods[] = $this->payment_method;
            }
        }

        if(!empty($paymentMethods)) {
            $paymentMethodTmps = [];

            foreach($paymentMethods as $paymentMethod) {
                $paymentConvert = trans($key. config('common.payment_convert.'.$paymentMethod));

                if(!in_array($paymentConvert, $paymentMethodTmps)) {
                    $paymentMethodTmps[] = $paymentConvert;
                }
            }

            $methods = implode($splitChar, $paymentMethodTmps);
        }

        return $methods;
    }

    public function getPaymentMethodPrintAttribute()
    {
        $methods = '';
        $paymentMethods = [];
        $key = 'common.payment_method.';
        $splitChar = '/ ';

        if(!empty($this->group_id) || $this->type == static::TYPE_IN_HOUSE) {
            if(!$this->groupOrders->isEmpty()) {
                foreach($this->groupOrders as $subOrder) {
                    if(isset($subOrder->payment_method)
                        && $subOrder->payment_method !== ''
                        && !is_null($subOrder->payment_method)
                        && !in_array($subOrder->payment_method, $paymentMethods)) {
                        $paymentMethods[] = $subOrder->payment_method;
                    }
                }
            }
        } else {
            if(isset($this->payment_method)
                && $this->payment_method !== ''
                && !is_null($this->payment_method)) {
                $paymentMethods[] = $this->payment_method;
            }
        }

        if(!empty($paymentMethods)) {
            $paymentMethodTmps = [];

            foreach($paymentMethods as $paymentMethod) {
                $paymentConvert = trans($key. config('common.payment_convert.'.$paymentMethod));

                if(!in_array($paymentConvert, $paymentMethodTmps)) {
                    $paymentMethodTmps[] = $paymentConvert;
                }
            }

            $methods = implode($splitChar, $paymentMethodTmps);
        }

        return $methods;
    }

    /**
     * Mutator gereed
     *
     * @return string
     */
    public function getGereedAttribute()
    {
        return $this->date_time;
    }

    public function getTotalProductItemsAttribute()
    {
        $total = 0;

        if(!empty($this->group_id) || $this->type == static::TYPE_IN_HOUSE) {
            if(!$this->groupOrders->isEmpty()) {
                foreach($this->groupOrders as $subOrder) {
                    if(!empty($subOrder->orderItems)) {
                        $total = $total + $subOrder->orderItems->sum('total_number');
                    }
                }
            }
        } else {
            if(!empty($this->orderItems)) {
                $total = $this->orderItems->sum('total_number');
            }
        }

        return $total;
    }

    /**
     * Mutator payment_method_show
     *
     * @return string
     */
    public function getDailyIdAttribute()
    {
        return $this->code;
    }

    public function getTypeConvertAttribute()
    {
        $type = $this->type;

        if(!empty($this->group)) {
            $type = $this->group->type;
        }

        return $type;
    }

    /**
     * Get all orders of the user in a workspace
     *
     * @param $userId
     * @param $workspaceId
     * @param null $inStatuses
     * @param $getAllOrder
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getOrderByUser($userId, $workspaceId, $inStatuses = null, $getAllOrder = false) {
        $memoryCacheService = \App\Services\MemoryCacheService::getInstance();
        $key = json_encode(compact('userId', 'workspaceId', 'inStatuses', 'getAllOrder'));

        if($memoryCacheService->get('getOrderByUser', $key, null, true) === null) {
            $model = static::where('user_id', $userId);

            if (!$getAllOrder) {
                $model = $model->where('workspace_id', $workspaceId);
            }

            $model = $model->where(function ($query) {
                // Individual when group_id is null
                // Group order when parent_id is null
                $query->whereNull('group_id')
                    ->orWhereNotNull('parent_id');
            })
            ->orderBy('id', 'desc');

            // Filter by order status
            if (!empty($inStatuses)) {
                $model->whereIn('orders.status', $inStatuses);
            }

            $model =  $model->with(
                'workspace',
                'user',
                'group'
            )->get();
            $memoryCacheService->set('getOrderByUser', $key, $model, true);
        } else {
            $model = $memoryCacheService->get('getOrderByUser', $key, null);
        }

        return $model;
    }

    public function getTotalOnInvoiceAttribute()
    {
        $totalOnInvoice = 0;

        if(!empty($this->group_id) || $this->type == static::TYPE_IN_HOUSE) {
            if(!$this->groupOrders->isEmpty()) {
                foreach($this->groupOrders as $subOrder) {
                    if($subOrder->payment_method == \App\Models\SettingPayment::TYPE_FACTUUR) {
                        $totalOnInvoice = $totalOnInvoice + $subOrder->total_price;
                    }
                }
            }
        } else {
            if($this->payment_method == \App\Models\SettingPayment::TYPE_FACTUUR) {
                $totalOnInvoice = $this->total_price;
            }
        }

        return $totalOnInvoice;
    }

    /**
     * Mutator parent_code
     *
     * @return string
     */
    public function getParentCodeAttribute()
    {
        $code = null;

        if ($this->isTableOrdering() || !empty($this->group_id)) {
            if (empty($this->parent_id)) {
                $code = $this->code;
            } else {
                if (!empty($this->parentOrder)) {
                    $code = $this->parentOrder->code;
                }
            }
        } else {
            $code = $this->code;
        }

        return $code;
    }

    public function getCalculateTotalPriceAttribute()
    {
        return $this->calculateValueOfAttr($this, 'total_price');
    }

    public function getCalculateTotalPaidAttribute()
    {
        return $this->calculateValueOfAttr($this, 'total_paid');
    }

    public function getCalculateSubTotalAttribute()
    {
        return $this->calculateValueOfAttr($this, 'subtotal');
    }

    public function getCalculateCouponDiscountAttribute()
    {
        return $this->calculateValueOfAttr($this, 'coupon_discount');
    }

    public function getCalculateRedeemDiscountAttribute()
    {
        return $this->calculateValueOfAttr($this, 'redeem_discount');
    }

    public function getCalculateShipPriceAttribute()
    {
        return $this->calculateValueOfAttr($this, 'ship_price');
    }

    public function getCalculateServiceCostAttribute()
    {
        return $this->calculateValueOfAttr($this, 'service_cost');
    }

    public function getCalculateGroupDiscountAttribute()
    {
        return $this->calculateValueOfAttr($this, 'group_discount');
    }

    private function calculateValueOfAttr($model, $attribute) {
        $total = 0;

        if(!empty($model->group_id) || ($model->type == static::TYPE_IN_HOUSE && empty($model->parent_id))) {
            if(!$model->groupOrders->isEmpty()) {
                foreach($model->groupOrders as $subOrder) {
                    $total = $total + (float)$subOrder->$attribute;
                }
            }
        } else {
            $total = (float)$model->$attribute;
        }

        return $total;
    }

    public function getCheckPrintedStickerMultiAttribute()
    {
        $printed = true;

        if(!empty($this->group_id) || $this->type == static::TYPE_IN_HOUSE) {
            if(!$this->groupOrders->isEmpty()) {
                foreach($this->groupOrders as $subOrder) {
                    if(empty($subOrder->printed_sticker_multi)) {
                        $printed = false;
                    }
                }
            }
        } else {
            $printed = $this->printed_sticker_multi;
        }

        return $printed;
    }

    public function isCutOffTimeAttribute() {
        $now = date('Y-m-d H:i:s');
        return strtotime($now) >= strtotime('+200 seconds', strtotime($this->cut_off_time));
    }

    public function getCutOffTimeAttribute()
    {
        $time = null;

        if(!empty($this->group)) {
            $timezone = !empty($this->timezone) ? $this->timezone : 'UTC';
            $dateTimeByTimeZone = Helper::convertDateTimeToTimezone($this->date_time, $timezone);
            $tmpTime = date('Y-m-d', strtotime($dateTimeByTimeZone)) . ' ' . $this->group->close_time;
            $time = Helper::convertDateTimeToUTC($tmpTime, $timezone);
        }

        return $time;
    }

    public function getTotalOrderAttribute()
    {
        $total = 1;

        if((!empty($this->group_id) || $this->type == static::TYPE_IN_HOUSE) && !$this->groupOrders->isEmpty()) {
            $total = $this->groupOrders->count();
        }

        return $total;
    }

    /**
     * Update parent order values
     *
     * @param int $parentId Order parent_id
     * @return int Number of record which updated successfully
     */
    private function updateParent(int $parentId)
    {
        $sqlChildOrders = static::where('parent_id', $parentId)
            ->whereIn('status', OrderHelper::getValidOrderStatus());
        $totalValidOrders = (clone $sqlChildOrders)->count();
        $totalPaidOrders = (clone $sqlChildOrders)->whereIn('status', [OrderHelper::PAYMENT_STATUS_PAID])->count();
        $parentStatus = ($totalValidOrders === $totalPaidOrders) ? OrderHelper::PAYMENT_STATUS_PAID : OrderHelper::PAYMENT_STATUS_PENDING;

        $arrQuery = [
            // Update status of the parent order
            'status' => $parentStatus,
        ];

        $calColumns = [
            'subtotal',
            'total_price',
            'total_paid',
            'ship_price',
            'service_cost'
        ];

        foreach ($calColumns as $col) {
            // Query for SUM sub-orders
            $arrQuery[$col] = \DB::raw("(SELECT sum_{$col} 
                FROM (
                    SELECT SUM({$col}) AS sum_{$col} 
                    FROM orders 
                    WHERE parent_id = {$parentId}
                ) AS tmp_{$col})");
        }

        $result = \DB::table('orders')
            ->where('id', $parentId)
            ->update($arrQuery);

        return $result;
    }

    public function getBesteIdAttribute()
    {
        return $this->created_at;
    }

    /**
     * Get Coupon used from order status
     *
     * @return int[]
     */
    public static function getCouponUsedFromOrderStatus()
    {
        return [
            static::PAYMENT_STATUS_PENDING,
            static::PAYMENT_STATUS_PAID,
        ];
    }

    /**
     * Mutator type_display
     *
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        if ($this->type === null) {
            return '';
        }

        $types = OrderHelper::getTypes();

        return array_get($types, $this->type, $this->type);
    }

    /**
     * Mutator order_prefix
     *
     * @return string
     */
    public function getCodeWithPrefixAttribute()
    {
        $code = "#";

        if ($this->type === OrderHelper::TYPE_IN_HOUSE) {
            $code .= "T" . $this->parent_code;
        } else if (!empty($this->group_id)) {
            $code .= "G" . $this->parent_code;
            if (!empty($this->extra_code)) {
                $code .= '-' . $this->extra_code;
            }
        } else {
            $code .= $this->code;
        }

        return $code;
    }

    /**
     * Check the order is
     *
     * @param Order $order
     * @return bool
     */
    public function isBuyYourSelf(Order $order = null)
    {
        if ($order === null) {
            $order = $this;
        }

        return in_array($order->type, OrderHelper::buyYourSelfTypes());
    }

    /**
     * Check the order is Table Ordering
     *
     * @param Order $order
     * @return bool
     */
    public function isTableOrdering(Order $order = null)
    {
        if ($order === null) {
            $order = $this;
        }

        return $order->type === \App\Helpers\OrderHelper::TYPE_IN_HOUSE;
    }

    /**
     * Check the order is Self Ordering
     *
     * @param Order $order
     * @return bool
     */
    public function isSelfOrdering(Order $order = null)
    {
        if ($order === null) {
            $order = $this;
        }

        return $order->type === \App\Helpers\OrderHelper::TYPE_SELF_ORDERING;
    }

    public function isSelfService()
    {
        if ($this->type === \App\Helpers\OrderHelper::TYPE_IN_HOUSE) {
            $workspace = $this->workspace;

            if (empty($workspace)) {
                return false;
            }
            $workspaceExtra = $workspace->workspaceExtras()
                ->where('active', 1)
                ->where('type', \App\Models\WorkspaceExtra::SELF_SERVICE)
                ->first();

            if (!empty($workspaceExtra)) {
                return true;
            }

        }

        return false;
    }

    /**
     * Check the Table Ordering if has last person in table ordering
     *
     * @return bool
     */
    public function hasLastPersonInTableOrdering()
    {
        $flag = false;

        if ($this->isTableOrdering()) {
            $query = static::where('table_last_person', true);

            if (!empty($this->parent_id)) {
                $flag = $query->paid()->where('parent_id', $this->parent_id)->count() > 0;
            } else {
                $flag = $query->paid()->where('parent_id', $this->id)->count() > 0;
            }
        }

        return $flag;
    }

    /**
     * Get last paid child order
     *
     * @return Order|null
     */
    public function getLastPaidChildOrder()
    {
        return static::where('parent_id', $this->id)
            ->where('status', static::PAYMENT_STATUS_PAID)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Scope a query to filter success orders
     *
     * @param $query
     * @return mixed
     */
    public function scopeSuccessOrder($query)
    {
        return $query->whereIn('status', [self::PAYMENT_STATUS_PAID])
            ->orWhere(function ($query1) {
                $query1->whereIn('status', [self::PAYMENT_STATUS_PENDING, self::PAYMENT_STATUS_PAID])
                    ->whereIn('payment_method', [SettingPayment::TYPE_CASH, SettingPayment::TYPE_FACTUUR]);
            });
    }

    public function scopePaid($query)
    {
        return $query->where(function($query) {
            $query->where(function($query){
                $query->whereNotNull('mollie_id')->where('status', self::PAYMENT_STATUS_PAID);
            })
            ->orWhere(function($query) {
                $query->whereNull('mollie_id');
            });
        });
    }

    public function tableOrderingLastPerson()
    {
        if(!empty($this->table_last_person)) {
            return $this;
        }

        $parentOrder = $this;

        if(!empty($this->parent_id)) {
            $parentOrder = $this->parentOrder;
        }

        return $parentOrder->groupOrders()->where('table_last_person', true)->first();
    }

    public function disabledCashManual() {
        $disabled = false;

        if($this->isBuyYourSelf() && $this->isTableOrdering()) {
            if($this->payment_method == \App\Models\SettingPayment::TYPE_MOLLIE
                || $this->status == \App\Models\Order::PAYMENT_STATUS_PAID) {
                $disabled = true;

                if(!$this->groupOrders->isEmpty()) {
                    $cashOrders = $this->groupOrders()
                        ->where('payment_method', \App\Models\SettingPayment::TYPE_CASH)
                        ->where('status', '!=', \App\Models\Order::PAYMENT_STATUS_PAID)
                        ->count();

                    if(!empty($cashOrders)) {
                        $disabled = false;
                    }
                }
            }
        }

        return $disabled;
    }
}
