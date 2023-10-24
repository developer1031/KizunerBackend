@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add new Package</span>
                    <form
                        class="form-validate is-alter"
                        action="@if($package->id) {{ route('admin.package.update', ['id' => $package->id]) }} @else {{ route('admin.package.store') }} @endif"
                        method="POST">
                        @csrf
                        <input class="d-none" id="id" name="id" type="text" value="{{ $package->id }}">
                        <div class="form-group">
                            <label class="form-label" for="point">Kizuna</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $package->point }}" class="form-control" id="point"
                                       name="point" placeholder="Kizuna point"/>
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="price">Price</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-right">
                                    <em class="icon ni ni-sign-dollar"></em>
                                </div>
                                <input required type="text" value="{{ $package->price }}" class="form-control" id="price"
                                       name="price" placeholder="Price in USD"/>
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Save</button>
                                @if ($package->id)
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
                    <span class="preview-title-lg overline-title">Package List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="packages-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Package</th>
                                <th>Price</th>
                                <th width="60">Actions</th>
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
        $('#packages-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.package.data') }}',
            columns: [
                {data: 'point', name: 'point'},
                {
                    data: 'price',
                    render: function (data, type, row) {
                        return '<strong>$' + data / 1000 + '</strong>'
                    }
                },
                {
                    data: 'edit',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return '<a href="' + data + '" class="btn btn-secondary">Edit</a>'
                    }
                }
            ]
        });
    </script>
@endsection
