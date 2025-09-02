@extends('layouts.web-home-new')
@section('content')
    <div id="map"></div>
    <div class="back-button-maps">
        <a href="javascript:;" class="back-button">{{trans('dashboard.back_to_listview')}}</a>
    </div>
    <div class="main-search-content">
        <div class="container-fluid type-zaak">
            <div class="col-md-11 col-xs-10 margin-top-10">
                <div class="wrap-search display-none">
                    <div class="owl-search-type">
                        <div class="active-type active">
                            <a href="#">
                                {{trans('dashboard.all')}}
                            </a>
                        </div>
                        @if($listRestaurantCategory)
                            @foreach($listRestaurantCategory as $restaurantCategory)
                                <div class="active-type">
                                    <a href="javascript:;" data-id="{{$restaurantCategory->id}}">
                                        {{$restaurantCategory->name}}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-1 col-xs-2 margin-top-10">
                <a href="javascript:;" class="show-map" data-url="{{route('web.marker.detail')}}" data-markericon="{{url('/assets/images/marker.svg')}}">
                    <svg width="24" height="30" viewBox="0 0 24 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.0222 12.5852C22.0222 20.8182 12.0111 27.875 12.0111 27.875C12.0111 27.875 2 20.8182 2 12.5852C2 9.77785 3.05474 7.08546 4.93219 5.10034C6.80963 3.11523 9.356 2 12.0111 2C14.6662 2 17.2126 3.11523 19.09 5.10034C20.9675 7.08546 22.0222 9.77785 22.0222 12.5852Z" stroke="#B5B268" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.7303 14.9712C13.2382 14.9712 14.4606 13.6345 14.4606 11.9856C14.4606 10.3367 13.2382 9 11.7303 9C10.2224 9 9 10.3367 9 11.9856C9 13.6345 10.2224 14.9712 11.7303 14.9712Z" stroke="#B5B268" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="container-fluid main-search">
            <div class="col-md-3 col-xs-12 col-search-left">
                <div class="choose-type">
                    <a class="choose-order btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <svg class="i-people" width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.8889 20V18.4167C18.8889 17.5768 18.4177 16.7714 17.579 16.1775C16.7403 15.5836 15.6028 15.25 14.4167 15.25H5.47222C4.28612 15.25 3.14859 15.5836 2.30988 16.1775C1.47118 16.7714 1 17.5768 1 18.4167V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.94466 8.125C12.0618 8.125 13.778 6.53001 13.778 4.5625C13.778 2.59499 12.0618 1 9.94466 1C7.82757 1 6.11133 2.59499 6.11133 4.5625C6.11133 6.53001 7.82757 8.125 9.94466 8.125Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23.9999 20V18.3816C23.9993 17.6644 23.748 16.9677 23.2855 16.4009C22.8229 15.8341 22.1753 15.4293 21.4443 15.25" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.3335 1C17.0645 1.20253 17.7125 1.66258 18.1752 2.30761C18.6379 2.95264 18.8891 3.74596 18.8891 4.5625C18.8891 5.37904 18.6379 6.17236 18.1752 6.81739C17.7125 7.46242 17.0645 7.92247 16.3335 8.125" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{trans('cart.success_afhalen_confirm')}}</span>
                        <svg class="i-arrow-down" width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L7 7L13 1" stroke="#fff" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <div class="dropdown-menu">
                        <h3>{{trans('dashboard.choose_your_order_method')}}</h3>
                        <ul>
                            <li class="active"><i class="icon-choice"></i><a href="javascript:;" data-type="takeout">{{trans('cart.success_afhalen_confirm')}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="levering">{{trans('cart.success_levering')}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="group">{{trans('workspace.group_order')}}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="filter-pane levering" style="display: none">
                    <h4>{{trans('dashboard.order_by')}}</h4>
                    <a class="choose-order-by btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span>{{trans('dashboard.distance') . ' (' . trans('dashboard.standard') . ')'}}</span>
                        <i class="icn-arrow-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <h3>{{trans('dashboard.order_by')}}</h3>
                        <ul>
                            <li class="active"><i class="icon-choice"></i><a href="javascript:;" data-type="distance">{{trans('dashboard.distance') . ' (' . trans('dashboard.standard') . ')'}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="minimum_amount">{{trans('dashboard.minimum_amount')}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="delivery_cost">{{trans('dashboard.delivery_cost')}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="minimum_waiting_time">{{trans('dashboard.minimum_waiting_time')}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="name">{{trans('coupon.naam')}}</a></li>
                        </ul>
                    </div>
                    <div class="search-restaurant">
                        <svg class="search-logo" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.7885 19.7272C15.1218 19.7272 18.6347 16.1568 18.6347 11.7525C18.6347 7.34821 15.1218 3.77783 10.7885 3.77783C6.45522 3.77783 2.94238 7.34821 2.94238 11.7525C2.94238 16.1568 6.45522 19.7272 10.7885 19.7272Z" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.5959 21.7205L16.3296 17.3843" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {!! Form::text('restaurant-name', NULL, [
                            'class'=>'form-control restaurant-search',
                            'placeholder'=> trans('dashboard.search_by_dealer'),
                        ]) !!}
                    </div>
                    <div class="minimum-order-amount">
                        <h4>{{trans('dashboard.minimum_order_amount')}}</h4>
                        <div class="active">
                            <input type="radio" class="flat checkbox" name="minimum_order_amount" value="" checked style="border: none;"/>
                            <span>{{trans('dashboard.no_preference')}}</span>
                        </div>
                        <div>
                            <input type="radio" class="flat checkbox" name="minimum_order_amount" value="10" style="border: none;"/>
                            <span>{{trans('dashboard.10_of_minder')}}</span>
                        </div>
                        <div class="mg-bottom-15">
                            <input type="radio" class="flat checkbox" name="minimum_order_amount" value="20" style="border: none;"/>
                            <span>{{trans('dashboard.20_of_minder')}}</span>
                        </div>
                    </div>
                    <div class="delivery-charge">
                        <h4>{{trans('dashboard.delivery_charge')}}</h4>
                        <div class="active">
                            <input type="radio" class="flat checkbox" name="delivery_charge" value="" checked style="border: none;"/>
                            <span>{{trans('dashboard.no_preference')}}</span>
                        </div>
                        <div>
                            <input type="radio" class="flat checkbox" name="delivery_charge" value="0" style="border: none;"/>
                            <span>{{trans('dashboard.gratis')}}</span>
                        </div>
                        <div>
                            <input type="radio" class="flat checkbox" name="delivery_charge" value="2.5" style="border: none;"/>
                            <span>{{trans('dashboard.2_5_of_minder')}}</span>
                        </div>
                        <div class="mg-bottom-15">
                            <input type="radio" class="flat checkbox" name="delivery_charge" value="4.5" style="border: none;"/>
                            <span>{{trans('dashboard.4_5_of_minder')}}</span>
                        </div>
                    </div>
                    <div class="wrap-checkbox-loyalty">
                        <input type="checkbox" name="checkbox-loyalty" id="checkbox-1" class="checkbox-loyalty" style="border: none;">
                        <label for="checkbox-1">{{trans('dashboard.show_merchants_with_loyalty')}}</label>
                    </div>
                </div>

                <div class="filter-pane group" style="display: none">
                    <h4>{{trans('dashboard.order_by')}}</h4>
                    <a class="choose-order-by btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span>{{trans('coupon.naam') . ' (' . trans('dashboard.standard') . ')'}}</span>
                        <i class="icn-arrow-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <h3>{{trans('dashboard.order_by')}}</h3>
                        <ul>
                            <li class="active"><i class="icon-choice"></i><a href="javascript:;" data-type="name">{{trans('coupon.naam') . ' (' . trans('dashboard.standard') . ')'}}</a></li>
                        </ul>
                    </div>
                    <div class="search-restaurant">
                        <svg class="search-logo" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.7885 19.7272C15.1218 19.7272 18.6347 16.1568 18.6347 11.7525C18.6347 7.34821 15.1218 3.77783 10.7885 3.77783C6.45522 3.77783 2.94238 7.34821 2.94238 11.7525C2.94238 16.1568 6.45522 19.7272 10.7885 19.7272Z" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.5959 21.7205L16.3296 17.3843" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {!! Form::text('restaurant-name', NULL, [
                            'class'=>'form-control restaurant-search',
                            'placeholder'=> trans('dashboard.search_by_dealer'),
                            ]) !!}
                    </div>
                    <div class="wrap-checkbox-loyalty">
                        <input type="checkbox" name="checkbox-loyalty" id="checkbox-1" class="checkbox-loyalty" style="border: none;">
                        <label for="checkbox-1">{{trans('dashboard.show_merchants_with_loyalty')}}</label>
                    </div>
                </div>

                <div class="filter-pane takeout">
                    <h4>{{trans('dashboard.order_by')}}</h4>
                    <a class="choose-order-by btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span>{{trans('cart.success_afhalen_confirm') . ' (' . trans('dashboard.standard') . ')'}}</span>
                        <i class="icn-arrow-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <h3>{{trans('dashboard.order_by')}}</h3>
                        <ul>
                            <li class="active"><i class="icon-choice"></i><a href="javascript:;" data-type="distance">{{trans('dashboard.distance') . ' (' . trans('dashboard.standard') . ')'}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="minimum_waiting_time">{{trans('dashboard.minimum_waiting_time')}}</a></li>
                            <li><i class="icon-choice"></i><a href="javascript:;" data-type="name">{{trans('coupon.naam')}}</a></li>
                        </ul>
                    </div>
                    <div class="search-restaurant">
                        <svg class="search-logo" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.7885 19.7272C15.1218 19.7272 18.6347 16.1568 18.6347 11.7525C18.6347 7.34821 15.1218 3.77783 10.7885 3.77783C6.45522 3.77783 2.94238 7.34821 2.94238 11.7525C2.94238 16.1568 6.45522 19.7272 10.7885 19.7272Z" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.5959 21.7205L16.3296 17.3843" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {!! Form::text('restaurant-name', NULL, [
                            'class'=>'form-control restaurant-search',
                            'placeholder'=> trans('dashboard.search_by_dealer'),
                            ]) !!}
                    </div>
                    <div class="wrap-checkbox-loyalty">
                        <input type="checkbox" name="checkbox-loyalty" id="checkbox-1" class="checkbox-loyalty" style="border: none;">
                        <label for="checkbox-1">{{trans('dashboard.show_merchants_with_loyalty')}}</label>
                    </div>
                </div>
            </div>
            <div class="col-md-1 col-xs-12 col-search-offset"></div>
            <div class="search-results col-md-8 col-xs-12">
                @include('web.restaurants.partials.restaurants')
            </div>

            <input type="hidden" name="search-restaurant-url" id="search-restaurant-url" value="{{route('web.search_restaurant')}}">
            <input type="hidden" name="current-type" id="current-type" value="{{$currentType}}">
            <input type="hidden" name="order-by" id="order-by" value="">
            <input type="hidden" name="isLoyalty" id="is_loyalty" value="">
            <input type="hidden" name="restaurant-name" id="restaurant_name" value="">
            <input type="hidden" name="category_id" id="category_id" value="">
        </div>
    </div>
@endsection