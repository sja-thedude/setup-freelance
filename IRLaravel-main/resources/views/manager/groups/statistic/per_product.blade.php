@extends('layouts.'.$guard)

@section('content')
    <div class="row layout-statistic statistic-lazyload" data-autoload="{!! $autoloadAjax !!}" data-route="{!! request()->fullUrl() !!}">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('statistic.title') {{$group->name}}
                    </h2>
                    <p class="statistic-sub-title mgb-0">@lang('statistic.per_product')</p>
                </div>
                <div class="main-content">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            @include('manager.groups.statistic.partials.search', [
                                'route' => [$guard.'.groups.statistic.perProduct', $group->id],
                                'dateIp' => true,
                                'searchIp' => true
                            ])
                            @include('manager.groups.statistic.partials.print_actions')
                        </div>
                    </div>

                    <div id="statistic-list" >
                        @if(empty($autoloadAjax))
                            @include('manager.groups.statistic.partials.per_product_table')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection