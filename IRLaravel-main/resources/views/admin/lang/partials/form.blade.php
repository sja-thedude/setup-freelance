@php
    $language = app()->getLocale();
@endphp

@if(!empty(config('languages')))
    <div class="col-md-12 col-sm-12 col-xs-12" id="formCommunication">
        <nav class="tab-custom">
            <ul class="nav nav-tabs">
                @foreach(config('languages') as $lKey => $lLabel)
                    <li class="nav-item nav-link tab-{{ $lKey }} {!! $lKey == $language ? 'active' : '' !!}" data-lang="{{ $lKey }}">
                        <a data-toggle="tab" href="#nav-lang-{!! $lKey !!}">{!! $lLabel !!}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="tab-content tab-custom-pdt">
            <?php
                $currentLangApp = app()->getLocale();
                $tabHasEmpty = array();
            ?>

            @foreach(config('languages') as $lKey => $lLabel)
                @php
                    if (!isset($contentLang[$lKey])) {
                        app()->setLocale($lKey);
                        $contentLang[$lKey] = array();
                        \App\Helpers\Helper::getChildNode("", trans($nameFile), $contentLang[$lKey]);
                    }
                @endphp

                <div id="nav-lang-{!! $lKey !!}" class="tab-pane fade {!! $lKey == $language ? 'in active' : '' !!}">
                    @foreach($content as $key => $row)
                        <div class="row form-group">
                            <div class="col-sm-4 col-xs-12">
                                {!! Form::label($key, $key) !!}
                            </div>

                            @php
                                if (isset($contentLang[$lKey][$key])) {
                                    $text = $contentLang[$lKey][$key];
                                } else {
                                    if ($lKey === "en") {
                                        $text = $row;
                                    } else {
                                        $text = NULL;
                                    }
                                }
                                if (!isset($contentLang[$lKey][$key]) || !$row) {
                                    $tabHasEmpty[] = $lKey;
                                }
                            @endphp

                            <div class="col-sm-8 col-xs-12">
                                {!! Form::text("data[" . $lKey . "][". $key . "]", $text, [
                                    'class' => 'form-control input',
                                    'style' => $text ? "" : "border-color:red",
                                ]) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <?php app()->setLocale($currentLangApp); ?>
        </div>
    </div>
@endif

<div class="row col-sm-12 col-xs-12 text-center">
    <hr/>
    <div class="form-group">
        {!! Html::link(route($guard.'.langs.index'), trans('strings.cancel'), ['class' => 'btn btn-default']) !!}
        {!! Form::submit(trans('strings.save'), ['class' => 'btn btn-primary']) !!}
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            @foreach ($tabHasEmpty as $lang)
                $('.nav-tabs .tab-{{ $lang }} a').css('color', 'red');
            @endforeach
        })
    </script>
@endpush
