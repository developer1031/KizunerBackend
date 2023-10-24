@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Offer List</span>
            <div class="table-responsive">
                <table style="width: 100%" class="table kz-table table-tranx is-compact" id="offer-table">
                    <thead class="thead-light">
                    <tr>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Address</th>
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
                    data: "start",
                    name: "start"
                },
                {
                    data: "end",
                    name: "end"
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
                    data: "address",
                    name: "address"
                }
            ]
        });
    </script>
@endsection


