{!! Form::model($settingOpenHour, [
    'route' => [
         $guard.'.settingOpenHour.update',
         $workspaceId,
         $settingOpenHour->id
     ],
     'method' => 'post',
     'files' => true,
     'class' => 'update-open-hour-time-slot',
     'onsubmit' => 'return false'
 ]) !!}
    <input type="hidden" name="type" value="open-hour-time-slots"/>
    @php
        $dbTimeSlots = [];
        if(!empty($settingOpenHour->openTimeSlots) && !$settingOpenHour->openTimeSlots->isEmpty()) {
            $openTimeSlots = $settingOpenHour->openTimeSlots()->orderBy('start_time', 'ASC')->get();

            foreach($openTimeSlots as $openTimeSlot) {
                if(!empty($dbTimeSlots[$openTimeSlot->day_number])) {
                    array_push($dbTimeSlots[$openTimeSlot->day_number], $openTimeSlot);
                } else {
                    $dbTimeSlots[$openTimeSlot->day_number] = [$openTimeSlot];
                }
            }
        }
    @endphp
    @foreach($dayInWeek as $day)
        <div class="row day-item">
            <div class="col-sm-2 col-xs-2">
                <div class="text-days">
                    @lang('common.days.'.$day)
                </div>
            </div>
            <div class="col-sm-10 col-xs-10 day-time" data-day="{!! $day !!}">
                @if(!empty($dbTimeSlots[$day]))
                    @foreach($dbTimeSlots[$day] as $openTimeSlot)
                        @include('manager.settings.partials.open_hours.day_time_item', ['slot' => $openTimeSlot])
                    @endforeach
                @else
                    @include('manager.settings.partials.open_hours.day_time_item')
                @endif
            </div>
        </div>
    @endforeach

    @if(!empty($connectorsList) && !$connectorsList->isEmpty())
        <hr />

        <h3 class="h4">@lang('product.connectors')</h3><br />

        <div class="row">
            @foreach($connectorsList as $connectorItem)
                @php
                    $settingOpenHourReference = null;
                    if(!empty($settingOpenHourReferences)):
                        foreach($settingOpenHourReferences as $settingOpenHourReferenceItem):
                            if(
                                $settingOpenHourReferenceItem->provider == $connectorItem->provider
                                && $settingOpenHourReferenceItem->local_id == $settingOpenHour->id
                            ):
                                $settingOpenHourReference = $settingOpenHourReferenceItem;
                                break;
                            endif;
                        endforeach;
                    endif;
                @endphp
                <div class="col-sm-6">
                    <strong>{{ $connectorItem->getProviders($connectorItem->provider) }}</strong>
                    <div class="form-group">
                        {!! Form::text('openHourReferences['.$settingOpenHour->type.']['.$connectorItem->id.'][remote_id]', !empty($settingOpenHourReference->remote_id) ? $settingOpenHourReference->remote_id : null, [
                        'class' => 'form-control setting-open-hour auto-submit',
                        'placeholder' => trans('setting.more.remote-id')
                        ]) !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
{{Form::close()}}
