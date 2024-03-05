@extends('layouts::base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-bordered">
                <div class="card-inner">
                    <span class="preview-title-lg overline-title">Add new video category</span>
                    <form class="form-validate is-alter" action="{{ $category->id ? route('admin.content.guide.category-update', $category) : route('admin.content.guide.category-store') }}" method="POST">
                        @csrf
                        <input value="{{ $category->id }}" type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <label for="name" class="form-label">Category</label>
                            <div class="form-control-wrap">
                                <input value="{{ $category->name }}"  type="text" class="form-control" required name="name" id="name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <button type="submit" class="btn btn-primary">Save</button>
                                @if ($category->id)
                                    <a class="btn btn-light" href="{{ route('admin.content.guide.category') }}">Cancel</a>
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
                    <span class="preview-title-lg overline-title">Video Categories</span>
                    <div class="table-responsive">
                        <table class="table kz-table table-tranx is-compact" id="guide-table">
                            <thead class="thead-light">
                            <tr>
                                <th>Video Category</th>
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
            ajax: '{{ route('admin.content.guide.category-data') }}',
            columns: [
                {
                    data: 'name',
                    render: function(data, type, row) {
                        return data.substring(0, 20)
                    }
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return '<div class="btn-group ">\n' +
                            '  <a href="/content/guides/category/'+data+'/edit"  type="button" class="btn btn-outline-gray"><em class="ni ni-pen"></em></a>\n' +
                            '  <a href="/content/guides/category/'+data+'/delete" type="button" class="btn btn-outline-danger"><em class="ni ni-trash"></em></a>\n' +
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
