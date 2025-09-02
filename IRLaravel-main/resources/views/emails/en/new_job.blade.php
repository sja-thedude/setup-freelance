<div>
    <p>Dear {{ $user->name }},</p>

    <p>A new candidate has shown interest in your open vacancy.</p>

    <p>
        Candidate: {{ $job->name }} <br>
        Email: <a href="mailto:{{ $job->email }}">{{ $job->email }}</a> <br>
        Phone: {{ $job->phone }} <br>
        Message: {!! nl2br(e($job->content)) !!} <br>
    </p>

    <p>Message submitted on {{ Helper::getDateFromFormat($job->created_at) }} at {{ Helper::getTimeFromFormat($job->created_at) }}.</p>

    <p>Good luck! </p>

    <p>
        It's Ready
    </p>
</div>