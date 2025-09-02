<a href="javascript:;"
   class="dropdown-toggle ir-actions"
   data-toggle="dropdown"
   aria-expanded="false">
    @lang('workspace.actions')
    <i class=" fa fa-angle-down"></i>
</a>

<ul class="dropdown-menu pull-right ir-dropdown-actions">
    @if(empty($order->no_show))
        <li class="mark-no-show"
            data-subtitle="@lang('order.mark_no_show_subtitle')"
            data-yes="@lang('order.mark_no_show_yes_button')"
            data-no="@lang('order.mark_no_show_no_button')"
            data-url="{!! route($guard.'.orders.markNoShow', ['id' => $order->id]) !!}">
            <a>@lang('order.mark_current_order_no_show')</a>
        </li>
    @endif

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
</ul>