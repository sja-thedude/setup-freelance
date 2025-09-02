<!-- Modal -->
@if(!empty($auth))
    <div id="modal_change_password" class="modal fade normal-text" role="dialog">
        {!! Form::model($auth, ['route' => [$guard.'.password.changePassword'], 'method' => 'post', 'files' => true, 'class' => 'required-all-field manager-change-password form-reset-close']) !!}
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content text-left">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <img src="{!! url('assets/images/icons/close.png') !!}"/>
                        </button>
                        <h4 class="modal-title ir-h4">
                            @lang('user.change_password')
                        </h4>
                    </div>
                    <div class="modal-body">
                        @include('layouts.partials.modal_manager_change_password_fields')
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12 text-left">
                                <button type="submit" class="validate-submit ir-btn ir-btn-primary submit1 inline-block text-center">
                                    @lang('common.save')
                                </button>
                                <button type="button" class="reset-form ir-btn ir-btn-secondary-default inline-block text-center mgl-10" data-dismiss="modal">
                                    @lang('common.cancel')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endif