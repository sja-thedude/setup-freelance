{!! Form::open(['route' => [$guard.'.orders.index'], 'method' => 'post', 'class' => 'keypress-search']) !!}

<div class="row print-job-filter mgt-30">
    <div class="col-sm-3 col-xs-12">
        {{ Form::select('filter_workspace_id', $workspaces->pluck('name', 'id'), null, [
            'class' => 'form-control select2',
            'placeholder' => trans('order.all_restaurant')
        ]) }}
    </div>
    <div class="col-sm-3 col-xs-12">
        {{ Form::select('filter_transform_type', \App\Helpers\OrderHelper::getTypes(), request('filter_transform_type'), [
           'class' => 'form-control select2',
           'placeholder' => ucwords(trans('order.all'))
       ]) }}
    </div>
    <div class="col-sm-3 col-xs-12">
        {{ Form::select('filter_payment_method', \App\Helpers\OrderHelper::getPaymentMethods(), request('filter_payment_method'), [
            'class' => 'form-control select2',
            'placeholder' => trans('order.all_payment_method')
        ]) }}
    </div>
    <div class="col-sm-3 col-xs-12">
        {{ Form::select('filter_printed', [
            \App\Models\Order::NOT_PRINTED => trans('order.not_printed'),
            \App\Models\Order::NOT_PRINTED_AUTO_ENABLED => trans('order.not_printed_auto_enabled'),
            \App\Models\Order::PRINTED => trans('order.printed'),
            \App\Models\Order::TO_BE_PRINTED => trans('order.to_be_printed')
        ], request('filter_printed'), [
            'class' => 'form-control select2',
            'placeholder' => trans('order.all_print_status')
        ]) }}
    </div>
</div>
<div class="row print-job-filter mgt-15">
    <div class="col-sm-3 col-xs-12">
        {{ Form::text('filter_datetime', null, [
            'class' => 'form-control datepicker ir-input-filter',
            'data-date-format' => 'dd/mm/yy',
            'placeholder' => ucwords(trans('common.select_date'))
        ])}}
    </div>
    <div class="col-sm-3 col-xs-12">
        <input type="hidden" name="timezone" class="auto-detect-timezone"/>
        {!! Form::submit(trans('common.send'), ['class' => 'ir-btn ir-btn-primary']) !!}
        <a class="ir-btn ir-btn-secondary mgl-15" href="{!! route($guard.'.orders.index') !!}">
            @lang('common.show_all')
        </a>
    </div>
</div>

{!! Form::close() !!}