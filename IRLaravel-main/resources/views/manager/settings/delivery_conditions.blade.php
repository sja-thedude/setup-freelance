@extends('layouts.manager')

@section('content')
    <div class="row general">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting.more.delivery_conditions')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="javascript:;" class="ir-btn ir-btn-secondary ir-add-more">
                                <i class="ir-plus mgl-20"></i> @lang('setting.more.new')
                            </a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    {!! Form::model($settingDeliveryConditions, [
                        'route' => [$guard.'.settingDeliveryConditions.updateOrCreate',
                        $tmpWorkspace->id],
                        'method' => 'post',
                        'files' => true,
                        'class' => 'update-form-delivery-conditions',
                    ]) !!}
                    <fieldset class="hidden-error">
                        <div class="list-responsive">
                            <div class="list-header list-header-non-bg">
                                <p>Correct voorbeeld: 0-2, 2-5, 5-7</p>
                                <div class="row">
                                    <div class="col-item col-sm-3 col-xs-12"></div>
                                    <div class="col-item col-sm-2 col-xs-12 text-center">
                                    </div>
                                    <div class="col-item col-sm-2 col-xs-12 text-center">
                                    </div>
                                    <div class="col-item col-sm-2 col-xs-12 text-center">
                                    </div>
                                </div>
                            </div>
                            <fieldset class="list-body">
                                @include($guard.'.settings.partials.delivery-conditions.fields')
                            </fieldset>
                            <div class="list-footer">
                                @include($guard.'.settings.partials.delivery-conditions.field')
                            </div>
                        </div>
                    </fieldset>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
