@php
    $currency = '€';
    $amountSell = 0;
    $totalRevenue = 0;
@endphp
<div class="print-statistic">
    <div class="print-header" style="display: none;">
        <div class="pull-left ir-title">
            <h2 class="ir-h2">
                @lang('statistic.title') {{$group->name}}
            </h2>
            <p class="statistic-sub-title">@lang('statistic.per_product')</p>
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
                    <div class="col-item col-sm-4 col-xs-12">
                        <span>@lang('statistic.tbl_title')</span>
                    </div>
                    <div class="col-item col-sm-4 col-xs-12 text-center">
                        <span>@lang('statistic.tbl_amount_sell')</span>
                    </div>
                    <div class="col-item col-sm-4 col-xs-12 text-center">
                        <span>@lang('statistic.tbl_total_revenue')</span>
                    </div>
                </div>
            </div>
            <div class="list-body list-body-manager overflow_hidden">
                <div class="row row-statistic row-item pd-20 pdb-i-0">
                    <div class="col-sm-12 col-xs-12">
                        @if(!empty($perProducts))
                            @foreach($perProducts as $catId => $item)
                                @if(!empty($item))
                                    @php
                                        $products = [];

                                        if(!empty($item['products'])) {
                                            foreach($item['products'] as $productId => $orderItems) {
                                                if(!empty($orderItems)) {
                                                    foreach($orderItems as $orderItem) {
                                                        $amountSell = $amountSell + $orderItem->total_number;
                                                        $totalRevenue = $totalRevenue + ($orderItem->total_price);
                                                        $productTotalNumber = !empty($products[$productId]['product_total']) ? $products[$productId]['product_total'] : 0;
                                                        $productTotalPrice = !empty($products[$productId]['product_price']) ? $products[$productId]['product_price'] : 0;
                                                        $products[$productId]['product'] = $orderItem->product;
                                                        $products[$productId]['product_total'] = $productTotalNumber + $orderItem->total_number;
                                                        $products[$productId]['product_price'] = $productTotalPrice + $orderItem->total_price;
                                                        $totalDiscount = $totalDiscount + $orderItem->coupon_discount + $orderItem->redeem_discount + $orderItem->group_discount;
                                                    }
                                                }
                                            }
                                        }

                                        $totalInclDiscount = $totalInclDiscount + (!empty($item['cat_price']) ? $item['cat_price'] : 0);
                                    @endphp

                                    <div class="wrap-collapse">
                                        <div class="row row-statistic row-cat collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{$catId}}" aria-expanded="false" aria-controls="collapse-{{$catId}}">
                                            <div class="col-item col-sm-4 col-xs-12">
                                                <span>{!! !empty($item['cat']) ? ucfirst(strtolower($item['cat']->name)) : '' !!}</span>
                                            </div>
                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                <span>{!! !empty($item['cat_total']) ? $item['cat_total'] : 0 !!}</span>
                                            </div>
                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                <span>{!! $currency !!}{!! !empty($item['cat_price']) ? $item['cat_price'] : 0 !!}</span>
                                            </div>
                                        </div>

                                        @if(!empty($products))
                                            <div id="collapse-{{$catId}}" class="row row-statistic row-product panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-{{$catId}}">
                                                <div class="col-sm-12 col-xs-12">
                                                    @foreach($products as $product)
                                                        <div class="row row-statistic">
                                                            <div class="col-item col-sm-4 col-xs-12">
                                                                <span>{!! !empty($product['product']) ? ucfirst(strtolower($product['product']->name)) : '' !!}</span>
                                                            </div>
                                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                                <span class="font-w-normal">{!! !empty($product['product_total']) ? $product['product_total'] : 0 !!}</span>
                                                            </div>
                                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                                <span class="font-w-normal">{!! $currency !!}{!! !empty($product['product_price']) ? $product['product_price'] : 0 !!}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach

                            @if(empty($keyword))
                                @php
                                    $totalInclDiscount += $hasShipOrders->sum('ship_price') + $calculateServiceCost['total_revenue'];
                                    $amountSold = $hasShipOrders->count() + $calculateServiceCost['amount'];
                                    $totalRevenueMiscellaneous = $hasShipOrders->sum('ship_price') + $calculateServiceCost['total_revenue'];
                                    $amountSell += $calculateServiceCost['amount'];
                                    $totalRevenue += $calculateServiceCost['total_revenue'];
                                @endphp
                                <div class="row row-statistic row-cat collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-fake" aria-expanded="false" aria-controls="collapse-fake">
                                    <div class="col-item col-sm-4 col-xs-12">
                                        <span>{!! ucfirst(strtolower(trans('statistic.diverse'))) !!}</span>
                                    </div>
                                    <div class="col-item col-sm-4 col-xs-12 text-center">
                                        <span>{!! $amountSold !!}</span>
                                    </div>
                                    <div class="col-item col-sm-4 col-xs-12 text-center">
                                        <span>{!! $currency.$totalRevenueMiscellaneous !!}</span>
                                    </div>
                                </div>
                                <div id="collapse-fake" class="row row-statistic row-product panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-fake">
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="row row-statistic">
                                            <div class="col-item col-sm-4 col-xs-12">
                                                <span>{!! ucfirst(strtolower(trans('statistic.leverkost'))) !!}</span>
                                            </div>
                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                <span class="font-w-normal">{!! $hasShipOrders->count() !!}</span>
                                            </div>
                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                <span class="font-w-normal">{!! $currency.$hasShipOrders->sum('ship_price') !!}</span>
                                            </div>
                                        </div>
                                        <div class="row row-statistic">
                                            <div class="col-item col-sm-4 col-xs-12">
                                                <span>{!! ucfirst(strtolower(trans('setting.preferences.service_cost'))) !!}</span>
                                            </div>
                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                <span class="font-w-normal">{!! $calculateServiceCost['amount'] !!}</span>
                                            </div>
                                            <div class="col-item col-sm-4 col-xs-12 text-center">
                                                <span class="font-w-normal">{!! $currency.$calculateServiceCost['total_revenue'] !!}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="list-footer pdl-i-30 pdr-i-30 pdb-i-30">
                <hr class="mgt-0 hidden-print no-print"/>
                <div class="row">
                    <div class="col-item col-sm-4 col-xs-12">
                        <span>@lang('statistic.total'):</span>
                    </div>
                    @if(empty($keyword))
                        <div class="col-item col-sm-4 col-xs-12 text-center">
                            <span class="sf-number">{!! $amountSell + $hasShipOrders->count() !!}</span>
                        </div>
                        <div class="col-item col-sm-4 col-xs-12 text-center">
                            <span class="sf-number">{!! $currency.($totalRevenue + $hasShipOrders->sum('ship_price')) !!}</span>
                        </div>
                    @else
                        <div class="col-item col-sm-4 col-xs-12 text-center">
                            <span class="sf-number">{!! $amountSell !!}</span>
                        </div>
                        <div class="col-item col-sm-4 col-xs-12 text-center">
                            <span class="sf-number">{!! $currency.$totalRevenue !!}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="statistic-only-total display-flex">
                    <div class="statistic-item hidden-print no-print">
                        <div class="s-number">
                            {!! $currency.$totalDiscount !!}
                        </div>
                        <div class="s-description">
                            @lang('statistic.discount_this_time')
                        </div>
                    </div>

                    <div class="statistic-item">
                        <div class="s-number">
                            {!! $currency.$totalInclDiscount !!}
                        </div>
                        <div class="s-description">
                            @lang('statistic.total_incl_discount')
                        </div>
                    </div>

                    <div class="statistic-item" style="display: none;">
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