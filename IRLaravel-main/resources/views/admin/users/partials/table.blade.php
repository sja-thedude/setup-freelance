<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('active')}}">
                    @lang('user.label_status') {{Helper::getIconSort('active')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('name')}}">
                    @lang('user.name') {{Helper::getIconSort('name')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('email')}}">
                    @lang('user.email') {{Helper::getIconSort('email')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('address')}}">
                    @lang('user.address') {{Helper::getIconSort('address')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('birthday')}}">
                    @lang('user.birthday') {{Helper::getIconSort('birthday')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12">
                <a href="{{Helper::getFullSortUrl('gsm')}}">
                    @lang('user.gsm') {{Helper::getIconSort('gsm')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('gender')}}">
                    @lang('user.gender') {{Helper::getIconSort('gender')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12 text-right">
                <a href="{{Helper::getFullSortUrl('created_at')}}">
                    @lang('user.active_date') {{Helper::getIconSort('created_at')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body">
        @foreach ($model as $data)
            <div id="tr-{{ $data->id }}" class="row pdb-6">
                <div class="col-item col-sm-1 col-xs-12">
                    <span class="switch-{{ $data->id }}">
                        <input type="checkbox" id="switch-{{ $data->id }}"
                               value="{{$data->active == true ? \App\Models\Workspace::INACTIVE : \App\Models\Workspace::ACTIVE}}"
                               class="switch-input" {{$data->active == \App\Models\Workspace::ACTIVE ? 'checked' : null}} />
                        <label
                                data-route="{{route($guard.'.users.updateStatus', [$data->id])}} "
                                for="switch-{{ $data->id }}" class="switch update-status"></label>
                    </span>
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $data->name !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->email !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->address !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! Helper::getDateFromFormat($data->birthday, null, $guard) !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12">
                    {!! $data->gsm !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {{ (array_key_exists($data->gender, App\Models\User::genders())) ? App\Models\User::genders($data->gender) : '' }}
                </div>
                <div class="col-item col-sm-1 col-xs-12 text-right">
                    {!! date(config('datetime.dateFormat'), strtotime($data->created_at)) !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#detail-{{$data->id}}">
                                @lang('user.details')
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" class="show-confirm" 
                                data-route="{{route($guard.'.users.destroy', [$data->id])}}"
                                data-title="{{trans('user.are_you_sure_delete', ['name' => $data->name])}}"
                                data-id="{{$data->id}}"
                                data-deleted_success="@lang('user.deleted_successfully')" 
                                data-close_label="@lang('workspace.close')" 
                                data-yes_label="@lang('common.yes_delete')" 
                                data-no_label="@lang('common.no_cancel')">
                                @lang('workspace.remove')
                            </a>
                        </li>
                    </ul>
                </div>
                
                @include($guard.'.users.partials.modal_detail')
            </div>
        @endforeach
    </div>
</div>

@if(!empty($model))
    {{ $model->appends(request()->all())->links() }}
@endif