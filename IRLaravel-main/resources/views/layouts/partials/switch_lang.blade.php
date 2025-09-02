@php($languages = Helper::getActiveLanguages())
@php($selected = App::getLocale())

<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
    &nbsp;
    <span>{{ $languages[$selected] }}</span>
    &nbsp;
    <span class="fa fa-angle-down"></span>
</a>
@if (count($languages) > 1)
<ul class="dropdown-menu">
    @foreach ($languages as $locale => $language)
        @if ($locale != $selected)
            <li><a href="{{ route('lang.switch', $locale) }}">{{$language}}</a></li>
        @endif
    @endforeach
</ul>
@endif