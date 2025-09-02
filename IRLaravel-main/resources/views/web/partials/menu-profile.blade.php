<li class="nav-item">
    <a href="javascript:;" class="header-right has-submenu">
        <span class="border-circle ">
            <img width="50px" height="50px" src="{{ url(auth()->user()->photo ?: '/images/avatar-right.jpg') }}" alt="Avatar">
        </span>
        <span class="wrap-text">
            <span>{{ auth()->user()->name }}</span>
            <i class="icn-arrow-down"></i>
        </span>
    </a>
    <ul class="sub-menu">
        <li class="hide-on-pc">
            <a href="javascript:;"
               class="messages-button-mobile" data-route="{!! route($guard.'.notification.index') !!}">
                @lang('frontend.messages')
            </a>
        </li>
        <li class="hide-on-pc">
            <a href="javascript:;"
               class="order-history-mobile">
                @lang('frontend.order_history')
            </a>
        </li>
        <li>
            <a href="javascript:;"
               class="profile-user-button profile-mobile">
                @lang('frontend.mijn_profiel')
            </a>
        </li>

        @php
            $workspaceObj = $webWorkspace;
            $allowLoyalties = false;
            if ($workspaceObj) {
                $workspaceExtras = $workspaceObj->workspaceExtras;
                $allowLoyalties = $workspaceExtras
                ->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                ->where('active', true)
                ->count() > 0;
            }
        @endphp

        @if($allowLoyalties)
            <li>
                <div class="hide-on-pc">
                    @if(!empty($categoryId))
                        <a class="loyalty-mobile" href="{!! route('web.category.index', [$categoryId]) !!}?tab={{\App\Models\Cart::TAB_TAKEOUT}}">@lang('frontend.klantenkaart')</a>
                    @else
                        <a href="{{ route('loyalties.index') }}">@lang('frontend.klantenkaart')</a>
                    @endif
                </div>
                <div class="hide-on-mobile">
                    <a href="{{ route('loyalties.index') }}">@lang('frontend.klantenkaart')</a>
                </div>
            </li>
        @endif

        <li>
            <a href="{{ route('logout') }}">
                @lang('frontend.uitloggen')
            </a>
        </li>
    </ul>
</li>
