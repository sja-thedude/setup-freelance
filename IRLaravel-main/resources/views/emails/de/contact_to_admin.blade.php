<div>
    <p>Sie haben das folgende Formular vom Kontaktformular auf der KONTAKT-Seite Ihrer Website erhalten:</p>

    <p>
        VORNAME: {{ $contact->first_name }} <br>
        NACHNAME: {{ $contact->last_name }} <br>
        TELEFON: {{ $contact->phone }} <br>
        E-MAIL: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        NACHRICHT: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>