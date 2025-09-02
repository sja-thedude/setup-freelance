@if (request()->has('step') && request()->get('step') === "2")
    @include('web.carts.partials.popup_payment_time_limit')
    @include('web.carts.partials.popup_not_mollie_response')

    <input type="hidden" name="step" value="2">

    <div class="shopping-cart">
        <h6>@lang('cart.txt_datum')</h6>

        @if (session()->has('flash_notification.message'))
            <span class="errors extend">
                {!! session('flash_notification.message') !!}
            </span>
        @endif

        <div class="form-line form-with-icon mb-20">

            @if ($cart->group)
                <input type="hidden" name="openTimeslotsGroup" value="{{ $cart->group->openTimeSlots }}">

                <input type="hidden" name="closeTimeGroup"
                       value="{{ \Carbon\Carbon::parse($cart->group->close_time)->format("H:i") }}">

                <input type="hidden" name="groupReceiveTime"
                       value="{{ \Carbon\Carbon::parse($cart->group->receive_time)->format("H:i") }}">

                <input type="hidden" name="groupId" value="{{ $cart->group_id }}">
            @endif

            <input onkeypress="keyPressMobile()" type="text" name="settingDateslot" data-group-route="{{route('web.cart.checkgroupdate', ['cartId' => $cart->id])}}" class="show-date" required data-offset-day="{{ $offsetDayOrder }}"
                   data-route="{{ route('web.timeslots.show', ['workspaceId' => $workspaceId]) }}" placeholder="__/__/____" autocomplete="off"
            />

            <input type="hidden" name="offsetTimeOrder" value="{{ $offsetTimeOrder }}">
            <input type="hidden" name="offsetDayOrder" value="{{ $offsetDayOrder }}">

            <div class="calendar-button">
                <img src="/images/calender.svg" alt="calender">
            </div>

        </div>
        <div class="wrap-container">
            <div class="wrap-sidebar-time owl-carousel wrap-selection">
            </div>
        </div>
    </div>
@endif