@php

// NOTE IF YOU CHANGE ANYTHING HERE YOU SHOULD ALSO ADJUST sticker.blade.php
// I know it's better to do this in blade language but because every space counts for printing we do it in php.
// Because it's still more of a 'view' thing i did it in the view files and not in the controllers

// @todo move this function to a helper class
if(!function_exists('replaceUnsupportedCharacters')) {
    function replaceUnsupportedCharacters($text) {
        $text = str_replace('ê', 'e', $text);
        $text = str_replace('è', 'e', $text);
        $text = str_replace('é', 'e', $text);
        $text = str_replace('ë', 'e', $text);
        $text = str_replace('ê', 'e', $text);
        $text = str_replace('ô', 'o', $text);
        $text = str_replace('û', 'u', $text);
        $text = str_replace('î', 'i', $text);
        $text = str_replace('ñ', 'n', $text);

        return $text;
    }
}

$currency = '[/euro]';
$counter = 0;
$isIdentical = true;

if(!isset($productStickers)) {
    $isIdentical = false;
    $productStickers = $order->print_products;
}

if(!empty($productStickers)) {
    $orderHasNotes = [];

    foreach($productStickers as $printProduct) {
        if(!empty($printProduct['category']) && !empty($printProduct['products'])) {
            $subOrder = $printProduct['order'];
            $category = $printProduct['category'];
            $products = $printProduct['products'];
            $order = \App\Helpers\Order::convertOrderItem(!empty($subOrder->parent_id) ? $subOrder->parentOrder : $subOrder);
            $printProducts = \App\Facades\Order::sortAgainProduct($order, $products);
            $timezone = $order->timezone;

            foreach($printProducts as $product) {
                $productItem = $product['order_item'];
                $productItemOrder = !empty($productItem) ? $productItem->order : null;

                $productOptionItems = !empty($product['option_items']) ? $product['option_items'] : [];
                $metaOrderItem = json_decode($productItem->metas, true);

                for($i = 1; $i <= $product['total_number']; $i++) {
                    if(!empty($subOrder->note) && !in_array($subOrder->id, $orderHasNotes)) {
                        if($counter > 0) {
                            echo '[pagebreak/]';
                        }

                        $counter++;

                        if((!empty($subOrder->group) && !empty($subOrder->group->name) && !$order->is_test_account) || (!empty($order->workspace) && !empty($order->workspace->name))) {
                            if(!empty($order->workspace) && !empty($order->workspace->name)) {
                                $workspaceName = replaceUnsupportedCharacters($order->workspace->name);

                                if(!empty(trim($workspaceName))) {
                                    echo '[textline]';
                                    echo '[b]' . $workspaceName . '[/b]';
                                    echo '[/textline]';
                                }
                            }

                            if(!empty($subOrder->group) && !empty($subOrder->group->name) && !$order->is_test_account) {
                                $gName = replaceUnsupportedCharacters($subOrder->group->name);

                                if(!empty(trim($gName))) {
                                    echo '[textline]';
                                    echo '[b]' . $gName . '[/b]';
                                    echo '[/textline]';
                                }
                            }
                        }

                        echo '[textline]';

                        // CUSTOMER NAME / TEST
                        if($order->is_test_account) {
                            $name = 'ADMIN ' . app('translator')->getFromJson('order.test');
                        }
                        else {
                            if(!empty($productItemOrder)) {
                                $name = (!empty($productItemOrder->user) ? replaceUnsupportedCharacters($productItemOrder->user->name) : '');
                            }
                            else {
                                $name = (!empty($subOrder->user) ? replaceUnsupportedCharacters($subOrder->user->name) : '');
                            }
                        }

                        // Total chars
                        $maxChars = 46;
                        $leftCharsCount = $maxChars - strlen($name);
                        $time = date('H:i', strtotime(Helper::convertDateTimeToTimezone($order->gereed, !empty($timezone) ? $timezone : 'UTC')));
                        $leftCharsCount -= strlen($time);

                        // CUSTOMER NAME / TEST
                        echo replaceUnsupportedCharacters($name);

                        // INSERT SPACES
                        echo str_repeat('[space/]', $leftCharsCount > 0 ? $leftCharsCount : 0);

                        // PRICE AND TIME
                        echo ' [b]' . $time . '[/b]';

                        echo '[/textline]';

                        // LINE
                        echo '[textline][/seperator][/textline]';

                        // IMPORTANT
                        echo '[textline]'.str_repeat('!', 48).'[/textline]';

                        // COMMENT
                        echo '[textline][b]';
                        $orderHasNotes[] = $subOrder->id;
                        echo replaceUnsupportedCharacters($subOrder->note);
                        echo '[/b][/textline]';

                        // IMPORTANT
                        echo '[textline]'.str_repeat('!', 48).'[/textline]';
                    }

                    if($counter > 0) {
                        echo '[pagebreak/]';
                    }

                    $counter++;
                    $optionItemConvert = [];

                    if(!empty($productOptionItems)) {
                        foreach($productOptionItems as $optionItem) {
                            if(!empty($optionItem['option_item']['opties_id'])) {
                                $optionItemId = $optionItem['option_item']['opties_id'];

                                if(!empty($optionItemConvert[$optionItemId])) {
                                    array_push($optionItemConvert[$optionItemId], $optionItem);
                                } else {
                                    $optionItemConvert[$optionItemId] = [$optionItem];
                                }
                            }
                        }
                    }

                    // @TODO GROUP ORDER WE NEED TO SHOW THE GROUP NAME
                    if((!empty($subOrder->group) && !empty($subOrder->group->name) && !$order->is_test_account) || (!empty($order->workspace) && !empty($order->workspace->name))) {
                        if(!empty($order->workspace) && !empty($order->workspace->name)) {
                            $workspaceName = replaceUnsupportedCharacters($order->workspace->name);

                            if(!empty(trim($workspaceName))) {
                                echo '[textline]';
                                echo '[b]' . $workspaceName . '[/b]';
                                echo '[/textline]';
                            }
                        }

                        if(!empty($subOrder->group) && !empty($subOrder->group->name) && !$order->is_test_account) {
                            $gName = replaceUnsupportedCharacters($subOrder->group->name);

                            if(!empty(trim($gName))) {
                                echo '[textline]';
                                echo '[b]' . $gName . '[/b]';
                                echo '[/textline]';
                            }
                        }
                    }

                    echo '[textline]';

                    // CUSTOMER NAME / TEST
                    if($order->is_test_account) {
                        $name = 'ADMIN ' . app('translator')->getFromJson('order.test');
                    }
                    else {
                        if(!empty($productItemOrder)) {
                            $name = (!empty($productItemOrder->user) ? replaceUnsupportedCharacters($productItemOrder->user->name) : '');
                        }
                        else {
                            $name = (!empty($subOrder->user) ? replaceUnsupportedCharacters($subOrder->user->name) : '');
                        }
                    }

                    // Total chars
                    $maxChars = 52;
                    $leftCharsCount = $maxChars - strlen($name);

                    $price = $currency.number_format((float)(!empty($productItem->subtotal / $productItem->total_number) ? $productItem->subtotal / $productItem->total_number : 0), 2, '.', '');
                    $time = date('H:i', strtotime(Helper::convertDateTimeToTimezone($order->gereed, !empty($timezone) ? $timezone : 'UTC')));

                    $leftCharsCount -= strlen($price);
                    $leftCharsCount -= strlen($time);

                    // CUSTOMER NAME / TEST
                    echo replaceUnsupportedCharacters($name);

                    // INSERT SPACES
                    echo str_repeat('[space/]', $leftCharsCount > 0 ? $leftCharsCount : 0);

                    // PRICE AND TIME
                    echo $price . ' [b]' . $time . '[/b]';

                    echo '[/textline]';

                    if(!empty($order->group_id) && !empty($subOrder->note) && !in_array($subOrder->id, $orderHasNotes)) {
                        $orderHasNotes[] = $subOrder->id;
                        echo replaceUnsupportedCharacters($subOrder->note);
                    }

                    echo '[textline][/seperator][/textline]';

                    // PRODUCT NAME AND NUMBER
                    $metaOrderItem = json_decode($productItem->metas, true);

                    $productname = !empty($productItem->product) ? $productItem->product->name : (!empty($metaOrderItem['product']['name']) ? $metaOrderItem['product']['name'] : '');
                    $transformType = !empty($order->group_id) ? $order->group->type : $order->type;
                    $transformLabel = !empty($transformType) ? 'L' : 'A';
                    $productnumber = $transformLabel . '#' . $order->daily_id_display . (!empty($order->group_id) && !empty($productItemOrder->extra_code) ? '-' . $productItemOrder->extra_code : '');

                    // Total chars (with bold text)
                    $maxChars = 48;
                    $leftCharsCount = $maxChars - (strlen($productname));
                    $leftCharsCount -= strlen($productnumber);

                    echo '[textline]';

                    // PRODUCT NAME
                    echo '[b]' . replaceUnsupportedCharacters($productname) . '[/b]';

                    // INSERT SPACES
                    echo str_repeat('[space/]', $leftCharsCount > 0 ? $leftCharsCount : 0);

                    // NUMBER
                    echo '[b]' . $productnumber . '[/b]';

                    echo '[/textline]';

                    if(!empty($optionItemConvert)) {
                        foreach($optionItemConvert as $optionItems) {
                            echo '[br/]';

                            foreach($optionItems as $key => $optionItem) {
                                $zonder = !empty($optionItem['option'][0]['is_ingredient_deletion']);
                                $optionItemName = !empty($optionItem['option_item']['name']) ? $optionItem['option_item']['name'] : '';

                                if(!empty($key)) {
                                    if(!empty($zonder)) {
                                        echo ' / [b]Z[/b] ' . replaceUnsupportedCharacters($optionItemName);
                                    }
                                    else {
                                        echo ' / ' . replaceUnsupportedCharacters($optionItemName);
                                    }
                                }
                                else {
                                    if(!empty($zonder)) {
                                        echo '- [b]Z[/b] ' . replaceUnsupportedCharacters($optionItemName);
                                    }
                                    else {
                                        echo '- ' . replaceUnsupportedCharacters($optionItemName);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

echo '[feedfullcut/]';

@endphp