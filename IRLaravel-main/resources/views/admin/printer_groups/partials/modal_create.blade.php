<!-- Modal -->
<div id="modal_create_printer_group" class="ir-modal modal fade" role="dialog">
    {!! Form::open(['route' => $guard.'.printergroup.store', 'files' => true, 'id' => 'create-form']) !!}
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close reset-form" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-create">
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('label_name', trans('printer_group.name'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('printer_group.name')]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('label_description', trans('strings.banner.label_description'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::textarea('description', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('strings.banner.label_description')]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('label_type', trans('menu.restaurants'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::select('restaurants[]', $restaurants, null, [
                                'id' => 'restaurants',
                                'class' => 'form-control select2-tag',
                                'multiple' => true,
                                'data-route' => route($guard.'.restaurants.store'),
                                'required' => 'required'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mgt-20 text-right">
                            {!! Form::submit(trans('strings.submit'), ['class' => 'ir-btn ir-btn-primary save-form']) !!}
                            <a href="javascript:;" class="ir-btn ir-btn-secondary mgl-20 reset-form" data-dismiss="modal">
                                @lang('strings.cancel')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{Form::close()}}
</div>