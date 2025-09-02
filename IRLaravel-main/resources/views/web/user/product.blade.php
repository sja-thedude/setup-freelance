<div class="col-md-8 pc-product">
    @if(!empty($products))
        <div class="grid-meal float-right mb-18 order">
            <div class="selection float-right">
                <span>@lang('frontend.sort')</span>
                @php
                    if (!isset($favourite)) {
                        if (request()->segment(2) == "favourite") {
                            $favourite = "favourite";
                        } else {
                            $favourite = null;
                        }
                    }
                @endphp
                <ul class="order-sub-menu sort-order">
                    <li>
                        <a data-order="1" data-url="{!! route($guard.'.product') !!}?{{$favourite}}" class="@if($orderType == 1)actived @endif" href="javascript:;">
                            @lang('frontend.sort_price_desc')
                        </a>
                    </li>
                    <li>
                        <a data-order="2" data-url="{!! route($guard.'.product') !!}?{{$favourite}}" class="@if($orderType == 2)actived @endif" href="javascript:;">
                            @lang('frontend.sort_price_asc')
                        </a>
                    </li>
                    <li>
                        <a data-order="3" data-url="{!! route($guard.'.product') !!}?{{$favourite}}" class="@if($orderType == 3)actived @endif " href="javascript:;">
                            @lang('frontend.sort_name_asc')
                        </a>
                    </li>
                </ul>
                <i class="icon-angle-down"></i>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="grid-meal">
            <div class="wrap-meal">
                @php $i = 0; @endphp
                @foreach($products as $product)
                    @php $i++; @endphp
                    @include('web.user.partials.product-item', ['i' => $i, 'product' => $product])
                @endforeach
            </div>
        </div>
        <div class="clearfix"></div>
    @else
        <div class="wrap-product no-product">
            <span class="text-center">
                @if(!empty($noProductFound))
                    {{$noProductFound}}
                @else
                    @lang('frontend.no_items_found')
                @endif
            </span>
        </div>
    @endif
</div>

<div class="col-md-8 mobile-product">
    @if(!empty($categories) && empty($favourite))
        <div class="grid-meal">
            <div class="wrap-meal">
                @php $i = 0; @endphp
                @foreach($categories as $key => $category)
                    <div id="ct-{{$category['id']}}" class="category-item">
                        <h3 data-index="{{$key}}"
                            data-id="{{$category['id']}}" 
                            id="category-{{$category['id']}}" 
                            class="mobile-category-title"
                            data-current="{{(!empty(request()->segments()[2]) && request()->segments()[2] == $category['id']) ? 'current' :'' }}"
                            >
                            <span class="underline-category">{{$category['name']}}</span>
                            </h3>
                    
                        @if(!empty($category['products']))
                            @foreach($category['products'] as $product)
                                @php $i++; @endphp
                                @include('web.user.partials.product-item', ['i' => $i, 'product' => $product])
                            @endforeach
                        @else
                            <div class="wrap-product no-product">
                                <span class="text-center">
                                    @lang('frontend.no_items_found')
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="clearfix"></div>
    @elseif(!empty($products) && !empty($favourite))
    <div class="grid-meal">
        <div class="wrap-meal">
            @php $i = 0; @endphp
            @foreach($products as $product)
                @php $i++; @endphp
                @include('web.user.partials.product-item', ['i' => $i, 'product' => $product])
            @endforeach
        </div>
    </div>
    @else 
        <div class="wrap-product no-product">
            <span class="text-center">
                @lang('frontend.no_items_found')
            </span>
        </div>
    @endif
</div>