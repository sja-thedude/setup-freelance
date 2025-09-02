{!! Form::open(['route' => [$guard.'.users.index'], 'method' => 'get', 'class' => 'keypress-search']) !!}

<div class="ir-group-search mgr-20">
    {{ Form::text('keyword', null, ['class' => 'ir-input', 'placeholder' => trans('user.placeholder_search_manager')]) }}
    <span class="ir-input-group-btn">
        {!! Form::button(null, ['class' => 'ir-btn-search submit', 'type' => 'submit']) !!}
    </span>
</div>

{!! Form::close() !!}