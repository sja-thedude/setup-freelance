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
                {!! Form::open(['route' => [$guard.'.langs.update', $nameFile], 'method' => 'patch']) !!}
                    @include('admin.lang.partials.form')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('ContentManager::partials.scriptdelete')
@endpush