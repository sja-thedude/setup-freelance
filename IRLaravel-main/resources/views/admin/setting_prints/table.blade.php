<table class="table table-responsive table-bordered jambo_table" id="settingPrints-table">
    <thead>
        <tr class="headings">
            <th>Workspace Id</th>
        <th>Type</th>
        <th>Mac</th>
        <th>Copy</th>
        <th>Auto</th>
        <th>Created At</th>
        <th>Updated At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($settingPrints as $settingPrint)
        <tr>
            <td>{!! $settingPrint->workspace_id !!}</td>
            <td>{!! $settingPrint->type !!}</td>
            <td>{!! $settingPrint->mac !!}</td>
            <td>{!! $settingPrint->copy !!}</td>
            <td>{!! $settingPrint->auto !!}</td>
            <td>{!! $settingPrint->created_at !!}</td>
            <td>{!! $settingPrint->updated_at !!}</td>
            <td>
                <a href="{!! route('admin.settingPrints.show', [$settingPrint->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.settingPrints.edit', [$settingPrint->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.settingPrints.destroy', $settingPrint->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>