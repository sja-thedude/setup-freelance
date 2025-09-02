<div>
    <p>Dear {{ !empty($workspace->manager_name) ? $workspace->manager_name : '' }},</p>

    <p>Attempts: {{ (int) $attempts }} / {{ (int) $maxAttempts }}</p>

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
            <strong>Order Information</strong><br />
            Order number: {{ $codeId }}<br />
            Order placed on: {{ $dateLocal }} {{ $timeLocal }}<br />
            Ready: {{ $gereedDateLocal }} {{ $gereedTimeLocal }}
        </p>
    @endif

    <p style="font-style: italic;">
        <strong>Technical Information</strong><br />
        Forwarding of order with ID {{ $orderId }} failed!<br />
        (Connector ID {{ $connectorId }})
    </p>

    <p>
        It's Ready
    </p>
</div>