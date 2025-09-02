<div>
    <p>Bonjour,</p>

    <p>Vous venez de recevoir une demande via It’s Ready pour passer une commande en groupe.</p>

    <p>Voici les détails du message :</p>

    <p>
        Prénom: {{ $contact->first_name }} <br>
        Nom: {{ $contact->last_name }} <br>
        Adresse e-mail: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> <br>
        Entreprise: {{ $contact->company_name }} <br>
        Téléphone/Portable: {{ $contact->phone }} <br>
        Commune: {{ $contact->address }} <br>
        Message: {!! nl2br(e($contact->content)) !!} <br>
    </p>

    <p>
        It's Ready
    </p>
</div>