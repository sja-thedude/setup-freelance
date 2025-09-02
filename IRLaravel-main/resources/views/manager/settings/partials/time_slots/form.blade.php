{!! Form::model($setting, [
     'route' => [
         $guard.'.settingTimeSlot.update',
         $workspaceId,
         $setting->id
     ],
     'method' => 'post',
     'files' => true,
     'class' => 'update-time-slot',
 ]) !!}
    <div class="row mgb-20">
        @if(empty($key))
            <div class="col-sm-6 col-xs-12"></div>
        @endif
        <fieldset class="pdl-30 col-sm-6 col-xs-12 has-overlay" {!! $disabledClass !!}>
            <h3 class="type text-uppercase">
                @lang('time_slot.types.'. $setting->type)
            </h3>
        </fieldset>
    </div>
    <div class="row mgb-20">
        @if(empty($key))
            <div class="col-sm-6 col-xs-12">
                <label class="label line-h-40">
                    @lang('time_slot.order_per_time_slot')
                </label>
            </div>
        @endif
        <fieldset class="pdl-30 col-sm-6 col-xs-12 has-overlay" {!! $disabledClass !!}>
            {!! Form::number('order_per_slot', !empty($setting->order_per_slot) ? $setting->order_per_slot : null, [
                'class' => 'form-control validate-min width-70 text-center timeslot-ip auto-submit',
                'data-min' => 0,
                'data-type' => 'setting_time_slot',
                'required' => 'required'
            ]) !!}
        </fieldset>
    </div>
    <div class="row mgb-20">
        @if(empty($key))
            <div class="col-sm-6 col-xs-12">
                <label class="label line-h-40">
                    @lang('time_slot.max_price_per_time_slot')
                </label>
            </div>
        @endif
        <fieldset class="pdl-30 col-sm-6 col-xs-12 has-overlay" {!! $disabledClass !!}>
            @if(empty($key))
                <label class="special-elm">€</label>
            @endif

            {!! Form::number('max_price_per_slot', !empty($setting->max_price_per_slot) ? $setting->max_price_per_slot : null, [
                'class' => 'form-control validate-min width-70 text-center timeslot-ip auto-submit auto-format-decimal',
                'data-min' => 0,
                'data-type' => 'setting_time_slot',
                'required' => 'required'
            ]) !!}
        </fieldset>
    </div>
    <div class="row mgb-20">
        @if(empty($key))
            <div class="col-sm-6 col-xs-12">
                <label class="label line-h-40">
                    @lang('time_slot.interval_time_slot')
                </label>
            </div>
        @endif
        <fieldset class="pdl-30 col-sm-6 col-xs-12 select2-with-70 has-overlay" {!! $disabledClass !!}>
            {!! Form::select('interval_slot', config('common.time_slots'), !empty($setting->interval_slot) ? $setting->interval_slot : null, [
                'class' => 'form-control select-not-search auto-submit',
                'data-type' => 'setting_time_slot',
                'required' => 'required',
                !in_array($setting->type, $settingOpenHourActive) ? 'disabled' : ''
            ]) !!}
            <span class="mgl-5">@lang('common.min')</span>
        </fieldset>
    </div>
    <div class="row mgb-20">
        @if(empty($key))
            <div class="col-sm-6 col-xs-12">
                <label class="label line-h-40">
                    @lang('time_slot.order_maximum')
                </label>
            </div>
        @endif
        <fieldset class="pdl-30 @if($key == 0)col-sm-6 @else col-sm-12 @endif col-xs-12 has-overlay" {!! $disabledClass !!}>
            @include('manager.settings.partials.time_slots.form_max_area')
        </fieldset>
    </div>
{{Form::close()}}
