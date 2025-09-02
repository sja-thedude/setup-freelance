<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-9 col-xs-12">
                @lang('user.name')
            </div>
            <div class="col-item col-sm-1-5 col-xs-12 text-right">
                @lang('user.active_date')
            </div>
            <div class="col-item col-sm-1-5 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body">
        @foreach ($model as $data)
            <div id="tr-{{ $data->id }}" class="row pdb-6">
                <div class="col-item col-sm-9 col-xs-12">
                    {!! $data->name !!}
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
                        <li>
                            <a href="#" data-toggle="modal" data-target="#detail-{{$data->id}}">
                                @lang('category.edit')
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" class="show-confirm" 
                                data-route="{{route($guard.'.type-zaak.destroy', [$data->id])}}"
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
                
                @include($guard.'.type_zaak.partials.modal_edit')
            </div>
        @endforeach
    </div>
</div>

@if(!empty($model))
    {{ $model->appends(request()->all())->links() }}
@endif