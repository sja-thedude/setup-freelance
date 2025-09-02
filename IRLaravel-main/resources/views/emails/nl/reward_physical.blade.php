<div>
    <p>Dag {{ $user->first_name }},</p>

    <p>Proficiat! U heeft succesvol uw punten ingewisseld voor een geschenk bij <span style="font-weight: bold;">{{ $workspace->name }}</span>.</p>

    <div style="margin-top: 20px; margin-bottom: 20px;">
        <table style="border: 1px solid #e9e9e9; border-radius: 12px; width: 100%;">
            <tr>
                <td rowspan="2" style="width: 170px;">
                    <img src="{{ $reward->photo }}" alt="" style="width: 150px; border-radius: 12px;">
                </td>
                <td style="padding-left: 10px;">
                    <div style="font-weight: bold;">Details van het geschenk:</div>
                    <div style="color: #7f9c03; font-size: 16px; font-weight: bold;">{{ $reward->title }}</div>
                    <div style="color: #686868;">{{ $reward->description }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">
                    <div>{{ Helper::getDatetimeFromFormat($redeem->created_at, 'd/m/Y H:i') }}</div>
                    <div>Aantal punten: {{ $reward->score }}</div>
                    <div>E-mailadres: {{ $user->email }}</div>
                </td>
            </tr>
        </table>
    </div>

    <p>Op naar het volgende geschenk!</p>

    <p>{{ $workspace->name }}</p>
</div>