<div class="dropdown-options">
    <input type="hidden" name="orderOptions"/>
    @php
        $itemOptionPartial = 'manager.partials.item-option';
        if (!empty($fromProduct)) {
            $itemOptionPartial = 'manager.partials.item-option-product';
        }
    @endphp

    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="selected-count">
            {{ $textButton }}
        </span>
        <i class=" fa fa-angle-down"></i>
    </button>

    <div class="ui-sortable dropdown-menu" aria-labelledby="dropdownMenuButton">
        @if (count($optionsRelation) > 0)
            @php($arrIdsOptRelation = array())

            @foreach($optionsRelation as $item)
                @if (!$item->option)
                    @continue
                @endif
                @include($itemOptionPartial, [
                    'useCategoryOption' => $useCategoryOption,
                    'optionId'          => $item->opties_id,
                    'isChecked'         => $item->is_checked,
                    'optionName'        => $item->option->name
                ])
                @php($arrIdsOptRelation[] = $item-> opties_id)
            @endforeach

            @php($rootIdsOption = $options->pluck('id')->toArray())
            @php($idsOpt = array_diff($rootIdsOption, $arrIdsOptRelation))

            @foreach($options as $opt)
                @if(in_array($opt->id, $idsOpt))
                    @include($itemOptionPartial, [
                        'useCategoryOption' => $useCategoryOption,
                        'optionId'          => $opt->id,
                        'isChecked'         => $opt->is_checked,
                        'optionName'        => $opt->name,
                    ])
                @endif
            @endforeach

        @else
            @foreach($options as $opt)
                @include($itemOptionPartial, [
                    'useCategoryOption' => $useCategoryOption,
                    'optionId'          => $opt->id,
                    'isChecked'         => $opt->is_checked,
                    'optionName'        => $opt->name,
                ])
            @endforeach
        @endif
    </div>
</div>
