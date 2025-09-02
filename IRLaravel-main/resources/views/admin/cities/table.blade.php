<table class="table table-responsive table-bordered jambo_table" id="cities-table">
    <thead>
        <tr class="headings">
            <th>Country Id</th>
        <th>Name</th>
        <th>Created At</th>
        <th>Updated At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($cities as $city)
        <tr>
            <td>{!! $city->country_id !!}</td>
            <td>{!! $city->name !!}</td>
            <td>{!! $city->created_at !!}</td>
            <td>{!! $city->updated_at !!}</td>
            <td>
                <a href="{!! route('admin.cities.show', [$city->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.cities.edit', [$city->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.cities.destroy', $city->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>