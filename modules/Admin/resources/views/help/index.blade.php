@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Helps List</span>
            <div class="table-responsive">
                <table style="width: 100%" class="table kz-table table-tranx is-compact" id="hangout-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Help</th>
                            <th>Help Email</th>
                            <th width="100">Help Type</th>
                            <th>Addition Info</th>
                            <th>Capacity</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th>Updated At</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('#hangout-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.help.data') }}',
            columns: [
                {data: "title", name: "title"},
                {
                    data: "description",
                    render: function(data, type, row) {
                        return data.substring(0, 200)
                    }
                },
                {
                    data: "user.name",
                    render: function(data, type, row) {
                        return '<a href="/users/'+row.user.id+'" >'+data+'</a>'
                    }
                },
                {
                    data: "user.email",
                    name: "user.email",
                    visible: false
                },
                {
                    data: "type",
                    render: function(data, type, row) {
                        if (data == "1") {
                            return '<span class="badge badge-outline-primary">Single</span>'
                        } else {
                            return '<span class="badge badge-outline-secondary">Anytime</span>'
                        }
                    }
                },
                {
                    data: "location.address",
                    bSortable: false,
                    render: function(data, type, row) {
                       if (row.type === "1") {
                           return '<ul>' +
                                       '<li><b>Start time: </b> '+row.start+'</li>' +
                                       '<li><b>End time: </b> '+row.end+'</li>' +
                                   '</ul>'
                       }
                        return '<b>Time: </b> ' + row.schedule
                    }
                },
                {
                    data: "capacity",
                    name: "capacity"
                },
                {
                    data: "available",
                    name: "available"
                },
                {
                    data: "status",
                    name: "status"
                },
                {
                    data: "updated_at",
                    name: "updated_at"
                },
                {
                   data: 'id',
                   searchable: false,
                   bSortable: false,
                   render: function (data, type, row) {
                        return '<div class="drodown">\n' +
                            '     <a href="#" class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>\n' +
                            '             <div class="dropdown-menu dropdown-menu-right" style="">\n' +
                            '                    <ul class="link-list-opt no-bdr">\n' +
                            '                        <li><a href="/helps/' +row.id+ '"><em class="icon ni ni-eye view-detail-hangout"></em><span>View Details</span></a></li>\n' +
                            '                        <li><a href="/helps/' +row.id+ '/offers"><em class="icon ni ni-repeat"></em><span>Offers</span></a></li>\n' +
                            '                        <li class="divider"></li>\n' +
                            '                        <li><a href="/helps/' +row.id+ '/delete"><em class="icon ni ni-trash-alt"></em><span>Delete</span></a></li>\n' +
                            '                    </ul>\n' +
                            '              </div>\n' +
                            '    </div>'
                   }
                }
            ]
        });
    </script>
@endsection


