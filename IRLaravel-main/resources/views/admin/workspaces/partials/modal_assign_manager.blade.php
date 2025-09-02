<!-- Modal -->
<form action="#" method="post" id="workspace-assign-manager">
    <div id="modal_assign_manager" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content text-left">
                <div class="modal-header">
                    <button type="button" class="close reset-form" data-dismiss="modal">
                        <img src="{!! url('assets/images/icons/close.png') !!}"/>
                    </button>
                    <h4 class="modal-title ir-h4">@lang('workspace.assign')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 mgb-30">
                            <div class="noti-message">
                                @lang('workspace.choose_another_manager')
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 mgb-30">
                            <div class="ir-h5">
                                @lang('manager.account_manager')
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 mgb-30">
                            {!! Form::select('account_manager', $accountManagers, null, [
                            'class' => 'form-control select2 another-account-manager', 
                            'placeholder' => trans('workspace.select_account_manager'), 
                            'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <a class="ir-btn ir-btn-secondary full-width inline-block text-center reset-form" data-dismiss="modal">
                                @lang('common.cancel')
                            </a>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <a class="ir-btn ir-btn-primary assign-account-manager full-width inline-block text-center"
                               data-route="{!! route($guard.'.restaurants.assignAccountManager', ['']) !!}"
                               disabled="disabled">
                                @lang('workspace.assign')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>