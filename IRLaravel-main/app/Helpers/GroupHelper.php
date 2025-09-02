<?php

namespace App\Helpers;

use App\Models\Cart;
use App\Models\Group;
use App\Models\Product;
use App\Models\Order as OrderModel;

class GroupHelper
{
    /**
     * Calculate group discount
     *
     * @param $cart
     * @param $group
     */
    public static function calculateGroupDiscount($cart)
    {
        $group = $cart->group;
        if (isset($group->discount_type)) {
            switch ($group->discount_type) {
                case Group::NO_DISCOUNT:
                    return 0;

                case Group::FIXED_AMOUNT:
                    return $group->discount;

                case Group::PERCENTAGE:
                    $limitProducts = self::getLimitProducts($group);
                    $productPrices = [];
                    if ($cart instanceof Cart) {
                        $productPrices = Helper::calculatePriceFromCart($cart, $limitProducts);
                    } else if ($cart instanceof OrderModel) {
                        $productPrices = Helper::calculateProductPriceFromOrder($cart, $limitProducts);
                    }

                    return self::calculateGroupDiscountValue($group, $productPrices);
            }
        }

        return 0;
    }

    /**
     * Calculate the group discount value when apply the coupon
     *
     * @param $coupon
     * @param $productPrices
     * @return float|int|mixed
     */
    public static function calculateGroupDiscountValue($group, $productPrices)
    {
        if ($group) {
            if ($group->discount_type == Group::FIXED_AMOUNT) {
                return $group->discount;
            } elseif ($group->discount_type == Group::PERCENTAGE) {
                $applicablePrice = Helper::calculateApplicablePrice($productPrices);
                return ($group->percentage * $applicablePrice)/100;
            }
        }

        return 0;
    }

    public static function getLimitProducts($group)
    {
        $limitProducts = [];

        if (isset($group->is_product_limit) && $group->is_product_limit == true) {
            $limitProducts = $group->products->pluck('id')->toArray();

            // Apply for categories
            $limitProducts = Helper::getCategoryIds($group, $limitProducts);
        }

        if (empty($group->is_product_limit)) {
            if (isset($group->type) && $group->type == Group::TYPE_TAKEOUT) {
                $limitProducts = Product::whereActive(1)
                    ->where('workspace_id', $group->workspace_id)
                    ->pluck('id')->toArray();
            }

            if (isset($group->type) && $group->type == Group::TYPE_DELIVERY) {
                $limitProducts = Product::select('products.*')
                    ->where('products.workspace_id', $group->workspace_id)
                    ->where(['products.active' => 1])
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->where(['categories.available_delivery' => 1])->pluck('id')->toArray();
            }
        }

        return $limitProducts;
    }

    /**
     * Check if the group discount is applied
     *
     * @param $cart
     * @return bool
     */
    public static function isApplyGroupDiscount($cart)
    {
        if (!$cart->redeem_history_id
            && !$cart->coupon && isset($cart->group)
            && in_array($cart->group->discount_type, [Group::FIXED_AMOUNT, Group::PERCENTAGE])) {
            if ($cart->group->discount_type == Group::FIXED_AMOUNT && $cart->group->discount > 0) {
                return true;
            }

            if ($cart->group->discount_type == Group::PERCENTAGE && $cart->group->percentage > 0) {
                return true;
            }

            return false;
        }

        return false;
    }

    public static function calculateTotalGroupDiscount($cart)
    {
        if (isset($cart->group)) {
            $limitProducts = self::getLimitProducts($cart->group);
            $productPrices = [];
            if ($cart instanceof Cart) {
                $productPrices = Helper::calculatePriceFromCart($cart, $limitProducts);
            } else if ($cart instanceof OrderModel) {
                $productPrices = Helper::calculateProductPriceFromOrder($cart, $limitProducts);
            }

            $discountValue = self::calculateGroupDiscount($cart);
            return Helper::getProductDiscountValues($productPrices['vatProducts'], $productPrices['unitPricesProduct'], $discountValue);
        }

        return [];
    }

    /**
     * Check is apply group discount from order items
     */
    public static function isGroupDiscountFromOrderItems($items, $groupId)
    {
        if (empty($items) || empty($groupId)) {
            return false;
        }

        $isGroupDiscount = true;

        foreach ($items as $arrItem) {
            if (!empty($arrItem['coupon_id']) || !empty($arrItem['redeem_history_id'])){
                $isGroupDiscount = false;
            }
        }

        return $isGroupDiscount;
    }
}
