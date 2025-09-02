<div marginwidth="0" marginheight="0"
     style="margin:0;padding:0;background-color:#eaeaea;height:100%!important;width:100%!important">
    <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"
           style="margin:0;padding:0;background-color:#eaeaea;border-collapse:collapse!important;height:100%!important;width:100%!important">
        <tbody>
        <tr>
            <td align="center" valign="top"
                style="margin:0;padding:20px;border-top:4px solid #0098ff;height:100%!important;width:100%!important">
                <table border="0" cellpadding="0" cellspacing="0"
                       style="width:600px;border-collapse:collapse!important">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                   style="background-color:#ffffff;border-top:1px solid #ffffff;border-bottom:1px solid #cccccc;border-collapse:collapse!important">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                               style="border-top-left-radius:4px;border-top-right-radius:4px;background-color:#ffffff;border-top:none!important;border-bottom:1px solid #cccccc;border-collapse:collapse!important">
                                            <tbody>
                                                <tr>
                                                    <td valign="top"
                                                        style="color:#505050;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:16px;line-height:150%; padding: 30px;text-align:center">
                                                        <span style="margin-bottom: 20px; display: block;">
                                                            {!! $labelMail['hello'] !!} <strong>{{$name}},</strong>
                                                        </span>
                                                        <h3 style="display:block;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:16px;font-weight:normal;line-height:100%;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;text-align:center;color:#606060!important">
                                                            {{ $labelMail['reminder.title'] }}
                                                        </h3>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                               style="background-color:#f4f4f4;border-top:1px solid #ffffff;border-bottom:1px solid #cccccc;border-collapse:collapse!important">
                                            <tbody>
                                            <tr>
                                                <td align="center" valign="top" style="padding-top:20px;width: 100%">
                                                    <table border="0" cellpadding="20" cellspacing="0" width="100%"
                                                           style="border-collapse:collapse!important">
                                                        <tbody>
                                                        <tr>
                                                            <td valign="top"
                                                                style="color:#505050;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:14px;line-height:150%;padding-top:0;padding-right:20px;padding-bottom:20px;padding-left:20px;text-align:left">
                                                                <table style="width:100%;border-collapse:collapse!important; vertical-align:top;color:#505050;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:14px;line-height:150%;">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style="vertical-align:top" colspan="2">
                                                                                <h2 style="font-weight:normal;display:block;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:20px;font-style:normal;line-height:100%;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;text-align:center;color:#404040!important">
                                                                                    {{ $labelMail['appointment.title'] }}
                                                                                </h2>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 130px">
                                                                                <b>{{ $labelMail['appointment.date'] }}</b></td>
                                                                            <td>{{date(config('calendar._dateFormatServer'), strtotime($appointment->start_time))}}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><b>{{ $labelMail['appointment.time'] }}</b></td>
                                                                            <td>{{date(config('calendar.hourFormatServer'), strtotime($appointment->start_time))}}
                                                                                - {{date(config('calendar.hourFormatServer'), strtotime($appointment->end_time))}}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><b>{{ $labelMail['appointment.agenda'] }}</b>
                                                                            </td>
                                                                            <td>{{ $agendaName }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                               style="background-color:#ffffff;border-top:1px solid #ffffff;border-bottom:1px solid #cccccc;border-collapse:collapse!important">
                                            <tbody>
                                            <tr>
                                                <td valign="top"
                                                    style="color:#505050;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:14px;line-height:150%;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;text-align:left">
                                                    <table style="width:100%;border-collapse:collapse!important; vertical-align:top;color:#505050;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:14px;line-height:150%">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <h2 style="font-weight:normal;display:block;font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;font-size:20px;font-style:normal;line-height:100%;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;text-align:center;color:#404040!important">
                                                                        {{ $labelMail['patient.title'] }}
                                                                    </h2>
                                                                </td>
                                                            </tr>
                                                            @if(!empty($appointment->reason_for_contact))
                                                                <tr>
                                                                    <td width="100px">
                                                                        <b>{{ $labelMail['patient.reason_for_contact'] }}:</b></td>
                                                                    <td>{{$appointment->reason_for_contact}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->name))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.first_name'] }}:</b></td>
                                                                    <td>{{$appointment->name}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->surname))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.last_name'] }}:</b></td>
                                                                    <td>{{$appointment->surname}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->birthday))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.birthday'] }}:</b></td>
                                                                    <td>{{date(config('calendar._dateFormatServer'), strtotime($appointment->birthday))}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->id_country))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.country'] }}:</b></td>
                                                                    <td>{{$appointment->country}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->email))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.email'] }}:</b></td>
                                                                    <td>{{$appointment->email}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->phone))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.telephone_number'] }}:</b></td>
                                                                    <td>{{$appointment->phone}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->gsm))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.gsm'] }}:</b></td>
                                                                    <td>{{$appointment->gsm}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->nationality_insurance_number))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.nationality_insurance_number'] }}:</b></td>
                                                                    <td>{{$appointment->nationality_insurance_number}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->address))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.address_number'] }}:</b></td>
                                                                    <td>{{$appointment->address}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->postcode))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.postcode_city'] }}:</b></td>
                                                                    <td>{{$appointment->postcode}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->first_visit))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.first_visit'] }}:</b></td>
                                                                    <td>{{\App\Models\Appointment::getVisit($appointment->first_visit)}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->note))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.note'] }}:</b></td>
                                                                    <td>{{$appointment->note}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->gender))
                                                                <tr>
                                                                    <td><b>{{ $labelMail['patient.gender'] }}:</b></td>
                                                                    <td>{{\App\Models\User::gendersDropdown($appointment->gender)}}</td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($appointment->number_people))
                                                                <tr>
                                                                    <td>
                                                                        <b>
                                                                            {{$labelMail['patient.people']}}:
                                                                        </b>
                                                                    </td>
                                                                    <td>{{$appointment->number_people}}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>