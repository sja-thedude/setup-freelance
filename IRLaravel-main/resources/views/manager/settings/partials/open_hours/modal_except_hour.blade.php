<div id="holiday_exception" class="modal fade normal-text" role="dialog">
    {!! Form::open(['route' => [$guard.'.settingOpenHour.storeHolidayException', $workspaceId], 'method' => 'post', 'files' => true, 'class' => 'holiday-exception form-reset-close only-reset-validate']) !!}
        <input type="hidden" name="timezone" class="auto-detect-timezone"/>
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content text-left">
                <div class="modal-header">
                    <button type="button" class="close absolute" data-dismiss="modal">
                        <img src="{!! url('assets/images/icons/close.png') !!}"/>
                    </button>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 empty-holiday" {!! empty($flagHoliday) ? 'style="display: none;"' : '' !!}>
                            <h3 class="form-detail show-hide-area modal-title ir-h3">
                                @lang('setting_open_hour.holiday')
                            </h3>
                        </div>
                        <div class="col-sm-12 col-xs-12 exist-holiday" {!! !empty($flagHoliday) ? 'style="display: none;"' : '' !!}>
                            <div class="pull-left">
                                <h3 class="form-detail show-hide-area modal-title ir-h3 text-left-i">
                                    @lang('setting_open_hour.holiday')
                                </h3>
                            </div>
                            <div class="pull-right pdr-30">
                                <a class="add-holiday ir-btn ir-btn-secondary full-width inline-block text-center">
                                    @lang('setting_open_hour.new_holiday')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="empty-holiday" {!! empty($flagHoliday) ? 'style="display: none;"' : '' !!}>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 text-center">
                                @lang('setting_open_hour.holiday_none')
                            </div>
                        </div>
                    </div>
                    <div class="exist-holiday" {!! !empty($flagHoliday) ? 'style="display: none;"' : '' !!}>
                        @if(!$settingHolidays->isEmpty())
                            @foreach($settingHolidays as $key => $settingHoliday)
                                @include('manager.settings.partials.open_hours.holiday_row')
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 text-center">
                            <a class="add-new-holiday-empty ir-btn ir-btn-secondary inline-block text-center empty-holiday" {!! empty($flagHoliday) ? 'style="display: none;"' : '' !!}>
                                @lang('setting_open_hour.new_holiday')
                            </a>
                            <button type="submit" class="ir-btn ir-btn-primary exist-holiday" {!! !empty($flagHoliday) ? 'style="display: none;"' : '' !!}>
                                @lang('common.save')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>