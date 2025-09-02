<div class="list-responsive">
    <div class="list-header list-header-manager">
        <div class="row" style="display:flex">
            <div class="width-70"></div>
            <div class="col-sm-12">
                <div class="col-item col-sm-3 col-xs-12">
                    <span>@lang('setting.connectors.manager.provider')</span>
                </div>
                <div class="col-item col-sm-6 col-xs-12 text-center mgl--15">
                    <span>@lang('setting.connectors.manager.endpoint')</span>
                </div>
                <div class="col-item col-sm-3 col-xs-12">
                </div>
            </div>
        </div>
    </div>
    <div class="list-body ui-sortable list-body-manager">
        @foreach($connectors as $k => $item)
            <div id="tr-{{ $item->id }}" class="row"
                 data-id="{{ $item->id }}"
            >
                <div class="col-md-12 data-item">
                    <div class="col-item col-sm-3 col-xs-12 cut-text">
                        <span class="font-size-16">
                            {{ $item->getProviders($item->provider) }}
                        </span>
                    </div>
                    <div class="col-item col-sm-6 col-xs-12 text-center">
                        {{ $item->endpoint }}
                    </div>
                    <div class="col-item col-sm-3 col-xs-12 text-right">
                        <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                            @lang('workspace.actions')
                            <i class=" fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right ir-dropdown-actions">
                            <li>
                                <a href="javascript:;" class="showItem"
                                   data-route="{{ route($guard.'.settings.connector.edit', ['id' => $item->id]) }}"
                                   data-id="{{ $item->id }}">@lang('category.edit')</a>
                            </li>
                            {{-- Currently test method only supports "PROVIDER_HENDRICKX_KASSAS" --}}
                            @if($item->provider == \App\Models\SettingConnector::PROVIDER_HENDRICKX_KASSAS)
                                <li>
                                    <a href="{{ route($guard.'.settings.connector.test', ['id' => $item->id]) }}"
                                       data-id="{{ $item->id }}">@lang('setting.connectors.manager.test')</a>
                                </li>
                            @endif
                            <li>
                                <a href="javascript:;" class="show-confirm"
                                   data-route="{{ route($guard.'.settings.connector.destroy', ['id' => $item->id]) }}"
                                   data-title="{{trans('workspace.are_you_sure_delete', ['name' => $item->provider . ' ('.$item->endpoint.')'])}}"
                                   data-id="{{ $item->id }}"
                                   data-deleted_success="@lang('setting.connectors.manager.deleted_successfully')"
                                   data-close_label="@lang('workspace.close')"
                                   data-yes_label="@lang('common.yes_delete')"
                                   data-no_label="@lang('common.no_cancel')">@lang('category.delete')</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>