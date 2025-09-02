@php
    $userId = !auth()->guest() ? auth()->user()->id : null;
@endphp
<div class="pop-up hidden" id="pop-search-address">
    <div class="bg"></div>
    {!! Form::open(['route' => [$guard.'.carts.orderAgain', $order->id], 'name' => 'orderAgain', 'method' => 'POST', 'class' => 'order-again']) !!}
        <div class="row ">
            <div class="col-md-6 col-md-push-3">
                <div class="wrap-popup-card">
                    <a href="javascript:;" class="close"><i class="icn-close"></i></a>
                    <div class="wp-card">
                        <div class="row modal-error-title">
                            <h5>@lang('frontend.receive_address_order')</h5>
                        </div>
                        <div class="form-line use-maps">
                            <div class="radio-custom maps">
                                <div class="item-radio row display-flex">
                                    <div class="wrap-input col-md-2 col-sm-2 col-xs-2">
                                        <input type="radio" name="address_type" value="0" checked>
                                        <div class="input-active"></div>
                                    </div>
                                    <div class="wrap-content col-md-10 col-sm-10 col-xs-10">
                                        <p><strong>@lang('landing.lb_mijn_adres'):</strong></p>
                                        <p class="font-18">{{ $userId ? auth()->user()->address : NULL }}</p>
                                    </div>
                                </div>
                                <div class="item-radio row display-flex">
                                    <div class="wrap-input col-md-2 col-sm-2 col-xs-2">
                                        <input type="radio" name="address_type" value="1">
                                        <div class="input-active"></div>
                                    </div>
                                    <div class="wrap-content with-input col-md-10 col-sm-10 col-xs-10" style="position: relative">
                                        <div class="form-input maps">
                                            <i class="icn-location left"></i>
                                            {!! Form::text('address', NULL, [
                                                'class'       => 'location pl-25 mb-5 font-18',
                                                'required'    => $userId ? false : true,
                                                'placeholder' => trans('landing.vul_hier_uw'),
                                                'autocomplete' => 'off'
                                            ]) !!}
                                            {!! Form::hidden('lat', $userId ? auth()->user()->lat : NULL, ['class' => 'latitude']) !!}
                                            {!! Form::hidden('long', $userId ? auth()->user()->lng : NULL, ['class' => 'longitude']) !!}
                                            @if (!$userId)
                                                {!! Form::hidden('address_type', 1, ['class' => 'address_type']) !!}
                                            @endif
                                            {!! Form::hidden('workspace_id', $workspace['id']) !!}
                                            {!! Form::hidden('user_id', $userId) !!}
                                        </div>
                                        <label>@lang('landing.example')</label>
                                        
                                        <ul class="place-results" style="left: 0 !important;"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-modal btn-modal-error btn-modal-save" @if (!$userId) disabled @endif type="submit">
                            @lang('strings.save')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
