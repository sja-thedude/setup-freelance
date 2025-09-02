<!DOCTYPE html><!--[if lt IE 7 ]> <html lang="{{ app()->getLocale() }}" class="ie6"> <![endif]-->
<!--[if IE 7 ]> <html lang="{{ app()->getLocale() }}" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="{{ app()->getLocale() }}" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="{{ app()->getLocale() }}" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{{ app()->getLocale() }}"> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ItsReady</title>
<meta name="keywords" content="" >
<meta name="description" content="ItsReady" >
<meta name="author" content="ItsReady" >
<meta name="robots" content="index" >
<meta name="googlebot" content="noarchive" >
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Custom Theme Style -->
    @stack('style-top')
    <link href="{{ URL::to('/builds/css/vendor.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
    <link href="{{ URL::to('/builds/css/main-core.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">
    <link href="{{ URL::to('/builds/css/main.web.css') . '?v=' . config('app.version_front.css') }} }}" rel="stylesheet">

    @include('layouts.partials.style_theme_home')

    {{-- Custom Style --}}
    @stack('style')

    @php
        $primaryColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->primary_color : null;
        $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
    @endphp

    <script>
        window.DOMAIN = '{{URL::to('/')}}/';
        var defaultLang = '{!! app()->getLocale() !!}';
    </script>

<!-- Mobile Optimized -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

