@include('layouts.partials.print.top')

@php
    $restaurant = !empty($order->workspace) ? $order->workspace : null;
    $currency = '€';
    $vatItems = [];
    $vatEx = 0;
    $vatPrice = 0;
    $vatIn = 0;
    $highestVat = 0;
@endphp

<div class="print-area print-kassabon-html" data-print_id="{!! $order->id !!}">
    <div class="print-main-content print-kassabon-content mgb-30">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                @if(!empty($restaurant))
                    <div class="row p-res-name mgb-20">
                        <div class="col-sm-12 col-xs-12 text-center">
                            {!! $restaurant->name !!}
                        </div>
                    </div>
                    <div class="row p-res-des">
                        <div class="col-sm-12 col-xs-12 text-center">
                            {!! $restaurant->address !!}
                        </div>
                    </div>
                    <div class="row p-res-des">
                        <div class="col-sm-12 col-xs-12 text-center">
                            {!! $restaurant->gsm !!}

                        </div>
                    </div>
                    <div class="row p-res-des mgb-20">
                        <div class="col-sm-12 col-xs-12 text-center">
                            {!! $restaurant->btw_nr !!}
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-center p-client-date">
                        @if($order->is_test_account)
                            @lang('order.test_order') 
                        @else 
                            {!! $order->client_name !!} - #{!! $order->daily_id_display !!}
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-center p-client-date">
                        <span>
                            @php
                                if($order->type_convert == \App\Models\Order::TYPE_IN_HOUSE) {
                                    $orderTime = $order->created_at;
                                } else {
                                    $orderTime = $order->gereed;
                                }

                                $dateShow = Helper::convertDateTimeToTimezone($orderTime, $timezone);
                                $dayOfWeek = \Carbon\Carbon::parse($dateShow)->dayOfWeek;
                            @endphp
                            {!! implode(' ', [ucfirst(trans('common.days.'.$dayOfWeek)), date('d/m/Y H:i', strtotime($dateShow))]) !!}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($order->print_kassabon))
            <div class="row border-line mgt-20">
                <div class="col-sm-12 col-xs-12">
                    @foreach($order->print_kassabon as $productArr)
                        @php
                            $productOrderItem = $productArr[0];
                            $productNumber = 0;
                            $productPrice = 0;
                            $productLevel = [];
                            $productName = '';
                            $productOrderOptionItems = [];
                            $metaOrderItem = json_decode($productOrderItem->metas, true);

                            if(!empty($metaOrderItem['product'])) {
                                $productId = !empty($productOrderItem->product) ? $productOrderItem->product->id : $metaOrderItem['product']['id'];
                                $productNameOption = !empty($productOrderItem->product) ? $productOrderItem->product->name : $metaOrderItem['product']['name'];
                                $productName = collect($productNames)
                                    ->where('product_id', $productId)
                                    ->where('locale', $locale)
                                    ->pluck('name')->first() ?? $productNameOption;
                            }

                            foreach($productArr as $productItem) {
                                if(!$productItem->optionItems->isEmpty()) {
                                    foreach($productItem->optionItems as $optionItem) {
                                        if(!empty($productOrderOptionItems[$optionItem->optie_id])) {
                                            array_push($productOrderOptionItems[$optionItem->optie_id], $optionItem);
                                        } else {
                                            $productOrderOptionItems[$optionItem->optie_id] = [$optionItem];
                                        }
                                    }
                                }

                                $productNumber = $productNumber + $productItem->total_number;
                                $productPrice = $productPrice + $productItem->subtotal;

                                if(!empty($productItem->vat_percent)) {
                                    $vatPercent = $productItem->vat_percent;

                                    if($vatPercent > $highestVat) {
                                        $highestVat = $vatPercent;
                                    }

                                    if($productItem->vat_percent == (int)$productItem->vat_percent) {
                                        $vatPercent = (int)$productItem->vat_percent;
                                    }

                                    if(!empty(config('common.vat_level')[$vatPercent]) &&
                                    !in_array(config('common.vat_level')[$vatPercent], $productLevel)) {
                                        $productLevel[] = config('common.vat_level')[$vatPercent];
                                    } else {
                                        $productLevel[] = 'D';
                                    }

                                    if(!empty($vatItems[$vatPercent])) {
                                        $vatItems[$vatPercent]['btw_in_price'] = $vatItems[$vatPercent]['btw_in_price'] + $productItem->total_price;
                                    } else {
                                        $vatItems[$vatPercent] = [
                                            'btw_percent' => $vatPercent,
                                            'btw_in_price' => $productItem->total_price
                                        ];
                                    }
                                } else {
                                    if(!empty($vatItems[0])) {
                                        $vatItems[0]['btw_in_price'] = $vatItems[0]['btw_in_price'] + $productItem->total_price;
                                    } else {
                                        $vatItems[0] = [
                                            'btw_percent' => 0,
                                            'btw_in_price' => $productItem->total_price
                                        ];
                                    }
                                }
                            }
                        @endphp
                        <div class="row mgt-10">
                            <div class="col-sm-12 col-xs-12">
                                <div class="pull-left text-left p-product-name">
                                    {!! $productNumber !!}x {!! $productName !!}
                                </div>
                                <div class="pull-right text-right p-product-name">
                                    {!! $currency.number_format((float)(!empty($productPrice) ? $productPrice : 0), 2, '.', '') !!} {!! implode(', ', $productLevel) !!}
                                </div>
                                @if(!empty($productOrderOptionItems))
                                    <div class="row print-product-option p-product-item">
                                        @foreach($productOrderOptionItems as $optionItems)
                                            @php
                                                $optionItemPrice = 0;
                                            @endphp
                                            <div class="col-sm-12 col-xs-12 mgt-5">
                                                <div class="pull-left text-left">
                                                    @foreach($optionItems as $key => $optionItem)
                                                        @php
                                                            $optionItemInfo = json_decode($optionItem->metas, true);
                                                            $optionItemPrice = $optionItemPrice + $optionItem->price;
                                                            $zonder = !empty($optionItemInfo['option'][0]['is_ingredient_deletion']);
                                                            $optionItemName = !empty($optionItemInfo['option_item']['name']) ? $optionItemInfo['option_item']['name'] : '';
                                                        @endphp
                                                        @if(!empty($optionItemInfo))
                                                            @if(!empty($key))
                                                                @if(!empty($zonder))
                                                                    / <strong>Z</strong> {!! $optionItemName !!}
                                                                @else
                                                                    / {!! $optionItemName !!}
                                                                @endif
                                                            @else
                                                                @if(!empty($zonder))
                                                                    <strong>Z</strong> {!! $optionItemName !!}
                                                                @else
                                                                    {!! $optionItemName !!}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="row border-line mgt-20">
            <div class="col-sm-12 col-xs-12">
                @if(($order->type_convert == \App\Models\Order::TYPE_DELIVERY) ||
                    !empty($order->calculate_coupon_discount) ||
                    !empty($order->calculate_redeem_discount) ||
                    !empty($order->calculate_service_cost))
                    <div class="row mgt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="pull-left text-left p-subtotal">
                                @lang('order.sub_total'):
                            </div>
                            <div class="pull-right text-right p-normal">
                                {!! $currency.number_format((float)(!empty($order->calculate_subtotal) ? $order->calculate_subtotal : 0), 2, '.', '') !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if(!empty($order->calculate_coupon_discount) || !empty($order->calculate_redeem_discount))
                    <div class="row mgt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="pull-left text-left p-subtotal">
                                @lang('order.coupon_discount'):
                            </div>
                            <div class="pull-right text-right p-normal">
                                @php
                                    $totalDiscount = $order->calculate_coupon_discount + $order->calculate_redeem_discount;
                                @endphp
                                {!! (!empty($totalDiscount) ? '-' : '').$currency.number_format((float)(!empty($totalDiscount) ? $totalDiscount : 0), 2, '.', '') !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if(!empty($order->calculate_group_discount))
                    <div class="row mgt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="pull-left text-left p-subtotal">
                                @lang('order.group_discount'):
                            </div>
                            <div class="pull-right text-right p-normal">
                                @php
                                    $totalDiscount = $order->calculate_group_discount;
                                @endphp
                                {!! (!empty($totalDiscount) ? '-' : '').$currency.number_format((float)(!empty($totalDiscount) ? $totalDiscount : 0), 2, '.', '') !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if($order->type_convert == \App\Models\Order::TYPE_DELIVERY)
                    <div class="row mgt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="pull-left text-left p-subtotal">
                                @lang('order.ship_price'):
                            </div>
                            <div class="pull-right text-right p-normal">
                                {!! $currency.number_format((float)(!empty($order->calculate_ship_price) ? $order->calculate_ship_price : 0), 2, '.', '') !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if(!empty($order->calculate_service_cost))
                    <div class="row mgt-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="pull-left text-left p-subtotal">
                                @lang('workspace.service_cost'):
                            </div>
                            <div class="pull-right text-right p-normal">
                                {!! $currency.number_format((float)(!empty($order->calculate_service_cost) ? $order->calculate_service_cost : 0), 2, '.', '') !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row mgt-10">
                    <div class="col-sm-12 col-xs-12 p-total">
                        <div class="pull-left text-left">
                            <span class="text-uppercase">@lang('order.total')</span>:
                        </div>
                        <div class="pull-right text-right">
                            {!! $currency.number_format((float)(!empty($order->calculate_total_price) ? $order->calculate_total_price : 0), 2, '.', '') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mgt-20">
            <div class="col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 p-total">
                        @lang('order.btw_list')
                    </div>
                </div>
                <div class="row p-subtotal border-bottom pd-5">
                    <div class="col-sm-3 col-xs-3 text-uppercase text-right">
                        @lang('order.btw_percent')
                    </div>
                    <div class="col-sm-3 col-xs-3 text-uppercase text-right">
                        @lang('order.btw_ex')
                    </div>
                    <div class="col-sm-3 col-xs-3 text-uppercase text-right">
                        @lang('order.btw')
                    </div>
                    <div class="col-sm-3 col-xs-3 text-uppercase text-right">
                        @lang('order.btw_in')
                    </div>
                </div>
                @if(!empty($vatItems))
                    @foreach($vatItems as $vatItem)
                        @php
                            $btwPercent = !empty($vatItem['btw_percent']) ? $vatItem['btw_percent'] : 0;
                            $btwInPrice = $vatItem['btw_in_price'];

                            if($highestVat == $btwPercent) {
                                $btwInPrice += $order->calculate_ship_price;
                                $btwInPrice += $order->calculate_service_cost;
                            }

                            // $btwPrice = $btwPercent * $btwInPrice / 100;
                            // $btwExPrice = $btwInPrice - $btwPrice;
                            $btwExPrice = $btwInPrice / (1 + ($btwPercent / 100));
                            $btwPrice = $btwInPrice - $btwExPrice;

                            $vatEx = $vatEx + $btwExPrice;
                            $vatPrice = $vatPrice + $btwPrice;
                            $vatIn = $vatIn + $btwInPrice;
                        @endphp
                        <div class="row p-normal pd-5">
                            <div class="col-sm-3 col-xs-3 text-right">
                                @if(!empty(config('common.vat_level.'.$btwPercent)))
                                    {!! config('common.vat_level.'.$btwPercent) !!}
                                @else
                                    {!! 'D'.$btwPercent !!}
                                @endif
                                @if(!empty(config('common.vat_level_trans.'.$btwPercent)))
                                    @lang('order.'.(config('common.vat_level_trans.'.$btwPercent)))
                                @endif
                                {!! $btwPercent !!}%
                            </div>
                            <div class="col-sm-3 col-xs-3 text-right">
                                {!! $currency !!}{!! number_format((float)(!empty($btwExPrice) ? $btwExPrice : 0), 2, '.', '') !!}
                            </div>
                            <div class="col-sm-3 col-xs-3 text-right">
                                {!! $currency !!}{!! number_format((float)(!empty($btwPrice) ? $btwPrice : 0), 2, '.', '') !!}
                            </div>
                            <div class="col-sm-3 col-xs-3 text-right">
                                {!! $currency !!}{!! number_format((float)(!empty($btwInPrice) ? $btwInPrice : 0), 2, '.', '') !!}
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="row p-normal pd-5">
                    <div class="col-sm-3 col-xs-3 text-right">
                        <strong class="text-uppercase">
                            @lang('order.total')
                        </strong>
                    </div>
                    <div class="col-sm-3 col-xs-3 text-right">
                        <strong class="text-uppercase">
                            {!! $currency.number_format((float)(!empty($vatEx) ? $vatEx : 0), 2, '.', '') !!}
                        </strong>
                    </div>
                    <div class="col-sm-3 col-xs-3 text-right">
                        <strong class="text-uppercase">
                            {!! $currency.number_format((float)(!empty($vatPrice) ? $vatPrice : 0), 2, '.', '') !!}
                        </strong>
                    </div>
                    <div class="col-sm-3 col-xs-3 text-right">
                        <strong class="text-uppercase">
                            {!! $currency.number_format((float)(!empty($vatIn) ? $vatIn : 0), 2, '.', '') !!}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12 text-center">
                <div class="p-payment">
                    @if($order->is_test_account)
                        @lang('order.test') 
                    @else
                        @lang('order.payment_method'): {!! str_replace(',', ' /', $order->payment_method_show) !!}
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12 text-center text-uppercase p-kasticket">
                @lang('order.kasticket')
            </div>
        </div>

        <div class="row mgt-10">
            <div class="col-sm-12 col-xs-12 text-center p-thanks">
                @if($order->is_test_account)
                    @lang('order.test_order') 
                @else
                    @lang('order.print_thank')
                @endif
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.print.bottom')