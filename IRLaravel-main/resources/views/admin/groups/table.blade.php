<table class="table table-responsive table-bordered jambo_table" id="groups-table">
    <thead>
        <tr class="headings">
            <th>Created At</th>
        <th>Updated At</th>
        <th>Workspace Id</th>
        <th>Name</th>
        <th>Company Name</th>
        <th>Company Street</th>
        <th>Company Number</th>
        <th>Company Vat Number</th>
        <th>Company City</th>
        <th>Company Postcode</th>
        <th>Payment Mollie</th>
        <th>Payment Payconiq</th>
        <th>Payment Cash</th>
        <th>Payment Factuur</th>
        <th>Close Time</th>
        <th>Receive Time</th>
        <th>Type</th>
        <th>Contact Email</th>
        <th>Contact Name</th>
        <th>Contact Surname</th>
        <th>Contact Gsm</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($groups as $group)
        <tr>
            <td>{!! $group->created_at !!}</td>
            <td>{!! $group->updated_at !!}</td>
            <td>{!! $group->workspace_id !!}</td>
            <td>{!! $group->name !!}</td>
            <td>{!! $group->company_name !!}</td>
            <td>{!! $group->company_street !!}</td>
            <td>{!! $group->company_number !!}</td>
            <td>{!! $group->company_vat_number !!}</td>
            <td>{!! $group->company_city !!}</td>
            <td>{!! $group->company_postcode !!}</td>
            <td>{!! $group->payment_mollie !!}</td>
            <td>{!! $group->payment_payconiq !!}</td>
            <td>{!! $group->payment_cash !!}</td>
            <td>{!! $group->payment_factuur !!}</td>
            <td>{!! $group->close_time !!}</td>
            <td>{!! $group->receive_time !!}</td>
            <td>{!! $group->type !!}</td>
            <td>{!! $group->contact_email !!}</td>
            <td>{!! $group->contact_name !!}</td>
            <td>{!! $group->contact_surname !!}</td>
            <td>{!! $group->contact_gsm !!}</td>
            <td>
                <a href="{!! route('admin.groups.show', [$group->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
                <a href="{!! route('admin.groups.edit', [$group->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                {!! Form::open(['route' => ['admin.groups.destroy', $group->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>