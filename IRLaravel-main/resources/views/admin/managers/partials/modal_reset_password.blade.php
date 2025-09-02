<!-- Modal -->
<div id="modal_reset_password_{!! $data->id !!}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content text-left">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <img src="{!! url('assets/images/icons/close.png') !!}"/>
                </button>
                <h4 class="modal-title ir-h4">@lang('manager.resend_invitation')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 mgb-30">
                        <div class="noti-message">
                            @lang('manager.reset_invitation_confirm')
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <a class="ir-btn ir-btn-secondary full-width inline-block text-center" data-dismiss="modal">
                            @lang('common.cancel')
                        </a>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <a class="ir-btn ir-btn-primary call-ajax full-width inline-block text-center"
                           data-route="{!! route($guard.'.managers.sendInvitation', [$data->id]) !!}"
                           data-id="{{$data->id}}"
                           data-method="post">
                            @lang('manager.send_invitation')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>