<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css" rel="stylesheet" media="all">
            @media only screen and (max-width:500px){.button{width:100% !important}}.verifyInvite,.idTeam,.idLeague,#contentMsg br{display:none}
            .shopping-cart{margin-bottom:14px}.shopping-cart.mb-40{margin-bottom:40px}.shopping-cart>h6{font-size:16px;line-height:22px;color:#000;font-family:"Open Sans",sans-serif;margin-bottom:10px;position:relative}.shopping-cart>h6:before{content:'';display:block;width:50px;height:0;border:1px solid #c4c4c4;position:absolute;left:0;bottom:0}.shopping-cart .wp-table .col-right{text-align:right}.shopping-cart .wp-table .col-right .price{display:inline-block;line-height:21px;margin-right:17px}.shopping-cart .wp-table .col-right .delete{float:right;display:inline-block;line-height:18px;margin-right:13px}.shopping-cart .wp-table .col-right .delete i:before{color:#6e371e;font-size:13px}.shopping-cart .wp-table .col-right span{margin-right:13px}.shopping-cart .wp-table .row-table{display:flex}.shopping-cart .wp-table .row-table .col-left{width:50%}.shopping-cart .wp-table .row-table .col-right{width:50%}.shopping-cart .wp-table .row-table .col-right h6{margin-right:13px}.shopping-cart .wp-table .row-table h6{font-size:14px}.shopping-cart .wp-table .row-table .col-right .minute-content .minus,.shopping-cart .wp-table .row-table .col-right .minute-content .plus{margin-right:0}.shopping-cart .wp-table .row-table.row-error .col-left label{color:#f11}.shopping-cart .wp-table .row-table.row-error .col-right{position:relative}.shopping-cart .wp-table .row-table.row-error .col-right:before{content:'';background-image:url(../../images/circle.png);background-repeat:no-repeat;position:absolute;right:-5px;width:13px;height:13px;background-size:contain;top:4px}.shopping-cart .wp-table .row-table.row-error .col-right .price{color:#f11}.shopping-cart .wp-table .row-table.row-error .col-right .delete i:before{color:#f11}.shopping-cart .wp-table .row-table.show-i-delete .col-right:before{content:'';background-image:url(../../images/sprite.png);background-repeat:no-repeat;position:absolute;right:-5px;width:13px;height:13px;background-position:-120px -49px;top:1px}.shopping-cart .wp-table .wrapForProduct{border-bottom:1px solid #d1d1d1}.no-border-bottom{border:none!important}
        </style>
    </head>

    <?php
        $style = [
            /* Layout ------------------------------ */
            'body' => 'margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;',
            'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;',
            /* Masthead ----------------------- */
            'email-masthead' => 'padding: 25px 0; text-align: center;',
            'email-masthead_name' => 'font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;',
            'email-body' => 'width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;',
            'email-body_inner' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0;',
            'email-body_cell' => 'padding: 0px;',
            'email-footer' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;',
            'email-footer_cell' => 'color: #AEAEAE; padding: 35px; text-align: center;',
            /* Body ------------------------------ */
            'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
            'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',
            /* Type ------------------------------ */
            'anchor' => 'color: #3869D4;',
            'header-1' => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
            'paragraph' => 'margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
            'paragraph-sub' => 'margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;',
            'paragraph-center' => 'text-align: center;',
            /* Buttons ------------------------------ */
            'button' => 'display: block; display: inline-block; width: auto; min-height: 20px; padding: 8px 25px;
                         background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                         text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',
            'button--green' => 'background-color: #22BC66;',
            'button--red' => 'background-color: #dc4d2f;',
            'button--blue' => 'background-color: #3869D4;',
            'mgNegative40' => 'margin-top:-40px;',
            'mgNegative15' => 'margin-top:-15px;',
            'content_note' => 'padding: 10px;margin-top: 50px;font-weight: bold;font-size: 24px;line-height: 28px;border: 1px solid #000000;box-sizing: border-box;',
            'h6' => 'font-size:16px;line-height: 22px;color: #000;font-family: "Open Sans",sans-serif;margin-bottom: 10px;',
        ];
    ?>

    <body style="{{ $style['body'] }}">
        <table width="600px" cellpadding="0" cellspacing="0" style="margin:auto">
            <tr>
                <td style="{{ $style['email-wrapper'] }}" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- Email Body -->
                        <tr>
                            <td style="{{ $style['email-body'] }}" width="100%">
                                <div style="padding:50px">

                                    <p>{{ $content2 }},</p>
                                    <p>
                                        {!! $cart->type == \App\Models\Cart::TYPE_LEVERING ? $content12 : $content3 !!}
                                    </p>

                                    <div class="shopping-cart">

                                        <div class="shopping-cart">
                                            <h6 style="{{ $style['h6'] }}">{{ $content5 }}:</h6>

                                            <div class="wp-table table-order">
                                                @foreach($listItem as $item)
                                                    @php
                                                        $metas                = json_decode($item->metas);
                                                        $product              = $metas->product;
                                                        $nameProduct          = $product->name;
                                                        $htmlOptions          = "";
                                                        $productOptions       = $item->optionItems;
                                                        $groupCartOptionItems = $productOptions->groupBy('optie_id');

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

                                                    <div class="wrapForProduct" style="border-bottom: 1px solid #d1d1d1;padding-bottom:3px">
                                                        <div class="row-table"  style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <label><b>{{ $item->total_number }} x {{ $nameProduct }}</b></label>
                                                                <input type="hidden" name="priceUnitProduct" value="{{ $product->price }}"/>
                                                            </div>

                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                <div class="minute-content hidden">
                                                                    <input type="hidden" name="cartItem[{{ $item->id }}][total_number]" value="{{ $item->total_number }}">
                                                                </div>
                                                                <label style="display: inline">€</label>
                                                                <label class="price product">
                                                                    {{ $item->subtotal }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        {!! $htmlOptions !!}
                                                    </div>
                                                @endforeach

                                                @if (!is_null($cart->ship_price) || !empty($cart->service_cost) || !is_null($cart->coupon_discount) || !is_null($cart->redeem_discount))
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <span class="extra">
                                                                    @lang('cart.subtotaal'):
                                                                </span>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                €
                                                                <span class="totalPriceOld">
                                                                    {{ $cart->subtotal }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!is_null($cart->ship_price) && !$cart->group_id)
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <span class="extra">
                                                                    @lang('cart.leverkosten'):
                                                                </span>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                €
                                                                <span class="leverkosten">
                                                                    {{ $cart->ship_price }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($cart->service_cost) && !$cart->group_id)
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                            <span class="extra">
                                                                @lang('workspace.service_cost'):
                                                            </span>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                €
                                                                <span class="leverkosten">
                                                                {{ $cart->service_cost }}
                                                            </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!is_null($cart->coupon_discount) && $cart->coupon_id)
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <span class="extra">
                                                                    @lang('cart.coupon_korting'):
                                                                </span>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                - €<span class="couponDiscount">
                                                                    {{ $cart->coupon_discount }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!is_null($cart->redeem_history_id))
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <span class="extra">
                                                                    @lang('cart.klantenkaart_korting'):
                                                                </span>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                - €<span class="couponDiscount">
                                                                    {{ $cart->redeem_discount }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                    @if(!is_null($cart->group_discount) && !empty($cart->group->discount_type) && $cart->group->discount_type)
                                                        <div class="wrapForProduct no-border-bottom">
                                                            <div class="row-table" style="display:flex">
                                                                <div class="col-left" style="width:50%;text-align:left">
                                                                <span class="extra">
                                                                    @lang('cart.group_discount'):
                                                                </span>
                                                                </div>
                                                                <div class="col-right" style="width:50%;text-align:right">
                                                                    - €<span class="groupDiscount">
                                                                    {{ $cart->group_discount }}
                                                                </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                <div class="wrapForProduct no-border-bottom">
                                                    <div class="row-table" style="display:flex">
                                                        <div class="col-left" style="width:50%;text-align:left">
                                                            <h6 style="{{ $style['h6'] }}">{{ $content6 }}:</h6>
                                                        </div>
                                                        <div class="col-right" style="width:50%;text-align:right">
                                                            <h6 style="{{ $style['h6'] }}">
                                                                €<b class="totalPriceFinal">
                                                                    {{ $cart->total_price }}
                                                                </b>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <br>

                                                <div class="wrapForProduct no-border-bottom">
                                                    <div class="row-table" style="display:flex">
                                                        <div class="col-left" style="width:50%;text-align:left">
                                                            <b class="extra">
                                                                {{ $content7 }}:
                                                            </b>
                                                        </div>
                                                        <div class="col-right" style="width:50%;text-align:right">
                                                            <span class="couponDiscount">
                                                                {{ $cart->is_test_account == \App\Models\Order::IS_TRUST_ACCOUNT && ($cart->payment_method == \App\Models\SettingPayment::TYPE_CASH || $cart->payment_method == \App\Models\SettingPayment::TYPE_FACTUUR)
                                                                    ? trans('cart.success_te_betalen')
                                                                    : trans('cart.success_betaald')
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="wrapForProduct no-border-bottom">
                                                    <div class="row-table" style="display:flex">
                                                        <div class="col-left" style="width:50%;text-align:left">
                                                            <b class="extra">
                                                                {{ $content8 }}:
                                                            </b>
                                                        </div>
                                                        <div class="col-right" style="width:50%;text-align:right">
                                                            <span class="couponDiscount">
                                                                {{ \App\Models\SettingPayment::getTypes()[$cart->payment_method] }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($cart->note)
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <b class="extra">
                                                                    {{ $content9 }}
                                                                </b>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                <span class="couponDiscount">
                                                                    {{ $cart->note }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- For group--}}
                                                @if ($cart->group_id)
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <b class="extra">
                                                                    {{ $content10 }}
                                                                </b>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                <span class="couponDiscount">
                                                                    {{ $cart->group->name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                {{-- ./For group--}}

                                                @if ($cart->type == \App\Models\Cart::TYPE_LEVERING)
                                                    <div class="wrapForProduct no-border-bottom">
                                                        <div class="row-table" style="display:flex">
                                                            <div class="col-left" style="width:50%;text-align:left">
                                                                <b class="extra">
                                                                    {{ $content11 }}
                                                                </b>
                                                            </div>
                                                            <div class="col-right" style="width:50%;text-align:right">
                                                                <span class="couponDiscount">
                                                                    {{ $cart->address }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <p>
                                        {{ $content4 }}
                                    </p>
                                    <p>
                                        {{ $cart->workspace->name }}
                                    </p>
                                    
                                    @if($cart->is_test_account && !empty($content_note))
                                        <p style="{{$style['content_note']}}">
                                            {{strtoupper($content_note)}}
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
