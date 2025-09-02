@if(!$notification->isEmpty())
    @foreach($notification as $item)
        <li @if(count($notification) == 1) class="border-none" @endif>
            <div>
                <a class="notification-detail" data-route="{!! route($guard.'.notification.show', [$item->id]) !!}" 
                href="javascript:;" 
                data-toggle="popup" 
                data-target="pop-up-eye">
                    {{$item->title}}
                    <i class="icn-eye-color hide-pc @if($item->status == \App\Models\Notification::ACTIVE) read @endif"></i>
                </a>
                <span>{{  str_limit($item->description, 80, '...')}}</span>
            </div>
            <a data-route="{!! route($guard.'.notification.show', [$item->id]) !!}" 
            href="javascript:;" 
            class="eye-color notification-detail" 
            data-toggle="popup" 
            data-target="pop-up-eye">
                <i class="icn-eye-color @if($item->status == \App\Models\Notification::ACTIVE) read @endif"></i>
            </a>
        </li>
    @endforeach
@else
<div class="no-order-available">
    <span>@lang('notification.no_notification')</span>
</div>
@endif
