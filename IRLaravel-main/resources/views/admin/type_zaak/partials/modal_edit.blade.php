<!-- Modal -->
<div id="detail-{{$data->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-medium">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-detail">
                    {!! Form::model($data, [
                        'route' => [$guard.'.type-zaak.update', 
                        $data->id], 
                        'method' => 'patch', 
                        'files' => true, 
                        'class' => 'update-form',
                        'data-close_label' => trans('strings.close')
                    ]) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- account manager Field -->
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_name', trans('type_zaak.name'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('name', $data->name, ['class' => 'form-control', 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mgt-20 text-right">
                                {!! Form::submit(trans('strings.save'), ['class' => 'ir-btn ir-btn-primary save-form submit1']) !!}
                                <a href="#" class="ir-btn ir-btn-secondary mgl-20 reset-form" data-dismiss="modal">
                                    @lang('strings.cancel')
                                </a>
                            </div>
                        </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
</div>