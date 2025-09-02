@php
    $orderType = session('orderType');
    $tab = $orderType['type'] == \App\Models\Cart::TYPE_LEVERING ? \App\Models\Cart::TAB_LEVERING : \App\Models\Cart::TAB_TAKEOUT;
@endphp

<div class="hp-slide">
    <div class="col-md-12">
        {!! Form::open(['route' => ['web.cartAddress.store', $workspace['id']], 'method' => 'POST']) !!}
        {!! Form::hidden('type', isset($orderType['type'])?$orderType['type']:\App\Models\Cart::TYPE_TAKEOUT) !!}
        {!! Form::hidden('workspace_id', $workspace['id']) !!}
        {!! Form::hidden('group_id', isset($orderType['group_id'])?$orderType['group_id']:NULL) !!}
        {!! Form::hidden('user_id', $userId??NULL) !!}
        <h6><a href="javascript:;" class="ontdek-ons">@lang('landing.ontdek_ons') <i class="icon-angle-right"></i></a></h6>
        {!! Form::close() !!}
    </div>
    <div class="col-md-12 col-slide">
        <div class="owl-carousel-home">
            @foreach($categories as $k => $category)
                @php($category['photo_path'] = (empty($category['photo_path']) && !empty($category['photo'])) ? Helper::getStoragePathFromUrl($category['photo']) : $category['photo_path'])

                <div class="item">
                    <a href="{!! route($guard.'.category.index', [$category['id']]) !!}?tab={{$tab}}">
                        <img class="owl-lazy" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20255%20330'%3E%3C/svg%3E"
                             data-src="{{Picture::get(Picture::getImageFolder($category['photo_path']), '410x245', Picture::getImageName($category['photo_path']), null, 'c', 'c')}}" alt="Slide">
                        <div class="overlay"></div>
                    </a>
                    <h6><a href="{!! route($guard.'.category.index', [$category['id']]) !!}?tab={{$tab}}">{{$category['name']}}</a></h6>
                </div>
            @endforeach
        </div>
    </div>
</div>