@php
    $currency = '€';
@endphp
<div class="print-statistic">
    <div class="print-header" style="display: none;">
        <div class="pull-left ir-title">
            <h2 class="ir-h2">
                @lang('statistic.title')
            </h2>
            <p class="statistic-sub-title">@lang('statistic.per_payment_method')</p>
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
                        <span style="color: #BFBFBF">@lang('statistic.turnover_at_vat')</span>
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
                            @php
                                $total_discount = [];
                            @endphp
                            @foreach($perProducts as $paymentId => $item)
                                @if(!empty($item))
                                    <div class="row row-statistic row-product payment-method">
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="row row-statistic">
                                                <div class="col-item col-sm-2 col-xs-12">
                                                    @if($paymentId == \App\Models\Order::PAYMENT_METHOD_CASH)
                                                        <span>{!! ucfirst(strtolower(trans('statistic.cash'))) !!}</span>
                                                    @elseif($paymentId == \App\Models\Order::PAYMENT_METHOD_PAID_ONLINE)
                                                        <span>{!! ucfirst(strtolower(trans('statistic.paid_online'))) !!}</span>
                                                    @else
                                                        <span>{!! ucfirst(strtolower(trans('statistic.for_invoice'))) !!}</span>
                                                    @endif
                                                </div>
                                                @if(!empty($vats))
                                                    @foreach($vats as $vat)
                                                        @if(!empty($item['coupon_discount']) && array_key_exists(number_format($vat, 2), $item['coupon_discount']))
                                                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                                                @php
                                                                    $couponDiscount = !empty($item['coupon_discount'][number_format($vat, 2)]) && $item['coupon_discount'][number_format($vat, 2)] > 0
                                                                        ? $item['coupon_discount'][number_format($vat, 2)] : 0;
                                                                @endphp
                                                                <span class="font-w-normal">{!! $currency.$couponDiscount !!}</span>
                                                                @php
                                                                    $otherDiscount = (!empty($total_discount[number_format($vat, 2)]) && $total_discount[number_format($vat, 2)] > 0)
                                                                        ? $total_discount[number_format($vat, 2)] : 0;
                                                                    $total_discount[number_format($vat, 2)] = $otherDiscount + $couponDiscount;
                                                                @endphp
                                                            </div>
                                                        @else
                                                            <div class="col-item col-sm-1 col-xs-12 text-center">
                                                                <span class="font-w-normal">{!! $currency !!}0</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <div class="col-item col-sm-1 col-xs-12 text-center">
                                                    <span class="font-w-normal">{!! $currency !!}{!! (!empty($item['payment_price']) && $item['payment_price'] > 0) ? $item['payment_price'] : 0 !!}</span>
                                                </div>
                                                <div class="col-item col-sm-2 col-xs-12 text-center">
                                                    <span class="font-w-normal">{!! !empty($item['payment_total']) ? $item['payment_total'] : 0 !!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
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
                            @if(!empty($total_discount) && array_key_exists(number_format($vat, 2), $total_discount))
                                <div class="col-item col-sm-1 col-xs-12 text-center">
                                    <span>{!! $currency.$total_discount[number_format($vat, 2)] !!}</span>
                                </div>
                            @else
                                <div class="col-item col-sm-1 col-xs-12 text-center">
                                    <span>{!! $currency !!}0</span>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    <div class="col-item col-sm-1 col-xs-12 text-center">
                        <span class="sf-number">{!! $currency.$totalInclDiscount !!}</span>
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        <span class="sf-number">{!! $totalCart !!}</span>
                    </div>
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
