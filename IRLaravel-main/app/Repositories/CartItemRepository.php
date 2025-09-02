<?php

namespace App\Repositories;

use App\Models\CartItem;

class CartItemRepository extends AppBaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return CartItem::class;
    }

    public function handleIfIdentical(&$newCartItem, $identicalCartItemWithoutLogin, &$newAddCartItem, $randomId)
    {
        if (empty($identicalCartItemWithoutLogin)) {
            return false;
        }

        $newTotalNumber = $newCartItem[$randomId]['total_number'];
        $newCartItem = [];
        $identicalCartItemId = $identicalCartItemWithoutLogin[0];
        foreach ($newAddCartItem as $key => $oldCartItem) {
            if ($oldCartItem['id'] == $identicalCartItemId) {
                $newAddCartItem[$key]['total_number'] = $newAddCartItem[$key]['total_number'] + $newTotalNumber;
            }
        }

        return $newAddCartItem;
    }
}
