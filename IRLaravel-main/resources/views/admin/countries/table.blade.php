<table class="table table-responsive" id="countries-table">
    <thead>
        <tr>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Active</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($countries as $country)
        <tr>
            <td>{!! $country->created_at !!}</td>
            <td>{!! $country->updated_at !!}</td>
            <td>{!! $country->active !!}</td>
            <td>{!! $country->name !!}</td>
            <td>
                @if(Helper::checkUserPermission('admin.countries.show'))
                    <a href="{!! route('admin.countries.show', [$country->id]) !!}" class="btn btn-default btn-xs">
                        <i class="glyphicon glyphicon-eye-open"></i>
                    </a>
                @endif
                @if(Helper::checkUserPermission('admin.countries.edit'))
                    <a href="{!! route('admin.countries.edit', [$country->id]) !!}" class="btn btn-default btn-xs">
                        <i class="glyphicon glyphicon-edit"></i>
                    </a>
                @endif
                @if(Helper::checkUserPermission('admin.countries.destroy'))
                    {!! Form::open(['route' => ['admin.countries.destroy', $country->id], 'method' => 'delete']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    {!! Form::close() !!}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(!empty($countries))
    <div class="pagination-area text-center">
        {{ $countries->links() }}
    </div>
@endif