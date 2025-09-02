@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('menu.groups')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            @include($guard.'.grouprestaurant.partials.quick_search')
                        </li>
                        @if(Helper::checkUserPermission($guard.'.grouprestaurant.create'))
                            <li>
                                <a  data-toggle="modal" data-target="#modal_create_group_restaurant"
                                    class="ir-btn ir-btn-primary cursor-pointer">
                                    <i class="ir-plus"></i> @lang('grouprestaurant.add_group')
                                </a>
                            </li>
                            @include($guard.'.grouprestaurant.partials.modal_create')
                        @endif
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.grouprestaurant.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection