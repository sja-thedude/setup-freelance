@if(!empty($order->data_sort['notNotes']))
    @foreach($order->data_sort['notNotes'] as $key => $subItems)
        @php
            $gKey = $key;
        @endphp
        @if(!empty($subItems))
            @php
                $productNotNotes = \App\Facades\Order::groupIdenticalProductInOrderList($subItems);
            @endphp
            @if(!empty($productNotNotes['products']))
                @foreach($productNotNotes['products'] as $productName => $productNumber)
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="row mgb-10">
                                <div class="col-sm-12 col-xs-12 item-title">
                                    {!! $productNumber .' x '. $productName !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    @endforeach
@endif

@if(!empty($order->data_sort['notes']))
    @foreach($order->data_sort['notes'] as $key => $itemFollowOrder)
        @php
            $note = null;
            $noteByUser = null;
        @endphp
        <div class="row {!! !empty($order->data_sort['notNotes']) || $key > 0 ? 'order-line' : '' !!}">
            <div class="col-sm-6 col-xs-12">
                @if(!empty($itemFollowOrder))
                    @foreach($itemFollowOrder as $subItems)
                        @if(!empty($subItems))
                            @php
                                $productNotNotes = \App\Facades\Order::groupIdenticalProductInOrderList($subItems, $note, $noteByUser);

                                if(!empty($productNotNotes['note'])) {
                                    $note = $productNotNotes['note'];
                                }
                                if(!empty($productNotNotes['noteByUser'])) {
                                    $noteByUser = $productNotNotes['noteByUser'];
                                }
                            @endphp
                            @if(!empty($productNotNotes['products']))
                                @foreach($productNotNotes['products'] as $productName => $productNumber)
                                    <div class="row mgb-10">
                                        <div class="col-sm-12 col-xs-12 item-title">
                                            {!! $productNumber .' x '. $productName !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="col-sm-6 col-xs-12">
                @if(!empty($note))
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 item-title">
                            @if(!empty($noteByUser))
                                @lang('order.note') {!! $noteByUser !!}:
                            @else
                                @lang('order.note'):
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            {!! $note !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif