<!-- Modal -->
<div id="detail-{{$workspace->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close reset-form" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-detail">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="modal-title ir-h4 inline-block mgb-i-0">
                                {{$workspace->name}}

                                @if(Helper::checkUserPermission($guard.'.restaurants.edit'))
                                    <a href="#" class="show-edit-form">
                                        <i class="ir-edit"></i>
                                    </a>
                                @endif
                            </h4>
                            <div class="subdomain-view">
                                {!! $workspace->slug.'.'.$domain !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group text-right">
                                <label class="ir-label mgr-30">@lang('workspace.label_status')</label>
                                <span class="switch-{{ $workspace->id }}">
                                    <input type="checkbox" id="switch-detail-{{ $workspace->id }}"
                                        value="{{$workspace->active == true ? \App\Models\Workspace::INACTIVE : \App\Models\Workspace::ACTIVE}}"
                                        class="switch-input" {{$workspace->active == \App\Models\Workspace::ACTIVE ? 'checked' : null}} />
                                    <label
                                        data-route="{{route($guard.'.restaurants.updateStatus', [$workspace->id])}}"
                                        for="switch-detail-{{ $workspace->id }}" class="switch update-status pull-right mgr-35"></label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- account manager Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_account_manager', trans('workspace.label_account_manager'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{!empty($workspace->workspaceAccount) ? $workspace->workspaceAccount->name : null}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- gsm Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_gsm', trans('workspace.label_gsm'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->gsm}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- manager Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_manager', trans('workspace.label_manager'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->manager_name ." ". $workspace->surname}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- created_at Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_created_at', trans('workspace.label_created_at'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {!! Helper::getDateFromFormat($workspace->created_at, null, $guard) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- address Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_address', trans('workspace.label_address'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->address}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- btw_nr Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_btw_nr', trans('workspace.label_btw_nr'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->btw_nr}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- type Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_type', trans('workspace.label_type'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{
                                                    !empty($workspace->workspaceCategories) && $workspace->workspaceCategories->count() > 0 ?
                                                    implode(', ', $workspace->workspaceCategories->pluck('name')->toArray()) : null
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- language Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_language', trans('workspace.label_language'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{!empty($workspace->language) && !empty(Helper::getActiveLanguages()[$workspace->language]) ? Helper::getActiveLanguages()[$workspace->language] : null}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- email Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_email', trans('workspace.label_email'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->email}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- VAT system Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('label_vat_system', trans('workspace.label_vat_system'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{!empty($workspace->country) ? $workspace->country->name : null}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- email Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('email_to_'.$workspace->id, trans('workspace.email_to'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->email_to}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- email Field -->
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('template_app_ios', trans('workspace.template_app_ios'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->template_app_ios}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('template_app_android', trans('workspace.template_app_android'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->template_app_android}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('address_line_1', trans('workspace.address_line_1'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->address_line_1}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('address_line_2', trans('workspace.address_line_2'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->address_line_2}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('firebase_project', trans('workspace.firebase_project'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                @php
                                                    $options = Helper::getOptionsForFirebaseProjects()
                                                @endphp
                                                {{isset($options[$workspace->firebase_project])?$options[$workspace->firebase_project]:''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('facebook_enabled', trans('workspace.facebook_enabled'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->facebook_enabled ? trans('common.yes') : trans('common.no') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($workspace->facebook_id) || !empty($workspace->facebook_key))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('facebook_id', trans('workspace.facebook_id'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="text-view">
                                                    {{$workspace->facebook_id}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('facebook_key', trans('workspace.facebook_key'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="text-view">
                                                    {{$workspace->facebook_key}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('google_enabled', trans('workspace.google_enabled'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->google_enabled ? trans('common.yes') : trans('common.no') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($workspace->google_id) || !empty($workspace->google_key))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('google_id', trans('workspace.google_id'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="text-view">
                                                    {{$workspace->google_id}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('google_key', trans('workspace.google_key'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="text-view">
                                                    {{$workspace->google_key}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('apple_enabled', trans('workspace.apple_enabled'), ['class' => 'ir-label'])) !!}
                                        </div>
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="text-view">
                                                {{$workspace->apple_enabled ? trans('common.yes') : trans('common.no') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($workspace->apple_id) || !empty($workspace->apple_key))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('apple_id', trans('workspace.apple_id'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="text-view">
                                                    {{$workspace->apple_id}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('apple_key', trans('workspace.apple_key'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="text-view">
                                                    {{$workspace->apple_key}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('token', trans('workspace.token'), ['class' => 'ir-label'])) !!}
                                            <div class="text-view">
                                                {{$workspace->token}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $nextUrl = config('app.next_protocol').'://'.$workspace->slug.'.'.config('app.next_domain');
                            @endphp

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('order_access_key', trans('workspace.table_ordering') . ' URL', ['class' => 'ir-label'])) !!}
                                            <div class="text-view">
                                                <a href="{{$nextUrl.'/nl/table-ordering/access/'.$workspace->order_access_key}}" target="_blank">
                                                    {{$nextUrl.'/nl/table-ordering/access/'.$workspace->order_access_key}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-sm-12 col-xs-12">
                                            {!! Html::decode(Form::label('order_access_key', trans('workspace.self_ordering') . ' URL', ['class' => 'ir-label'])) !!}
                                            <div class="text-view">
                                                <a href="{{$nextUrl.'/nl/self-ordering/access/'.$workspace->order_access_key}}" target="_blank">
                                                    {{$nextUrl.'/nl/self-ordering/access/'.$workspace->order_access_key}}
                                                </a>
                                            </div>
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
                                        @php
                                            $image = !empty($workspace->workspaceAvatar) ? $workspace->workspaceAvatar->full_path : url('assets/images/no-image.svg');
                                        @endphp
                                        <img class="image-detail" src="{{$image}}" alt="{{$workspace->name}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Helper::checkUserPermission($guard.'.restaurants.edit'))
                            <div class="col-md-12 mgt-20 text-right">
                                <a href="javascript:;" class="ir-btn ir-btn-primary show-edit-form">
                                    @lang('workspace.change_details')
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-edit">
                    {!! Form::model($workspaces, [
                        'route' => [$guard.'.restaurants.update',
                        $workspace->id],
                        'method' => 'patch',
                        'files' => true,
                        'class' => 'update-form',
                        'data-close_label' => trans('strings.close')
                    ]) !!}
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
                                                {!! Form::text('name', $workspace->name, ['class' => 'form-control', 'required' => 'required']) !!}
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
                                                {!! Form::text('slug', $workspace->slug, ['class' => 'form-control display-flex fill-slug', 'required' => 'required']) !!}
                                                <div class="display-flex subdomain-lbl">.{!! $domain !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group text-right">
                                    <label class="ir-label mgr-30">@lang('workspace.label_status')</label>
                                    <span class="switch-{{ $workspace->id }}">
                                        <input type="checkbox" id="switch-edit-{{ $workspace->id }}"
                                            value="{{$workspace->active == true ? \App\Models\Workspace::INACTIVE : \App\Models\Workspace::ACTIVE}}"
                                            class="switch-input" {{$workspace->active == \App\Models\Workspace::ACTIVE ? 'checked' : null}} />
                                        <label
                                            data-route="{{route($guard.'.restaurants.updateStatus', [$workspace->id])}}"
                                            for="switch-edit-{{ $workspace->id }}" class="switch update-status pull-right mgr-35"></label>
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
                                                {!! Html::decode(Form::label('label_account_manager', trans('workspace.label_account_manager'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('account_manager_id', $accountManagers, $workspace->account_manager_id, ['class' => 'form-control select2', 'placeholder' => trans('workspace.select_account_manager'), 'required' => 'required']) !!}
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
                                                {!! Form::text('gsm', $workspace->gsm, ['class' => 'form-control keyup-gsm', 'required' => 'required', 'placeholder' => trans('workspace.placeholder_gsm')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- manager Field -->
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_name', trans('workspace.label_name'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('manager_name', $workspace->manager_name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.label_name')]) !!}
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
                                                {!! Form::text('surname', $workspace->surname, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.label_surname')]) !!}
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
                                                    {!! Form::text('address', $workspace->address, [
                                                    'class' => 'form-control location',
                                                    'required' => 'required',
                                                    'placeholder' => trans('workspace.placeholder_address')
                                                    ]) !!}

{{--                                                    <img class="maps-marker" src="{!! asset('assets/images/map-marker-line.svg') !!}"/>--}}
                                                    <div id="modal-box-map-{{$workspace->id}}" class="modal fade signin-frm" tabindex="-1" role="dialog" aria-modal="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-medium" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-body">
                                                                    <div id="modal-box-map-{{$workspace->id}}-view" style="height: 500px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {!! Form::hidden('address_lat', $workspace->address_lat, ['class' => 'latitude','required']) !!}
                                                    {!! Form::hidden('address_long', $workspace->address_long, ['class' => 'longitude','required']) !!}
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
                                                {!! Form::text('btw_nr', $workspace->btw_nr, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.placeholder_btw_nr')]) !!}
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
                                                {!! Form::select('types[]', $types, $workspace->workspaceCategories->pluck('id')->toArray(), [
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
                                                {!! Form::select('language', $languages, $workspace->language, ['class' => 'form-control select2', 'placeholder' => trans('workspace.label_language'), 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- email Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_email', trans('workspace.label_email'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('email', $workspace->email, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.placeholder_email')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- VAT system Field -->
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_vat_system', trans('workspace.label_vat_system'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('country_id', $countries, $workspace->country_id, ['class' => 'form-control select2', 'placeholder' => trans('workspace.placeholder_country_id'), 'required' => 'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- email_to Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('email_to_'.$workspace->id, trans('workspace.email_to'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('email_to', $workspace->email_to, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('workspace.email_to')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- email Field -->
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('template_app_ios', trans('workspace.template_app_ios'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('template_app_ios', $workspace->template_app_ios, ['class' => 'form-control', 'placeholder' => trans('workspace.template_app_ios')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('template_app_android', trans('workspace.template_app_android'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('template_app_android', $workspace->template_app_android, ['class' => 'form-control', 'placeholder' => trans('workspace.template_app_android')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('address_line_1', trans('workspace.address_line_1'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('address_line_1', $workspace->address_line_1, ['class' => 'form-control', 'placeholder' => trans('workspace.address_line_1')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('address_line_2', trans('workspace.address_line_2'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('address_line_2', $workspace->address_line_2, ['class' => 'form-control', 'placeholder' => trans('workspace.address_line_2')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('firebase_project', trans('workspace.firebase_project'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('firebase_project', Helper::getOptionsForFirebaseProjects(), $workspace->firebase_project, ['class' => 'form-control select2']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Active languages -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('active_languages', trans('workspace.active_languages'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            @foreach(collect(config('languages'))->sortBy(function ($value, $key) {return $key !== 'nl';})->toArray() as $locale => $language)
                                            <div class="col-sm-3">
                                                <div class="display-flex">
                                                    <div>
                                                        <input type="checkbox" id="active_languages_{{ $workspace->id . '_' . $locale }}" name="active_languages[]" value="{{ $locale }}" class="switch-input" {{ $locale == 'nl' ? '' : '' }} {{ $locale == 'nl' || in_array($locale, $workspace->active_languages) ? 'checked' : '' }} />
                                                        <label for="active_languages_{{ $workspace->id . '_' . $locale }}" class="switch"></label>
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
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('facebook_enabled', trans('workspace.facebook_enabled'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <span>
                                                    <input type="checkbox" id="switch-facebook-{{ $workspace->id }}" name="facebook_enabled" value="1" data-toggle-div="#switch-facebook-{{ $workspace->id }}-advanced" class="switch-toggle-div switch-input" {{ !empty($workspace->facebook_enabled) ? 'checked' : null}} />
                                                    <label for="switch-facebook-{{ $workspace->id }}" class="switch mgr-35"></label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="switch-facebook-{{ $workspace->id }}-advanced" style="{{ empty($workspace->facebook_enabled) ? 'display:none;' : ''  }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row form-group">
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Html::decode(Form::label('facebook_id', trans('workspace.facebook_id') . ' ' . '(' . trans('workspace.optional') . ')', ['class' => 'ir-label'])) !!}
                                                </div>
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Form::text('facebook_id', $workspace->facebook_id, ['class' => 'form-control', 'placeholder' => trans('workspace.facebook_id')]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row form-group">
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Html::decode(Form::label('facebook_key', trans('workspace.facebook_key') . ' ' . '(' . trans('workspace.optional') . ')', ['class' => 'ir-label'])) !!}
                                                </div>
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Form::text('facebook_key', $workspace->facebook_key, ['class' => 'form-control', 'placeholder' => trans('workspace.facebook_key')]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('google_enabled', trans('workspace.google_enabled'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <span>
                                                    <input type="checkbox" id="switch-google-{{ $workspace->id }}" name="google_enabled" value="1" data-toggle-div="#switch-google-{{ $workspace->id }}-advanced" class="switch-toggle-div switch-input" {{ !empty($workspace->google_enabled) ? 'checked' : null}} />
                                                    <label for="switch-google-{{ $workspace->id }}" class="switch mgr-35"></label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="switch-google-{{ $workspace->id }}-advanced" style="{{ empty($workspace->google_enabled) ? 'display:none;' : ''  }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row form-group">
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Html::decode(Form::label('google_id', trans('workspace.google_id') . ' ' . '(' . trans('workspace.optional') . ')', ['class' => 'ir-label'])) !!}
                                                </div>
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Form::text('google_id', $workspace->google_id, ['class' => 'form-control', 'placeholder' => trans('workspace.google_id')]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row form-group">
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Html::decode(Form::label('google_key', trans('workspace.google_key') . ' ' . '(' . trans('workspace.optional') . ')', ['class' => 'ir-label'])) !!}
                                                </div>
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Form::text('google_key', $workspace->google_key, ['class' => 'form-control', 'placeholder' => trans('workspace.google_key')]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('apple_enabled', trans('workspace.apple_enabled'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <span>
                                                    <input type="checkbox" id="switch-apple-{{ $workspace->id }}" name="apple_enabled" value="1" data-toggle-div="#switch-apple-{{ $workspace->id }}-advanced" class="switch-toggle-div switch-input" {{ !empty($workspace->apple_enabled) ? 'checked' : null}} />
                                                    <label for="switch-apple-{{ $workspace->id }}" class="switch mgr-35"></label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="switch-apple-{{ $workspace->id }}-advanced" style="{{ empty($workspace->apple_enabled) ? 'display:none;' : ''  }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row form-group">
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Html::decode(Form::label('apple_id', trans('workspace.apple_id') . ' ' . '(' . trans('workspace.optional') . ')', ['class' => 'ir-label'])) !!}
                                                </div>
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Form::text('apple_id', $workspace->apple_id, ['class' => 'form-control', 'placeholder' => trans('workspace.apple_id')]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row form-group">
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Html::decode(Form::label('apple_key', trans('workspace.apple_key') . ' ' . '(' . trans('workspace.optional') . ')', ['class' => 'ir-label'])) !!}
                                                </div>
                                                <div class="col-sm-12 col-xs-12">
                                                    {!! Form::text('apple_key', $workspace->apple_key, ['class' => 'form-control', 'placeholder' => trans('workspace.apple_key')]) !!}
                                                </div>
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
                                            @php
                                                $image = !empty($workspace->workspaceAvatar) ? $workspace->workspaceAvatar->full_path : url('assets/images/no-image.svg');
                                            @endphp
                                            <img class="show-image" src="{{$image}}" alt="{{$workspace->name}}">
                                            <input type="file" name="uploadAvatar" class="manager-upload-image hidden" id="upload-avatar-{{$workspace->id}}" />
                                        </div>
                                        <div class="help-block">@lang('workspace.image_note') 210x210</div>
                                        <div class="upload-file mgt-10">
                                            <label for="upload-avatar-{{$workspace->id}}">
                                                <img src="{!! url('assets/images/attack.svg') !!}" />
                                                <span>@lang('workspace.attack_file')</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mgt-20 text-right">
                                {!! Form::submit(trans('strings.save'), ['class' => 'ir-btn ir-btn-primary save-form submit1']) !!}
                                <a href="javascript:;" class="ir-btn ir-btn-secondary mgl-20 show-detail-form reset-form1">
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