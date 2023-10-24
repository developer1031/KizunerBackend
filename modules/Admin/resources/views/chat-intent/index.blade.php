@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add new Chat-Intent</span>
                    <form
                        class="form-validate is-alter"
                        action="@if($chat_intent->id) {{ route('admin.chat-intent.update', ['id' => $chat_intent->id]) }} @else {{ route('admin.chat-intent.store') }} @endif"
                        method="POST">
                        @csrf
                        <input class="d-none" id="id" name="id" type="text" value="{{ $chat_intent->id }}">
                        <div class="form-group">
                            <label class="form-label" for="point">Intent</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $chat_intent->intent }}" class="form-control" id="intent"
                                       name="intent" placeholder="Intent content"/>
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="price">Reply</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $chat_intent->reply }}" class="form-control" id="reply"
                                       name="reply" placeholder="Reply content"/>
                                @error('price')
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
                    <span class="preview-title-lg overline-title">Chat-Intent List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="chat-intent-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Intent</th>
                                <th>Reply</th>
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
            ajax: '{{ route('admin.chat-intent.data') }}',
            columns: [
                {data: 'intent'},
                {data: 'reply'},
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
