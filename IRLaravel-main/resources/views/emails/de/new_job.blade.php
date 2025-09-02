<div>
    <p>Hallo {{ $user->name }},</p>

    <p>Ein neuer Kandidat hat Interesse an Ihrer offenen Stelle gezeigt.</p>

    <p>
        Kandidat: {{ $job->name }} <br>
        E-Mail: <a href="mailto:{{ $job->email }}">{{ $job->email }}</a> <br>
        Telefon: {{ $job->phone }} <br>
        Nachricht: {!! nl2br(e($job->content)) !!} <br>
    </p>

    <p>Nachricht eingereicht am {{ Helper::getDateFromFormat($job->created_at) }} um {{ Helper::getTimeFromFormat($job->created_at) }}.</p>

    <p>Viel Erfolg!</p>

    <p>
        It's Ready
    </p>
</div>