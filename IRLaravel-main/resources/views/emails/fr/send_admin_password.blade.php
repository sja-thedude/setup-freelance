<div>
    <p>Bonjour {{ $user->name }},</p>
    <p>Vous avez été assigné en tant qu'Admin de {!! config('app.name') !!}.</p>
    <p>
        <a href="{{ url('/') }}"></a>
    </p>
    <p>Votre compte a été créé comme suit<br>
        Nom d'utilisateur: {{ $user->email }}<br>
        Mot de passe: {{ $user->default_password }}</p>
    <p>Merci,<br>
        {!! config('app.name') !!}
</div>