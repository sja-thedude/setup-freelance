<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-1 col-xs-12">
            </div>
            <div class="col-item col-sm-3 col-xs-12">
            </div>
            <div class="col-item col-sm-2 col-xs-12">
            </div>
            <div class="col-item col-sm-2 col-xs-12">
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <span>@lang('category.beschikbaar')</span>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
            </div>
        </div>
    </div>
    <div class="list-body ui-sortable">
        @foreach($categories as $k => $item)
            <div id="tr-{{ $item->id }}" class="row ui-sortable-handle"
                 data-id="{{ $item->id }}"
                 data-route="{{ route($guard . '.categories.updateOrder') }}"
            >
                <a href="javascript:;" class="btn-order">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 14C10.1046 14 11 13.1046 11 12C11 10.8954 10.1046 10 9 10C7.89543 10 7 10.8954 7 12C7 13.1046 7.89543 14 9 14Z" fill="#808080"/>
                        <path d="M15 14C16.1046 14 17 13.1046 17 12C17 10.8954 16.1046 10 15 10C13.8954 10 13 10.8954 13 12C13 13.1046 13.8954 14 15 14Z" fill="#808080"/>
                        <path d="M9 7C10.1046 7 11 6.10457 11 5C11 3.89543 10.1046 3 9 3C7.89543 3 7 3.89543 7 5C7 6.10457 7.89543 7 9 7Z" fill="#808080"/>
                        <path d="M15 7C16.1046 7 17 6.10457 17 5C17 3.89543 16.1046 3 15 3C13.8954 3 13 3.89543 13 5C13 6.10457 13.8954 7 15 7Z" fill="#808080"/>
                        <path d="M9 21C10.1046 21 11 20.1046 11 19C11 17.8954 10.1046 17 9 17C7.89543 17 7 17.8954 7 19C7 20.1046 7.89543 21 9 21Z" fill="#808080"/>
                        <path d="M15 21C16.1046 21 17 20.1046 17 19C17 17.8954 16.1046 17 15 17C13.8954 17 13 17.8954 13 19C13 20.1046 13.8954 21 15 21Z" fill="#808080"/>
                    </svg>
                </a>

                <div class="col-md-12 data-item">
                    <div class="col-item col-sm-4 col-xs-12 cut-text">
                        {!! $item->name !!}
                    </div>
                    <div class="col-item col-sm-2 col-xs-12">
                        {!! $item->products_count !!} @lang('category.txt_items')
                    </div>
                    <div class="col-item col-sm-2 col-xs-12">
                        {!! $item->categoryOptions->where('is_checked', TRUE)->count() !!} @lang('category.txt_option')
                    </div>
                    <div class="col-item col-sm-2 col-xs-12">
                        <input type="checkbox" id="switch-{{ $item->id }}"
                               value="{{ !$item->active }}"
                               class="switch-input" {{$item->active ? "checked" : NULL}} />
                        <label data-route="{{ route($guard.'.categories.updateStatus', [$item->id]) }}"
                               data-id="{{ $item->id }}"
                               for="switch-{{ $item->id }}" class="switch update-status"></label>
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-right">
                        <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                            @lang('workspace.actions')
                            <i class=" fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right ir-dropdown-actions">
                            <li>
                                <a href="javascript:;" class="showItem"
                                   data-route="{{ route($guard.'.categories.edit', [$item->id]) }}"
                                   data-id="{{ $item->id }}">@lang('category.edit')</a>
                            </li>
                            <li>
                                <a href="javascript:;" class="show-confirm"
                                   data-route="{{ route($guard.'.categories.destroy', [$item->id]) }}"
                                   data-title="{{trans('workspace.are_you_sure_delete', ['name' => $item->name])}}"
                                   data-id="{{ $item->id }}"
                                   data-deleted_success="@lang('category.deleted_successfully')"
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