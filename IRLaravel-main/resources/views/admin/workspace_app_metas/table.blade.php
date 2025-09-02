<table class="table table-responsive table-bordered jambo_table" id="workspaceAppMetas-table">
    <thead>
        <tr class="headings">
            <th>Created At</th>
        <th>Updated At</th>
        <th>Active</th>
        <th>Order</th>
        <th>Workspace App Id</th>
        <th>Default</th>
        <th>Name</th>
        <th>Title</th>
        <th>Description</th>
        <th>Content</th>
        <th>Icon</th>
        <th>Url</th>
        <th>Meta Data</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($workspaceAppMetas as $workspaceAppMeta)
        <tr>
            <td>{!! $workspaceAppMeta->created_at !!}</td>
            <td>{!! $workspaceAppMeta->updated_at !!}</td>
            <td>{!! $workspaceAppMeta->active !!}</td>
            <td>{!! $workspaceAppMeta->order !!}</td>
            <td>{!! $workspaceAppMeta->workspace_app_id !!}</td>
            <td>{!! $workspaceAppMeta->default !!}</td>
            <td>{!! $workspaceAppMeta->name !!}</td>
            <td>{!! $workspaceAppMeta->title !!}</td>
            <td>{!! $workspaceAppMeta->description !!}</td>
            <td>{!! $workspaceAppMeta->content !!}</td>
            <td>{!! $workspaceAppMeta->icon !!}</td>
            <td>{!! $workspaceAppMeta->url !!}</td>
            <td>{!! $workspaceAppMeta->meta_data !!}</td>
            <td>
                <a href="{!! route('admin.workspaceAppMetas.show', [$workspaceAppMeta->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.workspaceAppMetas.edit', [$workspaceAppMeta->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.workspaceAppMetas.destroy', $workspaceAppMeta->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>