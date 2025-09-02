@php($languages = Helper::getActiveLanguages())
<!-- Split button -->
<div class="btn-group">
    <button type="button" class="btn btn-default">@lang('common.copy_from')</button>
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">@lang('common.dropdown')</span>
    </button>
    <ul class="dropdown-menu">
        @foreach ($languages as $lang => $language)
            @if ($lang != App::getLocale())
                <li><a href="{{ Admin::route('contentManager.data.copy_from', ['model' => get_class($model), 'id' => $id, 'locale' => $lang]) }}">{{$language}}</a></li>
            @endif
        @endforeach
    </ul>
</div>