<!-- implement javascript on web page that first first tries to open the deep link
    1. if user has app installed, then they would be redirected to open the app to specified screen
    2. if user doesn't have app installed, then their browser wouldn't recognize the URL scheme
    and app wouldn't open since it's not installed. In 1 second (1000 milliseconds) user is redirected
    to download app from app store.
 -->

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
            window.location = '{{ config('mobile.android.deeplink') }}?{!! http_build_query($data) !!}';
            <!-- Download URL (TUNE link) for new users to download the app -->
            {{--setTimeout("window.location = '{{ config('mobile.android.download') }}';", 1000);--}}
            setTimeout(function () {
                showContent();
            }, 1000);
        } else if (device === 'iOS') {
            <!-- Deep link URL for existing users with app already installed on their device -->
            window.location = '{{ config('mobile.ios.deeplink') }}?{!! http_build_query($data) !!}';
            <!-- Download URL (TUNE link) for new users to download the app -->
            {{--setTimeout("window.location = '{{ config('mobile.ios.download') }}';", 1000);--}}
            setTimeout(function () {
                showContent();
            }, 1000);
        } else {
            showContent();
        }
        // }

    });
</script>