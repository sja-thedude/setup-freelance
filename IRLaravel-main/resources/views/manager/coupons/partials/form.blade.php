@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'files' => TRUE, 'id' => $idForm]) !!}

    <input name="currency" value="EUR" type="hidden"/>
    <input name="timeZone" class="auto-detect-timezone" value="" type="hidden"/>

    <button type="button" class="close" data-dismiss="modal">&times;</button>

    <div class="modal-body">

        <div class="clear"></div>
        <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>

        <div class="row form-group">
            <div class="col-md-3 col-sm-3">
                <label>@lang('coupon.lb_code'):</label>
            </div>
            <div class="col-md-6 col-sm-7">
                <input class="form-control" name="code" type="text"
                       value="{{ isset($coupon) ? $coupon->code : NULL }}"/>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-3 col-sm-3">
                <label>@lang('coupon.lb_promo_name'):</label>
            </div>
            <div class="col-md-6 col-sm-7">
                <input class="form-control" name="promo_name" type="text"
                       value="{{ isset($coupon) ? ($coupon->translate(app()->getLocale()) ? $coupon->translate(app()->getLocale())->promo_name : $coupon->translate('en')->promo_name) : NULL }}"/>
                <input class="form-control" name="promo_name_valid" type="hidden" value=""/>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-3 col-sm-3"><label>@lang('coupon.lb_geldig_voor'):</label></div>
            <div class="col-md-6 col-sm-7">
                {{ Form::select('category_ids[]', $categories, $categoryIds, [
                    'id' => 'category_ids',
                    'class' => 'selectpicker form-control',
                    'data-live-search' => 'true',
                    'data-hide-disabled' => 'true',
                    'data-actions-box' => 'true',
                    'title' => trans('category.select_category'),
                    'multiple'
                ]) }}
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-3 col-sm-3">
{{--                <label>@lang('category.select_category'):</label>--}}
            </div>
            @php($arrProducts = isset($coupon) ? $coupon->products->pluck('id')->toArray() : [])
            <div class="col-md-6 col-sm-7 wrapSelectProduct">
                <select class="form-control select2-tags" name="category[]" multiple="multiple"
                        id="selectFeaturedProducts"
                        data-json="{{ $productsGroupByCategory }}"
                        data-default="{{ json_encode($arrProducts) }}"
                ></select>
                <input type="hidden" name="listProduct" value="{{ implode(',', $arrProducts) }}"/>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-6 col-sm-6">
                <label>@lang('coupon.lb_max_time_all'):</label>
            </div>
            <div class="col-md-2 col-sm-4">
                <input class="form-control" name="max_time_all" type="text"
                       value="{{ isset($coupon) ? $coupon->max_time_all : NULL }}"/>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-6 col-sm-6">
                <label>@lang('coupon.lb_max_time_single'):</label>
            </div>
            <div class="col-md-2 col-sm-4">
                <input class="form-control" name="max_time_single" type="text"
                       value="{{ isset($coupon) ? $coupon->max_time_single : NULL }}"/>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-2 col-sm-3">
                <label>@lang('coupon.lb_discount'):</label>
            </div>
            <div class="col-md-1 col-sm-1 discount_type">
                <input type="radio" class="flat checkbox" name="discount_type"
                       value="{{\App\Models\Coupon::DISCOUNT_FIXED_AMOUNT}}"
                       @if(isset($coupon) && $coupon->discount_type == \App\Models\Coupon::DISCOUNT_FIXED_AMOUNT) checked @endif />
            </div>
            <div class="col-md-2 col-sm-3 width-minus-20">
                <i class="fa fa-euro icon"></i>
                <input class="form-control is-number" name="discount" type="text"
                       value="{{ isset($coupon) ? $coupon->discount : NULL }}"/>
            </div>

            <div class="col-md-1 col-sm-1 discount_type">
                <input type="radio" class="flat checkbox" name="discount_type"
                       value="{{\App\Models\Coupon::DISCOUNT_PERCENTAGE}}"
                       @if(isset($coupon) && $coupon->discount_type == \App\Models\Coupon::DISCOUNT_PERCENTAGE) checked @endif />
            </div>
            <div class="col-md-2 col-sm-3 width-minus-20">
                <i class="fa fa-percent icon"></i>
                <input class="form-control" name="percentage" type="text"
                       value="{{ isset($coupon) ? $coupon->percentage : NULL }}"/>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-2 col-sm-3">
                <label>@lang('coupon.lb_expire_time'):</label>
            </div>
            <div class="col-md-6 col-sm-8">
                @php($expireTime = isset($coupon) ? \Carbon\Carbon::parse($coupon->expire_time)->format('m/d/Y H:i') : NULL)
                <div class="ir-group-date-range full-width ir-group-datepicker" data-min-date="1">
                    {{ Form::text('range_send_datetime', null, [
                        'class' => 'ir-input ir-datepicker',
                    ])}}

                    <span class="ir-input-group-btn ir-btn-date-range date-range-trigger">
                            <button class="ir-btn-search" type="button">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z"
                                          stroke="black" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                    <path d="M16 2V6" stroke="black" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                    <path d="M8 2V6" stroke="black" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                    <path d="M3 10H21" stroke="black" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </span>
                    {{ Form::hidden('expire_time', $expireTime, [
                        'class'            => 'start_date',
                        'data-single-date' => 1,
                        'data-timezone'    => 1,
                        'data-position'    => 'up',
                    ])}}
                    <input name="expire_time_valid" type="hidden"/>
                </div>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-md-3 col-sm-3">
                <label>@lang('coupon.lb_active'):</label>
            </div>
            <div class="col-md-3 col-sm-4">
                <input type="checkbox" name="active" id="active" value="1"
                       class="switch-input" {{ (isset($coupon) && $coupon->active) || !isset($coupon) ? "checked" : "" }}>
                <label for="active" class="switch"></label>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="ir-btn ir-btn-primary opslaan submit1" style="width:160px" aria-label="">
            @lang('category.btn_opslaan')
        </button>
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@endif


