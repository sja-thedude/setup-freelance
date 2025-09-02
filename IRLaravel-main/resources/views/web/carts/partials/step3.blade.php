@if (request()->has('step') && request()->get('step') === "3")

    <input type="hidden" name="step" value="3">

    <div class="form-line mb-67">

        <?php
            $message = "";
            if ($errors->has('workspace_offline')) {
                $message = $errors->first('workspace_offline');
            }
            if ($errors->has('incorrect_time_slot')) {
                $message = $errors->first('incorrect_time_slot');
            }
            if ($message !== "") {
                echo view('web.carts.partials.modal-incorrect-time-slot', ['message' => $message])->render();
            }
        ?>

        <div class="radio-custom payment-choice">

            @php
                $isAdminAlowActive = 0;
                if (isset($cart->workspace->workspaceExtras)) {
                    $extraSetting = $cart->workspace->workspaceExtras
                        ->where('type', \App\Models\WorkspaceExtra::PAYCONIQ)
                        ->first();
                    $isAdminAlowActive = $extraSetting ? $extraSetting->active : 0;
                }

                if (isset($cart->workspace->settingPayments)) {
                    $settingPayments = $cart->workspace->settingPayments;
                    $settingPaymentsMollie = $settingPayments
                        ->where('type', \App\Models\SettingPayment::TYPE_MOLLIE)
                        ->first();
                    $settingPaymentsPaycoinq = $settingPayments
                        ->where('type', \App\Models\SettingPayment::TYPE_PAYCONIQ)
                        ->first();
                    $settingPaymentsCash = $settingPayments
                        ->where('type', \App\Models\SettingPayment::TYPE_CASH)
                        ->first();
                }

                $attr = "takeout";
                if (request()->get('tab') === \App\Models\Cart::TAB_LEVERING) {
                    $attr = "delivery";
                }

                $group = $cart->group ?: new \App\Models\Group();
            @endphp

            @if ((isset($settingPaymentsMollie) && $settingPaymentsMollie->{$attr} && !$group->exists) || $group->payment_mollie)
                @if ($settingPaymentsMollie->api_token)
                    <div class="item-radio">
                        <div class="wrap-input">
                            <input type="radio" id="type-mollie" name="setting_payment_id"
                                   value="{{ $settingPaymentsMollie->id }}-{{ $group->exists ? \App\Models\SettingPayment::TYPE_MOLLIE : $settingPaymentsMollie->type }}">
                            <div class="input-active"></div>
                        </div>
                        <div class="wrap-content">
                            <h6>@lang('cart.method1')</h6>
                            <ul>
                                <li class="icon bancontact">
                                    <img src="{{ asset('images/cart/bancontact.svg') }}" />
                                </li>
                                <li class="icon ideal">
                                    <img src="{{ asset('images/cart/ideal.svg') }}" />
                                </li>
                                <li class="icon mastercard">
                                    <img src="{{ asset('images/cart/mastercard.svg') }}" />
                                </li>
                                <li class="icon visa">
                                    <img src="{{ asset('images/cart/visa.svg') }}" />
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            @endif

            @if ($isAdminAlowActive && ((isset($settingPaymentsPaycoinq) && $settingPaymentsPaycoinq->{$attr} && !$group->exists) || $group->payment_payconiq))
                <div class="item-radio hidden">
                    <div class="wrap-input">
                        <input type="radio" id="type-payconiq" name="setting_payment_id"
                               value="{{ $settingPaymentsPaycoinq->id }}-{{ $group->exists ? \App\Models\SettingPayment::TYPE_PAYCONIQ : $settingPaymentsPaycoinq->type }}">
                        <div class="input-active"></div>
                    </div>
                    <div class="wrap-content">
                        <h6 for="type-payconiq">@lang('cart.method2')</h6>
                        <p>
                            <i class="icn-festival"></i>
                        </p>
                    </div>
                </div>
            @endif

            @if ((isset($settingPaymentsCash) && $settingPaymentsCash->{$attr} && !$group->exists) || $group->payment_cash)
                <div class="item-radio">
                    <div class="wrap-input">
                        <input type="radio" id="type-cash" name="setting_payment_id"
                               value="{{ $settingPaymentsCash->id }}-{{ $group->exists ? \App\Models\SettingPayment::TYPE_CASH : $settingPaymentsCash->type }}">
                        <div class="input-active"></div>
                    </div>
                    <div class="wrap-content">
                        <h6 for="type-cash">@lang('cart.method3')</h6>
                        <p>
                            <img src="{{ asset('images/cart/cash.svg') }}" />
                        </p>
                    </div>
                </div>
            @endif

            @if ($group->payment_factuur)
                <div class="item-radio">
                    <div class="wrap-input">
                        <input type="radio" id="type-cash" name="setting_payment_id"
                               value="{{ NULL }}-{{ \App\Models\SettingPayment::TYPE_FACTUUR }}">
                        <div class="input-active"></div>
                    </div>
                    <div class="wrap-content">
                        <h6 for="type-cash">@lang('cart.method4')</h6>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif