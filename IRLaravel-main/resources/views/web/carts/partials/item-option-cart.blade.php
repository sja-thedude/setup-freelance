<div class="row-table no-border">
    <div class="col-left">
        <span class="extra">
            - {{ $nameOptionItem }}
        </span>
        <input type="hidden" class="priceUnitOption" value="{{ $priceOptionItem }}"/>
    </div>
    <div class="col-right" style="display:none">
        <span class="price" style="margin:0">€</span>
        <span class="price option">
            {{ $priceOptionItem * $numberProduct }}
        </span>
        @if (!$isSuccessPage)
            <a href="javascript:;" class="delete" data-type="option">
                <i class="icon-trash"></i>
            </a>
        @endif
    </div>
    <input type="hidden" name="cartOptionItem[{{ $optId }}]" value="{{ $cartOptionItems }}">
</div>