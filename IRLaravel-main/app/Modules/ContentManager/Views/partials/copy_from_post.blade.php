@php($languages = Helper::getActiveLanguages())
<form method="POST">
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
                    <li><a {!! (!empty($copyDatas)) ? 'data-action="copy-post"' : '' !!} data-copy-action="{{ Admin::route('contentManager.data.copyFromPost', ['locale' => $lang]) }}">{{$language}}</a></li>
                @endif
            @endforeach
        </ul>
    </div>
    
    @if(!empty($copyDatas))
        @foreach($copyDatas as $cpData)
            <input type="hidden" name="models[]" value="{{ $cpData['model'] }}"/>
            <input type="hidden" name="ids[]" value="{{ $cpData['id'] }}"/>
        @endforeach
    @endif
</form>