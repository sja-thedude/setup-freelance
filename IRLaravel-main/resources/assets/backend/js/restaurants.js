function Restaurant() {
}

Restaurant.fn = {
    init: function () {
        Restaurant.fn.showConfirmDeletePopupSubmit.call(this);
        Restaurant.fn.loadAssignDelete.call(this);
        Restaurant.fn.submitAssignDelete.call(this);
        Restaurant.fn.checkShowModal.call(this);
        Restaurant.fn.changeWorkspaceExtraTableOrdering.call(this);
    },
    
    submitAssignDelete: function() {
        $(document).on('click', '.assign-account-manager', function () {
            var _this = $(this);
            var disabled = _this.attr('disabled');
            
            if(typeof disabled == 'undefined') {
                var ids = [];
                $(".get-status-id:checkbox[name=checkbox]:checked").each(function () {
                    ids.push($(this).val());
                });
                
                var newIds = ids.length > 0 ? ids : 0;
                
                var route = _this.data('route')+"/"+ newIds;
                var _token = $('meta[name="csrf-token"]').val();
                var managerId = _this.closest('.modal').find('.another-account-manager').val();
                var modal = _this.closest('.modal');
                var data = {token: _token, manager_id: managerId};
                
                $('body').loading('toggle');
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            modal.modal('hide');
                            $("#tr-" + _this.data('id')).hide();
                            Swal.fire({
                                title: '<span class="ir-popup-title">' + Lang.get('workspace.assign_confirm') + '</span>',
                                html: '<span class="ir-popup-content">' + ids.length +" "+ response.message + '</span>',
                                width: 512,
                                padding: '43px 60px 30px 60px',
                                showConfirmButton: false,
                                showCloseButton: true,
                                showCancelButton: true,
                                cancelButtonText: Lang.get('workspace.close')
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '<span class="ir-popup-title">' + Lang.get('workspace.oops') + '</span>',
                                html: '<span class="ir-popup-content">' + response.message + '</span>',
                                width: 512,
                                padding: '43px 60px 30px 60px',
                                showConfirmButton: false,
                                showCloseButton: true,
                                showCancelButton: true,
                                cancelButtonText: Lang.get('workspace.close')
                            }).then((result) => {
                                location.reload();
                            });
                        }

                        $('body').loading('toggle');
                    }
                });
            }
        });
    },
    
    checkButtonAssignAccount: function(_this) {
        var select = _this.val();
        
        var assignAccount = _this.closest('.modal').find('.assign-account-manager');

        if(select != '') {
            assignAccount.removeAttr('disabled');
        } else {
            assignAccount.attr('disabled', 'disabled');
        }
    },
    
    loadAssignDelete: function() {
        $('.another-account-manager').map(function(){
            var _this = $(this);
            Restaurant.fn.checkButtonAssignAccount(_this);
        });  
    },
    
    showConfirmDeletePopupSubmit: function () {
        $(document).on('change', '.another-account-manager', function () {
            var _this = $(this);
            Restaurant.fn.checkButtonAssignAccount(_this);
        });
    },
    
    checkShowModal: function(){
        $(document).on('click', '#assign-manager', function () {
            var ids = [];
            $(".get-status-id:checkbox[name=checkbox]:checked").each(function () {
                ids.push($(this).val());
            });
            
            if(ids.length > 0) {
                $('#modal_assign_manager').modal('show');
            } else {
                Swal.fire({
                    title: '<span class="ir-popup-title">' + Lang.get('workspace.oops') + '</span>',
                    html: '<span class="ir-popup-content">' + Lang.get('workspace.not_found_assign') + '</span>',
                    width: 512,
                    padding: '43px 60px 30px 60px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: Lang.get('workspace.close')
                });
            }
        });
    },

    /**
     * Change workspace extra table ordering.
     * Add option to enable or disable the Table Ordering
     * and if that’s enabled we should be able to select an option to allow "Self-service".
     *
     * Ticket https://vitex1.atlassian.net/browse/ITR-1135
     */
    changeWorkspaceExtraTableOrdering: function () {
        $('[data-type="table_ordering"]').on('change', 'input.switch-input', function () {
            let input = $(this);
            let checked = input.is(':checked');

            let parentContainer = input.closest('[data-type="table_ordering"]');
            let selfServiceContainer = parentContainer.next('[data-type="self_service"]');

            if (checked) {
                selfServiceContainer.show();
            } else {
                selfServiceContainer.hide();
                // Disable self_service when switch table_ordering is off
                if (selfServiceContainer.find('input.switch-input').is(':checked')) {
                    selfServiceContainer.find('.update-status').click();
                }
            }
        });
    },

    rule: function () {
        $(document).ready(function () {
            Restaurant.fn.init.call(this);
        });
    },
};

Restaurant.fn.rule();