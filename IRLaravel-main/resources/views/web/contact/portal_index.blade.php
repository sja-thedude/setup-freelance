@extends('layouts.web-home-new')

@section('content')
    <div id="portal-contact">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">@lang('frontend.contact_us')</h1>
                <p class="info text-center mgb-90">@lang('frontend.contact_info')</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div id="contact-form">
                    @if (count($errors) > 0)
                        <div class="show-error">
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li class="send-feedback">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    {!! Form::open(['route' => $guard.'.contact.portalStore']) !!}
                    <div class="form-group">
                        {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => trans('frontend.first_name')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => trans('frontend.last_name')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('frontend.phone')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => trans('frontend.email')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 9, 'placeholder' => trans('frontend.message')]) !!}
                    </div>
                    <div class="form-group text-center">
                        {!! Form::submit(trans('frontend.send_message'), ['class' => 'btn btn-primary btn-send-message']) !!}
                    </div>
                    {!! Form::close() !!}

                    @if (session()->has('flash_notification.message'))
                        <div id="send-success" class="form-group text-center">
                            <p class="send-success">
                                <svg width="32" height="26" viewBox="0 0 32 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 17L9 23L29 3" stroke="#B5B268" stroke-width="5" stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                </svg>
                                @lang('frontend.contact_send_success')
                            </p>
                            <p class="send-feedback">@lang('frontend.contact_send_success_info')</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
@endsection