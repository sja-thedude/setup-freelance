@extends('layouts.manager')

@section('content')
    <div class="row layout-manager categories">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('category.title')
                    </h2>
                </div>

                <div class="search-and-button">
                    <ul class="nav navbar-left panel_toolbox">
                        <li>
                            @include($guard.'.categories.partials.quick_search')
                        </li>
                    </ul>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a data-route="{!! route('manager.categories.create') !!}"
                               class="ir-btn ir-btn-primary btnCreate">
                                <i class="ir-plus"></i> @lang('category.add')
                            </a>
                        </li>

                        <li>
                            <a href="{{ route($guard . '.excel.export.category') }}" class="ir-btn ir-btn-default">
                                @lang('category.export')
                            </a>
                        </li>

                        {!! Form::open(['url' => route($guard . '.excel.import.category'), 'method' => "POST", 'files' => TRUE, 'id' => 'formImportExcel']) !!}
                            <li class="mgr-20">
                                <input type="file" name="file" class="hidden" id="importExcel">
                                <label for="importExcel" class="ir-btn ir-btn-default">
                                    @lang('category.import')
                                </label>
                            </li>
                        {!! Form::close() !!}
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>

                @if (count($categories) > 0)
                    <div class="ir-content border-shadow">
                        @include('manager.categories.partials.table')
                    </div>
                @endif

                @include('manager.categories.partials.modal')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on("change", "#importExcel", function(e) {
            $('#formImportExcel').submit();
        });

        function ajaxSuccess(response) {
            $('#newCategoryModal .modal-content').html(response.data);
            $('#newCategoryModal').modal('show');

            $('.ui-sortable').sortable();

            $('.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            $(".select2-tags").select2({
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: '0 {{ trans('category.txt_selected') }}'
            });

            MainManager.fn.disabledTimeNoLimit("input[name=time_no_limit]:checked");

            MainManager.fn.timeNoLimit.call(this);

            MainManager.fn.countOptionSelected.call(this);

            MainManager.fn.getJsonDataOpties.call(this);

            MainManager.fn.submitForm.call(this);

            CustomSelect2.fn.action.call(this);

            MainManager.fn.selectpicker.call(this);
        }

        /**
         * Open modal create categories
         */
        MainManager.fn.ajaxShowFormCreate(function (response) {
            ajaxSuccess(response);
        });

        /**
         * Get detail categories
         */
        MainManager.fn.ajaxGetDetail(function (response) {
            ajaxSuccess(response);
        });
    </script>
@endpush
