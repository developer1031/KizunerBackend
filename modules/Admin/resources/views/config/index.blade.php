@extends('layouts::base')

@section('content')
    <div class="card card-bordered h-100">
        <div class="card-inner">
            <form action="{{ route('admin.config-setting.index') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card-head">
                            <h5 class="card-title">Global settings</h5>
                        </div>
                        <div class="row form-group">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Mail Notification content</label>
                                    <div class="form-control-wrap">
                                        <textarea name="content" rows="3" class="form-control">{{ @$offline_remain_setting['content'] }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nearby radius</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" name="nearby_radius" value="{{ @$nearby_radius }}" min="0"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Map radius</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" name="map_radius" value="{{ @$map_radius }}" min="0"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">First add post</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control" name="kizuner_first_add_post" value="{{ @$kizuner_first_add_post }}" min="0"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Now Payments email</label>
                                    <div class="form-control-wrap">
                                        <input type="email" class="form-control" name="now_payments_email" value="{{ @$now_payments_email }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Now Payments password</label>
                                    <div class="form-control-wrap">
                                        <input class="form-control" name="now_payments_password" value="{{ @$now_payments_password }}" />
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

@endsection

@section('page-script')

@endsection

