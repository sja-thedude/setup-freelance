<div class="item-meal pro-show-detail" data-locale="{{$locale}}"
     data-id="{{$product['id']}}"
     data-url="{!! route($guard.'.product.detail') !!}"
     data-toggle="popup"
     @if ($cart && $cart->group_id)
        data-allow=""
     @else
        data-allow="{{ $timeslotDetailsSet ? "" : trans('cart.het_is_momenteel_niet', ['workspace' => $webWorkspace->name]) }}"
     @endif
     data-target="#product-detail"
>
    <div class="wrap-item-content @php echo !empty($currentCategory)?\App\Helpers\Helper::buildClassIfHasIcon($currentCategory):'' @endphp @if(is_null($product['photo'])) no-product-image @endif">
        <div class="wrap-contentx">
            <div class="content-left {!! !is_null($product['photo']) ? 'width-50' : '' !!}">
                <div class="ct-head">
                    <h6>
                        {{$product['name']}}
                        @if(!$product['allergenens']->isEmpty())
                            @php
                                $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
                            @endphp
                            <i class="icn-information-o">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9.5" stroke="{{$secondColor}}"/>
                                <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="{{$secondColor}}"/>
                                </svg>
                            </i>
                        @endif
                    </h6>
                    @if(!$product['allergenens']->isEmpty())
                        <div class="wrap-info no-clickable">
                            @foreach($product['allergenens'] as $allergenen)
                                <a href="javascript:;" class="no-clickable"><img class="no-clickable" style="width: 40px; height: 40px" src="{{str_replace("gray","hover", url($allergenen['icon']))}}"></a>
                            @endforeach
                        </div>
                    @endif
                    @if(!is_null($product['photo']))
                        <div class="favoriet-kokette">
                            @if(!empty($product['category']['favoriet_friet']))
                                <i class="icn-active-menu-friet"></i>
                            @endif
                            @if(!empty($product['category']['kokette_kroket']))
                                <i class="icn-active-menu-kroket"></i>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="ct-desc">
                    @if(!empty($product['description']))      
                        <label>
                        @php
                            //$description = explode("\n",$product['description']);
                            /*if(is_array($description)){
                                  foreach($description as $value){
                                    echo $value . "<br />";
                                  }
                                }*/
                            echo str_limit($product['description'], 100, '...')
                        @endphp
                        </label>
                    @endif
                    
                </div>
            </div>
            <div class="content-right {!! !is_null($product['photo']) ? 'width-50' : '' !!}">
                @php
                    $productPhotoPath = null;

                    if (!empty($product['photo_path'])) {
                        $productPhotoPath = Picture::get(Picture::getImageFolder($product['photo_path']), '160x120', Picture::getImageName($product['photo_path']), null, 'c', 'c', false, null);
                    } else {
                        if (!empty($product['photo'])) {
                            $productPhotoFullPath = Helper::getStoragePathFromUrl($product['photo']);
                            $productPhotoPath = Picture::get(Picture::getImageFolder($productPhotoFullPath), '160x120', Picture::getImageName($productPhotoFullPath), null, 'c', 'c', false, null);
                        }
                    }
                @endphp
                @if(!is_null($productPhotoPath))
                    <div class="wrap-image">
                        <a href="javascript:;" class="bd-image" data-locale="{{$locale}}"
                        data-id="{{$product['id']}}"
                            @if ($cart && $cart->group_id)
                                data-allow=""
                            @else
                                data-allow="{{ $timeslotDetailsSet ? "" : trans('cart.het_is_momenteel_niet', ['workspace' => $webWorkspace->name]) }}"
                            @endif
                            data-url="{!! route($guard.'.product.detail') !!}"
                        >
                            <img src="{{ $productPhotoPath }}" alt="meal" style="min-height:97px" loading="lazy">
                        </a>
                    </div>
                @else
                    <div class="wrap-imagex">
                        <div class="favoriet-kokette">
                            @if(!empty($product['category']['favoriet_friet']))
                                <i class="icn-active-menu-friet"></i>
                            @endif
                            @if(!empty($product['category']['kokette_kroket']))
                                <i class="icn-active-menu-kroket"></i>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="wrap-bottom">
            <div class="row">
                <div class="col-md-9">
                    @foreach($product['labels'] as $label)
                        @if($label['type_display'] === 'NEWW')
                            <span class="attr-meal attr-new">@lang('frontend.new')</span>

                        @elseif($label['type_display'] === 'SPICY')
                            <span class="attr-meal attr-spicy">
                                <svg width="11" height="12" viewBox="0 0 390 301" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M85.7014 264.775C58.8092 274.81 46.7014 277.775 12.2014 264.775C-22.2985 251.775 18.7015 300.775 105.701 300.775C192.701 300.775 235.428 273.074 268.701 207.775C288.701 168.525 308.201 140.275 340.121 129.088C351.319 125.163 373.201 98.7752 342.202 98.7752C339.336 98.7752 335.972 100.29 332.731 101.75C325.947 104.806 319.702 107.618 319.702 95.7752C319.702 92.7123 323.134 88.1301 326.744 83.3114C334.069 73.5326 342.124 62.7801 323.702 61.7752C321.477 61.6539 320.029 61.5613 318.613 61.7345C314.988 62.178 311.576 64.3644 295.891 72.2752C236.119 102.42 211.857 126.025 184.201 177.775C162.473 218.434 128.892 248.658 85.7014 264.775ZM277.149 100.117C248.648 106.124 215.35 148.912 212.249 159.318C209.366 168.991 217.121 177.615 221.95 169.412C223.952 166.011 225.762 162.877 227.444 159.965C243.32 132.476 247.754 124.797 294.049 100.117C298.873 97.5448 282.498 98.9893 277.149 100.117Z" fill="#ffffff"/>
                                    <path d="M377.701 12.7753C387.522 -2.90287 394.628 -4.69333 386.288 10.4291C377.947 25.5515 379.816 58.498 378.912 63.7355L370.144 56.0245C374.5 53 367.881 28.4534 377.701 12.7753Z" fill="#ffffff"/>
                                    <path d="M370.144 56.0245L378.912 63.7355C378.912 63.7355 377.033 88.4231 372.671 102.896C370.803 109.093 365.452 80.4169 351.416 75.0712C339.245 72.6912 328.776 43.2199 344.192 55.8277C359.607 68.4356 361.557 56.5458 370.144 56.0245Z" fill="#ffffff"/>
                                </svg>
                                @lang('frontend.spicy')
                            </span>

                        @elseif($label['type_display'] === 'VEGGIE')
                            <span class="attr-meal attr-veggie">
                                <svg width="11" height="12" viewBox="0 0 236 270" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M93.8027 233.232L152.5 106L160.746 111.504L106.286 263.375C104.861 267.349 101.094 270 96.8726 270H91.0128C86.4477 270 82.4619 266.919 81.2901 262.507C75.8904 242.176 60.2507 184.956 45.5 147C32.6475 113.929 9.90289 68.2928 0.382654 49.5544C-0.950608 46.9302 1.63006 44.5737 3.9759 46.3517C15.2704 54.9119 36.45 74.6046 53.5195 111.504C78.0391 164.508 93.8027 233.232 93.8027 233.232Z" fill="#ffffff"/>
                                    <path d="M226.756 36.5959C233.081 14.5973 239.454 -7.56595 231.338 2.49749C226.526 8.46442 212.058 13.1047 195.437 18.4354C168.882 26.952 136.833 37.2309 129.889 57.4975C123.089 77.3451 135.404 93.2378 146.275 102.739C161.739 81.2789 168.466 67.7619 172.574 59.5068C175.846 52.9301 177.457 49.6933 180.5 48.4403C185.65 46.3195 168.049 79.1291 152.5 106C157.744 109.5 160.746 111.504 160.746 111.504C160.746 111.504 189.338 112.949 201.099 103.449C212.86 93.9491 215.709 86.0459 218.838 66.9975C219.936 60.3164 223.339 48.4801 226.756 36.5959Z" fill="#ffffff"/>
                                </svg>
                                @lang('frontend.veggie')
                            </span>

                        @elseif($label['type_display'] === 'PROMO')
                            <span class="attr-meal attr-new">@lang('frontend.promo')</span>

                        @elseif($label['type_display'] === 'VEGAN')
                            <span class="attr-meal attr-veggie">
                                <svg width="11" height="12" viewBox="0 0 236 270" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M93.8027 233.232L152.5 106L160.746 111.504L106.286 263.375C104.861 267.349 101.094 270 96.8726 270H91.0128C86.4477 270 82.4619 266.919 81.2901 262.507C75.8904 242.176 60.2507 184.956 45.5 147C32.6475 113.929 9.90289 68.2928 0.382654 49.5544C-0.950608 46.9302 1.63006 44.5737 3.9759 46.3517C15.2704 54.9119 36.45 74.6046 53.5195 111.504C78.0391 164.508 93.8027 233.232 93.8027 233.232Z" fill="#ffffff"/>
                                    <path d="M226.756 36.5959C233.081 14.5973 239.454 -7.56595 231.338 2.49749C226.526 8.46442 212.058 13.1047 195.437 18.4354C168.882 26.952 136.833 37.2309 129.889 57.4975C123.089 77.3451 135.404 93.2378 146.275 102.739C161.739 81.2789 168.466 67.7619 172.574 59.5068C175.846 52.9301 177.457 49.6933 180.5 48.4403C185.65 46.3195 168.049 79.1291 152.5 106C157.744 109.5 160.746 111.504 160.746 111.504C160.746 111.504 189.338 112.949 201.099 103.449C212.86 93.9491 215.709 86.0459 218.838 66.9975C219.936 60.3164 223.339 48.4801 226.756 36.5959Z" fill="#ffffff"/>
                                </svg>
                                @lang('frontend.vegan')
                            </span>
                        @else
                        @endif
                    @endforeach
                    
                    @if(!auth()->guest())
                        <a href="javascript:;" data-url="{!! route($guard.'.favourite.show', [$product['id']]) !!}" 
                        class="favorite pro-favorite">
                            @if($product['productFavorites']->where('id', auth()->user()->id)->count() > 0)
                                <i class="icon-heart"></i>
                            @else
                                <i class="icon-heart-o"></i>
                            @endif
                        </a>
                    @endif
                </div>
                <div class="col-md-3">
                    <span class="price" style="color: {{$generalSet['second_color']}}">€{{\App\Helpers\Helper::formatPrice($product['price'])}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if($i % 2 == 0): ?>
    <div class="clearfix hidden-xs"></div>
<?php endif?>