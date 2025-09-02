@extends('layouts.manager')

@section('content')
    <div class="row general page-general">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting.general.general')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    {!! Form::model($tmpWorkspace, [
                        'route' => [$guard.'.settings.updateWorkspace', $tmpWorkspace->id], 
                        'method' => 'post', 
                        'files' => true, 
                        'class' => 'general-update-workspace', 
                        'data-close_label' => trans('strings.close')
                    ]) !!}
                        @php $check = !empty($auth->workspace) ? 'readonly  disabled' : ''; @endphp
                    
                        @if(!empty($check))
                            {!! Form::hidden('name', $tmpWorkspace->name, ['class' => 'form-control auto-submit', 'data-type' => 'general_workspace']) !!}
                            {!! Form::hidden('btw_nr', $tmpWorkspace->btw_nr, ['class' => 'form-control auto-submit', 'data-type' => 'general_workspace']) !!}
                            {!! Form::hidden('address', $tmpWorkspace->address, ['class' => 'form-control auto-submit', 'data-type' => 'general_workspace']) !!}
                        @endif
                        <div class="row">
                            <div class="col-sm-6 pdr-25">
                                <h4 class="label-title">@lang('setting.general.company')</h4>
                                    <div class="row">
                                        <!-- name Field -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::text('name', $tmpWorkspace->name, [
                                                'class' => 'form-control auto-submit', 
                                                'data-type' => 'general_workspace',
                                                'required' => 'required', 
                                                'placeholder' => trans('workspace.placeholder_name'),
                                                $check
                                                ]) !!}
                                            </div>
                                        </div>
                                        <!-- btw_nr Field -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::text('btw_nr', $tmpWorkspace->btw_nr, [
                                                'class' => 'form-control auto-submit',
                                                'data-type' => 'general_workspace',
                                                'required' => 'required', 
                                                'placeholder' => trans('workspace.placeholder_btw_nr'),
                                                $check
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- types Field -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::select('types[]', $types, $tmpWorkspace->workspaceCategories->pluck('id')->toArray(), [
                                                'id' => 'types',
                                                'class' => 'form-control select2-type auto-submit', 
                                                'data-type' => 'general_workspace',
                                                'multiple' => true,
                                                'required' => 'required'
                                                ]) !!}
                                            </div>
                                        </div>
                                        <!-- address Field -->
                                        <div class="col-sm-6 use-maps">
                                            <div class="form-group maps">
                                                {!! Form::text('address', $tmpWorkspace->address, [
                                                'class' => 'form-control location auto-submit', 
                                                'data-type' => 'general_workspace',
                                                'required' => 'required', 
                                                'placeholder' => trans('workspace.placeholder_address'),
                                                $check
                                                ]) !!}
        
                                                @if(empty($auth->workspace))
{{--                                                    <img class="maps-marker" src="{!! asset('assets/images/map-marker-line.svg') !!}"/>--}}
                                                @endif
                                                <div id="modal-box-map" class="modal fade signin-frm" tabindex="-1"
                                                     role="dialog" aria-modal="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-medium"
                                                         role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <div id="modal-box-map-view" style="height: 500px;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {!! Form::hidden('address_lat', $tmpWorkspace->address_lat, [
                                                    'class' => 'latitude auto-submit',
                                                    'data-type' => 'general_workspace',
                                                    'required' => 'required'
                                                ]) !!}
                                                {!! Form::hidden('address_long', $tmpWorkspace->address_long, [
                                                    'class' => 'longitude auto-submit',
                                                    'data-type' => 'general_workspace',
                                                    'required' => 'required'
                                                ]) !!}
                                            </div>
                                            
                                            <ul class="place-results"></ul>
                                        </div>
                                    </div>
                                    <div class="row mgt-10">
                                        <div class="form-group">
                                            <div class="col-sm-1">
                                                {!! Html::decode(Form::label('label_language', trans('workspace.label_language'), ['class' => 'ir-label mgt-10'])) !!}
                                            </div>
                                            <div class="col-sm-3">
                                                {!! Form::select('language', $languages, $tmpWorkspace->language, [
                                                'class' => 'form-control select2 pull-left auto-submit', 
                                                'data-type' => 'general_workspace',
                                                'placeholder' => trans('workspace.label_language'), 
                                                'required' => 'required'
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-sm-6 pdl-25">
                                <h4 class="label-title">@lang('setting.general.personal_information')</h4>
    {{--                            {!! Form::model($auth, ['route' => [$guard.'.users.updateProfile'], 'method' => 'patch', 'files' => true, 'class' => 'general-update-profile form-reset-close']) !!}--}}
                                <div class="row">
                                    <!-- name Field -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::text('manager_name', $tmpWorkspace->last_name, [
                                            'class' => 'form-control auto-submit', 
                                            'data-type' => 'general_profile',
                                            'required' => 'required', 
                                            'placeholder' => trans('manager.name')
                                            ]) !!}
                                        </div>
                                    </div>
                                    <!-- btw_nr Field -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::text('surname', $tmpWorkspace->first_name, [
                                            'class' => 'form-control auto-submit', 
                                            'data-type' => 'general_profile',
                                            'required' => 'required', 
                                            'placeholder' => trans('manager.first_name')
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- name Field -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::text('email', $tmpWorkspace->email, [
                                            'class' => 'form-control auto-submit', 
                                            'data-type' => 'general_profile',
                                            'required' => 'required', 
                                            'placeholder' => trans('manager.email')
                                            ]) !!}
                                        </div>
                                    </div>
                                    <!-- btw_nr Field -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::text('gsm', $tmpWorkspace->gsm, [
                                            'class' => 'form-control auto-submit keyup-gsm', 
                                            'data-type' => 'general_profile',
                                            'required' => 'required', 
                                            'placeholder' => trans('manager.gsm')
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
    {{--                            {!! Form::close() !!}--}}
    {{--                            <div class="row">--}}
    {{--                                <div class="col-md-12">--}}
    {{--                                    <a href="#" class="ir-btn ir-btn-primary mgt-10 pull-left" data-toggle="modal"--}}
    {{--                                       data-target="#modal_change_password">--}}
    {{--                                        @lang('manager.change_password')</a>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
                            </div>
                        </div>
                    {{Form::close()}}
                    <hr />
                    <div class="row">
                        <div class="col-md-4 pdr-30">
                            {!! Form::model($tmpWorkspace, ['route' => [$guard.'.settings.updateWorkspace', $tmpWorkspace->id], 'method' => 'post', 'files' => true, 'class' => 'general-update-workspace','data-close_label' => trans('strings.close')]) !!}
                                {!! Form::hidden('name', $tmpWorkspace->name, ['class' => 'form-control auto-submit', 'data-type' => 'general_workspace','required' => 'required']) !!}
                                {!! Form::hidden('btw_nr', $tmpWorkspace->btw_nr, ['class' => 'form-control auto-submit','data-type' => 'general_workspace','required' => 'required' ]) !!}
                                {!! Form::hidden('address', $tmpWorkspace->address, ['class' => 'form-control location auto-submit', 'data-type' => 'general_workspace','required' => 'required', ]) !!}
                                {!! Form::hidden('email', $tmpWorkspace->email, ['class' => 'form-control location auto-submit', 'data-type' => 'general_workspace','required' => 'required', ]) !!}
                                {!! Form::hidden('gsm', $tmpWorkspace->gsm, ['class' => 'form-control location auto-submit', 'data-type' => 'general_workspace','required' => 'required', ]) !!}
                                <span class="hidden">
                                    {!! Form::select('types[]', $types, $tmpWorkspace->workspaceCategories->pluck('id')->toArray(), ['id' => 'types','class' => 'form-control select2-type auto-submit', 'data-type' => 'general_workspace','multiple' => true,'required' => 'required']) !!}
                                </span>
                                <span class="hidden">
                                    {!! Form::select('language', $languages, $tmpWorkspace->language, ['class' => 'form-control select2 pull-left auto-submit', 'data-type' => 'general_workspace','required' => 'required']) !!}
                                </span>
                                <div class="row wrap-upload-file">
                                    <div class="col-md-6">
                                        <div class="text-view">
                                            @php
                                            $activeWorkspaceGalleries = $tmpWorkspace->workspaceGalleries->filter(function($value, $key){
                                                    return ($value->active == 1);
                                                })->sortBy('order');
                                                $image = $activeWorkspaceGalleries->count() > 0 ? $activeWorkspaceGalleries->first()->full_path : url('assets/images/no-img.svg');
                                            @endphp
                                            <img class="show-image preview-{{\App\Models\Media::GALLERIES}}" src="{{$image}}" alt="{{$tmpWorkspace->name}}">
                                        </div>
                                        <input type="hidden" class="no-image" value="{{url('assets/images/no-img.svg')}}">
                                        <div class="upload-file mgt-10">
                                            <label class="full-width">
                                                <a class="ir-btn ir-btn-secondary full-width inline-block text-center" data-toggle="modal" data-target="#upload-galleries">
                                                    @lang('setting.general.photo_upload')
                                                </a>
                                            </label>
                                        </div>
                                        <div class="help-block">@lang('workspace.api_image_note') 1440x535</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-view">
                                            @php
                                                $image = !empty($tmpWorkspace->workspaceAvatar) ? $tmpWorkspace->workspaceAvatar->full_path : url('assets/images/no-img.svg');
                                            @endphp
                                            <img class="show-image" src="{{$image}}" alt="{{$tmpWorkspace->name}}">
                                            <input type="file" name="uploadAvatar" data-type="general_workspace" class="manager-upload-image auto-submit hidden" id="upload-logo" />
                                        </div>
                                        <div class="upload-file mgt-10">
                                            <label for="upload-logo" class="full-width">
                                                <a class="ir-btn ir-btn-secondary full-width inline-block text-center">
                                                    @lang('setting.general.logo_upload')
                                                </a>
                                            </label>
                                        </div>
                                        <div class="help-block">@lang('workspace.api_image_note') 210x210</div>
                                    </div>
                                </div>
                                <div class="row wrap-upload-file">
                                    <div class="col-md-6">
                                        <div class="text-view">
                                            @php
                                                $activeWorkspaceAPIGalleries = $tmpWorkspace->workspaceAPIGalleries->filter(function($value, $key){
                                                    return ($value->active == 1);
                                                })->sortBy('order');
                                                $image = $activeWorkspaceAPIGalleries->count() > 0 ? $activeWorkspaceAPIGalleries->first()->full_path : url('assets/images/no-img.svg');
                                            @endphp
                                            <img class="show-image preview-{{\App\Models\Media::API_GALLERIES}}" src="{{$image}}" alt="{{$tmpWorkspace->name}}">
                                        </div>
                                        <input type="hidden" class="no-image" value="{{url('assets/images/no-img.svg')}}">
                                        <div class="upload-file mgt-10">
                                            <label class="full-width">
                                                <a class="ir-btn ir-btn-secondary full-width inline-block text-center" data-toggle="modal" data-target="#upload-api-galleries">
                                                    @lang('setting.general.photo_upload')
                                                </a>
                                            </label>
                                        </div>
                                        <div class="help-block">@lang('workspace.api_image_note') 1080x1920</div>
                                    </div>
                                </div>
                            {{Form::close()}}
                        </div>
                        <div class="col-md-8 pdl-30">
                            <div class="row">
                                {!! Form::model($settingGeneral, [
                                    'route' => [$guard.'.settingGenerals.updateOrCreate', 
                                    $tmpWorkspace->id], 
                                    'method' => 'post', 
                                    'files' => true, 
                                    'class' => 'update-form-setting-generals',
                                ]) !!}
                                    <div class="col-md-5 ">
                                        <h4 class="label-title">@lang('setting.general.corporate_identity_colors')</h4>
                                        
                                        <div class="row form-group">
                                            <div class="col-sm-5 col-xs-12">
                                                {!! Html::decode(Form::label('primary', trans('setting.general.primary').":", ['class' => 'mgt-5'])) !!}
                                            </div>
                                            <div class="col-sm-1 col-xs-12">
                                                <div id="primary" class="general-color-picker">
                                                    <span class="color" style="background-color: {{!empty($settingGeneral->primary_color) ? $settingGeneral->primary_color : '#413E38'}}"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                {!! Form::text('primary_color', !empty($settingGeneral->primary_color) ? $settingGeneral->primary_color : '#413E38', [
                                                'class' => 'form-control auto-submit primary_color', 
                                                'data-type' => 'general_settings',
                                                'id' => 'primary-color',
                                                'placeholder' => '#413E38'
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-sm-5 col-xs-12">
                                                {!! Html::decode(Form::label('secondary', trans('setting.general.secondary').":", ['class' => 'mgt-5'])) !!}
                                            </div>
                                            <div class="col-sm-1 col-xs-12">
                                                <div id="secondary" class="general-color-picker">
                                                    <span class="color" style="background-color: {{!empty($settingGeneral->primary_color) ? $settingGeneral->second_color : '#B5B268'}}"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                {!! Form::text('second_color', !empty($settingGeneral->primary_color) ? $settingGeneral->second_color : '#B5B268', [
                                                'class' => 'form-control auto-submit second_color', 
                                                'data-type' => 'general_settings',
                                                'id' => 'secondary-color',
                                                'placeholder' => '#B5B268'
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <h4 class="label-title">@lang('setting.general.title_and_subtitle')</h4>
                                        <div class="row">
                                            <!-- name Field -->
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {!! Form::text('title', null, [
                                                    'class' => 'form-control auto-submit', 
                                                    'data-type' => 'general_settings',
                                                    'placeholder' => trans('setting.general.title')
                                                    ]) !!}
                                                </div>
                                                <div class="form-group">
                                                    {!! Form::text('subtitle', null, [
                                                    'class' => 'form-control auto-submit', 
                                                    'data-type' => 'general_settings',
                                                    'placeholder' => trans('setting.general.subtitle')
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                {{Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $keyLang = json_encode(array(
            'active' => Lang::get('common.active'),
            'inactive' => Lang::get('common.inactive')
        ));
    @endphp

    <input type="hidden" id="lang-keys" value="{{ $keyLang }}"/>
    @include($guard . '.settings.partials.general.upload_galleries')
    @include($guard . '.settings.partials.general.upload_api_galleries')
@endsection