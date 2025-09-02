<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('id')}}">
                    @lang('sms.id') {{Helper::getIconSort('id')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('workspace_id')}}">
                    @lang('sms.restaurant') {{Helper::getIconSort('workspace_id')}}
                </a>
            </div>
            <div class="col-item col-sm-1 col-xs-12">
                <a href="{{Helper::getFullSortUrl('status')}}">
                    @lang('sms.status') {{Helper::getIconSort('status')}}
                </a>
            </div>
            <div class="col-item col-sm-4 col-xs-12">
                <a href="{{Helper::getFullSortUrl('message')}}">
                    @lang('sms.message') {{Helper::getIconSort('message')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('sent_at')}}">
                    @lang('sms.sent_at') {{Helper::getIconSort('sent_at')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('created_at')}}">
                    @lang('sms.created_at') {{Helper::getIconSort('created_at')}}
                </a>
            </div>
        </div>
    </div>
    <div class="list-body restaurant">
        @foreach($sms as $smsItem)
            <div id="tr-{{ $smsItem->id }}" class="row">
                <div class="col-item col-sm-1 col-xs-12">
                    {!! $smsItem->id !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! !empty($smsItem->workspace) ? $smsItem->workspace->name : '' !!}
                </div>
                <div class="col-item col-sm-1 col-xs-12 text-uppercase">
                    {!! \App\Models\Sms::statusOptions($smsItem->status) !!}
                </div>
                <div class="col-item col-sm-4 col-xs-12">
                    {!! $smsItem->message !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    @if(!empty($smsItem->sent_at))
                        <span class="time-convert" data-format="DD/MM/YYYY HH:mm" data-datetime="{!! $smsItem->sent_at !!}"></span>
                    @endif
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    @if(!empty($smsItem->created_at))
                        <span class="time-convert" data-format="DD/MM/YYYY HH:mm" data-datetime="{!! $smsItem->created_at !!}"></span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($sms))
    {{ $sms->appends(request()->all())->links() }}
@endif