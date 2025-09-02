<table class="table table-responsive" id="banners-table">
    <thead>
    <tr>
        <th>#</th>
        <th>@lang('strings.banner.label_photo')</th>
        <th class="text-center">@lang('strings.banner.label_order')</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($banners as $banner)
        <tr data-id="{{ $banner->id }}">
            <td data-name="order">{!! $banner->order !!}</td>
            <td>
                <div style="max-width: 192px; max-height: 86px; overflow: hidden;">
                    {!! Html::image($banner->photo, null, ['style' => 'width: 100%;']) !!}
                    <div class="clearfix"></div>
                </div>
            </td>
            <td class="text-center">
                <button class="btn btn-default no-radius move up" onclick="return false;">@lang('strings.move_up')</button>
                <button class="btn btn-default no-radius move down" onclick="return false;">@lang('strings.move_down')</button>
            </td>
            <td>
                @if(Helper::checkUserPermission('admin.banners.edit'))
                    <a href="{!! route('admin.banners.edit', [$banner->id]) !!}" class='btn btn-default btn-xs'>
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                @endif

                @if(Helper::checkUserPermission('admin.banners.destroy'))
                    {!! Form::open(['route' => ['admin.banners.destroy', $banner->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    {!! Form::close() !!}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@push('scripts')
    <script>
        $(document).ready(function () {
            var tblBanners = $('#banners-table');
            // Init effect
            toggleEffect();

            tblBanners.on('click', 'tbody .move', function() {
                var row = $(this).closest('tr');

                if ($(this).hasClass('up')) {
                    row.prev().before(row);
                } else {
                    row.next().after(row);
                }

                // Change order
                changeOrder();
                toggleEffect();
            });

            /**
             * Change banner orders
             */
            function changeOrder() {
                var token = "<?php echo csrf_token() ?>";
                var idxOrders = {};
                var rows = tblBanners.find('tbody tr');

                rows.each(function () {
                    var row = $(this);
                    var order = row.index() + 1;
                    // Push to data order
                    idxOrders[row.data('id')] = order;
                    // Change order display
                    row.find('[data-name="order"]').text(order);
                });

                $.ajax({
                    type: 'put',
                    url: window.DOMAIN + 'admin/banners/change-order',
                    data: {
                        _token: token,
                        orders: idxOrders
                    },
                    success: function (data) {
                        // console.log('data:', data);
                    }
                });
            }

            /**
             * Toggle effect
             */
            function toggleEffect() {
                tblBanners.find('tbody tr .move').prop('disabled', false);
                tblBanners.find('tbody tr:first .move.up').prop('disabled', true);
                tblBanners.find('tbody tr:last .move.down').prop('disabled', true);
            }
        });
    </script>
@endpush