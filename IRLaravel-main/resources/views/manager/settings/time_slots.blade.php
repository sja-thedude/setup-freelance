@extends('layouts.manager')

@section('content')
    <div class="row general time-slot">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('time_slot.title')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content pd-i-30">
                    <div class="row time-slot-all-type">
                        @if(!empty($settings) && !$settings->isEmpty())
                            @foreach($settings as $key => $setting)
                                @php
                                    $disabledClass = !in_array($setting->type, $settingOpenHourActive) ? 'disabled' : '';
                                @endphp
                                <fieldset class="col-sm-6 col-xs-12 time-slot-column">
                                    @include('manager.settings.partials.time_slots.form', ['key' => $key])
                                </fieldset>
                            @endforeach
                        @endif
                    </div>

                    <button type="button" class="ir-btn ir-btn-primary" data-toggle="modal" data-target="#time-slot-modal">
                        @lang('time_slot.manage_dynamic_time_slot')
                    </button>

                    @include('manager.settings.partials.time_slots.modal_time_slot_detail')
                </div>
            </div>
        </div>
    </div>
@endsection
