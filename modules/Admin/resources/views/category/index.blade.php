@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add Category</span>
                    <form
                        class="form-validate is-alter"
                        action="@if($category->id) {{ route('admin.category.update', ['id' => $category->id]) }} @else {{ route('admin.category.store') }} @endif"
                        method="POST">
                        @csrf
                        <input minlength="4" class="d-none" id="id" name="id" type="text" value="{{ $category->id }}">
                        <div class="form-group">
                            <label class="form-label" for="name">Name</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $category->name }}" class="form-control" id="name" name="name" placeholder="Category name"/>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Save</button>
                                @if ($category->id)
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
                    <span class="preview-title-lg overline-title">Categories List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="category-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Edit</th>
                                <th>Delete</th>
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
        $('#category-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.category.data') }}',
            columns: [
                {data: 'name', name: 'name'},
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


