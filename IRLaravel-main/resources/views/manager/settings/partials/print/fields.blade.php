@if(!$printer->isEmpty())
    @foreach ($printer as $key => $item)
        <div id="tr-{{ $item->id }}" class="full-width pull-left list-item-none" data-number="{!! $key !!}">
            {!! Form::hidden($field.'['.$key.'][id]', !empty($item->id) ? $item->id : null) !!}
            {!! Form::hidden($field.'['.$key.'][type]', $type) !!}
            <div class="col-md-5">
                <div class="form-group form-with-text">
                    <span class="text-block mgr-10 normal-text row-number">{{$key+1}}.</span>
                    {!! Form::text($field.'['.$key.'][mac]', !empty($item->mac) ? $item->mac : null, [
                        'class' => 'form-control auto-submit large', 
                        'data-type' => 'print',
                        'placeholder' => trans('setting.more.mac')
                    ]) !!}
                    {!! Form::number($field.'['.$key.'][copy]', !empty($item->copy) ? $item->copy : null, [
                        'class' => 'form-control auto-submit medium', 
                        'data-type' => 'print',
                        'data-number' => 'true',
                        'placeholder' => trans('setting.more.copy')
                    ]) !!}
                    
                    @if($key != 0)
                        <a class="ir-a top-8 show-hide-area remove-print auto-submit" data-id="print-name">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H5H21" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 11V17" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 11V17" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group form-with-text">
                    <span class="pull-left text-block normal-text mgr-10">@lang('setting.more.print_automatically')</span>
                    <span class="mgr-8">
                        <input type="checkbox" name="{{$field}}[{{$key}}][auto]" id="{{$field}}-{{$type}}-auto-{{$key}}" 
                           class="switch-input auto-submit" 
                           data-type="print" {{!empty($item->auto) ? 'checked' : null}}/>
                        <label for="{{$field}}-{{$type}}-auto-{{$key}}" class="switch mg-0"></label>
                    </span>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="full-width pull-left list-item-none" data-number="0">
        {!! Form::hidden($field.'[0][id]', 0) !!}
        {!! Form::hidden($field.'[0][type]', $type) !!}
        <div class="col-md-4">
            <div class="form-group form-with-text">
                <span class="text-block mgr-10 normal-text row-number">1.</span>
                {!! Form::text($field.'[0][mac]', null, [
                    'class' => 'form-control auto-submit large', 
                    'data-type' => 'print',
                    'placeholder' => trans('setting.more.mac')
                ]) !!}
                {!! Form::number($field.'[0][copy]', null, [
                    'class' => 'form-control auto-submit medium', 
                    'data-type' => 'print',
                    'placeholder' => trans('setting.more.copy')
                ]) !!}
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group form-with-text">
                <span class="pull-left text-block normal-text mgr-10">@lang('setting.more.print_automatically')</span>
                <span class="mgr-8">
                    <input type="checkbox" name="{{$field}}[0][auto]" id="{{$field}}-{{$type}}-auto-0" 
                       class="switch-input auto-submit" 
                       data-type="print"/>
                    <label for="{{$field}}-{{$type}}-auto-0" class="switch mg-0"></label>
                </span>
            </div>
        </div>
    </div>
@endif