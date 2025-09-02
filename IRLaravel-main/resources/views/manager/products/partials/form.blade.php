@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'files' => TRUE, 'id' => $idForm]) !!}
        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <div class="modal-body">

            <div class="clear"></div>
            <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>

            <input type="hidden" name="order" value="{{ isset($product) ? $product->order : 100000000 }}" />
            <input type="hidden" name="timeSlots" value="{{ isset($openTimeRootCategory) ? json_encode($openTimeRootCategory) : NULL }}" />
            <input type="hidden" name="time_no_limit_category" value="{{ isset($timeNoLimitCategory) ? $timeNoLimitCategory : NULL }}" />

            <div id="data-show">
                <div class="row">
                    <div class="col-md-7">
                        <div class=" form-group">
                            <input class="form-control" name="name" type="text" placeholder="@lang('product.naam')"
                                   value="{{ isset($product) ? ($product->translate(app()->getLocale()) ? $product->translate(app()->getLocale())->name : $product->translate('en')->name) : NULL }}"/>
                        </div>

                        <div class=" form-group">
                            <textarea rows="5" class="form-control" name="description" placeholder="@lang('product.beschrijving')">{{
                                isset($product) ? ($product->translate(app()->getLocale()) ? $product->translate(app()->getLocale())->description : $product->translate('en')->description) : NULL
                            }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group has-feedback">
                                    <i class="fa fa-eur form-control-feedback"></i>
                                    <input class="form-control" name="price" type="number" placeholder="@lang('product.plh_price')"
                                           value="{{ isset($product) ? $product->price : NULL }}"/>
                                </div>
                                <input type="hidden" name="currency" value="{{ isset($product) ? $product->currency : "EUR" }}"/>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {{ Form::select(
                                        'vat_id',
                                        [NULL => trans('product.btw_type')] + $vats->pluck('fullname', 'id')->toArray(),
                                        old('vat_id') ?: (isset($product) ? $product->vat_id : NULL),
                                        ['class' => 'form-control']
                                    ) }}
                                </div>
                            </div>
                        </div>

                        <div class="row mgb-10">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    {{ Form::select(
                                        'category_id',
                                        [NULL => trans('product.categories')] + $categories->pluck('name', 'id')->toArray(),
                                        old('category_id') ?: (isset($product) ? $product->category_id : NULL),
                                        ['class' => 'form-control']
                                    ) }}
                                </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                @php
                                    $optionsRelation = isset($product) && $product->productOptions ? $product->productOptions : collect();
                                    $countOpties = $optionsRelation->where('is_checked', TRUE)->count();
                                @endphp
                                @include('manager.partials.opties', [
                                    'useCategoryOption' => isset($product) ? $product->use_category_option : FALSE,
                                    'optionsRelation'   => $optionsRelation,
                                    'textButton'        => $optionsRelation && $countOpties ? $countOpties . " " . trans('product.txt_selected') : trans('product.opties'),
                                    'fromProduct'       => true
                                ])
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-sm-10 col-xs-12">
                                <label class="font-size-14">
                                    @lang('product.uitsluiten')
                                </label>
                            </div>
                            <div class="col-sm-2 col-xs-12 text-right">
                                <input type="checkbox" name="use_category_option" id="use_category_option" value="1"
                                       class="switch-input" {{ isset($product) && $product->use_category_option ? "checked" : "" }}>
                                <label for="use_category_option" class="switch"></label>
                            </div>
                        </div>

                        @if ($wSExtraAllergenen && $wSExtraAllergenen->active)
                            <h3>@lang('product.allergenen')</h3>

                            <div class="form-group allergenen">
                                <ul>
                                    @php
                                        $allergenenIds = isset($product) ? $product->productAllergenens->pluck('allergenen_id')->toArray() : [];
                                    @endphp

                                    @foreach($allergenens as $item)
                                        <li>
                                            <input type="checkbox" name="allergenens[]" id="allergenen-{{ $item->id }}" value="{{ $item->id }}"
                                                {{ in_array($item->id, $allergenenIds) ? "checked" : "" }}
                                            >
                                            <label for="allergenen-{{ $item->id }}">
                                                <img src="{{ asset(in_array($item->id, $allergenenIds) ? str_replace("/gray/", "/hover/", $item->icon) : $item->icon) }}">
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @include('manager.partials.upload-avatar', ['media' => $media ?? NULL])

                        @if(!empty($connectorsList) && !$connectorsList->isEmpty())
                            <h3>@lang('product.connectors')</h3>

                            <div class="row">
                                @foreach($connectorsList as $connectorItem)
                                    @php

                                    $productReference = null;
                                    if(!empty($productReferences)):
                                        $productReference = $productReferences->get($connectorItem->provider);
                                    endif;

                                    @endphp

                                    <div class="col-sm-6">
                                        <strong>{{ $connectorItem->getProviders($connectorItem->provider) }}</strong>

                                        <div class=" form-group">
                                            <input class="form-control" name="productReferences[{{ $connectorItem->id }}][remote_id]" type="text" placeholder="@lang('product.remote_id')"
                                                   value="{{ !empty($productReference->remote_id) ? $productReference->remote_id : null }}"/>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="col-md-5">

                        @include('manager.partials.beschikbaarheid', [
                            'timeNoLimit' => isset($product) ? $product->time_no_limit : 0,
                            'openTime'    => isset($openTime) ? $openTime : NULL,
                        ])

                        <div>
                            <h3>@lang('product.labels')</h3>
                            <div class="row form-group mgr-bottom-3 line-bottom">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('product.veggie')
                                    </label>
                                </div>
                                @php($labelsActive = isset($product) ? $product->productLabels->where('active', 1) : collect())
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="veggie" id="veggie" value="{{ \App\Models\ProductLabel::VEGGIE }}" class="switch-input"
                                        {{ (isset($product) && $labelsActive->where('type', \App\Models\ProductLabel::VEGGIE)->count() > 0) ? "checked" : "" }}>
                                    <label for="veggie" class="switch"></label>
                                </div>
                            </div>
                            <div class="row form-group mgr-bottom-3 line-bottom">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('product.vegan')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="vegan" id="vegan" value="{{ \App\Models\ProductLabel::VEGAN }}" class="switch-input"
                                        {{ (isset($product) && $labelsActive->where('type', \App\Models\ProductLabel::VEGAN)->count() > 0) ? "checked" : "" }}>
                                    <label for="vegan" class="switch"></label>
                                </div>
                            </div>
                            <div class="row form-group mgr-bottom-3 line-bottom">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('product.spicy')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="spicy" id="spicy" value="{{ \App\Models\ProductLabel::SPICY }}" class="switch-input"
                                        {{ (isset($product) && $labelsActive->where('type', \App\Models\ProductLabel::SPICY)->count() > 0) ? "checked" : "" }}>
                                    <label for="spicy" class="switch"></label>
                                </div>
                            </div>
                            <div class="row form-group mgr-bottom-3 line-bottom">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('product.new')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="new" id="new" value="{{ \App\Models\ProductLabel::NEWW }}" class="switch-input"
                                        {{ (isset($product) && $labelsActive->where('type', \App\Models\ProductLabel::NEWW)->count() > 0) ? "checked" : "" }}>
                                    <label for="new" class="switch"></label>
                                </div>
                            </div>
                            <div class="row form-group line-bottom">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('product.promo')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="promo" id="promo" value="{{ \App\Models\ProductLabel::PROMO }}" class="switch-input"
                                        {{ (isset($product) && $labelsActive->where('type', \App\Models\ProductLabel::PROMO)->count() > 0) ? "checked" : "" }}>
                                    <label for="promo" class="switch"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="ir-btn ir-btn-primary pull-right opslaan submit1" style="width:160px" aria-label="">
            @lang('category.btn_opslaan')
        </button>

        <div class="clearfix"></div>
    {!! Form::close() !!}
@endif


