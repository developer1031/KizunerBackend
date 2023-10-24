@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-bordered">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">
                        Admin List
                        <button id="admin-add" class="float-right btn btn-primary">Add new Admin</button>
                    </span>
                    <div class="clearfix"></div>
                    <div class="table-responsive" style="margin-top: 10px">
                        <table style="width: 100%" class="table kz-table table-tranx is-compact" id="admin-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="50">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <div class="modal fade" tabindex="-1" id="adminModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin information</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden"  name="id" id="adminid" class="form-control"/>
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <div class="form-control-wrap">
                                <input required  name="name" id="adminanme" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <div class="form-control-wrap">
                                <input required type="email"  name="email" id="adminemail" class="form-control"/>
                            </div>
                        </div>
                        <hr>
                        <div id="accordion" class="accordion">
                            <div class="accordion-item">
                                <a href="#" class="accordion-head" data-toggle="collapse" data-target="#change-password">
                                    <h6 class="title">Change Password</h6>
                                    <span class="accordion-icon"></span>
                                </a>
                                <div class="accordion-body" id="change-password" data-parent="#accordion" >
                                    <div class="accordion-inner">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Password</label>
                                            <div class="form-control-wrap">
                                                <input type="password"  name="password" id="adminPassword" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="form-label">Password</label>
                                            <div class="form-control-wrap">
                                                <input type="password"  name="password_confirm" id="adminPasswordConfirm" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button style="margin-top: 10px" type="submit" class="btn btn-primary float-right">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#admin-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.admin.data') }}',
            columns: [
                {data: "name", name: "name"},
                {data: "email", name: "email"},
                {
                    data: "id",
                    render: function(data, row, type) {
                        return '<div class="btn-group ">\n' +
                            '  <button data-id="'+data+'"  class="btn btn-outline-gray btn-edit"><em class="ni ni-pen"></em></button>\n' +
                            '  <a href="admin/'+data+'/delete"  class="btn btn-outline-danger btn-delete"><em class="ni ni-trash"></em></a>\n' +
                            '</div>'
                    }
                }
            ]
        });

        $('#admin-table').on('click', '.btn-edit', function(e) {
            var id = $(this).data('id')
            $.ajax({
                url: '/admin/' + id,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    $('#adminModal #adminid').val(data.id)
                    $('#adminModal #adminanme').val(data.name)
                    $('#adminModal #adminemail').val(data.email)
                    $('#adminModal form').attr('action', "{{ route('admin.admin.update') }}?type=edit")
                    $('#adminModal').modal('show')
                }
            });
        })

        $('#admin-add').on('click', function(e) {
            $('#adminModal #adminid').val("")
            $('#adminModal #adminanme').val("")
            $('#adminModal #adminemail').val("")
            $('#adminModal form').attr('action', "{{ route('admin.admin.update') }}?type=add")
            $('#adminModal #adminPassword').attr('required', true)
            $('#adminModal #adminPasswordConfirm').attr('required', true)

            $('#adminModal').modal('show')
        })
    </script>
@endsection


