<div class="row">
    <div class="col-md-8 col-md-push-2">
        <div class="wrap-popup-text">
            <a href="javascript:;" class="close" data-dismiss="popup" data-target="#pop-up-eye">
                <i class="icn-close"></i>
            </a>
            <div class="row  ">
                <div class="col-md-12">
                    <h5>{{$notification->title}}</h5>
                    <span class="time-convert"
                         data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                         data-datetime="{!! $notification->sent_time !!}">
                    </span>
                    <label>{{$notification->description}}</label>
                </div>
            </div>
            <a href="javascript:;" class="btn btn-andere btn-bottom" data-dismiss="popup" data-target="#pop-up-eye">
                @lang('common.close')
            </a>
        </div>
    </div>
</div>