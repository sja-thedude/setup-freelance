@extends('layouts.web-home-new')

@section('content')
    <div class="row">
        <div class="col-md-12"  style="margin: 20px auto;">
            @if($model->post_name != 'how-does-it-work')
                <h1>{{ $model->post_title }}</h1>
            @endif

            {!!html_entity_decode($model->post_content)!!}
        </div>
    </div>
@endsection

