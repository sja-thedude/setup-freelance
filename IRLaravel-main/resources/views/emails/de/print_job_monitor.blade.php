<div>
    <p>Lieber {{ $workspace->manager_name }},</p>

    <p>
    Es tritt ein Problem mit dem Drucker auf. Seit mehr als @php echo round((time() - strtotime($printJob->created_at)) / 60) @endphp  Minuten wurden keine Druckaufträge vom Typ <strong>
            @if($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_KASSABON)
            receipt
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_WERKBON)
            order
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_STICKER)
            sticker
            @else
            unknown
            @endif
        </strong> mehr verarbeitet.
    </p>

    <p>Bitte überprüfen Sie Folgendes:</p>

    <ul>
        <li>Hat der Drucker noch ausreichend Papier?</li>
        <li>Ist der Drucker korrekt mit dem Internet verbunden?</li>
        <li>Ist der Drucker eingeschaltet?</li>
        <li>Ist der Drucker in Ihrem Konto korrekt konfiguriert?</li>
    </ul>

    <p>
        It's Ready
    </p>
</div>