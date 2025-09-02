@if(!empty($webWorkspace))
    @if(!$webWorkspace->settingExceptHours->isEmpty() && !isset(Request::segments()[2]))
        <div id="holiday" class="popup-center">
            <div class="wrap-popup">
                <p>
                    <svg class="icon-calendar" width="29" height="30" viewBox="0 0 29 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.9583 5H6.04167C4.70698 5 3.625 6.11929 3.625 7.5V25C3.625 26.3807 4.70698 27.5 6.04167 27.5H22.9583C24.293 27.5 25.375 26.3807 25.375 25V7.5C25.375 6.11929 24.293 5 22.9583 5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19.334 2.5V7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.66602 2.5V7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.625 12.5H25.375" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    @foreach($webWorkspace->settingExceptHours as $hour)
                        {{$hour->description}} <br>
                    @endforeach
                </p>
            </div>
        </div>
    @endif
@endif