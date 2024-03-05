@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-bordered">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Users List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="users-table">
                            <thead class="thead-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Action</th>
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
    <script>
        var storagePath = '{{ \Storage::disk('gcs')->url('') }}'
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            stateSaveCallback: function (settings, data) {
                localStorage.setItem(
                    'DataTables_' + settings.sInstance,
                    JSON.stringify(data)
                );
            },
            stateLoadCallback: function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance));
            },
            ajax: '{{ route('admin.user.data') }}',
            columns: [
                {
                    data: 'name',
                    sDefaultContent: "",
                    searching: true,
                    render: function(data, type, row) {
                        if (row.thumb) {
                            return '<div class="user-card" >' +
                                        '<div class="user-avatar">' +
                                        '    <img style="width: 40px; height: 40px"  src="'+storagePath + row.thumb +'" />' +
                                        '</div>' +
                                        '<div class="user-name">' +
                                            '<span class="tb-lead">'+row.name+'</span>' +
                                        '</div>' +
                                    '</div>'
                        } else {
                            if(row.name) {
                                return '<div class="user-card" >' +
                                    '<div class="user-avatar bg-primary">' +
                                    '    <span>'+ row.name.toUpperCase().substring(0,2) +'</span>' +
                                    '</div>' +
                                    '<div class="user-name">' +
                                    '<span class="tb-lead">'+ row.name +'</span>' +
                                        '</div>' +
                                        '</div>';
                            }
                            else {
                                return '<div class="user-card" >' +
                                    '<div class="user-avatar bg-primary">' +
                                    '    <span>'+ row.email.toUpperCase().substring(0,2) +'</span>' +
                                    '</div>' +
                                    '<div class="user-name">' +
                                    '<span class="tb-lead">'+ row.email +'</span>' +
                                    '</div>' +
                                    '</div>';
                            }
                        }
                    }
                },
                {data: 'email', name: 'email', sDefaultContent: "",},
                {data: 'username', name: 'username', searchable: false, bSortable: false},
                {
                    data: 'id',
                    ordering: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<a href="/users/'+data+'" class="btn btn-sm btn-outline-primary">View</a>'
                    }
                }
            ]
        });
    </script>
@endsection


