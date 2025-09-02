<div>
    <p>Hallo {{ $user->name }},</p>
    <p>Du wurdest als Admin für :app zugewiesen.</p>
    <p>
        <a href="{{ url('/') }}"></a>
    </p>
    <p>Dein Konto wurde wie folgt erstellt:<br>
        Benutzername: {{ $user->email }}<br>
        Passwort: {{ $user->default_password }}</p>
    <p>Danke,<br>
        {!! config('app.name') !!}
</div>