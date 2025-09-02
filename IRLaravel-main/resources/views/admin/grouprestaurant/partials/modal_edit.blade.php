<!-- Modal -->
<div id="modal_edit_group_restaurant_{{$groupRestaurant->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close reset-form" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-detail">
                    {!! Form::model($groupRestaurant, [
                        'route' => [$guard.'.grouprestaurant.update',
                        $groupRestaurant->id],
                        'method' => 'patch',
                        'files' => true,
                        'class' => 'update-form',
                        'data-close_label' => trans('strings.close')
                    ]) !!}
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('label_name', trans('grouprestaurant.name'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('name', $groupRestaurant->name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('grouprestaurant.name')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('label_description', trans('strings.banner.label_description'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::textarea('description', $groupRestaurant->description, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('strings.banner.label_description')]) !!}
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
                                    @php
                                        $image = !empty($groupRestaurant->groupRestaurantAvatar) ? $groupRestaurant->groupRestaurantAvatar->full_path : url('assets/images/no-image.svg');
                                    @endphp
                                    <img id="logo-restaurant" class="show-image" src="{!! $image !!}" />
                                    <input type="file" name="logo" class="manager-upload-image hidden" id="upload-logo-{{$groupRestaurant->id}}" />
                                </div>
                                <div class="help-block">@lang('workspace.image_note') 210x210</div>
                                <div class="upload-file mgt-10">
                                    <label for="upload-logo-{{$groupRestaurant->id}}">
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
                                    <span class="color" style="background-color: {{!empty($groupRestaurant->color)?$groupRestaurant->color:'#B5B268'}}"></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                {!! Form::text('color', !empty($groupRestaurant->color)?$groupRestaurant->color:'#B5B268', [
                                'id' => 'color',
                                'class' => 'form-control color-picker-input',
                                'placeholder' => !empty($groupRestaurant->color)?$groupRestaurant->color:'#B5B268'
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
                                {!! Form::select('restaurants[]', $restaurants, $groupRestaurant->groupRestaurantWorkspaces->pluck('id')->toArray(), [
                                'id' => 'restaurants',
                                'class' => 'form-control select2-tag',
                                'multiple' => true,
                                'data-route' => route($guard.'.restaurants.store'),
                                'required' => 'required'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    @php
                        $options = Helper::getOptionsForFirebaseProjects()
                    @endphp
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('firebase_project', trans('workspace.firebase_project'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::select('firebase_project', $options, $groupRestaurant->firebase_project, [
                                'id' => 'firebase_project',
                                'class' => 'form-control select2-tag'
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
                                {!! Form::text('facebook_id', $groupRestaurant->facebook_id, ['class' => 'form-control', 'placeholder' => trans('workspace.facebook_id')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('facebook_key', trans('workspace.facebook_key'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('facebook_key', $groupRestaurant->facebook_key, ['class' => 'form-control', 'placeholder' => trans('workspace.facebook_key')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('google_id', trans('workspace.google_id'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('google_id', $groupRestaurant->google_id, ['class' => 'form-control', 'placeholder' => trans('workspace.google_id')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('google_key', trans('workspace.google_key'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('google_key', $groupRestaurant->google_key, ['class' => 'form-control', 'placeholder' => trans('workspace.google_key')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('apple_id', trans('workspace.apple_id'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('apple_id', $groupRestaurant->apple_id, ['class' => 'form-control', 'placeholder' => trans('workspace.apple_id')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('apple_key', trans('workspace.apple_key'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text('apple_key', $groupRestaurant->apple_key, ['class' => 'form-control', 'placeholder' => trans('workspace.apple_key')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Html::decode(Form::label('token', trans('workspace.token'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-sm-8 col-xs-12">
                                {{$groupRestaurant->token}}
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