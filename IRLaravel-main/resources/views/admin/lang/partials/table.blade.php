<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-5 col-xs-12">
                @lang('strings.file')
            </div>
            @foreach($arrDirLang as $keyLang)
                <div class="col-item col-sm-1 col-xs-12">
                    {{ $keyLang }}
                </div>
            @endforeach
            <div class="col-item col-sm-2 col-xs-12">
                @lang('common.options')
            </div>
        </div>
    </div>

    <div class="list-body">
        @foreach($datas as $fileName => $langs)
            <div id="tr-{{ $fileName }}" class="row">
                <div class="col-item col-sm-5 col-xs-12" style="word-wrap: break-word">
                    {!! $fileName !!}
                </div>

                @foreach($arrDirLang as $keyLang)
                    <div class="col-item col-sm-1 col-xs-12">
                        {!! $langs[$keyLang] !!}
                    </div>
                @endforeach

                <div class="col-item col-sm-2 col-xs-12" style="word-wrap: break-word">
                    @if(Helper::checkUserPermission($guard.'.langs.edit'))
                        <a href="{!! route($guard.'.langs.edit', ['id' => $fileName, 'name' => $fileName]) !!}" class='btn btn-default btn-xs' id="edit_client">
                            @lang('strings.translate')
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>