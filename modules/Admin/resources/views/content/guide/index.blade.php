@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add new video guide</span>
                    <form class="form-validate is-alter" action="{{ $guide->id ? route('admin.content.guide.update') : route('admin.content.guide.store') }}" method="POST">
                        @csrf
                        <input value="{{ $guide->id }}" type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <label for="url" class="form-label">URL</label>
                            <div class="form-control-wrap">
                                <div class="row">
                                    <div class="col-md-7"><input value="{{ $guide->url }}" type="url" class="form-control" required name="url" id="url"/></div>
                                    <div class="col-md-5"><button type="button" class="btn btn-primary" id="url-btn">Get Info</button></div>
                                </div>
                            </div>
                        </div>
                        
          <div class="form-group">
                            <label class="form-label" for="categories">Categories</label>
                            <div class="form-control-wrap">
                                <div style="width: 100%; height: 200px; overflow-y: scroll;border: 1px solid #dbdfea; border-radius: 4px;">
                                @if(isset($category_ids))
                                    @foreach($categories as $category)
                                        @php
                                            $isSelected = false;
                                        @endphp
                                        @foreach($category_ids as $category_id)
                                            @if($category->id == $category_id)
                                                @php
                                                    $isSelected = true;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if($isSelected == true)
                                                <div class="custom-control custom-control-sm custom-checkbox">
                                                    <input type="checkbox" checked value="{{$category->id}}" class="custom-control-input" name="categories[]" id="{{$category->id}}">
                                                    <label class="custom-control-label" for="{{$category->id}}">{{$category->name}}</label>
                                                </div> </br>
                                            @else
                                                <div class="custom-control custom-control-sm custom-checkbox">
                                                    <input type="checkbox" value="{{$category->id}}" class="custom-control-input" name="categories[]" id="{{$category->id}}">
                                                    <label class="custom-control-label" for="{{$category->id}}">{{$category->name}}</label>
                                                </div> </br>
                                            @endif
                                    @endforeach
                                @else 
                                    @foreach($categories as $category)
                                        <div class="custom-control custom-control-sm custom-checkbox">
                                                <input type="checkbox" value="{{$category->id}}" class="custom-control-input" name="categories[]" id="{{$category->id}}">
                                                <label class="custom-control-label" for="{{$category->id}}">{{$category->name}}</label>
                                            </div> </br>
                                    @endforeach
                                @endif
                            </div>
                            </div>
                        </div>

                      

                        <div class="form-group">
                            <label for="text" class="form-label">Text</label>
                            <div class="form-control-wrap">
                                <input value="{{ $guide->text }}"  type="text" class="form-control" required name="text" id="text"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position" class="form-label">Position</label>
                                    <div class="form-control-wrap">
                                        <input value="{{ $guide->position ?? 1 }}" type="number" class="form-control" required name="position"
                                               id="position"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration" class="form-label">Duration</label>
                                    <div class="form-control-wrap">
                                        <input  value="{{ $guide->duration }}"  type="text" readonly class="form-control" required name="duration"
                                               id="duration"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 10px">
                            <label for="cover" class="form-label">Cover</label>
                            <div class="form-control-wrap">
                                <img src="{{ $guide->cover ?? '' }}" id="cover-preview">
                                <input value="{{ $guide->cover ?? '' }}" type="text" class="form-control d-none" readonly name="cover" id="cover"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <div class="form-control-wrap">
                                <select name="status" id="status" class="form-control">
                                    <option value="1" {{ $guide->status ==1 ? 'selected' : '' }}>Enable</option>
                                    <option value="0" {{ $guide->status ==0 ? 'selected' : '' }}>Disable</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Save</button>
                                @if ($guide->id)
                                    <a class="btn btn-light" href="{{ route('admin.content.guide.index') }}">Cancel</a>
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
                    <span class="preview-title-lg overline-title">Video Guide</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="guide-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Cover</th>
                                <th>Link</th>
                                <th>Text</th>
                                <th>Duration</th>
                                <th>Position</th>
                                <th>Actions</th>
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

        $('#guide-table').DataTable({
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
            ajax: '{{ route('admin.content.guide.data') }}',
            columns: [
                {
                    data: 'cover',
                    bSortable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<img src="'+data+'" class="img-thumbnail"  width="50"/>'
                    }
                }
                ,
                {
                    data: 'url',
                    render: function(data, type, row) {
                        return '<a target="_blank" href="'+data+'"><em class="ni ni-youtube"></em> Youtube</a>'
                    }
                },
                {
                    data: 'text',
                    render: function(data, type, row) {
                        return data.substring(0, 20)
                    }
                },
                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data:'position',
                    name: 'position'
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return '<div class="btn-group ">\n' +
                            '  <a href="/content/guides/'+data+'/edit"  type="button" class="btn btn-outline-gray"><em class="ni ni-pen"></em></a>\n' +
                            '  <a href="/content/guides/'+data+'/delete" type="button" class="btn btn-outline-danger"><em class="ni ni-trash"></em></a>\n' +
                            '</div>'
                    }
                }
            ]
        });


        function convert_time(duration) {
            var a = duration.match(/\d+/g);

            if (duration.indexOf('M') >= 0 && duration.indexOf('H') == -1 && duration.indexOf('S') == -1) {
                a = [0, a[0], 0];
            }

            if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1) {
                a = [a[0], 0, a[1]];
            }
            if (duration.indexOf('H') >= 0 && duration.indexOf('M') == -1 && duration.indexOf('S') == -1) {
                a = [a[0], 0, 0];
            }

            duration = 0;

            if (a.length == 3) {
                duration = duration + parseInt(a[0]) * 3600;
                duration = duration + parseInt(a[1]) * 60;
                duration = duration + parseInt(a[2]);
            }

            if (a.length == 2) {
                duration = duration + parseInt(a[0]) * 60;
                duration = duration + parseInt(a[1]);
            }

            if (a.length == 1) {
                duration = duration + parseInt(a[0]);
            }
            return duration
        }

        function fancyTimeFormat(time)
        {
            // Hours, minutes and seconds
            var hrs = ~~(time / 3600);
            var mins = ~~((time % 3600) / 60);
            var secs = ~~time % 60;

            // Output like "1:01" or "4:03:59" or "123:03:59"
            var ret = "";

            if (hrs > 0) {
                ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
            }

            ret += "" + mins + ":" + (secs < 10 ? "0" : "");
            ret += "" + secs;
            return ret;
        }

        var key = 'AIzaSyDkQezPy7OG_xMLCtdW50NOCuTf-3WRRq0';
        $('#url-btn').on('click',function(e){
            var url = $('#url').val();
            var id = url.split('v=')[1]
            $.ajax({
                url: 'https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id='+id+'&key='+key,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                   if (data !== null) {
                       var item = data.items[0];
                       $('#text').val(item.snippet.title)
                       var durationNo = item.contentDetails.duration
                       var duration = fancyTimeFormat(convert_time(durationNo))
                       $('#duration').val(duration)
                       var cover = item.snippet.thumbnails.high.url
                       $('#cover').val(cover)
                       $('#cover-preview').attr('src', cover)
                       $('#cover-preview').addClass('img-thumbnail')
                   }
                }
            });

        });
    </script>
@endsection
