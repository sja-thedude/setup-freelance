{!! Form::open(['route' => [$guard.'.rewards.index'], 'method' => 'get', 'class' => 'keypress-search']) !!}

    <div class="ir-group-search">
        {{ Form::text('keyword', NULL, ['class' => 'ir-input', 'placeholder' => trans('product.plh_search')]) }}
        <span class="ir-input-group-btn">
            {!! Form::button(NULL, ['class' => 'ir-btn-search', 'type' => 'submit']) !!}
        </span>
    </div>

{!! Form::close() !!}