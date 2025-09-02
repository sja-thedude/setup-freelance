<table class="table table-responsive table-bordered jambo_table" id="workspaceApps-table">
    <thead>
        <tr class="headings">
            <th>Created At</th>
        <th>Updated At</th>
        <th>Active</th>
        <th>Workspace Id</th>
        <th>Theme</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($workspaceApps as $workspaceApp)
        <tr>
            <td>{!! $workspaceApp->created_at !!}</td>
            <td>{!! $workspaceApp->updated_at !!}</td>
            <td>{!! $workspaceApp->active !!}</td>
            <td>{!! $workspaceApp->workspace_id !!}</td>
            <td>{!! $workspaceApp->theme !!}</td>
            <td>
                <a href="{!! route('admin.workspaceApps.show', [$workspaceApp->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.workspaceApps.edit', [$workspaceApp->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.workspaceApps.destroy', $workspaceApp->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>