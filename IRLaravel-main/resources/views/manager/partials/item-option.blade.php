<div class="ui-sortable-handle">
    <a href="javascript:;">
        <img src="{!! url('assets/images/icons/drag-drop.svg') !!}"/>
    </a>
    <input type="checkbox" class="flat checkbox" data-role="checkbox"
           value="{{ $optionId }}"
           name="categoryOptions[]"@if(!$useCategoryOption) disabled @endif @if ($isChecked) checked @endif/>
    <span class="day">{{ $optionName }}</span>
</div>