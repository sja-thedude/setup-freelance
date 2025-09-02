<a href="javascript:;"
   class="dropdown-toggle ir-actions"
   data-toggle="dropdown"
   aria-expanded="false">
    @lang('workspace.actions')
    <i class=" fa fa-angle-down"></i>
</a>
@php
    $flagCutoffTime = false;

    if(empty($order->group_id) ||
    (!is_null($order->cut_off_time) && strtotime(date('Y-m-d H:i:s')) >= strtotime($order->cut_off_time))) {
        $flagCutoffTime = true;
    }
@endphp
<ul class="dropdown-menu pull-right ir-dropdown-actions">
    @if(!empty($flagCutoffTime))
        <li>
            <a class="print-item"
               data-type="werkbon"
               data-id="{!! $order->id !!}"
               data-url="{!! route($guard.'.orders.printItem', [
                    'type' => 'werkbon',
                    'orderId' => $order->id
                ]) !!}">
                @lang('order.print_used_chefs')
            </a>
        </li>
        <li>
            <a class="print-item"
               data-type="kassabon"
               data-id="{!! $order->id !!}"
               data-url="{!! route($guard.'.orders.printItem', [
                    'type' => 'kassabon',
                    'orderId' => $order->id
                ]) !!}">
                @lang('order.print_another_client')
            </a>
        </li>
        @if ($isShowSticker && $isShowSticker->active)
            <li>
                <a class="print-item"
                   data-type="sticker"
                   data-id="{!! $order->id !!}"
                   data-url="{!! route($guard.'.orders.printItem', [
                    'type' => 'sticker',
                    'orderId' => $order->id
                ]) !!}">
                    @lang('order.print_only_items_of_this_order')
                </a>
            </li>
        @endif
    @endif
</ul>