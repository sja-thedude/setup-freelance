<table class="table table-responsive table-bordered jambo_table" id="coupons-table">
    <thead>
        <tr class="headings">
            <th>Created At</th>
        <th>Updated At</th>
        <th>Active</th>
        <th>Workspace Id</th>
        <th>Code</th>
        <th>Max Time All</th>
        <th>Max Time Single</th>
        <th>Currency</th>
        <th>Discount</th>
        <th>Expire Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($coupons as $coupon)
        <tr>
            <td>{!! $coupon->created_at !!}</td>
            <td>{!! $coupon->updated_at !!}</td>
            <td>{!! $coupon->active !!}</td>
            <td>{!! $coupon->workspace_id !!}</td>
            <td>{!! $coupon->code !!}</td>
            <td>{!! $coupon->max_time_all !!}</td>
            <td>{!! $coupon->max_time_single !!}</td>
            <td>{!! $coupon->currency !!}</td>
            <td>{!! $coupon->discount !!}</td>
            <td>{!! $coupon->expire_time !!}</td>
            <td>
                <a href="{!! route('admin.coupons.show', [$coupon->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.coupons.edit', [$coupon->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.coupons.destroy', $coupon->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>