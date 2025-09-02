@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
          <h2>@lang('strings.banner.title_add')</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-12">
                    @include('ContentManager::partials.errormessage')
                </div>
                {!! Form::open(['route' => 'admin.banners.store']) !!}

                    @include('admin.banners.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
