{{-- Include mail content by locale template --}}
@php($locale = App::getLocale())
@include('auth.emails.'. $locale .'.register_confirmation')