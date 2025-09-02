<div class="list-responsive black list-responsive-small">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-2 col-xs-12">
                @lang('notification.label_title')
            </div>
            <div class="col-item col-lg-7 col-sm-4 col-xs-12">
                @lang('notification.label_description')
            </div>
            <div class="col-item col-lg-1-5 col-sm-3 col-xs-12 text-right">
                @lang('notification.date_time')
            </div>
            <div class="col-item col-lg-1-5 col-sm-3 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body white">
        @foreach ($model as $data)
            <div id="tr-{{ $data->id }}" class="row pdb-6">
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->title !!}
                </div>
                <div class="col-item col-lg-7 col-sm-4 col-xs-12">
                    {!! $data->description !!}
                </div>
                <div class="col-item col-lg-1-5 col-sm-3 col-xs-12 text-right">
                    <span class="time-convert"
                         data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                         data-datetime="{!! $data->send_datetime !!}">
                    </span>
                </div>
                <div class="col-item col-lg-1-5 col-sm-3 col-xs-12 text-right">
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
                                data-route="{{route($guard.'.notifications.destroy', [$data->id])}}"
                                data-title="@lang('notification.are_you_sure_delete')" 
                                data-id="{{$data->id}}"
                                data-deleted_success="@lang('notification.deleted_successfully')" 
                                data-close_label="@lang('workspace.close')" 
                                data-yes_label="@lang('common.yes_delete')" 
                                data-no_label="@lang('common.no_cancel')">
                                @lang('workspace.remove')
                            </a>
                        </li>
                    </ul>
                </div>
                
                @include($guard.'.notifications.partials.modal_detail')
            </div>
        @endforeach
    </div>
</div>

@if(!empty($model))
    {{ $model->appends(request()->all())->links() }}
@endif