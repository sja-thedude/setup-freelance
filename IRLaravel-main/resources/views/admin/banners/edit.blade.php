@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="x_panel">
        <div class="x_title">
          <h2>@lang('strings.banner.title_edit')</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                <div class="col-md-12">
                    @include('ContentManager::partials.errormessage')
                </div>
                {!! Form::model($banner, ['route' => ['admin.banners.update', $banner->id], 'method' => 'patch']) !!}

                    @include('admin.banners.fields')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection