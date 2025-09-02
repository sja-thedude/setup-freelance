@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
          <h2>Country</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-12">
                    @include('ContentManager::partials.errormessage')
                </div>
                {!! Form::model($country, ['route' => ['admin.countries.update', $country->id], 'method' => 'patch']) !!}

                    @include('admin.countries.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection