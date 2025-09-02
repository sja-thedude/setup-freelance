<fieldset class="list-item-none row delivery-default-row" disabled="disabled" style="display: none;">
    <div class="col-item col-sm-12 col-xs-12 mgt-10">
        <div class="wrap-slider-range">
            <div class="pull-left text-center" style="width: 10%">
                <strong class="row-number">1.</strong>
            </div>
            <div class="slider-range-temp range-small pull-left" style="width: 75%"></div>
            <div class="pull-left text-right" style="width: 15%">
                <strong>200 KM</strong>
            </div>
            {!! Form::hidden('id', 0) !!}
            {!! Form::hidden('area_start', 20, ['class' => 'form-control start_age_dest auto-submit', 'placeholder' => trans('notification.text_field')]) !!}
            {!! Form::hidden('area_end', 65, ['class' => 'form-control end_age_dest auto-submit', 'placeholder' => trans('notification.text_field')]) !!}
        </div>
    </div>
    <div class="col-item col-sm-3 col-xs-12 has-symbol">
        <span class="text-symbol inline-block">@lang('setting.more.min')</span>
        <div class="form-group flex inline-block">
            <span class="icon">€</span>
            {!! Form::number('price_min', '0.00', [
                'class' => 'form-control validate-min auto-format-decimal auto-submit form-price inline-block',
                'data-type' => 'delivery',
                'data-min' => 0,
                'required' => 'required'
            ]) !!}
        </div>
    </div>
    <div class="col-item col-sm-3 col-xs-12 has-symbol">
        <span class="text-symbol inline-block">@lang('setting.more.cost')</span>
        <div class="form-group flex inline-block">
            <span class="icon">€</span>
            {!! Form::number('price', '0.00', [
                'class' => 'form-control validate-min auto-format-decimal auto-submit form-price inline-block',
                'data-type' => 'delivery',
                'data-min' => 0,
                'required' => 'required'
            ]) !!}
        </div>
    </div>
    <div class="col-item col-sm-4 col-xs-12 has-symbol">
        <span class="text-symbol inline-block">@lang('setting.more.free_from')</span>
        <div class="form-group flex inline-block">
            <span class="icon">€</span>
            {!! Form::number('free','0.00', [
                'class' => 'form-control validate-min auto-format-decimal auto-submit form-price inline-block',
                'data-type' => 'delivery',
                'data-min' => 0,
                'required' => 'required'
            ]) !!}
        </div>
    </div>
    <div class="col-item col-sm-2 col-xs-12">
        <a class="ir-a top-8 show-hide-area remove-delivery auto-submit" data-id="delivery-name">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6H5H21" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 11V17" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 11V17" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</fieldset>
