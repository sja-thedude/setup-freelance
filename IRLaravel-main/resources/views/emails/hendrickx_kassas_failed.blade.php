{{-- Include mail content by locale template --}}
@php($locale = App::getLocale())
@include('emails.'. $locale .'.hendrickx_kassas_failed')