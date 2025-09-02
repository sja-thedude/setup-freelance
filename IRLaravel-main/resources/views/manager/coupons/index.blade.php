@extends('layouts.manager')

@section('content')
    <div class="row layout-manager products options groups coupons">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('coupon.title')
                    </h2>
                </div>

                <div class="search-and-button">
                    <ul class="nav navbar-left panel_toolbox">
                        <li>
                            @include($guard.'.coupons.partials.quick_search')
                        </li>
                    </ul>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a data-route="{!! route('manager.coupons.create') !!}"
                               class="ir-btn ir-btn-primary btnCreate">
                                <i class="ir-plus"></i> @lang('coupon.add')
                            </a>
                        </li>
                    </ul>

                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>

                <div class="ir-content">
                    @include('manager.coupons.partials.table')
                    <br><br><br>
                </div>

                @include('manager.coupons.partials.modal')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function ajaxSuccess(response) {
            $('#newCategoryModal .modal-content').html(response.data);
            $('#newCategoryModal').modal('show');

            $(".flat").iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            $(".select2-tags").select2({
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: '0 {{ trans('category.txt_selected') }}'
            });

            MainManager.fn.countOptionSelected.call(this);

            MainManager.fn.submitForm.call(this);

            MainShared.fn.datePicker.call(this);

            MainManager.fn.autoDetectTimeZone.call(this);

            CustomSelect2.fn.action.call(this);

            MainManager.fn.selectpicker.call(this);
        }

        /**
         * Open modal create coupon
         */
        MainManager.fn.ajaxShowFormCreate(function (response) {
            ajaxSuccess(response);
        });

        /**
         * Get detail coupon
         */
        MainManager.fn.ajaxGetDetail(function (response) {
            ajaxSuccess(response);
        });
    </script>
@endpush
