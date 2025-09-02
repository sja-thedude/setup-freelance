@extends('layouts.manager')

@section('content')
    <div class="row general">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting.more.payment_methods')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    {!! Form::model($settingPayments, [
                        'route' => [$guard.'.settingPayments.updateOrCreate', 
                        $tmpWorkspace->id], 
                        'method' => 'post', 
                        'files' => true, 
                        'class' => 'update-form-payment-methods',
                    ]) !!}
                        <div class="row">
                            <div class="col-md-4 mgt-35 pdr-25"></div>
                            <div class="col-md-1">
                                {!! Html::decode(Form::label('takeout', trans('setting.more.takeout'), ['class' => 'ir-label payment-label-switch'])) !!}
                            </div>
                            <div class="col-md-1">
                                {!! Html::decode(Form::label('delivery', trans('setting.more.delivery'), ['class' => 'ir-label payment-label-switch'])) !!}
                            </div>

                            @if($enableInHouse)
                            <div class="col-md-1">
                                {!! Html::decode(Form::label('in_house', trans('setting.more.in_house'), ['class' => 'ir-label payment-label-switch'])) !!}
                            </div>
                            @endif

                            @if($enableSelfOrdering)
                            <div class="col-md-1">
                                {{ Form::label('self_ordering', trans('setting.more.self_ordering'), ['class' => 'ir-label payment-label-switch']) }}
                            </div>
                            @endif

                            @if(!empty($connectorsList) && !$connectorsList->isEmpty())
                                @foreach($connectorsList as $connectorItem)
                                    <div class="col-md-3">
                                        {!! Html::decode(Form::label('delivery', $connectorItem->getProviders($connectorItem->provider), ['class' => 'ir-label payment-label-switch'])) !!}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-4 pdr-25">
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        {!! Html::decode(Form::label('mollie', trans('setting.more.mollie'), ['class' => 'ir-label payment-label-switch'])) !!}
                                    </div>
                                    <div class="col-md-10">
                                        {!! Form::text('payments[0][api_token]', !empty($settingPayments[0]) ? $settingPayments[0]->api_token : null, [
                                        'class' => 'form-control auto-submit', 
                                        'data-type' => 'payment_methods',
                                        'placeholder' => trans('setting.more.api-token')
                                        ]) !!}
                                        {!! Form::hidden('payments[0][type]', \App\Models\SettingPayment::TYPE_MOLLIE) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[0][takeout]" id="switch-0-takeout" 
                                               class="switch-input auto-submit" 
                                               data-type="payment_methods" {{!empty($settingPayments[0]) && $settingPayments[0]->takeout ? 'checked' : null}}/>
                                        <label for="switch-0-takeout" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[0][delivery]" id="switch-0-delivery" 
                                               class="switch-input auto-submit" 
                                               data-type="payment_methods" {{!empty($settingPayments[0]) && $settingPayments[0]->delivery ? 'checked' : null}}/>
                                        <label for="switch-0-delivery" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>

                            @if($enableInHouse)
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[0][in_house]" id="switch-0-in_house"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[0]) && $settingPayments[0]->in_house ? 'checked' : null}}/>
                                        <label for="switch-0-in_house" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            @endif

                            @if($enableSelfOrdering)
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[0][self_ordering]" id="switch-0-self_ordering"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[0]) && $settingPayments[0]->self_ordering ? 'checked' : null}}/>
                                        <label for="switch-0-self_ordering" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            @endif

                            @if(!empty($connectorsList) && !$connectorsList->isEmpty())
                                @foreach($connectorsList as $connectorItem)
                                    @php

                                    $paymentReference = null;
                                    if(!empty($settingPayments[0])):
                                        $paymentReference = $settingPayments[0]->paymentReferences()
                                            ->where('provider', $connectorItem->provider)
                                            ->where('local_id', $settingPayments[0]->id)
                                            ->first();
                                    endif;

                                    @endphp
                                    <div class="col-md-3">
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                {!! Form::text('paymentReferences[0]['.$connectorItem->id.'][remote_id]', !empty($paymentReference->remote_id) ? $paymentReference->remote_id : null, [
                                                'class' => 'form-control auto-submit',
                                                'data-type' => 'payment_methods',
                                                'placeholder' => trans('setting.more.remote-id')
                                                ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::text('paymentReferences[0]['.$connectorItem->id.'][remote_name]', !empty($paymentReference->remote_name) ? $paymentReference->remote_name : null, [
                                                'class' => 'form-control auto-submit',
                                                'data-type' => 'payment_methods',
                                                'placeholder' => trans('setting.more.remote-name')
                                                ]) !!}
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            @endif
                        </div>
                    
                        {{--@php
                            $disableClass = !empty($payconiq) && !empty($payconiq->active) ? null : 'disable-payment';
                            $disableCheckbox = !empty($payconiq) && !empty($payconiq->active) ? null : 'disabled';
                            $readonly = !empty($payconiq) && !empty($payconiq->active) ? null : 'readonly';
                        @endphp
                        <div class="row {{$disableClass}}">
                            <div class="col-md-4 pdr-25">
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        {!! Html::decode(Form::label('payconiq', trans('setting.more.payconiq'), ['class' => 'ir-label payment-label-switch'])) !!}
                                    </div>
                                    <div class="col-md-10">
                                        {!! Form::text('payments[1][api_token]', !empty($settingPayments[1]) ? $settingPayments[1]->api_token : null, [
                                        'class' => 'form-control auto-submit',
                                        'data-type' => 'payment_methods',
                                        'placeholder' => trans('setting.more.api-token'),
                                        $readonly
                                        ]) !!}
                                        {!! Form::hidden('payments[1][type]', \App\Models\SettingPayment::TYPE_PAYCONIQ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[1][takeout]" id="switch-1-takeout"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[1]) && $settingPayments[1]->takeout ? 'checked' : null}} {{$disableCheckbox}}/>
                                        <label for="switch-1-takeout" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[1][delivery]" id="switch-1-delivery"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[1]) && $settingPayments[1]->delivery ? 'checked' : null}} {{$disableCheckbox}}/>
                                        <label for="switch-1-delivery" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[1][in_house]" id="switch-1-in_house"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[1]) && $settingPayments[1]->in_house ? 'checked' : null}} {{$disableCheckbox}}/>
                                        <label for="switch-1-in_house" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                        </div>--}}
                        <div class="row">
                            <div class="col-md-4 pdr-25">
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        {!! Html::decode(Form::label('cash', trans('setting.more.cash'), ['class' => 'ir-label payment-label-switch'])) !!}
                                    </div>
                                    <div class="col-md-10">
                                        {!! Form::hidden('payments[2][type]', \App\Models\SettingPayment::TYPE_CASH) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[2][takeout]" id="switch-2-takeout" 
                                               class="switch-input auto-submit" 
                                               data-type="payment_methods" {{!empty($settingPayments[2]) && $settingPayments[2]->takeout ? 'checked' : null}}/>
                                        <label for="switch-2-takeout" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[2][delivery]" id="switch-2-delivery" 
                                               class="switch-input auto-submit" 
                                               data-type="payment_methods" {{!empty($settingPayments[2]) && $settingPayments[2]->delivery ? 'checked' : null}}/>
                                        <label for="switch-2-delivery" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>

                            @if($enableInHouse)
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[2][in_house]" id="switch-2-in_house"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[2]) && $settingPayments[2]->in_house ? 'checked' : null}}/>
                                        <label for="switch-2-in_house" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            @endif

                            @if($enableSelfOrdering)
                            <div class="col-md-1">
                                <div class="form-group payment-label-switch">
                                    <span>
                                        <input type="checkbox" name="payments[2][self_ordering]" id="switch-2-self_ordering"
                                               class="switch-input auto-submit"
                                               data-type="payment_methods" {{!empty($settingPayments[2]) && $settingPayments[2]->self_ordering ? 'checked' : null}}/>
                                        <label for="switch-2-self_ordering" class="switch mg-0"></label>
                                    </span>
                                </div>
                            </div>
                            @endif

                            @if(!empty($connectorsList) && !$connectorsList->isEmpty())
                                @foreach($connectorsList as $connectorItem)
                                    @php

                                    $paymentReference = null;
                                    if(!empty($settingPayments[2])):
                                        $paymentReference = null;
                                        if(!empty($settingPaymentReferences)):
                                            foreach($settingPaymentReferences as $settingPaymentReferenceItem):
                                                if(
                                                    !empty($settingPayments[2])
                                                    && $settingPaymentReferenceItem->provider == $connectorItem->provider
                                                    && $settingPaymentReferenceItem->local_id == $settingPayments[2]->id
                                                ):
                                                    $paymentReference = $settingPaymentReferenceItem;
                                                    break;
                                                endif;
                                            endforeach;
                                        endif;
                                    endif;

                                    @endphp
                                    <div class="col-md-3">
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                {!! Form::text('paymentReferences[2]['.$connectorItem->id.'][remote_id]', !empty($paymentReference->remote_id) ? $paymentReference->remote_id : null, [
                                                'class' => 'form-control auto-submit',
                                                'data-type' => 'payment_methods',
                                                'placeholder' => trans('setting.more.remote-id')
                                                ]) !!}
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::text('paymentReferences[2]['.$connectorItem->id.'][remote_name]', !empty($paymentReference->remote_name) ? $paymentReference->remote_name : null, [
                                                'class' => 'form-control auto-submit',
                                                'data-type' => 'payment_methods',
                                                'placeholder' => trans('setting.more.remote-name')
                                                ]) !!}
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            @endif

                        </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection