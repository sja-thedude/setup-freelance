@extends('layouts.manager')

@section('content')
    <div class="row general">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('setting.more.print')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    <div class="row mgb-20">
                        <div class="col-sm-12 col-xs-12">
                            <h4 class="label-title">
                                @lang('setting.print_endpoint')
                            </h4>
                            <span>
                                <strong>STAR</strong>: {!! route('api.printer_jobs.printerStarAskJob', ['workspaceId' => $tmpWorkspace->id]) !!}<br />
                                <strong>EPSON</strong>: {!! route('api.printer_jobs.printerEpson', ['workspaceId' => $tmpWorkspace->id]) !!}<br />
                            </span>
                        </div>
                    </div>

                    {!! Form::open(['route' => [$guard.'.settingPrint.updateOrCreate', $tmpWorkspace->id], 'method' => 'post', 'files' => true, 'class' => 'update-form-print']) !!}
                        <div class="row">
                            <div class="col-md-12 pdr-25">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="label-title">
                                            @lang('setting.more.receipt_printers') 
                                            <a href="javascript:;" class="mgl-5 btn-print-add" 
                                               data-id="#receipt-printer" 
                                               data-type="{{\App\Models\SettingPrint::TYPE_KASSABON}}" 
                                               data-field="print">
                                                <img src="{!! url('assets/images/icon-plus.svg') !!}" /> @lang('common.add')
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="receipt-printer">
                                        <div class="list-body">
                                            @include($guard.'.settings.partials.print.fields', [
                                                'printer' => $receiptPrinter, 
                                                'field' => 'print',
                                                'type' => \App\Models\SettingPrint::TYPE_KASSABON
                                            ])
                                        </div>
                                        <div class="list-footer">
                                            @include($guard.'.settings.partials.print.field', ['type' => \App\Models\SettingPrint::TYPE_KASSABON, 'field' => 'print'])
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="label-title">
                                            @lang('setting.more.work_order_printer') 
                                            <a href="javascript:;" class="mgl-5 btn-print-add" 
                                               data-id="#work-order-printer" 
                                               data-type="{{\App\Models\SettingPrint::TYPE_WERKBON}}" 
                                               data-field="work_order">
                                                <img src="{!! url('assets/images/icon-plus.svg') !!}"/> @lang('common.add')
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="work-order-printer">
                                        <div class="list-body">
                                            @include($guard.'.settings.partials.print.fields', [
                                                'printer' => $workOrderPrinter, 
                                                'field' => 'work_order',
                                                'type' => \App\Models\SettingPrint::TYPE_WERKBON
                                            ])
                                        </div>
                                        <div class="list-footer">
                                            @include($guard.'.settings.partials.print.field', ['type' => \App\Models\SettingPrint::TYPE_WERKBON, 'field' => 'work_order'])
                                        </div>
                                    </div>
                                </div>
                                
                                @php
                                    $isShowSticker = !empty($tmpWorkspace->workspaceExtras) ? $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::STICKER)->first() : null
                                @endphp
                                @if (isset($tmpWorkspace) && $isShowSticker && $isShowSticker->active)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="label-title">
                                                @lang('setting.more.sticker_printer') 
                                            </h4>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-with-text">
                                                {!! Form::hidden('sticker[id]', !empty($stickerPrinter) ? $stickerPrinter->id : null) !!}
                                                {!! Form::hidden('sticker[type]', \App\Models\SettingPrint::TYPE_STICKER) !!}
                                                {!! Form::text('sticker[mac]', !empty($stickerPrinter) ? $stickerPrinter->mac : null, [
                                                    'class' => 'form-control auto-submit large', 
                                                    'data-type' => 'print',
                                                    'placeholder' => trans('setting.more.mac')
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group form-with-text">
                                                <span class="pull-left text-block normal-text mgr-10">@lang('setting.more.sticker_print_automatically')</span>
                                                <span class="mgr-8">
                                                    <input type="checkbox" name="sticker[auto]" id="auto" 
                                                       class="switch-input auto-submit" 
                                                       data-type="print" {{!empty($stickerPrinter->auto) ? 'checked' : null}}/>
                                                    <label for="auto" class="switch mg-0"></label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-with-text">
                                                <span class="pull-left text-block normal-text mgr-10">@lang('setting.more.print_according_to')</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::select('sticker[type_id]', $printTypes, !empty($stickerPrinter) ? $stickerPrinter->type_id : null, [
                                                'class' => 'form-control select2 pull-left auto-submit', 
                                                'data-type' => 'print'
                                            ]) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection