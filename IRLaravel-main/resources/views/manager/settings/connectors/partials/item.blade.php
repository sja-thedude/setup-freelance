<div class="row opties-sortable-handle">
    <input type="hidden" name="items[{{ $key }}][idOptionItem]" value="{{ $item->id }}">
    <div class="col-xs-4">
        <div class="form-group flex naam_keuzeoptie">
            <a href="javascript:;" class="icon">
                <img src="{!! url('assets/images/icons/drag-drop.svg') !!}"/>
            </a>
            <input class="form-control name_item" name="items[{{ $key }}][name]" type="text"
                   placeholder="@lang('option.naam_keuzeoptie')" value="{{ $item->name }}"/>
        </div>
    </div>
    <div class="col-xs-2">
        <div class=" form-group flex">
            <span class="icon">€</span>
            <input class="form-control price is-number" name="items[{{ $key }}][price]" type="text" value="{{ $item->price ?: "0.00" }}"/>
        </div>
    </div>
    <div class="col-xs-2 text-center">
        <div class="mgt-16 form-group">
            <input name="items[{{ $key }}][available]" type="checkbox" class="flat checkbox" value="1" {{ is_null($item->available) || $item->available ? "checked" : "" }}/>
        </div>
    </div>
    <div class="col-xs-2 text-center">
        <div class=" mgt-11 form-group">
            <input name="master" type="checkbox" class="flat checkbox master" value="{{ $key }}" {{ $item->master ? "checked" : "" }}/>
        </div>
    </div>
    <div class="col-xs-2 text-center">
        <div class="mgt-9 form-group">
            <a class="removeItem" href="javascript:;">
                <i class="fa fa-trash-o font-size-25 color-B5B268"></i>
            </a>
        </div>
    </div>
</div>