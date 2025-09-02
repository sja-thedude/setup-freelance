<div>
    <p>Beste</p>

    <p>U heeft zonet een aanvraag ontvangen via It’s Ready om te bestellen als groep.</p>

    <p>Dit zijn de gegevens van het bericht:</p>

    <p>
        Voornaam: {{ $contact->first_name }} <br>
        Achternaam: {{ $contact->last_name }} <br>
        E-mailadres: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        Bedrijf: {{ $contact->company_name }} <br>
        Telefoon/GSM: {{ $contact->phone }} <br>
        Gemeente: {{ $contact->address }} <br>
        Bericht: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>