@extends('layouts.manager')

@section('content')
    <div class="row layout-manager products">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('product.title')
                    </h2>
                </div>

                <div class="search-and-button">
                    <ul class="nav navbar-left panel_toolbox">
                        <li>
                            @include($guard.'.products.partials.quick_search')
                        </li>
                    </ul>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a data-route="{!! route('manager.products.create') !!}"
                               class="ir-btn ir-btn-primary btnCreate">
                                <i class="ir-plus"></i> @lang('product.add')
                            </a>
                        </li>
                        <li>
                            <span class="lb_order">@lang('product.sort_by')</span>
                        </li>
                        <li class="mgr-20">
                            <div class="row col-md-12 mgr-bottom-20 dropdown-sort">
                                <button data-type="naam" href="javascript:;" class="ir-btn ir-btn-default dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                                    @lang('product.naam')
                                    <i class=" fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right ir-dropdown-actions">
                                    <li>
                                        <a data-value="asc" href="javascript:;">@lang('product.oplopend')</a>
                                    </li>
                                    <li>
                                        <a data-value="desc" href="javascript:;">@lang('product.aflopend')</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div class="row col-md-12 mgr-bottom-20 dropdown-sort">
                                <button data-type="prijs" href="javascript:;" class="ir-btn ir-btn-default dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                                    @lang('product.prijs')
                                    <i class=" fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right ir-dropdown-actions">
                                    <li>
                                        <a data-value="asc" href="javascript:;">@lang('product.oplopend1')</a>
                                    </li>
                                    <li>
                                        <a data-value="desc" href="javascript:;">@lang('product.aflopend2')</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>

                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>

                @if (count($categoriesWithProducts) > 0)
                    <div class="ir-content mgb-30 border-shadow">
                        <div class="accordion">
                            @php($firstItemCount = 0)
                            @php($k = 0)

                            @foreach($categoriesWithProducts as $categoryId => $products)

                                <?php
                                    if (\request()->has('category_id_accordion') && \request()->get('category_id_accordion') == $categoryId) {
                                        if (\request()->get('naam') === "desc" ) {
                                            $products = $products->sortByDesc('product_name');
                                        }
                                        if (\request()->get('naam') === "asc" ) {
                                            $products = $products->sortBy('product_name');
                                        }
                                        if (\request()->get('prijs') === "desc" ) {
                                            $products = $products->sortByDesc('price');
                                        }
                                        if (\request()->get('prijs') === "asc" ) {
                                            $products = $products->sortBy('price');
                                        }
                                    }
                                ?>

                                @php($productGroup = $products->where('id', '<>', NULL)->groupBy('id'))

                                @if (!$k)
                                    @php ($firstItemCount = $productGroup->count())
                                @endif

                                @include('manager.products.partials.table', [
                                    'products'     => $products,
                                    'productGroup' => $productGroup,
                                ])

                                @php($k++)
                            @endforeach
                        </div>
                    </div>
                @endif

                @include('manager.products.partials.modal')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on("change", ".select2-tags", function(e) {
            $('input[name=listProduct]').val($(this).val());
        });

        @if (isset($firstItemCount) && $firstItemCount === 0)
            $('.accordion .content-wrap').hide();
        @endif
        @if ($categoryIdAccordion)
            $('.accordion .list-product-{{ $categoryIdAccordion }}').prev().trigger('click');
        @endif


        $(document).on('click', ".dropdown-sort a", function() {
            var valSort = $(this).data('value');
            var type = $(this).parents('.dropdown-sort').find('button').data('type');
            var formSearch = $('.ir-group-search');
            var id = null;

            $('.layout-manager.products .ui-accordion-header').each(function(k, v) {
                if ($(v).attr("aria-expanded") === "true") {
                    id = $(v).data('id');
                }
            });

            formSearch.find('input').val(null);
            formSearch.find('input[name=category_id_accordion]').val(id);
            formSearch.find('input[name=' + type + ']').val(valSort).parents('form').submit();
        });

        function initObject(option = {}) {
            var useCategoryOption = $('input[name=use_category_option]:checked').val() === undefined
                ? 0
                : 1;

            $('.ui-sortable').sortable({
                cancel: useCategoryOption ? "" : ".ui-sortable-handle",
            });


            $(option.icheck).iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            $(".select2-tags").select2({
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: '0 {{ trans('product.txt_selected') }}'
            });

            MainManager.fn.disabledTimeNoLimit("input[name=time_no_limit]:checked");

            MainManager.fn.timeNoLimit.call(this);

            MainManager.fn.countOptionSelected.call(this);

            MainManager.fn.getJsonDataOpties.call(this);

            MainManager.fn.submitForm.call(this);
        }

        $(document).on('click', '.form-group.allergenen li label', function() {
            var src = $(this).find('img').attr('src');
            var valInput = $(this).prev().is(':checked');
            if (valInput) {
                src = src.replace('/hover/', '/gray/');
            } else {
                src = src.replace('/gray/', '/hover/');
            }
            $(this).find('img').attr('src', src);
        });

        /**
         * Auto save price and name product
         */
        $(document).on('change', '.input-ajax', function() {
            $('body').loading('toggle');
            var val = $(this).val();

            if($(this).hasClass('is-number')) {
                val = val.replace(',', '.');
            }

            var urlAction = $(this).parents('.data-item').data('route') + "?_method=PUT&" + $(this).attr('name') + "=" + encodeURIComponent(val);
            $.ajax({
                type: 'POST',
                url: urlAction,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response);
                },
                error: function (response) {
                    console.log(response);
                }
            }).always(function () {
                $('body').loading('toggle');
            });
        });

        /**
         * Get opties
         */
        $(document).on('change', 'select[name=category_id], input[name=use_category_option]', function() {
            var categoryId = $('select[name=category_id]').val();
            var useCategoryOption = $('input[name=use_category_option]:checked').val() === undefined ? 0 : 1;

            if (!categoryId) {
                $('input[name=orderOptions]').val(null);
                $('input[name=timeSlots]').val(null);
                $('input[name=time_no_limit_category]').val(null);
                $('.beschikbaarheid #altijd').iCheck('check');
            }

            $.ajax({
                type: 'GET',
                url: "/manager/products/get-option-by-category/" + categoryId + "/" + useCategoryOption,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    var dropMenu = $('.dropdown-options .dropdown-menu');
                    dropMenu.html(response.data.dropdown);

                    $('.dropdown-options button .selected-count')
                        .text(response.data.numberChecked + " {{ trans('product.txt_selected') }}");

                    $('input[name=orderOptions]').val(JSON.stringify(MainManager.fn.getOrder()));
                    $('input[name=timeSlots]').val(response.data.timeSlots);
                    $('input[name=time_no_limit_category]').val(response.data.timeNoLimitCategory);

                    if (response.data.timeNoLimitCategory === 0) {
                        $('.beschikbaarheid #altijd').iCheck('check');
                    }
                    if (response.data.timeNoLimitCategory === 1) {
                        $('.beschikbaarheid #specifieke').iCheck('check');
                    }

                    var timeSlot = JSON.parse(response.data.timeSlots);
                    $('.beschikbaarheid .days li').each(function(k, v) {
                        var objTime   = timeSlot[k + 1];
                        var checkbox  = $(this).find('input[type=checkbox]');
                        var startTime = $(this).find('.start_time');
                        var endTime   = $(this).find('.end_time');

                        // reset
                        startTime.val("00:00");
                        endTime.val("23:59");
                        checkbox.iCheck('uncheck');

                        if (objTime !== undefined) {
                            startTime.val(objTime.start_time_convert);
                            endTime.val(objTime.end_time_convert);

                            if (objTime.status) {
                                checkbox.iCheck('check');
                            } else {
                                checkbox.iCheck('uncheck');
                            }
                        }
                    });

                    initObject({icheck: ".dropdown-options .flat"});
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });

        /**
         * Open modal create product
         */
        MainManager.fn.ajaxShowFormCreate(function (response) {
            $('#newCategoryModal .modal-content').html(response.data);
            $('#newCategoryModal').modal('show');
            initObject({icheck: ".flat"});
        });

        /**
         * Get detail product
         */
        MainManager.fn.ajaxGetDetail(function (response) {
            $('#newCategoryModal .modal-content').html(response.data);
            $('#newCategoryModal').modal('show');
            initObject({icheck: ".flat"});
        });
    </script>
@endpush