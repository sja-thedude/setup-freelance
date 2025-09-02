@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="x_panel">
            <div class="x_title">
                <h2>Countries</h2>
                <ul class="nav navbar-right panel_toolbox">
                    @if(Helper::checkUserPermission('admin.countries.create'))
                        <li>
                            <a href="{!! route('admin.countries.create') !!}" class="btn-toolbox success">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="clearfix"></div>
                @include('ContentManager::partials.errormessage')
            </div>
            <div class="x_content">
                @include('admin.countries.table')
            </div>
        </div>
    </div>
@endsection