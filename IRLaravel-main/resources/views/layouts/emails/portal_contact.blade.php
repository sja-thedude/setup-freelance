<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('frontend.contact_us')</title>
</head>
<body>
<p>
    @lang('frontend.first_name'): {{ $contact['first_name'] }} <br>
    @lang('frontend.last_name'): {{ $contact['last_name'] }} <br>
    @lang('frontend.email'): <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a> <br>
    @lang('frontend.phone'): {{ $contact['phone'] }} <br>
    @lang('frontend.message'): {!! nl2br(e($contact['message'])) !!} <br>
</p>

<p>It's Ready</p>
</body>
</html>