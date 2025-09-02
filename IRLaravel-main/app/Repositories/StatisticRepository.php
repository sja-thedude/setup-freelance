<?php

namespace App\Repositories;

use App\Models\SettingPayment;
use App\Models\Order;
use App\Models\Workspace;
use App\Models\User;
use App\Helpers\Order as OrderHelper;

class StatisticRepository extends AppBaseRepository
{
    public function model()
    {
        return Order::class;
    }

    public function convertStatisticOrders($orders) {
        $result = [];

        if(!$orders->isEmpty()) {
            foreach($orders as $order) {
                if ($order->type == Order::TYPE_IN_HOUSE && $order->parent_id) continue; 
                $result[] = [
                    'date_time' => date('Y-m-d H:i:s', strtotime($order->date_time)),
                    'total_price' => $order->calculate_total_price
                ];
            }
        }

        return $result;
    }

    public function statisticOrderInTimes($startDate = null, $endDate = null, $timezone = 'UTC', $workspaceId = null)
    {
        $model = $this->model->where('no_show', false);

        if (!is_null($workspaceId)) {
            $model = $model->whereIn('workspace_id', $workspaceId);
        }

        $model = $model->where(function ($query) {
            $query->whereNull('group_id')
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNotNull('group_id')->whereNull('parent_id');
                });
        });

        $model = OrderHelper::conditionShowOrderList($model);

        if (!empty($startDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $startDate, $timezone, 0);
        }

        if (!empty($endDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $endDate, $timezone, 1);
        }

        return $model->orderBy('date_time', 'ASC');
    }

    public function statisticActiveOrders($workspaceId = null)
    {
        $now = now();
        $model = $this->makeModel()->withTrashed()->where('no_show', false);

        if (!is_null($workspaceId)) {
            $model = $model->whereIn('workspace_id', $workspaceId);
        }

        $model = $model->where(function ($query) {
            $query->whereNull('group_id')
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNotNull('group_id')->whereNull('parent_id');
                });
        });

        $model = OrderHelper::conditionShowOrderList($model);
        $model = OrderHelper::filterOrderByDateTime($model, $now, 'UTC', 0, ['cond' => '>']);

        return $model->get();
    }

    public function statisticActiveRestaurants($workspaceId = null)
    {
        $model = Workspace::where('active', true);

        if (!is_null($workspaceId)) {
            $model = $model->whereIn('id', $workspaceId);
        }

        return $model->get();
    }

    public function statisticActiveEndUsers()
    {
        $model = User::where('platform', User::PLATFORM_FRONTEND)
            ->where('is_super_admin', false)
            ->where('is_admin', false);

        return $model->get();
    }

    public function statisticManagerActiveEndUsers($workspaceId = null)
    {
        $userIds = [];

        if (!is_null($workspaceId)) {
            $userInOrder = Order::whereIn('workspace_id', $workspaceId)->get();
            $userIds = array_unique($userInOrder->pluck('user_id')->all());
        }

        $model = User::whereIn('id', $userIds)
            ->where('platform', User::PLATFORM_FRONTEND)
            ->where('is_super_admin', false)
            ->where('is_admin', false);

        return $model->get();
    }

    /**
     * @param null $startDate
     * @param null $endDate
     * @param string $timezone
     * @param null $keyword
     * @param null $workspaceId
     * @param bool $discountTab
     * @param array $cond
     * @param array $productIds
     * @param null $groupId
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function statisticManagerPerProduct(
        $startDate = null,
        $endDate = null,
        $timezone = 'UTC',
        $keyword = null,
        $workspaceId = null,
        $discountTab = false,
        $cond = [],
        $productIds = [],
        $groupId = null
    ) {
        $model = $this->makeModel()->where('no_show', false)
            ->where('is_test_account', Order::IS_TRUST_ACCOUNT);

        if (!is_null($workspaceId)) {
            $model = $model->whereIn('workspace_id', $workspaceId);
        }

        //using for manager en/manager/statistic/discount
        if ($discountTab) {
            $model = $model->where(function ($query) {
                $query->whereNotNull('coupon_discount')
                    ->orWhereNotNull('redeem_discount')
                    ->orWhereNotNull('group_discount');
            });

            $model = $model->whereHas('orderItems', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('coupon_discount')
                        ->orWhereNotNull('redeem_discount')
                        ->orWhereNotNull('group_discount');
                });
            });
        }

        // Check case for only group
        if (empty($groupId)) {
            $model = $model->where(function ($query) {
                $query->whereNull('group_id')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('group_id')->whereNull('parent_id');
                    });
            });
        } else {
            $model = $model->where('group_id', $groupId)->whereNull('parent_id');
        }


        $model = OrderHelper::conditionShowOrderList($model);

        if(!empty($startDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $startDate, $timezone, 0);
        }

        if(!empty($endDate)) {
            $model = OrderHelper::filterOrderByDateTime($model, $endDate, $timezone, 1);
        }

        if (!is_null($keyword)) {
            $model = $model->where(function ($query) use ($productIds, $discountTab) {
                $query->whereHas('orderItems', function ($query) use ($productIds, $discountTab) {
                    $query->whereIn('product_id', $productIds);

                    //using for manager en/manager/statistic/discount
                    if ($discountTab) {
                        $query->where(function ($subQuery) {
                            $subQuery->whereNotNull('coupon_discount')
                                ->orWhereNotNull('redeem_discount')
                                ->orWhereNotNull('group_discount');
                        });
                    }
                });

                // Or child order where has product
                $query->orWhereHas('groupOrders.orderItems', function ($query) use ($productIds, $discountTab) {
                    $query->whereIn('product_id', $productIds);

                    if ($discountTab) {
                        $query->where(function ($subQuery) {
                            $subQuery->whereNotNull('coupon_discount')
                                ->orWhereNotNull('redeem_discount')
                                ->orWhereNotNull('group_discount');
                        });
                    }
                });
            });
        }

        if(!empty($cond)) {
            $model = $model->where($cond['field'], $cond['cond'], $cond['value']);
        }

        $model = $model->select('id', 'group_id', 'type', 'ship_price', 'parent_id', 'payment_method', 'total_price', 'service_cost')->with([
        'orderItems' => function ($query) {
            $query->select(
                'id',
                'workspace_id',
                'order_id',
                'category_id',
                'product_id',
                'type',
                'price',
                'total_number',
                'subtotal',
                'total_price',
                'paid',
                'vat_percent',
                'coupon_id',
                'coupon_discount',
                'redeem_history_id',
                'ship_price',
                'group_discount',
                'available_discount',
                'redeem_discount'
            );
        },
        'orderItems.category' => function ($query) {
            $query->select('id');
        }
        ])->get();

        return $model;
    }

    /**
     * @param $orders
     * @param bool $discountTab
     * @return array
     */
    public function groupByCategory($orders, $discountTab = false, $checkProductIds = false, $productIds = []) {
        $categoryProducts = [];

        if(!$orders->isEmpty()) {
            $noShowOrders = Order::whereIn('id', $orders->pluck('parent_id'))->where('no_show', 1)->pluck('id');
            foreach($orders as $order) {
                if(!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
                    if(!$order->groupOrders->isEmpty()) {
                        foreach($order->groupOrders as $subOrder) {
                            if(!$subOrder->orderItems->isEmpty()) {
                                foreach($subOrder->orderItems as $orderItem) {
                                    if ($discountTab && $this->isHideProductInDiscountStatistics($orderItem)) {
                                        continue;
                                    }

                                    if(!empty($checkProductIds) && !in_array($orderItem->product_id, $productIds)) {
                                        continue;
                                    }

                                    if(empty($categoryProducts[$orderItem->category_id]['cat_total'])) {
                                        $categoryProducts[$orderItem->category_id]['cat_total'] = 0;
                                    }
                                    if(empty($categoryProducts[$orderItem->category_id]['cat_price'])) {
                                        $categoryProducts[$orderItem->category_id]['cat_price'] = 0;
                                    }
                                    if (empty($categoryProducts[$orderItem->category_id]['cat_total_order'])) {
                                        $categoryProducts[$orderItem->category_id]['cat_total_order'] = 0;
                                    }

                                    $categoryProducts[$orderItem->category_id]['cat_total_order'] = $categoryProducts[$orderItem->category_id]['cat_total_order'] + 1;

                                    $categoryProducts[$orderItem->category_id]['cat_total'] = $categoryProducts[$orderItem->category_id]['cat_total'] + $orderItem->total_number;
                                    //using for manager en/manager/statistic/discount
                                    $totalDiscount = !empty($categoryProducts[$orderItem->category_id]['coupon_discount'][$orderItem->vat_percent]) ? $categoryProducts[$orderItem->category_id]['coupon_discount'][$orderItem->vat_percent] : 0;

                                    if ($discountTab) {
                                        $categoryProducts[$orderItem->category_id]['cat_price'] = $categoryProducts[$orderItem->category_id]['cat_price'] + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);
                                        $categoryProducts[$orderItem->category_id]['coupon_discount'][$orderItem->vat_percent] = $totalDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);
                                    } else {
                                        $categoryProducts[$orderItem->category_id]['cat_price'] = $categoryProducts[$orderItem->category_id]['cat_price'] + ($orderItem->total_price);
                                    }

                                    if(!empty($categoryProducts[$orderItem->category_id]['products'][$orderItem->product_id])) {
                                        array_push($categoryProducts[$orderItem->category_id]['products'][$orderItem->product_id], $orderItem);
                                    } else {
                                        $categoryProducts[$orderItem->category_id]['cat'] = $orderItem->category;
                                        $categoryProducts[$orderItem->category_id]['products'][$orderItem->product_id] = [$orderItem];
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if(!$order->orderItems->isEmpty()) {
                        if ($order->type == \App\Models\Order::TYPE_IN_HOUSE && $noShowOrders->contains($order->parent_id)) {
                            continue;
                        }
                        foreach($order->orderItems as $orderItem) {
                            if ($discountTab && $this->isHideProductInDiscountStatistics($orderItem)) {
                                continue;
                            }
                            if(!empty($checkProductIds) && !in_array($orderItem->product_id, $productIds)) {
                                continue;
                            }

                            if(empty($categoryProducts[$orderItem->category_id]['cat_total'])) {
                                $categoryProducts[$orderItem->category_id]['cat_total'] = 0;
                            }
                            if(empty($categoryProducts[$orderItem->category_id]['cat_price'])) {
                                $categoryProducts[$orderItem->category_id]['cat_price'] = 0;
                            }
                            if (empty($categoryProducts[$orderItem->category_id]['cat_total_order'])) {
                                $categoryProducts[$orderItem->category_id]['cat_total_order'] = 0;
                            }

                            $categoryProducts[$orderItem->category_id]['cat_total'] = $categoryProducts[$orderItem->category_id]['cat_total'] + $orderItem->total_number;
                            $categoryProducts[$orderItem->category_id]['cat_total_order'] = $categoryProducts[$orderItem->category_id]['cat_total_order'] + 1;
                            //using for manager en/manager/statistic/discount
                            $totalDiscount = !empty($categoryProducts[$orderItem->category_id]['coupon_discount'][$orderItem->vat_percent]) ? $categoryProducts[$orderItem->category_id]['coupon_discount'][$orderItem->vat_percent] : 0;

                            if ($discountTab) {
                                $categoryProducts[$orderItem->category_id]['cat_price'] = $categoryProducts[$orderItem->category_id]['cat_price'] + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);
                                $categoryProducts[$orderItem->category_id]['coupon_discount'][$orderItem->vat_percent] = $totalDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);
                            } else {
                                $categoryProducts[$orderItem->category_id]['cat_price'] = $categoryProducts[$orderItem->category_id]['cat_price'] + ($orderItem->total_price);
                            }

                            if(!empty($categoryProducts[$orderItem->category_id]['products'][$orderItem->product_id])) {
                                array_push($categoryProducts[$orderItem->category_id]['products'][$orderItem->product_id], $orderItem);
                            } else {
                                $categoryProducts[$orderItem->category_id]['cat'] = $orderItem->category;
                                $categoryProducts[$orderItem->category_id]['products'][$orderItem->product_id] = [$orderItem];
                            }
                        }
                    }
                }
            }
        }

        return $categoryProducts;
    }

    /**
     * @param $orders
     * @return array
     */
    public function groupByPaymentMethod($orders) {
        $paymentMethods = [];
        $totalOrder = [
            Order::PAYMENT_METHOD_PAID_ONLINE => 0,
            Order::PAYMENT_METHOD_CASH => 0,
            Order::PAYMENT_METHOD_FOR_INVOICE => 0
        ];

        $methodConvert = Order::PAYMENT_METHOD_FOR_INVOICE;

        if(!$orders->isEmpty()) {
            foreach($orders as $order) {
                $hasShipOrService = !empty($order->ship_price) || !empty($order->service_cost);
                $shipAndServiceCost = $order->ship_price + $order->service_cost;
                if(!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE) {
                    if(!$order->groupOrders->isEmpty()) {
                        foreach($order->groupOrders as $subOrder) {
                            //Convert payment method for group order
                            $this->calculateOrderForPaymentMethod($subOrder, $methodConvert, $totalOrder);

                            if(!$subOrder->orderItems->isEmpty()) {
                                foreach($subOrder->orderItems as $orderItem) {
                                    if(empty($paymentMethods[$methodConvert]['payment_total'])) {
                                        $paymentMethods[$methodConvert]['payment_total'] = 0;
                                    }
                                    if(empty($paymentMethods[$methodConvert]['payment_price'])) {
                                        $paymentMethods[$methodConvert]['payment_price'] = 0;
                                    }
                                    if(empty($paymentMethods[$methodConvert]['discount_price'])) {
                                        $paymentMethods[$methodConvert]['discount_price'] = 0;
                                    }

                                    $paymentMethods[$methodConvert]['payment_total'] = $totalOrder[$methodConvert];
                                    $totalDiscount = !empty($paymentMethods[$methodConvert]['coupon_discount'][$orderItem->vat_percent]) ? $paymentMethods[$methodConvert]['coupon_discount'][$orderItem->vat_percent] : 0;
                                    $paymentMethods[$methodConvert]['payment_price'] = $paymentMethods[$methodConvert]['payment_price'] + ($orderItem->total_price);
                                    $paymentMethods[$methodConvert]['coupon_discount'][$orderItem->vat_percent] = $totalDiscount + $orderItem->total_price;

                                    $paymentMethods[$methodConvert]['discount_price'] = $paymentMethods[$methodConvert]['discount_price'] + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);

                                    if(!empty($paymentMethods[$methodConvert]['products'][$orderItem->product_id])) {
                                        array_push($paymentMethods[$methodConvert]['products'][$orderItem->product_id], $orderItem);
                                    } else {
                                        $paymentMethods[$methodConvert]['products'][$orderItem->product_id] = [$orderItem];
                                    }
                                }

                                if ($hasShipOrService) {
                                    $orderItems = $subOrder->orderItems->pluck('vat_percent')->toArray();
                                    $maxPercent = max($orderItems);

                                    // $paymentMethods[$methodConvert]['coupon_discount'][$maxPercent] = $paymentMethods[$methodConvert]['coupon_discount'][$maxPercent] + $order->ship_price;
                                    // @todo verify is this is the correct solution
                                    if(isset($paymentMethods[$methodConvert]['coupon_discount'][$maxPercent])) {
                                        $paymentMethods[$methodConvert]['coupon_discount'][$maxPercent] = $paymentMethods[$methodConvert]['coupon_discount'][$maxPercent] + $shipAndServiceCost;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $this->calculateOrderForPaymentMethod($order, $methodConvert, $totalOrder);
                    if(!$order->orderItems->isEmpty()) {
                        foreach($order->orderItems as $orderItem) {
                            if(empty($paymentMethods[$methodConvert]['payment_total'])) {
                                $paymentMethods[$methodConvert]['payment_total'] = 0;
                            }
                            if(empty($paymentMethods[$methodConvert]['payment_price'])) {
                                $paymentMethods[$methodConvert]['payment_price'] = 0;
                            }
                            if(empty($paymentMethods[$methodConvert]['discount_price'])) {
                                $paymentMethods[$methodConvert]['discount_price'] = 0;
                            }

                            $paymentMethods[$methodConvert]['payment_total'] = $totalOrder[$methodConvert];
                            $totalDiscount = !empty($paymentMethods[$methodConvert]['coupon_discount'][$orderItem->vat_percent]) ? $paymentMethods[$methodConvert]['coupon_discount'][$orderItem->vat_percent] : 0;
                            $paymentMethods[$methodConvert]['payment_price'] = $paymentMethods[$methodConvert]['payment_price'] + ($orderItem->total_price);
                            $paymentMethods[$methodConvert]['coupon_discount'][$orderItem->vat_percent] = $totalDiscount + $orderItem->total_price;

                            $paymentMethods[$methodConvert]['discount_price'] = $paymentMethods[$methodConvert]['discount_price'] + ($orderItem->coupon_discount + $orderItem->redeem_discount);

                            if(!empty($paymentMethods[$methodConvert]['products'][$orderItem->product_id])) {
                                array_push($paymentMethods[$methodConvert]['products'][$orderItem->product_id], $orderItem);
                            } else {
                                $paymentMethods[$methodConvert]['products'][$orderItem->product_id] = [$orderItem];
                            }
                        }

                        if ($hasShipOrService) {
                            $orderItems = $order->orderItems->pluck('vat_percent')->toArray();
                            $maxPercent = max($orderItems);

                            // @todo verify is this is the correct solution - QUICKFIX BY KURT TO MAKE CLIENT HAPPY
                            if(isset($paymentMethods[$methodConvert]['coupon_discount'][$maxPercent])) {
                                $paymentMethods[$methodConvert]['coupon_discount'][$maxPercent] = $paymentMethods[$methodConvert]['coupon_discount'][$maxPercent] + $shipAndServiceCost;
                            }
                        }
                    }
                }

                if ($hasShipOrService) {
                    $paymentMethods[$methodConvert]['payment_price'] = $paymentMethods[$methodConvert]['payment_price'] + $shipAndServiceCost;
                }
            }
        }

        return $paymentMethods;
    }

    protected function isHideProductInDiscountStatistics($orderItem)
    {
        if ((empty($orderItem->coupon_discount)
            && empty($orderItem->group_discount)
            && empty($orderItem->redeem_discount))
            || ((float)($orderItem->coupon_discount) == 0
            && (float)($orderItem->group_discount) == 0
            && (float)($orderItem->redeem_discount) == 0
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Calculate total orders for each payment methods
     *
     * @param $order
     * @param $methodConvert
     * @param $totalOrder
     * @return mixed
     */
    private function calculateOrderForPaymentMethod($order, &$methodConvert, &$totalOrder)
    {
        if ($order->payment_method == SettingPayment::TYPE_MOLLIE || $order->payment_method == SettingPayment::TYPE_PAYCONIQ) {
            $methodConvert = Order::PAYMENT_METHOD_PAID_ONLINE;
            $totalOrder[$methodConvert] += 1;
        } elseif ($order->payment_method == SettingPayment::TYPE_CASH) {
            $methodConvert = Order::PAYMENT_METHOD_CASH;
            $totalOrder[$methodConvert] += 1;
        } else {
            $methodConvert = Order::PAYMENT_METHOD_FOR_INVOICE;
            $totalOrder[$methodConvert] += 1;
        }

        return $totalOrder;
    }
}
