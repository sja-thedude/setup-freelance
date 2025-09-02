<div class="row day-time-item">
    <div class="col-sm-12 col-xs-12">
        <div class="pull-left time-area">
            @if(!empty($isDefault))
                <input type="time" class="timeNew start-time" name="start_time" value="00:00" /> -
                <input type="time" class="timeNew end-time" name="end_time" value="23:59" />

                <input class="day-time-ip" type="hidden" name="start_end_time" value="00:00 - 23:59" maxlength="13"/>
                <input type="hidden" class="day-time-id" name="slot_id" value="0"/>
            @else
                @if(!empty($slot))
                    <input type="time" class="timeNew start-time" name="start_time" value="{!! date('H:i', strtotime($slot->start_time)) !!}" /> -
                    <input type="time" class="timeNew end-time" name="end_time" value="{!! date('H:i', strtotime($slot->end_time)) !!}" />

                    <input class="day-time-ip" type="hidden" maxlength="13" readonly="readonly"
                           name="start_end_time[{!! !empty($day) ? $day : 0 !!}][]"
                           value="{!! date('H:i', strtotime($slot->start_time)) !!} - {!! date('H:i', strtotime($slot->end_time)) !!}"/>
                    <input type="hidden" class="day-time-id" name="slot_id[{!! !empty($day) ? $day : 0 !!}][]" value="{!! $slot->id !!}"/>
                @else
                    <div class="text-day-off">
                        @lang('setting_open_hour.closed')
                    </div>
                @endif
            @endif
        </div>
        <div class="pull-left action-area">
            @if(!empty($isDefault) || !empty($slot))
                <svg class="cursor-pointer remove-time" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5 5H4.16667H17.5" stroke="#828282" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.66602 4.99935V3.33268C6.66602 2.89065 6.84161 2.46673 7.15417 2.15417C7.46673 1.84161 7.89066 1.66602 8.33268 1.66602H11.666C12.108 1.66602 12.532 1.84161 12.8445 2.15417C13.1571 2.46673 13.3327 2.89065 13.3327 3.33268V4.99935M15.8327 4.99935V16.666C15.8327 17.108 15.6571 17.532 15.3445 17.8445C15.032 18.1571 14.608 18.3327 14.166 18.3327H5.83268C5.39065 18.3327 4.96673 18.1571 4.65417 17.8445C4.34161 17.532 4.16602 17.108 4.16602 16.666V4.99935H15.8327Z" stroke="#828282" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.33398 9.16602V14.166" stroke="#828282" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11.666 9.16602V14.166" stroke="#828282" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endif

            <svg class="cursor-pointer add-more-time"
                 width="16" height="16"
                 viewBox="0 0 16 16" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <rect width="16" height="15.9042" rx="5" fill="#B5B268"/>
                <path d="M8.33398 5.20898V11.3939" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5.2207 8.30078H11.4429" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>
