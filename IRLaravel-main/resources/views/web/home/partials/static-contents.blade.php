<div class="container-fluid">
    <div class="row">
        <div class="col-md-7 col-sm-12 col-left restaurant-content">
            <h2>{{trans('dashboard.choose_favorite_trader')}}</h2>
            <p>{!! trans('dashboard.everything_is_possible') !!} {!! trans('dashboard.enter_location') !!}</p>
        </div>
        <div class="col-md-5 col-sm-12 list-restaurant-box">
            <div class="restaurant-box restaurant_1">
                <div class="restaurant-box-img">
                    <div class="logo-restaurant" style="background-image: url('{!! asset('images/home/restaurant_1.svg') !!}');"></div>
                </div>
                <div class="full-width">
                    <div class="row">
                        <div class="col-xs-10 col-sm-10 col-md-10 padding-0">
                            <h4>Ramen Rose</h4>
                            <p>sushi, chinees, indisch</p>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 delivery-status">
                            <p>1 km</p>
                            <p class="status">Open</p>
                            <div class="award"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 padding-0">
                            <span class="time">
                                <img class="restaurant-icon" src="{!! url("/images/home/waiting-time-icon.svg") !!}"/>
                                <span></i>±20 min</span>
                            </span>
                            <span class="delivery">
                                <img class="restaurant-icon" src="{!! url("/images/home/motobike.svg") !!}"/>
                                <span>€2,00</span>
                            </span>
                            <span class="price">
                                <img class="restaurant-icon" src="{!! url("/images/home/money.svg") !!}"/>
                                <span class="min-title">Min</span>
                                <span>€20,00</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row restaurant-box restaurant_2">
                <div class="restaurant-box-img">
                    <div class="logo-restaurant" style="background-image: url('{!! asset('images/home/restaurant_2.svg') !!}');"></div>
                </div>
                <div class="full-width">
                    <div class="row">
                        <div class="col-xs-10 col-sm-10 col-md-10 padding-0">
                            <h4>Slices</h4>
                            <p>pizza, pasta, snacks</p>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 delivery-status">
                            <p>2 km</p>
                            <p class="status">Open</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 padding-0">
                            <span class="time">
                                <img class="restaurant-icon" src="{!! url("/images/home/waiting-time-icon.svg") !!}"/>
                                <span>±20 min</span>
                            </span>
                            <span class="delivery">
                                <img class="restaurant-icon" src="{!! url("/images/home/motobike.svg") !!}"/>
                                <span>gratis</span>
                            </span>
                            <span class="price">
                                <img class="restaurant-icon" src="{!! url("/images/home/money.svg") !!}"/>
                                <span class="min-title">Min</span>
                                <span>€20,00</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row restaurant-box restaurant_3">
                <div class="restaurant-box-img">
                    <div class="logo-restaurant" style="background-image: url('{!! asset('images/home/restaurant_3.svg') !!}');"></div>
                </div>
                <div class="full-width">
                    <div class="row">
                        <div class="col-xs-10 col-sm-10 col-md-10 padding-0">
                            <h4>Bakery Cooking</h4>
                            <p>broodjes, wafels, desserts</p>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 delivery-status">
                            <p>3 km</p>
                            <p class="status">Open</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 padding-0">
                            <span class="time">
                                <img class="restaurant-icon" src="{!! url("/images/home/waiting-time-icon.svg") !!}"/>
                                <span>±20 min</span>
                            </span>
                            <span class="delivery">
                                <img class="restaurant-icon" src="{!! url("/images/home/motobike.svg") !!}"/>
                                <span>€2,00</span>
                            </span>
                            <span class="price">
                                <img class="restaurant-icon" src="{!! url("/images/home/money.svg") !!}"/>
                                <span class="min-title">Min</span>
                                <span>€20,00</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid order-type">
    <div class="row">
        <div class="col-md-7 col-left show-mobile mgb-20">
            <h2>{{trans('dashboard.choose_your_order_method')}}</h2>
            <p>{!! trans('dashboard.avoid_long_queues') !!}</p>
            <p>{!! trans('dashboard.order_as_a_company') !!}</p>
        </div>
        <div class="col-md-5 col-left open">
            <a class="choose-order btn btn-default dropdown-toggle">
                <i class="icon-people"></i>
                <span>{{trans('workspace.group_order')}}</span>
                <i class="icn-arrow-down"></i>
            </a>
            <div class="dropdown-menu">
                <h3>{{trans('dashboard.choose_your_order_method')}}</h3>
                <ul>
                    <li><i class="icon-choice"></i><span>{{trans('cart.success_afhalen_confirm')}}</span></li>
                    <li><i class="icon-choice"></i><span>{{trans('cart.success_levering')}}</span></li>
                    <li class="active"><i class="icon-choice"></i><span>{{trans('workspace.group_order')}}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-md-7 col-right show-pc">
            <h2>{{trans('dashboard.choose_your_order_method')}}</h2>
            <p>{!! trans('dashboard.avoid_long_queues') !!} {!! trans('dashboard.order_as_a_company') !!}</p>
        </div>
    </div>
