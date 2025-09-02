<footer>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 logo-socials">
                <a href="{!! route($guard.'.index') !!}" class="logo">
                    <img width="139px" height="97px" src="{!! url('assets/images/logo/new_logo.svg') !!}">
                </a>
                <ul class="group-social">
                    <li><a href="#" class="instagram"></a></li>
                    <li><a href="#" class="facebook"></a></li>
                    <li><a href="#" class="linkedin"></a></li>
                    <li><a href="#" class="email"></a></li>
                </ul>
            </div>
            <div class="col-md-7 web-links">
                <div class="col-md-5">
                    <a href="{{ Url('/') }}/{{App::getLocale()}}/how-does-it-work.html">
                        {{trans('dashboard.how_does_it_work')}}
                    </a>
                    <a href="#">
                        {{trans('dashboard.register_as_a_trader')}}
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ Url('/') }}/{{App::getLocale()}}/faqs.html">
                        {{trans('dashboard.faq')}}
                    </a>
                    <a href="{{ Url('/') }}/{{App::getLocale()}}/contact.html">
                        {{trans('strings.contact.title_detail')}}
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ Url('/') }}/{{App::getLocale()}}/terms-and-conditions.html">
                        {{trans('dashboard.terms_and_conditions')}}
                    </a>
                    <a href="{{ Url('/') }}/{{App::getLocale()}}/privacy-policy.html">
                        {{trans('dashboard.privacy')}}
                    </a>
                    <a href="{{ Url('/') }}/{{App::getLocale()}}/cookie-policy.html">
                        {{trans('dashboard.cookie_policy')}}
                    </a>
                </div>
            </div>
            <div class="col-md-3 new-traders">
                <h6>{{trans('dashboard.new_traders')}}</h6>
                @foreach($restaurants as $restaurant)
                    <p><a href="{{\App\Facades\Helper::getSubDomainOfWorkspace($restaurant->id)}}" target="_blank">{{$restaurant->name}}</a></p>
                @endforeach
                <div class="clearfix"></div>
            </div>
            <div class="col-md-2 m-logo-socials">
                <a href="{!! route($guard.'.index') !!}" class="logo">
                    <img width="139px" height="97px" src="{!! url('assets/images/logo/new_logo.svg') !!}">
                </a>
                <ul class="group-social">
                    <li><a href="#" class="instagram"></a></li>
                    <li><a href="#" class="facebook"></a></li>
                    <li><a href="#" class="linkedin"></a></li>
                    <li><a href="#" class="email"></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="powered_by">
        {{date('Y')}} © It’s Ready -  Powered By <a href="http://www.convisto.be/" target="_blank" class="yellow">Convisto</a>
    </div>
</footer>