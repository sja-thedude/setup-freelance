<div class="user-modal hidden"  style="z-index:10">
    <div class="bg"></div>
    @php
        $isOpendTabTakeOut  = $webWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_TAKEOUT)->where('active', 1)->first();
        $isOpendTabLevering = $webWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_DELIVERY)->where('active', 1)->first();
        $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
    @endphp
    @if ($isOpendTabTakeOut || $isOpendTabLevering)
        {!! Form::open(['route' => ['web.user.storeCart'], 'files' => true, 'class' => 'create-cart']) !!}
    @else
        <form method="POST" action="javascript:;" accept-charset="UTF-8" class="create-cart" enctype="multipart/form-data">
    @endif
        <input type="hidden" name="workspace_id" id="workspace_id" value="{{$workspace->id}}">
        <input type="hidden" name="category_id" id="category_id" value="{{$category->id}}">
        <input type="hidden" name="product_id" id="product_id" value="{{$product['id']}}">
        <input type="hidden" name="user_id" id="user_id" value="{{!auth()->guest() ? auth()->user()->id : null}}">
        <div class="check-modal-container">
            @php
                $styleBackground = $secondColor . " url('/assets/images/bg.png') no-repeat";
                $circleIcon = "icn-times-circle-white";
                $hideBgMobile = "hide-bg-mobile";
                $productPhotoPath = null;
                $countOptions = !empty($options) ? count($options) : null;

                if(!empty($product['photo_path'])) {
                    $productPhotoPath = Picture::get(Picture::getImageFolder($product['photo_path']), 'orig', Picture::getImageName($product['photo_path']), null, 'sf', 'c', false, null);
                }

                if (empty($productPhotoPath) && !empty($product['photo'])) {
                    $productPhotoFullPath = Helper::getStoragePathFromUrl($product['photo']);
                    $productPhotoPath = Picture::get(Picture::getImageFolder($productPhotoFullPath), 'orig', Picture::getImageName($productPhotoFullPath), null, 'sf', 'c', false, null);
                }

                if (!empty($productPhotoPath)) {
                    $styleBackground = "url('" . $productPhotoPath . "') 50% no-repeat";
                    $circleIcon = "icn-times-circle";
                    $hideBgMobile = "";
                }
            @endphp
            <div class="check-image {{ $hideBgMobile }} {{empty($product['photo']) ? 'check-image-28' : null}}" style="background: {{$styleBackground}}; {{!empty($product['photo']) ? 'background-size: 100%' : null}};
                    height: {{empty($product['photo']) ? '115px' : '160px'}};
                    ">
                <a href="javascript:;" class="close-product-detail" data-dismiss="popup" data-target=".user-modal">
                    <i class="{!! $circleIcon !!}"></i>
                </a>
            </div>
            <div class="modal-content check-modal" @if($countOptions == 0) style="height: auto;" @endif>
                <div class="check-content" id="check-content" @if($countOptions == 0) style="height: auto;" @endif>
                    <div id="check-content-child">
                        <div class="check-container row-check-container">
                            <div class="left title-container mb-10">
                                <h5 class="check-title">{{$product['name']}}</h5>
                                
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
                            <div class="text-right allergenens-web">
                                @if(!empty($product['allergenens']))
                                    @foreach($product['allergenens'] as $allergenen)
                                        <a href="javascript:;">
                                            <img style="width: 40px; height: 40px" src="{{str_replace("gray","hover", url($allergenen['icon']))}}">
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @if(!empty($product['description']))
                            <div class="row check-container mb-10">
                                <div class="col-md-12">
                                    <span class="check-sub">
                                    @php
                                        $description = explode("\n",$product['description']);
                                        if(is_array($description)){
                                            foreach($description as $value){
                                                echo $value . "<br />";
                                            }
                                            }
                                    @endphp
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="row check-container allergenens-mobile {{ !empty($product['allergenens']) && count($product['allergenens']) > 0 ? 'mb-10' : '' }}">
                            <div class="col-md-12">
                                <div class="">
                                    @if(!empty($product['allergenens']))
                                        @foreach($product['allergenens'] as $allergenen)
                                            <a href="javascript:;">
                                                <img style="width: 40px; height: 40px" src="{{str_replace("gray","hover", url($allergenen['icon']))}}">
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if(!$product['labels']->where('type', '!=', 0)->isEmpty())
                        <div class="row check-container mb-10">
                            <div class="col-md-12">
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
                            </div>
                        </div>
                        @endif
                        
                        @if($countOptions == 0) 
                            <div style="height: 20px"></div>
                        @endif
                        @foreach($options as $option)
                            <div class="check-container mb-5 check-content-{{$option['id']}} row-check-container">
                                <div class="check-content">
                                    <span><strong>{{$option['name']}}</strong></span>
                                    <span class="text-option">
                                        @if($option['type'] === 1)
                                            (@lang('frontend.valid'))
                                        @elseif($option['type'] === 0)
                                            (@lang('frontend.optional'))
                                        @endif
                                    </span>
                                </div>
                                <div class="price-container">
                                    <span class="error-check error-{{$option['id']}}"></span>
                                    <span class="price" style="display: none">
                                        <strong class="display-flex">
                                            <span class="currency_{{$option['id']}}">€</span>
                                            <span class="row-price price_{{$option['id']}}">{{\App\Helpers\Helper::formatPrice(0)}}</span>
                                        </strong>
                                    </span>
                                </div>
                            </div>
                            @if(!empty($option['items']))
                                @php
                                    $count = count($option['items']);
                                @endphp
                                @if($count > 12)
                                    <div class="wrap-html">
                                        <div class="wrap-html-content no-padding">
                                            @endif
                                            <div class="row check-container">
                                                <div class="col-md-12">
                                                    <div class="wrap-container">
                                                        @foreach($option['items'] as $item)
                                                            <div class="wrap-selection">
                                                                <input type="checkbox" id="check{{$item['id']}}" name="cartOptionItem[{{ $item['id'] }}]" 
                                                                class="option-choice {{!empty($option['is_ingredient_deletion']) ? 'check-selection-strike' : 'check-selection'}} option-{{$option['id']}} item-{{$item['id']}}" 
                                                                data-currency="{{$item['currency']}}" 
                                                                data-price="{{$item['price']}}" 
                                                                data-item="{{$item['id']}}" 
                                                                data-product="{{$product['id']}}" 
                                                                data-min="{{$option['min']}}" 
                                                                data-max="{{$option['max']}}" 
                                                                data-option="{{$option['id']}}" 
                                                                data-master="{{!empty($item['master']) ? 'true' : 'false'}}"
                                                                value="{{json_encode($item)}}">
                                                                <label for="check{{$item['id']}}" class="check-text {{!empty($option['is_ingredient_deletion']) ? 'check-text-discount' : 'check-text-brown'}} label-item-{{$item['id']}} uncheck" 
                                                                    @if($option['is_ingredient_deletion']) data-delete="1" @else data-delete="0" @endif 
                                                                    data-currency="{{$item['currency']}}" 
                                                                    data-price="{{$item['price']}}" 
                                                                    data-item="{{$item['id']}}" 
                                                                    data-product="{{$product['id']}}" 
                                                                    data-min="{{$option['min']}}" 
                                                                    data-max="{{$option['max']}}"
                                                                    data-option="{{$option['id']}}" data-select-all="{{!empty($item['master']) ? 'true' : 'false'}}">{{$item['name']}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @if($count > 12)
                                        </div>
                                        <div class="row check-container wrap-html-bottom">
                                            <strong class="check-content-collapse">Meer bekijken
                                                <i class="icon-chevron-down"></i>
                                            </strong>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <div class="row check-button-container">
                    <div class="minute-content">
                        <span class="minus"><i class="icon-minus"></i></span>
                        <input name="total_number" type="text" value="1" class="qty">
                        <span class="plus"><i class="icon-plus"></i></span>
                    </div>
                    <a href="javascript:;" class=" btn-pr-custom btn btn-modal btn-check submit-cart" @if (!$isOpendTabTakeOut && !$isOpendTabLevering) disabled @endif>
                        <i class="icn-shopping-bag icon-case"></i>
                        <strong class="font-size-24">€<span class="total-price total_{{$product['id']}}" data-price="{{$product['price']}}">{{$product['price']}}</span></strong>
                        <input type="hidden" class="get-total-price" value="{{$product['price']}}">
                    </a>
                </div>
            </div>
        </div>
    {{Form::close()}}
</div>
<script>
    $( document ).ready(function() {
        var clientHeight = document.getElementById('check-content-child').clientHeight
        var parent = document.getElementById('check-content').clientHeight
        if(clientHeight < parent) {
            $('.check-modal').css('height', 'auto');
        }
    })
</script>