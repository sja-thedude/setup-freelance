<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="pull-left form-group payment-label-switch">
            <span>
                {!! Form::checkbox('max_mode', 1, !empty($setting->max_mode) ? $setting->max_mode : null, [
                    'id' => 'switch-max-mode'. (!empty($setting->type) ? $setting->type : 0),
                    'class' => 'switch-input auto-submit disable-fields',
                    'data-type' => 'setting_time_slot',
                    'data-disable' => 'should-disable'
                ]) !!}

                <label for="switch-max-mode{!! (!empty($setting->type) ? $setting->type : 0) !!}" class="switch mg-0"></label>
            </span>
        </div>
    </div>
</div>
<fieldset class="row has-overlay should-disable">
    <div class="col-sm-12 col-xs-12">
        <div class="row mgb-15">
            <div class="col-sm-12 col-xs-12">
                {!! Form::time('max_time', !empty($setting->max_time) ? date('H:i', strtotime($setting->max_time)) : null, [
                    'class' => 'form-control1 w-150 text-center timeslot-ip auto-submit-now h-40',
                    'data-type' => 'setting_time_slot',
                    'required' => 'required',
                    'style' => 'border: 1px solid #CFCFCD;'
                ]) !!}
            </div>
        </div>
        <div class="row mgb-15">
            <div class="col-sm-12 col-xs-12 select2-with-180">
                {!! Form::select('max_before', \App\Models\SettingTimeslot::getBeforeDays(), !empty($setting->max_before) ? $setting->max_before : null, [
                    'class' => 'form-control select-not-search auto-submit',
                    'data-type' => 'setting_time_slot',
                    'required' => 'required',
                    !in_array($setting->type, $settingOpenHourActive) ? 'disabled' : ''
                ]) !!}
            </div>
        </div>
        <div class="row mgb-15">
            <div class="col-sm-12 col-xs-12 mgb-25">
                <div class="apply-with">
                    @lang('time_slot.apply_with'):
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
                @if(!empty($dayInWeek))
                    @php
                        $maxDays = [];

                        if(!empty($setting->max_days)) {
                            $maxDays = explode(',', $setting->max_days);
                        }
                    @endphp
                    @foreach($dayInWeek as $day)
                        <label class="ir-container-cb inline-block mgr-10">
                            <span class="time-mini-day">@lang('time_slot.mini_days.'.$day)</span>
                            {!! Form::checkbox('max_days['.$day.']', $day, in_array($day, $maxDays) ? true : false, [
                                'class' => 'form-control auto-submit',
                                'data-type' => 'setting_time_slot'
                            ]) !!}
                            <span class="ir-checkmark"></span>
                        </label>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</fieldset>
