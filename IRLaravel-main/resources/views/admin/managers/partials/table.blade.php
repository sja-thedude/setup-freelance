<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('name')}}">
                    @lang('manager.name') {{Helper::getIconSort('name')}}
                </a>
            </div>
            <div class="col-item col-sm-3 col-xs-12">
                <a href="{{Helper::getFullSortUrl('email')}}">
                    @lang('manager.email') {{Helper::getIconSort('email')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('gsm')}}">
                    @lang('manager.gsm') {{Helper::getIconSort('gsm')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('status')}}">
                    @lang('manager.status') {{Helper::getIconSort('status')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12 text-right">
                <a href="{{Helper::getFullSortUrl('created_at')}}">
                    @lang('manager.active_date') {{Helper::getIconSort('created_at')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body">
        @foreach ($model as $data)
            <div id="tr-{{ $data->id }}" class="row pdb-6">
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->name !!}
                </div>
                <div class="col-item col-sm-3 col-xs-12">
                    {!! $data->email !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->gsm !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12 status">
                    @lang('manager.status_'. $data->status)
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    {!! date(config('datetime.dateFormat'), strtotime($data->created_at)) !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        @if(Helper::checkUserPermission($guard.'.managers.sendInvitation'))
                            <li>
                                <a data-toggle="modal" data-target="#modal_reset_password_{!! $data->id !!}"
                                   class="cursor-pointer">
                                    @lang('manager.send_invitation')
                                </a>
                            </li>
                        @endif
                        @if(Helper::checkUserPermission($guard.'.managers.destroy'))
                            <li>
                                <a data-toggle="modal" data-target="#modal_assign_manager_{!! $data->id !!}" 
                                   class="cursor-pointer show-delete-confirm">
                                    @lang('common.remove')
                                </a>
                            </li>                             
                        @endif
                    </ul>
                    
                    @include($guard.'.managers.partials.modal_reset_password')
                    @include($guard.'.managers.partials.modal_assign_manager')
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($model))
    {{ $model->appends(request()->all())->links() }}
@endif