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

<!-- Active Field -->
<div class="form-group">
    {!! Form::label('active', 'Active:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('active', 0) !!}
            {!! Form::checkbox('active', 1, null) !!}
        </label>
    </div>
</div>

<!-- Order Field -->
<div class="form-group">
    {!! Form::label('order', 'Order:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::number('order', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Workspace App Id Field -->
<div class="form-group">
    {!! Form::label('workspace_app_id', 'Workspace App Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::number('workspace_app_id', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Default Field -->
<div class="form-group">
    {!! Form::label('default', 'Default:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('default', 0) !!}
            {!! Form::checkbox('default', 1, null) !!}
        </label>
    </div>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Title Field -->
<div class="form-group">
    {!! Form::label('title', 'Title:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Description Field -->
<div class="form-group">
    {!! Form::label('description', 'Description:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::textarea('description', null, ['class' => 'form-control ckeditor']) !!}
    </div>
</div>

<!-- Content Field -->
<div class="form-group">
    {!! Form::label('content', 'Content:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::textarea('content', null, ['class' => 'form-control ckeditor']) !!}
    </div>
</div>

<!-- Icon Field -->
<div class="form-group">
    {!! Form::label('icon', 'Icon:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::textarea('icon', null, ['class' => 'form-control ckeditor']) !!}
    </div>
</div>

<!-- Url Field -->
<div class="form-group">
    {!! Form::label('url', 'Url:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::textarea('url', null, ['class' => 'form-control ckeditor']) !!}
    </div>
</div>

<!-- Meta Data Field -->
<div class="form-group">
    {!! Form::label('meta_data', 'Meta Data:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::textarea('meta_data', null, ['class' => 'form-control ckeditor']) !!}
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.workspaceAppMetas.index') !!}" class="btn btn-default">Cancel</a>
</div>