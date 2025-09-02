@include('layouts.partials.print.top')
{{-- NOTE IF YOU CHANGE ANYTHING HERE YOU SHOULD ALSO ADJUST sticker-bbcode.blade.php --}}

@php
    $currency = '€';
    $counter = 0;
    $isIdentical = true;

    if(!isset($productStickers)) {
        $isIdentical = false;
        $productStickers = $order->print_products;
    }
@endphp

@if(!empty($productStickers))
    @php
        $orderHasNotes = [];
    @endphp
    @foreach($productStickers as $printProduct)
        @if(!empty($printProduct['category']) && !empty($printProduct['products']))
            @php
                $subOrder = $printProduct['order'];
                $category = $printProduct['category'];
                $products = $printProduct['products'];
                $order = \App\Helpers\Order::convertOrderItem(!empty($subOrder->parent_id) ? $subOrder->parentOrder : $subOrder);
                $printProducts = \App\Facades\Order::sortAgainProduct($order, $products);
                $timezone = $order->timezone;
            @endphp
            @if(!empty($printProducts))
                @foreach($printProducts as $product)
                    @php
                        $productItem = $product['order_item'];
                        $productItemOrder = !empty($productItem) ? $productItem->order : null;

                        $productOptionItems = !empty($product['option_items']) ? $product['option_items'] : [];
                        $metaOrderItem = json_decode($productItem->metas, true);
                    @endphp
                    @for ($i = 1; $i <= $product['total_number']; $i++)
                        @if(!empty($subOrder->note) && !in_array($subOrder->id, $orderHasNotes))
                            @php
                                $counter++;
                            @endphp
                            @if($counter > 1)
                                <div class="margin-area" style="display:block;width:100%; @php
                                    echo 'height: '.config('print.px.sticker.margin') . 'px;';
                                @endphp "></div>
                            @endif
                            <div class="print-area print-sticker-html" style="display:block;overflow:hidden;width:100%; @php
                                echo ' height:' . config('print.px.sticker.height') . 'px;';
                                echo ' margin-top:' . config('print.px.sticker.margintop') . 'px;';
                            @endphp " data-print_id="{!! $order->id !!}">
                                <div class="print-main-content print-sticker-content mgb-30">
                                    <div class="row sticker-item mgb-15">
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="row sticker-border-bt pdb-5">
                                                @if(!empty($order->workspace) && !empty($order->workspace->name))
                                                    <div class="col-sm-12 col-xs-12 text-right sticker-name mgb-5">
                                                        {!! $order->workspace->name !!}
                                                    </div>
                                                @endif
                                                @if(!empty($subOrder->group) && !empty($subOrder->group->name) && !$order->is_test_account)
                                                    <div class="col-sm-12 col-xs-12 text-left sticker-name mgb-5">
                                                        {!! !empty($subOrder->group->name) ? $subOrder->group->name : '' !!}
                                                    </div>
                                                @endif
                                                <div class="col-sm-6 col-xs-6 text-left sticker-name">
                                                    @if($order->is_test_account)
                                                        ADMIN (@lang('order.test'))
                                                    @else
                                                        @if(!empty($productItemOrder))
                                                            {!! !empty($productItemOrder->user) ? $productItemOrder->user->name : '' !!}
                                                        @else
                                                            {!! !empty($subOrder->user) ? $subOrder->user->name : '' !!}
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="col-sm-6 col-xs-6 text-right">
                                                    <strong class="sticker-time">
                                                        {!! date('H:i', strtotime(Helper::convertDateTimeToTimezone($order->gereed, $timezone))) !!}
                                                    </strong>
                                                </div>
                                            </div>
                                            @php
                                                $orderHasNotes[] = $subOrder->id;
                                            @endphp
                                            <div class="row mgt-5 mgb-5">
                                                <div class="col-sm-12 col-xs-12">
                                                    <div class="sticker-note-border">
                                                        <strong>{!! $subOrder->user->name !!}:</strong>
                                                        {!! $subOrder->note !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @php
                            $counter++;
                        @endphp

                        @if($counter > 1)
                            <div class="margin-area" style="display:block;width:100%; @php
                                echo 'height: '.config('print.px.sticker.margin') . 'px;';
                            @endphp "></div>
                        @endif

                        <div class="print-area print-sticker-html" style="display:block;overflow:hidden;width:100%; @php
                            echo ' height:' . config('print.px.sticker.height') . 'px;';
                            echo ' margin-top:' . config('print.px.sticker.margintop') . 'px;';
                        @endphp " data-print_id="{!! $order->id !!}">
                            <div class="print-main-content print-sticker-content mgb-30">
                                @php
                                    $optionItemConvert = [];

                                    if(!empty($productOptionItems)) {
                                        foreach($productOptionItems as $optionItem) {
                                            if(!empty($optionItem['option_item']['opties_id'])) {
                                                $optionItemId = $optionItem['option_item']['opties_id'];

                                                if(!empty($optionItemConvert[$optionItemId])) {
                                                    array_push($optionItemConvert[$optionItemId], $optionItem);
                                                } else {
                                                    $optionItemConvert[$optionItemId] = [$optionItem];
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <div class="row sticker-item mgb-15">
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="row sticker-border-bt pdb-5">
                                            @if(!empty($order->workspace) && !empty($order->workspace->name))
                                                <div class="col-sm-12 col-xs-12 text-right sticker-name mgb-5">
                                                    {!! $order->workspace->name !!}
                                                </div>
                                            @endif
                                            @if(!empty($subOrder->group) && !empty($subOrder->group->name) && !$order->is_test_account)
                                                <div class="col-sm-12 col-xs-12 text-left sticker-name mgb-5">
                                                    {!! !empty($subOrder->group->name) ? $subOrder->group->name : '' !!}
                                                </div>
                                            @endif

                                            <div class="col-sm-6 col-xs-6 text-left sticker-name">
                                                @if($order->is_test_account)
                                                    ADMIN (@lang('order.test'))
                                                @else
                                                    @if(!empty($productItemOrder))
                                                        {!! !empty($productItemOrder->user) ? $productItemOrder->user->name : '' !!}
                                                    @else
                                                        {!! !empty($subOrder->user) ? $subOrder->user->name : '' !!}
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="col-sm-6 col-xs-6 text-right">
                                                <span class="sticker-price">{!! $currency.number_format((float)(!empty($productItem->subtotal / $productItem->total_number) ? $productItem->subtotal / $productItem->total_number : 0), 2, '.', '') !!}</span>
                                                <strong class="sticker-time">
                                                    {!! date('H:i', strtotime(Helper::convertDateTimeToTimezone($order->gereed, $timezone))) !!}
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="row pdt-5">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <strong class="sticker-product-name">
                                                            {!! !empty($productItem->product) ? $productItem->product->name : (!empty($metaOrderItem['product']['name']) ? $metaOrderItem['product']['name'] : '') !!}
                                                        </strong>
                                                        <div class="pull-right text-right sticker-product-number">
                                                            @php
                                                                $transformType = !empty($order->group_id) ? $order->group->type : $order->type;
                                                                $transformLabel = !empty($transformType) ? 'L' : 'A';
                                                            @endphp

                                                            <b>{!! $transformLabel !!}#{!! $order->daily_id_display . (!empty($order->group_id) && !empty($productItemOrder->extra_code) ? '-' . $productItemOrder->extra_code : '') !!}</b>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(!empty($optionItemConvert))
                                                    <div class="row print-product-option sticker-product-option p-product-item">
                                                        @foreach($optionItemConvert as $optionItems)
                                                            @if(!empty($optionItems))
                                                                <div class="col-sm-12 col-xs-12 mgt-5">
                                                                    @foreach($optionItems as $key => $optionItem)
                                                                        @php
                                                                            $zonder = !empty($optionItem['option'][0]['is_ingredient_deletion']);
                                                                            $optionItemName = !empty($optionItem['option_item']['name']) ? $optionItem['option_item']['name'] : '';
                                                                        @endphp
                                                                        @if(!empty($key))
                                                                            @if(!empty($zonder))
                                                                                / <strong>Z</strong> {!! $optionItemName !!}
                                                                            @else
                                                                                / {!! $optionItemName !!}
                                                                            @endif
                                                                        @else
                                                                            @if(!empty($zonder))
                                                                                - <strong>Z</strong> {!! $optionItemName !!}
                                                                            @else
                                                                                - {!! $optionItemName !!}
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforeach
            @endif
        @endif
    @endforeach
@endif

@include('layouts.partials.print.bottom')