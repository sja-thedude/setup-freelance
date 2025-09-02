{!! Form::model($settingTimeSlot, [
     'route' => [
         $guard.'.settingTimeSlot.updateTimeSlotDetail',
         $workspaceId,
         $settingTimeSlot->id
     ],
     'method' => 'post',
     'files' => true,
     'class' => 'time-slot-detail',
]) !!}
    <div class="row col-header text-center mgb-15 mgt-15">
        <div class="col-sm-3 col-xs-12">
            @lang('time_slot.time')
        </div>
        <div class="col-sm-3 col-xs-12">
            @lang('time_slot.time_mode')
        </div>
        <div class="col-sm-3 col-xs-12">
            @lang('time_slot.max_best')
        </div>
        <div class="col-sm-3 col-xs-12">
            @lang('time_slot.each')
            <span class="time-detail-day">
                @if($dayOfWeek >= 0)
                    @lang('common.days.'. $dayOfWeek)?
                @endif
            </span>
        </div>
    </div>
    <fieldset class="has-overlay row col-content text-center" {!! empty($settingOpenHour->active) ? 'disabled' : '' !!}>
        <div class="col-sm-12 col-xs-12">
            @if(!empty($settingTimeSlotDetails) && !$settingTimeSlotDetails->isEmpty())
                @foreach($settingTimeSlotDetails as $key => $settingTimeSlotDetail)
                    <div class="row mgb-5">
                        <div class="col-sm-3 col-xs-12 timebox">
                            <input type="hidden" name="data[{!! $settingTimeSlotDetail->id !!}][id]" value="{!! $settingTimeSlotDetail->id !!}" data-item-id="{{(!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0)}}" />
                            <input type="hidden" name="data[{!! $settingTimeSlotDetail->id !!}][time]" value="{!! date('H:i', strtotime($settingTimeSlotDetail->time)) !!}" data-item-id="{{(!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0)}}"/>
                            <input type="hidden" name="data[{!! $settingTimeSlotDetail->id !!}][type]" value="{!! $settingTimeSlotDetail->type !!}" data-item-id="{{(!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0)}}"/>
                            {!! date('H:i', strtotime($settingTimeSlotDetail->time)) !!}
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            <div class="form-group payment-label-switch">
                                <span>
                                    {!! Form::checkbox('data['.$settingTimeSlotDetail->id.'][active]', 1, !empty($settingTimeSlotDetail->active), [
                                        'id' => 'detail-switch-max-mode'. (!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0),
                                        'class' => 'switch-input auto-submit',
                                        'data-type' => 'setting_time_slot_detail',
                                        'data-item-id' => (!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0)
                                    ]) !!}

                                    <label for="detail-switch-max-mode{!! (!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0) !!}" class="switch mg-0"></label>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            {!! Form::number('data['.$settingTimeSlotDetail->id.'][max]', !empty($settingTimeSlotDetail->max) ? $settingTimeSlotDetail->max : 0, [
                                'class' => 'form-control max-field validate-min width-70 text-center timeslot-ip auto-submit mg-auto',
                                'data-min' => 0,
                                'data-type' => 'setting_time_slot_detail',
                                'required' => 'required',
                                'data-item-id' => (!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0)
                            ]) !!}
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            <label class="ir-container-cb inline-block mgr-10">
                                {!! Form::checkbox('data['.$settingTimeSlotDetail->id.'][repeat]', 1, !empty($settingTimeSlotDetail->repeat), [
                                    'class' => 'form-control auto-submit',
                                    'data-type' => 'setting_time_slot_detail',
                                    'data-item-id' => (!empty($settingTimeSlotDetail->id) ? $settingTimeSlotDetail->id : 0)
                                ]) !!}
                                <span class="ir-checkmark"></span>
                            </label>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </fieldset>
{!! Form::close() !!}