<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->id !!}
    </div>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->created_at !!}
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->updated_at !!}
    </div>
</div>

<!-- Workspace Id Field -->
<div class="form-group">
    {!! Form::label('workspace_id', 'Workspace Id:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->workspace_id !!}
    </div>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->name !!}
    </div>
</div>

<!-- Company Name Field -->
<div class="form-group">
    {!! Form::label('company_name', 'Company Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->company_name !!}
    </div>
</div>

<!-- Company Street Field -->
<div class="form-group">
    {!! Form::label('company_street', 'Company Street:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->company_street !!}
    </div>
</div>

<!-- Company Number Field -->
<div class="form-group">
    {!! Form::label('company_number', 'Company Number:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->company_number !!}
    </div>
</div>

<!-- Company Vat Number Field -->
<div class="form-group">
    {!! Form::label('company_vat_number', 'Company Vat Number:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->company_vat_number !!}
    </div>
</div>

<!-- Company City Field -->
<div class="form-group">
    {!! Form::label('company_city', 'Company City:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->company_city !!}
    </div>
</div>

<!-- Company Postcode Field -->
<div class="form-group">
    {!! Form::label('company_postcode', 'Company Postcode:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->company_postcode !!}
    </div>
</div>

<!-- Payment Mollie Field -->
<div class="form-group">
    {!! Form::label('payment_mollie', 'Payment Mollie:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->payment_mollie !!}
    </div>
</div>

<!-- Payment Payconiq Field -->
<div class="form-group">
    {!! Form::label('payment_payconiq', 'Payment Payconiq:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->payment_payconiq !!}
    </div>
</div>

<!-- Payment Cash Field -->
<div class="form-group">
    {!! Form::label('payment_cash', 'Payment Cash:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->payment_cash !!}
    </div>
</div>

<!-- Payment Factuur Field -->
<div class="form-group">
    {!! Form::label('payment_factuur', 'Payment Factuur:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->payment_factuur !!}
    </div>
</div>

<!-- Close Time Field -->
<div class="form-group">
    {!! Form::label('close_time', 'Close Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->close_time !!}
    </div>
</div>

<!-- Receive Time Field -->
<div class="form-group">
    {!! Form::label('receive_time', 'Receive Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->receive_time !!}
    </div>
</div>

<!-- Type Field -->
<div class="form-group">
    {!! Form::label('type', 'Type:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->type !!}
    </div>
</div>

<!-- Contact Email Field -->
<div class="form-group">
    {!! Form::label('contact_email', 'Contact Email:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->contact_email !!}
    </div>
</div>

<!-- Contact Name Field -->
<div class="form-group">
    {!! Form::label('contact_name', 'Contact Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->contact_name !!}
    </div>
</div>

<!-- Contact Surname Field -->
<div class="form-group">
    {!! Form::label('contact_surname', 'Contact Surname:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->contact_surname !!}
    </div>
</div>

<!-- Contact Gsm Field -->
<div class="form-group">
    {!! Form::label('contact_gsm', 'Contact Gsm:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! $group->contact_gsm !!}
    </div>
</div>

