@extends('layouts.web-default')

@section('slider')
    <div class="hp-slider">
        @include('web.partials.slider')

        <div class="wp-content pl-35" id="wrapSwitchAfhaalLevering">
            <div class="row">
                <div class="col-md-12">

                    {!! Form::open(['url' => '#', 'name' => 'step-register', 'id' => 'form-register', 'class' => 'step-register']) !!}

                    <div class="wrap-step active" data-id="1">
                        <div class="row ">
                            <div class="col-md-12 pl-35">
                                <div class="wrap-action">
                                    <a href="{{ route('login') }}" class="dark-grey">
                                        <i class="icn-arrow-left"></i> @lang('strings.back')
                                    </a>
                                    <div>
                                        <p>@lang('auth.message_register_successfully')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row custom-col">
                            <div class="col-md-4 pl-35" style="height: 220px;">

                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="main-body">
        @if(!(!empty($is_delivery) && empty($is_takeout)))
            @include('web.partials.slide-category')
        @endif
    </div>
    
    @include('web.partials.footer')
@endsection