@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'id' => $idForm]) !!}
        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <div class="modal-body">
            <div class="clear"></div>
            <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>
            @php
                $providers = \App\Models\SettingConnector::getProviders(null, false, !empty($settingConnector->id) ? $settingConnector->id : null, $workspaceId);
            @endphp
            <div id="data-show">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {{ Form::select(
                                'provider',
                                $providers,
                                old('provider') ?: (!empty($settingConnector->provider) ? $settingConnector->provider : null),
                                ['class' => 'form-control']
                            ) }}
                        </div>
                    </div>
                </div>

                @php
                    $showFieldFlag = true;
                    $readOnlyField = false;

                    if(empty($settingConnector->provider) && (count($providers) == 1 && isset($providers[\App\Models\SettingConnector::PROVIDER_CUSTOM]))) {
                        $showFieldFlag = false;
                    }

                    if(!empty($settingConnector->provider) && $settingConnector->provider == \App\Models\SettingConnector::PROVIDER_CUSTOM) {
                        $readOnlyField = true;
                    }
                @endphp
                <fieldset class="connector-token"
                    style="display: {{ !empty($showFieldFlag) ? 'block' : 'none' }}">
                    <hr />

                    <strong>@lang('setting.connectors.manager.global')</strong>

                    <div class="row">
                        <div class="col-md-{{ !empty($readOnlyField) ? '3' : '4' }}">
                            <div class="form-group">
                                <div class="form-group">
                                    <input class="form-control" name="endpoint" type="text" placeholder="@lang('setting.connectors.manager.endpoint')"
                                           {{ !empty($readOnlyField) ? 'readonly' : '' }}
                                           value="{{ old('endpoint') ?: !empty($settingConnector->endpoint) ? $settingConnector->endpoint : null }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-{{ !empty($readOnlyField) ? '3' : '4' }}">
                            <div class="form-group">
                                <div class="form-group">
                                    <input class="form-control" name="key" type="text" placeholder="@lang('setting.connectors.manager.key')"
                                           {{ !empty($readOnlyField) ? 'readonly' : '' }}
                                           value="{{ old('key') ?: !empty($settingConnector->key) ? $settingConnector->key : null }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-{{ !empty($readOnlyField) ? '3' : '4' }}">
                            <div class="form-group">
                                <div class="form-group">
                                    <input class="form-control" name="token" type="text" placeholder="@lang('setting.connectors.manager.token')"
                                           {{ !empty($readOnlyField) ? 'readonly' : '' }}
                                           value="{{ old('token') ?: !empty($settingConnector->token) ? $settingConnector->token : null }}"/>
                                </div>
                            </div>
                        </div>
                        @if(!empty($readOnlyField))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-group">
                                        <input class="form-control" name="refresh_token" type="text" placeholder="@lang('setting.connectors.manager.refresh_token')"
                                               {{ !empty($readOnlyField) ? 'readonly' : '' }}
                                               value="{{ old('refresh_token') ?: !empty($settingConnector->refresh_token) ? $settingConnector->refresh_token : null }}"/>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr />

                    @if(empty($settingConnector->provider) || (!empty($settingConnector->provider) && $settingConnector->provider != \App\Models\SettingConnector::PROVIDER_CUSTOM))
                        @php

                            $types = [
                                'takeout',
                                'delivery',
                                'inhouse'
                            ];

                            @endphp
                        @foreach($types as $type)
                            @php

                                $variableNameEndpoint = $type . '_endpoint';
                                $variableNameKey = $type . '_key';
                                $variableNameToken = $type . '_token';

                            @endphp
                            <strong>@lang('setting.connectors.manager.' . $type)</strong>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <input class="form-control" name="{{ $variableNameEndpoint }}" type="text" placeholder="@lang('setting.connectors.manager.endpoint')"
                                                   value="{{ old($variableNameEndpoint) ?: !empty($settingConnector->$variableNameEndpoint) ? $settingConnector->$variableNameEndpoint : null }}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <input class="form-control" name="{{ $variableNameKey }}" type="text" placeholder="@lang('setting.connectors.manager.key')"
                                                   value="{{ old($variableNameKey) ?: !empty($settingConnector->$variableNameKey) ? $settingConnector->$variableNameKey : null }}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <input class="form-control" name="{{ $variableNameToken }}" type="text" placeholder="@lang('setting.connectors.manager.token')"
                                                   value="{{ old($variableNameToken) ?: !empty($settingConnector->$variableNameToken) ? $settingConnector->$variableNameToken : null }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <p><small>@lang('setting.connectors.manager.overwrite_note')</small></p>
                    @endif
                </fieldset>
            </div>

            <button type="submit" class="ir-btn ir-btn-primary pull-right opslaan submit1" style="width:160px" aria-label="">
                @lang('category.btn_opslaan')
            </button>
        </div>

        <div class="clearfix"></div>
    {!! Form::close() !!}
@endif

@push('scripts')
    <script>
        $(document).ready(function () {
            var loadProvider = function(provider) {
                var connectorToken = $('.connector-token');

                if (provider === '{{ \App\Models\SettingConnector::PROVIDER_CUSTOM }}') {
                    connectorToken.hide();
                } else {
                    connectorToken.show();
                }
            }

            loadProvider($('select[name="provider"]').val());

            $(document).on('change', 'select[name="provider"]', function () {
                var provider = $(this).val();
                loadProvider(provider);
            });
        });
    </script>
@endpush

