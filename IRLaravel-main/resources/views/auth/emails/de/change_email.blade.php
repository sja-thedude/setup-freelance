<div>
    <p>Sehr geehrte/r {{ $user->name }},</p>
    <p>Sie haben angefragt, die E-Mail-Adresse Ihres Kontos auf diese E-Mail-Adresse zu ändern.</p>
    <p>Bitte klicken Sie auf diesen <a href="{{ route('user.confirmChangeEmail', ['token' => base64_encode(json_encode(array('id' => $user->id)))]) }}" 
          target="_blank">Link</a>, um die Änderung zu bestätigen, damit Sie sich bei {!! config('app.name') !!} mit dieser E-Mail-Adresse anmelden können.</p>
    <p>Falls Sie nicht angefragt haben, die E-Mail-Adresse zu ändern, ignorieren Sie bitte diese E-Mail und kontaktieren Sie {!! config('app.name') !!}, um dies zu melden.</p>
    <p>Mit freundlichen Grüßen,</p>
    <p>{!! config('app.name') !!}</p>
</div>
