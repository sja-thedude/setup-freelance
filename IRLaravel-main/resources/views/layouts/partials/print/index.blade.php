@include('layouts.partials.print.top', ['width' => 'default'])

@if(!empty($images))
    @foreach($images as $image)
        <img src="{!! url($image) !!}"/>
    @endforeach
@endif

@include('layouts.partials.print.bottom')