<div class="list-responsive">
    <div class="list-header list-header-manager list-header-custom">
        <div class="row mgl-i-14">
            <div class="col-item col-sm-2 col-xs-12">
                @lang('order.ready')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                @lang('order.client')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                @lang('order.payment_method')
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('order.print_sticker')
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('order.type')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                @lang('order.besteld')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                #
            </div>
        </div>
    </div>
    <div class="list-body list-body-manager">
        @php
            $importantOptionItems = [];
            $optionSettingId = !empty($option->id) ? $option->id : null;
        @endphp

        @if(!empty($orders))
            @foreach($orders as $order)
                <div class="row list-body-item root-expand mgb-20">
                    @include('manager.orders.partials.table_row', ['isShowSticker' => $isShowSticker])

                    @include('manager.orders.partials.table_item_detail')

                    @if(!empty($order->group_id) || $order->type == \App\Models\Order::TYPE_IN_HOUSE)
                        @php
                            if(!$order->groupOrders->isEmpty()) {
                                foreach($order->groupOrders as $subOrder) {
                                    if(!$subOrder->orderItems->isEmpty()) {
                                        foreach($subOrder->orderItems as $orderItem) {
                                            if(!$orderItem->optionItems->isEmpty()) {
                                                foreach($orderItem->optionItems as $optionItem) {
                                                    if(!empty($importantOptionItems[$optionItem->optie_item_id])) {
                                                        $importantOptionItems[$optionItem->optie_item_id] = $importantOptionItems[$optionItem->optie_item_id] + ($orderItem->total_number > 0 ? $orderItem->total_number : 1);
                                                    } else {
                                                        $importantOptionItems[$optionItem->optie_item_id] = $orderItem->total_number > 0 ? $orderItem->total_number : 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @include('manager.orders.partials.table_group_extra_info')
                    @else
                        @php
                            if(!$order->orderItems->isEmpty()) {
                                foreach($order->orderItems as $orderItem) {
                                    if(!$orderItem->optionItems->isEmpty()) {
                                        foreach($orderItem->optionItems as $optionItem) {
                                            if(!empty($importantOptionItems[$optionItem->optie_item_id])) {
                                                $importantOptionItems[$optionItem->optie_item_id] = $importantOptionItems[$optionItem->optie_item_id] + ($orderItem->total_number > 0 ? $orderItem->total_number : 1);
                                            } else {
                                                $importantOptionItems[$optionItem->optie_item_id] = $orderItem->total_number > 0 ? $orderItem->total_number : 1;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp
                        @include('manager.orders.partials.table_individual_extra_info')
                    @endif
                </div>
            @endforeach
        @endif

        @include('manager.orders.partials.table_important_option_items')
    </div>
</div>

@if(!empty($orders))
    {{ $orders->appends(request()->all())->links() }}
@endif