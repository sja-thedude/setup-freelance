<div>
    <p>Hi {{ $user->name }},</p>
    <p>You have been assigned to be an Admin of {!! config('app.name') !!}.</p>
    <p>
        <a href="{{ url('/') }}"></a>
    </p>
    <p>Your account was created as below<br>
    Username: {{ $user->email }}<br>
    Password: {{ $user->default_password }}</p>
    <p>Thanks,<br>
    {!! config('app.name') !!}
</div>