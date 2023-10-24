@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Offer List</span>
            <div class="table-responsive">
                <table style="width: 100%" class="table kz-table table-tranx is-compact" id="offer-table">
                    <thead class="thead-light">
                    <tr>
                        <th>Hangout</th>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Is Able to contact</th>
                        <th>Subject Reject</th>
                        <th>Message Reject</th>
                        <th>Is Within Time</th>
                        <th>Media</th>
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
        $('#offer-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: currentLocation + "/data",
            columns: [
                 {
                    data: "hangout_title",
                    render: function(data, type, row) {
                        return '<a href="/hangouts/'+row.hangout_id+'" >'+data+'</a>'
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
                            return '<span class="badge badge-dot badge-gray">Cancel</span>'
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
                            return '<span class="badge badge-dot badge-gray">Cast Cancelled</span>'
                        }
                        if (data === 12) {
                            return '<span class="badge badge-dot badge-gray">Paid</span>'
                        }
                        if (data === 14) {
                            return '<span class="badge badge-dot badge-gray">Guest Declined</span>'
                        }

                        return ""
                    }
                },
                {
                    data: "is_able_contact",
                    name: "is_able_contact"
                },
                {
                    data: "subject_reject",
                    name: "subject_reject"
                },
                {
                    data: "message_reject",
                    name: "message_reject"
                },
                {
                    data: "is_within_time",
                    name: "is_within_time"
                },
                {
                        data: 'media',
                        searchable: false,
                        sortable: false,
                        render: function (data, type, row) {

                            if (data == null || data.length == 0) {
                                return ''
                            }
                            
                            return  '  <button class="btn btn-xs btn-outline-gray view-content"  data-data="' + data + '" >View media...</button>'
                        }
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
                            '                        <li><a href="/hangout-offers-cancel/' +row.id+ '"><em class="icon ni ni-eye view-detail-hangout"></em><span>View Details</span></a></li>\n' +
                            '                    </ul>\n' +
                            '              </div>\n' +
                            '    </div>'
                   }
                }

            ]
        });
    </script>
@endsection


