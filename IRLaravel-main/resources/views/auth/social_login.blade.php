@php
    $showGoogle = true;
    if(empty(config('common.social_google'))) {
        $showGoogle = false;
    }
    if(empty($workspace) && (empty(config('services.google.client_id')) || empty(config('services.google.client_secret')))) {
        $showGoogle = false;
    }
    if(!empty($workspace) && (empty($workspace['google_enabled']))) {
        $showGoogle = false;
    }

    $showFacebook = true;
    if(empty(config('common.social_facebook'))) {
        $showFacebook = false;
    }
    if((empty(config('services.facebook.client_id')) || empty(config('services.facebook.client_secret')))) {
        $showFacebook = false;
    }
    if(!empty($workspace) && (empty($workspace['facebook_enabled']))) {
        $showFacebook = false;
    }

    $showApple = true;
    if(empty(config('common.social_apple'))) {
        $showApple = false;
    }
    if(empty($workspace) && (empty(config('services.apple.client_id')) || empty(config('services.apple.client_secret')))) {
        $showApple = false;
    }
    if(!empty($workspace) && (empty($workspace['apple_enabled']))) {
        $showApple = false;
    }
@endphp

@if(!empty($showGoogle) || !empty($showFacebook) || !empty($showApple))
    <div class="login-social {!! !empty($horizontal) ? 'horizontal' : '' !!}" style="{!! !empty($style) ? $style : '' !!}">
        <span class="of text-uppercase">
            @lang('auth.of')
        </span>
        <div class="row">
            <div class="col-sm-12 icon-login-social">
                <p>@lang('auth.'.$site)</p>
                @if(!empty($showGoogle))
                    <a 
                       href="{!! route('login_social_redirect', [
                            'provider' => \App\Models\UserSocial::PROVIDER_GOOGLE,
                            'workspace_id' => !empty($workspace['id']) ? $workspace['id'] : 0
                        ]) !!}">
                        <img src="{!! url('assets/images/google.svg') !!}"/>
                    </a>
                @endif
                @if(!empty($showFacebook))
                    <a 
                       href="{!! route('login_social_redirect', [
                            'provider' => \App\Models\UserSocial::PROVIDER_FACEBOOK,
                            'workspace_id' => !empty($workspace['id']) ? $workspace['id'] : 0
                        ]) !!}">
                        <img src="{!! url('assets/images/facebook.svg') !!}"/>
                    </a>
                @endif
                @if(!empty($showApple))
                    <a 
                       href="{!! route('login_social_redirect', [
                            'provider' => \App\Models\UserSocial::PROVIDER_APPLE,
                            'workspace_id' => !empty($workspace['id']) ? $workspace['id'] : 0
                        ]) !!}">
                        <img src="{!! url('assets/images/apple.svg') !!}"/>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif