<div>
    <p>Vous avez reçu le formulaire suivant du formulaire de contact sur la page CONTACT de votre site Web :</p>

    <p>
        PRÉNOM: {{ $contact->first_name }} <br>
        NOM: {{ $contact->last_name }} <br>
        TÉLÉPHONE: {{ $contact->phone }} <br>
        E-MAIL: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        MESSAGE: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>