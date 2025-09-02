@extends('layouts.web-user')
@section('menu')
@endsection
@section('content')
    <div id="container">
        <div id="main-body">
            @include('layouts.partials.error-msg')

            <div class="ap-content ap-error">
                <div class="row">
                    <div class="col-md-6 col-md-push-3 text-center">
                        <img src="{{ url('/images/error.svg') }}" alt="Error">
                        <h5>@lang('errors.invalid_link.title')</h5>
                        <p>@lang('errors.invalid_link.description')</p>
                        {{--<p>{{ $errors->first('verify_token') }}</p>--}}
                        <a href="{{ url('/') }}" class="btn btn-andere">@lang('strings.back')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay"></div>
@endsection

@push('scripts')
    <!-- implement javascript on web page that first first tries to open the deep link
        1. if user has app installed, then they would be redirected to open the app to specified screen
        2. if user doesn't have app installed, then their browser wouldn't recognize the URL scheme
        and app wouldn't open since it's not installed. In 1 second (1000 milliseconds) user is redirected
        to download app from app store.
     -->
    @php($data = [
        'screen' => 'reset_password_failed',
        'redirect_url' => $redirect_url,
    ])
    <script>
        function showContent() {
            document.getElementsByTagName('body')[0].style.display = 'block';
        }

        /**
         * Deep Linking to Your Mobile App from Your Website
         * @link https://tune.docs.branch.io/sdk/deep-linking-to-your-mobile-app-from-your-website/
         */
        window.addEventListener('DOMContentLoaded', (event) => {
            // var confirm = window.confirm('Open in It’s Ready app');
            //
            // if (confirm) {
            var device = getMobileOperatingSystem();
            console.log('device:', device);

            if (device === 'Android') {
                <!-- Deep link URL for existing users with app already installed on their device -->
                window.location = '{{ array_get($config, 'android.deeplink') }}?{!! http_build_query($data) !!}';
                <!-- Download URL (TUNE link) for new users to download the app -->
                {{--setTimeout("window.location = '{{ array_get($config, 'android.download') }}';", 1000);--}}
                setTimeout(function () {
                    showContent();
                }, 1000);
            } else if (device === 'iOS') {
                <!-- Deep link URL for existing users with app already installed on their device -->
                window.location = '{{ array_get($config, 'ios.deeplink') }}?{!! http_build_query($data) !!}';
                <!-- Download URL (TUNE link) for new users to download the app -->
                {{--setTimeout("window.location = '{{ array_get($config, 'ios.download') }}';", 1000);--}}
                setTimeout(function () {
                    showContent();
                }, 1000);
            } else {
                showContent();
            }
            // }

        });
    </script>
@endpush