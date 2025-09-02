@php
    $basePath = app()->getLocale().'/'.$guard;
    $activeClass = request()->menu == 'sm' ? 'active-sm' : 'active';
@endphp

<li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/dashboard*') ? $activeClass : '' }}">
    <a href="{{ route($guard.'.dashboard.index') }}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.625 7.875L10.5 1.75L18.375 7.875V17.5C18.375 17.9641 18.1906 18.4092 17.8624 18.7374C17.5342 19.0656 17.0891 19.25 16.625 19.25H4.375C3.91087 19.25 3.46575 19.0656 3.13756 18.7374C2.80937 18.4092 2.625 17.9641 2.625 17.5V7.875Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.875 19.25V10.5H13.125V19.25" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>@lang('menu.home')</span>
        <span class="tooltiptext">@lang('menu.home')</span>
    </a>
</li>

<li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/orders*') ? $activeClass : '' }}">
    <a href="{!! route($guard.'.orders.index') !!}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.75 7C15.75 5.60761 15.1969 4.27226 14.2123 3.28769C13.2277 2.30312 11.8924 1.75 10.5 1.75C9.10761 1.75 7.77226 2.30312 6.78769 3.28769C5.80312 4.27226 5.25 5.60761 5.25 7C5.25 13.125 2.625 14.875 2.625 14.875H18.375C18.375 14.875 15.75 13.125 15.75 7Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12.0138 18.375C11.86 18.6402 11.6392 18.8603 11.3735 19.0133C11.1079 19.1664 10.8067 19.2469 10.5001 19.2469C10.1935 19.2469 9.89229 19.1664 9.62663 19.0133C9.36097 18.8603 9.14016 18.6402 8.98633 18.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>@lang('menu.orders')</span>
        <span class="tooltiptext">@lang('menu.orders')</span>
    </a>
</li>

<li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/statistic*') ? $activeClass : '' }}">
    <a href="{!! route($guard.'.statistic.perProduct') !!}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.75 17.5V8.75" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.5 17.5V3.5" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5.25 17.5V12.25" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>@lang('menu.statistic')</span>
        <span class="tooltiptext">@lang('menu.statistic')</span>
    </a>
</li>

<li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/categories*') || $request->is($basePath.'/products*') || $request->is($basePath.'/options*') ? $activeClass : '' }}">
    <a href="{!! route($guard.'.categories.index') !!}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.625 10.5H18.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2.625 5.25H18.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2.625 15.75H18.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>@lang('menu.menukaart')</span>
        <span class="tooltiptext">@lang('menu.menukaart')</span>
    </a>
</li>

<li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/users*') ? $activeClass : '' }}">
    <a href="{{ route($guard.'.users.index') }}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 18.375V16.625C17.5 15.6967 17.1313 14.8065 16.4749 14.1501C15.8185 13.4937 14.9283 13.125 14 13.125H7C6.07174 13.125 5.1815 13.4937 4.52513 14.1501C3.86875 14.8065 3.5 15.6967 3.5 16.625V18.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.5 9.625C12.433 9.625 14 8.058 14 6.125C14 4.192 12.433 2.625 10.5 2.625C8.567 2.625 7 4.192 7 6.125C7 8.058 8.567 9.625 10.5 9.625Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>@lang('menu.users')</span>
        <span class="tooltiptext">@lang('menu.users')</span>
    </a>
</li>

@php($isShowGroup = !empty($tmpWorkspace->workspaceExtras) ? $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::GROUP_ORDER)->first() : null)
@if (isset($tmpWorkspace) && $isShowGroup && $isShowGroup->active)
    <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/groups*') ? $activeClass : '' }}">
        <a href="{{ route($guard.'.groups.index') }}">
            <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0)">
                    <path d="M14.875 18.375V16.625C14.875 15.6967 14.5063 14.8065 13.8499 14.1501C13.1935 13.4937 12.3033 13.125 11.375 13.125H4.375C3.44674 13.125 2.5565 13.4937 1.90013 14.1501C1.24375 14.8065 0.875 15.6967 0.875 16.625V18.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.875 9.625C9.808 9.625 11.375 8.058 11.375 6.125C11.375 4.192 9.808 2.625 7.875 2.625C5.942 2.625 4.375 4.192 4.375 6.125C4.375 8.058 5.942 9.625 7.875 9.625Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.125 18.3745V16.6245C20.1244 15.849 19.8663 15.0957 19.3912 14.4828C18.9161 13.8699 18.2509 13.4322 17.5 13.2383" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2.73828C14.7529 2.93104 15.4202 3.36889 15.8967 3.9828C16.3732 4.59671 16.6319 5.35176 16.6319 6.12891C16.6319 6.90605 16.3732 7.6611 15.8967 8.27501C15.4202 8.88892 14.7529 9.32677 14 9.51953" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0">
                        <rect width="21" height="21" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
            <span>@lang('menu.groups')</span>
            <span class="tooltiptext">@lang('menu.groups')</span>
        </a>
    </li>
