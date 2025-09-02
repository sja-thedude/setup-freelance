<li class="nav-language">
    @php($languages = Helper::getActiveLanguages())
    @php($selected = App::getLocale())
    
    <a href="javascript:;" class="has-submenu language">
        <span class="wrap-text color{{$class}}">
            {{ $selected }}
            <i class="icn-arrow-down{{$class}}"></i>
        </span>
    </a>
    @if (count($languages) > 1)
    <ul class="sub-menu">
        @foreach ($languages as $locale => $language)
            <li><a class="@if($locale == $selected)actived @endif" href="{{ route('lang.switch', $locale) }}">{{$language}}</a></li>
        @endforeach
    </ul>
    @endif
</li>