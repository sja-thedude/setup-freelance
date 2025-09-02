<div>
    <p>Hi {{ $workspace->manager_name }},</p>

    <p>
    There is a problem with the printer. Been there longer than
        @php echo round((time() - strtotime($printJob->created_at)) / 60) @endphp minutes no more print jobs are processed of type <strong>
            @if($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_KASSABON)
                receipt
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_WERKBON)
                order
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_STICKER)
                sticker
            @else
                unknown
            @endif
        </strong>
    </p>

    <p>Please check the following:</p>

    <ul>
        <li>Printer still has enough paper?</li>
        <li>Printer is properly connected to the internet?</li>
        <li>Printer is on?</li>
        <li>Printer is configured correctly in your account?</li>
    </ul>

    <p>
        It's Ready
    </p>
</div>