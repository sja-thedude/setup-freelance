@php
    $baseLink = url('password/reset', $token);

    if($user->platform == \App\Models\User::PLATFORM_BACKOFFICE) {
        $baseLink = url('admin/password/reset', $token);
    }

    $link = $baseLink.'?email='.urlencode($user->getEmailForPasswordReset());
@endphp
Click here to reset your password: <a href="{{ $link }}"> {{ $link }} </a>
