@extends('layouts.manager')

@section('content')
    <div class="row general">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting.preferences.preferences')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    {!! Form::model($settingPreferences, [
                        'route' => [$guard.'.settingPreferences.updateOrCreate', 
                        $tmpWorkspace->id], 
                        'method' => 'post', 
                        'files' => true, 
                        'class' => 'update-form-preferences',
                    ]) !!}
                        <div class="row">
                            <div class="col-md-4 pdr-25">
                                <h4 class="label-title">@lang('setting.more.takeout')</h4>
                                @php
                                    $_settingTakeout = $tmpWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_TAKEOUT)->first();
                                    $disabledClass = !empty($_settingTakeout) && !$_settingTakeout->active ? 'disabled' : '';
                                @endphp
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-with-text">
                                            <span class="text-block">@lang('setting.preferences.minimum_waiting_time')</span>
                                            {!! Form::text('takeout_min_time', !empty($settingPreferences->takeout_min_time)
                                                ? $settingPreferences->takeout_min_time : config('settings.preferences.takeout_min_time'), [
                                            'class' => 'form-control medium auto-submit has-overlay', 
                                            'data-type' => 'preferences',
                                            'style' => 'text-align: center !important;',
                                            $disabledClass
                                            ]) !!}
                                            <span class="text-block">@lang('setting.preferences.minute')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-with-text">
                                            <span class="text-block">@lang('setting.preferences.order_before_days'):</span>
                                            {!! Form::text('takeout_day_order', !empty($settingPreferences->takeout_day_order)
                                                ? $settingPreferences->takeout_day_order : config('settings.preferences.takeout_day_order'), [
                                            'class' => 'form-control auto-submit', 
                                            'data-type' => 'preferences',
                                            $disabledClass
                                            ]) !!}
                                            <div class="info-block">@lang('setting.preferences.max_days')</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 pdl-25">
                                <h4 class="label-title">@lang('setting.more.delivery')</h4>
                                @php
                                    $_settingDelivery = $tmpWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_DELIVERY)->first();
                                    $disabledClass = !empty($_settingDelivery) && !$_settingDelivery->active ? 'disabled' : '';
                                @endphp
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-with-text">
                                            <span class="text-block">@lang('setting.preferences.minimum_waiting_time')</span>
                                            {!! Form::text('delivery_min_time', !empty($settingPreferences->delivery_min_time)
                                                ? $settingPreferences->delivery_min_time : config('settings.preferences.delivery_min_time'), [
                                            'class' => 'form-control medium auto-submit', 
                                            'data-type' => 'preferences',
                                            'style' => 'text-align: center !important;',
                                            $disabledClass
                                            ]) !!}
                                            <span class="text-block">@lang('setting.preferences.minute')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-with-text">
                                            <span class="text-block">@lang('setting.preferences.order_before_days'):</span>
                                            {!! Form::text('delivery_day_order', !empty($settingPreferences->delivery_day_order)
                                                ? $settingPreferences->delivery_day_order : config('settings.preferences.delivery_day_order'), [
                                            'class' => 'form-control auto-submit', 
                                            'data-type' => 'preferences',
                                            $disabledClass
                                            ]) !!}
                                            <div class="info-block">@lang('setting.preferences.max_days')</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row line-collection-delivery">
                            <div class="col-md-12">
                                <div class="form-group form-with-text">
                                    <span class="text-block">@lang('setting.preferences.reminder_message'):</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-with-text">
                                    {!! Form::text('mins_before_notify', !empty($settingPreferences->mins_before_notify) ? $settingPreferences->mins_before_notify : 15, [
                                    'class' => 'form-control auto-submit', 
                                    'data-type' => 'preferences',
                                    ]) !!}
                                    <span class="text-block normal-text">@lang('setting.preferences.minutes_before_ready')</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-with-text mt-7">
                                    <span class="pull-left mgr-8">
                                        <input type="checkbox" name="use_sms_whatsapp" id="switch_use_sms_whatsapp" 
                                           class="switch-input auto-submit" 
                                           data-type="preferences" {{!empty($settingPreferences->use_sms_whatsapp) ? 'checked' : null}} disabled/>
                                        <label for="switch_use_sms_whatsapp" class="switch mg-0"></label>
                                    </span>
                                    <span class="text-block normal-text">@lang('setting.preferences.sms_whatsapp')</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-with-text mt-7">
                                    <span class="pull-left mgr-8">
                                        <input type="checkbox" name="use_email" id="switch_use_email" 
                                           class="switch-input auto-submit" 
                                           data-type="preferences" {{!empty($settingPreferences->use_email) ? 'checked' : null}}/>
                                        <label for="switch_use_email" class="switch mg-0"></label>
                                    </span>
                                    <span class="text-block normal-text">@lang('setting.preferences.email')</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-with-text mt-7">
                                    <span class="pull-left mgr-8">
                                        <input type="checkbox" name="receive_notify" id="switch_receive_notify" 
                                           class="switch-input auto-submit" 
                                           data-type="preferences" {{!empty($settingPreferences->receive_notify) ? 'checked' : null}}/>
                                        <label for="switch_receive_notify" class="switch mg-0"></label>
                                    </span>
                                    <span class="text-block normal-text">@lang('setting.preferences.push_notification_for_mobile_app')</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-with-text">
                                    <span class="text-block">@lang('setting.preferences.play_sound_with_new_order')</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-with-text">
                                    <span class="pull-left mgr-8">
                                        <input type="checkbox" name="sound_notify" id="switch_sound_notify" 
                                           class="switch-input auto-submit" 
                                           data-type="preferences" {{!empty($settingPreferences->sound_notify) ? 'checked' : null}}/>
                                        <label for="switch_sound_notify" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-with-text">
                                    <span class="text-block">
                                        <label class="is_ingredient_deletion" for="is_ingredient_deletion">
                                            @lang('setting.preferences.activate_addition_option')
                                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom"
                                               data-html="true" title="<img width='100%' src='{{ url('/assets/images/tooltip-img.png') }}'/>"></i>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-with-text">
                                    {!! Form::select('opties_id', $options, !empty($settingPreferences) ? $settingPreferences->opties_id : null, [
                                    'class' => 'form-control select2 pull-left auto-submit', 
                                    'data-type' => 'preferences',
                                    'placeholder' => trans('setting.preferences.choose_option')
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        @if(!empty($serviceCost) && !empty($serviceCost->active))
                            @include('manager.settings.partials.preferences.service_cost')
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-with-text">
                                    <span class="text-block">
                                        <label class="text_display_holiday" for="text_display_holiday">
                                            @lang('setting.preferences.text_display_holiday')
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-with-text">
                                    @if(!empty($settingPreferences->holiday_text))
                                        @php $content = explode("\n", $settingPreferences->holiday_text); @endphp
                                        @foreach($content as $text)
                                            <p style="color: #000000;font-weight: bold">{{$text}}</p>
                                        @endforeach
                                    @endif
                                    <a class="ir-btn ir-btn-primary pull-left"
                                       id="id-holiday-exception"
                                       data-toggle="modal"
                                       data-target="#holiday_exception"
                                       data-holiday_empty="{!! !empty($flagHoliday) ? 1 : 0 !!}">
                                        @if(!empty($settingPreferences->holiday_text))
                                            @lang('setting.preferences.button_display_holiday_changed')
                                        @else
                                            @lang('setting.preferences.button_display_holiday')
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                            $tableOrderingPopUpText = $settingPreferences->workspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::TABLE_ORDERING)->first();
                        ?>
                        {{-- Display table ordering pop up text --}}
                        @if(!empty($tableOrderingPopUpText) && $tableOrderingPopUpText->active == 1)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-with-text">
                                        <span class="text-block">
                                            <label class="text_display_table_ordering_pop_up_text" for="text_display_table_ordering_pop_up">
                                                @lang('setting.preferences.display_table_ordering_pop_up_text_changed')
                                            </label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-with-text">
                                        @if(!empty($settingPreferences->table_ordering_pop_up_text))
                                            @php $content = explode("\n", $settingPreferences->table_ordering_pop_up_text); @endphp
                                            @foreach($content as $text)
                                                <p style="color: #000000;font-weight: bold">{{$text}}</p>
                                            @endforeach
                                        @endif
                                        <a class="ir-btn ir-btn-primary pull-left"
                                           id="id-holiday-exception"
                                           data-toggle="modal"
                                           data-target="#table_ordering_popup_exception"
                                           data-holiday_empty="{!! !empty($flagHoliday) ? 1 : 0 !!}">
                                            @if(!empty($settingPreferences->table_ordering_pop_up_text))
                                                @lang('setting.preferences.button_display_holiday_changed')
                                            @else
                                                @lang('setting.preferences.button_display_holiday')
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <?php
                           $selfOrderingPopUpText = $settingPreferences->workspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::SELF_ORDERING)->first();
                        ?>
                        {{-- Display self ordering pop up text --}}
                        @if(!empty($selfOrderingPopUpText) && $selfOrderingPopUpText->active == 1)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-with-text">
                                        <span class="text-block">
                                            <label class="text_display_self_ordering_pop_up_text" for="text_display_self_ordering_pop_up">
                                                @lang('setting.preferences.display_self_ordering_pop_up_text_changed')
                                            </label>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group form-with-text">
                                        @if(!empty($settingPreferences->self_ordering_pop_up_text))
                                            @php $content = explode("\n", $settingPreferences->self_ordering_pop_up_text); @endphp
                                            @foreach($content as $text)
                                                <p style="color: #000000;font-weight: bold">{{$text}}</p>
                                            @endforeach
                                        @endif
                                        <a class="ir-btn ir-btn-primary pull-left"
                                           id="id-holiday-exception"
                                           data-toggle="modal"
                                           data-target="#self_ordering_popup_exception"
                                           data-holiday_empty="{!! !empty($flagHoliday) ? 1 : 0 !!}">
                                            @if(!empty($settingPreferences->self_ordering_pop_up_text))
                                                @lang('setting.preferences.button_display_holiday_changed')
                                            @else
                                                @lang('setting.preferences.button_display_holiday')
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @include('manager.settings.partials.preferences.modal_holiday_text_display')
                        @include('manager.settings.partials.preferences.modal_table_ordering_pop_up_text_display')
                        @include('manager.settings.partials.preferences.modal_self_ordering_pop_up_text_display')
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection