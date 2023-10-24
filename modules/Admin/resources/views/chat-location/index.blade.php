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
                        Chat-Location
                    </span>
                    <form
                        class="form-validate is-alter"
                        action="@if($chat_location->id) {{ route('admin.chat-location.update', ['id' => $chat_location->id]) }} @else {{ route('admin.chat-location.store') }} @endif"
                        method="POST">
                        @csrf
                        <input class="d-none" id="id" name="id" type="text" value="{{ $chat_location->id }}">
                        <div class="form-group">
                            <label class="form-label" for="name">Location</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $chat_location->name }}" class="form-control" id="name" name="name" placeholder="Location"/>
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="latitude">Latitude</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $chat_location->latitude }}" class="form-control" id="latitude" name="latitude" placeholder="Latitude"/>
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="longitude">Longitude</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $chat_location->longitude }}" class="form-control" id="longitude" name="longitude" placeholder="Longitude"/>
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

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
                    <span class="preview-title-lg overline-title">Chat-Location List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="chat-intent-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Location</th>
                                <th>Country</th>
                                <th>Lat</th>
                                <th>Long</th>
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
            ajax: '{{ route('admin.chat-location.data') }}',
            columns: [
                {data: 'name'},
                {data: 'country'},
                {data: 'latitude'},
                {data: 'longitude'},
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
