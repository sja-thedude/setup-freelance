@if ((request()->has('step') && request()->get('step') === "1") || !request()->has('step'))

    <input type="hidden" name="step" value="1">
    <input type="hidden" name="redeemId" value="{{ $redeemId }}">
    <input type="hidden" name="coupon_id" value="{{ $cart->coupon_id }}" />
    @if (!$isSuccessPage)
        <input type="hidden" name="coupon_percentage" value="{{ \App\Helpers\Helper::getPercentage($cart->coupon_id) }}" />
        <input type="hidden" name="redeem_percentage" value="{{ \App\Helpers\Helper::getPercentage($redeemId, \App\Models\Cart::REDEEM) }}" />
        <input type="hidden" name="group_percentage" value="{{ \App\Helpers\Helper::getPercentage($cart->group_id, \App\Models\Cart::GROUP) }}" />
    @endif
    <input type="hidden" name="groupId" value="{{ $cart->group_id }}" />
    <input type="hidden" name="coupon_discount" value="{{ $couponDiscount }}" />
    <input type="hidden" name="totalCouponDiscount" value="{{ !empty($totalCouponDiscount)?$totalCouponDiscount:0 }}"/>
    <input type="hidden" name="discountProducts" value="{{ !empty($discountProducts)?json_encode($discountProducts):'' }}"/>
    <input type="hidden" name="group_discount" value="{{ !empty($groupDiscount)?$groupDiscount:0 }}"/>

    <div class="shopping-cart {{$isSuccessPage ? 'success-page' : ''}}">
        @if ($isSuccessPage)
            <h6 class="title-success">@lang('cart.title_bestelde_artikelen')</h6>
            <input type="hidden" name="tab"
                   value="{{ $cart->type == \App\Models\Cart::TYPE_LEVERING
                        ? \App\Models\Cart::TAB_LEVERING
                        : \App\Models\Cart::TAB_TAKEOUT }}"
            />
        @else
            <h6>@lang('cart.title_winkelmand')</h6>
        @endif

        <div class="wp-table table-order">
            @foreach($listItem as $kk => $item)
                @php
                    $product           = $item->product;
                    $objTrans          = $product->translate(app()->getLocale());
                    $nameProduct       = $objTrans ? $objTrans->name : $product->translate('en')->name;
                    $categoryOfProduct = $product->category;

                    if ($isSuccessPage) {
                        $metas             = json_decode($item->metas);
                        $product           = $metas->product;
                        $categoryOfProduct = $metas->category;
                        $nameProduct       = $product->name;
                    }

                    $htmlOptions          = "";
                    $productOptions       = $item->cartOptionItems ?? $item->optionItems;
                    $groupCartOptionItems = $productOptions->groupBy('optie_id');
                    $totalPriceUnit       = $product->price;
                    $categoryIsDelivery   = $categoryOfProduct->available_delivery;

                    foreach($groupCartOptionItems as $optId => $cartOptionItems) {
                        if (!empty($failedOpties[$product->id]) && in_array($optId, $failedOpties[$product->id])) {
                            continue;
                        }

                        $options          = collect();
                        $opt              = NULL;
                        $originOptionItem = array();

                        foreach($cartOptionItems as $optionItem) {
                            $optIt = $optionItem->optionItem;
                            $opt   = $optionItem->option;

                            if ($isSuccessPage) {
                                $metas         = json_decode($optionItem->metas);
                                $optIt         = new \App\Models\CartOptionItem();
                                $optIt->master = $metas->option_item->master;
                                $optIt->price  = $metas->option_item->price;
                                $optIt->name   = $metas->option_item->name;
                                $opt           = isset($metas->option[0]) ? $metas->option[0] : NULL;
                            } else {
                                $optionItem = $optionItem->toArray();
                                unset($optionItem['option_item']);
                                unset($optionItem['option']);
                                unset($optionItem['created_at']);
                                unset($optionItem['updated_at']);
                                $originOptionItem[] = $optionItem;
                            }

                            $options->push($optIt);
                        }

                        $nameOption = "";
                        $isMaster   = $options->where('master', true)->first();

                        if ($opt && $opt->is_ingredient_deletion) {
                            $nameOption .= trans('cart.txt_zonder');
                        }

                        if ($isMaster) {
                            $priceOption = $isMaster->price;
                            $nameOption .= $isMaster->name;
                        } else {
                            $priceOption = $options->sum('price');
                            $nameOption .= implode(', ', $options->pluck('name')->toArray());
                        }

                        $totalPriceUnit += $priceOption;

                        $htmlOptions .= view('web.carts.partials.item-option-cart', [
                            'numberProduct'   => $item->total_number,
                            'nameOptionItem'  => $nameOption,
                            'priceOptionItem' => $priceOption,
                            'isSuccessPage'   => $isSuccessPage,
                            'optId'           => $optId . $kk,
                            'cartOptionItems' => json_encode($originOptionItem),
                        ])->render();
                    }
                @endphp

                {{-- Display on view cart--}}
                <div class="wrapForProduct">
                    <input type="hidden" class="listProductInCart" name="listProductInCart[]" value="{{ $product->id }}"/>
                    <div class="row-table {{($isSuccessPage && $kk == 0) ? 'border-top-none' : '' }}
                        {{ in_array($product->id, $idsProductFail ?? []) || (request()->has('tab') && request()->get('tab') === \App\Models\Cart::TAB_LEVERING && !$categoryIsDelivery) || !$product->active
                       ||   (!\App\Helpers\Helper::isInGroupProducts($cart->group_id, $product)) ? 'row-error' : '' }}"
                    >
                        <div class="col-left display-flex">
                            @if($isSuccessPage)
                                <span class="total-number">{{ $item->total_number }} x </span>
                            @endif
                            <label>{{ $nameProduct }}</label>
                            <input type="hidden" name="priceUnitProduct" value="{{ $product->price }}"/>
                        </div>

                        <div class="col-right">
                            @if ($isSuccessPage)
                                <div class="minute-content hidden">
                                    <input type="hidden" name="cartItem[{{ $item->id }}][total_number]" value="{{ $item->total_number }}">
                                </div>
                            @else
                                <div class="minute-content">
                                    <span class="minus" data-route="{!! route($guard.'.cart.updateQuantity') !!}" data-cart-item-id="{!! $item->id !!}">
                                        <svg width="7" height="11" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.45801 5.5H5.54134" stroke="{{$generalSet['primary_color']}}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="cartItem[{{ $item->id }}][total_number]" value="{{ $item->total_number }}">
                                    <span class="plus" data-route="{!! route($guard.'.cart.updateQuantity') !!}" data-cart-item-id="{!! $item->id !!}">
                                        <svg width="8" height="9" viewBox="0 0 8 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 1.875V7.125" stroke="{{$generalSet['primary_color']}}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M1.66699 4.5H6.33366" stroke="{{$generalSet['primary_color']}}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                            @endif
                            <label class="price-currency" style="display: inline">€</label>
                            <label class="price product">
                                {{ number_format($item->total_number * $totalPriceUnit, 2) }}
                            </label>
                            @if (!$isSuccessPage)
                                <a href="javascript:;" class="delete" data-type="product">
                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.625 3.25H2.70833H11.375" stroke="{{$generalSet['primary_color']}}" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4.33301 3.25016V2.16683C4.33301 1.87951 4.44714 1.60396 4.65031 1.4008C4.85347 1.19763 5.12902 1.0835 5.41634 1.0835H7.58301C7.87033 1.0835 8.14588 1.19763 8.34904 1.4008C8.5522 1.60396 8.66634 1.87951 8.66634 2.16683V3.25016M10.2913 3.25016V10.8335C10.2913 11.1208 10.1772 11.3964 9.97404 11.5995C9.77088 11.8027 9.49533 11.9168 9.20801 11.9168H3.79134C3.50402 11.9168 3.22847 11.8027 3.02531 11.5995C2.82214 11.3964 2.70801 11.1208 2.70801 10.8335V3.25016H10.2913Z" stroke="{{$generalSet['primary_color']}}" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.41699 5.9585V9.2085" stroke="{{$generalSet['primary_color']}}" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M7.58301 5.9585V9.2085" stroke="{{$generalSet['primary_color']}}" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            @endif
                        </div>

                        <input type="hidden" name="cartItem[{{ $item->id }}][is_levering]" value="{{ $categoryOfProduct->available_delivery }}">
                        <input type="hidden" name="zero-field" value="{{ $item->category_id }}">
                        <input type="hidden" name="cartItem[{{ $item->id }}][category_id]" value="{{ $item->category_id }}">
                        <input type="hidden" name="cartItem[{{ $item->id }}][product_id]" value="{{ $item->product_id }}">
                        <input type="hidden" name="cartItem[{{ $item->id }}][type]" value="{{ $item->type }}">
                    </div>
                    {!! $htmlOptions !!}
                </div>

                @php $totalPrice += $item->total_number * $totalPriceUnit; @endphp
            @endforeach

            <?php
                $isShowSubTotal = !$cart->coupon_id && !$cart->redeem_history_id
                    && !\App\Helpers\GroupHelper::isApplyGroupDiscount($cart) &&
                !($isDeleveringAvailable
                    && $isDeleveringPriceMin
                    && $conditionDelevering
                    && $conditionDelevering->free > $totalPrice
                    && $totalPrice >= $conditionDelevering->price_min && !$cart->group_id);
            ?>
            <div class="row-table">
                <div class="wrapSubTotal @if ($isShowSubTotal) hiddenSubtotal @endif"
                     style="@if ($isShowSubTotal) display:none @endif">
                    <div class="col-left">
                        <span>
                            @lang('cart.subtotaal'):
                        </span>
                    </div>
                    <div class="col-right">
                        <span class="price-currency">€</span>
                        <span class="totalPriceOld" style="display:inline">
                            {{ number_format($totalPrice, 2) }}
                        </span>
                    </div>
                </div>
                @php
                    $feeShip = 0;
                    if ($isDeleveringAvailable
                        && $isDeleveringPriceMin
                        && $conditionDelevering
                        && $conditionDelevering->free > $totalPrice
                        && $totalPrice >= $conditionDelevering->price_min
                        && !$cart->group_id) {
                            $feeShip = $conditionDelevering->price;
                        }
                @endphp
                <div id="fee" style="display:
                    @if ($isDeleveringAvailable
                        && $isDeleveringPriceMin
                        && $conditionDelevering
                        && $conditionDelevering->free > $totalPrice
                        && $totalPrice >= $conditionDelevering->price_min
                        && !$cart->group_id
                    ) block @else none @endif"
                >
                    <div class="col-left">
                        <span>
                            @lang('cart.leverkosten'):
                        </span>
                    </div>
                    <div class="col-right">
                        <span class="price-currency">€</span>
                        <span class="leverkosten" style="display:inline">
                            {{ $conditionDelevering && $conditionDelevering->free > $totalPrice && !$cart->group_id ? number_format((float) $conditionDelevering->price, 2) : 0.00 }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="row-table total">
                <div class="wrapCouponCode" style="@if (!$cart->coupon) display:none @endif">
                    <div class="col-left">
                        <span>
                            @lang('cart.coupon_korting'):
                        </span>
                    </div>
                    <div class="col-right">
                        - <span class="price-currency">€</span>
                        <span class="couponDiscount" style="display:inline">
                            {{ number_format($priceDiscount, 2) }}
                        </span>
                        @if (!$isSuccessPage)
                            <a href="javascript:;" class="deleteCouponDiscount">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.71745 12.1331C9.70899 12.1331 12.1341 9.70801 12.1341 6.71647C12.1341 3.72493 9.70899 1.2998 6.71745 1.2998C3.72591 1.2998 1.30078 3.72493 1.30078 6.71647C1.30078 9.70801 3.72591 12.1331 6.71745 12.1331Z" stroke="#B4B4B4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.3418 5.09131L5.0918 8.34131" stroke="#B4B4B4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.0918 5.09131L8.3418 8.34131" stroke="#B4B4B4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="wrapRedeemDiscount" style="display:@if ($cart->redeem_history_id) block @else none @endif">
                    <div class="col-left">
                        <span>
                            @lang('cart.klantenkaart_korting'):
                        </span>
                    </div>
                    <div class="col-right">
                        - <span class="price-currency">€</span>
                        <span class="redeemDiscount" style="display:inline">
                            {{ number_format($redeemDiscount, 2) }}
                        </span>
                        @if (!$isSuccessPage)
                            <a href="javascript:;" class="deleteRedeemDiscount">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.71745 12.1331C9.70899 12.1331 12.1341 9.70801 12.1341 6.71647C12.1341 3.72493 9.70899 1.2998 6.71745 1.2998C3.72591 1.2998 1.30078 3.72493 1.30078 6.71647C1.30078 9.70801 3.72591 12.1331 6.71745 12.1331Z" stroke="#B4B4B4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.3418 5.09131L5.0918 8.34131" stroke="#B4B4B4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.0918 5.09131L8.3418 8.34131" stroke="#B4B4B4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="wrapGroupDiscount" style="display:@if (\App\Helpers\GroupHelper::isApplyGroupDiscount($cart)) block @else none @endif">
                    <div class="col-left">
                        <span>
                            @lang('cart.group_discount'):
                        </span>
                    </div>
                    <div class="col-right">
                        - <span class="price-currency">€</span>
                        <span class="groupDiscount" style="display:inline">
                            {{ number_format($groupDiscount, 2) }}
                        </span>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="total-cart-step1">
                    <div class="col-left">
                        <h6>@lang('cart.totaal'):</h6>
                    </div>
                    <div class="col-right">
                        <h6>€<b class="totalPriceFinal">{{ number_format((float) $totalPrice, 2) + number_format((float) $feeShip, 2) - number_format((float) $priceDiscount, 2) }}</b></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$isSuccessPage)
        <div class="form-line form-inline formInputCoupon" style="@if ($cart->coupon) display:none @endif">
            <input type="text" name="coupon_code" id="coupon_code"
                   @if (session()->has('cart_coupon_code')) value="{{session()->get('cart_coupon_code')}}" @endif
                   placeholder="@lang('cart.plh_coupon_code')" class="required text-uppercase custom-placeholder"
                   data-route="{{ route($guard.'.coupons.checkCode', [$webWorkspace->id, 'code' => '-coupon_code-']) }}"
            />
            <input type="hidden" name="submitByCoupon" />
            <a href="javascript:;" class="btn btn-andere btn-submit-coupon btn-pr-custom">@lang('cart.btn_toepassen')</a>
            <div class="clearfix"></div>
            <span class="errors extend" style="text-align:left;@if(!session()->has('cart_coupon_error')) display:none @endif">@if(session()->has('cart_coupon_error')) {{session()->get('cart_coupon_error')}} @else @lang('cart.msm_error_coupon') @endif</span>
        </div>
        <div class="form-line">
            <textarea name="note" class="custom-placeholder" cols="30" rows="10" maxlength="100"
                      placeholder="@lang('cart.plh_eventuele')">{{ $cart->note }}</textarea>
        </div>

        @php
            session()->forget(['cart_coupon_code', 'cart_coupon_error']);
        @endphp
    @endif
@endif
