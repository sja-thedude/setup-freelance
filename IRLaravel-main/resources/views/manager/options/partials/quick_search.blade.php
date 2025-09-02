{!! Form::open(['route' => [$guard.'.options.index'], 'method' => 'get', 'class' => 'keypress-search']) !!}

<div class="ir-group-search">
    {{ Form::text('keyword_search', NULL, ['class' => 'ir-input', 'placeholder' => trans('option.plh_search')]) }}
    <span class="ir-input-group-btn">
        {!! Form::button(NULL, ['class' => 'ir-btn-search', 'type' => 'submit']) !!}
    </span>
</div>

{!! Form::close() !!}