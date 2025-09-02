{!! Form::open(['route' => [$guard.'.products.index'], 'method' => 'get', 'class' => 'keypress-search']) !!}

    <div class="ir-group-search">
        {{ Form::text('keyword_search', NULL, ['class' => 'ir-input', 'placeholder' => trans('product.plh_search')]) }}
        <span class="ir-input-group-btn">
            {!! Form::button(NULL, ['class' => 'ir-btn-search', 'type' => 'submit']) !!}
        </span>
        <input type="hidden" name="naam" value="{{ request()->has('naam') ? request()->get('naam') : NULL }}">
        <input type="hidden" name="prijs" value="{{ request()->has('prijs') ? request()->get('prijs') : NULL }}">
        <input type="hidden" name="category_id_accordion" value="" />
    </div>

{!! Form::close() !!}