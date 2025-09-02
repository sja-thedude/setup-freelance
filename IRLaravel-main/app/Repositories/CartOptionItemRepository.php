<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Models\CartOptionItem;

class CartOptionItemRepository extends AppBaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return CartOptionItem::class;
    }

    public function getIdenticalCartItem($cartItem, $cartOptionItems)
    {
        $productId = $cartItem['product_id'];
        $cartId = $cartItem['cart_id'];
        $cartItems = [];
        $i = 0;

        if (count($cartOptionItems) == 0) {
            $identicalCartItem = CartItem::withcount('cartOptionItems')->where('product_id', $productId)
                                ->where('cart_id', $cartId)->get()->where('cart_option_items_count', 0)->first();

            if (!empty($identicalCartItem)) {
                return $identicalCartItem;
            }

            return false;
        }

        foreach ($cartOptionItems as $cartOptionItem) {
            $cartOptionItem = \GuzzleHttp\json_decode($cartOptionItem);
            $cartItemIds = CartOptionItem::where('product_id', $productId)
                ->where('optie_id', $cartOptionItem->opties_id)
                ->where('optie_item_id', $cartOptionItem->id)->get()->pluck('cart_item_id')->toArray();
            if ($i == 0) {
                $cartItems = $cartItemIds;
            } else {
                $cartItems = array_intersect($cartItems, $cartItemIds);
            }
            $i++;
        }

        if (empty($cartItems)) {
            return false;
        }

        $oldCartOptionItems = CartOptionItem::whereIn('cart_item_id', $cartItems)->get()->groupBy('cart_item_id');
        foreach ($oldCartOptionItems as $cartItemId => $oldCartOptionItem) {
            if ($oldCartOptionItem->count() == count($cartOptionItems)) {
                $cartItem = CartItem::where('cart_id', $cartId)->where('id', $cartItemId)->first();
                if(!empty($cartItem)) 
                    return $cartItem;
            }
        }

        return false;
    }
}
