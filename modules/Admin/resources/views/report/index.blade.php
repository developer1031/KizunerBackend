@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Report List</span>
            <div class="table-responsive">
                <table style="width: 100%" class="table kz-table table-tranx is-compact" id="report-table">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Report User</th>
                        <th>Reference(Click to check)</th>
                        <th>Reason</th>
                        <th>Resolve</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('#report-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.report.data') }}',
            columns: [
                {data: 'id', name: 'id'},
                {
                    data: "user.name",
                    render: function(data, type, row) {
                        return '<a href="/users/'+row.user.id+'" >'+data+'</a>'
                    }
                },
                {
                    data: 'type',
                    render: function (data, type, row) {
                        if (data === 'hangout') {
                            return '<a href="/hangouts/'+row.reference_id+'" class="btn-xs btn btn-danger">Hangout</a>';
                        } else if (data === 'status') {
                            return '<a href="/statuses?id='+row.reference_id+'" class="btn-xs btn btn-success">Status</a>';
                        } else if (data === 'user') {
                            return '<a href="/users/'+row.reference_id+'" class="btn-xs btn btn-primary">User</a>';
                        }
                    }
                },
                {data: 'reason', name: 'reason'},
                {
                    data: 'id',
                    render(data, type, row) {
                        if (row.status === 1) {
                            return '<button class="btn btn-xs btn-success">Resolved</button>';
                        }
                        return '<button class="btn btn-xs btn-outline-primary btn-resolve" data-id="'+data+'">Resolve</button>';
                    }
                }
            ]
        });
        $('#report-table').on('click', '.btn-resolve', function(e) {
            var id = $(this).data('id')
            $.ajax({
                url: '{{ route("admin.report.update")}}',
                type: 'post',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    location.reload(true);
                }
            });
        })
    </script>
@endsection


