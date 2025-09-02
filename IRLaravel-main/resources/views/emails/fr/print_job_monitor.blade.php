<div>
    <p>Bonjour {{ $workspace->manager_name }},</p>

    <p>
    Un problème est survenu avec l'imprimante. Aucun travail d'impression du type <strong>
            @if($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_KASSABON)
            receipt
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_WERKBON)
            order
            @elseif($printJob->job_type == App\Models\PrinterJob::JOB_TYPE_STICKER)
            sticker
            @else
            unknown
            @endif
        </strong> n'a été traité depuis plus de @php echo round((time() - strtotime($printJob->created_at)) / 60) @endphp minutes.
    </p>

    <p>Veuillez vérifier les éléments suivants:</p>

    <ul>
        <li>L'imprimante a-t-elle encore suffisamment de papier ?</li>
        <li>L'imprimante est-elle correctement connectée à Internet ?</li>
        <li>L'imprimante est-elle allumée ?</li>
        <li>L'imprimante est-elle correctement configurée dans votre compte ?</li>
    </ul>

    <p>
        It's Ready
    </p>
</div>