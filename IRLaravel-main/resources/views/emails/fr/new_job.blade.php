<div>
    <p>Bonjour {{ $user->name }},</p>

    <p>Un nouveau candidat est intéressé par votre offre d'emploi.</p>

    <p>
        Candidat : {{ $job->name }} <br>
        E-mail: <a href="mailto:{{ $job->email }}">{{ $job->email }}</a> <br>
        Téléphone : {{ $job->phone }} <br>
        Message : {!! nl2br(e($job->content)) !!} <br>
    </p>

    <p>Message soumis le {{ Helper::getDateFromFormat($job->created_at) }} à {{ Helper::getTimeFromFormat($job->created_at) }}.</p>

    <p>Bonne chance!</p>

    <p>
        It's Ready
    </p>
</div>