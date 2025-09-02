<table class="table table-responsive" id="contacts-table">
    <thead>
        <tr>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Content</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($contacts as $contact)
        <tr>
            <td>{!! $contact->created_at !!}</td>
            <td>{!! $contact->updated_at !!}</td>
            <td>{!! $contact->name !!}</td>
            <td>{!! $contact->email !!}</td>
            <td>{!! $contact->phone !!}</td>
            <td>{!! $contact->content !!}</td>
            <td>
                @if(Helper::checkUserPermission('admin.contacts.show'))
                    <a href="{!! route('admin.contacts.show', [$contact->id]) !!}" class="btn btn-default btn-xs">
                        <i class="glyphicon glyphicon-eye-open"></i>
                    </a>
                @endif
                @if(Helper::checkUserPermission('admin.contacts.edit'))
                @endif
                <a href="{!! route('admin.contacts.edit', [$contact->id]) !!}" class="btn btn-default btn-xs">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
                @if(Helper::checkUserPermission('admin.contacts.destroy'))
                    {!! Form::open(['route' => ['admin.contacts.destroy', $contact->id], 'method' => 'delete']) !!}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    {!! Form::close() !!}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(!empty($contacts))
    <div class="pagination-area text-center">
        {{ $contacts->links() }}
    </div>
@endif