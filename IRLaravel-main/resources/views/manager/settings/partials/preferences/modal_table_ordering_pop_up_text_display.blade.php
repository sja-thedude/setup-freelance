<div id="table_ordering_popup_exception" class="modal fade normal-text opening-hour" role="dialog">
    <input class="holiday-reload" type="hidden" name="holiday-reload" value="1"/>
    <div class="modal-dialog modal-lg holiday-exception">
        <!-- Modal content-->
        <div class="modal-content text-left">
            <div class="modal-header">
                <button type="button" class="close absolute" data-dismiss="modal">
                    <img src="{!! url('assets/images/icons/close.png') !!}"/>
                </button>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h4><strong>@lang('setting.preferences.info_display_holiday')</strong></h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="display-flex full-width">
                        <label class="form-label mgr-15">@lang('setting_open_hour.note_holiday')</label>
                        {{ Form::textarea('table_ordering_pop_up_text', null, [
                            'class' => 'form-control holiday-textarea auto-submit',
                            'rows' => 10
                        ])}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-center">
                        <button type="submit" class="ir-btn ir-btn-primary">
                            @lang('common.save')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>