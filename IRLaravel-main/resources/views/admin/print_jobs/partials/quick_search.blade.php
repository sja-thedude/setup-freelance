{!! Form::open(['route' => [$guard.'.printjob.index'], 'method' => 'post', 'class' => 'keypress-search']) !!}

<div class="row print-job-filter mgt-30">
    <div class="col-sm-3 col-xs-12">
        {{ Form::select('workspace_id', $workspaces->pluck('name', 'id'), null, [
            'class' => 'form-control select2',
            'placeholder' => trans('printjob.all_restaurant')
        ]) }}
    </div>
    <div class="col-sm-8 col-xs-12">
        {!! Form::submit(trans('printjob.send'), ['class' => 'ir-btn ir-btn-primary']) !!}
        <a class="ir-btn ir-btn-secondary mgl-15" href="{!! route($guard.'.printjob.index') !!}">
            @lang('common.show_all')
        </a>
    </div>
</div>

{!! Form::close() !!}