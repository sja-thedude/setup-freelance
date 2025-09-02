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

<!-- Company Name Field -->
<div class="form-group">
    {!! Form::label('company_name', 'Company Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('company_name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Company Street Field -->
<div class="form-group">
    {!! Form::label('company_street', 'Company Street:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('company_street', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Company Number Field -->
<div class="form-group">
    {!! Form::label('company_number', 'Company Number:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('company_number', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Company Vat Number Field -->
<div class="form-group">
    {!! Form::label('company_vat_number', 'Company Vat Number:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('company_vat_number', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Company City Field -->
<div class="form-group">
    {!! Form::label('company_city', 'Company City:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('company_city', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Company Postcode Field -->
<div class="form-group">
    {!! Form::label('company_postcode', 'Company Postcode:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('company_postcode', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Payment Mollie Field -->
<div class="form-group">
    {!! Form::label('payment_mollie', 'Payment Mollie:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('payment_mollie', 0) !!}
            {!! Form::checkbox('payment_mollie', 1, null) !!}
        </label>
    </div>
</div>

<!-- Payment Payconiq Field -->
<div class="form-group">
    {!! Form::label('payment_payconiq', 'Payment Payconiq:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('payment_payconiq', 0) !!}
            {!! Form::checkbox('payment_payconiq', 1, null) !!}
        </label>
    </div>
</div>

<!-- Payment Cash Field -->
<div class="form-group">
    {!! Form::label('payment_cash', 'Payment Cash:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('payment_cash', 0) !!}
            {!! Form::checkbox('payment_cash', 1, null) !!}
        </label>
    </div>
</div>

<!-- Payment Factuur Field -->
<div class="form-group">
    {!! Form::label('payment_factuur', 'Payment Factuur:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('payment_factuur', 0) !!}
            {!! Form::checkbox('payment_factuur', 1, null) !!}
        </label>
    </div>
</div>

<!-- Close Time Field -->
<div class="form-group">
    {!! Form::label('close_time', 'Close Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('close_time', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Receive Time Field -->
<div class="form-group">
    {!! Form::label('receive_time', 'Receive Time:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('receive_time', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Type Field -->
<div class="form-group">
    {!! Form::label('type', 'Type:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label class="checkbox-inline">
            {!! Form::hidden('type', 0) !!}
            {!! Form::checkbox('type', 1, null) !!}
        </label>
    </div>
</div>

<!-- Contact Email Field -->
<div class="form-group">
    {!! Form::label('contact_email', 'Contact Email:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('contact_email', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Contact Name Field -->
<div class="form-group">
    {!! Form::label('contact_name', 'Contact Name:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('contact_name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Contact Surname Field -->
<div class="form-group">
    {!! Form::label('contact_surname', 'Contact Surname:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('contact_surname', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Contact Gsm Field -->
<div class="form-group">
    {!! Form::label('contact_gsm', 'Contact Gsm:', ['class' => 'control-label col-xs-12 col-sm-2 col-md-2 col-lg-2']) !!}
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {!! Form::text('contact_gsm', null, ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Submit Field -->
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.groups.index') !!}" class="btn btn-default">Cancel</a>
</div>