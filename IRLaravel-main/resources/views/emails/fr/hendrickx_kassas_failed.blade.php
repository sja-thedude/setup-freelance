<div>
    <p>Bonjour {{ !empty($workspace->manager_name) ? $workspace->manager_name : '' }},</p>

    <p>Essais: {{ (int) $attempts }} / {{ (int) $maxAttempts }}</p>

    @if($order)
    @php
    $codeId = "#" . ($order->group_id ? "G" . $order->parent_code : $order->code);

    $dateTimeLocal = Helper::convertDateTimeToTimezone($order->date . " " . $order->time, $order->timezone);
    $dateTimeLocalParse = \Carbon\Carbon::parse($dateTimeLocal);
    $dateLocal = $dateTimeLocalParse->format("d/m/y");
    $timeLocal = $dateTimeLocalParse->format("H:i");

    $dateTimeLocalGereed = Helper::convertDateTimeToTimezone($order->gereed, $order->timezone);
    $dateTimeLocalGereedParse = \Carbon\Carbon::parse($dateTimeLocalGereed);
    $gereedDateLocal = $dateTimeLocalGereedParse->format("d/m/y");
    $gereedTimeLocal = $dateTimeLocalGereedParse->format("H:i");

    @endphp

    <p>
        <strong>Informations sur la commande</strong><br />
        Numéro de commande: {{ $codeId }}<br />
        Commande passée le: {{ $dateLocal }} {{ $timeLocal }}<br />
        Prêt: {{ $gereedDateLocal }} {{ $gereedTimeLocal }}
    </p>
    @endif

    <p style="font-style: italic;">
        <strong>Informations techniques</strong><br />
        L'envoi de la commande avec l'ID {{ $orderId }} a échoué !<br />
        (ID du connecteur {{ $connectorId }})
    </p>

    <p>
        It's Ready
    </p>
</div>