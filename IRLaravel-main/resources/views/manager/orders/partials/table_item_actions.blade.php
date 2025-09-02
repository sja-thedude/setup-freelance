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
               data-url="{!! route('manager.orders.printItem', [
                'type' => 'werkbon',
                'orderId' => $order->id
            ]) !!}">@lang('order.print_used_chefs')</a>
        </li>
        <li>
            <a class="print-item"
               data-type="kassabon"
               data-id="{!! $order->id !!}"
               data-url="{!! route('manager.orders.printItem', [
                'type' => 'kassabon',
                'orderId' => $order->id
            ]) !!}">@lang('order.print_another_client')</a>
        </li>
        @if (isset($tmpWorkspace) && $isShowSticker && $isShowSticker->active)
            <li>
                <a class="print-item"
                   data-type="sticker"
                   data-id="{!! $order->id !!}"
                   data-url="{!! route('manager.orders.printItem', [
                'type' => 'sticker',
                'orderId' => $order->id
            ]) !!}">@lang('order.print_only_items_of_this_order')</a>
            </li>
        @endif
        <li>
            <a class="print-item"
               data-type="a4"
               data-id="{!! $order->id !!}"
               data-url="{!! route('manager.orders.printItem', [
                    'type' => 'a4',
                    'orderId' => $order->id
                ]) !!}">@lang('order.print_a4')</a>
        </li>
        @if(!empty($connectorsList))
            <li>
                <a class="connector-push-order"
                   data-url="{!! route('manager.orders.triggerConnectors', [
                'orderId' => $order->id
            ]) !!}">@lang('order.trigger_connectors')</a>
            </li>
        @endif
    @endif
    @if(empty($order->no_show))
        <li class="mark-no-show"
            data-subtitle="@lang('order.mark_no_show_subtitle')"
            data-yes="@lang('order.mark_no_show_yes_button')"
            data-no="@lang('order.mark_no_show_no_button')"
            data-url="{!! route($guard.'.orders.markNoShow', ['id' => $order->id]) !!}">
            <a>@lang('order.mark_current_order_no_show')</a>
        </li>
    @endif

    @if($order->isBuyYourSelf())
        @if($order->isTableOrdering())
            <li class="manual-checked-fully-paid-cash"
                @if(!empty($order->disabledCashManual()))
                style="opacity: 50%; pointer-events: none;"
                @endif
                data-subtitle="@lang('order.manual_checked_fully_paid_cash_subtitle', ['amount' => $order->calculate_total_price - $order->calculate_total_paid])"
                data-yes="@lang('order.manual_checked_fully_paid_cash_yes_button')"
                data-no="@lang('order.manual_checked_fully_paid_cash_no_button')"
                data-url="{!! route($guard.'.orders.manualCheckedFullyPaidCash', ['id' => $order->id]) !!}">
                <a>@lang('order.action_receive_cash')</a>
            </li>
        @endif

        @if($order->isTableOrdering())
        <li class="manual-confirm-order"
            @if($order->hasLastPersonInTableOrdering())
                style="opacity: 50%; pointer-events: none;"
            @endif
            data-subtitle="@lang('order.manual_confirm_subtitle')"
            data-yes="@lang('order.manual_confirm_yes_button')"
            data-no="@lang('order.manual_confirm_no_button')"
            data-url="{!! route($guard.'.orders.manualConfirmed', ['id' => $order->id]) !!}">
            <a>@lang('order.action_close_manual')</a>
        </li>
        @endif

        @if($order->isSelfService())
            <li class="send-sms"
                data-workspace_id={{ $order->workspace_id }}
                data-status={{ \App\Models\Sms::STATUS_PENDING }}
                data-foreign_model={{ \App\Models\Order::class }}
                data-foreign_id={{ $order->id }}
                data-message="@lang('order.done_sms', [], $order->user ? $order->user->getLocale() : $order->workspace->language)"
                data-subtitle="@lang('order.send_sms_subtitle')"
                data-yes="@lang('order.manual_confirm_yes_button')"
                data-no="@lang('order.manual_confirm_no_button')"
                data-url="{!! route($guard.'.orders.sendSms') !!}">
                <a>@lang('order.action_send_buzzer')</a>
            </li>
        @endif
    @endif
</ul>