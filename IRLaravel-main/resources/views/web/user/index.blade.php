@extends('layouts.web-user')

@php
    $settingOpenHour    = $workspace['setting_open_hours'];
    $isOpendTabTakeOut  = $settingOpenHour->where('type', \App\Models\SettingOpenHour::TYPE_TAKEOUT)->where('active', 1)->first();
    $isOpendTabLevering = $settingOpenHour->where('type', \App\Models\SettingOpenHour::TYPE_DELIVERY)->where('active', 1)->first();
@endphp

@if (!$isOpendTabTakeOut && $isOpendTabLevering && \request()->get('tab') !== \App\Models\Cart::TAB_LEVERING)
    <script>window.location="{{ route('web.category.index') . "?tab=" . \App\Models\Cart::TAB_LEVERING }}";</script>
@endif

@section('content')
    <div class="header-search mobile-category">
        <div class="row">
            <div class="col-md-12">
                <form action="javascript:;" class="form-category display-none">
                    <div class="wrap-search">
                        <div class="search-action">
                            <div class="wrap-action">
                                <a href="javascript:;" class="search-text">
                                    <svg width="17" height="17" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2 6.25076C2 3.90221 3.90176 2 6.24564 2C8.58953 2 10.4913 3.90221 10.4913 6.25076C10.4913 8.5993 8.58953 10.5015 6.24564 10.5015C3.90176 10.5015 2 8.5993 2 6.25076ZM6.24564 0C2.79535 0 0 2.79948 0 6.25076C0 9.70203 2.79535 12.5015 6.24564 12.5015C7.6279 12.5015 8.90503 12.0522 9.93943 11.2917C9.96471 11.3255 9.99242 11.3581 10.0225 11.3892L13.2823 14.7568C13.6665 15.1536 14.2996 15.1639 14.6964 14.7798C15.0932 14.3956 15.1035 13.7625 14.7194 13.3657L11.4596 9.99817C11.4185 9.95573 11.3745 9.91771 11.3284 9.88412C12.0605 8.86008 12.4913 7.60564 12.4913 6.25076C12.4913 2.79948 9.69594 0 6.24564 0Z" fill="#413E38"/>
                                    </svg>
                                </a>

                                @if(request()->segment(2) == "favourite")
                                    @php $favourite = "favourite"; @endphp
                                @else
                                    @php $favourite = null; @endphp
                                @endif
                                @if(!auth()->guest())
                                    <a href="{!! route($guard.'.favourite.index') !!}" class="heart">
                                        @if(request()->segment(2) == "favourite")
                                        <div class="favourite">
                                            <svg width="17" height="17" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664Z" fill="#413E38" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        @else
                                        <svg width="17" height="17" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664V2.15664Z" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        @endif
                                    </a>
                                @endif
                                <input value="" type="text" placeholder="@lang('frontend.search_product')" class="search-box" data-workspace="{{$workspaceId}}" data-url="{!! route($guard.'.product') !!}?{{$favourite}}" data-locale="{{$locale}}">
                                <input type="hidden" value="0" id="is_search">
                                <input type="hidden" name="workspace_id" id="workspace_id" value="{{$workspaceId}}">
                                <input type="hidden" name="category_id" id="category_id" value="{{$categoryId}}">
                                <input type="hidden" name="order_type" id="order_type" value="{{$orderType}}">
                                <input type="hidden" name="locale" id="locale" value="{{$locale}}">
                                <a href="javascript:;" class="remove-input" id="remove-input" data-url="{!! route($guard.'.product') !!}?{{$favourite}}"><i class="icon-times-circle"></i></a>
                            </div>
                        </div>
                        <div class="owl-search-category category-pc1 category-mobile">
                            @php
                                $tab = request()->get('tab');
                                if(empty($tab) && !empty($cart)) {
                                    if($cart->type == \App\Models\Order::TYPE_TAKEOUT ) {
                                        $tab = \App\Models\Cart::TAB_TAKEOUT;
                                    }
                                    if($cart->type == \App\Models\Order::TYPE_DELIVERY ) {
                                        $tab = \App\Models\Cart::TAB_LEVERING;
                                    }
                                }
                            @endphp
                            @php($i=0)
                            @foreach($categories as $category)
                                <div id="menu-category-{{$category['id']}}" data-id="{{$category['id']}}" class="active-category @if($category['favoriet_friet'] || $category['kokette_kroket'])active-active @else none-active @endif @if(!empty(request()->segments()[2]) && request()->segments()[2] == $category['id'])current @endif">
                                    <a class="@if(!$category['favoriet_friet'] && !$category['kokette_kroket'])none @endif" href="{{ route($guard.'.category.index', ['category' => $category['id']]) . "?tab=" . $tab }}">
                                        @if($category['favoriet_friet'])
                                            <i class="icn-active-menu-friet"></i>
                                        @endif
                                        @if($category['kokette_kroket'])
                                            <i class="icn-active-menu-kroket"></i>
                                        @endif
                                        {{$category['name']}}
                                    </a>
                                </div>
                                @php($i++)
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="container" >
        <div id="main-body">
            <div class="row">
                <div id="product">
                    @include('web.user.product')
                </div>

                @if ($isOpendTabTakeOut || $isOpendTabLevering)
                    @include('web.carts.index', [
                        'isOpendTabTakeOut'  => $isOpendTabTakeOut,
                        'isOpendTabLevering' => $isOpendTabLevering,
                    ])
                @endif
                <div id="loyalties-container"></div>
            </div>
        </div>
    </div>

    {{--Product popup--}}
    <div id="product-detail"></div>

    @include('web.carts.partials.modal-product-suggestion')
    @include('web.carts.partials.modal-fill-address')
    @include('web.carts.partials.modal-unavaliable-time')

    @if (session()->has('idsProductFail') && count(session()->get('idsProductFail')) > 0)
        @if(session()->has('isWeekdayError') && (session()->get('isWeekdayError') == true))
            @include('web.carts.partials.modal-not-available', ['message' => trans('cart.een_of_weekday')])
        @else
            @include('web.carts.partials.modal-not-available', ['message' => trans('cart.een_of')])
        @endif
        @php(session()->forget('idsProductFail'))
        @php(session()->forget('isWeekdayError'))
    @endif

    @if (session()->get('address_not_avaliable')
        && (
            (\request()->get('tab') === \App\Models\Cart::TAB_LEVERING && \request()->has('again'))
            || (\request()->get('tab') !== \App\Models\Cart::TAB_LEVERING)
        )
    )
        @include('web.carts.partials.modal-not-available', [
            'message' => trans('cart.address_not_avaliable', ['workspace' => $workspace['name']]),
            'url'     => route('web.category.index') . (\request()->has('again') ? "" : "?reopen=modal-fill-address"),
        ])
    @endif

    @if (session()->has('product_not_avaliable'))
        @include('web.carts.partials.modal-not-available', ['message' => trans('cart.product_not_avaliable')])
    @endif
@endsection
