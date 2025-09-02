@php
    $categoryId = !empty($categories) && !empty($categories[0]) ? $categories[0]['id'] : $workspace['id'];
@endphp

{{-- <div class="wp-content home" id="wrapSwitchAfhaalLevering" style="{{ session()->has('address_not_avaliable') ? "display:none" : "margin-left:-500px; display: none;" }}">
    <div class="row">
        <div class="col-md-12">
            @if($is_takeout)
                {!! Form::open(['route' => ['web.cartAddress.store', $categoryId], 'method' => 'POST', 'class' => 'step-order']) !!}
                    {!! Form::hidden('type', \App\Models\Cart::TYPE_TAKEOUT) !!}
                    {!! Form::hidden('workspace_id', $workspace['id']) !!}
                    {!! Form::hidden('group_id', NULL) !!}
                    {!! Form::hidden('user_id', $userId) !!}
                    <button class="btn btn-home" type="submit">
                        @lang('landing.switch_afhaal')
                    </button>
                {!! Form::close() !!}
            @endif
            @if($is_delivery)
                <a href="javascript:;" class="btn btn-home btnLevering" data-redirect="{{ !$userId ? route('login') . "?from=group" : "" }}">
                    @lang('landing.switch_levering')
                </a>
            @endif
            @if($is_group_order)
                <h6>
                    <a href="javascript:;" class="btnSearchGroup dark-grey" data-redirect="{{ !$userId ? route('login') . "?from=group" : "" }}">
                        @lang('landing.wenst_u_te')
                    </a>
                </h6>
            @endif
        </div>
    </div>
</div> --}}

{{--Fill address to delevering--}}
<div class="wp-content" id="wrapFillAddress" style="{{ !session()->has('address_not_avaliable') ? "display:none" : "" }}">
    {!! Form::open(['route' => ['web.cartAddress.store', $categoryId], 'name' => 'order', 'method' => 'POST', 'class' => 'step-order']) !!}
        <div class="wrap-step active" data-id="1">
            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-12">
                    <div class="wrap-action">
                        <a href="javascript:;" class="backStep mb-30 dark-grey" style="display:inline-block" data-step="1">
                            <i class="icn-arrow-left"></i>
                            @lang('landing.btn_terug')
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 col-xs-12 col-sm-12">
                    @include('web.partials.fill-address')
                </div>
                <div class="clear-fix"></div>
                <div class="col-md-12 col-xs-12 col-sm-12">
                    <div class="form-line">
                        <button class="btn btn-disable btn-order" @if (auth()->guest()) disabled @endif type="submit">
                            @lang('landing.btn_bestelling')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>

{{--Fill and search group--}}
<div class="wp-content" id="wrapSearchGroup" style="display:none">

    {!! Form::open(['route' => ['web.cartAddress.store', $categoryId], 'name' => 'order', 'method' => 'POST', 'class' => 'step-order']) !!}

        {!! Form::hidden('group_id', NULL) !!}
        {!! Form::hidden('name_group', NULL) !!}
        {!! Form::hidden('type', NULL) !!}
        {!! Form::hidden('workspace_id', $workspace['id']) !!}
        {!! Form::hidden('user_id', $userId) !!}

        <div class="wrap-step active" data-id="1">
            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-12">
                    <div class="wrap-action">
                        <a href="javascript:;" class="backStep mb-30 dark-grey" style="display:inline-block" data-step="1">
                            <i class="icn-arrow-left"></i>
                            @lang('landing.btn_terug')
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3 mgl-10">
                    <div class="form-line">
                        <div class="form-input">
                            <input id="m-keyword-search-groups" data-route="{{ route('web.groups.index') }}"
                                   type="search" placeholder="@lang('frontend.typ_hier_de')" >
                        </div>
                    </div>
                </div>
                <div class="clear-fix"></div>
                <div class="col-md-12 col-xs-12 col-sm-12">
                    <div class="form-line">
                        <button class="btn disableBtn btn-order" disabled>
                            @lang('landing.btn_bestelling')
                        </button>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <p>
                        @lang('frontend.interesse_om_als', ['url' => route('web.contact.index')]).
                    </p>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>