<?php

namespace App\Helpers;

use App\Models\Contact;
use App\Models\OrderItem;
use App\Models\SettingPreference;
use App\Facades\Helper;
use App\Models\Order as OrderModel;
use App\Models\SettingPrint;
use App\Models\Category;
use App\Models\PrinterJob;
use App\Models\ProductTranslation;
use Illuminate\Support\Facades\Log;

class Order
{
    public function __construct(){}

    /**
     * Convert order item
     *
     * @param OrderModel $order
     * @return OrderModel
     */
    public static function convertOrderItem(OrderModel $order)
    {
        $order->daily_id_display = $order->code;
        $order->client_name = !empty($order->user) ? $order->user->name : '';
        $order->phone_display = !empty($order->user) ? $order->user->gsm : '';
        $order->email_display = !empty($order->user) ? $order->user->email : '';
        $order->address_show = !empty($order->address) ? $order->address : '';
        $order->transform_type = trans('order.transform_types.' . $order->type_convert);

        if ($order->isTableOrdering()) {
            $contact = $order->contact ?? new Contact();
            $order->daily_id_display = 'T' . $order->parent_code;
            $order->client_name = '<span style="text-transform: uppercase;">' . trans('order.label_table_number') . ' ' . ($order->table_number ?? '?') . '</span>';
            $order->phone_display = $contact->phone ?? '';
            $order->email_display = $contact->email ?? '';
            $order->transform_type = trans('order.types.in_house');
        } elseif ($order->isSelfOrdering()) {
            $contact = $order->contact ?? new Contact();
            $order->client_name = '<span style="font-weight: bold;">' . ($contact->name ?? '?') . '</span>';
            $order->phone_display = '';
            $order->email_display = $contact->fake_email ? '' : $contact->email;
            $order->transform_type = trans('order.types.self_ordering');
        } elseif (!empty($order->group_id)) {
            // BEGIN - CONVERT WITH TYPE ORDER GROUP
            $order->daily_id_display = 'G' . $order->parent_code;
            $order->client_name = !empty($order->group) ? $order->group->name : '';
            $order->phone_display = !empty($order->group) ? $order->group->contact_gsm : '';
            $order->email_display = !empty($order->group) ? $order->group->contact_email : '';
            $order->address_show = !empty($order->group) ? $order->group->address_display : '';
            // END - CONVERT TYPE ORDER GROUP
        }

        return $order;
    }

    // PRINT WERKBON & A4
    public static function prepareWerkbonA4Print($order) {
        if(!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
            $convertOrder = static::werkbonOrderGroup($order);
        } else {
            $convertOrder = static::werkbonOrderIndividual($order);
        }

        $order->print_categories = $convertOrder['categories'];
        $order->print_total_items = $convertOrder['totalItems'];

        return $order;
    }

    public static function werkbonOrderIndividual($order) {
        $categories = [];
        $totalItems = 0;

        if(!$order->orderItems->isEmpty()) {
            foreach($order->orderItems as $orderItem) {
                $meta = json_decode($orderItem->metas, true);

                if(!empty($categories[$orderItem->category_id])) {
                    array_push($categories[$orderItem->category_id]['order_items'], $orderItem);
                } else {
                    if(!empty($meta['category'])) {
                        $orderItem->category->name = $meta['category']['name'];
                        if (isset($meta['category']['translations']) && count($meta['category']['translations'])) {
                            $transCategory = collect($meta['category']['translations'])->where('locale', \App::getLocale())->first();
                            if ($transCategory) {
                                $orderItem->category->name = $transCategory['name'];
                            }
                        }
                    }

                    $categories[$orderItem->category_id] = [
                        'order' => $order,
                        'category' => $orderItem->category,
                        'order_items' => [$orderItem]
                    ];
                }

                $totalItems += $orderItem->total_number;
            }
        }

        return compact('categories', 'totalItems');
    }

    public static function werkbonOrderGroup($order) {
        $categories = [];
        $categoryNotes = [];
        $totalItems = 0;

        if(!$order->groupOrders->isEmpty()) {
            foreach($order->groupOrders as $subOrder) {
                $individual = static::werkbonOrderIndividual($subOrder);
                $individualCategories = $individual['categories'];
                $totalItems += $individual['totalItems'];

                if(!empty($individualCategories)) {
                    if(!empty($subOrder->note)) {
                        foreach($individualCategories as $iCategoryId => $iCategory) {
                            $tmpCategory = $iCategory;
                            $categoryNotes[$iCategoryId.'-cat_'.$subOrder->id.'-order'] = $tmpCategory;
                        }
                    } else {
                        foreach($individualCategories as $iCategoryId => $iCategory) {
                            $tmpCategory = $iCategory;

                            if(!empty($categories[$iCategoryId])) {
                                $mergeCategory = array_merge($categories[$iCategoryId]['order_items'], $iCategory['order_items']);
                                $tmpCategory = [
                                    'order' => $tmpCategory['order'],
                                    'category' => $tmpCategory['category'],
                                    'order_items' => $mergeCategory
                                ];
                            }

                            $categories[$iCategoryId] = $tmpCategory;
                        }
                    }
                }
            }

            if(empty($categories)) {
                $categories = $categoryNotes;
            } else {
                if(!empty($categoryNotes)) {
                    foreach ($categoryNotes as $key => $category) {
                        $categories[$key] = $category;
                    }
                }
            }
        }

        return compact('categories', 'totalItems');
    }

