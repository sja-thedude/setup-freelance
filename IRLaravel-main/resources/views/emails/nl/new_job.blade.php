<div>
    <p>Beste {{ $user->name }},</p>

    <p>Een nieuwe kandidaat heeft interesse in uw openstaande vacature.</p>

    <p>
        Kandidaat: {{ $job->name }} <br>
        E-mail: <a href="mailto:{{ $job->email }}">{{ $job->email }}</a> <br>
        Telefoon: {{ $job->phone }} <br>
        Bericht: {!! nl2br(e($job->content)) !!} <br>
    </p>

    <p>Bericht ingezonden op {{ Helper::getDateFromFormat($job->created_at) }} om {{ Helper::getTimeFromFormat($job->created_at) }}.</p>

    <p>Succes! </p>

    <p>
        It's Ready
    </p>
</div>