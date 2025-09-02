<div class="shopping-cart">
<h6 class="title-order-detail">@lang('cart.title_bestelde_artikelen')</h6>
<div class="name-restaurant display-none">
    {!! !empty($order->workspace) ? $order->workspace->name : '' !!}
</div>
    <div class="wp-table table-order">
        @foreach($listItem as $kk => $item)
            @php
                $metas                = json_decode($item->metas);
                $product              = $metas->product;
                $nameProduct          = $product->name;
                $htmlOptions          = "";
                $productOptions       = $item->optionItems;
                $groupCartOptionItems = $productOptions->groupBy('optie_id');
                $borderTop            = $kk > 0 ? 'border-top' : '';

                foreach($groupCartOptionItems as $optId => $cartOptionItems) {
                    $options          = collect();
                    $opt              = NULL;
                    $originOptionItem = array();

                    foreach($cartOptionItems as $optionItem) {
                        $metas         = json_decode($optionItem->metas);
                        $optIt         = new \App\Models\CartOptionItem();
                        $optIt->master = $metas->option_item->master;
                        $optIt->price  = $metas->option_item->price;
                        $optIt->name   = $metas->option_item->name;
                        $opt           = isset($metas->option[0]) ? $metas->option[0] : NULL;
                        $options->push($optIt);
                    }

                    $nameOption = "";
                    $isMaster   = $options->where('master', true)->first();

                    if ($opt && $opt->is_ingredient_deletion) {
                        $nameOption .= trans('cart.txt_zonder');
                    }

                    $nameOption .= $isMaster
                        ? $isMaster->name
                        : implode(', ', $options->pluck('name')->toArray());

                    $htmlOptions .= view('web.carts.partials.item-option-cart', [
                        'numberProduct'   => $item->total_number,
                        'nameOptionItem'  => $nameOption,
                        'priceOptionItem' => 0,
                        'isSuccessPage'   => TRUE,
                        'optId'           => $optId,
                        'cartOptionItems' => json_encode($originOptionItem),
                    ])->render();
                }
            @endphp
            
            <div class="wrapForProduct">
                <div class="row-table {!! $borderTop !!}">
                    <div class="col-left">
                        <label class="label-prd display-flex">
                            @if($isSuccessPage)
                                <b class="total">{{ $item->total_number }} x </b>
                            @endif
                            <b>
                                {{ $nameProduct }}
                            </b>
                        </label>
                    </div>

                    <div class="col-right">
                        <label style="display: inline">€</label>
                        <label class="price product">
                            {{ $item->subtotal > 0 ? \App\Helpers\Helper::formatPrice($item->subtotal) : 0 }}
                        </label>
                    </div>
                </div>
                {!! $htmlOptions !!}
            </div>
        @endforeach
        <div class="row-table">
            @if (!is_null($cart->ship_price) || !empty($cart->service_cost) || !is_null($cart->coupon_discount) || !is_null($cart->redeem_discount))
                <div class="wrapSubTotal">
                    <div class="col-left">
                        <span>
                            @lang('cart.subtotaal'):
                        </span>
                    </div>
                    <div class="col-right">
                        <span class="price-currency">€</span>
                        <span class="totalPriceOld" style="display:inline">
                            {{ \App\Helpers\Helper::formatPrice($cart->subtotal) }}
                        </span>
                    </div>
                </div>
            @endif
            @if (!is_null($cart->ship_price) && ($cart->ship_price > 0))
                <div id="fee">
                    <div class="col-left">
                        <span class="extra">
                            @lang('cart.leverkosten'):
                        </span>
                    </div>
                    <div class="col-right">
                        €<span class="leverkosten" style="display:inline">
                            {{ \App\Helpers\Helper::formatPrice($cart->ship_price) }}
                        </span>
                    </div>
                </div>
            @endif
            @if (!empty($cart->service_cost))
                <div id="fee">
                    <div class="col-left">
                    <span class="extra">
                        @lang('workspace.service_cost'):
                    </span>
                    </div>
                    <div class="col-right">
                        €<span class="leverkosten" style="display:inline">
                        {{ \App\Helpers\Helper::formatPrice($cart->service_cost) }}
                    </span>
                    </div>
                </div>
            @endif
        </div>
        <div class="row-table total">
            @if (!is_null($cart->coupon_discount) && $cart->coupon_id)
                <div class="wrapCouponCode">
                    <div class="col-left">
                        <span>
                            @lang('cart.coupon_korting'):
                        </span>
                    </div>
                    <div class="col-right">
                        - €<span class="couponDiscount" style="display:inline">
                            {{ \App\Helpers\Helper::formatPrice($cart->coupon_discount) }}
                        </span>
                    </div>
                </div>
            @endif

            @if (!is_null($cart->redeem_discount) && $cart->redeem_history_id)
                <div class="wrapRedeemDiscount">
                    <div class="col-left">
                        <span>
                            @lang('cart.klantenkaart_korting'):
                        </span>
                    </div>
                    <div class="col-right">
                        - <span class="price-currency">€</span>
                        <span class="redeemDiscount" style="display:inline">
                            {{ \App\Helpers\Helper::formatPrice($cart->redeem_discount) }}
                        </span>
                    </div>
                </div>
            @endif

            @if(!is_null($cart->group_discount))
                <div class="wrapGroupDiscount" style="display:@if (\App\Helpers\GroupHelper::isApplyGroupDiscount($cart)) block @else none @endif">
                    <div class="col-left">
                        <span>
                            @lang('cart.group_discount'):
                        </span>
                    </div>
                    <div class="col-right">
                        - <span class="price-currency">€</span>
                        <span class="groupDiscount" style="display:inline">
                            {{ number_format($cart->group_discount, 2) }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="clearfix"></div>
            <div class="total-cart-step1">
                <div class="col-left">
                    <h6>@lang('cart.totaal'):</h6>
                </div>
                <div class="col-right">
                    <h6>€<b class="totalPriceFinal"> {{ number_format($cart->total_price, 2) }}</b></h6>
                </div>
            </div>
        </div>
    </div>
</div>
