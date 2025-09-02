<div class="pull-left ir-group-date-range order-date-range mgr-20">
    {{ Form::text('date_range', !empty($date_range) ? $date_range : null, [
        'class' => 'ir-input ir-date-range'
    ])}}
    {{ Form::hidden('range_start_date', !empty($range_start_date) ? $range_start_date : null, [
            'class' => 'range_start_date'
    ])}}
    {{ Form::hidden('range_end_date', !empty($range_end_date) ? $range_end_date : null, [
        'class' => 'range_end_date'
    ])}}

    <span class="ir-input-group-btn ir-btn-date-range date-range-trigger">
        <button class="ir-btn-search" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3 10H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </span>
</div>

<div class="pull-left ir-group-search">
    {{ Form::text('keyword_search', NULL, ['class' => 'ir-input', 'placeholder' => trans('option.plh_search')]) }}
    <span class="ir-input-group-btn ir-btn-search">
        {!! Form::button(NULL, ['class' => 'ir-btn-search', 'type' => 'submit']) !!}
    </span>
</div>