</div>
<div class="container-fluid order-success">
    <div class="row">
        <div class="col-md-7 col-left col-for-ipad">
            <h2>{{trans('dashboard.place_your_order')}}</h2>
            <p>{!! trans('dashboard.choose_from_your_menu') !!} {!! trans('dashboard.no_more_hassle') !!}</p>
        </div>
        <div class="col-md-5 col-right col-for-ipad">
            <div class="row">
                <div class="col-md-8 text-center m-table-order mobile-right-0">
                    <img width="106" height="91" src="{{ asset('images/home/icon-success-order.svg') }}" />
                    <h4 class="title">{{trans('cart.success_title')}}</h4>
                    <div class="order-detail">
                        <h6 class="text-left">{{trans('cart.title_bestelde_artikelen')}}</h6>
                        <h4 class="yellow text-left">RAMEN ROSE</h4>
                        <div class="wp-table table-order text-left">
                            <div class="wrapForProduct">
                                <div class="row">
                                    <div class="col-md-6">Margherita</div>
                                    <div class="col-md-6 price">€7.50</div>
                                </div>
                                <div class="row extra">
                                    <div class="col-md-6 subtitle"> - extra kaas</div>
                                    <div class="col-md-6 price">€1.00</div>
                                </div>
                            </div>
                            <div class="wrapForProduct">
                                <div class="row">
                                    <div class="col-md-6">Coca-Cola</div>
                                    <div class="col-md-6 price">€2.50</div>
                                </div>
                            </div>
                            <div class="wrapSubTotal firstSubTotal">
                                <div class="row">
                                    <div class="col-md-6">{{trans('cart.subtotaal')}}:</div>
                                    <div class="col-md-6 price">€11.00</div>
                                </div>
                            </div>
                            <div class="wrapSubTotal">
                                <div class="row">
                                    <div class="col-md-6">{{trans('order.coupon_discount')}}:</div>
                                    <div class="col-md-6 price">- €6.00</div>
                                </div>
                            </div>
                            <div class="row-table total">
                                <div class="clearfix"></div>
                                <div class="total-cart-step1">
                                    <div class="col-left">
                                        <h6>{{trans('order.total')}}:</h6>
                                    </div>
                                    <div class="col-right">
                                        <h6>€<b class="totalPriceFinal">5.00</b></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row mgb-10">
                                <div class="col-sm-12 col-xs-12 text-center">
                                    <img src="{!! url('images/slide_dot.svg') !!}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="wrap-rectangle">
                        <div class="main-rectangular">
                            <div class="wrap-label">
                                <span class="label-even top-1">1</span>
                                <span class="label-even top-2">2</span>
                                <span class="label-even top-3">3</span>
                            </div>
                            <div class="active-rectangular top-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>