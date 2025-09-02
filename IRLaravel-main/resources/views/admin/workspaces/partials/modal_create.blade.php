<!-- Modal -->
<div id="form-create" class="ir-modal modal fade" role="dialog">
    {!! Form::open(['route' => $guard.'.restaurants.store', 'files' => true, 'id' => 'create-form']) !!}
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <button type="button" class="close reset-form" data-dismiss="modal">
                    <img src="{!! url('assets/images/icons/close.png') !!}"/>
                </button>
                <div class="modal-body">
                    <div class="form-create">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- account manager Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_name', trans('workspace.workspace_name'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- slug Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_slug', trans('workspace.workspace_subdomain'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12 slug-special">
                                                {!! Form::text('slug', null, ['class' => 'form-control display-flex fill-slug', 'required' => 'required']) !!}
                                                <div class="display-flex subdomain-lbl">.{!! $domain !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group text-right">
                                    <label class="ir-label mgr-30">@lang('workspace.label_status')</label>
                                    <span>
                                        <input type="checkbox" name="active" id="switch-add" class="switch-input"/>
                                        <label for="switch-add" class="switch pull-right mgr-35"></label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- account manager Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('account_manager', trans('workspace.label_account_manager'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('account_manager_id', $accountManagers, null, ['class' => 'form-control select2', 'placeholder' => trans('workspace.select_account_manager'), 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- gsm Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_gsm', trans('workspace.label_gsm'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('gsm', null, ['class' => 'form-control keyup-gsm', 'required' => 'required', 'placeholder' => trans('workspace.placeholder_gsm')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- manager Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_name', trans('workspace.label_name'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('manager_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.label_name')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- surname Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_surname', trans('workspace.label_surname'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('surname', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.label_surname')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- address Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_address', trans('workspace.label_address'), ['class' => 'ir-label'])) !!}
                                                <b class="ml-2 font-weight-normal" data-toggle="tooltip" data-placement="bottom"
                                                    title="@lang('workspace.location_helper')">
                                                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                                                </b>
                                            </div>
                                            <div class="col-sm-12 col-xs-12 use-maps">
                                                <div class="maps">
                                                    {!! Form::text('address', null, [
                                                    'class' => 'form-control location', 
                                                    'required' => 'required', 
                                                    'placeholder' => trans('workspace.placeholder_address')
                                                    ]) !!}
                                                    
{{--                                                    <img class="maps-marker" src="{!! asset('assets/images/map-marker-line.svg') !!}"/>--}}
                                                    <div id="modal-box-map" class="modal fade signin-frm" tabindex="-1" role="dialog" aria-modal="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-medium" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-body">
                                                                    <div id="modal-box-map-view" style="height: 500px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::hidden('address_lat', null, ['class' => 'latitude','required' => 'required']) !!}
                                                    {!! Form::hidden('address_long', null, ['class' => 'longitude','required' => 'required']) !!}
                                                </div>
                                                
                                                <ul class="place-results"></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- btw_nr Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_btw_nr', trans('workspace.label_btw_nr'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('btw_nr', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.placeholder_btw_nr')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- type Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_type', trans('workspace.label_type'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('types[]', $types, null, [
                                                'id' => 'types',
                                                'class' => 'form-control select2-tag', 
                                                'multiple' => true,
                                                'data-route' => route($guard.'.type-zaak.store'),
                                                'required' => 'required'
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- language Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_language', trans('workspace.label_language'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('language', $languages, null, ['class' => 'form-control select2', 'placeholder' => trans('workspace.label_language'), 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Active languages -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                <label class="ir-label">{{ trans('workspace.active_languages') }}</label>
                                            </div>
                                            @foreach(collect(config('languages'))->sortBy(function ($value, $key) {return $key !== 'nl';})->toArray() as $locale => $language)
                                            <div class="col-sm-3">
                                                <div class="display-flex">
                                                    <div>
                                                        <input type="checkbox" id="active_languages_new_{{ $locale }}" name="active_languages[]" value="{{ $locale }}" class="switch-input" {{ $locale == 'nl' ? '' : '' }} {{ $locale == 'nl' ? 'checked' : '' }} />
                                                        <label for="active_languages_new_{{ $locale }}" class="switch"></label>
                                                    </div>
                                                    <div class="mgl-10">
                                                        <span>{{ trans('common.languages.' . $locale) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- Active languages -->
                                <div class="row">
                                    <!-- email Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_email', trans('workspace.label_email'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('email', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.placeholder_email')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- VAT system Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_vat_system', trans('workspace.label_vat_system'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('country_id', $countries, null, ['class' => 'form-control select2', 'placeholder' => trans('workspace.placeholder_country_id'), 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- email_to Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('email_to', trans('workspace.email_to'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('email_to', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.email_to')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- logo Field -->
                                <div class="row form-group">
                                    <div class="col-sm-12 col-xs-12">
                                        {!! Html::decode(Form::label('label_logo', trans('workspace.label_logo'), ['class' => 'ir-label'])) !!}
                                    </div>
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="text-view">
                                            <img class="show-image" src="{!! url('assets/images/no-image.svg') !!}" />
                                            <input type="file" name="uploadAvatar" class="manager-upload-image hidden" id="upload-avatar" />
                                        </div>
                                        <div class="help-block">@lang('workspace.image_note') 210x210</div>
                                        <div class="upload-file mgt-10">
                                            <label for="upload-avatar">
                                                <img src="{!! url('assets/images/attack.svg') !!}" />
                                                <span>@lang('workspace.attack_file')</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mgt-20 text-right">
                                {!! Form::submit(trans('workspace.send_invitation'), ['class' => 'ir-btn ir-btn-primary save-form submit1']) !!}
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
