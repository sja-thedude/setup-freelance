<header class="container-fluid new-home {!! !empty($headerTitleKey) ? $headerTitleKey : '' !!}">
    <div class="container-fluid">
        <div class="col-sm-2 col-md-2">
            <a href="{!! route($guard.'.index') !!}">
                @if(!empty($headerTitleKey) && $headerTitleKey == 'how-does-it-work')
                    <img src="{!! url('assets/images/logo/logo_black.svg') !!}" class="logo">
                @else
                    <img src="{!! url('assets/images/logo/new_logo.svg') !!}" class="logo">
                @endif
            </a>
        </div>
        <div class="col-sm-10 col-md-10">
            <nav class="navbar navbar-inverse">
                <button  class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="collapse navbar-collapse menu" id="navbarNavAltMarkup">
                    <ul class="nav navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item @if(request()->route()->getName() == 'web.index') active @endif"><a href="{{route('web.index')}}" class="nav-link">{{trans('dashboard.home')}}</a></li>
                        <li class="nav-item"><a href="{{ Url('/') }}/{{App::getLocale()}}/how-does-it-work.html" class="nav-link">{{trans('dashboard.how_does_it_work')}}</a></li>
                        <li class="nav-item @if(request()->route()->getName() == 'web.search_restaurant') active @endif"><a href="{{route('web.search_restaurant')}}" class="nav-link">{{trans('dashboard.find_merchants')}}</a></li>
                        <li class="nav-item"><a href="{{ Url('/') }}/{{App::getLocale()}}/contact.html" class="nav-link">{{trans('strings.contact.title_detail')}}</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle border-0 switch-language" data-toggle="dropdown" href="#" aria-expanded="false">
                                @php
                                    $locale = \App::getLocale()
                                @endphp
                                <span class="language {{$locale}}">{{strtoupper($locale)}}</span>
                                <i class="icn-arrow-down"></i>
                            </a>
                            <div class="dropdown-menu languages">
                                <a class="dropdown-item {!! $locale == 'en' ? 'active' : '' !!}" href="{{route('lang.switch', ['lang' => 'en'])}}">
                                    <span title="{!! trans('common.languages.en') !!} text-uppercase">{!! trans('common.languages.en') !!}</span>
                                </a>
                                <a class="dropdown-item {!! $locale == 'fr' ? 'active' : '' !!}" href="{{route('lang.switch', ['lang' => 'fr'])}}">
                                    <span title="{!! trans('common.languages.fr') !!} text-uppercase">{!! trans('common.languages.fr') !!}</span>
                                </a>
                                <a class="dropdown-item {!! $locale == 'nl' ? 'active' : '' !!}" href="{{route('lang.switch', ['lang' => 'nl'])}}">
                                    <span title="{!! trans('common.languages.nl') !!} text-uppercase">{!! trans('common.languages.nl') !!}</span>
                                </a>
                                <a class="dropdown-item {!! $locale == 'de' ? 'active' : '' !!}" href="{{route('lang.switch', ['lang' => 'de'])}}">
                                    <span title="{!! trans('common.languages.de') !!} text-uppercase">{!! trans('common.languages.de') !!}</span>
                                </a>
                            </div>
                        </li>
                        @if(auth()->guest())
                            <li class="nav-item">
                                <a class="btn btn-small pull-right color-theme btn-show-login-modal cr-header-btn" href="javascript:;"
                                   data-toggle="popup" data-target="#modalLogin">
                                    @lang('strings.menu_my_account')
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="javascript:;" class="header-time has-submenu">
                                    <img class="msg-notify" src="{!! url('images/icon-time-white.svg') !!}"/>
                                </a>
                                @php
                                    $inStatuses = [
                                        \App\Models\Order::PAYMENT_STATUS_PENDING,
                                        \App\Models\Order::PAYMENT_STATUS_PAID,
                                    ];
                                    $orderLists = \App\Helpers\Order::getOrderByUser($inStatuses, true);
                                @endphp
                                @if(!$orderLists->isEmpty())
                                    <ul class="sub-menu sub-menu-email sub-menu-time sub-order-history">
                                        @foreach($orderLists as $order)
                                            @php
                                                $order = OrderHelper::convertOrderItem($order);
                                            @endphp
                                            <li>
                                                <?php $dateTimeLocal = \App\Helpers\Helper::convertDateTimeToTimezone($order->date_time, $order->timezone); ?>
                                                <h6>
                                                    @lang('cart.success_datetime', [
                                                        'date' => Carbon\Carbon::parse($dateTimeLocal)->format('d/m/Y'),
                                                        'time' => Carbon\Carbon::parse($dateTimeLocal)->format('H:i')
                                                    ])
                                                </h6>
                                                <span>
                                                    {{$order->workspace->name}}
                                                </span>
                                                <span>
                                                    #{{$order->daily_id_display . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '')}} -
                                                    @if($order->type == \App\Models\Cart::TYPE_TAKEOUT)
                                                        @lang('cart.tab_afhaal')
                                                    @elseif($order->type == \App\Models\Cart::TYPE_LEVERING)
                                                        @lang('cart.tab_levering')
                                                    @else
                                                        @lang('cart.tab_in_house')
                                                    @endif
                                                </span>
                                                <a href="javascript:;" class="eye-color-portal order-detail"
                                                   data-target="pop-order-detail"
                                                   data-route="{!! route($guard.'.orders.show', [$order->id]) !!}"
                                                   data-toggle="popup"
                                                   data-target="#pop-order-detail">
                                                    <i class="icn-eye-color"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" class="header-email has-submenu notifications"
                                   data-route="{!! route($guard.'.notification.index') !!}">
                                    <img class="msg-notify" src="{!! url('images/msg_notification.svg') !!}"/>

                                    @if(!auth()->guest())
                                        <span class="number user-{{auth()->user()->id}}">
                                                        {{Helper::displayNotificationNumberByUser()}}
                                                    </span>
                                    @endif
                                </a>
                                @if(!auth()->guest())
                                    <ul id="notification-list" class="sub-menu sub-menu-email"></ul>
                                @endif
                            </li>

                            @if(!auth()->guest())
                                @include('web.partials.menu-profile')
                            @endif
                        @endif
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="m-container-fluid">
        <div class="row">
            <div class="col-sm-12 mgt-20">
                <div class="float-left">
                    <img src="{!! url('/images/home/navbar-toggle.svg') !!}" class="navbar-toggle m-menu-open"/>
                </div>
                <div class="header-logo">
                    <a href="{!! route($guard.'.index') !!}">
                        <img src="{!! url('assets/images/logo/new_logo.svg') !!}">
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="menu-overlay"></div>
        <div class="m-side-menu-wrapper" style="left: -300px">
            <div class="row">
                <a href="#" class="menu-close">×</a>
            </div>
            <div class="m-side-content">
                <div class="row">
                    <div class="col-xs-2 sw-language">
                        <a class="nav-link dropdown-toggle border-0 switch-language" data-toggle="dropdown" href="#" aria-expanded="false">
                            @php
                                $locale = \App::getLocale()
                            @endphp
                            <span class="language {{$locale}}">{{strtoupper($locale)}}</span>
                            <i class="icn-arrow-down"></i>
                        </a>
                        <div class="dropdown-menu languages">
                            <a class="dropdown-item" href="{{route('lang.switch', ['lang' => 'en'])}}">
                                <span title="English">EN</span>
                            </a>
                            <a class="dropdown-item" href="{{route('lang.switch', ['lang' => 'fr'])}}">
                                <span title="French">FR</span>
                            </a>
                            <a class="dropdown-item" href="{{route('lang.switch', ['lang' => 'nl'])}}">
                                <span title="Dutch">NL</span>
                            </a>
                            <a class="dropdown-item" href="{{route('lang.switch', ['lang' => 'de'])}}">
                                <span title="German">DE</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-9 m-side-logo">
                        <a href="{!! route($guard.'.index') !!}" class="">
                            <img src="{!! url('assets/images/logo/new_logo.svg') !!}">
                        </a>
                        @if(!auth()->guest())
                            <div class="nav-item menu-time" id="menu-time">
                                <a href="javascript:;" class="header-time has-submenu">
                                    <img class="msg-notify" src="{!! url('images/icon-time-white.svg') !!}"/>
                                </a>
                                @php
                                    $inStatuses = [
                                        \App\Models\Order::PAYMENT_STATUS_PENDING,
                                        \App\Models\Order::PAYMENT_STATUS_PAID,
                                    ];
                                    $orderLists = \App\Helpers\Order::getOrderByUser($inStatuses, true);
                                @endphp
                                @if(!$orderLists->isEmpty())
                                    <ul class="sub-menu sub-menu-email sub-menu-time sub-order-history" id="m-menu-time">
                                        @foreach($orderLists as $order)
                                            @php
                                                $order = OrderHelper::convertOrderItem($order);
                                            @endphp
                                            <li>
                                                <?php $dateTimeLocal = \App\Helpers\Helper::convertDateTimeToTimezone($order->date_time, $order->timezone); ?>
                                                <div>
                                                    <h6>
                                                        @lang('cart.success_datetime', [
                                                            'date' => Carbon\Carbon::parse($dateTimeLocal)->format('d/m/Y'),
                                                            'time' => Carbon\Carbon::parse($dateTimeLocal)->format('H:i')
                                                        ])
                                                    </h6>
                                                    <span>
                                                        {{$order->workspace->name}}
                                                    </span>
                                                    <span>
                                                        #{{$order->daily_id_display . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '')}} -
                                                        @if($order->type == \App\Models\Cart::TYPE_TAKEOUT)
                                                            @lang('cart.tab_afhaal')
                                                        @elseif($order->type == \App\Models\Cart::TYPE_LEVERING)
                                                            @lang('cart.tab_levering')
                                                        @else
                                                            @lang('cart.tab_in_house')
                                                        @endif
                                                    </span>
                                                </div>
                                                <a href="javascript:;" class="eye-color-portal order-detail"
                                                data-target="pop-order-detail"
                                                data-route="{!! route($guard.'.orders.show', [$order->id]) !!}"
                                                data-toggle="popup"
                                                data-target="#pop-order-detail">
                                                    <i class="icn-eye-color"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="nav-item menu-email" id="menu-email">
                                <a href="javascript:void(0);" class="header-email has-submenu notifications" data-route="{!! route($guard.'.notification.index') !!}">
                                    <img class="msg-notify" src="{!! url('images/msg_notification.svg') !!}"/>
                                    @if(!auth()->guest())
                                        <span class="number user-{{auth()->user()->id}}">
                                            {{Helper::displayNotificationNumberByUser()}}
                                        </span>
                                    @endif
                                </a>
                                @if(!auth()->guest())
                                    <ul id="m-notification-list" class="sub-menu sub-menu-email"></ul>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row btn-login">
                    @if(auth()->guest())
                        <a class="btn btn-small color-theme btn-show-login-modal" href="javascript:;"
                        data-toggle="popup" data-target="#modalLogin">
                            @lang('strings.menu_my_account')
                        </a>
                    @else
                        @if(!auth()->guest())
                            @include('web.partials.menu-profile')
                        @endif
                    @endif
                    <div class="clearfix"></div>
                </div>
                <div class="m-menu">
                    <h5>MENU</h5>
                    <ul class="">
                        <li class=" @if(request()->route()->getName() == 'web.index') active @endif"><a href="{{route('web.index')}}" class="nav-link">{{trans('dashboard.home')}}</a></li>
                        <li class=""><a href="{{ Url('/') }}/{{App::getLocale()}}/how-does-it-work.html" class="nav-link">{{trans('dashboard.how_does_it_work')}}</a></li>
                        <li class=" @if(request()->route()->getName() == 'web.search_restaurant') active @endif"><a href="{{route('web.search_restaurant')}}" class="nav-link">{{trans('dashboard.find_merchants')}}</a></li>
                        <li class=""><a href="{{ Url('/') }}/{{App::getLocale()}}/contact.html" class="nav-link">{{trans('strings.contact.title_detail')}}</a></li>
                        <li class=" dropdown"></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <h1 class="title">{{$headerTitle ?? config('app.name') }}</h1>
    @if(!empty($isInSearchPage))
        <main class="search">
            <div class="container location-search">
                @include('web.home.partials.location_search')
            </div>
        </main>
    @endif
</header>