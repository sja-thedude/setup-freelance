<div>
    <p>Hallo {{ $user->name }},</p>
    <p>Je bent toegewezen als beheerder van {!! config('app.name') !!}.</p>
    <p>
        <a href="{{ url('/') }}"></a>
    </p>
    <p>Je account is als volgt aangemaakt:<br>
    Gebruikersnaam: {{ $user->email }}<br>
    Wachtwoord: {{ $user->default_password }}</p>
    <p>Bedankt,<br>
        {!! config('app.name') !!}
</div>