    // $stickerMode: Order model => STICKER_PRINT_REMAINING = 0; STICKER_PRINT_ALLOW_ALL_IF_DONE = 1;
    public static function werkbonPrint($order, $isSticker = false, $stickerMode = 0) {
        $products = [];
        $groupByProducts = [];

        if(!empty($order->print_categories)) {
            foreach($order->print_categories as $catId => $orderItems) {
                $flag = true;
                $category = $orderItems['category'];
                $orderOfCat = $orderItems['order'];

                if(!empty($isSticker)) {
                    $category = Category::withTrashed()->where('id', $catId)->first();

                    if(!empty($category)) {
                        if(!empty($orderOfCat->group_id) && empty($category->group)) {
                            $flag = false;
                        }
                        if(empty($orderOfCat->group_id) && empty($category->individual)) {
                            $flag = false;
                        }
                    }
                }

                if(!empty($flag)) {
                    if(empty($products[$catId])) {
                        $products[$catId]['order'] = $orderOfCat;
                        $products[$catId]['category'] = $category;
                    }

                    if(!empty($orderItems['order_items'])) {
                        foreach($orderItems['order_items'] as $orderItem) {
                            $options = $orderItem->optionItems;
                            $optionItems = [];
                            $uniqueKey = 'product_' . $orderItem->product_id;

                            if(!$options->isEmpty()) {
                                foreach($options as $option) {
                                    $optionItemMeta = !empty($option->metas) ? json_decode($option->metas, true) : [];
                                    $optionItems[] = $optionItemMeta;

                                    if(!empty($optionItemMeta['option_item'])) {
                                        $uniqueKey .= '_' . $optionItemMeta['option_item']['id'];
                                    }
                                }
                            }

                            if(!empty($isSticker)) {
                                if($stickerMode == OrderModel::STICKER_PRINT_REMAINING) {
                                    if(!empty($orderItem->order->printed_sticker)) {
                                        continue;
                                    }
                                }
                                if($stickerMode == OrderModel::STICKER_PRINT_ALLOW_ALL_IF_DONE) {
                                    if(empty($order->printed_sticker) && !empty($orderItem->order->printed_sticker)) {
                                        continue;
                                    }
                                }

                                $products[$catId]['products'][] = [
                                    'order_item' => $orderItem,
                                    'total_number' => !empty($orderItem->total_number) ? $orderItem->total_number : 0,
                                    'option_items' => $optionItems
                                ];
                            } else {
                                if(!empty($groupByProducts[$catId])) {
                                    if(in_array($uniqueKey, $groupByProducts[$catId])) {
                                        $products[$catId]['products'][$uniqueKey]['total_number'] += !empty($orderItem->total_number) ? $orderItem->total_number : 0;
                                        continue;
                                    } else {
                                        $groupByProducts[$catId][] = $uniqueKey;
                                    }
                                } else {
                                    $groupByProducts[$catId][] = $uniqueKey;
                                }

                                $products[$catId]['products'][$uniqueKey] = [
                                    'order_item' => $orderItem,
                                    'total_number' => !empty($orderItem->total_number) ? $orderItem->total_number : 0,
                                    'option_items' => $optionItems
                                ];
                            }
                        }
                    }

                    if(empty($products[$catId]['products'])) {
                        unset($products[$catId]);
                    }
                }
            }
        }

        $order->print_products = static::sortAgainCategory($order, $products);

        return $order;
    }

    // $a4Mode: Order model => A4_PRINT_MULTI = 0; A4_PRINT_FORCE = 1;
    public static function a4Print($order, $a4Mode) {
        $products = [];
        $now = date('Y-m-d H:i:s');
        $orderPrinted = [];

        if(!empty($order->print_categories)) {
            foreach($order->print_categories as $catId => $orderItems) {
                $category = $orderItems['category'];
                $orderOfCat = $orderItems['order'];

                if(empty($products[$catId])) {
                    $products[$catId]['order'] = $orderOfCat;
                    $products[$catId]['category'] = $category;
                }

                if(!empty($orderItems['order_items'])) {
                    foreach($orderItems['order_items'] as $orderItem) {
                        $options = $orderItem->optionItems;
                        $optionItems = [];

                        if(!$options->isEmpty()) {
                            foreach($options as $option) {
                                $optionItems[] = !empty($option->metas) ? json_decode($option->metas, true) : [];
                            }
                        }

                        if($a4Mode == OrderModel::A4_PRINT_MULTI) {
                            if(!empty($orderItem->order->printed_a4)) {
                                continue;
                            }
                            // group
                            if(!empty($orderItem->order->group_id)) {
                                if(strtotime($now) < strtotime($orderItem->order->cut_off_time)) {
                                    continue;
                                }
                            }
                        }

                        $orderPrinted[] = $orderItem->order;
                        $products[$catId]['products'][] = [
                            'order_item' => $orderItem,
                            'total_number' => !empty($orderItem->total_number) ? $orderItem->total_number : 0,
                            'option_items' => $optionItems
                        ];
                    }
                }

                if(empty($products[$catId]['products'])) {
                    unset($products[$catId]);
                }
            }
        }

        $order->print_products = static::sortAgainCategory($order, $products);

        if(!empty($orderPrinted)) {
            foreach ($orderPrinted as $orderItem) {
                if(!empty($orderItem->parentOrder) && empty($orderItem->parentOrder->printed_a4)) {
                    $orderItem->parentOrder->printed_a4 = true;
                    $orderItem->parentOrder->save();
                } else {
                    $orderItem->printed_a4 = true;
                    $orderItem->save();
                }
            }
        }

        return $order;
    }

