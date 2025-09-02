<div class="user-modal modal-fill-address modelFillAddress {{ session()->has('modal_fill_address') ? "" : "hidden" }}">

    {!! Form::open(['route' => ['web.cartAddress.store', $workspace['id']], 'method' => 'POST']) !!}
        <div class="pop-up">
            <div class="row">
                <div class="col-md-6 col-md-push-3">
                    <div class="wrap-popup-card">
                        <a @if(session()->has('modal_fill_address'))
                                onclick="window.location='{{ route('web.category.index') }}'"
                           @else
                                href="javascript:;"
                           @endif class="close"
                        >
                            <i class="icn-close"></i>
                        </a>

                        <div class="wp-card">
                            <div class="row modal-error-title">
                                <h5>@lang('cart.op_welk_adres_wenst')</h5>
                            </div>

                            {!! Form::hidden('switch_tab', 1) !!}

                            @include('web.partials.fill-address', ['isModal' => true])

                            <button class="btn btn-modal btn-modal-error btn-modal-save btn-pr-custom" type="submit" @if (auth()->guest()) disabled @endif>
                                @lang('frontend.opslaan')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}

</div>