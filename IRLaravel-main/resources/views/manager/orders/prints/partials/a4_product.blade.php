<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-sm-12 col-xs-12 text-uppercase">
                <div class="print-cat-name">
                    {!! $category->name !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12">
                @php
                    $printProducts = \App\Facades\Order::sortAgainProduct($order, $products);
                @endphp
                @if(!empty($printProducts))
                    @foreach($printProducts as $product)
                        @php
                            $orderItem = $product['order_item'];
                            $metaOrderItem = json_decode($orderItem->metas, true);
                        @endphp
                        <div class="row print-product-item">
                            <div class="col-sm-2 col-xs-2">
                                {!! !empty($orderItem->order->user) ? $orderItem->order->user->name : '' !!}
                            </div>
                            <div class="col-sm-2 col-xs-2">
                                {!! $product['total_number'] !!} x
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="print-product-name">{!! !empty($product['order_item']->product) ? $product['order_item']->product->name : (!empty($metaOrderItem['product']) ? $metaOrderItem['product']['name'] : $product['order_item']->product->name) !!}</div>
                            </div>
                            <div class="col-sm-2 col-xs-2 text-right">
                                {!! $currency.number_format((float)(!empty($orderItem->subtotal / $orderItem->total_number) ? $orderItem->subtotal / $orderItem->total_number : 0), 2, '.', '') !!}
                            </div>
                            <div class="col-sm-2 col-xs-2 text-right">
                                @if(!empty($orderItem->order->total_paid))
                                    <del>{!! $currency.number_format((float)(!empty($orderItem->total_price) ? $orderItem->total_price : 0), 2, '.', '') !!}</del>
                                @else
                                    {!! $currency.number_format((float)(!empty($orderItem->total_price) ? $orderItem->total_price : 0), 2, '.', '') !!}
                                @endif
                            </div>
                            <div class="col-sm-8 col-sm-offset-4 col-xs-8 col-xs-offset-4">
                                @if(!empty($product['option_items']))
                                    @php
                                        $countItem = 0;
                                        $optionItemConvert = [];

                                        foreach($product['option_items'] as $optionItem) {
                                            if(!empty($optionItem['option_item']['opties_id'])) {
                                                $optionItemId = $optionItem['option_item']['opties_id'];

                                                if(!empty($optionItemConvert[$optionItemId])) {
                                                    array_push($optionItemConvert[$optionItemId], $optionItem);
                                                } else {
                                                    $optionItemConvert[$optionItemId] = [$optionItem];
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(!empty($optionItemConvert))
                                        <div class="row print-product-option">
                                            <div class="col-sm-12 col-xs-12">
                                                @foreach($optionItemConvert as $optionItems)
                                                    @php
                                                        $countItemChar = !empty($countItem) ? '/ ' : '';
                                                        $countItem++;
                                                    @endphp

                                                    @foreach($optionItems as $key => $optionItem)
                                                        @php
                                                            $zonder = !empty($optionItem['option'][0]['is_ingredient_deletion']);
                                                            $optionItemName = !empty($optionItem['option_item']['name']) ? $optionItem['option_item']['name'] : '';
                                                        @endphp
                                                        @if(!empty($zonder))
                                                            {!! $countItemChar !!}<strong>Z</strong> {!! $optionItemName !!}
                                                        @else
                                                            {!! $countItemChar !!}{!! $optionItemName !!}
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>