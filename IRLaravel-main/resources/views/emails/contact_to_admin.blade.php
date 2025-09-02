{{-- Include mail content by locale template --}}
@php($locale = App::getLocale())
@include('emails.'. $locale .'.contact_to_admin')