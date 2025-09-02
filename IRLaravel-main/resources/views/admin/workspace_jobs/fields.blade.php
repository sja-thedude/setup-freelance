<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::date('created_at', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::date('updated_at', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Workspace Id Field -->
<div class="form-group">
    {!! Form::label('workspace_id', 'Workspace Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::number('workspace_id', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Email Field -->
<div class="form-group">
    {!! Form::label('email', 'Email:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::email('email', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Phone Field -->
<div class="form-group">
    {!! Form::label('phone', 'Phone:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('phone', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Content Field -->
<div class="form-group">
    {!! Form::label('content', 'Content:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::textarea('content', null, ['class' => 'form-control ckeditor']) !!}
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.workspaceJobs.index') !!}" class="btn btn-default">Cancel</a>
</div>