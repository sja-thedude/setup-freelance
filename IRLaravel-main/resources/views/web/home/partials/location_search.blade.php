{!! Form::open(['method'=>'POST', 'route' =>['web.search_restaurant']]) !!}
<div class="row use-maps">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-6">
        <div class="form-group maps">
            <svg class="search-logo" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.7885 19.7272C15.1218 19.7272 18.6347 16.1568 18.6347 11.7525C18.6347 7.34821 15.1218 3.77783 10.7885 3.77783C6.45522 3.77783 2.94238 7.34821 2.94238 11.7525C2.94238 16.1568 6.45522 19.7272 10.7885 19.7272Z" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20.5959 21.7205L16.3296 17.3843" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {!! Form::text('address', NULL, [
                'class'=>'form-control location-search location clearable',
                'placeholder'=> trans('dashboard.enter_your_address'),
                'autocomplete' => 'off'
                ]) !!}
            {!! Form::hidden('lat', !empty($lat)?$lat:NULL, ['class' => 'latitude']) !!}
            {!! Form::hidden('long', !empty($long)?$long : NULL, ['class' => 'longitude']) !!}
        </div>
    </div>

    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-6">
        <a type="submit" href="javascript:;" disabled
                class="btn btn-search">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.7885 18.94C15.1218 18.94 18.6347 15.3697 18.6347 10.9654C18.6347 6.5611 15.1218 2.99072 10.7885 2.99072C6.45522 2.99072 2.94238 6.5611 2.94238 10.9654C2.94238 15.3697 6.45522 18.94 10.7885 18.94Z" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20.5962 20.9334L16.3298 16.5972" stroke="#AAAAAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ trans('dashboard.search') }}</a>
    </div>

    <ul class="place-results" id="place-results"></ul>
</div>
{!! Form::close() !!}