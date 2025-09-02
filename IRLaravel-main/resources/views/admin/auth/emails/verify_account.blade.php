<div>
    <p>Hi {{ $user->name }},</p>
    <p>Thanks for joining {!! config('app.name') !!}.</p>
    <p>We just need to verify your email address to complete your registration.</p>
    <p>Please click <a href="{{ url('/verify_account', [$user->id]) }}" target="_blank">here</a> to verify!</p>
    <p>{!! config('app.name') !!}</p>
</div>