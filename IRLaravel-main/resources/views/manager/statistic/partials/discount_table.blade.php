@php
    $currency = '€';
    $amountSell = 0;
    $totalOrder = 0;
    $totalRevenue = 0;
@endphp
<div class="print-statistic">
    <div class="print-header" style="display: none;">
        <div class="pull-left ir-title">
            <h2 class="ir-h2">
                @lang('statistic.title')
            </h2>
            <p class="statistic-sub-title">@lang('statistic.discount')</p>
            <p class="statistic-date"></p>
        </div>
        <div class="pull-right ir-right text-right">
            <h3>{!! $tmpWorkspace->name !!}</h3>
            <p>{!! $tmpWorkspace->btw_nr !!}</p>
        </div>
    </div>
    <div class="ir-content">
        <div class="list-responsive mgt-30">
            <div class="list-header list-header-manager pdl-i-30 pdr-i-30">
                <div class="row">
                    <div class="col-item col-sm-2 col-xs-12">
                        <span style="color: #BFBFBF">@lang('statistic.discount_at_vat')</span>
                    </div>
                    @if(!empty($vats))
                        @foreach($vats as $vat)
                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                <span>{{$vat}}%</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="col-item col-sm-1 col-xs-12 text-center">
                        <span>@lang('statistic.total')</span>
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        <span>@lang('statistic.number_of_orders')</span>
                    </div>
                </div>
            </div>
            <div class="list-body list-body-manager overflow_hidden">
                <div class="row row-statistic row-item pd-20 pdb-i-0">
                    <div class="col-sm-12 col-xs-12">
                        @if(!empty($perProducts))
                            @foreach($perProducts as $catId => $item)
                                @if(!empty($item))
                                    <div class="wrap-collapse">
                                        @php
                                            $products = [];
                                            if(!empty($item['products'])) {
                                                foreach($item['products'] as $productId => $orderItems) {
                                                    if(!empty($orderItems)) {
                                                        $totalOrderOfProduct = count(array_unique(collect($orderItems)->pluck('order_id')->all()));
                                                        $totalOrder = $totalOrder + $totalOrderOfProduct;
                                                        $products[$productId]['total_order'] = $totalOrderOfProduct;
                                                        foreach($orderItems as $orderItem) {
                                                            if (!empty($orderItem->coupon_discount) || !empty($orderItem->redeem_discount) || !empty($orderItem->group_discount)) {
                                                                $amountSell = $amountSell + $orderItem->total_number;
                                                                $totalRevenue = $totalRevenue + ($orderItem->total_number * $orderItem->price);
                                                                $productTotalNumber = !empty($products[$productId]['product_total']) ? $products[$productId]['product_total'] : 0;
                                                                $productTotalPrice = !empty($products[$productId]['product_price']) ? $products[$productId]['product_price'] : 0;
                                                                $productDiscount = !empty($products[$productId]['product_discount']) ? $products[$productId]['product_discount'] : 0;
                                                                $otherDiscount = !empty($total_discount[$orderItem->vat_percent]) ? $total_discount[$orderItem->vat_percent] : 0;
                                                                $_totalDiscount = !empty($products[$productId]['coupon_discount'][$orderItem->vat_percent]) ? $products[$productId]['coupon_discount'][$orderItem->vat_percent] : 0;

                                                                $products[$productId]['product'] = $orderItem->product;
                                                                $products[$productId]['product_total'] = $productTotalNumber + $orderItem->total_number;
                                                                $products[$productId]['product_price'] = $productTotalPrice + ($orderItem->total_number * $orderItem->price);
                                                                $products[$productId]['product_discount'] = $productDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);
                                                                $products[$productId]['coupon_discount'][$orderItem->vat_percent] = $_totalDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);

                                                                $total_discount[$orderItem->vat_percent] = $otherDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div class="row row-statistic row-cat collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{$catId}}" aria-expanded="false" aria-controls="collapse-{{$catId}}">
                                            <div class="col-item col-sm-2 col-xs-12">
                                                <span>{!! !empty($item['cat']) ? ucfirst(strtolower($item['cat']->name)) : '' !!}</span>
                                            </div>
                                            @if(!empty($vats))
                                                @foreach($vats as $vat)
                                                    <div class="col-item col-sm-1 col-xs-12 text-center">
                                                        @if(!empty($item['coupon_discount']) && array_key_exists(number_format($vat, 2), $item['coupon_discount']))
                                                            <span>{!! $currency.$item['coupon_discount'][number_format($vat, 2)] !!}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                                @if(!empty($item['cat_price']))
                                                    <span>{!! $currency !!}{!! $item['cat_price'] !!}</span>
                                                @endif
                                            </div>
                                            <div class="col-item col-sm-2 col-xs-12 text-center">
                                                <span>{!! !empty($item['cat_total_order']) ? $item['cat_total_order'] : 0 !!}</span>
                                            </div>
                                        </div>

                                        @if(!empty($products))
                                            <div id="collapse-{{$catId}}" class="row row-statistic row-product panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-{{$catId}}">
                                                <div class="col-sm-12 col-xs-12">
                                                    @foreach($products as $product)
                                                        <div class="row row-statistic">
                                                            <div class="col-item col-sm-2 col-xs-12">
                                                                @if (!empty($product['product']))
                                                                    <span>{!! ucfirst(strtolower($product['product']->name)) !!}</span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($vats))
                                                                @foreach($vats as $vat)
                                                                    <div class="col-item col-sm-1 col-xs-12 text-center">
                                                                        @if(!empty($product['coupon_discount']) && array_key_exists(number_format($vat, 2), $product['coupon_discount']) && !empty($product['coupon_discount'][number_format($vat, 2)]))
                                                                            <span class="font-w-normal">{!! $currency.$product['coupon_discount'][number_format($vat, 2)] !!}</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                                                @if (!empty($product['product_discount']))
                                                                    <span class="font-w-normal">{!! $currency !!}{!! $product['product_discount'] !!}</span>
                                                                @endif
                                                            </div>
                                                            <div class="col-item col-sm-2 col-xs-12 text-center">
                                                                <span class="font-w-normal">{!! !empty($product['total_order']) ? $product['total_order'] : '' !!}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        <div class="row row-statistic row-cat collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-fake" aria-expanded="false" aria-controls="collapse-fake">
                            <div class="col-item col-sm-2 col-xs-12">
                                <span>{!! ucfirst(strtolower(trans('statistic.physical_product'))) !!}</span>
                            </div>
                            @if(!empty($vats))
                                @foreach($vats as $vat)
                                    <div class="col-item col-sm-1 col-xs-12 text-center">

                                    </div>
                                @endforeach
                            @endif
                            <div class="col-item col-sm-1 col-xs-12 text-center">

                            </div>
                            <div class="col-item col-sm-2 col-xs-12 text-center">
                                <span>{{$totalReward}}</span>
                            </div>
                        </div>

                        @if(!empty($rewards))
                            <div id="collapse-fake" class="row row-statistic row-product panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-fake">
                                <div class="col-sm-12 col-xs-12">
                                    @foreach($rewards as $reward)
                                        <div class="row row-statistic">
                                            <div class="col-item col-sm-2 col-xs-12">
                                                <span>{!! ucfirst(strtolower($reward['reward_data']['title'])) !!}</span>
                                            </div>
                                            @if(!empty($vats))
                                                @foreach($vats as $vat)
                                                    <div class="col-item col-sm-1 col-xs-12 text-center">
                                                        <span class="font-w-normal"></span>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                                <span class="font-w-normal"></span>
                                            </div>
                                            <div class="col-item col-sm-2 col-xs-12 text-center">
                                                <span class="font-w-normal">{{$reward['totalReward']}}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="list-footer pdl-i-30 pdr-i-30 pdb-i-30">
                <hr class="mgt-0 hidden-print no-print"/>
                <div class="row">
                    <div class="col-item col-sm-2 col-xs-12">
                        <span>@lang('statistic.total'):</span>
                    </div>
                    @if(!empty($vats))
                        @foreach($vats as $vat)
                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                @if(!empty($total_discount) && array_key_exists(number_format($vat, 2), $total_discount))
                                    <span>{!! $currency.$total_discount[number_format($vat, 2)] !!}</span>
                                @endif
                            </div>
                        @endforeach
                    @endif
                    <div class="col-item col-sm-1 col-xs-12 text-center">
                        <span class="sf-number">{!! $currency.$totalDiscount !!}</span>
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        <span class="sf-number">{!! $totalOrder + $totalReward !!}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="statistic-only-total display-flex">
                    <div class="statistic-item opacity-0"></div>
                    <div class="statistic-item">
                        <div class="s-number">
                            {!! $currency.$totalDiscount !!}
                        </div>
                        <div class="s-description">
                            @lang('statistic.discount_this_time')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>