@if(!empty($workspaceUsers) && $workspaceUsers->count() > 0)
    <hr/>
    <!-- Name Field -->
    <div class="form-group">
        {!! Html::decode(Form::label(null, trans('strings.workspace.plural_name'))) !!}

        <div class="x_panel no-border mgb-i-0">
            <div class="x_content">
                <table class="table table-hover mgb-i-0" id="workspace_table" data-start-index="{{ (!empty($workspaceUsers)) ? $workspaceUsers->count() : 0 }}">
                    {{-- Load exist workspace users --}}
                    <thead>
                        <tr>
                            <th class="tb-col-40">@lang('strings.user.label_workspace')</th>
                            <th class="tb-col-40">@lang('strings.user.label_role')</th>
                            <th class="tb-col-20"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($indexAssign = 0)

                        @foreach($workspaceUsers as $item)
                            @php($roleId = (!empty($item['meta_data']) && !empty($item['meta_data']['role_id'])) ? $item['meta_data']['role_id'] : null)
                            <tr class="row-item" data-row-item="{{ $indexAssign }}">
                                <td class="tb-col-40">
                                    {!! Form::text('workspaces[' . $indexAssign . '][workspace_id]', $workspaces[$item->workspace_id],
                                        ['class' => 'form-control', 'disabled' => 'disabled', 'style' => 'background: transparent; border: none; box-shadow: none;']) !!}
                                </td>
                                <td class="tb-col-40">
                                    {!! Form::text('workspaces[' . $indexAssign . '][role_id]', (isset($roles[$roleId])) ? $roles[$roleId] : '',
                                        ['class' => 'form-control', 'disabled' => 'disabled', 'style' => 'background: transparent; border: none; box-shadow: none;']) !!}
                                </td>
                                <td class="tb-col-20"></td>
                            </tr>

                            @php($indexAssign++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif