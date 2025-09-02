@extends('layouts.admin')

@section('content')
<div class="row">
	<div class="x_panel">
    <div class="x_title">
      <h2>Contacts</h2>
      <ul class="nav navbar-right panel_toolbox">
          @if(Helper::checkUserPermission('admin.contacts.create'))
              <li><a href="{!! route('admin.contacts.create') !!}" class="btn-toolbox success"><i class="fa fa-plus"></i> Add New</a></li>
          @endif
      </ul>
      <div class="clearfix"></div>
      @include('ContentManager::partials.errormessage')
    </div>
    <div class="x_content">
      @include('admin.contacts.table')
    </div>
  </div>
</div>
@endsection