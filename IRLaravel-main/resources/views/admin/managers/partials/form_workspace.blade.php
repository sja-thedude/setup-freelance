@php($workspaceId = config('workspace.active'))
@php($auth = Auth::guard($guard)->user())

<!-- Name Field -->
<div class="form-group">
    @if($auth->isSuperAdmin())
        <div class="x_panel">
            <div class="x_content">
                <table class="table no-border-cell">
                    <thead>
                    <tr>
                        <th class="tb-col-40">@lang('strings.user.label_workspace')</th>
                        <th class="tb-col-40">@lang('strings.user.label_role')</th>
                        <th class="tb-col-20"></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="row-container">
                            <td class="tb-col-40">
                                {!! Form::select('tmp_workspace_id', $workspaces, $workspaceId,
                                    ['class' => 'form-control select2', 'placeholder' => trans('strings.user.placeholder_workspace'),
                                        'data-default-workspace' => $workspaceId, 'style' => 'width: 100%;']) !!}
                            </td>
                            <td class="tb-col-40">
                                {!! Form::select('tmp_role_id', $roles, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                            </td>
                            <td class="tb-col-20">
                                <button class="btn btn-default btn-assign-workspace">@lang('strings.user.button_assign')</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="x_panel no-border">
        <div class="x_content">
            <table class="table table-hover" id="workspace_table" data-start-index="{{ (!empty($workspaceUsers)) ? $workspaceUsers->count() : 0 }}">
                <thead>
                    <tr>
                        <th class="tb-col-40">@lang('strings.user.label_workspace')</th>
                        <th class="tb-col-40">@lang('strings.user.label_role')</th>
                        <th class="tb-col-20"></th>
                    </tr>
                </thead>
                <tbody>
                @if($auth->isSuperAdmin())
                    <tr class="row-template" style="display: none;">
                        <td class="tb-col-40">
                            {!! Form::select('template_workspace_id', $workspaces, null,
                                ['data-field' => 'workspace_id', 'class' => 'form-control convert-select2', 'style' => 'width: 100%;']) !!}
                        </td>
                        <td class="tb-col-40">
                            {!! Form::select('template_role_id', $roles, null,
                                ['data-field' => 'role_id', 'class' => 'form-control convert-select2', 'style' => 'width: 100%;']) !!}
                        </td>
                        <td class="tb-col-20">
                            <button class="btn btn-danger btn-delete-workspace">@lang('strings.delete')</button>
                        </td>
                    </tr>

                    {{-- Load exist workspace users --}}
                    @if(!empty($workspaceUsers) && $workspaceUsers->count() > 0)
                        @php($indexAssign = 0)

                        @foreach($workspaceUsers as $item)
                            @php($roleId = (!empty($item['meta_data']) && !empty($item['meta_data']['role_id'])) ? $item['meta_data']['role_id'] : null)
                            <tr class="row-item" data-row-item="{{ $indexAssign }}">
                                <td class="tb-col-40">
                                    {!! Form::select('workspaces[' . $indexAssign . '][workspace_id]', $workspaces, $item->workspace_id,
                                        ['class' => 'form-control select2', 'readonly' => 'readonly', 'data-field' => 'workspace_id', 'style' => 'width: 100%;']) !!}
                                </td>
                                <td class="tb-col-40">
                                    {!! Form::select('workspaces[' . $indexAssign . '][role_id]', $roles, $roleId,
                                        ['class' => 'form-control select2', 'data-field' => 'role_id', 'style' => 'width: 100%;']) !!}
                                </td>
                                <td class="tb-col-20">
                                    <button class="btn btn-danger btn-delete-workspace">@lang('strings.delete')</button>
                                </td>
                            </tr>

                            @php($indexAssign++)
                        @endforeach
                    @endif
                @else
                    <tr class="row-item">
                        <td class="tb-col-40">
                            {!! Form::hidden('workspaces[0][workspace_id]', $workspaceId) !!}
                            {!! Form::text('workspaces[0][workspace_name]', $workspaces[$workspaceId],
                                ['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'background: transparent; border: none; box-shadow: none;']) !!}
                        </td>
                        <td class="tb-col-40">
                            {!! Form::select('workspaces[0][role_id]', $roles, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                        </td>
                        <td class="tb-col-20"></td>
                    </tr>
                @endif

                </tbody>
            </table>

        </div>
    </div>
</div>

@if($auth->isSuperAdmin())
    @push('scripts')
        <script>
            $(document).ready(function () {
                var workspaceTable = $('#workspace_table');
                var workspaceBody = workspaceTable.find('tbody');
                var rowTemplate = workspaceBody.find('.row-template');
                var btnAssign = $('.btn-assign-workspace');
                var workspaceIndex = parseInt(workspaceTable.data('start-index'));
                var tmpWorkspace = $('select[name="tmp_workspace_id"]');
                var tmpRole = $('select[name="tmp_role_id"]');
                var disableSelectedWorkspace = function () {
                    var tblWorkspace = $('#workspace_table');
                    var defaultWorkspace = parseInt(tmpWorkspace.data('default-workspace'));
                    var selectedWorkspaces = [];

                    // Enable all options before check and disable
                    tmpWorkspace.find('option').prop('disabled', false);
                    // Get all workspaces which are assigned
                    tblWorkspace.find('.row-item select[data-field="workspace_id"]').each(function () {
                        var selectedWorkspace = parseInt($(this).val());
                        selectedWorkspaces.push(selectedWorkspace);
                        // Disable workspace option in Assignment
                        tmpWorkspace.find('option[value="' + selectedWorkspace + '"]').prop('disabled', true)
                    });

                    // Update workspace assignment
                    if (selectedWorkspaces.indexOf(defaultWorkspace) >= 0) {
                        tmpWorkspace.val(null);
                    }

                    // Reload workspace assignment
                    tmpWorkspace.select("destroy").select2();
                };

                // Initial when edit user (with selected workspaces)
                disableSelectedWorkspace();
                // For exist
                workspaceTable.find('.row-item').each(function () {
                    var row = $(this);
                    var workspaceItem = row.find('select[data-field="workspace_id"]');
                    var roleItem = row.find('select[data-field="role_id"]');
                });

                /**
                 * Delete workspace function
                 * @param e
                 */
                var onDeleteWorkspace = function (e) {
                    // Prevent submit form
                    e.preventDefault();
                    var rowItem = $(this).closest('.row-item');
                    // Remove row
                    rowItem.remove();
                    // Reload workspace list
                    disableSelectedWorkspace();
                };

                // Add delete event for exist workspace rows
                workspaceBody.find('.btn-delete-workspace').on('click', onDeleteWorkspace);
                // Assign workspace to role action
                btnAssign.on('click', function (e) {
                    // Prevent submit form
                    e.preventDefault();
                    // Flag which allow to add new workspace/role row or not
                    var flag = true;
                    var container = $(this).closest('.row-container');
                    // Create new row by clone row template
                    var newRow = rowTemplate.clone();
                    // Remove template mark
                    newRow.removeClass('row-template')
                        .addClass('row-item')
                        .attr('data-row-item', workspaceIndex);
                    var workspaceItem = null;
                    var roleItem = null;
                    // Convert select to select2
                    // newRow.find('select.convert-select2').addClass('select2').select2();
                    newRow.find('select.convert-select2').each(function () {
                        var select = $(this);
                        var fieldName = select.data('field');
                        // Get value from assignment inputs
                        var assignmentVal = container.find('select[name="tmp_' + fieldName + '"]').val();

                        if (!assignmentVal) {
                            flag = false;
                            swal({
                                title: '{!! trans('common.please_select_workspace') !!}',
                                type: "warning",
                                confirmButtonText: '{!! trans('common.ok') !!}',
                            });

                            return;
                        }

                        if (fieldName == 'workspace_id') {
                            // Mark readonly if is workspace field
                            select.attr('readonly', 'readonly');
                            workspaceItem = select;
                        } else if (fieldName == 'role_id') {
                            roleItem = select;
                        }

                        // Change field name
                        select.attr('name', 'workspaces[' + workspaceIndex + '][' + fieldName + ']');
                        // Change selected value
                        select.val(assignmentVal);
                        // Convert to select2
                        select.addClass('select2').select2();
                    });

                    if (!flag) {
                        // Prevent insert new row in to workspace/role
                        return;
                    }
                    // Attach remove row event
                    newRow.find('.btn-delete-workspace').on('click', onDeleteWorkspace);
                    // Change display status of row template
                    newRow.show();
                    // Append new row to table body
                    workspaceBody.append(newRow);
                    // Refresh workspace select option
                    disableSelectedWorkspace();
                    // Increment workspace index
                    workspaceIndex++;
                });

                // When submit form
                var userForm = $('form[name="userForm"]');
                userForm.on('submit', function (e) {
                    if (workspaceTable.find('.row-item').length === 0) {
                        swal({
                            title: '{!! trans('common.select_workspace_invalid') !!}',
                            type: "warning",
                            confirmButtonText: '{!! trans('common.ok') !!}',
                        });

                        e.preventDefault();
                        return false;
                    }

                    return true;
                })
            });
        </script>
    @endpush
@endif