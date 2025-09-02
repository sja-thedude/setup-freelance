<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->id !!}
    </div>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->created_at !!}
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->updated_at !!}
    </div>
</div>

<!-- Deleted At Field -->
<div class="form-group">
    {!! Form::label('deleted_at', 'Deleted At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->deleted_at !!}
    </div>
</div>

<!-- Workspace Id Field -->
<div class="form-group">
    {!! Form::label('workspace_id', 'Workspace Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->workspace_id !!}
    </div>
</div>

<!-- User Id Field -->
<div class="form-group">
    {!! Form::label('user_id', 'User Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->user_id !!}
    </div>
</div>

<!-- Setting Payment Id Field -->
<div class="form-group">
    {!! Form::label('setting_payment_id', 'Setting Payment Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->setting_payment_id !!}
    </div>
</div>

<!-- Open Timeslot Id Field -->
<div class="form-group">
    {!! Form::label('open_timeslot_id', 'Open Timeslot Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->open_timeslot_id !!}
    </div>
</div>

<!-- Group Id Field -->
<div class="form-group">
    {!! Form::label('group_id', 'Group Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->group_id !!}
    </div>
</div>

<!-- Coupon Id Field -->
<div class="form-group">
    {!! Form::label('coupon_id', 'Coupon Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->coupon_id !!}
    </div>
</div>

<!-- Daily Id Field -->
<div class="form-group">
    {!! Form::label('daily_id', 'Daily Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->daily_id !!}
    </div>
</div>

<!-- Payment Method Field -->
<div class="form-group">
    {!! Form::label('payment_method', 'Payment Method:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->payment_method !!}
    </div>
</div>

<!-- Payment Status Field -->
<div class="form-group">
    {!! Form::label('payment_status', 'Payment Status:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->payment_status !!}
    </div>
</div>

<!-- Coupon Code Field -->
<div class="form-group">
    {!! Form::label('coupon_code', 'Coupon Code:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->coupon_code !!}
    </div>
</div>

<!-- Date Time Field -->
<div class="form-group">
    {!! Form::label('date_time', 'Date Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->date_time !!}
    </div>
</div>

<!-- Time Field -->
<div class="form-group">
    {!! Form::label('time', 'Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->time !!}
    </div>
</div>

<!-- Date Field -->
<div class="form-group">
    {!! Form::label('date', 'Date:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->date !!}
    </div>
</div>

<!-- Address Field -->
<div class="form-group">
    {!! Form::label('address', 'Address:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->address !!}
    </div>
</div>

<!-- Address Type Field -->
<div class="form-group">
    {!! Form::label('address_type', 'Address Type:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->address_type !!}
    </div>
</div>

<!-- Type Field -->
<div class="form-group">
    {!! Form::label('type', 'Type:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->type !!}
    </div>
</div>

<!-- Meta Data Field -->
<div class="form-group">
    {!! Form::label('meta_data', 'Meta Data:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->meta_data !!}
    </div>
</div>

<!-- Note Field -->
<div class="form-group">
    {!! Form::label('note', 'Note:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->note !!}
    </div>
</div>

<!-- Total Price Field -->
<div class="form-group">
    {!! Form::label('total_price', 'Total Price:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->total_price !!}
    </div>
</div>

<!-- Currency Field -->
<div class="form-group">
    {!! Form::label('currency', 'Currency:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $order->currency !!}
    </div>
</div>

