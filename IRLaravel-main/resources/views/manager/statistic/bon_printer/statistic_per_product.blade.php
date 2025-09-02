@include('layouts.partials.print.top')

@php
    $currency = '€';
    $amountSell = 0;
    $totalRevenue = 0;
@endphp

<div class="row layout-bon-printer bon-printer-per-product">
    <div class="col-sm-12 col-xs-12">
        <div class="row text-center mgb-20">
            <div class="col-sm-12 col-xs-12">
                <h2 class="bon-printer-title mgb-i-0">
                    @lang('statistic.title')
                </h2>
                <p class="bon-printer-normal mgb-i-0">
                    @lang('statistic.per_product')
                </p>
                <p class="bon-printer-normal mgb-i-0">
                    {!! $filterDate !!}
                </p>
            </div>
        </div>
        <div class="row text-center mgb-40">
            <div class="col-sm-12 col-xs-12">
                <h4 class="bon-printer-restaurant mgb-i-0">
                    {!! $tmpWorkspace->name !!}
                </h4>
                <p class="bon-printer-normal mgb-i-0">
                    {!! $tmpWorkspace->btw_nr !!}
                </p>
            </div>
        </div>
        <div class="row bon-printer-list">
            <div class="col-sm-12 col-xs-12">
                <div class="row bp-list-header">
                    <div class="col-sm-9 col-xs-9 pdl-i-0">
                        @lang('statistic.tbl_number_and_product')
                    </div>
                    <div class="col-sm-3 col-xs-3 pdr-i-0">
                        @lang('statistic.tbl_total_revenue')
                    </div>
                </div>
                <div class="row bp-list-body">
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

                                    <div class="row bp-cat">
                                        <div class="col-sm-9 col-xs-9 pdl-i-0">
                                            {!! !empty($item['cat_total']) ? $item['cat_total'] : 0 !!} x
                                            {!! !empty($item['cat']) ? $item['cat']->name : '' !!}
                                        </div>
                                        <div class="col-sm-3 col-xs-3 pdr-i-0">
                                            {!! $currency !!}{!! !empty($item['cat_price']) ? $item['cat_price'] : 0 !!}
                                        </div>
                                    </div>

                                    @if(!empty($products))
                                        @foreach($products as $product)
                                            <div class="row bp-product">
                                                <div class="col-sm-9 col-xs-9 pdl-i-0">
                                                    {!! !empty($product['product_total']) ? $product['product_total'] : 0 !!} x
                                                    {!! !empty($product['product']) ? $product['product']->name : '' !!}
                                                </div>
                                                <div class="col-sm-3 col-xs-3 pdr-i-0">
                                                    {!! $currency !!}{!! !empty($product['product_price']) ? $product['product_price'] : 0 !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
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
                                <div class="row bp-cat">
                                    <div class="col-sm-9 col-xs-9 pdl-i-0">
                                        {!! $amountSold !!} x @lang('statistic.diverse')
                                    </div>
                                    <div class="col-sm-3 col-xs-3 pdr-i-0">
                                        {!! $currency.$totalRevenueMiscellaneous !!}
                                    </div>
                                </div>
                                <div class="row bp-product">
                                    <div class="col-sm-9 col-xs-9 pdl-i-0">
                                        {!! $hasShipOrders->count() !!} x @lang('statistic.leverkost')
                                    </div>
                                    <div class="col-sm-3 col-xs-3 pdr-i-0">
                                        {!! $currency.$hasShipOrders->sum('ship_price') !!}
                                    </div>
                                </div>
                                <div class="row bp-product">
                                    <div class="col-sm-9 col-xs-9 pdl-i-0">
                                        {!! $calculateServiceCost['amount'] !!} x @lang('setting.preferences.service_cost')
                                    </div>
                                    <div class="col-sm-3 col-xs-3 pdr-i-0">
                                        {!! $currency.$calculateServiceCost['total_revenue'] !!}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="row bp-list-footer mgb-30">
                    @if(empty($keyword))
                        <div class="col-sm-9 col-xs-9 pdl-i-0">
                            <div class="pull-left">
                                {!! $amountSell + $hasShipOrders->count() !!}
                            </div>
                            <div class="pull-right text-right">
                                @lang('statistic.total'):
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-3 pdr-i-0">
                            {!! $currency.($totalRevenue + $hasShipOrders->sum('ship_price')) !!}
                        </div>
                    @else
                        <div class="col-sm-9 col-xs-9 pdl-i-0">
                            <div class="pull-left">
                                {!! $amountSell !!}
                            </div>
                            <div class="pull-right text-right">
                                @lang('statistic.total'):
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-3 pdr-i-0">
                            {!! $currency.$totalRevenue !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row bon-printer-total">
            <div class="col-sm-12 col-xs-12">
                <div class="row pb-total-item text-center">
                    <div class="col-sm-12 col-xs-12">
                        <div class="s-number">
                            {!! $currency.$totalDiscount !!}
                        </div>
                        <div class="s-description">
                            @lang('statistic.discount_this_time')
                        </div>
                    </div>
                </div>
                <div class="row pb-total-item text-center">
                    <div class="col-sm-12 col-xs-12">
                        <div class="s-number">
                            {!! $currency.$totalInclDiscount !!}
                        </div>
                        <div class="s-description">
                            @lang('statistic.total_incl_discount')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.print.bottom')