@endif

@php($isShowNotification = !empty($tmpWorkspace->workspaceExtras) ? $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::OWN_MOBILE_APP)->first() : null)
@if (isset($tmpWorkspace) && $isShowNotification && $isShowNotification->active)
    <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/notifications*') ? $activeClass : '' }}">
        <a href="{{ route($guard.'.notifications.index') }}">
            <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.25 1.75L9.625 11.375" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19.25 1.75L13.125 19.25L9.625 11.375L1.75 7.875L19.25 1.75Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>@lang('menu.push_notifications')</span>
            <span class="tooltiptext">@lang('menu.push_notifications')</span>
        </a>
    </li>
@endif

@php($ownMobileApp = (!empty($tmpWorkspace->workspaceExtras))
    ? $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::OWN_MOBILE_APP)->first() : null)
@if (!empty($tmpWorkspace) && $ownMobileApp && $ownMobileApp->active)
    <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/apps*') ? $activeClass : '' }}">
        <a href="{{ route($guard.'.apps.settings') }}">
            <svg class="menu-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 2H7C5.89543 2 5 2.89543 5 4V20C5 21.1046 5.89543 22 7 22H17C18.1046 22 19 21.1046 19 20V4C19 2.89543 18.1046 2 17 2Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 18H12.01" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span style="top: -4;">@lang('menu.manage_app')</span>
            <span class="tooltiptext">@lang('menu.manage_app')</span>
        </a>
    </li>
@endif

<li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/coupons*') || $request->is($basePath.'/rewards*') ? $activeClass : '' }}">
    <a href="{!! route($guard.'.coupons.index') !!}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.625 4.375L4.375 16.625" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5.6875 7.875C6.89562 7.875 7.875 6.89562 7.875 5.6875C7.875 4.47938 6.89562 3.5 5.6875 3.5C4.47938 3.5 3.5 4.47938 3.5 5.6875C3.5 6.89562 4.47938 7.875 5.6875 7.875Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.3125 17.5C16.5206 17.5 17.5 16.5206 17.5 15.3125C17.5 14.1044 16.5206 13.125 15.3125 13.125C14.1044 13.125 13.125 14.1044 13.125 15.3125C13.125 16.5206 14.1044 17.5 15.3125 17.5Z" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>@lang('menu.loyalty')</span>
        <span class="tooltiptext">@lang('menu.loyalty')</span>
    </a>
</li>

<li class="ir-sidebar-item ir-h5 {{ 
    $request->is($basePath.'/settings/general*') || 
    $request->is($basePath.'/settings/opening-hours*') || 
    $request->is($basePath.'/settings/preferences*') || 
    $request->is($basePath.'/settings/time-slots*') || 
    $request->is($basePath.'/settings/delivery-conditions*') || 
    $request->is($basePath.'/settings/print*') || 
    $request->is($basePath.'/settings/payment-methods*') ? $activeClass : '' }}">
    <a href="{{ route($guard.'.settings.general') }}">
        <svg class="menu-icon" width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0)">
                <path d="M3.5 18.375V12.25" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3.5 8.75V2.625" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10.5 18.375V10.5" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10.5 7V2.625" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17.5 18.375V14" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17.5 10.5V2.625" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M0.875 12.25H6.125" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.875 7H13.125" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.875 14H20.125" stroke="#CFCFCD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <defs>
                <clipPath id="clip0">
                    <rect width="21" height="21" fill="white"/>
                </clipPath>
            </defs>
        </svg>
        <span>@lang('menu.settings')</span>
        <span class="tooltiptext">@lang('menu.settings')</span>
    </a>
</li>