{{-- Include mail content by locale template --}}
@php($locale = App::getLocale())
@include('emails.'. $locale .'.new_job')