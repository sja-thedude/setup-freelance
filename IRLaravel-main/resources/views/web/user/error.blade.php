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
                        <h5>@lang('frontend.error_title')</h5>
                        <p>@lang('frontend.error_description')</p>
                        <a href="{!! route($guard.'.index') !!}" class="btn btn-andere">@lang('strings.back')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay"></div>

    @if (request('is_api') == 1)
        @php($data = [
            'screen'   => 'payment_failed',
            'order_id' => request()->get('order_id'),
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
    @endif
@endsection
