@extends('layouts.web-default')

@section('slider')
    <div class="hp-slider">
        @include('web.partials.slider')

        <div class="wp-content pl-35" id="wrapSwitchAfhaalLevering">
            <div class="row">
                <div class="col-md-12">

                    @php($classInvalid = $errors->any() ? ' invalid ' : '')
                    {!! Form::open(['route' => 'submit_register', 'name' => 'step-register', 'id' => 'form-register', 'class' => 'step-register' . $classInvalid]) !!}

                        <input type="hidden" name="platform" value="web">

                        <div class="wrap-step active" data-id="1">
                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="wrap-action">
                                        <a href="{{ route('login') }}" class="dark-grey font-18">
                                            <i class="icn-arrow-left"></i> @lang('strings.back')
                                        </a>
                                        <div class="font-18">@lang('register.register_description')</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-line @if($errors->has('first_name')) invalid-input @endif">
                                                {!! Form::text('first_name', null, ['required', 'placeholder' => trans('register.placeholders.first_name')]) !!}
                                                @if($errors->has('first_name'))
                                                    <span class="error">{{ $errors->first('first_name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-line @if($errors->has('last_name')) invalid-input @endif">
                                                {!! Form::text('last_name', null, ['required', 'placeholder' => trans('register.placeholders.last_name')]) !!}
                                                @if($errors->has('last_name'))
                                                    <span class="error">{{ $errors->first('last_name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-line @if($errors->has('gsm')) invalid-input @endif">
                                        {!! Form::text('gsm', null, ['class' => 'keyup-gsm', 'required', 'placeholder' => trans('register.placeholders.gsm')]) !!}
                                        @if(!$errors->has('gsm'))
                                            <label style="display: block; font-size: 14px">@lang('register.descriptions.gsm')</label>
                                        @endif
                                        @if($errors->has('gsm'))
                                            <span class="error">{{ $errors->first('gsm') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-line @if($errors->has('email')) invalid-input @endif">
                                        {!! Form::email('email', null, ['required', 'placeholder' => trans('register.placeholders.email')]) !!}
                                        @if($errors->has('email'))
                                            <span class="error">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-line @if($errors->has('password')) invalid-input @endif">
                                        <div class="form-input">
                                            {!! Form::password('password', ['class' => 'password required', 'required', 'placeholder' => trans('register.placeholders.password')]) !!}
                                            <span class="right show-pass active">
                                                <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        </div>
                                        @if($errors->has('password'))
                                            <span class="error">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-line @if($errors->has('password_confirmation')) invalid-input @endif">
                                        <div class="form-input">
                                            {!! Form::password('password_confirmation', ['class' => 'password_confirmation required', 'required', 'placeholder' => trans('register.placeholders.password_confirmation')]) !!}
                                            <span class="right show-pass active">
                                                <svg class="svg-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <svg class="svg-icon hidden" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.378 1.31812L19.318 23.6221M1 12.5001C1 12.5001 5 4.83345 12 4.83345C19 4.83345 23 12.5001 23 12.5001C23 12.5001 19 20.1668 12 20.1668C5 20.1668 1 12.5001 1 12.5001ZM15 12.5001C15 14.0879 13.6569 15.3751 12 15.3751C10.3431 15.3751 9 14.0879 9 12.5001C9 10.9123 10.3431 9.62512 12 9.62512C13.6569 9.62512 15 10.9123 15 12.5001Z" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        </div>
                                        @if($errors->has('password_confirmation'))
                                            <span class="error">{{ $errors->first('password_confirmation') }}</span>
                                        @endif
                                        <label style="display: block; font-size: 14px">&nbsp;</label>
                                    </div>
                                    <div class="form-line wrap-checkbox-register display-flex">
                                        <input type="checkbox" name="checkbox-register" id="checkbox-1" class="checkbox-register" required  @if (old('checkbox-register')) checked="checked" @endif/>
                                        <label for="checkbox-1">@lang('register.text_terms_and_conditions')</label>
                                    </div>
                                </div>
                                <div class="col-md-3 hidden-mobile">
                                    @include('auth.social_login', ['site' => 'register_with', 'style' => 'padding-top: 18px;padding-bottom: 50px;'])
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-line">
                                        <button class="btn btn-disable btn-register"  type="submit">@lang('auth.button_register')</button>
                                    </div>
                                </div>
                                <div class="col-md-3 hidden-pc">
                                    @include('auth.social_login', ['site' => 'register_with'])
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

@push('scripts')
    <script>
        $(function () {
            var formRegister = $('#form-register');
            var txtBirthdayDisplay = formRegister.find(':input[name="birthday_display"]');
            var txtBirthday = formRegister.find(':input[name="birthday"]');

            // Apply change from birthday display field to birthday field
            txtBirthdayDisplay.on('change', function () {
                var value = $(this).val();

                // Empty value
                if ((value + '').trim() === '') {
                    txtBirthday.val('');
                    return;
                }

                var date = moment(value, "DD/MM/YYYY");
                var valFormat = date.format('YYYY-MM-DD');

                if (!date || !valFormat) {
                    // Process when invalid value
                    valFormat = '';
                }

                // Set value for submit data
                txtBirthday.val(valFormat);
            });
        });
    </script>
@endpush