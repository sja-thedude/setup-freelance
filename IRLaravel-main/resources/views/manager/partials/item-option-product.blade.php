<div class="ui-sortable-handle">
    <a href="javascript:;">
        <img src="{!! url('assets/images/icons/drag-drop.svg') !!}"/>
    </a>
    <div class="icheckbox_flat-green @if(!$useCategoryOption) disabled @endif @if ($isChecked) checked @endif" style="position: relative;">
        <input type="checkbox" class="checkbox categoryOptions" data-role="checkbox"
               value="{{ $optionId }}"
               style="position: absolute; opacity: 0;"
               name="categoryOptions[]"@if(!$useCategoryOption) disabled @endif @if ($isChecked) checked @endif/>
        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
    </div>

    <span class="day">{{ $optionName }}</span>
</div>