</head>
<body class="body homepage">
        <!-- PC layout -->
    <div id="pc-wrapper">
        <header id="header" >
            @section('header')
                <div class="main-header">
                    <div class="row">
                        <div class="col-md-6 left-header">
                            @if(!empty($webWorkspace->photo))
                                <a href="{!! route($guard.'.index') !!}" class="logo border-circle width-120">
                                    <img src="{!! url($webWorkspace->photo) !!}" alt="Logo"/>
                                </a>
                            @endif
                            <h2>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->title : null}}</h2>
                            <h6>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->subtitle : null}}</h6>
                        </div>
                        <div class="col-md-6 text-right right-header">
                            @if(auth()->guest())
                                <a class="btn btn-small pull-right color-theme" href="{{ route('login') }}">
                                    @lang('strings.menu_my_account')
                                </a>
                                <div class="pull-right">
                                    <nav class="menu pull-right menu-white">
                                        <ul>
                                            {{--@include('web.partials.menu-language', ['class' => '-white'])--}}
                                        </ul>
                                    </nav>
                                </div>
                            @else
                                <div class="pull-right">
                                    <nav class="menu pull-right menu-white">
                                        <ul>
                                            {{--@include('web.partials.menu-language', ['class' => '-white'])--}}
                                            @include('web.partials.menu-profile')
                                        </ul>
                                    </nav>
                                </div>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            @show
        </header>
    
        @yield('slider')
    
        <div id="container" class="cd-main-content">
            @yield('content')
        </div>
    
        @include('web.partials.holiday', ['webWorkspace' => $webWorkspace])
    </div>
    <!-- End pc layout -->
    
    <!-- Mobile layout -->
    <div id="m-wrapper">
        <div id="m-header">
            <div class="hp-slider">
                <div class="wrap-title-map-information">
                    <a class="mgl-10 information" href="javascript:;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="9.5" stroke="#ffffff"/>
                            <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="#ffffff"/>
                        </svg>
                    </a>
                    @include('web.partials.info')
                    <div class="overlay-mobile"></div>
                    @if(auth()->guest())
                        <a href="javascript:;" class="login-homepage">
                            <i class="icn-user">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </i>
                        </a>
                    @else
                        <div class="pull-right">
                            <nav class="menu pull-right menu-white">
                                <ul>
                                    @include('web.partials.menu-profile')
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
                
                @include('web.partials.mobile_slider')
                
                @if(!empty($webWorkspace->photo))
                    <a href="{!! route($guard.'.index') !!}" class="logo border-circle">
                        <img src="{!! url($webWorkspace->photo) !!}" alt="Logo"/>
                    </a>
                @endif
                <div class="title-subtitle">
                    <h2>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->title : null}}</h2>
                    <h6>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->subtitle : null}}</h6>
                </div>
                <img class="bg-elip" src="{{ url('/images/bg-elip.png') }}" alt="">
            </div>
            <div class="mobile-header" style="background: {{$primaryColor}};display: none;">
                <input type="hidden" class="primary-color" value="{{$primaryColor}}">
                <a class="mobile-header-back-button" data-url="{!! route($guard.'.index') !!}" href="javascript:;"><i class="icon-chevron-left"></i></a>
                <h2>
                    {{$workspace['name']}}
                </h2>
                <div class="wrap-title-map-information">

                    <i class="mgl-10 information">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="9.5" stroke="#ffffff"/>
                            <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="#ffffff"/>
                        </svg>
                    </i>
                    @include('web.partials.info')
                    <div class="overlay-mobile">
                    </div>
                </div>
            </div>
        </div>
        
        @include('web.partials.holiday', ['webWorkspace' => $webWorkspace])
        
        <div class="wrap-mobile">
            @include('web.partials.mobile-wrap')
        </div>

        <div id="m-content">
            <div class="row animation">
                <div class="col-md-12">
                    <div class="animation" id="wrapMSwitchAfhaalLevering" style="{{ session()->has('address_not_avaliable') ? "display:none" : "left:-500px; display: none;" }}">
                        @php $categoryId = !empty($categories) && !empty($categories[0]) ? $categories[0]['id'] : $workspace['id']; @endphp
                        
                        @if($is_takeout)
                            {!! Form::open(['route' => ['web.cartAddress.store', $categoryId], 'method' => 'POST', 'class' => 'step-order']) !!}
                                {!! Form::hidden('type', \App\Models\Cart::TYPE_TAKEOUT) !!}
                                {!! Form::hidden('workspace_id', $workspace['id']) !!}
                                {!! Form::hidden('group_id', NULL) !!}
                                {!! Form::hidden('user_id', $userId) !!}
                                <button class="btn-mobile btn-color-primary mgb-10" type="submit">
                                    @lang('landing.switch_afhaal')
                                </button>
                            {!! Form::close() !!}
                        @endif
                        @if($is_delivery)
                            <a href="javascript:;" class="btn-mobile btn-white mgb-15 mBtnLevering" data-redirect="{{ !$userId ? route('login') . "?from=group" : "" }}">
                                @lang('landing.switch_levering')
                            </a>
                        @endif
                        @if($is_group_order)
                            <a href="javascript:;" class="btnSearchGroup dark-grey" data-logged="{{ $userId ? 1 : 0 }}">
                                @lang('landing.wenst_u_te')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End mobile layout -->
    <div class="mobile-login responsive-modal" data-withoutcart="{{route('web.cart.storeWithoutLogin')}}" data-workspace-id="{{$webWorkspace->id}}"></div>
    
    <div class="mobile-forgot-password responsive-modal"></div>
    <div class="mobile-forgot-password-confirmation responsive-modal"></div>
    <div class="mobile-register responsive-modal"></div>
    <div class="mobile-register-confirmation responsive-modal"></div>
    @if (!auth()->guest())
        @include('web.user.partials.modal-edit-profile')

        {{-- Mobile order history --}}
        <div class="modal-content custom-modal modelOrderHistory hidden">
            <div class="wrap-content">
            <a href="javascript:;" class="close" onclick="document.getElementsByClassName('modelOrderHistory')[0].classList.add('hidden')">×</a>
                <div class="mobile-order-history">
                    @include('layouts.partials.mobile-order-history')
                </div>
            </div>
        </div>
        <!--Order Detail-->
        <div class="pop-up hidden" id="pop-order-detail"></div>

        <div class="modal-content custom-modal modelMessage hidden">
            <div class="wrap-content">
            <a href="javascript:;" class="close" onclick="document.getElementsByClassName('modelMessage')[0].classList.add('hidden')">×</a>
                <div id="mobile-messages-user" class="mobile-messages-user">
                    <h4>@lang('frontend.messages')</h4>
                    <ul></ul>
                </div>
            </div>
        </div>
        <!--Notification Detail-->
        <div class="pop-up hidden" id="pop-up-eye"></div>
    @else
       @include('web.partials.popup_login')
    @endif

    <div id="modal-box-map-view" style="display: none"></div>
    <div class="lds-dual-ring" id="loader-page"></div>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={!! config('maps.api_key') !!}&language={{ app()->getLocale() }}&callback=initializeLocationsGoogleMaps"></script>
    <script src="{{ URL::to('/builds/js/vendor.web.js')  . '?v=' . config('app.version_front.js') }}"></script>
    <script src="{{ URL::to('/builds/js/main.web.js')  . '?v=' . config('app.version_front.js') }}"></script>
    <script>
        @if(!empty($webWorkspace))
            MainWeb.fn.checkIfTemplateAppInstalled('{{$webWorkspace->template_app_ios}}', '{{$webWorkspace->template_app_android}}')
        @endif
    </script>
    @stack('scripts')
</body>
</html>
