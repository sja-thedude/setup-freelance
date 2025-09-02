<div>
    <p>Beste {{ $workspace->manager_name }},</p>

    <p>
        Er doet zich een probleem voor met de printer. Er zijn al langer dan
        @php echo round((time() - strtotime($printJob->created_at)) / 60) @endphp minuten geen print jobs meer verwerkt
        van het type <strong>
            @if($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_KASSABON)
                kassabon
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_WERKBON)
                werkbon
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_STICKER)
                sticker
            @else
                onbekend
            @endif
        </strong>
    </p>

    <p>Kijk volgende zaken even na:</p>

    <ul>
        <li>Printer heeft nog voldoende papier?</li>
        <li>Printer is correct verbonden met het internet?</li>
        <li>Printer staat aan?</li>
        <li>Printer is correct geconfigureerd in je account?</li>
    </ul>

    <p>
        It's Ready
    </p>
</div>