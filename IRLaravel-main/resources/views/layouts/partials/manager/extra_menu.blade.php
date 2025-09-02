@if ($request->is($basePath.'/categories*') || $request->is($basePath.'/products*') || $request->is($basePath.'/options*'))
    <ul class="nav navbar-nav navbar-left">
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/categories*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.categories.index') !!}">
                @lang('menu.category')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/products*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.products.index') !!}">
                @lang('menu.product')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/options*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.options.index') !!}">
                @lang('menu.option')
            </a>
        </li>
    </ul>
@endif

@if ($request->is($basePath.'/coupons*') || $request->is($basePath.'/rewards*'))
    <ul class="nav navbar-nav navbar-left">
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/coupons*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.coupons.index') !!}">
                @lang('menu.coupon')
            </a>
        </li>
        @php($isShowGroup = $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)->first())
        @if (isset($tmpWorkspace) && $isShowGroup && $isShowGroup->active)
            <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/rewards*') ? 'active' : '' }}">
                <a href="{!! route($guard.'.rewards.index') !!}">
                    @lang('menu.reward')
                </a>
            </li>
        @endif
    </ul>
@endif

@if ($request->is($basePath.'/settings*'))
    <ul class="nav navbar-nav navbar-left">
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/settings/general*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.settings.general') !!}">
                @lang('setting.general.general')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/settings/opening-hours*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.settings.openingHours') !!}">
                @lang('setting.opening_hours.opening_hours')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/settings/preferences*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.settings.preferences') !!}">
                @lang('setting.preferences.preferences')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/settings/time-slots*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.settings.timeSlots') !!}">
                @lang('setting.time_slots.time_slots')
            </a>
        </li>
        <li class="hidden-print dropdown ir-sidebar-item ir-h5 {{ $request->is($basePath.'/settings/payment-methods*') || $request->is($basePath.'/settings/delivery-conditions*') || $request->is($basePath.'/settings/print*') ? 'active' : '' }}">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                @if($request->is($basePath.'/settings/payment-methods*'))
                    @lang('setting.more.payment_methods')
                @elseif($request->is($basePath.'/settings/delivery-conditions*'))
                    @lang('setting.more.delivery_conditions')
                @elseif($request->is($basePath.'/settings/print*'))
                    @lang('setting.more.print')
                @elseif($request->is($basePath.'/settings/connectors*'))
                    @lang('setting.more.connectors')
                @else
                    @lang('setting.more.more')
                @endif
                <i class=" fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{!! route($guard.'.settings.paymentMethods') !!}" class="no-border">@lang('setting.more.payment_methods')</a>
                </li>
                <li>
                    <a href="{!! route($guard.'.settings.deliveryConditions') !!}" class="no-border">@lang('setting.more.delivery_conditions')</a>
                </li>
                <li>
                    <a href="{!! route($guard.'.settings.print') !!}" class="no-border">@lang('setting.more.print')</a>
                </li>
                @if(!empty($tmpWorkspace->id))
                    @php($isShowConnectors = $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CONNECTORS)->first())
                    @if(!empty($isShowConnectors) && $isShowConnectors->active)
                        <li>
                            <a href="{!! route($guard.'.settings.connector.index') !!}" class="no-border">@lang('setting.more.connectors')</a>
                        </li>
                    @endif
                @endif
            </ul>
        </li>
    </ul>
@endif

@if ($request->is($basePath.'/statistic*'))
    <ul class="nav navbar-nav navbar-left hidden-print">
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/statistic/per-product*') ? 'active' : '' }}">
            <a class="text-transform-init" href="{!! route($guard.'.statistic.perProduct') !!}">
                @lang('statistic.per_product')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/statistic/per-payment-method*') ? 'active' : '' }}">
            <a class="text-transform-init" href="{!! route($guard.'.statistic.perPaymentMethod') !!}">
                @lang('statistic.per_payment_method')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/statistic/discount*') ? 'active' : '' }}">
            <a class="text-transform-init" href="{!! route($guard.'.statistic.discount') !!}">
                @lang('statistic.discount')
            </a>
        </li>
    </ul>
@endif

@if ($request->is($basePath.'/groups/statistic*') && !empty($group))
    <ul class="nav navbar-nav navbar-left hidden-print">
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/groups/statistic/per-product*') ? 'active' : '' }}">
            <a class="text-transform-init" href="{!! route($guard.'.groups.statistic.perProduct', $group->id) !!}">
                @lang('statistic.per_product')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/groups/statistic/per-payment-method*') ? 'active' : '' }}">
            <a class="text-transform-init" href="{!! route($guard.'.groups.statistic.perPaymentMethod', $group->id) !!}">
                @lang('statistic.per_payment_method')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/groups/statistic/discount*') ? 'active' : '' }}">
            <a class="text-transform-init" href="{!! route($guard.'.groups.statistic.discount', $group->id) !!}">
                @lang('statistic.discount')
            </a>
        </li>
    </ul>
@endif

{{-- Manage App menu --}}
@if ($request->is($basePath.'/apps*'))
    <ul class="nav navbar-nav navbar-left">
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/apps/settings*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.apps.settings') !!}">
                @lang('menu.manage_app_functions')
            </a>
        </li>
        <li class="ir-sidebar-item ir-h5 {{ $request->is($basePath.'/apps/theme*') ? 'active' : '' }}">
            <a href="{!! route($guard.'.apps.theme') !!}">
                @lang('menu.manage_app_theme')
            </a>
        </li>
    </ul>
@endif

<ul class="nav navbar-nav navbar-right hidden-print">
    <li class="dropdown">
        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <img src="{{ $auth->photo }}" alt="">
            <span class="profile-name">{{ $auth->name }}</span>
            <span class=" fa fa-angle-down"></span>
        </a>
        
        <ul class="dropdown-menu dropdown-usermenu pull-right">
            <li>
                <a class="cursor-pointer" data-toggle="modal" data-target="#modal_manager_profile">
                    @lang('strings.menu_profile')
                </a>
            </li>
            <li>
                <a href="{{ route($guard.'.logout') }}">
                    @lang('strings.menu_logout')
                </a>
            </li>
        </ul>
    </li>

    <li class="dropdown">
        <a href="javascript:;" class="dropdown-toggle">
            <span class="online-offline">
                <input type="checkbox" id="switch"
                        value="{{!empty($tmpWorkspace) && $tmpWorkspace->is_online == true ? \App\Models\Workspace::IS_OFFLINE : \App\Models\Workspace::IS_ONLINE}}"
                        class="switch-input" {{!empty($tmpWorkspace) && $tmpWorkspace->is_online == \App\Models\Workspace::IS_ONLINE ? 'checked' : null}} />
                <label 
                    data-route="{{route($guard.'.restaurants.updateStatus', [
                    $tmpUser->workspace_id, 'is_online' => true
                    ])}}"
                    for="switch" class="switch update-status"></label>
            </span>
            <span class="profile-name">
                @lang('dashboard.online')    
            </span>
        </a>
    </li>

    <li class="dropdown">
        @include('layouts.partials.switch_lang')
    </li>

    <li role="presentation" class="dropdown" style="display: none;">
        <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-envelope-o"></i>
            <span class="badge bg-green">6</span>
        </a>

        @include('layouts.partials.manager.msg_list')
    </li>
</ul>