<div class="col-sm-12 col-xs-12 item-detail has-expand">
    <div class="row">
        <div class="col-sm-2 col-xs-12 text-more">
            {!! $order->phone_display !!}
        </div>
        <div class="col-sm-2 col-xs-12 text-more">
            {!! $order->email_display !!}
        </div>
        <div class="col-sm-6 col-xs-12">
            @if(!in_array($order->type_convert, [\App\Models\Order::TYPE_IN_HOUSE, \App\Models\Order::TYPE_TAKEOUT]))
                {!! $order->address_show !!}
            @endif
        </div>
        <div class="col-sm-2 col-xs-12 text-right">
            <a href="javascript:;"
               class="no-show-label ir-btn ir-btn-primary" {!! empty($order->no_show) ? 'style="display: none;"' : '' !!}>
                @lang('order.no_show')
            </a>
        </div>
    </div>

    @include('manager.orders.partials.table_item_detail_connector')
</div>