<div id="time-slot-modal" class="modal fade normal-text" role="dialog">
    <div class="modal-dialog modal-md mgt-i-0 mgb-i-0">
        <!-- Modal content-->
        <div class="modal-content bg-white text-left">
            <div class="modal-header pdb-0">
                <button type="button" class="close absolute" data-dismiss="modal">
                    <img src="{!! url('assets/images/icons/close.png') !!}"/>
                </button>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h3 class="form-detail show-hide-area modal-title time-slot-title">
                            @lang('time_slot.manage_dynamic_time_slot')
                        </h3>
                    </div>
                </div>
            </div>
            <div class="modal-body pdb-0">
                <input type="hidden" name="timezone" class="auto-detect-timezone"/>
                @if(!empty($settings) && !$settings->isEmpty())
                    <div class="row mgb-10">
                        <div class="col-sm-12 col-xs-12">
                            <div class="dynamic-tab-content tab-content" id="pills-tabContent">
                                @foreach($settings as $key => $setting)
                                    <fieldset class="time-date-picker has-overlay"
                                         data-type="{!! $setting->type !!}"
                                         {!! !in_array($setting->type, $settingOpenHourActive) ? 'disabled' : '' !!}
                                         {!! !empty($key) ? 'style="display: none;"' : '' !!}>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="input-group date-group display-flex">
                                                    @php
                                                        $datePickerOptions = [
                                                            'class' => 'form-control datepicker time-slot-detail-date',
                                                            'data-date-format' => 'dd/mm/yy',
                                                            'data-set-date' => 'now',
                                                            'data-route' => route('manager.settingTimeSlot.renderTimeSlotDetail', [
                                                                 'workspaceId' => $workspaceId,
                                                                 'settingId' => $setting->id
                                                            ]),
                                                            'required'
                                                        ];
                                                    @endphp

                                                    {!! Form::text('date' , null, $datePickerOptions) !!}

                                                    <span class="date-icon {!! !in_array($setting->type, $settingOpenHourActive) ? '' : 'time-slot-icon' !!}">
                                                        <svg class="mgt-1" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M16 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M8 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M3 10H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-6 col-xs-12 col-sm-offset-3">
                        @if(!empty($settings) && !$settings->isEmpty())
                            <ul class="dynamic-tab-nav nav nav-pills mb-3" id="pills-tab" role="tablist">
                                @foreach($settings as $key => $setting)
                                    <li class="nav-item-tab nav-item col-sm-6 col-xs-6 {!! !empty($key) ? 'mgl-i-0' : 'active' !!}"
                                        data-type="{!! $setting->type !!}">
                                        <a class="nav-link text-center" id="pills-{!! $setting->type !!}-tab" data-toggle="pill" href="#pills-{!! $setting->type !!}"
                                           role="tab" aria-controls="pills-{!! $setting->type !!}" aria-selected="true">
                                            @lang('time_slot.types.'. $setting->type)
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="row time-slot-detail-fields time-slot-detail-height">
                    <div class="col-sm-12 col-xs-12">
                        @if(!empty($settings) && !$settings->isEmpty())
                            <div class="dynamic-tab-content tab-content" id="pills-tabContent">
                                @foreach($settings as $key => $setting)
                                    <div class="tab-pane fade {!! empty($key) ? 'active in' : '' !!}"
                                         id="pills-{!! $setting->type !!}"
                                         data-dynamic-id="{!! $setting->type !!}"
                                         role="tabpanel"
                                         aria-labelledby="pills-{!! $setting->type !!}-tab">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>