<!-- Modal -->
<div id="modal_create_group_restaurant" class="ir-modal modal fade" role="dialog">
    {!! Form::open(['route' => $guard.'.grouprestaurant.store', 'files' => true, 'id' => 'create-form']) !!}
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
                                {!! Html::decode(Form::label('label_name', trans('grouprestaurant.name'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('grouprestaurant.name')]) !!}
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
                                {!! Html::decode(Form::label('label_logo', trans('workspace.label_logo'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                <div class="text-view">
                                    <img id="logo-restaurant" class="show-image" src="{!! url('assets/images/no-image.svg') !!}" />
                                    <input type="file" name="logo" class="manager-upload-image hidden" id="upload-logo" />
                                </div>
                                <div class="help-block">@lang('workspace.image_note') 210x210</div>
                                <div class="upload-file mgt-10">
                                    <label for="upload-logo">
                                        <img src="{!! url('assets/images/attack.svg') !!}" />
                                        <span>@lang('workspace.attack_file')</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-5 col-xs-12">
                                {!! Html::decode(Form::label('color', trans('grouprestaurant.color').":", ['class' => 'mgt-5'])) !!}
                            </div>
                            <div class="col-sm-1 col-xs-12">
                                <div id="secondary" class="general-color-picker color-picker-button">
                                    <span class="color" style="background-color: #B5B268"></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                {!! Form::text('color', '#B5B268', [
                                'id' => 'color',
                                'class' => 'form-control color-picker-input',
                                'placeholder' => '#B5B268'
                                ]) !!}
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
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('firebase_project', trans('workspace.firebase_project'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::select('firebase_project', Helper::getOptionsForFirebaseProjects(), null, [
                                'id' => 'firebase_project',
                                'class' => 'form-control select2-tag',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('facebook_id', trans('workspace.facebook_id'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('facebook_id', null, ['class' => 'form-control', 'placeholder' => trans('workspace.facebook_id')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('facebook_key', trans('workspace.facebook_key'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('facebook_key', null, ['class' => 'form-control', 'placeholder' => trans('workspace.facebook_key')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('google_id', trans('workspace.google_id'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('google_id', null, ['class' => 'form-control', 'placeholder' => trans('workspace.google_id')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('google_key', trans('workspace.google_key'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('google_key', null, ['class' => 'form-control', 'placeholder' => trans('workspace.google_key')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('apple_id', trans('workspace.apple_id'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('apple_id', null, ['class' => 'form-control', 'placeholder' => trans('workspace.apple_id')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('apple_key', trans('workspace.apple_key'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('apple_key', null, ['class' => 'form-control', 'placeholder' => trans('workspace.apple_key')]) !!}
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