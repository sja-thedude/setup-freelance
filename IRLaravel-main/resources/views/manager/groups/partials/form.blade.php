@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'files' => TRUE, 'id' => $idForm]) !!}
    <button type="button" class="close" data-dismiss="modal">&times;</button>

    <div class="modal-body">

        <div class="clear"></div>
        <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>

        <div id="data-show">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class=" form-group">
                                <input class="form-control" name="name" type="text"
                                       placeholder="@lang('group.company_name')"
                                       value="{{ isset($group) ? $group->name : NULL }}"/>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class=" form-group max-width-150">
                                <input class="form-control" name="company_vat_number" type="text"
                                       placeholder="@lang('group.company_vat_number')"
                                       value="{{ isset($group) ? $group->company_vat_number : NULL }}"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class=" form-group">
                                <input class="form-control" name="company_street" type="text"
                                       placeholder="@lang('group.company_street')"
                                       value="{{ isset($group) ? $group->company_street : NULL }}"/>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class=" form-group max-width-150">
                                <input class="form-control" name="company_number" type="text"
                                       placeholder="@lang('group.company_number')"
                                       value="{{ isset($group) ? $group->company_number : NULL }}"/>
                            </div>
                        </div>
                    </div>

                    <div class="row mgb-30">
                        <div class="col-md-3 col-sm-3">
                            <div class=" form-group max-width-150">
                                <input class="form-control" name="company_postcode" type="text"
                                       placeholder="@lang('group.company_postcode')"
                                       value="{{ isset($group) ? $group->company_postcode : NULL }}"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class=" form-group">
                                <input class="form-control" name="company_city" type="text"
                                       placeholder="@lang('group.company_city')"
                                       value="{{ isset($group) ? $group->company_city : NULL }}"/>
                            </div>
                        </div>
                    </div>

                    <h3>@lang('group.method_type')</h3>

                    <div class="row method">
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <input type="checkbox" name="payment_mollie" id="payment_mollie" value="1"
                                       class="switch-input" {{ (isset($group) && $group->payment_mollie) ? "checked" : "" }}/>
                                <label for="payment_mollie" class="switch"></label>
                                <label class="font-size-16">
                                    @lang('group.payment_mollie')
                                </label>
                                <input type="hidden" name="payment_method_error" disabled />
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <input type="checkbox" name="payment_cash" id="payment_cash" value="1"
                                       class="switch-input" {{ (isset($group) && $group->payment_cash) ? "checked" : "" }}/>
                                <label for="payment_cash" class="switch"></label>
                                <label class="font-size-16">
                                    @lang('group.payment_cash')
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <input type="checkbox" name="payment_factuur" id="payment_factuur" value="1"
                                       class="switch-input" {{ (isset($group) && $group->payment_factuur) ? "checked" : "" }}/>
                                <label for="payment_factuur" class="switch"></label>
                                <label class="font-size-16">
                                    @lang('group.payment_factuur')
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row type-and-time mgt-50">
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <div class="col-md-5 col-sm-5">@lang('group.close_time'):</div>
                                <div class="col-md-7 col-sm-7">
                                    <input id="close_time" class="form-control" name="close_time" type="time"
                                           value="{{ isset($group) && $group->close_time ? $group->close_time : "00:00" }}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-sm-5">@lang('group.receive_time'):</div>
                                <div class="col-md-7 col-sm-7">
                                    <input id="receive_time" class="form-control" name="receive_time" type="time" target_element="#close_time"
                                           value="{{ isset($group) && $group->receive_time ? $group->receive_time : "00:00" }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="row">
                                <div class="col-md-3 lable col-sm-3">
                                    @lang('group.type'):
                                </div>
                                <div class="col-md-9 lable col-sm-9">
                                    {{ Form::select('type', \App\Models\Group::getTypes(),
                                        old('vat_id') ?: (isset($group) ? $group->type : NULL),
                                        ['class' => 'form-control']
                                    ) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-5">
                            <div class="beschikbaarheid">
                                <h3>@lang('category.beschikbaarheid')</h3>

                                <div class="days">
                                    <ul>
                                        @php($listDay = array_values(trans('category.mini_days')))

                                        @foreach($listDay as $k => $day)
                                            <li>
                                                <span class="day">
                                                    {{ $day }}
                                                </span>
                                                <label class="container-cb inline-block custom-checkmark">
                                                    <input type="checkbox" class="form-control" value="1" name="days[{{ $k }}][status]"
                                                           value="1" {{ (isset($openTime) && $openTime[$k + 1]['status']) || (!isset($openTime) && $k !== 5 && $k !== 6) ? "checked" : "" }}/>
                                                    <span class="checkmark" style="width: 23px;height: 23px;"></span>
                                                </label>
                                                <input type="hidden" class="day_number" name="days[{{ $k }}][day_number]" value="{{ $k + 1 }}"/>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-30">
                        <div class="col-md-5 col-sm-5">
                            <strong>{{trans('group.lb_limit_products')}}</strong>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <input type="checkbox" name="is_product_limit" id="limit_products" value="1"
                                   class="switch-input" {{ (isset($group) && $group->is_product_limit) ? "checked" : "" }}/>
                            <label for="limit_products" class="switch"></label>
                            <label class="font-size-16">
                                @lang('option.ja')
                            </label>
                        </div>
                    </div>

                    <div class="form-group row mt-30">
                        <div class="col-md-3 col-sm-5">
                            <strong>@lang('reward.lb_geldig_voor')</strong>
                        </div>
                        <div class="col-sm-9 col-xs-12">
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

                    <div class="form-group row mt-30">
                        <div class="col-md-3 col-sm-5"></div>
                        <div class="col-md-9 col-xs-12">
                            @php($arrProducts = isset($group) ? $group->products->pluck('id')->toArray() : [])
                            <div class="wrapSelectProduct">
                                <select class="form-control select2-tags" name="products[]" multiple="multiple"
                                        id="selectFeaturedProducts"
                                        data-json="{{ $productsGroupByCategory }}"
                                        data-default="{{ json_encode($arrProducts) }}"
                                ></select>
                                <input type="hidden" name="listProduct" value="{{ implode(',', $arrProducts) }}"/>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-30">
                        <div class="col-md-2 col-sm-2">
                            <strong>{{trans('group.lb_discount')}}</strong>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="@lang('group.discount_tooltip')"></i>
                        </div>
                        <div class="col-md-2 col-sm-2 no-discount">
                            <label>
                                <input type="radio" class="flat checkbox" name="discount_type" value="{{\App\Models\Group::NO_DISCOUNT}}"
                                       @if(isset($group) && $group->discount_type == \App\Models\Group::NO_DISCOUNT || !isset($group->discount_type)) checked @endif />
                                {{trans('group.no_discount')}}
                            </label>
                        </div>
                        <div class="col-md-1 col-sm-1 discount_type">
                            <input type="radio" class="flat checkbox" name="discount_type" value="{{\App\Models\Group::FIXED_AMOUNT}}"
                                   @if(isset($group) && $group->discount_type == \App\Models\Group::FIXED_AMOUNT) checked @endif />
                        </div>
                        <div class="col-md-2 col-sm-3">
                            <i class="fa fa-euro icon"></i>
                            <input class="form-control is-number" name="discount" type="text"
                                   value="{{ isset($group) ? $group->discount : NULL }}"/>
                        </div>

                        <div class="col-md-1 col-sm-1 discount_type">
                            <input type="radio" class="flat checkbox" name="discount_type" value="{{\App\Models\Group::PERCENTAGE}}"
                                   @if(isset($group) && $group->discount_type == \App\Models\Group::PERCENTAGE) checked @endif />
                        </div>
                        <div class="col-md-2 col-sm-3">
                            <i class="fa fa-percent icon"></i>
                            <input class="form-control" name="percentage" type="text"
                                   value="{{ isset($group) ? $group->percentage : NULL }}"/>
                        </div>
                    </div>

                    <h3>@lang('group.contactpersoon')</h3>

                    <div class="row contact mt-30">
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <input class="form-control" name="contact_email" type="email" placeholder="@lang('group.contact_email')"
                                       value="{{ isset($group) ? $group->contact_email : NULL }}"/>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" name="contact_surname" type="text" placeholder="@lang('group.contact_surname')"
                                               value="{{ isset($group) ? $group->contact_surname : NULL }}"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" name="contact_name" type="text" placeholder="@lang('group.contact_name')"
                                               value="{{ isset($group) ? $group->contact_name : NULL }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <input class="form-control keyup-gsm" name="contact_gsm" type="text" placeholder="@lang('group.contact_gsm')"
                                       value="{{ isset($group) ? $group->contact_gsm : NULL }}"/>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="ir-btn ir-btn-primary opslaan submit1 mgt-30 pull-right"
                            style="width:160px" aria-label="">
                        @lang('category.btn_opslaan')
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    {!! Form::close() !!}
@endif


