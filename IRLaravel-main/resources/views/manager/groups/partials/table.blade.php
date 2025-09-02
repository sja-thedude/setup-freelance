<div class="list-responsive black">
    <div class="list-header list-header-manager">
        <div class="col-item col-sm-1 col-xs-12">
            <a href="{{Helper::getFullSortUrl('active')}}">
                @lang('group.lb_status') {{Helper::getIconSort('active')}}
            </a>
        </div>
        <div class="col-item col-sm-1 col-xs-12">
            <a href="{{Helper::getFullSortUrl('name')}}">
                @lang('group.lb_name') {{Helper::getIconSort('name')}}
            </a>
        </div>
        <div class="col-item col-sm-2 col-xs-12 text-center">
            <a href="{{Helper::getFullSortUrl('type')}}">
                @lang('group.lb_type') {{Helper::getIconSort('type')}}
            </a>
        </div>
        <div class="col-item col-sm-1 col-xs-12 text-center fixBug">
            <a href="javascript:;">
                @lang('group.lb_afhaal_levertijd')
            </a>
        </div>
        <div class="col-item col-sm-1 col-xs-12 text-center">
            <a href="javascript:;">
                @lang('group.lb_afsluit')
            </a>
        </div>
        <div class="col-item col-sm-3 col-xs-12 text-center">
            <a href="javascript:;">
                @lang('group.lb_adres')
            </a>
        </div>
        <div class="col-item col-sm-1-5 col-xs-12 text-right">
            <a href="#">
                @lang('group.lb_laatste_bestelling') {{Helper::getIconSort('')}}
            </a>
        </div>
        <div class="col-item col-sm-1-5 col-xs-12 text-center">
        </div>
    </div>
    <div class="list-body list-body-manager">
        @foreach($groups as $k => $item)
            <div id="tr-{{ $item->id }}" class="row pdl-10 font-size-14" data-id="{{ $item->id }}" >
                <div class="col-item col-sm-1 col-xs-12 cut-text">
                    <span class="switch-{{ $item->id }}">
                        <input type="checkbox" id="switch-{{ $item->id }}"
                               value="{{$item->active == 0 ? \App\Models\Workspace::INACTIVE : \App\Models\Workspace::ACTIVE}}"
                               class="switch-input" {{$item->active == \App\Models\Workspace::ACTIVE ? 'checked' : null}} />
                        <label
                                data-route="{{route($guard.'.groups.updateStatus', [$item->id])}} "
                                for="switch-{{ $item->id }}" class="switch update-status"></label>
                    </span>
                </div>
                <div class="col-item col-sm-1 col-xs-12 cut-text">
                    <b>{!! $item->name !!}</b>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    {!! \App\Models\Group::getTypes($item->type) !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12 text-center">
                    {!! \Carbon\Carbon::parse($item->receive_time)->format('H:i') !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12 text-center">
                    {!! \Carbon\Carbon::parse($item->close_time)->format('H:i') !!}
                </div>
                <div class="col-item col-sm-3 col-xs-12 text-center cut-text">
                    {!! $item->address_display !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-center">
                    @php($orderLastest = $item->orders->where('parent_id', '<>', NULL)->sortByDesc('created_at')->first())
                    {{ $orderLastest ? \Carbon\Carbon::parse($orderLastest->created_at)->timezone($orderLastest->timezone)->format('d/m/Y H:i') : NULL }}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        <li>
                            <a href="javascript:;" class="showItem"
                               data-route="{{ route($guard.'.groups.edit', [$item->id]) }}"
                               data-id="{{ $item->id }}">@lang('category.edit')</a>
                        </li>
                        <li>
                            <a href="{{route($guard.'.groups.statistic.perProduct', $item->id)}}" target="_blank">@lang('menu.statistic')</a>
                        </li>
                        @if (Helper::isSuperAdmin())
                        <li>
                            <a href="javascript:;" class="show-confirm"
                               data-route="{{ route($guard.'.groups.destroy', [$item->id]) }}"
                               data-title="{{trans('workspace.are_you_sure_delete', ['name' => $item->name])}}"
                               data-id="{{ $item->id }}"
                               data-deleted_success="@lang('group.deleted_successfully')"
                               data-close_label="@lang('workspace.close')"
                               data-yes_label="@lang('common.yes_delete')"
                               data-no_label="@lang('common.no_cancel')">@lang('category.delete')</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($groups))
    {{ $groups->appends(request()->all())->links() }}
@endif