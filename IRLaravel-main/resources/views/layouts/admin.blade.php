<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
        <title>{!! config('app.name') !!}</title>
    
        <link id="link-vendor" href="{{ URL::to('/builds/css/vendor.admin.css') }}" rel="stylesheet">
        <link href="{{ URL::to('/assets/iCheck/skins/flat/green.css') }}" rel="stylesheet">
        <!-- Custom Theme Style -->
        @stack('style-top')
        <link id="link-all" href="{{ URL::to('/builds/css/all.css') }}" rel="stylesheet">
        <link href="{{ URL::to('/builds/css/main.admin.css'). '?v=' . config('app.version_manager') }}" rel="stylesheet">
        {{-- Custom Style --}}
        @stack('style')
    
        <script>
            window.DOMAIN = '{{URL::to('/')}}/';
            var defaultLang = '{!! app()->getLocale() !!}';
        </script>
    </head>

    @php($basePath = app()->getLocale().'/'.$guard)
    
    <body class="footer_fixed ir-theme ir-sidebar-bg page-admin nav-{{ request()->menu == 'sm' ? 'sm' : 'md' }}">

        <input type="hidden" id="localeDaysOfWeek" value="{{ json_encode(array_values(trans('daterangepicker.daysOfWeek'))) }}">
        <input type="hidden" id="localeMonthNames" value="{{ json_encode(array_values(trans('daterangepicker.monthNames'))) }}">

        <div class="container body">
            @if(!empty($auth))
                <div class="main_container">
                    <div class="col-md-3 left_col">
                        <div class="left_col scroll-view ir-sidebar">
                            <div class="navbar nav_title ir-sidebar-nav-title">
                                <a href="{{ route($guard.'.dashboard.index') }}" class="site_title text-center">
                                    <img class="blue_logo" src="{!! url('assets/images/logo/blue_logo.svg') !!}"/>
                                    <img class="icon_logo" src="{!! url('assets/images/logo/icon_logo.svg') !!}" style="display: none;"/>
                                </a>
                            </div>
        
                            <div class="clearfix"></div>
        
                            <!-- menu profile quick info -->
                            <div class="profile" style="display: none;">
                                <div class="profile_pic">
                                    <img src="{{ $auth->photo }}" alt="..." class="img-circle profile_img">
                                </div>
                                <div class="profile_info">
                                    <span>Welcome,</span>
                                    <h2>{{ $auth->name }}</h2>
                                </div>
                            </div>
                            <!-- /menu profile quick info -->
        
                            <br/>
        
                            <!-- sidebar menu -->
                            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                                <div class="menu_section">
                                    {{--<h3>General</h3>--}}
                                    <ul class="nav side-menu">
                                        @include('layouts.partials.generated-menu')
                                    </ul>
                                </div>
                            </div>
                            <!-- /sidebar menu -->
                            <!-- /menu footer buttons -->
                            <div class="sidebar-footer hidden-small ir-sidebar-bg text-center">
                                <div class="copyright text-uppercase">
                                    @lang('common.copyright')
                                </div>
                            </div>
                            <!-- /menu footer buttons -->
                        </div>
                    </div>
        
                    <!-- top navigation -->
                    <div class="top_nav">
                        <div class="nav_menu ir-topbar">
                            <div class="col-sm-6 col-xs-12 ir-top-menu">
                                @if($request->is($basePath.'/managers*') || $request->is($basePath.'/vats*') || $request->is($basePath.'/type-zaak*'))
                                    @if(Helper::checkUserPermission($guard.'.managers.index'))
                                        <a href="{{ route($guard.'.managers.index') }}" class="{{ $request->is($basePath.'/managers*') ? 'active' : '' }}">
                                            <span>@lang('menu.account_managers')</span>
                                        </a>
                                    @endif

                                    @if(Helper::checkUserPermission($guard.'.vats.edit'))
                                        <a href="{!! route('admin.vats.index') !!}" class="{{ $request->is($basePath.'/vats*') ? 'active' : '' }}">
                                            <span>@lang('menu.vat')</span>
                                        </a>
                                    @endif

                                    @if(Helper::checkUserPermission($guard.'.type-zaak.index'))
                                        <a href="{!! route('admin.type-zaak.index') !!}" class="{{ $request->is($basePath.'/type-zaak*') ? 'active' : '' }}">
                                            <span>@lang('menu.type_zaak')</span>
                                        </a>
                                    @endif
                                @endif
                            </div>
                            <nav class="col-sm-6 col-xs-12">
                                @include('layouts.partials.extra_menu')
                            </nav>
                        </div>
                    </div>
                        
                    @include('layouts.partials.modal_manager_profile')
                    @include('layouts.partials.modal_manager_change_password')
                    <!-- /top navigation -->
        
                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="ir-toggle nav toggle">
                            <a id="menu_toggle">
                                <i class="fa fa-angle-left ir-toggle-icon ir-toggle-left" aria-hidden="true"></i>
                                <i class="fa fa-angle-right ir-toggle-icon ir-toggle-right" aria-hidden="true" style="display: none;"></i>
                            </a>
                        </div>
                        
                        @yield('content')
                    </div>
                    <!-- /page content -->
                </div>
            @endif
        </div>
        
        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={!! config('maps.api_key') !!}&language={{ app()->getLocale() }}"></script>
        
        <script src="{{ URL::to('/builds/js/vendor.admin.js') }}"></script>
        <script src="{{ URL::to('/assets/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ URL::to('/builds/js/all.js'). '?v=' . config('app.version_manager') }}"></script>
        <script src="{{ URL::to('/builds/js/main.admin.js'). '?v=' . config('app.version_manager') }}"></script>
        
        <!-- CKEditor scripts -->
        @include('layouts.partials.ckeditor_scripts')
        
        @stack('scripts')
    </body>
</html>