<div class="form-line use-maps">
    <div class="radio-custom maps">

        @if (!auth()->guest())
            <div class="item-radio row display-flex">
                <div class="wrap-input col-md-2 col-sm-2 col-xs-2">
                    <input type="radio" name="address_type" value="0" checked>
                    <div class="input-active"></div>
                </div>
                <div class="wrap-content @if(empty($isModal)) pop-address @endif col-md-10 col-sm-10 col-xs-10">
                    <p><strong class="font-18">@lang('landing.lb_mijn_adres'):</strong></p>
                    <p class="font-18">
                        @if(auth()->user()->address)
                            {{ auth()->user()->address }}
                        @else
                            @lang('frontend.edit_profile')
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <div class="item-radio row display-flex">
            @if (!auth()->guest())
                <div class="wrap-input col-md-2 col-sm-2 col-xs-2">
                    <input type="radio" name="address_type" value="1">
                    <div class="input-active"></div>
                </div>
            @endif
            <div class="wrap-content with-input @if(empty($isModal)) pop-address @endif @if(!auth()->guest()) col-md-10 col-sm-10 col-xs-10 @else col-md-12 col-sm-12 @endif" style="position: relative">
                <div class="form-input maps">
                    <i class="icn-location left"></i>
                    {!! Form::text('address', NULL, [
                        'class'       => 'location pl-25 mb-5 font-18',
                        'placeholder' => trans('landing.vul_hier_uw'),
                        'autocomplete' => 'off'
                    ]) !!}
                    {!! Form::hidden('type', \App\Models\Cart::TYPE_LEVERING) !!}
                    {!! Form::hidden('lat', 0, ['class' => 'latitude']) !!}
                    {!! Form::hidden('long', 0, ['class' => 'longitude']) !!}
                    @if (auth()->guest())
                        {!! Form::hidden('address_type', 1, ['class' => 'address_type']) !!}
                    @endif
                    {!! Form::hidden('workspace_id', $workspace['id']) !!}
                    {!! Form::hidden('user_id', auth()->guest() ? NULL : auth()->user()->id) !!}
                    {!! Form::hidden('group_id', NULL) !!}
                </div>
                <label class="font-18">@lang('landing.example')</label>

                <ul class="place-results" id="address-box" style="left: 0 !important;"></ul>
            </div>
        </div>
    </div>
</div>
