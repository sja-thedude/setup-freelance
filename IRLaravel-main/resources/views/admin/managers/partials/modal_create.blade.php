<!-- Modal -->
<div id="modal_create_manager" class="modal fade" role="dialog">
    {!! Form::open(['route' => $guard.'.managers.store', 'files' => true, 'id' => 'create_manager', 'class' => 'required-all-field form-reset-close']) !!}
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content text-left">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <img src="{!! url('assets/images/icons/close.png') !!}"/>
                    </button>
                    <h4 class="modal-title ir-h4">@lang('manager.add_manager')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 mgb-10">
                            <!-- Name Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12 text-uppercase">
                                    {!! Html::decode(Form::label('name', trans('manager.name'), [
                                        'class' => 'ir-h5 mgb-10'
                                    ])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::text('last_name', null, ['class' => 'form-control need-required', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 mgb-10">
                            <!-- Name Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12 text-uppercase">
                                    {!! Html::decode(Form::label('first_name', trans('manager.first_name'), [
                                        'class' => 'ir-h5 mgb-10'
                                    ])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::text('first_name', null, ['class' => 'form-control need-required', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 mgb-10">
                            <!-- Name Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12 text-uppercase">
                                    {!! Html::decode(Form::label('email', trans('manager.email'), [
                                        'class' => 'ir-h5 mgb-10'
                                    ])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::email('email', null, ['class' => 'form-control need-required', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <!-- Name Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12 text-uppercase">
                                    {!! Html::decode(Form::label('gsm', trans('manager.gsm'), [
                                        'class' => 'ir-h5 mgb-10'
                                    ])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    {!! Form::text('gsm', null, ['class' => 'form-control need-required keyup-gsm', 'required' => 'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <button class="ir-btn ir-btn-primary add-manager full-width inline-block text-center validate-submit"
                                    data-route="{!! route($guard.'.managers.store') !!}"
                                    data-method="post" 
                                    type="submit">
                                @lang('manager.submit_new_account_manager')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>