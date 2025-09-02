@php
    $countCheck = 0;
    $countFlag = false;

    if(!empty($optionSettingId)) {
        if(!$option->items->isEmpty()) {
            foreach($option->items as $key => $optionItem) {
                if(!empty($importantOptionItems[$optionItem->id])) {
                    $countFlag = true;
                    break;
                }
            }
        }
    }
@endphp
@if(!empty($optionSettingId))
    @if(!$option->items->isEmpty() && !empty($countFlag))
        <div class="important-option-items">
            @foreach($option->items as $key => $optionItem)
                @if(!empty($importantOptionItems[$optionItem->id]))
                    <div class="order-option-items {!! $countCheck > 0 ? 'mgt-10' : '' !!}">
                        <div class="pull-left order-option-name">
                            {!! $optionItem->name !!}
                        </div>
                        <div class="pull-right order-option-number">
                            {!! $importantOptionItems[$optionItem->id] !!}
                        </div>
                    </div>
                    @php
                        $countCheck++;
                    @endphp
                @endif
            @endforeach
        </div>
    @endif
@endif