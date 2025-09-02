@extends('layouts.admin')

@section('content')
    <div class="row dashboard-statistic dashboard-chart-lazyload" id="dashboard-statistic"  data-autoload="{!! $autoloadAjax !!}" data-route="{!! request()->fullUrl() !!}">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('dashboard.home')
                    </h2>
                </div>
                <div class="main-content">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 date-range-picker">
                            {!! Form::open(['route' => [$guard.'.dashboard.index'],
                                 'method' => 'get',
                                 'files' => true,
                                 'class' => 'ir-group-date-range area-date-range'
                            ]) !!}
                                {!! Form::text('date_range', null, ['class' => 'ir-input ir-only-date-range pdt-i-7 pdb-i-7']) !!}
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
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="dashboard-order-statistic">
                        @include('admin.dashboard.partials.order_charts')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection