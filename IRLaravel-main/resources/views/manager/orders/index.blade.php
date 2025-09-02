@extends('layouts.manager')

@section('content')
    <input type="hidden" id="welcome-sound" value="{!! url('assets/files/welcome.mp3') !!}" />
    <div class="row order-list" id="manager-order-list" data-autoload="{!! $autoloadAjax !!}" data-route="{!! request()->fullUrl() !!}">
        <div class="col-sm-12 col-md-12 col-xs-12">
            <div class="ir-panel pdt-i-10">
                <div class="panel-bg">
                    {!! Form::open(['route' => [$guard.'.orders.index'], 'method' => 'get', 'class' => 'keypress-search']) !!}
                        <input type="hidden" name="timezone" class="auto-detect-timezone"/>
                        <input type="hidden" name="workspace_id" value="{!! $workspaceId !!}"/>
                        <div class="ir-title mgb-i-20">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="pull-left ir-h2 mgr-i-20">
                                        @lang('order.title')
                                    </h2>

                                    @include('manager.orders.partials.search')
                                </div>
                            </div>
                        </div>

                        @include('manager.orders.partials.filter')
                    {!! Form::close() !!}
                </div>

                <div class="order-list-table ir-content mgb-150">
                    @include('manager.orders.partials.table')
                </div>
            </div>
        </div>
    </div>
    <fieldset id="print-preview-area" disabled="disabled" style="display: none;">
        <div id="print-preview-main"></div>
    </fieldset>
@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            @if(empty($autoloadAjax))
                Order.totalOrder = {!! $totalOrder !!};
            @endif

            if($('#menu_toggle .ir-toggle-left').length) {
                var body = $('body');

                if(body.hasClass('nav-md')) {
                    $('#menu_toggle .ir-toggle-left').trigger('click');
                }
            }
        });
    </script>
@endpush