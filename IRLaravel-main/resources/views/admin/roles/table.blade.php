<div class="list-responsive">
    <div class="list-header">
        <div class="row">
            <div class="col-item col-sm-5 col-xs-12">
                @lang('strings.role.label_name')
            </div>
            <div class="col-item col-sm-5 col-xs-12">
                @lang('strings.role.label_description')
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                @lang('common.options')
            </div>
        </div>
    </div>
    <div class="list-body">
        @foreach($roles as $role)
            <div id="tr-{{ $role->id }}" class="row">
                <div class="col-item col-sm-5 col-xs-12">
                    {!! $role->name !!}
                </div>
                <div class="col-item col-sm-5 col-xs-12">
                    {!! nl2br($role->description) !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    @if(Helper::checkUserPermission($guard.'.roles.edit'))
                        <a href="{!! route($guard.'.roles.edit', [$role->id, 'platform' => $platform]) !!}" class='btn btn-default btn-xs'>
                            <i class="glyphicon glyphicon-pencil"></i>
                        </a>
                    @endif

                    @if (!in_array($role->id, $role->getHiddenRoles()))
                        @if(Helper::checkUserPermission($guard.'.roles.destroy'))
                            {!! Form::open(['route' => [$guard.'.roles.destroy', $role->id], 'method' => 'delete', 'class' => 'inline-block']) !!}
                            {!! Form::button('<i class="glyphicon glyphicon-trash"></i>',
                                [
                                'type' => 'button',
                                'class' => 'btn btn-danger btn-xs show-confirm',
                                'data-title' => trans('role.are_you_sure_delete'),
                                'data-yes_label' => trans('common.yes'),
                                'data-no_label' => trans('common.no')
                            ]) !!}
                            {!! Form::close() !!}
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($roles))
    {{ $roles->links() }}
@endif