<div>
    <p>U heeft het volgende formulier ontvangen van het Neem contact met ons op-formulier op de CONTACT pagina van uw website:</p>

    <p>
        VOORNAAM: {{ $contact->first_name }} <br>
        NAAM: {{ $contact->last_name }} <br>
        TELEFOON: {{ $contact->phone }} <br>
        E-MAIL: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        BERICHT: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>