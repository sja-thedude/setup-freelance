<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('active')}}">
                    @lang('workspace.label_status') {{Helper::getIconSort('active')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('name')}}">
                    @lang('workspace.label_company') {{Helper::getIconSort('name')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('types')}}">
                    @lang('workspace.label_type') {{Helper::getIconSort('types')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('manager_name')}}">
                    @lang('workspace.label_account_manager') {{Helper::getIconSort('manager_name')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('gsm')}}">
                    @lang('workspace.label_gsm') {{Helper::getIconSort('gsm')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('address')}}">
                    @lang('workspace.label_address') {{Helper::getIconSort('address')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12 text-right">
                <a href="{{Helper::getFullSortUrl('last_order')}}">
                    @lang('workspace.last_order') {{Helper::getIconSort('last_order')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body restaurant">
        @foreach($workspaces as $workspace)
            <div id="tr-{{ $workspace->id }}" class="row">
                <div class="col-item col-sm-1 col-xs-12">
                    @if(Helper::checkUserPermission($guard.'.workspace@assignaccountmanager'))
                        <input type="checkbox" class="flat checkbox get-status-id hidden" name="checkbox" data-role="checkbox" value="{{$workspace->id}}" />
                    @endif
{{--                    <input type="hidden" id="idPost" value="{{ $workspace->id }}">--}}
                    
                    <span class="switch-{{ $workspace->id }}">
                        <input type="checkbox" id="switch-{{ $workspace->id }}"
                            value="{{$workspace->active == true ? \App\Models\Workspace::INACTIVE : \App\Models\Workspace::ACTIVE}}"
                            class="switch-input" {{$workspace->active == \App\Models\Workspace::ACTIVE ? 'checked' : null}} />
                        <label 
                            data-route="{{route($guard.'.restaurants.updateStatus', [$workspace->id])}}"
                            for="switch-{{ $workspace->id }}" class="switch update-status"></label>
                    </span>
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $workspace->name !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {{
                        !empty($workspace->workspaceCategories) && $workspace->workspaceCategories->count() > 0 ? 
                        implode(', ', $workspace->workspaceCategories->pluck('name')->toArray()) : null
                    }}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! !empty($workspace->workspaceAccount) ? $workspace->workspaceAccount->name : null !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $workspace->gsm !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $workspace->address !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    @if(empty($workspace->status))
                        <span style="color: #B5B268">@lang('workspace.status_invitation')</span>
                    @else
                        @php($lastOrder = $workspace->orders()->orderBy('id', 'desc')->first() ? $workspace->orders()->orderBy('id', 'desc')->first()->created_at : null)
                        {!! Helper::getDateFromFormat($lastOrder, null, $guard) !!}    
                    @endif
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        <li>
                            <a target="_blank" href="{!! route($guard.'.restaurants.autoLogin', ['id' => $auth->id, 'workspace_id' => $workspace->id]) !!}">
                                @lang('workspace.login')
                            </a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#detail-{{$workspace->id}}">@lang('workspace.details')</a>
                        </li>
                        @if(Helper::checkUserPermission($guard.'.workspaceextra.index'))
                            <li>
                                <a href="#" data-toggle="modal" data-target="#workspace-extra-{{$workspace->id}}">
                                    @lang('workspace.extras')
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal_reset_password_{{$workspace->id}}">
                                @lang('workspace.resend_invitation')
                            </a>
                        </li>
                        @if(Helper::checkUserPermission($guard.'.workspace@destroy'))
                        <li>
                            <a href="javascript:;" class="show-confirm" 
                                data-route="{{route($guard.'.restaurants.destroy', [$workspace->id])}}"
                                data-title="{{trans('workspace.are_you_sure_delete', ['name' => $workspace->name])}}"
                                data-id="{{$workspace->id}}"
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
                
                @include($guard.'.workspaces.partials.modal_extra')
                @include($guard.'.workspaces.partials.modal_detail')
                @include($guard.'.workspaces.partials.modal_reset_password')
            </div>
        @endforeach
    </div>
</div>

@if(!empty($workspaces))
    {{ $workspaces->appends(request()->all())->links() }}
@endif