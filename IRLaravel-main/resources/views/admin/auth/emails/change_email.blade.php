<div>
    <p>Dear {{ $user->name }},</p>
    <p>You have requested to change the email address of your account to this email.</p>
    <p>Please kindly click on this <a href="{{ route('admin.user.confirmChangeEmail', ['token' => base64_encode(json_encode(array('id' => $user->id)))]) }}" target="_blank">link</a> to confirm your change so you can login to {!! config('app.name') !!} with this email.</p>
    <p>If you did not request to change email address, please ignore this email and contact {!! config('app.name') !!} to report.</p>
    <p>Thanks and Regards, </p>
    <p>{!! config('app.name') !!}</p>
</div>
