<div class="row mgt-20">
    <div class="col-md-12">
        <div class="form-group form-with-text">
            <span class="text-block">@lang('setting.preferences.service_cost'):</span>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group form-with-text">
            <span class="pull-left mgr-8">
                <input type="checkbox"
                       name="service_cost_set"
                       id="switch_service_cost_set"
                       class="switch-input auto-submit"
                       data-type="preferences" {{!empty($settingPreferences->service_cost_set) ? 'checked' : null}}/>
                <label for="switch_service_cost_set" class="switch mg-0"></label>
            </span>
            <span class="text-block normal-text">@lang('setting.preferences.service_cost_set')</span>
        </div>
    </div>
</div>

@php
    $disabledClass = !empty($settingPreferences->service_cost_set) ? '' : 'disabled';
@endphp
<div class="row {{$disabledClass}}">
    <div class="col-md-12">
        <div class="form-group form-with-text">
            <span class="text-block">@lang('setting.preferences.service_cost') €</span>
            {!! Form::text('service_cost', !empty($settingPreferences->service_cost)
                ? $settingPreferences->service_cost : 0, [
                'class' => 'form-control is-number medium auto-submit has-overlay mgl-i-0',
                'data-type' => 'preferences',
                'style' => 'text-align: center !important;',
                $disabledClass
            ]) !!}
        </div>
    </div>
</div>

@php
    $disabledClass = !empty($settingPreferences->service_cost_set) ? '' : 'disabled';
    $disabledClass = !empty($settingPreferences->service_cost_always_charge) ? 'disabled' : $disabledClass;
@endphp
<div class="row {{$disabledClass}}">
    <div class="col-md-12">
        <div class="form-group form-with-text">
            <span class="text-block">@lang('setting.preferences.service_cost_amount') €</span>
            {!! Form::text('service_cost_amount', !empty($settingPreferences->service_cost_amount)
                ? $settingPreferences->service_cost_amount : 0, [
                'class' => 'form-control is-number medium auto-submit has-overlay mgl-i-0',
                'data-type' => 'preferences',
                'style' => 'text-align: center !important;',
                $disabledClass
            ]) !!}
        </div>
    </div>
</div>

@php
    $disabledClass = !empty($settingPreferences->service_cost_set) ? '' : 'disabled';
@endphp
<div class="row mgb-20 {{$disabledClass}}">
    <div class="col-md-12">
        <div class="form-group form-with-text">
            <span class="pull-left mgr-8">
                <input type="checkbox"
                       name="service_cost_always_charge"
                       id="switch_service_cost_always_charge"
                       class="switch-input auto-submit"
                       data-type="preferences" {{!empty($settingPreferences->service_cost_always_charge) ? 'checked' : null}}
                       {{$disabledClass}} />
                <label for="switch_service_cost_always_charge" class="switch mg-0"></label>
            </span>
            <span class="text-block normal-text">@lang('setting.preferences.service_cost_always_charge')?</span>
        </div>
        @if($disabledClass == 'disabled')
            <input type="hidden" name="service_cost_always_charge_disabled" value="1">
        @endif
    </div>
</div>