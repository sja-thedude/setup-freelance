<!-- Modal -->
@if(!empty($auth))
    <div id="modal_manager_profile" class="modal fade normal-text" role="dialog">
        {!! Form::model($auth, ['route' => [$guard.'.users.updateProfile'], 'method' => 'patch', 'files' => true, 'class' => 'manager-profile form-reset-close']) !!}
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content text-left">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <img src="{!! url('assets/images/icons/close.png') !!}"/>
                        </button>
                        <h4 class="form-detail show-hide-area modal-title ir-h4" data-id="view-profile-area">
                            @lang('user.my_profile')
                        </h4>
                        <h4 class="form-edit show-hide-area modal-title ir-h4" data-id="edit-profile-area" style="display: none;" disabled="disabled">
                            @lang('user.edit_profile')
                        </h4>
                    </div>
                    <div class="modal-body">
                        @include('layouts.partials.modal_manager_view_profile', ['user' => $auth])
                        @include('layouts.partials.modal_manager_edit_profile', ['user' => $auth])
                    </div>
                    <div class="modal-footer">
                        <div class="row form-detail show-hide-area" data-id="view-profile-area">
                            <div class="col-sm-12 col-xs-12 text-left">
                                <a class="ir-btn ir-btn-primary inline-block text-center show-hide-actions"
                                    data-target="edit-profile-area">
                                    @lang('manager.edit_profile')
                                </a>
                                
                                <a class="ir-btn ir-btn-secondary-default cursor-pointer inline-block text-center mgl-10"
                                   data-toggle="modal" data-target="#modal_change_password">
                                    @lang('manager.change_password')
                                </a>
                            </div>
                        </div>
                        <div class="row form-edit show-hide-area" data-id="edit-profile-area" style="display: none;" disabled="disabled">
                            <div class="col-sm-12 col-xs-12 text-left">
                                <button type="submit"
                                        class="ir-btn ir-btn-primary submit1 submit-check inline-block text-center"
                                        disabled="disabled">
                                    @lang('common.save')
                                </button>
                                <a class="reset-form ir-btn ir-btn-secondary-default inline-block text-center show-hide-actions mgl-10 reset-form"
                                   data-target="view-profile-area">
                                    @lang('common.cancel')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endif