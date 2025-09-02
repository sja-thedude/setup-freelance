<div id="userModal" class="user-modal modal-gms modelProfileUser1 {!! !empty(request()->get('profile', null)) ? '' : 'hidden' !!}">
    <div class="bg"></div>
<!-- Modal content -->
    <div class="modal-content text-center">
        {!! Form::open(['url' => route('web.user.updateProfile'), 'method' => "POST", 'files' => TRUE, 'id' => "update_profile"]) !!}
            <div class="wrap-modal">
                <a href="javascript:;" class="close"
                   onclick="document.getElementsByClassName('modelProfileUser1')[0].classList.add('hidden')">
                    <img src="{!! url('images/close.svg') !!}"/>
                </a>
                @php($user = auth()->user())
                <div class="row">
                    <div class="col-md-12 modal-title">
                        <h2>@lang('frontend.profiel_wijzigen')</h2>
                        @if(\App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@'))
                            <p>@lang('frontend.description_apple_login')</p>
                        @endif
                    </div>
                </div>
                <div class="row custom-col">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="avatar-container">
                            <div class="wrap-avatar">
                                <span class="show-img" data-image="{{ url('/images/black-profile.png') }}">
                                    <img width="100%" src="{{ url($user->photo ?: '/images/black-profile.png') }}" alt="avatar"/>
                                </span>
                                <a class="button-upload-avatar" href="javascript:;">
                                    <img class="avatar-trash-icon" src="{!! url('images/trash.svg') !!}">
                                </a>
                                <input type="hidden" name="deleteAvatar" disabled />
                            </div>
                            <div class="form-group upload-avatar">
                                <input type="file" id="uploadAvatar" name="uploadAvatar" class="input-upload-image upload-avatar hidden">
                                <label for="uploadAvatar">@lang('frontend.klik_hier_om')</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-line @if(empty($user->first_name) || \App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@')) mgb-20 @endif">
                            <div class="input-container">
                                <span>@lang('frontend.voornaam')</span>
                                <input type="text" class="Gianluca @if(empty($user->first_name) || \App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@')) error @endif"
                                       name="first_name"
                                       value="{{ $user->first_name }}"
                                       placeholder="@lang('register.placeholders.first_name')">
                            </div>
                        </div>
                        <div class="form-line @if(empty($user->last_name) || \App\Helpers\Helper::checkSpecialCharacters($user->last_name, '@')) mgb-20 @endif">
                            <div class="input-container">
                                <span>@lang('frontend.naam')</span>
                                <input type="text" class="Punzo @if(empty($user->last_name) || \App\Helpers\Helper::checkSpecialCharacters($user->last_name, '@')) error @endif"
                                       name="last_name"
                                       value="{{ $user->last_name }}"
                                       placeholder="@lang('register.placeholders.last_name')">
                            </div>
                        </div>
                        <div class="form-line text-left @if(empty($user->gsm)) mgb-20 @endif">
                            <div class="input-container" style="display: block !important;">
                                <span>@lang('frontend.gsm')</span>
                                <input type="text" class="Punzo keyup-gsm @if(empty($user->gsm)) error @endif"
                                       name="gsm" value="{{ $user->gsm }}"
                                       placeholder="@lang('register.placeholders.gsm')">
                            </div>
                            <label class=" mb-30">@lang('register.descriptions.gsm')</label>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-line @if(empty($user->email)) mgb-20 @endif">
                            <div class="input-container">
                                <span>@lang('frontend.email')</span>
                                <input type="text" class="email @if(empty($user->email)) error @endif"
                                       name="email"
                                       value="{{ $user->email }}"
                                       placeholder="@lang('register.placeholders.email')">
                            </div>
                        </div>
                        <div class="form-line use-maps">
                            <div class="input-container maps">
                                <span>@lang('frontend.adres')</span>
                                <input type="text" class="location" name="address" value="{{ $user->address }}" placeholder="@lang('frontend.vul_hier_uw')" autocomplete="off">
                                {!! Form::hidden('lat', $user->lat, ['class' => 'latitude']) !!}
                                {!! Form::hidden('lng', $user->lng, ['class' => 'longitude']) !!}
                            </div>
                            <label class="address-optioneel">@lang('frontend.optioneel')</label>
                            <ul class="place-results" style="left: 0 !important;"></ul>
                        </div>
                        <div class="form-line mb-30 visible-pc">
                            <div class="input-container text-left">
                                <span>&nbsp;</span>
                                <button type="submit" class="btn btn-andere opslaan submit1 btn-bottom btn-pr-custom">@lang('frontend.opslaan')</button>
                            </div>
                        </div>
                        <div class="form-line visible-mobile">
                            <div class="input-container text-center col-md-offset-3 col-md-9 mt-30">
                                <button type="submit" class="btn btn-andere opslaan submit1 btn-bottom btn-pr-custom">@lang('frontend.opslaan')</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

<div class="user-modal modal-gms hidden update-gsm">
    {!! Form::open(['url' => route('web.user.updateProfile'), 'method' => "POST", 'files' => TRUE, 'id' => "update_gsm"]) !!}
        <div class="pop-up">
            <div class="row">
                <div class="col-md-6 col-md-push-3">
                    <div class="wrap-popup-card">
                        <a href="javascript:;" class="close">
                            <i class="icn-close"></i>
                        </a>

                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5 style="margin-bottom: 10px">@lang('frontend.gsm_title')</h5>
                                @if(empty($user->gsm) && \App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@'))
                                    <p>@lang('frontend.first_name_gsm_description')</p>
                                @elseif(empty($user->gsm))
                                    <p>@lang('frontend.gsm_description')</p>
                                @elseif(\App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@'))
                                    <p>@lang('frontend.first_name_description')</p>
                                @endif
                            </div>

                            {!! Form::hidden('required_only_gsm', 1) !!}

                            <div class="form-line text-left @if(!empty($user->first_name) && !\App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@')) hidden @endif">
                                <div class="input-container" style="display: block !important;">
                                    <input type="text" class="@if(empty($user->first_name) || \App\Helpers\Helper::checkSpecialCharacters($user->first_name, '@')) error @else hidden @endif"
                                           name="first_name" value="{{ $user->first_name }}"
                                           placeholder="@lang('register.placeholders.first_name')">
                                </div>
                                <label style="color: #717171; margin-left: 0">&nbsp;</label>
                            </div>

                            <div class="form-line text-left @if(!empty($user->gsm)) hidden @endif">
                                <div class="input-container" style="display: block !important;">
                                    <input type="text" class="Punzo keyup-gsm @if(empty($user->gsm)) error @endif"
                                           name="gsm" value="{{ $user->gsm }}"
                                           placeholder="@lang('register.placeholders.gsm')">
                                </div>
                                <label style="color: #717171; margin-left: 0">@lang('register.descriptions.gsm')</label>
                            </div>

                            <button type="submit" class="btn btn-andere opslaan submit1 btn-bottom btn-pr-custom">
                                @lang('frontend.opslaan')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
