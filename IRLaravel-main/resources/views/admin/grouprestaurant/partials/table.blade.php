<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('active')}}">
                    @lang('workspace.label_status') {{Helper::getIconSort('active')}}
                </a>
            </div>
            <div class="col-item col-sm-4 col-xs-12">
                <a href="{{Helper::getFullSortUrl('name')}}">
                    @lang('grouprestaurant.name') {{Helper::getIconSort('name')}}
                </a>
            </div>
            <div class="col-item col-sm-4 col-xs-12">
                <a href="{{Helper::getFullSortUrl('restaurants')}}">
                    @lang('menu.restaurants') {{Helper::getIconSort('restaurants')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body restaurant">
        @foreach($model as $groupRestaurant)
            <div id="tr-{{ $groupRestaurant->id }}" class="row">
                <div class="col-item col-sm-2 col-xs-12">
                    <span class="switch-{{ $groupRestaurant->id }}">
                        <input type="checkbox" id="switch-{{ $groupRestaurant->id }}"
                               value="{{$groupRestaurant->active == true ? \App\Models\Workspace::INACTIVE : \App\Models\Workspace::ACTIVE}}"
                               class="switch-input" {{$groupRestaurant->active == \App\Models\Workspace::ACTIVE ? 'checked' : null}} />
                        <label
                                data-route="{{route($guard.'.grouprestaurant.updateStatus', [$groupRestaurant->id])}}"
                                for="switch-{{ $groupRestaurant->id }}" class="switch update-status"></label>
                    </span>
                </div>
                <div class="col-item col-sm-4 col-xs-12">
                    {!! $groupRestaurant->name !!}
                </div>
                <div class="col-item col-sm-4 col-xs-12">
                    {{
                        !empty($groupRestaurant->groupRestaurantWorkspaces) && $groupRestaurant->groupRestaurantWorkspaces->count() > 0 ?
                        implode(', ', $groupRestaurant->groupRestaurantWorkspaces->pluck('name')->toArray()) : null
                    }}
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal_edit_group_restaurant_{{$groupRestaurant->id}}">
                                @lang('strings.edit')
                            </a>
                        </li>
                        @if(Helper::checkUserPermission($guard.'.grouprestaurant@destroy'))
                            <li>
                                <a href="javascript:;" class="show-confirm"
                                   data-route="{{route($guard.'.grouprestaurant.destroy', [$groupRestaurant->id])}}"
                                   data-title="{{trans('workspace.are_you_sure_delete', ['name' => $groupRestaurant->name])}}"
                                   data-id="{{$groupRestaurant->id}}"
                                   data-deleted_success="@lang('workspace.deleted_successfully')"
                                   data-close_label="@lang('workspace.close')"
                                   data-yes_label="@lang('common.yes_delete')"
                                   data-no_label="@lang('common.no_cancel')">
                                    @lang('workspace.remove')
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                @include($guard.'.grouprestaurant.partials.modal_edit')
            </div>
        @endforeach
    </div>
</div>

@if(!empty($model))
    {{ $model->appends(request()->all())->links() }}
@endif