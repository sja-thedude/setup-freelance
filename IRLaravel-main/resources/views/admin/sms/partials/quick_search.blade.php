{!! Form::open(['route' => [$guard.'.sms.index'], 'method' => 'post', 'class' => 'keypress-search']) !!}

<div class="row print-job-filter mgt-30">
    <div class="col-sm-3 col-xs-12">
        {{ Form::select('workspace_id', $workspaces->pluck('name', 'id'), null, [
            'class' => 'form-control select2',
            'placeholder' => trans('sms.all_restaurant')
        ]) }}
    </div>
    <div class="col-sm-8 col-xs-12">
        {!! Form::submit(trans('sms.send'), ['class' => 'ir-btn ir-btn-primary']) !!}
        <a class="ir-btn ir-btn-secondary mgl-15" href="{!! route($guard.'.sms.index') !!}">
            @lang('common.show_all')
        </a>
    </div>
    <div class="col-sm-12 col-xs-12 mgt-15 ir-group-date-range area-date-range date-range-picker">
        {!! Form::text('date_range', null, ['class' => 'ir-input ir-only-date-range']) !!}
        {!! Form::hidden('start_date', null, ['class' => 'range_start_date']) !!}
        {!! Form::hidden('end_date', null, ['class' => 'range_end_date']) !!}
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
</div>

{!! Form::close() !!}