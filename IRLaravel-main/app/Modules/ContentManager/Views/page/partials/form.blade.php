<form method="POST"
      action="{{ ($model != "") ? Admin::route('contentManager.page.update',['page'=>$model->id]) : Admin::route('contentManager.page.store') }}">
    <div class="col-md-12">
        {{ csrf_field() }}
        @if($model != "")
            <input name="_method" type="hidden" value="PUT">
        @endif
        <div class="form-group">
            <label for="title-post">@lang('post.fields.post_title')</label>
            <input type="text" class="form-control" name="post_title"
                   value="{{ ($model != "" ) ? $model->post_title : old('post_title') }}" id="title-post"
                   placeholder="@lang('post.placeholders.post_title')">
            @if($model != "")
                <p class="help-block"><strong>Permalink: </strong>
                    <span id="slug-permalink" title="Click to preview">
            <a href="{{ Url('/') }}/{{App::getLocale()}}/{{ $model->post_name }}.html" target="_blank">{{ Url('/') }}/{{App::getLocale()}}/{{ $model->post_name }}.html</a>
          </span>
                </p>
            @endif
        </div>
        <div class="form-group">
            <label for="title-post">@lang('post.fields.slug')</label>
            <input type="text" class="form-control" name="post_name"
                   value="{{ ($model != "" ) ? $model->post_name : old('post_name') }}" id="name-post"
                   placeholder="@lang('post.placeholders.slug')">
        </div>
        <div class="form-group">
            <label for="content-post">@lang('post.fields.content')</label>
            <textarea id="content-post" name="post_content" class="form-control ckeditor"
                      rows="18">{{ ($model != "" ) ? $model->post_content : old('post_content') }}</textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="ir-btn ir-btn-primary">@lang('strings.save')</button>
        </div>
    </div>
</form>

