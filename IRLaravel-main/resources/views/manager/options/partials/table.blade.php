<div class="list-responsive">
    <div class="list-header list-header-manager">
        <div class="row" style="display:flex">
            <div class="width-70"></div>
            <div class="col-sm-12">
                <div class="col-item col-sm-4 col-xs-12">
                    <span>@lang('option.naam')</span>
                </div>
                <div class="col-item col-sm-3 col-xs-12 text-center mgl--15">
                    <span>@lang('option.bereik')</span>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    <span>@lang('option.verplicht')</span>
                </div>
                <div class="col-item col-sm-3 col-xs-12">
                </div>
            </div>
        </div>
    </div>
    <div class="list-body ui-sortable list-body-manager">
        @if(!empty($tmpWorkspace->id))
            @php($isShowConnectors = $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first())
        @endif

        @foreach($options as $k => $item)
            <div id="tr-{{ $item->id }}" class="row ui-sortable-handle"
                 data-id="{{ $item->id }}"
                 data-route="{{ route($guard . '.options.updateOrder') }}"
            >
                <a href="javascript:;" class="btn-order mgl-15">
                    <img src="{!! url('assets/images/icons/drag-drop.svg') !!}" />
                </a>

                <div class="col-md-12 data-item">
                    <div class="col-item col-sm-4 col-xs-12 cut-text">
                        <span class="font-size-16">
                            {!! $item->name !!}
                        </span>
                    </div>
                    <div class="col-item col-sm-3 col-xs-12 text-center">
                        {!! $item->min !!}-{!! $item->max !!}
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        <span class="font-size-16">
                            {!! $item->type_name !!}
                        </span>
                    </div>
                    <div class="col-item col-sm-3 col-xs-12 text-right">
                        <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                            @lang('workspace.actions')
                            <i class=" fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right ir-dropdown-actions">
                            <li>
                                <a href="javascript:;" class="showItem"
                                   data-route="{{ route($guard.'.options.edit', [$item->id]) }}"
                                   data-id="{{ $item->id }}">@lang('category.edit')</a>
                            </li>
                            @if(!empty($isShowConnectors) && $isShowConnectors->active)
                                <li>
                                    <a href="javascript:;" class="showItem"
                                       data-route="{{ route($guard.'.options.itemsReferences', [$item->id]) }}"
                                       data-id="{{ $item->id }}">@lang('category.connectors')</a>
                                </li>
                            @endif
                            <li>
                                <a href="javascript:;" class="show-confirm"
                                   data-route="{{ route($guard.'.options.destroy', [$item->id]) }}"
                                   data-title="{{trans('workspace.are_you_sure_delete', ['name' => $item->name])}}"
                                   data-id="{{ $item->id }}"
                                   data-deleted_success="@lang('option.deleted_successfully')"
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