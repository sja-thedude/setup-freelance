<script type="text/javascript">
    $(document).ready(function () {
        $("a[data-role='uninstall-theme']").on("click", function () {
            var themeId = $(this).data('theme_id');
            var urlDelete = $(this).data('url');
            swal({
                title: "Are you sure?",
                text: "Uninstall this theme.",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                confirmButtonText: "Yes",
                confirmButtonClass: "btn-danger",
                cancelButtonText: "No"
            }, function () {
                $.ajax({
                    type: 'DELETE',
                    url: urlDelete,
                    data: {"_token": "{{ csrf_token() }}"}
                })
                        .done(function () {
                             location.reload();
                            /*swal("Uninstall!", "Uninstall theme " + themeName + " success", "success");
                            $("#item-" + themeId).remove();*/
                        });
            });
            return false;
        });
    });

</script>