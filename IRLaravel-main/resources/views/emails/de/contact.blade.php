<div>
    <p>Hallo,</p>

    <p>Sie haben gerade eine Anfrage über It’s Ready erhalten, um eine Bestellung als Gruppe aufzugeben.</p>

    <p>Hier sind die Details der Nachricht:</p>

    <p>
        Vorname: {{ $contact->first_name }} <br>
        Nachname: {{ $contact->last_name }} <br>
        E-Mail-Adresse: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        Firma: {{ $contact->company_name }} <br>
        Telefon/Mobil: {{ $contact->phone }} <br>
        Gemeinde: {{ $contact->address }} <br>
        Nachricht: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>