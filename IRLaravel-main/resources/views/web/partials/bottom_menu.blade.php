@php
    $allowedRoutes = [
        'web.user.index',
        'loyalties.index',
        'web.favourite.index'
    ];
@endphp
@if(in_array(request()->route()->getName(), $allowedRoutes))
    <div class="bottom-menu">
        <input type="hidden" class="isShoppingCartBottomClick" value="@if(request()->has('step')) 1 @else 0 @endif" >
        <ul>
            <li>
                <a href="javascript:;" class="search-menu-button active">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 6H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 18H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <strong>{{trans('dashboard.menu')}}</strong>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="shopping-cart-button">
                    <svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 1L1 5V19C1 19.5304 1.21071 20.0391 1.58579 20.4142C1.96086 20.7893 2.46957 21 3 21H17C17.5304 21 18.0391 20.7893 18.4142 20.4142C18.7893 20.0391 19 19.5304 19 19V5L16 1H4Z" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1 5H19" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 9C14 10.0609 13.5786 11.0783 12.8284 11.8284C12.0783 12.5786 11.0609 13 10 13C8.93913 13 7.92172 12.5786 7.17157 11.8284C6.42143 11.0783 6 10.0609 6 9" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    @if(!empty($cart) && $cart->cartItems->count() > 0)
                        <i class="cart-counter @if(auth()->check()) logined  @endif">{{$cart->cartItems->count()}}</i>
                    @endif
                    <strong>{{trans('cart.title_winkelmand')}}</strong>
                </a>
            </li>
            @if(auth()->check())
                @php
                    $isShowGroup = $webWorkspace
                    ->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                    ->first();
                @endphp
                <li>
                    <a href="javascript:;" class="loyalties-button" data-loyalty-active="{{!empty($isShowGroup)?$isShowGroup['active']:''}}" data-route="{!! route('loyalties.index') !!}">
                        <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15Z" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.21 13.8899L3 22.9999L8 19.9999L13 22.9999L11.79 13.8799" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <strong>{{trans('menu.reward')}}</strong>
                    </a>
                </li>
                <input type="hidden" name="not_use_loyalty_card" class="not_use_loyalty_card" value="{{trans('loyalty.not_use_loyalty_card', ['restaurant_name' => $webWorkspace->name])}}" />
            @endif

            <li>
                <a href="javascript:;" class="show-menu-user" data-islogin="@if(!auth()->guest()) 1 @else 0 @endif">
                    <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 19V17C17 15.9391 16.5786 14.9217 15.8284 14.1716C15.0783 13.4214 14.0609 13 13 13H5C3.93913 13 2.92172 13.4214 2.17157 14.1716C1.42143 14.9217 1 15.9391 1 17V19" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke="#413E38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <strong>{{trans('strings.account')}}</strong>
                </a>
            </li>
        </ul>
    </div>

    @push('style')
        {!! Html::style('builds/css/web.loyalty.css') !!}
    @endpush

    @push('scripts')
        {!! Html::script('builds/js/web.loyalty.js') !!}
    @endpush
@endif