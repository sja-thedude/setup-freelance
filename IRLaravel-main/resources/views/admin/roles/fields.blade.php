<div class="col-sm-12 col-xs-12">
    @php
        $currentPlatform = \App\Models\Role::PLATFORM_BACKOFFICE;
        $permissionsList = config("permission");

        if(!empty($platform)) {
            $currentPlatform = $platform;
            $permissionsList = config("permission_manager");
        }

        if(!empty($role->platform)) {
            $currentPlatform = $role->platform;
            $permissionsList = config("permission_manager");
        }

        $assignAll = array();

        if(!empty(old('permission'))) {
            $permissionAsigned = old('permission');
        } else {
            $permissionAsigned = (!empty($role) && $role->permission !== null) ? $role->permission : [];
        }
    @endphp

    <input type="hidden" name="platform" value="{!! $currentPlatform  !!}"/>

    <!-- Name Field -->
    <div class="form-group">
        {!! Html::decode(Form::label('name', trans('strings.role.label_name') . '<span class="required">*</span>')) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Description Field -->
    <div class="form-group mgt-20">
        {!! Form::label('description', trans('strings.role.label_description')) !!}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'style' => 'resize: vertical;']) !!}
    </div>

    <!-- Permission Field -->
    <div class="form-group mgt-20">
    {!! Form::label('description', trans('strings.role.label_permission')) !!}
    <!-- Permission List -->
        <div class="row">
            <div class="col-md-12">
                <div id="permission-area">
                    <div class="row">
                        <ul class="treeview">
                            @if(!empty($permissionsList))
                                <li class="col-sm-12 col-xs-12">
                                    <input class="treeview-lv1" data-role="treeview-cb" type="checkbox" id="tree">
                                    <label for="tree" class="custom-unchecked">@lang('strings.role.label_full_permissions')</label>

                                    <ul class="row">
                                        @php
                                            $totalLevel2 = count($permissionsList);
                                            $count = 1;
                                        @endphp

                                        @foreach($permissionsList as $key => $pms)
                                            <li class="col-sm-12 col-xs-12 {!! ($count == $totalLevel2) ? 'last' : '' !!}">
                                                <input class="treeview-lv2" data-role="treeview-cb" type="checkbox" id="tree-{!! $count !!}">
                                                <label for="tree-{!! $count !!}" class="custom-unchecked">{!! $pms['name'] !!}</label>
                                                <ul class="row">
                                                    @if(!empty($pms['actions']))
                                                        @php
                                                            $totalLevel3 = count($pms['actions']);
                                                            $subCount = 1;
                                                        @endphp

                                                        @foreach($pms['actions'] as $subKey => $elem)
                                                            @php
                                                                $fieldData = explode('@', $elem['action']);
                                                                $parentKey = Helper::recursive_array_search($elem['action'], $permissionAsigned);

                                                                if($parentKey !== false && array_key_exists($parentKey, $permissionAsigned)) {
                                                                    $cls = 'checked';
                                                                    $checkAttr = 'checked="checked"';
                                                                } else {
                                                                    $cls = 'unchecked';
                                                                    $checkAttr = '';
                                                                }
                                                            @endphp

                                                            <li class="col-sm-6 col-xs-12 {!! ($subCount == $totalLevel3) ? 'last' : '' !!}">
                                                                <input class="treeview-lv3"
                                                                       data-role="treeview-cb"
                                                                       type="checkbox"
                                                                       name="permission[{!! $fieldData[0] !!}][]"
                                                                       id="tree-{!! $count !!}-{!! $subCount !!}"
                                                                       value="{!! $elem['action'] !!}"
                                                                        {!! $checkAttr !!}
                                                                />
                                                                <label for="tree-{!! $count !!}-{!! $subCount !!}" class="custom-{!! $cls !!}">{!! $elem['name'] !!}</label>
                                                            </li>
                                                            @php $subCount++; @endphp
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </li>

                                            @php $count++; @endphp
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Field -->
    <div class="row form-group">
        <div class="col-sm-12 col-xs-12 text-center">
            <hr/>
            <a href="{!! route($guard.'.roles.index', ['platform' => $currentPlatform]) !!}" type="button" class="btn btn-default clear-border-radius">
                @lang('strings.cancel')
            </a>
            {!! Form::submit(trans('strings.save'), ['class' => 'btn btn-primary clear-border-radius']) !!}
        </div>
    </div>
</div>

