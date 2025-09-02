@extends('layouts.manager')

@section('content')
    <div class="row layout-manager products options">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('option.title')
                    </h2>
                </div>

                <div class="search-and-button">
                    <ul class="nav navbar-left panel_toolbox">
                        <li>
                            @include($guard.'.options.partials.quick_search')
                        </li>
                    </ul>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a data-route="{!! route('manager.options.create') !!}"
                               class="ir-btn ir-btn-primary btnCreate">
                                <i class="ir-plus"></i> @lang('option.add')
                            </a>
                        </li>
                    </ul>

                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>

                <div class="ir-content">
                    @include('manager.options.partials.table')
                    <br><br><br>
                </div>

                @include('manager.options.partials.modal')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        $(document).on('click', '.removeItem', function() {
            if ($(this).parents('.ui-sortable').find('.opties-sortable-handle').length > 1) {
                $(this).parents('.opties-sortable-handle').remove();
            } else {
                Swal.fire({
                    title: "{{ trans('option.item_option_required') }}",
                    type: "error",
                });
            }
        })

        function ajaxSuccess() {
            $('#optie-items').sortable({
                handle: ".naam_keuzeoptie .icon"
            })

            $('.ui-sortable.list-body-manager').sortable();

            $('[data-toggle="tooltip"]').tooltip({html: true});

            $(".flat").iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            MainManager.fn.submitForm.call(this);

            MainManager.fn.masterOptions.call(this);
        }

        /**
         * Create form item of option
         */
        $(document).on('click', '#createFormItem', function() {
            $.ajax({
                type: 'GET',
                url: $(this).data('route'),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    $('#optie-items').append(response.data);
                    ajaxSuccess();
                },
                error: function (response) {
                    console.log(response.responseText);
                }
            });
        });

        /**
         * Open modal create options
         */
        MainManager.fn.ajaxShowFormCreate(function (response) {
            $('#newCategoryModal .modal-content').html(response.data);
            $('#newCategoryModal').modal('show');
            ajaxSuccess();
        });

        /**
         * Get detail options
         */
        MainManager.fn.ajaxGetDetail(function (response) {
            $('#newCategoryModal .modal-content').html(response.data);
            $('#newCategoryModal').modal('show');
            ajaxSuccess();
        });
    </script>
@endpush