<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Group;
use App\Models\Option;
use App\Models\OptionItem;
use Carbon\Carbon;

class CartRepository extends AppBaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Cart::class;
    }

    /**
     * @param $request
     * @param $order
     * @return array
     */
    public function convertOrderToArray($request, $order) {
        $orderData = [
            'workspace_id' => $order->workspace_id,
            'user_id' => $order->user_id,
//            'setting_payment_id' => $order->setting_payment_id,
//            'open_timeslot_id' => $order->open_timeslot_id,
//            'setting_timeslot_detail_id' => $order->setting_timeslot_detail_id,
            'group_id' => $order->group_id,
//            'address' => !empty($request->address) ? $request->address : $order->address,
//            'address_type' => !empty($request->address_type) ? $request->address_type : $order->address_type,
//            'lat' => !empty($request->lat) ? $request->lat : $order->lat,
//            'long' => !empty($request->long) ? $request->long : $order->lng,
            'type' => $order->type,
//            'note' => $order->note
        ];
        
        //Set type
        if ($order->group_id) {
            $newGroup = Group::where('id', $order->group_id)->first();
            $orderData['type'] = $newGroup->type;
        }
        
        if (empty($order->group_id) && $order->type) {
            $orderData['address'] = !empty($request->address) ? $request->address : $order->address;
            $orderData['address_type'] = isset($request->address_type) ? $request->address_type : $order->address_type;
            $orderData['lat'] = !empty($request->lat) ? $request->lat : $order->lat;
            $orderData['long'] = !empty($request->long) ? $request->long : $order->lng;
        }
        
        return $orderData;
    }

    /**
     * @param $orderItem
     * @param $cart
     * @return mixed
     */
    public function convertOrderItemsToArray($orderItem, $cart) {
        $items['workspace_id'] = $orderItem->workspace_id;
        $items['cart_id'] = $cart->id;
        $items['category_id'] = $orderItem->category_id;
        $items['product_id'] = $orderItem->product_id;
        $items['type'] = $cart->type;
        $items['total_number'] = $orderItem->total_number;
        $items['created_at'] = Carbon::now();
        $items['updated_at'] = Carbon::now();
        
        return $items;
    }

    /**
     * @param $optionItems
     * @param $cartItem
     * @param null $countOptionNotAvailable
     * @return array
     */
    public function convertOrderItemsOptionToArray($optionItems, $cartItem, &$countOptionNotAvailable = null) {
        $optionItemsArray = [];
        
        //Check options
        $optiesItemId = !empty($optionItems) ? array_unique($optionItems->pluck('optie_item_id')->toArray()): [];
        $productOptions = !empty($optionItems) ? array_unique($optionItems->pluck('optie_id')->toArray()) : [];
        $opties = Option::whereIn('id', $productOptions)->pluck('id')->toArray();
        $countOptions = OptionItem::whereIn('opties_id', $opties)
            ->where('available', 1)
            ->pluck('id')->toArray();
        
        $countOptionNotAvailable = OptionItem::whereIn('id', $optiesItemId)->withTrashed()->where(function($query) {
            $query->where('available', 0)->orwhereNotNull('deleted_at');
        })->count();
        
        foreach ($optionItems as $key => $option) {
            if (in_array($option->optie_item_id, $countOptions)) {
                $optionItemsArray[$key]['workspace_id'] = $cartItem->workspace_id;
                $optionItemsArray[$key]['cart_item_id'] = $cartItem->id;
                $optionItemsArray[$key]['product_id'] = $cartItem->product_id;
                $optionItemsArray[$key]['optie_id'] = $option->optie_id;
                $optionItemsArray[$key]['optie_item_id'] = $option->optie_item_id;
                $optionItemsArray[$key]['created_at'] = Carbon::now();
                $optionItemsArray[$key]['updated_at'] = Carbon::now();
            }
        }
        
        return $optionItemsArray;
    }
}
