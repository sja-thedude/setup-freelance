<?php

namespace App\Helpers;

class OrderHelper extends Order
{
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
     * Order status
     */
    const PAYMENT_STATUS_UNKOWN = 0;
    const PAYMENT_STATUS_PENDING = 1;
    const PAYMENT_STATUS_PAID = 2;
    const PAYMENT_STATUS_CANCELLED = 3;
    const PAYMENT_STATUS_FAILED = 4;
    const PAYMENT_STATUS_EXPIRED = 5;

    /**
     * @param int|null $value
     * @return array|string
     */
    public static function getTypes(int $value = null)
    {
        $options = array(
            static::TYPE_TAKEOUT => trans('order.types.takeout'),
            static::TYPE_DELIVERY => trans('order.types.delivery'),
            static::TYPE_IN_HOUSE => trans('order.types.in_house'),
            static::TYPE_SELF_ORDERING => trans('order.types.self_ordering'),
        );
        return static::enum($value, $options);
    }

    /**
     * static enums
     * @access static
     *
     * @param mixed $value
     * @param array $options
     * @param string $default
     * @return string|array
     */
    public static function enum($value, $options, $default = '') {
        if ($value !== null) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }
            return $default;
        }
        return $options;
    }

    /**
     * All types of buy your self
     *
     * @return array
     */
    public static function buyYourSelfTypes()
    {
        return [
            static::TYPE_IN_HOUSE,
            static::TYPE_SELF_ORDERING,
        ];
    }

    /**
     * @param int|null $value
     * @return array|string
     */
    public static function getPaymentMethods(int $value = null)
    {
        $options = array(
            static::PAYMENT_METHOD_CASH => trans('order.cash'),
            static::PAYMENT_METHOD_PAID_ONLINE => trans('order.paid_online'),
            static::PAYMENT_METHOD_FOR_INVOICE => trans('order.for_invoice'),
        );
        return static::enum($value, $options);
    }

    /**
     * Get all valid order status
     *
     * @return int[]
     */
    public static function getValidOrderStatus()
    {
        return [
            static::PAYMENT_STATUS_PENDING,
            static::PAYMENT_STATUS_PAID,
        ];
    }
}