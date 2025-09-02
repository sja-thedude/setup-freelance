@extends('layouts.manager')

@section('content')
    <div class="row layout-manager products options">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting.connectors.manager.test')
                    </h2>
                </div>

                <table class="table table-border table">
                    <thead>
                        <tr>
                            <th>@lang('setting.connectors.manager.provider')</th>
                            <th>@lang('setting.connectors.manager.endpoint')</th>
                        </tr>

                    </thead>

                    <tbody>
                        <tr>
                            <td>{{ $settingConnector->getProviders($settingConnector->provider) }}</td>
                            <td>{{ $settingConnector->endpoint }}</td>
                        </tr>
                    </tbody>
                </table>

                <hr />

                <div class="ir-content">
                    <div class="action-test">
                        <h3>@lang('setting.connectors.manager.connection_test')</h3>

                        <p>
                            <br />
                            <strong>@lang('setting.connectors.manager.status')</strong>: <span class="output-test-status">@lang('setting.connectors.manager.status_checking')</span><br />
                            <strong>@lang('setting.connectors.manager.message')</strong>: <span class="output-test-status-message"></span><br />

                        </p>
                    </div>

                    <div class="action-mapping-data" style="display: none;">
                        <hr />

                        <div class="action-payment-methods loading">
                            <h3>@lang('setting.connectors.manager.connection_payment_methods.title')</h3> <a href="javascript:selectElementContents(document.getElementById('action-payment-methods-output'))">Select all</a>

                            <table class="table" id="action-payment-methods-output">
                                <thead>
                                    <tr>
                                        <th>@lang('setting.connectors.manager.connection_payment_methods.id')</th>
                                        <th>@lang('setting.connectors.manager.connection_payment_methods.name')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr class="empty">
                                        <td colspan="3" class="text-center">@lang('setting.connectors.manager.connection_payment_methods.empty')</td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr />
                        </div>

                        <div class="action-products loading">
                            <h3>@lang('setting.connectors.manager.connection_products.title')</h3> <a href="javascript:selectElementContents(document.getElementById('action-products-output'))">Select all</a>

                            <table class="table" id="action-products-output">
                                <thead>
                                    <tr>
                                        <th>@lang('setting.connectors.manager.connection_products.id')</th>
                                        <th>@lang('setting.connectors.manager.connection_products.name')</th>
                                        <th>@lang('setting.connectors.manager.connection_products.price')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr class="empty">
                                        <td colspan="3" class="text-center">@lang('setting.connectors.manager.connection_products.empty')</td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr />
                        </div>
                    </div>

                    <br><br><br>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        function selectElementContents(el) {
            var body = document.body, range, sel;
            if (document.createRange && window.getSelection) {
                range = document.createRange();
                sel = window.getSelection();
                sel.removeAllRanges();
                try {
                    range.selectNodeContents(el);
                    sel.addRange(range);
                } catch (e) {
                    range.selectNode(el);
                    sel.addRange(range);
                }
            } else if (body.createTextRange) {
                range = body.createTextRange();
                range.moveToElementText(el);
                range.select();
            }
        }

        function doAjaxCall(action, callback) {
            // Do test call
            $.ajax({
                type: 'POST',
                url: {!! json_encode(route($guard.'.settings.connector.test_ajax', ['id' => $settingConnector->id])) !!},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                cache: false,
                data: {
                    action: action
                },
                success: function (response) {
                    callback(response);
                },
                error: function (response) {
                    callback(response);
                }
            });
        }

        function doTest(response) {
            let testSuccess = false;

            if(
                typeof response.data.IsSuccessStatusCode != 'undefined'
                && response.data.IsSuccessStatusCode === 'true'
            ) {
                testSuccess = true;

                $('.output-test-status').html({!! json_encode(trans('setting.connectors.manager.status_successfully')) !!});
                $('.action-test').removeClass('text-danger').addClass('text-success');
            }
            else {
                $('.output-test-status').html({!! json_encode(trans('setting.connectors.manager.status_failed')) !!});
                $('.action-test').removeClass('text-success').addClass('text-danger');
            }

            $('.output-test-status-message').html(response.data.ErrorMessage);

            return testSuccess;
        }

        function paymentMethods(response) {
            if(
                typeof response.data.IsSuccessStatusCode != 'undefined'
                && response.data.IsSuccessStatusCode === 'true'
                && response.data.Payments.length > 0
            ) {
                $('#action-payment-methods-output tbody .empty').remove();

                $.each(response.data.Payments, function(index, item) {
                    let row = '<tr><td>'+item.Number+'</td><td>'+item.Name+'<td></tr>';
                    $('#action-payment-methods-output tbody').append(row);
                });
            }
        }

        function products(response) {
            if(
                typeof response.data.IsSuccessStatusCode != 'undefined'
                && response.data.IsSuccessStatusCode === 'true'
                && response.data.AllProducts.length > 0
            ) {
                $('#action-products-output tbody .empty').remove();

                $.each(response.data.AllProducts, function(index, item) {
                    let row = '<tr><td>'+item.Id+'</td><td>'+item.Name+'</td><td>&euro; '+item.Price+'<td></tr>';
                    $('#action-products-output tbody').append(row);
                });
            }
        }

        $(document).ready(function() {
            doAjaxCall('test', function(response) {
                if(doTest(response)) {
                    // Show data
                    $('.action-mapping-data').show();

                    // Grab payment methods
                    doAjaxCall('payment-methods', function(response) {
                        paymentMethods(response);

                        // Grab products
                        doAjaxCall('products', function(response) {
                            products(response);
                        });
                    });
                }
            });
        });
    </script>
@endpush