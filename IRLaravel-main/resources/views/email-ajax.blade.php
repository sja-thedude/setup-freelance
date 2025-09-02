<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Created</th>
            <th>To</th>
            <th>Locale</th>
            <th>Subject</th>
            <th>Location</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($emails as $email)
        <tr>
            <th>{{ $email->id }}</th>
            <th>{{ $email->created_at }}</th>
            <td>{{ $email->to }}</td>
            <td>{{ $email->locale }}</td>
            <td>{{ $email->subject }}</td>
            <td><code>{{ $email->location }}</code></td>
            <td>
                <a href="javascript:;" data-key="{{ $email->id }}" data-action="show-detail">
                    <i class="bi-eye"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination">
    {{ $emails->links() }}
</div>