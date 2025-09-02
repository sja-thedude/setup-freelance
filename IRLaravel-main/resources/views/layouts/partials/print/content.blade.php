@if(!empty($contents))
    @foreach($contents as $content)
        @if($content['type'] == 'image')
            <div class="print-image-item print-item-type-{!! $type !!} text-center">
                <img src="{!! url($content['url']) !!}"/>
            </div>
        @endif
    @endforeach
@endif