    public static function sortAgainCategory($order, $printProducts) {
        $products = [];
        $productNotes = [];
        $sort = static::prepareSortByCategoryProduct($order, 'category');

        if(!empty($printProducts)) {
            foreach ($printProducts as $printProduct) {
                $key = array_search($printProduct['category']->id, $sort);

                if(!empty($printProduct['order']->note)) {
                    $productNotes[$key][] = $printProduct;
                } else {
                    $products[$key][] = $printProduct;
                }
            }
        }

        ksort($products);
        ksort($productNotes);
        $result = [];

        if(!empty($products)) {
            foreach ($products as $productArr) {
                if(!empty($productArr)) {
                    foreach ($productArr as $product) {
                        $result[] = $product;
                    }
                }
            }
        }
        if(!empty($productNotes)) {
            $groupByOrders = [];

            foreach ($productNotes as $productArr) {
                if(!empty($productArr)) {
                    foreach ($productArr as $product) {
                        if(!empty($groupByOrders[$product['order']->id])) {
                            array_push($groupByOrders[$product['order']->id], $product);
                        } else {
                            $groupByOrders[$product['order']->id] = [$product];
                        }
                    }
                }
            }

            if(!empty($groupByOrders)) {
                foreach ($groupByOrders as $productArr) {
                    if(!empty($productArr)) {
                        foreach ($productArr as $product) {
                            $result[] = $product;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public static function sortAgainProduct($order, $printProducts) {
        $results = [];
        $products = [];
        $sort = static::prepareSortByCategoryProduct($order);

        if(!empty($printProducts)) {
            foreach ($printProducts as $printProduct) {
                $key = array_search($printProduct['order_item']->product_id, $sort);

                if(!empty($products[$key])) {
                    array_push($products[$key], $printProduct);
                } else {
                    $products[$key] = [$printProduct];
                }
            }
        }

        ksort($products);

        if(!empty($products)) {
            foreach ($products as $productArr) {
                if(!empty($productArr)) {
                    foreach ($productArr as $product) {
                        $results[] = $product;
                    }
                }
            }
        }

        return $results;
    }
    // PRINT WERKBON & A4

    // PRINT KASSABON
    public static function kassabonPrint($order) {
        $products = [];
        $sort = static::prepareSortByCategoryProduct($order);

        if(!empty($order->group_id)) {
            if(!$order->groupOrders->isEmpty()) {
                foreach($order->groupOrders as $subOrder) {
                    $orderItems = $subOrder->orderItems;

                    if(!$orderItems->isEmpty()) {
                        foreach($orderItems as $orderItem) {
                            $productKey = array_search($orderItem->product_id, $sort);

                            if(empty($products[$productKey])) {
                                $products[$productKey] = [$orderItem];
                            } else {
                                array_push($products[$productKey], $orderItem);
                            }
                        }
                    }
                }
            }
        } else {
            $orderItems = $order->orderItems;

            if(!$orderItems->isEmpty()) {
                foreach($orderItems as $orderItem) {
                    $productKey = array_search($orderItem->product_id, $sort);

                    if(empty($products[$productKey])) {
                        $products[$productKey] = [$orderItem];
                    } else {
                        array_push($products[$productKey], $orderItem);
                    }
                }
            }
        }

        ksort($products);
        $result = [];

        if(!empty($products)) {
            foreach ($products as $productItems) {
                if (!empty($productItems)) {
                    foreach ($productItems as $productItem) {
                        $result[] = [$productItem];
                    }
                }
            }
        }

        $order->print_kassabon = $result;

        return $order;
    }
    // PRINT KASSABON

    public static function stickerPrint($order, $stickerMode = 0) {
        $order = static::werkbonPrint($order, true, $stickerMode);

        if(empty($order->print_products)) {
            $updateOrder = OrderModel::find($order->id);

            if($updateOrder) {
                $updateOrder->printed_sticker = true;
                $updateOrder->save();
            }
        }

        return $order;
    }

    /**
     * Get all orders of the user in a workspace
     * 
     * @param null $inStatuses
     * @param bool $getAllOrder
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOrderByUser($inStatuses = null, $getAllOrder = false) {
        $userId = !auth()->guest() ? auth()->user()->id : null;

        return OrderModel::getOrderByUser($userId, request()->workspaceId, $inStatuses, $getAllOrder);
    }

    // $type: 0 => start, 1: end
    public static function filterOrderByDateTime($model, $rangeDate, $timezone, $type = 0, $customConds = [])
    {
        $rangeDate = Helper::convertDateTimeToUTC($rangeDate, $timezone);
        $convertDateTime = date('Y-m-d H:i:s', strtotime($rangeDate));
        $cond = '>=';

        if (!empty($type)) {
            $cond = '<=';
        }

        if (!empty($customConds)) {
            $cond = $customConds['cond'];
        }

        return $model->where('date_time', $cond, $convertDateTime);
    }

    public static function autoPrintOrder($orderIdsOrigin = [], $printTypes = [
        SettingPrint::TYPE_WERKBON,
        SettingPrint::TYPE_KASSABON,
        SettingPrint::TYPE_STICKER
    ], $runCrontab = false, $alwayTriggerCrontab = false, $now = null) {
        if(is_null($now)) {
            $now = date('Y-m-d H:i:s');
        }

        if(!empty($orderIdsOrigin)) {
            $orderIdsOrigin = array_unique($orderIdsOrigin);

            foreach (array_chunk($orderIdsOrigin, 100) as $orderIds) {
                if(config('print.debug') == 1) {
                    Log::info("[Order::autoPrintOrder] Processing order IDs: " . implode(',', $orderIdsOrigin) . " for print types: " . implode(',', $printTypes));
                }

                $timezone = 'UTC';
                $orders = OrderModel::whereIn('id', $orderIds)->get();

                if(!$orders->isEmpty()) {
                    $workspaceIds = $orders->pluck('workspace_id')->all();
                    $workspaceIds = array_unique($workspaceIds);
                    $settings = SettingPrint::whereIn('workspace_id', $workspaceIds)
                        ->whereIn('type', $printTypes)
                        ->groupBy('type')
                        ->groupBy('workspace_id');

                    if(empty($alwayTriggerCrontab)) {
                        $settings = $settings->where('auto', true);
                    }

                    $settings = $settings->get();

                    if(!$settings->isEmpty()) {
                        $workspacePrintTypes = [];

                        foreach($settings as $setting) {
                            if(!empty($workspacePrintTypes[$setting->workspace_id])) {
                                array_push($workspacePrintTypes[$setting->workspace_id], $setting->type);
                            } else {
                                $workspacePrintTypes[$setting->workspace_id] = [$setting->type];
                            }
                        }

                        if(!empty($workspacePrintTypes)) {
                            $data = [];
                            $stickerIdentical = [];

                            foreach($orders as $order) {
                                if(!empty($workspacePrintTypes[$order->workspace_id])) {
                                    $printSettingIdentical = SettingPrint::where('workspace_id', $order->workspace_id)
                                        ->where('type', SettingPrint::TYPE_STICKER)
                                        ->where('type_id', SettingPrint::IDENTICAL_PRODUCTS)
                                        ->first();

                                    foreach($workspacePrintTypes[$order->workspace_id] as $printType) {
                                        if(!empty($order->timezone)) {
                                            $timezone = $order->timezone;
                                        }

                                        $gereedLocalTime = \App\Helpers\Helper::convertDateTimeToTimezone($order->gereed, $timezone);
                                        $todayLocalTime = \App\Helpers\Helper::convertDateTimeToTimezone($now, $timezone);

                                        // check condition and will checked crontab to trigger when run crontab
                                        if(empty($runCrontab) && in_array($printType, [
                                                SettingPrint::TYPE_WERKBON,
                                                SettingPrint::TYPE_KASSABON,
                                                SettingPrint::TYPE_STICKER
                                            ])) {
                                            $checkPrinter = SettingPrint::where('workspace_id', $order->workspace_id)
                                                ->where('type', $printType)
                                                ->where('auto', true)
                                                ->first();

                                            if(empty($checkPrinter)) {
                                                $order->refresh();
                                                $order->run_crontab = true;
                                                $order->save();
                                                continue;
                                            }

                                            $flag = false;

                                            if(in_array($order->type, [\App\Models\Order::TYPE_IN_HOUSE, \App\Models\Order::TYPE_SELF_ORDERING])) {
                                                $gereed = $order->gereed;

                                                if($order->type == \App\Models\Order::TYPE_IN_HOUSE) {
                                                    $gereed = $order->tableOrderingLastPerson()->gereed;
                                                }

                                                if(strtotime($now) <= strtotime('+5 minutes', strtotime($gereed))) {
                                                    $flag = true;
                                                } else {
                                                    continue;
                                                }
                                            } elseif(!empty($order->group_id)) {
                                                // group
                                                if(strtotime($now) < strtotime('+200 seconds', strtotime($order->cut_off_time))) {
                                                    $flag = true;
                                                }
                                            } else {
                                                // individual
                                                if(strtotime($now) <= strtotime($order->gereed)) {
                                                    $flag = true;
                                                } else {
                                                    continue;
                                                }
                                            }

                                            if(!empty($flag)) {
                                                $order->refresh();
                                                $order->run_crontab = true;
                                                $order->save();
                                                continue;
                                            }
                                        }

                                        // run when crontab enabled
                                        if(!empty($runCrontab)) {
                                            $flag = false;

                                            if($order->type != \App\Models\Order::TYPE_IN_HOUSE) {
                                                if(!empty($order->group_id)) {
                                                    // group
                                                    if(strtotime($now) < strtotime('+200 seconds', strtotime($order->cut_off_time))) {
                                                        $flag = true;
                                                    } else {
                                                        $gereedLocalTimeSum200 = \App\Helpers\Helper::convertDateTimeToTimezone(date('Y-m-d H:i:s', strtotime('+200 seconds', strtotime($order->gereed))), $timezone);
                                                        if(date('Y-m-d', strtotime($gereedLocalTime)) != date('Y-m-d', strtotime($todayLocalTime)) &&
                                                            date('Y-m-d', strtotime($gereedLocalTimeSum200)) != date('Y-m-d', strtotime($todayLocalTime))) {
                                                            $order->refresh();
                                                            $order->run_crontab = false;
                                                            $order->save();
                                                            continue;
                                                        }
                                                    }
                                                } else {
                                                    // individual
                                                    if(date('Y-m-d', strtotime($gereedLocalTime)) != date('Y-m-d', strtotime($todayLocalTime))) {
                                                        $flag = true;
                                                    }
                                                    if(strtotime($now) > strtotime('+5 minutes', strtotime($order->gereed))) {
                                                        $order->refresh();
                                                        $order->run_crontab = false;
                                                        $order->save();
                                                        continue;
                                                    }
                                                }
                                            }

                                            if(!empty($flag)) {
                                                continue;
                                            }
                                        }

                                        if(!empty($runCrontab) &&
                                            !empty($printSettingIdentical) &&
                                            $printType == SettingPrint::TYPE_STICKER) {
                                            $stickerIdentical[] = $order;
                                        } else {
                                            // trigger printing function
                                            $type = config('print.job_type_decode.'.$printType);
                                            $contents = static::processPrint($order, $type, $timezone, false, null);
                                            $generateContentAutoPrint = static::generateContentAutoPrint($order, $type, $contents, $data);
                                            $data = $generateContentAutoPrint['data'];

                                            if($type == 'werkbon') {
                                                $extraIds = static::getCategoryIdsExtraWerkbon($order->id);

                                                if(!empty($extraIds)){
                                                    $subContents = static::processPrint($order, $type, $timezone, false, $extraIds);
                                                    $subGenerateContentAutoPrint = static::generateContentAutoPrint($order, $type, $subContents, $data);
                                                    $data = $subGenerateContentAutoPrint['data'];
                                                }
                                            }
                                        }

                                        // printing function will be trigger and checked in db
                                        if(in_array($printType, [
                                            SettingPrint::TYPE_WERKBON,
                                            SettingPrint::TYPE_KASSABON,
                                            SettingPrint::TYPE_STICKER
                                        ])){
                                            $order->refresh();

                                            if($printType == SettingPrint::TYPE_WERKBON){
                                                $order->auto_print_werkbon = true;
                                            } else if($printType == SettingPrint::TYPE_KASSABON){
                                                $order->auto_print_kassabon = true;
                                            } else {
                                                $order->auto_print_sticker = true;
                                            }

                                            $order->save();
                                        }
                                    }
                                }
                            }

                            if(!empty($stickerIdentical)) {
                                $contents = [];
                                $triggerPrint = static::triggerProcessPrintStickerIdentical($stickerIdentical, $data, 'sticker', $contents);
                                $data = $triggerPrint['data'];
                            }

                            static::createJobAndCopyPrint($data, true);
                        }
                    }
                }
            }
        }
    }

    public static function generateContentAutoPrint($order, $type, $contents, $data = []) {
        if(!empty($contents)) {
            foreach($contents as $contentKey => $content) {
                if($content['type'] == 'image') {
                    $contents[$contentKey]['url'] = \Storage::url($content['path']);
                }
            }

            $dataItem = static::prepareOrderJobData($order, $type, $contents);
            $data = array_merge($data, $dataItem);
        }

        return compact('data', 'contents');
    }

    public static function createJobAndCopyPrint($data, $auto = true, $stickerSpecialCase = false) {
        if(!empty($data)) {
            $jobs = [];

            foreach($data as $jobItem) {
                $settings = SettingPrint::where('workspace_id', $jobItem['workspace_id'])
                    ->where('type', $jobItem['job_type'])
                    ->whereNotNull('mac');

                if(!empty($auto)) {
                    $settings = $settings->where('auto', true);
                }

                $settings = $settings->get();

                if(!$settings->isEmpty()) {
                    if($jobItem['job_type'] == SettingPrint::TYPE_STICKER &&
                        $jobItem['foreign_model'] == \App\Models\Order::class) {
                        if(!empty($jobItem['foreign_id'])) {
                            $orderId = $jobItem['foreign_id'];
                            $order = OrderModel::find($orderId);

                            if((!empty($auto) || !empty($stickerSpecialCase)) && !empty($order->check_printed_sticker_multi)) {
                                continue;
                            }

                            $order->printed_sticker_multi = true;
                            $order->save();
                        }
                        if(!empty($jobItem['foreign_ids'])) {
                            $orderIds = array_unique(explode('_', $jobItem['foreign_ids']));
                            $orders = OrderModel::whereIn('id', $orderIds)->get();
                            $checked = true;

                            foreach ($orders as $order) {
                                if((!empty($auto) || !empty($stickerSpecialCase)) && empty($order->check_printed_sticker_multi)) {
                                    $checked = false;
                                }

                                $order->printed_sticker_multi = true;
                                $order->save();
                            }

                            if(!empty($auto) && !empty($checked)) {
                                continue;
                            }
                        }
                    }

                    foreach ($settings as $setting) {
                        $jobItem['printer_id'] = $setting->id;
                        $jobItem['mac_address'] = $setting->mac;
                        $copy = !empty($setting->copy) ? $setting->copy : 1;

                        for ($i = 0; $i < $copy; $i++) {
                            $jobs[] = $jobItem;
                        }
                    }
                }
            }

            if(!empty($jobs)) {
                PrinterJob::insert($jobs);
            }
        }
    }

    public static function processPrint($order, $type, $timezone, $manualPrint = false, $extraWerkbonCategoryIds = null) {
        $optionSetting = SettingPreference::where('workspace_id', $order->workspace_id)->first();
        $option = !empty($optionSetting->option) ? $optionSetting->option : null;
        $optionSettingId = !empty($option->id) ? $option->id : null;
        $locale = $order->workspace->getLocale();
        if(in_array($type, [
            config('print.all_type.sticker'),
            config('print.all_type.kassabon'),
        ])) {
            $locale = $order->user ? $order->user->getLocale() : $order->workspace->getLocale();
        }
        \App::setLocale($locale);
        $order = static::convertOrderItem($order);
        if(in_array($type, [
            config('print.all_type.a4'),
            config('print.all_type.werkbon'),
            config('print.all_type.sticker')
        ])) {
            $order = static::sortByCategoryProduct($order);
            $order = static::prepareWerkbonA4Print($order);
        }
        if($type == config('print.all_type.werkbon')) {
            $order = static::werkbonPrint($order);
        }
        if($type == config('print.all_type.a4')) {
            $a4Mode = !empty($manualPrint) ? OrderModel::A4_PRINT_FORCE : OrderModel::A4_PRINT_MULTI;
            $order = static::a4Print($order, $a4Mode);
            return compact('order', 'timezone', 'type', 'optionSettingId', 'option');
        }
        if($type == config('print.all_type.kassabon')) {
            $order = static::kassabonPrint($order);
        }
        if($type == config('print.all_type.sticker')) {
            $stickerMode = !empty($manualPrint) ? OrderModel::STICKER_PRINT_ALLOW_ALL_IF_DONE : OrderModel::STICKER_PRINT_REMAINING;
            $order = static::stickerPrint($order, $stickerMode);

            if(empty($order->print_products)) {
                return [];
            }
        }

        // Makes it easier to later switch to text or combination of text/image
        $contents = [];
        $printPath = 'print';

        // Check what type we will use to process the content
        $format = 'image';
        $configFormats = config('print.format');

        if(isset($configFormats[$type])) {
            $format = $configFormats[$type];
        }

        switch($format) {
            case 'bbcode':
                $width = config('print.px.'.$type.'.width');
                $viewData = compact('order', 'timezone', 'width');

                if(!empty($viewExtraData)) {
                    $viewData = array_merge($viewData, $viewExtraData);
                }

                // Process view and make sure we do not include spaces we do not actual need
                $view = view('manager.orders.prints.'.$type.'-bbcode', $viewData)->render();
                $view = preg_replace('/[\t\n\r\0\x0B]/', '', $view);
                $view = preg_replace('/([\s])\1+/', ' ', $view);
                $view = trim($view);

                $content = [
                    'type' => $format,
                    // Make sure we do not include any line endings to make the design based on the actual bbcode
                    'text' => $view
                ];

                // (it's possible to split a print in multiple parts/types)
                $contents[] = $content;
            break;

            default:
            case 'image';
                // Define image name
                $image = implode('-', ['order', $type, $order->id, strtotime(now()), \Uuid::generate()->string]).'.png';
                $path = implode('/', [$printPath, $image]);

                $content = [
                    'type' => $format,
                    'filename' => $image,
                    'path' => $path
                ];

                // (it's possible to split a print in multiple parts/types)
                $contents[] = $content;

                static::snappyImageSave($order, $timezone, $type, $image, compact('optionSettingId', 'option', 'extraWerkbonCategoryIds'), false);
            break;
        }

        return $contents;
    }

    public static function prepareOrderJobData($order, $type, $contents, $settingType = 'normal') {
        $data = [];
        $now = now();

        foreach($contents as $key => $content) {
            if (
                isset($content['type'])
                && $content['type'] == 'image'
                && isset($content['path'])
            ) {
                $jobContent = $content['path'];

                $printPartPath = 'print/parts';
                $sourceFile = \Storage::disk('public')->path($content['path']);
                $destPath = implode('/', [config('filesystems.disks.public.root'), $printPartPath]);
                $imageSplits = Helper::splitImage($sourceFile, $destPath, basename($content['filename'], '.png') . '-part%02d.png');

                $metas = [];
                if (!empty($imageSplits)) {
                    foreach ($imageSplits as $imageItem) {
                        $metas[] = [
                            'type' => 'image',
                            'filename' => $imageItem,
                            'path' => implode('/', [$printPartPath, $imageItem]),
                            'printed' => 0
                        ];
                    }
                }

                $contents[$key]['printed'] = 0;
                $contents[$key]['metas'] = $metas;
            }
        }

        $item = [
            'workspace_id' => null,
            'foreign_id' => null,
            'foreign_ids' => null,
            'status' => \App\Models\PrinterJob::STATUS_PENDING,
            'job_type' => config('print.job_type.' . $type),
            'foreign_model' => OrderModel::class,
            'content' => '',
            'meta_data' => !empty($contents) ? json_encode($contents) : null,
            'created_at' => $now,
            'updated_at' => $now
        ];

        if($settingType == 'identical') {
            $item['workspace_id'] = $order['workspaceId'];
            $item['foreign_ids'] = implode('_', $order['orderIds']);
        } else {
            $item['workspace_id'] = $order->workspace_id;
            $item['foreign_id'] = $order->id;
        }

        $data[] = $item;

        return $data;
    }

    public static function snappyImageSave($order, $timezone, $type, $image, $viewExtraData = [], $convertToGreyScaleImage = false) {
        $width = config('print.px.'.$type.'.width');
        $viewData = compact('order', 'timezone', 'width');

        if(!empty($viewExtraData)) {
            $viewData = array_merge($viewData, $viewExtraData);
        }
        $productIds = [];
        $productNames = [];
        if ($type == config('print.all_type.kassabon')) {
            foreach($order->print_kassabon ?? [] as $optionItem) {
                $productIds[] = $optionItem[0]->product_id;
            }
        } else {
            foreach($order->print_products ?? [] as $optionItem) {
                $productIds[] = collect($optionItem['products'])->pluck('order_item.product_id')->all();
            }
        }
        $productNames = ProductTranslation::whereIn('product_id', $productIds)->get()->toArray();
        $locale = $order->workspace->getLocale();
        
        if(in_array($type, [
            config('print.all_type.sticker'),
            config('print.all_type.kassabon'),
        ])) {
            $locale = $order->user ? $order->user->getLocale() : $order->workspace->getLocale();
        }
        if ($order->contact) {
            $locale = $order->contact->locale;
        }
        \App::setLocale($locale);
        $viewData['locale'] = $locale;
        $viewData['productNames'] = $productNames;
        $view = view('manager.orders.prints.'.$type, $viewData)->render();
        $path = implode('/', [config('filesystems.disks.public.root'), 'print', $image]);

        \SnappyImage::loadHTML($view)
            ->setOption('width', $width)
            ->setOption('quality', 1)
            ->setOption('format', 'png')
            ->setOption('disable-smart-width', true)
            ->save($path);

        // Greyscale image (lower in size and better for printing)
        if($convertToGreyScaleImage && file_exists($path)) {
            $image = imagecreatefrompng($path);
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            imagefilter($image, IMG_FILTER_CONTRAST, -1000);
            imagepng($image, $path);
        }
    }

    public static function getOptionAndOptionItemByOrderOptions($orderOptionItems) {
        $options = [];
        $optionItems = [];

        if(!empty($orderOptionItems)) {
            foreach($orderOptionItems as $optionItem) {
                if(!empty($optionItem->metas)) {
                    $metas = json_decode($optionItem->metas, true);

                    if(!empty($metas['option_item'])) {
                        $optionItems[$metas['option_item']['id']] = $metas['option_item'];

                        foreach($metas['option'] as $option) {
                            $options[$option['id']] = $option;
                        }
                    }
                }
            }
        }

        return compact('options', 'optionItems');
    }

    public static function conditionShowOrderList($model)
    {
        return $model->where(function ($query) {
            $query->where(function ($subQuery) {
                    $subQuery->whereNull('group_id')
                        ->successOrder();
                })
                ->orWhereHas('groupOrders', function ($subQuery) {
                    $subQuery->successOrder();
                });
            })
            ->with(['groupOrders' => function ($query) {
                $query->successOrder();
            }]);
    }

    public static function convertHaveNoteAndNot($order) {
        $notes = [];
        $notNotes = [];

        if(!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
            if(!$order->groupOrders->isEmpty()) {
                foreach($order->groupOrders as $subOrder) {
                    if(!$subOrder->orderItems->isEmpty()) {
                        if(!empty($subOrder->note)) {
                            $notes[] = $subOrder->orderItems;
                        } else {
                            $notNotes[] = $subOrder->orderItems;
                        }
                    }
                }
            }
        } else {
            if(!$order->orderItems->isEmpty()) {
                if(!empty($order->note)) {
                    $notes[] = $order->orderItems;
                } else {
                    $notNotes[] = $order->orderItems;
                }
            }
        }

        return compact('notes', 'notNotes');
    }

    public static function prepareSortByCategoryProduct($order = null, $mode = 'product', $catIds = [], $productIds = []){
        if(!is_null($order)) {
            $orderItems = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->where('orders.id', $order->id)
                ->orWhere('orders.parent_id', $order->id)
                ->select(['products.category_id', 'product_id'])
                ->get();

            $catIds = $orderItems->pluck('category_id');
            $productIds = $orderItems->pluck('product_id');
        }

        $productSorts = [];
        $cats = \App\Models\Category::whereIn('id', $catIds)
            ->withTrashed()
            ->orderBy('order', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->get();

        if(!$cats->isEmpty()) {
            if($mode == 'category') {
                return $cats->pluck('id')->toArray();
            }

            foreach ($cats as $cat) {
                $products = $cat->productIncTrash()
                    ->whereIn('id', $productIds)
                    ->orderBy('order', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->select('id')
                    ->get();

                if(!$products->isEmpty()) {
                    // foreach ($products as $product) {
                        $productSorts = array_merge($productSorts, $products->pluck('id')->toArray());
                    // }
                }
            }
        }

        return $productSorts;
    }

    public static function sortByCategoryProduct($order){
        $optionItems = static::convertHaveNoteAndNot($order);
        $productSort = static::prepareSortByCategoryProduct($order);

        $notes = [];
        $notNotes = [];
        $optionItemCheck = [];

        if(!empty($optionItems['notes'])) {
            foreach ($optionItems['notes'] as $originKey => $orderOptionItem) {
                if(!empty($orderOptionItem)) {
                    foreach ($orderOptionItem as $item) {
                        if(!$item->optionItems->isEmpty()) {
                            foreach($item->optionItems as $optionItem) {
                                if(!empty($optionItemCheck[$optionItem->optie_item_id])) {
                                    $optionItemCheck[$optionItem->optie_item_id] = $optionItemCheck[$optionItem->optie_item_id] + ($item->total_number > 0 ? $item->total_number : 1);
                                } else {
                                    $optionItemCheck[$optionItem->optie_item_id] = $item->total_number > 0 ? $item->total_number : 1;
                                }
                            }
                        }

                        if(in_array($item->product_id, $productSort)) {
                            $key = array_search($item->product_id, $productSort);

                            if(!empty($notes[$originKey][$key])) {
                                array_push($notes[$originKey][$key], $item);
                            } else {
                                $notes[$originKey][$key] = [$item];
                            }

                            ksort($notes[$originKey]);
                        }
                    }
                }
            }
        }

        if(!empty($optionItems['notNotes'])) {
            foreach ($optionItems['notNotes'] as $orderOptionItem) {
                if(!empty($orderOptionItem)) {
                    foreach ($orderOptionItem as $item) {
                        if(!$item->optionItems->isEmpty()) {
                            foreach($item->optionItems as $optionItem) {
                                if(!empty($optionItemCheck[$optionItem->optie_item_id])) {
                                    $optionItemCheck[$optionItem->optie_item_id] = $optionItemCheck[$optionItem->optie_item_id] + ($item->total_number > 0 ? $item->total_number : 1);
                                } else {
                                    $optionItemCheck[$optionItem->optie_item_id] = $item->total_number > 0 ? $item->total_number : 1;
                                }
                            }
                        }

                        if(in_array($item->product_id, $productSort)) {
                            $key = array_search($item->product_id, $productSort);

                            if(!empty($notNotes[$key])) {
                                array_push($notNotes[$key], $item);
                            } else {
                                $notNotes[$key] = [$item];
                            }

                            ksort($notNotes);
                        }
                    }
                }
            }
        }

        $order->data_sort = compact('notes', 'notNotes');
        $order->option_item_check = $optionItemCheck;

        return $order;
    }

    // BEGIN STICKER MULTIPLE
    public static function processPrintStickerIdentical($orders) {
        $products = [];
        $productInNote = [];
        $productExNote = [];
        $orderIds = [];
        $catIds = [];
        $productIds = [];
        $workspaceId = null;
        $existedProduct = false;

        if(!empty($orders)) {
            foreach ($orders as $order) {
                $workspaceId = $order->workspace_id;
                $orderIds[] = $order->id;
                $order = static::convertOrderItem($order);
                $order = static::sortByCategoryProduct($order);
                $order = static::prepareWerkbonA4Print($order);
                $order = static::stickerPrint($order);

                if(!empty($order->print_products)) {
                    foreach ($order->print_products as $printProduct) {
                        if(!empty($printProduct['order']->note)) {
                            $productInNote[] = $printProduct;
                        } else {
                            if(!empty($printProduct['products'])) {
                                foreach ($printProduct['products'] as $productItem) {
                                    $productId = $productItem['order_item']->product_id;
                                    $catIds[] = $productItem['order_item']->category_id;
                                    $productIds[] = $productId;
                                    $clonePrintProduct = $printProduct;
                                    $clonePrintProduct['products'] = [$productItem];

                                    if(!empty($productExNote[$productId])) {
                                        array_push($productExNote[$productId], $clonePrintProduct);
                                    } else {
                                        $productExNote[$productId] = [$clonePrintProduct];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($productExNote)) {
                $productExNoteSort = static::prepareSortByCategoryProduct(null, 'product', $catIds, $productIds);
                $productExNoteTmp = [];

                foreach ($productExNote as $productId => $productArr) {
                    $key =  array_search($productId, $productExNoteSort);
                    $productExNoteTmp[$key] = $productArr;
                }

                ksort($productExNoteTmp);
                $productExNote = $productExNoteTmp;

                foreach ($productExNote as $groupByProduct) {
                    if(!empty($groupByProduct)) {
                        foreach ($groupByProduct as $product) {
                            if(!empty($product['products'])) {
                                $existedProduct = true;
                            }
                            $products[] = $product;
                        }
                    }
                }
            }
            if(!empty($productInNote)) {
                foreach ($productInNote as $product) {
                    if(!empty($product['products'])) {
                        $existedProduct = true;
                    }
                    $products[] = $product;
                }
            }
            if(!empty($orderIds)) {
                $orderIds = array_unique($orderIds);
            }
        }

        if(empty($existedProduct)) {
            $products = [];
        }

        return compact('products', 'orderIds', 'workspaceId');
    }

    public static function processPrintItemStickerIdentical($productStickers, $type) {
        // Makes it easier to later switch to text or combination of text/image
        $contents = [];
        $printPath = 'print';

        // Check what type we will use to process the content
        $format = 'image';
        $configFormats = config('print.format');
        $viewExtraData = compact('productStickers');

        if(isset($configFormats[$type])) {
            $format = $configFormats[$type];
        }

        switch($format) {
            case 'bbcode':
                $width = config('print.px.'.$type.'.width');
                $viewData = compact('width');

                if(!empty($viewExtraData)) {
                    $viewData = array_merge($viewData, $viewExtraData);
                }

                // Process view and make sure we do not include spaces we do not actual need
                $view = view('manager.orders.prints.'.$type.'-bbcode', $viewData)->render();
                $view = preg_replace('/[\t\n\r\0\x0B]/', '', $view);
                $view = preg_replace('/([\s])\1+/', ' ', $view);
                $view = trim($view);

                $content = [
                    'type' => $format,
                    // Make sure we do not include any line endings to make the design based on the actual bbcode
                    'text' => $view
                ];

                // (it's possible to split a print in multiple parts/types)
                $contents[] = $content;
                break;

            default:
            case 'image';
                // Define image name
                $image = implode('-', ['order', $type, 'identical', strtotime(now()), \Uuid::generate()->string]).'.png';
                $path = implode('/', [$printPath, $image]);

                $content = [
                    'type' => $format,
                    'filename' => $image,
                    'path' => $path
                ];

                // (it's possible to split a print in multiple parts/types)
                $contents[] = $content;

                static::snappyImageSave(null, null, $type, $image, $viewExtraData);
                break;
        }

        return $contents;
    }

    public static function triggerProcessPrintStickerIdentical($orders, $data, $type, $contents) {
        $identicalData = static::processPrintStickerIdentical($orders);

        if(!empty($identicalData['products'])) {
            $products = $identicalData['products'];
            $orderIds = $identicalData['orderIds'];
            $workspaceId = $identicalData['workspaceId'];
            $currentContents = static::processPrintItemStickerIdentical($products, $type);

            if(!empty($currentContents)) {
                foreach($currentContents as $contentKey => $content) {
                    if ($content['type'] == 'image') {
                        $currentContents[$contentKey]['url'] = \Storage::url($content['path']);
                    }
                }

                $dataItem = static::prepareOrderJobData(compact('workspaceId', 'orderIds'), $type, $currentContents, 'identical');
                $data = array_merge($data, $dataItem);
            }

            $contents = array_merge($contents, $currentContents);
        }

        return compact('contents', 'data');
    }

    // END STICKER MULTIPLE

    /**
     * @param OrderModel $order
     * @return OrderModel
     */
    public static function sortOptionItems(OrderModel $order)
    {
//        $order->orderItems->transform(function ($orderItem) {
//            $options = static::getOptionsOrderByOrderItem($orderItem);
//            if (count($options) > 0) {
//                $optionItems = $orderItem->optionItems->sort(function ($a, $b) use ($options) {
//                    $keyA = array_search($a->optie_id, $options);
//                    $keyB = array_search($b->optie_id, $options);
//                    if ($keyA == $keyB) {
//                        return 0;
//                    }
//
//                    return ($keyA < $keyB) ? -1 : 1;
//                });
//
//                $orderItem->optionItems = $optionItems;
//            }
//
//            return $orderItem;
//        });

        return $order;
    }

    /**
     * @param $orderItem
     * @return array|mixed
     */
    public static function getOptionsOrderByOrderItem($orderItem)
    {
        $sortedOptiesIds = [];
        $product = $orderItem->product;
        if ($product) {
            $sortedOptiesIds = $product->options
                ->sortBy(function ($option, $key) {
                    $option->items = collect($option->items)->sortBy('order')->all();
                    return $option;
                })
                ->where('pivot.is_checked', true)
                ->pluck('id')->toArray();
        }

        return $sortedOptiesIds;
    }

    public static function groupIdenticalProductInOrderList($subItems, $note = null, $noteByUser = null) {
        $products = [];
        $note = null;
        $noteByUser = null;

        if(!empty($subItems)) {
            foreach ($subItems as $orderItem) {
                $meta = json_decode($orderItem->metas, true);
                $productName = !empty($orderItem->product) ? $orderItem->product->name : (!empty($meta['product']['name']) ? $meta['product']['name'] : '');
                $optionItems = [];

                if(empty($note)) {
                    $note = !empty($orderItem->order->note) ? $orderItem->order->note : '';
                    $noteByUser = !empty($orderItem->order->user) ? $orderItem->order->user->name : '';
                }

                if (!$orderItem->optionItems->isEmpty()) {
                    foreach ($orderItem->optionItems as $optionItem) {
                        $meta = json_decode($optionItem->metas, true);

                        if (!empty($meta['option_item']['id']) && empty($optionItems[$meta['option_item']['id']])) {
                            $optionItemName = !empty($optionItem->optionItem) ? $optionItem->optionItem->name : $meta['option_item']['name'];

                            if (!empty($meta['option'][0]['is_ingredient_deletion'])) {
                                $optionItemName = '<i>Z</i> ' . $optionItemName;
                            }

                            $optionItems[$meta['option_item']['id']] = $optionItemName;
                        }
                    }
                }

                $key = $productName;

                if(!empty($optionItems)) {
                    $key .= ' ('. implode(', ', $optionItems) .')';
                }

                $key = trim($key);

                if(!empty($products[$key])) {
                    $products[$key] += $orderItem->total_number;
                } else {
                    $products[$key] = $orderItem->total_number;
                }
            }
        }

        return compact('products', 'note', 'noteByUser');
    }

    public static function getCategoryIdsExtraWerkbon($orderId) {
        $orderIds = \App\Models\Order::where('id', $orderId)
            ->orWhere('parent_id', $orderId)
            ->pluck('id')
            ->all();

        if(!empty($orderIds)) {
            $orderItemCategoryExtras = \App\Models\OrderItem::whereIn('order_id', $orderIds)
                ->whereHas('category', function ($query) {
                    $query->where('extra_werkbon', true);
                })
                ->pluck('category_id')
                ->all();

            if(!empty($orderItemCategoryExtras)) {
                return $orderItemCategoryExtras;
            }
        }

        return null;
    }

    /**
     * @param string $type
     * @param \App\Models\Order $order
     * @param array $contents
     * @param array $data
     * @return array
     */
    public static function printItemProcess($type, $order, $contents, $data = [])
    {
        if (!empty($contents)) {
            foreach ($contents as $contentKey => $content) {
                if ($content['type'] == 'image') {
                    $contents[$contentKey]['url'] = \Storage::url($content['path']);
                }
            }

            if ($type != 'a4') {
                $dataItem = static::prepareOrderJobData($order, $type, $contents);
                $data = array_merge($data, $dataItem);
            }
        }

        return compact('data', 'contents');
    }
}