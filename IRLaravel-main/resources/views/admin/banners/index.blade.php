@extends('layouts.admin')

@section('content')
<div class="row">
	<div class="x_panel">
    <div class="x_title">
      <h2>@lang('strings.banner.title_all')</h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
            <a href="{!! URL::to('/') !!}" target="_blank" class="btn btn-info">
                <i class="fa fa-eye"></i> @lang('strings.preview')
            </a>
        </li>
        @if(Helper::checkUserPermission('admin.banners.create'))
        <li>
            <a href="{!! route('admin.banners.create') !!}" class="btn-toolbox success">
                <i class="fa fa-plus"></i> @lang('strings.add_new')
            </a>
        </li>
        @endif
      </ul>
      <div class="clearfix"></div>
      @include('ContentManager::partials.errormessage')
    </div>
    <div class="x_content">
      @include('admin.banners.table')
    </div>
  </div>
</div>
@endsection