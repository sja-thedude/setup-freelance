<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->id !!}
    </div>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->created_at !!}
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->updated_at !!}
    </div>
</div>

<!-- Active Field -->
<div class="form-group">
    {!! Form::label('active', 'Active:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->active !!}
    </div>
</div>

<!-- Workspace Id Field -->
<div class="form-group">
    {!! Form::label('workspace_id', 'Workspace Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->workspace_id !!}
    </div>
</div>

<!-- Code Field -->
<div class="form-group">
    {!! Form::label('code', 'Code:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->code !!}
    </div>
</div>

<!-- Max Time All Field -->
<div class="form-group">
    {!! Form::label('max_time_all', 'Max Time All:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->max_time_all !!}
    </div>
</div>

<!-- Max Time Single Field -->
<div class="form-group">
    {!! Form::label('max_time_single', 'Max Time Single:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->max_time_single !!}
    </div>
</div>

<!-- Currency Field -->
<div class="form-group">
    {!! Form::label('currency', 'Currency:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->currency !!}
    </div>
</div>

<!-- Discount Field -->
<div class="form-group">
    {!! Form::label('discount', 'Discount:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->discount !!}
    </div>
</div>

<!-- Expire Time Field -->
<div class="form-group">
    {!! Form::label('expire_time', 'Expire Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $coupon->expire_time !!}
    </div>
</div>

