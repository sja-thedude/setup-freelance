<div>
    <p>Hallo {{ !empty($workspace->manager_name) ? $workspace->manager_name : '' }},</p>

    <p>Versuche: {{ (int) $attempts }} / {{ (int) $maxAttempts }}</p>

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
        <strong>Bestellinformationen</strong><br />
        Bestellnummer: {{ $codeId }}<br />
        Bestellung aufgegeben am: {{ $dateLocal }} {{ $timeLocal }}<br />
        Fertig: {{ $gereedDateLocal }} {{ $gereedTimeLocal }}
    </p>
    @endif

    <p style="font-style: italic;">
        <strong>Technische Informationen</strong><br />
        Weiterleitung der Bestellung mit der ID {{ $orderId }} fehlgeschlagen!<br />
        (Connector ID {{ $connectorId }})
    </p>

    <p>
        It's Ready
    </p>
</div>