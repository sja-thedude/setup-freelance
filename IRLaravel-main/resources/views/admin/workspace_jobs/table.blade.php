<table class="table table-responsive table-bordered jambo_table" id="workspaceJobs-table">
    <thead>
        <tr class="headings">
            <th>Created At</th>
        <th>Updated At</th>
        <th>Workspace Id</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Content</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($workspaceJobs as $workspaceJob)
        <tr>
            <td>{!! $workspaceJob->created_at !!}</td>
            <td>{!! $workspaceJob->updated_at !!}</td>
            <td>{!! $workspaceJob->workspace_id !!}</td>
            <td>{!! $workspaceJob->name !!}</td>
            <td>{!! $workspaceJob->email !!}</td>
            <td>{!! $workspaceJob->phone !!}</td>
            <td>{!! $workspaceJob->content !!}</td>
            <td>
                <a href="{!! route('admin.workspaceJobs.show', [$workspaceJob->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.workspaceJobs.edit', [$workspaceJob->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.workspaceJobs.destroy', $workspaceJob->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>