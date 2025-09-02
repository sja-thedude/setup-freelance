@include('layouts.partials.print.top')

@php
    $currency = '€';
@endphp

<div class="row layout-bon-printer bon-printer-payment-method">
    <div class="col-sm-12 col-xs-12">
        <div class="row text-center mgb-20">
            <div class="col-sm-12 col-xs-12">
                <h2 class="bon-printer-title mgb-i-0">
                    @lang('statistic.title')
                </h2>
                <p class="bon-printer-normal mgb-i-0">
                    @lang('statistic.per_payment_method')
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
                        @lang('statistic.turnover_at_vat')
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
                            @php
                                $total_discount = [];
                            @endphp
                            @foreach($perProducts as $paymentId => $item)
                                @if(!empty($item))
                                    <div class="row bp-product">
                                        <div class="col-sm-2 col-xs-2 pdl-i-0">
                                            @if($paymentId == \App\Models\Order::PAYMENT_METHOD_CASH)
                                                @lang('statistic.cash')
                                            @elseif($paymentId == \App\Models\Order::PAYMENT_METHOD_PAID_ONLINE)
                                                @lang('statistic.paid_online')
                                            @else
                                                @lang('statistic.for_invoice')
                                            @endif
                                        </div>
                                        @if(!empty($vats))
                                            @foreach($vats as $vat)
                                                @if(!empty($item['coupon_discount']) && array_key_exists(number_format($vat, 2), $item['coupon_discount']))
                                                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                        {!! $currency.$item['coupon_discount'][number_format($vat, 2)] !!}
                                                    </div>
                                                    @php
                                                        $otherDiscount = !empty($total_discount[number_format($vat, 2)]) ? $total_discount[number_format($vat, 2)] : 0;
                                                        $total_discount[number_format($vat, 2)] = $otherDiscount + $item['coupon_discount'][number_format($vat, 2)];
                                                    @endphp
                                                @else
                                                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                                        {!! $currency !!}0
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0">
                                            {!! $currency !!}{!! !empty($item['payment_price']) ? $item['payment_price'] : 0 !!}
                                        </div>
                                        <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                                            {!! !empty($item['payment_total']) ? $item['payment_total'] : 0 !!}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
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
                        {!! $currency.$totalInclDiscount !!}
                    </div>
                    <div class="col-sm-2 col-xs-2 text-center pdl-i-0 pdr-i-0">
                        {!! $totalCart !!}
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