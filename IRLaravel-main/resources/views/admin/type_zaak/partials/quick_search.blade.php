{!! Form::open(['route' => [$guard.'.type-zaak.index'], 'method' => 'get', 'class' => 'keypress-search']) !!}

<div class="ir-group-search mgr-20">
    {{ Form::text('keyword', null, ['class' => 'ir-input', 'placeholder' => trans('type_zaak.placeholder_search')]) }}
    <span class="ir-input-group-btn">
        {!! Form::button(null, ['class' => 'ir-btn-search submit', 'type' => 'submit']) !!}
    </span>
</div>

{!! Form::close() !!}