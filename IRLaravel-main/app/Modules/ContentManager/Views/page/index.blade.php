@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('post.manage_page')
                    </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a id="btn-sel-del" style="display:none; background: #f00;" href="#"
                               class="ir-btn ir-btn-primary danger">
                                <i class="fa fa-trash"></i> @lang('post.btn_delete_selected_page')</a>
                        </li>
                        <li class="mgl-10">
                            <a href="{{ Admin::route('contentManager.page.create') }}" class="ir-btn ir-btn-primary">
                                <i class="fa fa-plus"></i> @lang('post.btn_create_page')</a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="ir-content">
                    <div class="list-responsive bulk_action">
                        <div class="list-header">
                            <div class="row">
                                <div class="col-item col-sm-6 col-xs-12">
                                    <input id="checkAll" type="checkbox" class="flat">
                                    @lang('post.title_post_title')
                                </div>
                                <div class="col-item col-sm-3 col-xs-12">
                                    @lang('post.fields.author')
                                </div>
                                <div class="col-item col-sm-1-5 col-xs-12">
                                    @lang('post.fields.date')
                                </div>
                                <div class="col-item col-sm-1-5 col-xs-12"></div>
                            </div>
                        </div>
                        <div class="list-body restaurant">
                            @foreach($model as $data)
                                <div id="tr-{{ $data->id }}" class="row">
                                    <div class="col-item col-sm-6 col-xs-12">
                                        <input type="checkbox" class="flat" name="checkbox" data-role="checkbox" value="{{$data->id}}"/>
                                        <input type="hidden" id="idPost" value="{{ $data->id }}">
                                        {{$data->post_title}}
                                    </div>
                                    <div class="col-item col-sm-3 col-xs-12">
                                        {{$data->user->name}}
                                    </div>
                                    <div class="col-item col-sm-1-5 col-xs-12">
                                        {{$data->updated_at}}
                                    </div>
                                    <div class="col-item col-sm-1-5 col-xs-12 text-right">
                                        <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown"
                                           aria-expanded="false">
                                            @lang('workspace.actions')
                                            <i class=" fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right ir-dropdown-actions">
                                            <li>
                                                <a href="{!! route($guard.'.contentManager.page.edit', ['page' => $data->id]) !!}">
                                                    @lang('strings.edit')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" data-role="delete-post" data-idpost="{{ $data->id }}">
                                                    @lang('workspace.remove')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if(!empty($model))
                        {{ $model->appends(request()->all())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $("a[data-role='delete-post']").on("click", function () {
                var idpost = $(this).data('idpost');

                Swal.fire({
                    width: 512,
                    padding: '43px 60px 30px 60px',
                    title: 'Are you sure?',
                    text: "Delete this page",
                    showDenyButton: false,
                    showCancelButton: true,
                    focusConfirm: false,
                    focusDeny: false,
                    cancelButtonText: "No",
                    // denyButtonText: yesLabel,
                    confirmButtonText: 'Yes',
                    showCloseButton: true,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $('body').loading('toggle');

                        $.ajax({
                            type: 'DELETE',
                            url: "{{ Admin::route('contentManager.page.index') }}/" + idpost,
                            data: {"_token": "{{ csrf_token() }}"}
                        })
                            .done(function () {
                                Swal.fire("Deleted!", "Delete Success", "success");
                                $("#tr-" + idpost).remove();
                                $('body').loading('toggle');
                            });
                    }
                });
                return false;
            });

            $("#checkAll").change(function () {
                $("input:checkbox[name=checkbox]").prop('checked', $(this).prop("checked"));
                if ($("#btn-sel-del").css('display') == 'none') {
                    $("#btn-sel-del").css("display", "inline-block");
                } else {
                    $("#btn-sel-del").css("display", "none");
                }
            });

            $("input[type=checkbox]").on("change", function () {
                var n = $("input:checked[name=checkbox]").length;
                if (n == 0) {
                    $("#btn-sel-del").css("display", "none");
                } else {
                    $("#btn-sel-del").css("display", "inline-block");
                }
            });

            $("#btn-sel-del").on("click", function () {
                var array = new Array();
                $("input:checkbox[name=checkbox]:checked").each(function () {
                    array.push($(this).val());
                });
                var id = array.join()

                Swal.fire({
                    width: 512,
                    padding: '43px 60px 30px 60px',
                    title: 'Are you sure?',
                    text: "Delete the selected page",
                    showDenyButton: false,
                    showCancelButton: true,
                    focusConfirm: false,
                    focusDeny: false,
                    cancelButtonText: "No",
                    // denyButtonText: yesLabel,
                    confirmButtonText: 'Yes',
                    showCloseButton: true,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $('body').loading('toggle');

                        $.ajax({
                            type: 'DELETE',
                            url: "{{ Admin::route('contentManager.page.index') }}/" + id,
                            data: {"_token": "{{ csrf_token() }}"}
                        })
                            .done(function () {
                                Swal.fire("Deleted!", "Delete Success", "success");
                                location.reload();
                            });
                    }
                });
                return false;
            });
        });
    </script>
@endpush

<style>
    .icheckbox_flat-green {
        top: 0 !important;
    }
</style>