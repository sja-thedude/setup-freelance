<div class="row workspace_app_meta_item" data-type="{{ $appMeta->type }}" data-id="{{ $appMeta->id }}"
     data-url="{{ route('manager.apps.change_settings', ['id' => $appMeta->id]) }}">

    <input type="hidden" name="default" value="{{ ($appMeta->default) ? 1 : 0 }}">
    <input type="hidden" name="type" value="{{ $appMeta->type }}">
    <input type="hidden" name="key" value="{{ $appMeta->key }}">
    <input type="hidden" name="key" value="{{ $appMeta->key }}">
    <input type="hidden" name="order" value="{{ $appMeta->order }}">
    <input type="hidden" name="workspace_app_id" value="{{ $appMeta->workspace_app_id }}">

    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 group-function">
        <div class="row">
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                <span class="container-sort">
                    {!! Html::image('images/workspace_app/sort.svg') !!}
                </span>

                <label class="container-status">
                    {{-- Active status --}}
                    <input type="checkbox" name="active" value="{{ $appMeta->id }}" class="switch-input field-status"
                           @if($appMeta->active) checked @endif
                           data-url="{{ route('manager.apps.settings.change_status', ['id' => $appMeta->id]) }}">
                    <span class="switch"></span>
                </label>
            </div>
            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                @if($appMeta->default)
                    {{-- Show title in default type --}}
                    <span class="title">{{ $appMeta->name }}</span>
                @else
                    {{-- Title Field --}}
                    <div class="group-function-field @if(!$appMeta->active) hide-border @endif">
                        <input type="text" name="title" value="{{ $appMeta->title }}" class="form-control" maxlength="25"
                            placeholder="@lang('workspace_app.settings.placeholders.title')">
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 group-field" style="@if(!$appMeta->active) display: none; @endif">
        {{-- Description Field --}}
        <input type="text" name="description" value="{{ $appMeta->description }}" class="form-control" maxlength="91"
            placeholder="@lang('workspace_app.settings.placeholders.description')">
        @if(in_array($appMeta->type, [\App\Models\WorkspaceAppMeta::TYPE_3]))
            <br>
            {{-- Title Field with type = 3 (Jobs) --}}
            <input type="text" name="title" value="{{ $appMeta->title }}" class="form-control" maxlength="100"
                placeholder="@lang('workspace_app.settings.placeholders.title')">
        @endif
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 group-field" style="@if(!$appMeta->active) display: none; @endif">
        @if(in_array($appMeta->type, [\App\Models\WorkspaceAppMeta::TYPE_1]))
            {{-- URL Field --}}
            <input type="url" name="url" value="{{ $appMeta->url }}" placeholder="@lang('workspace_app.settings.placeholders.url')" class="form-control">
        @endif
        @if(in_array($appMeta->type, [\App\Models\WorkspaceAppMeta::TYPE_3]))
            {{-- Content Field with type = 3 (Jobs) --}}
            <textarea name="content" rows="5" placeholder="@lang('workspace_app.settings.placeholders.content')" class="form-control">{{ $appMeta->content }}</textarea>
        @endif
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        {{-- Only allow delete non-default type --}}
        {{-- Delete button --}}
        @if(!$appMeta->default)
            <a href="javascript: void(0);" class="btn-remove-item"
                data-url="{{ route('manager.apps.settings.destroy', ['id' => $appMeta->id]) }}">
                <img src="{{ asset('images/workspace_app/trash.svg') }}" alt="">
            </a>
        @endif

        {{-- Save button --}}
        <a href="javascript: void(0);" class="btn-save-item" style="display: none;">
            <img src="{{ asset('images/workspace_app/check.svg') }}" alt="">
        </a>
    </div>
</div>