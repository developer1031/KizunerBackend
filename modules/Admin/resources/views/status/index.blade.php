@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Status List</span>
            <div class="table-responsive">
                <table width="100%" class="table kz-table table-tranx is-compact" id="status-table">
                    <thead class="thead-light">
                    <tr>
                        <th>Status</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th width="20">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <div class="modal fade" tabindex="-1" id="statusModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Status</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
        <script>
            var path = '{{ route('admin.status.data') }}';


            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const id = urlParams.get('id');
            if (id !== null) {
                path = '{{ route('admin.status.data') }}?id=' + id
            }

            var storagePath = '{{ \Storage::disk('gcs')->url('') }}'
            $('#status-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: path,
                columns: [
                    {
                        data: 'status',
                        render: function (data, type, row) {
                            var path = (row.media == null) ? "" : storagePath + row.media.thumb
                            return data.substring(0, 200) +
                                 '  <button class="btn btn-xs btn-outline-gray view-content" data-media="'+path+'" data-data="' + data + '" >Read more...</button>'
                        }
                    },
                    {
                        data: 'user.name',
                        render: function (data, type, row) {
                            return '<a href="/users/' + row.user.id + '" >' + data + '</a>'
                        }
                    },
                    {data: 'user.email', name: 'user.email', visible:false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return '<a href="/statuses/'+data+'/delete" class="center"><em class="ni ni-trash"></em></a>'
                        }
                    }
                ]
            });
            $('#status-table').on('click', '.view-content', function(e) {
                var data = $(this).data('data')
                var path = $(this).data('media')
                var media = ""
                if (path !== "") {
                    media = ' <img src="'+path+'" class="card-img-top" alt="">\n'
                }
                $('#statusModal .modal-body').empty();
                $('#statusModal .modal-body').append(
                    '<div class="card card-bordered content">\n' +
                     media +
                    '    <div class="card-inner">\n' +
                    '        <p class="card-text">'+data+'</p>\n' +
                    '    </div>\n' +
                    '</div>'
                );
                $('#statusModal').modal('show')
            })
        </script>
@endsection


