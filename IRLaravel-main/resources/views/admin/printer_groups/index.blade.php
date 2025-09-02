@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('menu.printer_groups')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            @include($guard.'.printer_groups.partials.quick_search')
                        </li>
                        @if(Helper::checkUserPermission($guard.'.printergroup.create'))
                            <li>
                                <a  data-toggle="modal" data-target="#modal_create_printer_group"
                                    class="ir-btn ir-btn-primary cursor-pointer">
                                    <i class="ir-plus"></i> @lang('printer_group.add_group')
                                </a>
                            </li>
                            @include($guard.'.printer_groups.partials.modal_create')
                        @endif
                    </ul>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    @include($guard.'.printer_groups.partials.table')
                </div>
            </div>
        </div>
    </div>
@endsection