<div>
    <p>Beste {{ $user->name }},</p>
    <p>U heeft verzocht om het e-mailadres van uw account te wijzigen naar dit e-mailadres.</p>
    <p>Gelieve op deze <a href="{{ route('user.confirmChangeEmail', ['token' => base64_encode(json_encode(array('id' => $user->id)))]) }}"
            target="_blank">link</a> te klikken om uw wijziging te bevestigen, zodat u kunt inloggen op {!! config('app.name') !!} met dit e-mailadres.</p>
    <p>Als u niet heeft verzocht om het e-mailadres te wijzigen, negeer deze e-mail dan en neem contact op met {!! config('app.name') !!} om dit te melden.</p>
    <p>Bedankt en met vriendelijke groet,</p>
    <p>{!! config('app.name') !!}</p>
</div>