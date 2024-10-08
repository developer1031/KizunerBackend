@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">
                        @if($chat_location->id)
                            Update
                        @else
                            Add new
                        @endif
                        Chat Public Room
                    </span>
                    <form
                        class="form-validate is-alter"
                        action="@if($chat_location->id) {{ route('admin.chat-public-group.update', ['id' => $chat_location->id]) }} @else {{ route('admin.chat-public-group.store') }} @endif"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input class="d-none" id="id" name="id" type="text" value="{{ $chat_location->id }}">
                        <div class="form-group">
                            <label class="form-label" for="point">Room name</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $chat_location->name }}" class="form-control" id="name"
                                       name="name" placeholder=""/>
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="point">Avatar</label>
                            <div class="form-control-wrap">
                                <input type="file" class="form-control" id="avatar" name="avatar" placeholder=""/>
                            </div>
                        </div>

                        @if($chat_location->avatar)
                        <div class="form-group">
                            <img src="{{ \Storage::disk('gcs')->url($chat_location->avatar) }}" style="width: 100px;" />
                        </div>
                        @endif

                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-bordered">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Public Room List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="chat-intent-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Room name</th>
                                <th>Avatar</th>
                                <th width="60">Actions</th>
                                <th width="60"></th>
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
        $('#chat-intent-table').DataTable({
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
            ajax: '{{ route('admin.chat-public-group.data') }}',
            columns: [
                {data: 'name'},
                { data: 'avatar_url' },
                {
                    data: 'edit',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return '<a href="' + data + '" class="btn btn-secondary">Edit</a>'
                    }
                },
                {
                    data: 'delete',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return ' <a href="' + data + '" class="btn btn-danger">Delete</a>'
                    }
                }
            ]
        });
    </script>
@endsection
