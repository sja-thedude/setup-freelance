<table class="table table-responsive table-bordered jambo_table" id="addresses-table">
    <thead>
        <tr class="headings">
            <th>City Id</th>
        <th>Postcode</th>
        <th>Address</th>
        <th>Created At</th>
        <th>Updated At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($addresses as $address)
        <tr>
            <td>{!! $address->city_id !!}</td>
            <td>{!! $address->postcode !!}</td>
            <td>{!! $address->address !!}</td>
            <td>{!! $address->created_at !!}</td>
            <td>{!! $address->updated_at !!}</td>
            <td>
                <a href="{!! route('admin.addresses.show', [$address->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.addresses.edit', [$address->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.addresses.destroy', $address->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>