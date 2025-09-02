@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="x_panel">
            <div class="x_title">
                <h2>@lang('menu.lang_manager')</h2>
                <div class="clearfix"></div>
                @include('ContentManager::partials.errormessage')
            </div>

            <div class="x_content">
                @include('admin.lang.partials.table')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('ContentManager::partials.scriptdelete')
@endpush