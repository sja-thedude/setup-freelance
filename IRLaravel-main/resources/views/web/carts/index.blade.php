<div class="col-md-4" id="parentCart">

    @if (count($coupons) > 0)
        <div class="wrap-validate validate-slider owl-carousel display-none" @if (count($coupons) === 1) style="display:block" @endif>
            @foreach($coupons as $cp)
                <div class="item">
                    <h6>
                        {{
                            $cp->translate(app()->getLocale())
                                ? $cp->translate(app()->getLocale())->promo_name
                                : $cp->translate('en')->promo_name
                        }}
                    </h6>
                    <div class="sub-title-code">
                        <p>@lang('cart.sub_title_gebruik'):</p>
                        <h5>{{ $cp->code }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    @php
        $isInTakeoutTab = request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_TAKEOUT && (request()->get('step') == 2 || request()->get('step') == 3);
        $isInLeveringTab = request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING && (request()->get('step') == 2 || request()->get('step') == 3);
    @endphp

    <div class="wrap-box" id="wrapRedeem"
         style="display: {{ !(in_array(true, $infoRedeem->toArray()) && request()->get('step') != 2 && request()->get('step') != 3) || $cart->redeem_history_id || $cart->coupon_id
                            ? "none" :
                            "block" }}"
    >
        <p>@lang('cart.maak_nu_gebruik')</p>
        <a href="javascript:;"
           data-discount="{{ $totalRedeemDiscount??0 }}"
           data-id="{{ $redeem['id'] }}" class="btn btn-andere"
        >
            @lang('cart.pas_toe')
        </a>
    </div>

    @php
        //Case 1: user not logged
        $user = session()->get('user_id_not_login');
        $condition = session()->get('condition');
        if(!empty($user) && !empty($condition)) {
            $conditionLevering = $condition;
        }else {
            //Case 2: User logged
            session()->forget('condition');
            $conditionLevering = $conditionDelevering ?: new \App\Models\SettingDeliveryConditions();
        }
    @endphp

    @if (request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING && (!$cart || !$cart->group_id))
        <p class="notiMaxFreeCart">
            @lang('cart.gratis_levering', ['price' => $conditionLevering->free ?: 0])
        </p>
    @endif

    @php
        $showMessageGroup = false;
        if ($cart && isset($cart->cartItems) && $cart->cartItems->count() > 0) {
            session(['productNotAvailable' => []]);
            foreach($cart->cartItems as $item) {
                $product = $item->product;
                //Push product not available to array
                if (!$product->active || !\App\Helpers\Helper::isInGroupProducts($cart->group_id, $product)) {
                    session()->push('productNotAvailable', $product->id);
                    \Flash::error(trans('cart.mes_error'));
                    $showMessageGroup = true;
                }
            }
        }
    @endphp
    <div class="wrap-sidebar">
        @if (isset(request()->step) && request()->step != 2 || $showMessageGroup)
            <p style="text-align: center;">
                @include('layouts.partials.error-msg')
            </p>
        @endif

        {!! Form::open(['url' => route($guard.'.carts.store', [$webWorkspace->id]), 'method' => 'POST', 'files' => TRUE, 'id' => 'formCartStep1']) !!}

            @if ($cart)
                <input type="hidden" name="currentUrl" value="{{ \url()->current() }}" />
                <input type="hidden" name="tab" value="{{ request()->get('tab') }}" />
                <input type="hidden" name="timezone" value="" class="auto-detect-timezone" />
                <input type="hidden" name="is_trigger_del" value="" />
                <input type="hidden" name="workspace_id" value="{{ $workspaceId }}" />
                <input type="hidden" name="cart_id" value="{{ $cart->id }}" />
                <input type="hidden" name="user_id" value="{{ $userId }}" />
                <input type="hidden" name="type" value="{{ $type }}" />
                <input type="hidden" name="group_id" value="{{ $cart->group_id ?: NULL }}" />
                <input type="hidden" name="address_type" value="1" />
                <input type="hidden" name="holidays" value="{{ json_encode($holidays) }}" />
                <input type="hidden" name="openHours" value="{{ json_encode($openHours) }}" />
                <input type="hidden" name="dayInWeekActive" value="{{ json_encode($dayInWeekActive) }}" />
                <input type="hidden" name="timeSlot" value="{{ json_encode($timeSlot) }}" />
                <input type="hidden" name="listDayAvalidable" value="{{ json_encode($listDayAvalidable) }}" />
                <input type="hidden" name="datesDisable" value="{{ json_encode($datesDisable) }}" />
                <input type="hidden" name="redeem_history_id" value="{{ $cart->redeem_history_id }}" />
                <input type="hidden" name="redeem_discount" value="{{ $cart->redeem_discount }}" />

                @if ($cart->group && request()->step == 3)
                    <input type="hidden" name="closeTimeGroup"
                       value="{{ \Carbon\Carbon::parse($cart->group->close_time)->format("H:i") }}">
                @endif
            @endif

            @if (!$cart || !$cart->group_id)
                <div class="checkbox-sex checkbox-color
                    @if(\request()->get('step') == 2 || \request()->get('step') == 3) disabled @endif
                    @if (($isOpendTabTakeOut && !$isOpendTabLevering) || (!$isOpendTabTakeOut && $isOpendTabLevering)) one-tab @endif"
                >
                    @if ($isOpendTabTakeOut)
                        <div class="tabAfhaal wrap-content @if(auth()->guest() && \request()->get('step') != 2 && \request()->get('step') != 3) btn-show-login-modal @endif
                        @if($isInTakeoutTab) has-checked @endif"
                             @if(!auth()->guest() && \request()->get('step') != 2 && \request()->get('step') != 3) onclick="window.location.href='{{ url()->current() . "?tab=afhaal" }}'" @endif
                            @if($isInLeveringTab) style="display:none" @endif
                        >
                            <input type="radio" id="order1" checked="checked" name="order-type" class="radio-sex"
                                   {{ (!empty($cart) && $cart->type == \App\Models\Order::TYPE_TAKEOUT)
                                   || (request()->has('tab') && request()->get('tab')) || !request()->has('tab') === \App\Models\Cart::TAB_TAKEOUT
                                   ? "checked" : "" }}
                                   value="{{\App\Models\Order::TYPE_TAKEOUT}}" />
                            <label for="order1" class="slider active">
                                @lang('cart.tab_afhaal')
                            </label>
                        </div>
                    @endif

                    @if ($isOpendTabLevering)
                        <div class="tabLevering wrap-content
                            @if($isInLeveringTab) has-checked @endif"
                             @if($isInTakeoutTab) style="display: none" @endif
                             @if (\request()->get('step') != 2 && \request()->get('step') != 3)
                                 data-route="window.location.href='{{ url()->current() . "?tab=levering" }}'"
                                 data-class="@if(auth()->guest()) btn-show-login-modal @endif"
                             @endif
                        >
                            <input type="radio" id="r2" name="order-type" class="radio-sex"
                               {{ request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING
                               || (!empty($cart) && $cart->type == \App\Models\Order::TYPE_DELIVERY)
                                ? "checked" : "" }}
                               value="{{\App\Models\Order::TYPE_DELIVERY}}" />
                            <label for="r2" class="slider" >
                                @lang('cart.tab_levering')
                            </label>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
            @else
                <?php $closeTime = \Carbon\Carbon::parse($cart->group->close_time); ?>
                <input type="hidden" name="address_group" value="{{ $cart->group->address_display }}" />
                <h5 class="title-siderbar mb-10" style="text-align:center">
                    @lang('cart.bestelling_voor', [
                        'group' => $cart->group->name
                    ])
                </h5>
            @endif

            @if ($cart && isset($cart->cartItems) && $cart->cartItems->count() > 0)
                @php
                    $totalPrice = 0;
                    if (session()->has('idsProductFail')) {
                        $idsProductFail = session()->get('idsProductFail');
                    }
                @endphp

                @include('web.carts.partials.step1', [
                    'isSuccessPage'         => FALSE,
                    'cart'                  => $cart,
                    'listItem'              => $cart->cartItems,
                    'isDeleveringAvailable' => $isDeleveringAvailable,
                    'isDeleveringPriceMin'  => $isDeleveringPriceMin,
                    'conditionDelevering'   => $conditionDelevering,
                    'redeemDiscount'        => (float) $cart->redeem_discount,
                    'redeemId'              => $cart->redeem_history_id,
                    'totalCouponDiscount'   => $totalCouponDiscount
                ])

                @include('web.carts.partials.step2')

                @include('web.carts.partials.step3')

                <div class="form-line center">

                    <input type="hidden" name="numberCategory" value="{{ $productSuggesstions ? 1 : NULL }}" />
                    <input type="hidden" name="isDeleveringPriceMin" value="{{ $isDeleveringPriceMin }}" />
                    <input type="hidden" name="price_to_free" value="{{ $conditionLevering->free ?: 0 }}" />
                    <input type="hidden" name="feeCart" value="{{ $conditionLevering->price ?: 0 }}" />
                    <input type="hidden" name="discountProducts" value="{{ json_encode($discountProducts) }}"/>
                    <input type="hidden" name="totalCouponDiscount" value="{{ $totalCouponDiscount }}"/>
                    @if ((request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING
                                && (!$isDeleveringAvailable || !$isDeleveringPriceMin)
                            )
                        || count(session('productNotAvailable')) > 0
                    )
                        <button type="button" class="btn btn-andere-gray" id="btn-andere" disabled
                                data-class="@if(auth()->guest()) btn-show-login-modal @endif">
                            @lang('cart.btn_verder')
                        </button>
                    @else
                        <button type="button" class="btn btn-andere btn-pr-custom" id="btn-andere"
                            data-class="@if(auth()->guest()) btn-show-login-modal @elseif(empty(auth()->user()->gsm) || empty(auth()->user()->first_name) || \App\Helpers\Helper::checkSpecialCharacters(auth()->user()->first_name, '@')) btn-show-update-gsm @endif">
                            @lang('cart.btn_verder')
                        </button>
                    @endif

                    @if ((isset($idsProductFail) && count($idsProductFail) > 0)
                        || (request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING && !$isDeleveringAvailable)
                    )
                        <span class="error" style="display:inline-block">
                            @lang('cart.mes_error')
                        </span>
                    @endif

                    <div class="wrap-overview-order row mt-20 error-delevering"
                        style="display:
                        @if ($isDeleveringAvailable
                                && request()->has('tab')
                                && request()->get('tab') === \App\Models\Cart::TAB_LEVERING
                                && !$isDeleveringPriceMin
                                && !$cart->group_id
                        ) block @else none @endif"
                    >
                        <div class="col-md-12">
                            <span>
                                @lang('cart.mes_delevering', [
                                    'name'      => $webWorkspace->name,
                                    'min_price' => $conditionLevering->price_min ?: 0
                                ])
                            </span>
                        </div>
                    </div>

                    <div class="wrap-overview-order row mt-20 message-free"
                         style="display:
                            @if ($isDeleveringAvailable
                                    && request()->has('tab')
                                    && request()->get('tab') === \App\Models\Cart::TAB_LEVERING
                                    && $isDeleveringPriceMin
                                    && $isShowMessageFree
                                    && !$cart->group_id
                            ) block @else none @endif"
                    >
                        <div class="col-md-12">
                            <span>@lang('cart.gratis_levering', ['price' => $conditionLevering->free ?: 0])</span>
                        </div>
                    </div>
                </div>

                <div class="wrap-overview-order row steps-wrap"
                     style="display:@if (request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING && !$isDeleveringPriceMin) none @else block @endif"
                >
                    <div class="col-md-4">
                        <a href="{{route('web.category.index') . '?tab=' . request()->get('tab') . '&step=1'}}" class="@if ((request()->has('step') && (int) request()->get('step') >= 1) || !request()->has('step')) active @endif">
                            @lang('cart.step_besteloverzicht')
                            <span>1</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="@if(request()->has('step') && (int)request()->get('step') > 2) {{route('web.category.index') . '?tab=' . request()->get('tab') . '&step=2'}} @else javascript:; @endif" class="@if (request()->has('step') && (int) request()->get('step') >= 2) active @endif">
                            @lang('cart.step_datum_tijd')
                            <span>2</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="javascript:;" class="@if (request()->has('step') && (int) request()->get('step') >= 3) active @endif">
                            @lang('cart.step_betaalmethode')
                            <span>3</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="cart-nothing">
                    <span class="text-center">
                        @lang('cart.nothing')
                    </span>
                </div>
            @endif
        {!! Form::close() !!}
    </div>
