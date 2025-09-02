@extends(Admin::theme())

@section('content')
	<div class="row">
		<div class="x_panel">
          <div class="x_title">
            <h2>Theme Manager</h2>
            <ul class="nav navbar-right panel_toolbox">
            	<li><a href="{{ Admin::route('contentManager.theme.install') }}">Install Theme</a></li>
              	<li><a class="close-link"><i class="fa fa-close"></i></a></li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
              @include('ContentManager::theme.partials.alert')
             @include("ContentManager::theme.partials.x_list")
          </div>
        </div>
	</div>

@endsection