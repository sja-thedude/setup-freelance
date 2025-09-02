@php
    $isShowSticker = !empty($tmpWorkspace->workspaceExtras) ? $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::STICKER)->first() : null
@endphp

@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'files' => TRUE, 'id' => $idForm]) !!}
    <button type="button" class="close" data-dismiss="modal">&times;</button>

    <div class="modal-body">

        <div class="clear"></div>
        <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>

        <input type="hidden" name="order" value="{{ isset($category) ? $category->order : 100000000 }}"/>

        <div id="data-show">
            <div class="row">
                <div class="col-md-7">

                    @include('manager.partials.upload-avatar', ['media' => $media ?? NULL])

                    <div class=" form-group">
                        <input class="form-control" name="name" type="text" placeholder="@lang('category.naam')"
                               value="{{ isset($category) ? ($category->translate(app()->getLocale()) ? $category->translate(app()->getLocale())->name : $category->translate('en')->name) : NULL }}"/>
                    </div>

                    <h3>@lang('category.category_opties')</h3>

                    <div class="row col-md-12 mgr-bottom-20">
                        @php($optionsRelation = isset($category) && $category->categoryOptions ? $category->categoryOptions : collect())
                        @include('manager.partials.opties', [
                            'useCategoryOption' => TRUE,
                            'optionsRelation'   => $optionsRelation,
                            'textButton'        => ($optionsRelation ? $optionsRelation->where('is_checked', TRUE)->count() : 0) . " " . trans('category.txt_selected'),
                        ])
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-10 col-xs-12">
                            <label class="font-size-14">
                                @lang('category.available_delivery')
                            </label>
                        </div>
                        <div class="col-sm-2 col-xs-12 text-right">
                            <input type="checkbox" name="available_delivery" id="available_delivery" value="1"
                                   class="switch-input"
                                    {{ isset($category) && $category->available_delivery ? "checked" : "" }}>
                            <label for="available_delivery" class="switch"></label>
                        </div>
                    </div>

                    @if(!empty($enableInHouse))
                    <div class="row form-group" data-type="table_ordering">
                        <div class="col-sm-10 col-xs-12">
                            <label class="font-size-14">
                                @lang('category.available_in_house')
                            </label>
                        </div>
                        <div class="col-sm-2 col-xs-12 text-right">
                            <input type="hidden" name="available_in_house" value="0">
                            <input type="checkbox" name="available_in_house" id="available_in_house" value="1" class="switch-input"
                                {{ isset($category) && $category->available_in_house ? "checked" : "" }}>
                            <label for="available_in_house" class="switch update-status-manual"></label>
                        </div>
                    </div>

                    <div class="row form-group" data-type="self_service" style=" @if(empty($category->available_in_house)) display: none; @endif margin-top: -12px; ">
                        <div class="col-sm-10 col-xs-12">
                            <label class="font-size-13" style="padding-left: 10px;">@lang('category.exclusively_in_house')</label>
                        </div>
                        <div class="col-sm-2 col-xs-12 text-right">
                            <input type="hidden" name="exclusively_in_house" value="0">
                            <input type="checkbox" name="exclusively_in_house" id="exclusively_in_house" value="1" class="switch-input"
                                {{ isset($category) && $category->exclusively_in_house ? "checked" : "" }}>
                            <label for="exclusively_in_house" class="switch update-status-manual"></label>
                        </div>
                    </div>
                    @endif

                    @if ($roleAccount === \App\Models\Role::ROLE_ADMIN)
                        <div class="row">
                            <div class="col-sm-5 col-xs-12">
                                <div class="row form-group">
                                    <div class="col-sm-8 col-xs-12">
                                        <label class="font-size-14">
                                            @lang('category.favoriet_friet')
                                        </label>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 text-right">
                                        <input type="checkbox" name="favoriet_friet" id="favoriet_friet" value="1"
                                               class="switch-input"
                                                {{ (isset($category) && $category->favoriet_friet) ? "checked" : "" }}>
                                        <label for="favoriet_friet" class="switch"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-7 col-xs-12 text-right">
                                <div class="row form-group">
                                    <div class="col-sm-8 col-xs-12">
                                        <label class="font-size-14">
                                            @lang('category.kokette_kroket')
                                        </label>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 text-right">
                                        <input type="checkbox" name="kokette_kroket" id="kokette_kroket" value="1"
                                               class="switch-input"
                                                {{ (isset($category) && $category->kokette_kroket) ? "checked" : "" }}>
                                        <label for="kokette_kroket" class="switch"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group row mt-30">
                        <div class="col-sm-12">
                            <label class="font-size-14">@lang('category.is_suggestion_product')</label>
                        </div>
                        <div class="col-sm-12">
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

                    <div class=" form-group wrapSelectProduct">
                        <?php
                        $products = array();
                        if (isset($category)) {
                            foreach ($category->productSuggestions as $productSuggestion) {
                                $products[] = $productSuggestion->product_id;
                            }
                        }
                        ?>
                        <select class="form-control select2-tags" name="category[]" multiple="multiple"
                                id="selectFeaturedProducts"
                                data-json="{{ $productsGroupByCategory }}"
                                data-default="{{ json_encode($products) }}"
                        ></select>
                        <input type="hidden" name="listProduct" value="{{ implode(',', $products) }}"/>
                    </div>
                </div>
                <div class="col-md-5">
                    @include('manager.partials.beschikbaarheid', [
                        'timeNoLimit' => isset($category) ? $category->time_no_limit : 0,
                        'openTime'    => isset($openTime) ? $openTime : NULL,
                    ])

                    <div class="row mgb-15">
                        <div class="col-sm-12 col-xs-12">
                            <h3>@lang('category.print_category_separately')?</h3>
                            <div class="row form-group mgr-bottom-3">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('category.on_extra_werkbon')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="extra_werkbon" id="extra_werkbon" value="1"
                                           class="switch-input"
                                            {{ (isset($category) && $category->extra_werkbon) ? "checked" : "" }}>
                                    <label for="extra_werkbon" class="switch"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (isset($tmpWorkspace) && $isShowSticker && $isShowSticker->active)
                        <div>
                            <h3>@lang('category.categorie_afdrukken_op_sticker')</h3>
                            <div class="row form-group mgr-bottom-3">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('category.individueel')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="individual" id="individual" value="1"
                                           class="switch-input"
                                            {{ (isset($category) && $category->individual) ? "checked" : "" }}>
                                    <label for="individual" class="switch"></label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-8 col-xs-12">
                                    <label class="font-size-16">
                                        @lang('category.groep')
                                    </label>
                                </div>
                                <div class="col-sm-4 col-xs-12 text-right">
                                    <input type="checkbox" name="group" id="group" value="1"
                                           class="switch-input" {{ $groupOrder && $groupOrder->active ? "" : "disabled" }}
                                            {{ $groupOrder && $groupOrder->active ? ((isset($category) && $category->group) ? "checked" : "") : "" }}>
                                    <label for="group" class="switch"></label>
                                </div>
                            </div>
                        </div>
                    @endif
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

@push('scripts')
    <script>
        (function ($) {
            $('#newCategoryModal').on('change', '[data-type="table_ordering"] input.switch-input', function () {
                let input = $(this);
                let checked = input.is(':checked');

                let parentContainer = input.closest('[data-type="table_ordering"]');
                let selfServiceContainer = parentContainer.next('[data-type="self_service"]');

                if (checked) {
                    selfServiceContainer.show();
                } else {
                    selfServiceContainer.hide();
                    // Disable self_service when switch table_ordering is off
                    if (selfServiceContainer.find('input.switch-input').is(':checked')) {
                        selfServiceContainer.find('.update-status-manual').click();
                    }
                }
            });
        })(jQuery);
    </script>
@endpush