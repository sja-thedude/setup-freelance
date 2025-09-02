@extends('layouts.manager')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('workspace_app.title')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
                            <h4 class="workspace_app_description">@lang('workspace_app.settings.description')</h4>
                        </div>

                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <button class="ir-btn ir-btn-primary btn-create-item pull-right"
                                data-url="{{ route('manager.apps.settings.create') }}"
                                data-url-store="{{ route('manager.apps.settings.store') }}">
                                <i class="ir-plus"></i> @lang('workspace_app.buttons.new')
                            </button>
                        </div>
                    </div>

                    <div class="row workspace_app_theme">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 workspace_app_meta_container"
                            data-url-orders="{{ route('manager.apps.settings.orders') }}">

                            @foreach($workspaceApp->workspaceAppMeta->sortBy('order') as $appMeta)

                                @include('manager.workspace_apps.partials.setting_item', ['appMeta' => $appMeta])

                            @endforeach

                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    {!! Html::style('css/app/settings.css') !!}
@endpush

@push('scripts')
    {!! Html::script('js/app/settings.js') !!}
@endpush