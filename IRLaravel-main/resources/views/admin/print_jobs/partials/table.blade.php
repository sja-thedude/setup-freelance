<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('id')}}">
                    @lang('printjob.id') {{Helper::getIconSort('id')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('workspace_id')}}">
                    @lang('printjob.restaurant') {{Helper::getIconSort('workspace_id')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('printer_id')}}">
                    @lang('printjob.printer_id') {{Helper::getIconSort('printer_id')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('status')}}">
                    @lang('printjob.status') {{Helper::getIconSort('status')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('mac_address')}}">
                    @lang('printjob.mac_address') {{Helper::getIconSort('mac_address')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('job_type')}}">
                    @lang('printjob.job_type') {{Helper::getIconSort('job_type')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('foreign_id')}}">
                    @lang('printjob.order_id') {{Helper::getIconSort('foreign_id')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('retries')}}">
                    @lang('printjob.retries') {{Helper::getIconSort('retries')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('printed_at')}}">
                    @lang('printjob.printed_at') {{Helper::getIconSort('printed_at')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('created_at')}}">
                    @lang('printjob.created_at') {{Helper::getIconSort('created_at')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                @lang('printjob.options')
            </div>
        </div>
    </div>
    <div class="list-body restaurant">
        @foreach($jobs as $job)
            <div id="tr-{{ $job->id }}" class="row">
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $job->id !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! !empty($job->workspace) ? $job->workspace->name : '' !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $job->printer_id !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12 text-uppercase">
                    {!! \App\Models\PrinterJob::statusOptions($job->status) !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $job->mac_address !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! \App\Models\PrinterJob::typeOptions($job->job_type) !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $job->foreign_id !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $job->retries !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    @if(!empty($job->printed_at))
                        <span class="time-convert" data-format="DD/MM/YYYY HH:mm" data-datetime="{!! $job->printed_at !!}"></span>
                    @endif
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    @if(!empty($job->created_at))
                        <span class="time-convert" data-format="DD/MM/YYYY HH:mm" data-datetime="{!! $job->created_at !!}"></span>
                    @endif
                </div>
                <div class="col-item col-sm-1 col-xs-12">
                    @if(Helper::checkUserPermission($guard.'.printjob@cancel')
                         && $job->status != \App\Models\PrinterJob::STATUS_ERROR
                         && $job->status != \App\Models\PrinterJob::STATUS_DONE)
                        {!! Form::open(['route' => ['admin.printjob.cancel', $job->id], 'method' => 'put', 'class' => 'inline-block']) !!}
                        {!! Form::button(trans('common.cancel'), ['type' => 'submit', 'class' => 'btn btn-default btn-xs', 'onclick' => "return confirm('". trans('common.are_you_sure') ."')"]) !!}
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($jobs))
    {{ $jobs->appends(request()->all())->links() }}
@endif