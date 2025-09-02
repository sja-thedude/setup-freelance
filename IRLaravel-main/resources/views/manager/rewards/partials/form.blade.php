@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'files' => TRUE, 'id' => $idForm]) !!}

    <input name="currency" value="EUR" type="hidden"/>
    <input name="timeZone" class="auto-detect-timezone" value="" type="hidden"/>

    <button type="button" class="close" data-dismiss="modal">&times;</button>

    <div class="modal-body">

        <div class="clear"></div>
        <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>

        <div class="row">
            <div class="col-md-4 col-sm-3">
                @include('manager.partials.upload-avatar', [
                    'media'  => $media ?? NULL,
                    'width'  => 200,
                    'height' => 300,
                ])
            </div>

            <div class="col-md-8 col-sm-9">
                <div class="row">
                    <div class="col-md-3 col-sm-3">
                        <div class="form-group">
                            <input name="type" type="radio" class="flat checkbox" id="korting"
                                   value="{{ \App\Models\Reward::KORTING }}"
                                    {{ (isset($reward) && $reward->type === \App\Models\Reward::KORTING) || !isset($reward) ? "checked" : "" }} />
                            <label class="line-height-initial" for="korting">
                                @lang('reward.lb_korting')
                            </label>
                        </div>
                    </div>

                    <div class="col-md-5 col-sm-6">
                        <div class="form-group">
                            <input name="type" type="radio" class="flat checkbox" id="fysiek_cadeau"
                                   value="{{ \App\Models\Reward::FYSIEK_CADEAU }}"
                                    {{ isset($reward) && $reward->type === \App\Models\Reward::FYSIEK_CADEAU ? "checked" : "" }} />
                            <label class="line-height-initial" for="fysiek_cadeau">
                                @lang('reward.lb_fysiek_cadeau')
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-4 col-sm-3">
                        <label>@lang('reward.lb_title'):</label>
                    </div>
                    <div class="col-md-7 col-sm-7">
                        <input class="form-control" name="title" type="text"
                               value="{{ isset($reward) ? $reward->title : NULL }}"/>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-4 col-sm-3">
                        <label>@lang('reward.lb_tekst'):</label>
                    </div>
                    <div class="col-md-7 col-sm-7">
                        <input class="form-control" name="description" type="text"
                               value="{{ isset($reward) ? $reward->description : NULL }}"/>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-4 col-sm-4">
                        <label>@lang('reward.table.credits_nodig'):</label>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <input class="form-control is-number" name="score" type="text"
                               value="{{ isset($reward) ? $reward->score : NULL }}"/>
                    </div>
                </div>

                <div class="row form-group beloning_waarde"
                     style="{{ isset($reward) && $reward->type === \App\Models\Reward::FYSIEK_CADEAU ? "display:none" : "" }}">
                    <div class="col-md-4 col-sm-4 line-height-15">
                        <label class="line-height-initial">@lang('reward.lb_beloning_waarde'):</label><br>
                        <small>@lang('reward.lb_beloning_waarde_sub')</small>
                    </div>
                    <div class="col-md-1 col-sm-1 discount_type">
                        <input name="discount_type" type="radio" class="flat checkbox"
                               value="{{\App\Models\Coupon::DISCOUNT_FIXED_AMOUNT}}"
                               @if(isset($reward) && $reward->discount_type == \App\Models\Coupon::DISCOUNT_FIXED_AMOUNT) checked @endif />
                    </div>
                    <div class="col-md-3 col-sm-3 width-minus-20">
                        <i class="fa fa-euro icon"></i>
                        <input class="form-control is-number" name="reward" type="text"
                               value="{{ isset($reward) ? $reward->reward : NULL }}"/>
                    </div>

                    <div class="col-md-1 col-sm-1 discount_type">
                        <input name="discount_type" type="radio" class="flat checkbox"
                               value="{{\App\Models\Coupon::DISCOUNT_PERCENTAGE}}"
                               @if(isset($reward) && $reward->discount_type == \App\Models\Coupon::DISCOUNT_PERCENTAGE) checked @endif />
                    </div>
                    <div class="col-md-3 col-sm-3 width-minus-20">
                        <i class="fa fa-percent icon"></i>
                        <input class="form-control is-number" name="percentage" type="text"
                               value="{{ isset($reward) ? $reward->percentage : NULL }}"/>
                    </div>
                </div>

                <div class="row form-group wrap_geldig_voor"
                     style="{{ isset($reward) && $reward->type === \App\Models\Reward::FYSIEK_CADEAU ? "display:none" : "" }}">
                    <div class="col-md-4 col-sm-3 pd-0">
                        <label>@lang('reward.lb_geldig_voor')</label>
                    </div>
                    <div class="col-md-8 col-sm-9">
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

                <div class="row form-group wrap_geldig_voor"
                     style="{{ isset($reward) && $reward->type === \App\Models\Reward::FYSIEK_CADEAU ? "display:none" : "" }}">
                    <div class="col-md-4 col-sm-3 pd-0"></div>
                    @php($arrProducts = isset($reward) ? $reward->products->pluck('id')->toArray() : [])
                    <div class="col-md-8 col-sm-9 wrapSelectProduct">
                        <select class="form-control select2-tags" name="category[]" multiple="multiple"
                                id="selectFeaturedProducts"
                                data-json="{{ $productsGroupByCategory }}"
                                data-default="{{ json_encode($arrProducts) }}"
                        ></select>
                        <input type="hidden" name="listProduct" value="{{ implode(',', $arrProducts) }}"/>
                    </div>
                </div>

                <div class="row form-group display-table-flex">
                    <div class="col-md-4 pd-0">
                        <label>@lang('reward.lb_expire_time'):</label>
                    </div>
                    <div class="col-md-7">
                        @php($expireDate = isset($reward) ? \Carbon\Carbon::parse($reward->expire_date)->format('m/d/Y H:i') : NULL)
                        <div class="ir-group-date-range full-width ir-group-datepicker" data-min-date="1">
                            {{ Form::text('range_send_datetime', null, [
                                'class' => 'ir-input ir-datepicker',
                                'value' => null,
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
                            {{ Form::hidden('expire_date', $expireDate, [
                                'class'            => 'start_date',
                                'data-single-date' => 1,
                                'data-timezone'    => 1,
                                'data-position'    => 'up',
                                'value' => null,
                            ])}}
                            <input name="expire_date_valid" type="hidden"/>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-5">
                        <label class="line-height-initial">@lang('reward.lb_active'):</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="repeat" id="repeat" value="1"
                               class="switch-input" {{ (isset($reward) && $reward->repeat) || !isset($reward) ? "checked" : "" }}>
                        <label for="repeat" class="switch"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="ir-btn ir-btn-primary opslaan submit1 pull-right" style="width:160px"
                aria-label="">
            @lang('category.btn_opslaan')
        </button>
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@endif


