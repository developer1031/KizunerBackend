@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <form class="form-validate is-alter" method="post" action="{{ route('admin.sample-hangout.import') }}" enctype="multipart/form-data">
                        @csrf
                        <span class="preview-title-lg overline-title">Import From Excel file</span> <small><a href="{{ asset('assets/sample_hangout.xlsx') }}">Download sample</a></small>
                        <div class="form-group">
                            <label class="form-label" for="file">Excel file</label>
                            <div class="form-control-wrap">
                                <input type="file" class="form-control" id="file" name="file" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-bordered bt-10">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add new Sample</span>
                    <form
                        class="form-validate is-alter"
                        action="@if($sampleHelp->id) {{ route('admin.sample-hangout.update', ['id' => $sampleHelp->id]) }} @else {{ route('admin.sample-hangout.store') }} @endif"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="sample.cover">
                        <input class="d-none" id="id" name="id" type="text" value="{{ $sampleHelp->id }}">
                        <div class="form-group">
                            <label class="form-label" for="title">Title</label>
                            <div class="form-control-wrap">
                                <input required type="text" value="{{ $sampleHelp->title }}" class="form-control" id="title" name="title" placeholder=""/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control" id="description" name="description" rows="5" required>{{ $sampleHelp->description }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="speciality">Specialities</label>
                            <div class="form-control-wrap">
                                <select name="speciality[]" id="speciality" class="form-control select2" multiple size="8">
                                    @foreach($skills as $skill)
                                        <option value="{{$skill->id }}" {{ (in_array($skill->id, $sample_specialities)) ? 'selected' : '' }} >{{$skill->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="category">Categories</label>
                            <div class="form-control-wrap">
                                <select name="category[]" id="category" class="form-control select2" multiple size="8">
                                    @foreach($categories as $category)
                                        <option value="{{$category->id }}" {{ (in_array($category->id, $sample_categories)) ? 'selected' : '' }} >{{$category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="image">Image</label>
                            <div class="form-control-wrap">
                                <input type="file" class="form-control" id="image" name="file" />
                            </div>
                        </div>

                        @if($sampleHelp->id && $thumb)
                            <img src="{{ $thumb }}" alt="" style="width: 80px">
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
                    <span class="preview-title-lg overline-title">Sample Hangouts List</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="chat-intent-table">
                            <thead class="thead-light">
                            <tr>
                                <th width="80">Image</th>
                                <th>Title</th>
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
            ajax: '{{ route('admin.sample-hangout.data') }}',
            columns: [
                {data: 'image'},
                {data: 'title'},
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

