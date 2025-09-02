<!-- Photo Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Html::decode(Form::label('photo', trans('strings.banner.label_photo') . ' ' . trans('strings.banner.recommend_photo_size'))) !!}

    <div class="clearfix"></div>

    @include('admin.banners.partials.photo_chooser')
</div>

<!-- Title 1 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('title_1', 'Title 1:') !!}
    {!! Form::textarea('title_1', null, ['class' => 'form-control ckeditor']) !!}
</div>

<!-- Title 2 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('title_2', 'Title 2:') !!}
    {!! Form::textarea('title_2', null, ['class' => 'form-control ckeditor']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control ckeditor']) !!}
</div>

<!-- Button 1 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('button_1', 'Button 1:') !!}
    {!! Form::text('button_1', null, ['class' => 'form-control']) !!}
</div>

<!-- Link 1 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('link_1', 'Link 1:') !!}
    {!! Form::text('link_1', null, ['class' => 'form-control', 'type' => 'url']) !!}
</div>

<!-- Button 2 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('button_2', 'Button 2:') !!}
    {!! Form::text('button_2', null, ['class' => 'form-control']) !!}
</div>

<!-- Link 2 Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('link_2', 'Link 2:') !!}
    {!! Form::text('link_2', null, ['class' => 'form-control', 'type' => 'url']) !!}
</div>

<!-- Align Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('', trans('strings.banner.label_align')) !!}

    @foreach(\App\Models\Banner::aligns() as $value => $name)
        <label class="checkbox-inline">
            {!! Form::radio('align', $value,
                (empty($banner)) ? ($value == \App\Models\Banner::getDefaultAlign()) : ($banner->align == $value)) !!}
            <span>{!! $name !!}</span>
        </label>
    @endforeach
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(trans('strings.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.banners.index') !!}" class="btn btn-default">@lang('strings.cancel')</a>
</div>
