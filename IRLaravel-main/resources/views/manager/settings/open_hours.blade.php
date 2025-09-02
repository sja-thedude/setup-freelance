@extends('layouts.manager')

@php
    $flagHoliday = $settingHolidays->isEmpty();
@endphp

@section('content')
    <div class="row general opening-hour">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting_open_hour.title')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="main-content">
                    <div class="row mgb-30">
                        <div class="col-sm-12 col-xs-12">
                            <a class="ir-btn ir-btn-primary pull-left"
                               id="id-holiday-exception"
                               data-toggle="modal"
                               data-target="#holiday_exception"
                               data-holiday_empty="{!! !empty($flagHoliday) ? 1 : 0 !!}">
                                @lang('setting_open_hour.arrange_holiday')
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            @if(!$settings->isEmpty())
                                <ul class="setting-tab-nav nav nav-pills mb-3" id="opening-hours-tablist" role="tablist">
                                    @foreach($settings as $key => $settingOpenHour)

                                        @php($disableTab = ($key == \App\Models\SettingOpenHour::TYPE_IN_HOUSE && !$enableInHouse)
                                            || ($key == \App\Helpers\OrderHelper::TYPE_SELF_ORDERING && !$enableSelfOrdering))

                                        <li class="nav-item {!! empty($key) ? 'active' : '' !!} @if($disableTab) disabled @endif ">
                                            {!! Form::model($settingOpenHour, [
                                                 'route' => [
                                                     $guard.'.settingOpenHour.update',
                                                     $workspaceId,
                                                     $settingOpenHour->id
                                                 ],
                                                 'method' => 'post',
                                                 'files' => true,
                                                 'class' => 'update-open-hour',
                                             ]) !!}
                                                <input type="hidden" name="type" value="open-hours"/>
                                                <div class="pull-left form-group payment-label-switch pdt-18 pdl-30">
                                                    <span>
                                                        {!! Form::checkbox('active', 1, ($disableTab) ? false : null, [
                                                            'id' => 'switch-type-'.$settingOpenHour->id,
                                                            'class' => 'switch-input auto-submit',
                                                            'data-type' => 'setting_open_hour_active'
                                                        ]) !!}

                                                        <label for="switch-type-{!! $settingOpenHour->id !!}" class="switch mg-0"></label>
                                                    </span>
                                                </div>
                                                <a class="pull-left nav-link pd-20" id="pills-{!! $settingOpenHour->id !!}-tab" data-toggle="pill" href="#pills-{!! $settingOpenHour->id !!}"
                                                   role="tab" aria-controls="pills-{!! $settingOpenHour->id !!}" aria-selected="true">
                                                    {!! \App\Models\SettingOpenHour::getTypes($settingOpenHour->type) !!}
                                                </a>
                                            {{Form::close()}}
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="setting-tab-content tab-content" id="pills-tabContent">
                                    <p><strong>@lang('setting_open_hour.text_opening_hours')</strong></p>
                                    @foreach($settings as $key => $settingOpenHour)
                                        <div class="tab-pane fade {!! empty($key) ? 'active in' : '' !!}" id="pills-{!! $settingOpenHour->id !!}" role="tabpanel" aria-labelledby="pills-{!! $settingOpenHour->id !!}-tab">
                                            @include('manager.settings.partials.open_hours.setting_form')
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <fieldset class="day-time-item-default" disabled="disabled" style="display: none;">
            @include('manager.settings.partials.open_hours.day_time_item', ['isDefault' => true])
        </fieldset>

        <fieldset class="holiday-item-default" disabled="disabled" style="display: none;">
            @include('manager.settings.partials.open_hours.holiday_row', [
                'settingHoliday' => null,
                'key' => 0
            ])
        </fieldset>

        @include('manager.settings.partials.open_hours.modal_except_hour')
    </div>
@endsection