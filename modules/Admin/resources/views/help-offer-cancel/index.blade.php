@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Help cancel offer List</span>
            <div class="table-responsive">
                <table style="width: 100%" class="table kz-table table-tranx is-compact" id="help-offer-table">
                    <thead class="thead-light">
                    <tr>
                        <th>Help</th>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Is Able to contact</th>
                        <th>Subject Cancel</th>
                        <th>Message Cancel</th>
                        <th>Is Within Time</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Updated at</th>
                        <th>Address</th>
                        <th>...</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        var currentLocation = window.location.href;
        $('#help-offer-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: currentLocation + "/data",
            columns: [
                 {
                    data: "help_title",
                    render: function(data, type, row) {
                        return '<a href="/helps/'+row.help_id+'" >'+data+'</a>'
                    }
                },
                {
                    data: "sender.name",
                    render: function(data, type, row) {
                        return '<a href="/users/'+row.sender.id+'" >'+data+'</a>'
                    }
                },
                {
                    data: "sender.email",
                    name: "sender.email",
                    visible: false
                },
                {
                    data: "status",
                    render: function (data, type, row) {
                        if (data === 1) {
                            return '<span class="badge badge-dot badge-secondary">Pending</span>'
                        }
                        if (data === 2) {
                            return '<span class="badge badge-dot badge-secondary">Waiting</span>'
                        }
                        if (data === 3) {
                            return '<span class="badge badge-dot badge-success">Accept</span>'
                        }
                        if (data === 4) {
                            return '<span class="badge badge-dot badge-gray">Reject</span>'
                        }
                        if (data === 5) {
                            return '<span class="badge badge-dot badge-success">Completed</span>'
                        }
                        if (data === 6) {
                            return '<span class="badge badge-dot badge-gray">Reject</span>'
                        }
                        if (data === 7) {
                            return '<span class="badge badge-dot badge-gray">Started</span>'
                        }
                        if (data === 8) {
                            return '<span class="badge badge-dot badge-gray">Paid</span>'
                        }
                        if (data === 9) {
                            return '<span class="badge badge-dot badge-gray">Approved</span>'
                        }
                        if (data === 10) {
                            return '<span class="badge badge-dot badge-gray">Declined</span>'
                        }
                        if (data === 11) {
                            return '<span class="badge badge-dot badge-gray">Helper Cancel</span>'
                        }
                        if (data === 12) {
                            return '<span class="badge badge-dot badge-gray">Paid</span>'
                        }
                        if (data === 13) {
                            return '<span class="badge badge-dot badge-gray">Helper Started</span>'
                        }
                        return ""
                    }
                },
                {
                    data: "is_able_contact",
                    name: "is_able_contact"
                },
                {
                    data: "subject_cancel",
                    name: "subject_cancel"
                },
                {
                    data: "message_cancel",
                    name: "message_cancel"
                },
                {
                    data: "is_within_time",
                    name: "is_within_time"
                },
                {
                    data: "start",
                    name: "start"
                },
                {
                    data: "end",
                    name: "end"
                },
                {
                    data: "updated_at",
                    name: "updated_at"
                },
                {
                    data: "address",
                    name: "address"
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
                            '                        <li><a href="/help-offers-cancel/' +row.id+ '"><em class="icon ni ni-eye view-detail-help"></em><span>View Details</span></a></li>\n' +
                            '                    </ul>\n' +
                            '              </div>\n' +
                            '    </div>'
                   }
                }

            ]
        });
    </script>
@endsection