</div>

@push('scripts')
    <script>
        // Validate frontend show/hide datetime in step2
        $(function() {
            var todayDate             = new Date();
            var maxDate               = new Date();
            var objDate               = $("#parentCart .show-date");
            var offset                = objDate.data("offset-day");
            var holidays              = getDateHolidays();
            var daysDisableTijdslot   = getDaysDisableTijdslot();
            var valDayInWeekActive    = $('#parentCart input[name=dayInWeekActive]').val();
            var valDatesDisable       = $('#parentCart input[name=datesDisable]').val();
            var groupId               = $('#parentCart input[name=group_id]').val();
            var closeTimeGroup        = $('#parentCart input[name=closeTimeGroup]').val();
            var daysActiveOpeningHour = [];
            var datesDisable          = [];
            var dateGroupDisable      = [];
            var strTodate             = moment().format("YYYY-MM-DD");

            if (valDayInWeekActive !== undefined) {
                daysActiveOpeningHour  = JSON.parse(valDayInWeekActive);
            }
            if (valDatesDisable !== undefined) {
                datesDisable  = JSON.parse(valDatesDisable);
            }

            maxDate.setDate(todayDate.getDate() + parseInt(offset));
            if (closeTimeGroup = moment(closeTimeGroup, 'hh:mm:ss')) {
                if (closeTimeGroup <= moment()) {
                    dateGroupDisable.push(strTodate);
                }
            }

            initDateTimePicker(
                objDate,
                holidays,
                dateGroupDisable,
                daysDisableTijdslot,
                daysActiveOpeningHour,
                datesDisable,
                groupId,
                todayDate,
                maxDate,
            )

            if (groupId) {
                objDate.datepicker({
                    beforeShow: function(){
                        $('#ui-datepicker-div').remove()
                        $('body').loading('toggle')
                        objDate.datetimepicker('destroy')
                        dateGroupDisable = []
                        $.ajax({
                            url: objDate.data('group-route'),
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                let closeTimeGroup = response.data.closeTimeGroup
                                var strTodate = moment().format("YYYY-MM-DD");
                                todayDate = new Date();
                                maxDate = new Date();
                                if (closeTimeGroup = moment(closeTimeGroup, 'hh:mm:ss')) {
                                    if (closeTimeGroup <= moment()) {
                                        dateGroupDisable.push(strTodate);
                                    }
                                }

                                initDateTimePicker(
                                    objDate,
                                    holidays,
                                    dateGroupDisable,
                                    daysDisableTijdslot,
                                    daysActiveOpeningHour,
                                    datesDisable,
                                    groupId,
                                    todayDate,
                                    maxDate,
                                )

                                objDate.datetimepicker('show')
                                $('body').loading('toggle')
                            }
                        })
                    }
                })
            }
        })

        function getDateHolidays() {
            var holidays = $('#parentCart input[name=holidays]').val();
            var between = [];

            if (holidays !== undefined) {
                var dates = JSON.parse(holidays);

                dates.map(function(obj, key) {
                    var start = new Date(obj.start_time);
                    var end = new Date(obj.end_time);

                    while (start <= end) {
                        between.push(start.yyyymmdd());
                        start.setDate(start.getDate() + 1);
                    }
                });
            }

            return between;
        }

        function getGroupDayDisable() {
            var openTimeslotsGroup = $('#parentCart input[name=openTimeslotsGroup]').val();
            var dayDisable         = [];

            if (openTimeslotsGroup !== undefined) {
                JSON.parse(openTimeslotsGroup).map(function(obj, key) {
                    if (obj.status) {
                        var intDay = parseInt(obj.day_number);
                        if (intDay === 7) {
                            dayDisable.push(0);
                        } else {
                            dayDisable.push(intDay);
                        }
                    }
                });
            }

            return dayDisable;
        }

        function getDaysDisableTijdslot() {
            var dayDisable           = [];
            var valTimeSlot          = $('#parentCart input[name=timeSlot]').val();
            var valListDayAvalidable = $('#parentCart input[name=listDayAvalidable]').val();

            if (valTimeSlot === undefined || valListDayAvalidable === undefined) {
                return dayDisable;
            }

            var timeSlot                  = JSON.parse(valTimeSlot);
            var daysAvalidableTijdslot    = JSON.parse(valListDayAvalidable);
            var dateNow                   = new Date();
            var dateCompareIndex          = new Date();
            var dateCompareStart          = new Date();
            var dateCompareEnd            = new Date();
            var [hours, minutes, seconds] = timeSlot.max_time.split(':');

            dateCompareStart.setDate(dateNow.getDate() + timeSlot.max_before);

            dateCompareEnd = setDateTime(
                dateCompareEnd,
                dateNow.getDate() + timeSlot.max_before,
                hours, minutes, seconds
            );

            dateCompareIndex = setDateTime(
                dateCompareIndex,
                dateCompareIndex.getDate(),
                hours, minutes, seconds
            );

            while (dateCompareIndex <= dateCompareEnd) {
                if (timeSlot.max_mode && dateCompareStart > dateCompareIndex) {
                    var dayInWeek = dateCompareIndex.getDay();
                    if (daysAvalidableTijdslot.indexOf(dayInWeek) !== -1) {
                        var dateValid = $.datepicker.formatDate('yy-mm-dd', dateCompareIndex);
                        dayDisable[dayInWeek] = dateValid;
                    }
                }

                dateCompareIndex = setDateTime(
                    dateCompareIndex,
                    dateCompareIndex.getDate() + 1,
                    hours, minutes, seconds
                );
            }
            return dayDisable;
        }

        function setDateTime(dateJs, date, hour, time, sec) {
            dateJs.setDate(date);
            dateJs.setHours(+hour);
            dateJs.setMinutes(time);
            dateJs.setSeconds(sec);
            return dateJs;
        }

        Date.prototype.yyyymmdd = function() {
            var mm = this.getMonth() + 1;
            var dd = this.getDate();

            return [this.getFullYear(),
                (mm>9 ? '' : '0') + mm,
                (dd>9 ? '' : '0') + dd
            ].join('-');
        };

        function initDateTimePicker(
            objDate,
            holidays,
            dateGroupDisable,
            daysDisableTijdslot,
            daysActiveOpeningHour,
            datesDisable,
            groupId,
            todayDate,
            maxDate,
        ) {
            objDate.datetimepicker({
                formatDate: "dd/mm/YYYY",
                format: "d/m/Y",
                minDate: todayDate,
                scrollInput : false,
                scrollMonth: false,
                maxDate: groupId ? false : maxDate,
                dayOfWeekStart: 1,
                beforeShowDay: function(date) {
                    var stringDate = $.datepicker.formatDate('yy-mm-dd', date);
                    var day        = date.getDay();

                    var showDaysDisableTijdslot = true;
                    if(daysDisableTijdslot[day] != undefined && compareDates(date, daysDisableTijdslot[day])) {
                        showDaysDisableTijdslot = false;
                    }

                    if(groupId) {
                        return [
                            holidays.indexOf(stringDate) === -1 &&
                            getGroupDayDisable().indexOf(day) !== -1 &&
                            dateGroupDisable.indexOf(stringDate) === -1
                        ];
                    } else {
                        return [
                            holidays.indexOf(stringDate) === -1 &&
                            datesDisable.indexOf(stringDate) === -1 &&
                            daysActiveOpeningHour.indexOf(day) !== -1 &&
                            showDaysDisableTijdslot
                        ]
                    }
                }
            })
        }

        //Compare 2 date (yy-mm-dd)
        function compareDates(d1, d2){
            const date1 = new Date(d1);
            const date2 = new Date(d2);

            return date1.getDate() === date2.getDate()
        }

    </script>
@endpush
