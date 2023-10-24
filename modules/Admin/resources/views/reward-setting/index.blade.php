@extends('layouts::base')

@section('content')

    <div class="card card-bordered h-100">
        <div class="card-inner">
            <form action="{{ route('admin.reward-tutorial-setting') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card-head">
                            <h5 class="card-title">Tutorial images settings</h5>
                        </div>

                        <div class="row form-group">
                            <div class="col-6">
                                <div class="form-group">
                                    <div><img src="{{ \Storage::disk('gcs')->url($tutorial_setting[0]['image']) }}" style="max-width: 70px" /></div>
                                    <div style="margin: 10px 0;"><input type="checkbox" value="0" name="tutorial_disabled[]" {{ @$tutorial_setting[0]['disabled'] ? 'checked' : '' }}> <label class="form-label">Disabled</label></div>
                                    <label class="form-label">Image 01</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="tutorial_images[]" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Title 01</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="tutorial_title[]" value="{{ $tutorial_setting[0]['title'] }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description 01</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="tutorial_description[]" id="" rows="2">{{ @$tutorial_setting[0]['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div>
                                        <img src="{{ \Storage::disk('gcs')->url($tutorial_setting[1]['image']) }}" style="max-width: 70px" />
                                    </div>
                                    <div style="margin: 10px 0;"><input type="checkbox" value="1" name="tutorial_disabled[]" {{ @$tutorial_setting[1]['disabled'] ? 'checked' : '' }}> <label class="form-label">Disabled</label></div>
                                    <label class="form-label">Image 02</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="tutorial_images[]" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Title 02</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="tutorial_title[]" value="{{ $tutorial_setting[1]['title'] }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description 02</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="tutorial_description[]" id="" rows="2">{{ $tutorial_setting[1]['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>


                        <div class="row form-group">
                            <div class="col-6">
                                <div class="form-group">
                                    <div>
                                        <img src="{{ \Storage::disk('gcs')->url($tutorial_setting[2]['image']) }}" style="max-width: 70px" />
                                    </div>
                                    <div style="margin: 10px 0;"><input type="checkbox" value="2" name="tutorial_disabled[]" {{ @$tutorial_setting[2]['disabled'] ? 'checked' : '' }}> <label class="form-label">Disabled</label></div>
                                    <label class="form-label">Image 03</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="tutorial_images[]"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Title 03</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="tutorial_title[]" value="{{ $tutorial_setting[2]['title'] }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description 03</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="tutorial_description[]" id="" rows="2">{{ $tutorial_setting[2]['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <div>
                                        <img src="{{ \Storage::disk('gcs')->url($tutorial_setting[3]['image']) }}" style="max-width: 70px" />
                                    </div>
                                    <div style="margin: 10px 0;"><input type="checkbox" value="3" name="tutorial_disabled[]" {{ @$tutorial_setting[3]['disabled'] ? 'checked' : '' }}> <label class="form-label">Disabled</label></div>
                                    <label class="form-label">Image 04</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="tutorial_images[]"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Title 04</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="tutorial_title[]" value="{{ $tutorial_setting[3]['title'] }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description 04</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="tutorial_description[]" id="" rows="2">{{ $tutorial_setting[3]['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="row form-group">
                            <div class="col-6">
                                <div class="form-group">
                                    <div>
                                        <img src="{{ \Storage::disk('gcs')->url($tutorial_setting[4]['image']) }}" style="max-width: 70px" />
                                    </div>
                                    <div style="margin: 10px 0;"><input type="checkbox" value="4" name="tutorial_disabled[]" {{ @$tutorial_setting[4]['disabled'] ? 'checked' : '' }}> <label class="form-label">Disabled</label></div>
                                    <label class="form-label">Image 05</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="tutorial_images[]"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Title 05</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="tutorial_title[]" value="{{ $tutorial_setting[4]['title'] }}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Description 05</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="tutorial_description[]" id="" rows="2">{{ $tutorial_setting[4]['description'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary float-right" style="margin-top: 10px" type="submit">Update</button>
                    </div>

                </div>
            </form>
        </div>
    </div>



    <div class="card card-bordered h-100">
        <div class="card-inner">
            <form action="{{ route('admin.reward-trophy-setting') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card-head">
                            <h5 class="card-title">Trophy icons settings</h5>
                        </div>

                        <div class="row form-group">
                            <div class="col-2">
                                <img src="{{ \Storage::disk('gcs')->url($trophy_icons['icon_0']) }}" style="max-width: 70px" />
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label class="form-label">Icon 01</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="trophy_icon[]" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <img src="{{ \Storage::disk('gcs')->url($trophy_icons['icon_1']) }}" style="max-width: 70px" />
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label class="form-label">Icon 02</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="trophy_icon[]" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-2">
                                <img src="{{ \Storage::disk('gcs')->url($trophy_icons['icon_2']) }}" style="max-width: 70px" />
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label class="form-label">Icon 03</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" name="trophy_icon[]" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary float-right" style="margin-top: 10px" type="submit">Update</button>
                    </div>

                </div>
            </form>
        </div>
    </div>



        <div class="card card-bordered h-100">
        <div class="card-inner">

            <form action="{{ route('admin.reward-setting.index') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-12">
                        <div class="card-head">
                            <h5 class="card-title">Badges settings</h5>
                        </div>

                        <div class="row form-group">
                            <div class="col-3">
                                <div class="form-group">
                                    <label class="form-label" for="point">Radius</label>
                                    <div class="form-control-wrap">
                                        <input required type="number" value="{{ $reward_radius }}" class="form-control" name="reward_radius" min="0" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        @foreach($data as $key => $badge)
                            <input required type="hidden" value="{{ $badge['name'] }}" class="form-control" name="{{$key}}[name]" />
                            <input required type="hidden" value="{{ $badge['name_reward'] }}" class="form-control" name="{{$key}}[name_reward]" />
                            <div class="row form-group">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="point">Title</label>
                                        <div class="form-control-wrap">
                                            <input required type="text" value="{{ $badge['name'] }}" class="form-control" name="{{$key}}[name]" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label class="form-label" for="point">{{ $badge['name'] }} (points)</label>
                                        <div class="form-control-wrap">
                                            <input required type="number" value="{{ $badge['point'] }}" class="form-control" name="{{$key}}[point]" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label class="form-label" for="point">{{ $badge['name_reward'] }} (+Kizuna)</label>
                                        <div class="form-control-wrap">
                                            <input required type="number" value="{{$badge['reward']}}" class="form-control" name="{{$key}}[reward]" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    @if(isset($badge['icon']) && $badge['icon'])
                                        <label class="form-label" for="point">Icon</label>
                                        <img src="{{ \Storage::disk('gcs')->url($badge['icon']) }}" style="max-width: 70px" />
                                    @endif
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-label">Active Icon</label>
                                        <div class="form-control-wrap">
                                            <input type="file" class="form-control" name="{{$key . '_icon'}}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-1">
                                    @if(isset($badge['inactive_icon']) && $badge['inactive_icon'])
                                        <label class="form-label" for="point">Icon</label>
                                        <img src="{{ \Storage::disk('gcs')->url($badge['inactive_icon']) }}" style="max-width: 70px" />
                                    @endif
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-label">Inactive Icon</label>
                                        <div class="form-control-wrap">
                                            <input type="file" class="form-control" name="{{$key . '_inactive_icon'}}" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row form-group">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="point">Description</label>
                                        <div class="form-control-wrap">
                                            <textarea cols="30" rows="5" class="form-control" name="{{$key}}[description]">{{ @$badge['description'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="margin-bottom: 50px; border: 1px dashed #d8d8d8">
                        @endforeach
                    </div>
                </div>

                <button class="btn btn-primary float-right" style="margin-top: 10px" type="submit">Update</button>
            </form>
        </div>
    </div>
@endsection

@section('page-script')

@endsection

