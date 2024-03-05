@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add Specialities</span>
                    <form
                        class="form-validate is-alter"
                        action="@if($skill->id) {{ route('admin.skill.update', ['id' => $skill->id]) }} @else {{ route('admin.skill.store') }} @endif"
                        method="POST">
                        @csrf
                        <input minlength="4" class="d-none" id="id" name="id" type="text" value="{{ $skill->id }}">
                        <div class="form-group">
                            <label class="form-label" for="name">Name</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $skill->name }}" class="form-control" id="name"
                                       name="name" placeholder="Skill name"/>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="suggest" name="suggest" {{ $skill->suggest ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="suggest">Suggest User</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Save</button>
                                @if ($skill->id)
                                    <a class="btn btn-light" href="{{ route('admin.package.index') }}">Cancel</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-bordered">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Specialities List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="skill-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Suggest</th>
                                <th>Created by</th>
                                <th></th>
                                <th></th>
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

        $('#skill-table').on('click', '.skill-update', function(e) {
            var id = $(this).data('id');
            var value = $(this).data('value')
            var vm = $(this)
            $.ajax({
                url: '{{ route("admin.skill.update")}}',
                type: 'post',
                data: {
                    id: id,
                    value: value
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    location.reload(true);
                }
            });

        });

        $('#skill-table').DataTable({
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
            ajax: '{{ route('admin.skill.data') }}',
            columns: [
                {data: 'name', name: 'name'},
                {
                    data: 'suggest',
                    render: function (data, type, row) {
                            var value = "Disable"
                            var className = "btn-default"
                            if (data == '1') {
                                value = "Enable"
                                className = "btn-success"
                            }
                            return '<button data-value="'+data+'" data-id="'+row.id+'" value="'+data+'" class="btn btn-xs skill-update '+className+'">'+value +'</button>'
                        }
                },
                {
                    data: 'admin',
                    render: function (data, type, row) {
                        var value = "User"
                        var className = "btn-default"
                        if (data === '1') {
                            value = "Admin"
                            className = "btn-primary"
                        }
                        return '<button disabled class="btn btn-xs skill-update '+className+'">'+value+'</button>'
                    }
                },
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


