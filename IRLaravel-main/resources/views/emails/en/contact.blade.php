<div>
    <p>Dear,</p>
    <p>You have just received a request via It’s Ready to place an order as a group.</p>

    <p>Here are the details of the message:</p>

    <p>
        First Name: {{ $contact->first_name }} <br>
        Last Name: {{ $contact->last_name }} <br>
        Email Address: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        Company: {{ $contact->company_name }} <br>
        Phone/Cell: {{ $contact->phone }} <br>
        Municipality: {{ $contact->address }} <br>
        Message: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>