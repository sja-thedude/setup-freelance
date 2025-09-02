<div class="pull-left date-range-picker">
    {!! Form::open(['route' => $route,
         'method' => 'get',
         'files' => true,
         'class' => 'ir-group-date-range area-date-range'
     ]) !!}
        @if(!empty($dateIp))
            <div class="pull-left mgr-20">
                {!! Form::text('date_range', null, ['class' => 'ir-input ir-only-date-range']) !!}
                {!! Form::hidden('start_time', null, ['class' => 'range_start_date']) !!}
                {!! Form::hidden('end_time', null, ['class' => 'range_end_date']) !!}
                <input type="hidden" name="timezone" class="auto-detect-timezone"/>
                <span class="ir-input-group-btn ir-btn-date-range date-range-trigger">
                    <button class="ir-btn-search" type="button">
                        <svg class="mgt-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 10H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </span>
            </div>
        @endif

        @if(!empty($searchIp))
            <div class="pull-left">
                <div class="search-and-button">
                    <div class="ir-group-search">
                        {!! Form::text('keyword_search', null, ['class' => 'ir-input', 'placeholder' => trans('statistic.search_product')]) !!}
                        <span class="ir-input-group-btn">
                        <button class="ir-btn-search" type="submit"></button>
                    </span>
                    </div>
                </div>
            </div>
        @endif

    {!! Form::close() !!}
</div>