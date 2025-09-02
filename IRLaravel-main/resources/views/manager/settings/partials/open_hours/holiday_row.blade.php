@php
    $newKey = $key + 1;
@endphp
<div class="holiday-row row mgb-15" data-row="{!! $newKey !!}">
    <input class="holiday-id" type="hidden" name="holiday[{!! $newKey !!}][id]" value="{!! !empty($settingHoliday->id) ? $settingHoliday->id : 0 !!}"/>
    <div class="col-sm-5 col-xs-12">
        <p class="text-center">
            <strong>@lang('setting_open_hour.from_until')</strong>
        </p>
        <div class="display-flex">
            <label class="holiday-lbl form-label mgr-15 mgt-10">@lang('setting_open_hour.holiday') {!! $newKey !!}:</label>
            <div class="ir-group-date-range area-date-range">
                {{ Form::text('holiday['.$newKey.'][date_range]', !empty($settingHoliday->start_time) && !empty($settingHoliday->end_time) ? ($settingHoliday->start_time . ' - ' . $settingHoliday->end_time) : null, [
                    'class' => 'ir-input ir-only-date-range',
                    'required' => 'required'
                ])}}
                {{ Form::hidden('holiday['.$newKey.'][start_time]', !empty($settingHoliday->start_time) ? $settingHoliday->start_time : null, [
                    'class' => 'range_start_date'
                ])}}
                {{ Form::hidden('holiday['.$newKey.'][end_time]', !empty($settingHoliday->end_time) ? $settingHoliday->end_time : null, [
                    'class' => 'range_end_date'
                ])}}
                <span class="ir-input-group-btn ir-btn-date-range date-range-trigger">
                    <button class="ir-btn-search" type="button">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.8333 3.33398H4.16667C3.24619 3.33398 2.5 4.08018 2.5 5.00065V16.6673C2.5 17.5878 3.24619 18.334 4.16667 18.334H15.8333C16.7538 18.334 17.5 17.5878 17.5 16.6673V5.00065C17.5 4.08018 16.7538 3.33398 15.8333 3.33398Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.333 1.66602V4.99935" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.66699 1.66602V4.99935" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.5 8.33398H17.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-7 col-xs-12">
        <p>&nbsp;</p>
        <div class="display-flex">
            <label class="form-label mgr-15">@lang('setting_open_hour.note_holiday')</label>
            {{ Form::textarea('holiday['.$newKey.'][description]', !empty($settingHoliday->description) ? $settingHoliday->description : null, [
                'class' => 'form-control holiday-textarea',
                'rows' => 3,
                'required' => 'required'
            ])}}
            <svg class="mgl-15 mgt-30 remove-holiday-exception cursor-pointer" width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 5H3H19" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M6 5V3C6 2.46957 6.21071 1.96086 6.58579 1.58579C6.96086 1.21071 7.46957 1 8 1H12C12.5304 1 13.0391 1.21071 13.4142 1.58579C13.7893 1.96086 14 2.46957 14 3V5M17 5V19C17 19.5304 16.7893 20.0391 16.4142 20.4142C16.0391 20.7893 15.5304 21 15 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5H17Z" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 10V16" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>