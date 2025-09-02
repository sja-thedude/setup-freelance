<div>
    <p>You have received the following form from the Contact Us form on the CONTACT page of your website:</p>
    <p>
        FIRST NAME: {{ $contact->first_name }} <br>
        LAST NAME: {{ $contact->last_name }} <br>
        PHONE: {{ $contact->phone }} <br>
        EMAIL: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        MESSAGE: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>