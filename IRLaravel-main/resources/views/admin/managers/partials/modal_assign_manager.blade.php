<!-- Modal -->
<div id="modal_assign_manager_{!! $data->id !!}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content text-left">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <img src="{!! url('assets/images/icons/close.png') !!}"/>
                </button>
                <h4 class="modal-title ir-h4">@lang('manager.delete_account_manager')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 mgb-30">
                        <div class="noti-message">
                            @lang('manager.choose_another_manager')
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 mgb-30">
                        <div class="ir-h5">
                            @lang('manager.account_manager')
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 mgb-30">
                        <select class="form-control select2 another-manager">
                            <option value="">@lang('common.select')</option>
                            @if(!empty($allManagers))
                                @foreach($allManagers as $manager)
                                    @if($manager->id != $data->id)
                                        <option value="{!! $manager->id !!}">{!! $manager->name !!}</option>
                                    @endif                                     
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <a class="account-manager-reset ir-btn ir-btn-secondary full-width inline-block text-center" data-dismiss="modal">
                            @lang('common.cancel')
                        </a>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <a class="ir-btn ir-btn-primary assign-delete full-width inline-block text-center"
                           data-route="{!! route($guard.'.managers.destroy', [$data->id]) !!}"
                           data-id="{!! $data->id !!}"
                           disabled="disabled">
                            @lang('common.assign_delete')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>