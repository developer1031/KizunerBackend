@extends('layouts::base')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner">
            <span class="preview-title-lg overline-title">Supports List</span>
            <div class="table-responsive">
                <table width="100%" class="table kz-table table-tranx is-compact" id="supports-table">
                    <thead class="thead-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Help offer</th>
                        <th>Hangout offer</th>
                        <th>Media</th>
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
<div class="modal fade" tabindex="-1" id="mediaModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">media</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
        <script>
            var path = '{{ route('admin.supports.data') }}';


            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const id = urlParams.get('id');
            if (id !== null) {
                path = '{{ route('admin.supports.data') }}?id=' + id
            }

            var storagePath = '{{ \Storage::disk('gcs')->url('') }}'
            $('#supports-table').DataTable({
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
                ajax: path,
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'subject', name: 'subject'},
                    {data: 'message', name: 'message'},
                    {
                        data: 'help_offer_id',
                        render: function (data, type, row) {

                            if (data == null || data.length == 0) {
                                return ''
                            }
                  
                            return  '<a href="/help-offers-cancel/' +row.help_offer_id+ '"><span>View Details</span></a>'
                        }
                    },
                    {
                        data: 'hangout_offer_id',
                        render: function (data, type, row) {

                            if (data == null || data.length == 0) {
                                return ''
                            }
                            
                            return  '<a href="/hangout-offers-cancel/' +row.hangout_offer_id+ '"><span>View Details</span></a>'
                        }
                    },
                    {
                        data: 'medias',
                        sortable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            if (data == 'undefined' || data == undefined || data == null || data.length == 0) {
                                return ''
                            }
          
                            return  "<button class='btn btn-xs btn-outline-gray view-content'  data-data='" + JSON.stringify(data) + "' >Read more...</button>";
                        }
                    },
                 
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return '<a href="/supports/'+data+'/delete" class="center"><em class="ni ni-trash"></em></a>'
                        }
                    }
                ]
            });
            $('#supports-table').on('click', '.view-content', function(e) {
                var data = $(this).data('data');
                const myArray = data;
                $('#mediaModal .modal-body').empty();
                myArray.forEach( function(mediaItem){
                    var storagePath = '{{ \Storage::disk('gcs')->url('') }}';
                    storagePath = storagePath.slice(0, -1);
                    src =  storagePath + mediaItem.path;

                    // check mp4 file and if it is mp4 file, then show video tag
                    if (src.includes('.mp4')) {
                      media = '<video class="card-img-top" controls><source src="'+ src +'" type="video/mp4"></video>\n';
                    } else {
                      media = '<img class="card-img-top" src="'+ src +'"/>\n';
                    }

                    $('#mediaModal .modal-body').append(
                    '<div class="card card-bordered content">\n' +
                     media +
                    '    <div class="card-inner">\n' +
                    '        <a class="card-text" target="_blank" href="'+ src +'">'+src+'</a>\n' +
                    '    </div>\n' +
                    '</div>'
                );
});
               
                $('#mediaModal').modal('show')
            })
        </script>
@endsection


