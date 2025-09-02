@php
    $currency = '€';
    $countCheck = 0;
    $countFlag = false;

    if(!empty($optionSettingId)) {
        if(!$option->items->isEmpty()) {
            foreach($option->items as $key => $optionItem) {
                if(!empty($order->option_item_check[$optionItem->id])) {
                    $countFlag = true;
                    break;
                }
            }
        }
    }
@endphp
<div class="col-sm-12 col-xs-12 order-items dest-expand" style="display: none;">
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-sm-12 col-xs-12 total-item">
                    {!! $order->total_product_items !!}
                    <span class="text-uppercase">@lang('order.items')</span>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xs-12 mgt-10">
            <div class="row">
                @if(!empty($optionSettingId))
                    @if(!$option->items->isEmpty() && !empty($countFlag))
                        <div class="col-sm-2 col-xs-12 calculate-options-{!! $order->id !!}">
                            @foreach($option->items as $key => $optionItem)
                                @if(!empty($order->option_item_check[$optionItem->id]))
                                    <div class="row {!! $countCheck > 0 ? 'mgt-10' : '' !!}">
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="order-option-items">
                                                <div class="pull-left order-option-name">
                                                    {!! $optionItem->name !!}
                                                </div>
                                                <div class="pull-right order-option-number">
                                                    {!! $order->option_item_check[$optionItem->id] !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $countCheck++;
                                    @endphp
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif
                
                <div class="{!! !empty($optionSettingId) && !$option->items->isEmpty() && !empty($countFlag) ? 'col-sm-7' : 'col-sm-9' !!} col-xs-12 has-line">
                    @include('manager.orders.partials.table_extra_info_normal_order')
                </div>

                <div class="col-sm-3 col-xs-12">
                    @php
                        $revenueTaxs = [];

                        if(!$order->groupOrders->isEmpty()) {
                            foreach($order->groupOrders as $subOrder) {
                                if(!$subOrder->orderItems->isEmpty()) {
                                    foreach($subOrder->orderItems as $orderItem) {
                                         if(!empty($orderItem->vat_percent)) {
                                            $totalPrice = !empty($orderItem->total_price) ? $orderItem->total_price : 0;

                                            if(!empty($revenueTaxs[$orderItem->vat_percent])) {
                                                $revenueTaxs[$orderItem->vat_percent] = $revenueTaxs[$orderItem->vat_percent] + $totalPrice;
                                            } else {
                                                $revenueTaxs[$orderItem->vat_percent] = $totalPrice;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    @endphp

                    <div class="row mgb-5">
                        <div class="col-sm-12 col-xs-12 text-right">
                            <strong>@lang('order.group_order_paid'):</strong>
                            <span class="calculate_total_paid">{!! $currency.$order->calculate_total_paid !!}</span>
                            @lang('order.order_van')
                            {!! $currency.(!empty($order->calculate_total_price) ? $order->calculate_total_price : 0) !!}
                        </div>
                    </div>

                    @if(!$order->isBuyYourSelf())
                    <!-- Since this is in-house order, the "Te betalen op factuur" can be removed.
                        Users cannot choose to select "op factuur" as payment method on checkout. -->
                    <div class="row mgb-15">
                        <div class="col-sm-12 col-xs-12 text-right">
                            <strong>@lang('order.group_order_remaining'):</strong>
                            {!! $currency !!}{!! $order->total_on_invoice !!}
                        </div>
                    </div>
                    @endif

                    @if(!empty($revenueTaxs))
                        @foreach($revenueTaxs as $vatValue => $revenueValue)
                            <div class="row mgb-5">
                                <div class="col-sm-12 col-xs-12 text-right">
                                    <span class="text-uppercase">@lang('order.btw')</span>
                                    {!! $vatValue !!}%: {!! $currency.$revenueValue !!}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="row">
                        <div class="col-sm-12 col-xs-12 text-right total-revenue">
                            <span class="text-uppercase">@lang('order.total')</span>: {!! $currency.$order->calculate_total_price !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>