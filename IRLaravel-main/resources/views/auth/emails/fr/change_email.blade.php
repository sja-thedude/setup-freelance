<div>
    <p>Cher/Chère {{ $user->name }},</p>
    <p>Vous avez demandé à changer l'adresse e-mail de votre compte pour cette adresse.</p>
    <p>Veuillez cliquer sur ce <a href="{{ route('user.confirmChangeEmail', ['token' => base64_encode(json_encode(array('id' => $user->id)))]) }}" 
          target="_blank">lien</a> pour confirmer votre changement et pouvoir vous connecter à {!! config('app.name') !!} avec cette adresse e-mail.</p>
    <p>Si vous n'avez pas demandé à changer votre adresse e-mail, veuillez ignorer cet e-mail et contacter {!! config('app.name') !!} pour le signaler.</p>
    <p>Cordialement,</p>
    <p>{!! config('app.name') !!}</p>
</div>
