@if(!empty(
    $workspace['setting_preference']->holiday_text) 
    && Route::currentRouteName() == $guard.'.user.index'
))
    <div id="popupContact">
        <div class="popup-content">
            <a class="popupContactClose" href="javascript:;">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 1L1 13" stroke="black" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <path d="M1 1L13 13" stroke="black" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </a>
            <a href="{!! route($guard.'.index') !!}" class="avatar border-circle width-80">
                <img src="{{ url($workspace['photo'] ? $workspace['photo'] : "images/no-img.png") }}" alt="Logo"/>
            </a>
            <div class="display-content">
                @php $content = explode("\n", $workspace['setting_preference']->holiday_text); @endphp
                @foreach($content as $text)
                    <p>{{$text}}</p>
                @endforeach
            </div>
            <a href="javascript:;" class="btn btn-modal btn-pr-custom close-popup"
               style="line-height: normal;">@lang('common.close')</a>
        </div>
    </div>
    <div id="backgroundPopup"></div>
@endif