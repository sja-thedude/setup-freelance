@include('layouts.partials.print.top')

@php
    $currency = '€';
    $amountSell = 0;
    $totalRevenue = 0;
@endphp

<div class="row layout-bon-printer bon-printer-discount">
    <div class="col-sm-12 col-xs-12">
        <div class="row text-center mgb-20">
            <div class="col-sm-12 col-xs-12">
                <h2 class="bon-printer-title mgb-i-0">
                    @lang('statistic.title') {{$group->name}}
                </h2>
                <p class="bon-printer-normal mgb-i-0">
                    @lang('statistic.discount')
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
                <div class="row bp-list-header mgb-5 pdb-5">
                    <div class="col-sm-2 col-xs-2 pdl-i-0">
                        @lang('statistic.discount_at_vat')
                    </div>
                    @if(!empty($vats))
                        @foreach($vats as $vat)
                            <div class="col-sm-2 col-xs-2 text-center pdl-i-0">{!! $vat !!}%</div>
                        @endforeach
                    @endif
                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                        @lang('statistic.tot')
                    </div>
                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                        @lang('statistic.bon_printer_orders')
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
                                                        if (!empty($orderItem->coupon_discount) || !empty($orderItem->redeem_discount)) {
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
                                                            $products[$productId]['product_discount'] = $productDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount);
                                                            $products[$productId]['coupon_discount'][$orderItem->vat_percent] = $_totalDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount);

                                                            $total_discount[$orderItem->vat_percent] = $otherDiscount + ($orderItem->coupon_discount + $orderItem->redeem_discount);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="row bp-cat">
                                        <div class="col-sm-2 col-xs-2 pdl-i-0">
                                            {!! !empty($item['cat']) ? $item['cat']->name : '' !!}
                                        </div>
                                        @if(!empty($vats))
                                            @foreach($vats as $vat)
                                                @if(!empty($item['coupon_discount']) && array_key_exists(number_format($vat, 2), $item['coupon_discount']))
                                                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                        {!! $currency.$item['coupon_discount'][number_format($vat, 2)] !!}
                                                    </div>
                                                @else
                                                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                        {!! $currency !!}0
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                            {!! $currency !!}{!! !empty($item['cat_price']) ? $item['cat_price'] : 0 !!}
                                        </div>
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                                            {!! !empty($item['cat_total']) ? $item['cat_total'] : 0 !!}
                                        </div>
                                    </div>

                                    @if(!empty($products))
                                        @foreach($products as $product)
                                            <div class="row bp-product">
                                                <div class="col-sm-2 col-xs-2 pdl-i-0">
                                                    {!! !empty($product['product']) ? $product['product']->name : '' !!}
                                                </div>
                                                @if(!empty($vats))
                                                    @foreach($vats as $vat)
                                                        @if(!empty($product['coupon_discount']) && array_key_exists(number_format($vat, 2), $product['coupon_discount']))
                                                            <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                                {!! $currency.$product['coupon_discount'][number_format($vat, 2)] !!}
                                                            </div>
                                                        @else
                                                            <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                                {!! $currency !!}0
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                    {!! $currency !!}{!! !empty($product['product_discount']) ? $product['product_discount'] : 0 !!}
                                                </div>
                                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                                                    {!! !empty($product['product_total']) ? $product['product_total'] : '' !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                            @endforeach

                            <div class="row bp-cat">
                                <div class="col-sm-2 col-xs-2 pdl-i-0">
                                    @lang('statistic.physical_product')
                                </div>
                                @if(!empty($vats))
                                    @foreach($vats as $vat)
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                            {!! $currency !!}0
                                        </div>
                                    @endforeach
                                @endif

                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                    {!! $currency !!}0
                                </div>
                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                                    {!! $totalReward !!}
                                </div>
                            </div>

                            @if(!empty($rewards))
                                @foreach($rewards as $reward)
                                    <div class="row bp-product">
                                        <div class="col-sm-2 col-xs-2 pdl-i-0">
                                            {!! $reward['reward_data']['title'] !!}
                                        </div>
                                        @if(!empty($vats))
                                            @foreach($vats as $vat)
                                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                    {!! $currency !!}0
                                                </div>
                                            @endforeach
                                        @endif
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                            {!! $currency !!}0
                                        </div>
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                                            {!! $reward['totalReward'] !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
                <div class="row bp-list-footer mgb-30">
                    <div class="col-sm-2 col-xs-2 pdl-i-0">
                        @lang('statistic.total'):
                    </div>
                    @if(!empty($vats))
                        @foreach($vats as $vat)
                            @if(!empty($total_discount) && array_key_exists(number_format($vat, 2), $total_discount))
                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                    {!! $currency.$total_discount[number_format($vat, 2)] !!}
                                </div>
                            @else
                                <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                    {!! $currency !!}0
                                </div>
                            @endif
                        @endforeach
                    @endif
                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                        {!! $currency.$totalDiscount !!}
                    </div>
                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                        {!! $amountSell + $totalReward !!}
                    </div>
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
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.print.bottom')