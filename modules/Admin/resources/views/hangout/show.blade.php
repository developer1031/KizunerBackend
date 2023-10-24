@extends('layouts::base')

@section('content')
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-bordered">
                        @if($hangout->media && $hangout->media->count() > 0 ) <img class="card-img-top" src="{{ \Storage::disk('gcs')->url($hangout->media->first()->thumb) }}" /> @endif
                        <div class="card-inner">
                            <h5 class="card-title">{{ $hangout->title }}</h5>
                            <p class="card-text">{{ $hangout->description }}</p>
                            @if($hangout->type == "1")
                                <span  style="margin-bottom: 5px" class="badge badge-outline-primary">Single</span>
                                <p><b>Start: </b> {{$hangout->start}} - <b>End: </b> {{$hangout->end}}</p>
                            @else
                                <span style="margin-bottom: 5px" class="badge badge-outline-secondary">Anytime</span>
                                <p><b>Schedule: </b> #{{$hangout->schedule}} </p>
                            @endif
                            <a style="margin-top: 5px" href="{{ route('admin.hangout.delete', ['id' => $hangout->id]) }}" class="btn btn-primary">Delete This Hangout</a>
                        </div>

                    </div>
                </div>
                <div class="col-md-8">
                    <div class="user-card">
                        @if ($hangout->user->avatar == null)
                            <div class="user-avatar lg bg-primary">
                                <span>{{ strtoupper(substr($hangout->user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        @if ($hangout->user->avatar != null)
                            <div class="user-avatar lg">
                                <img style="width: 82px; height: 82px!important;" src="{{ \Storage::disk('gcs')->url('') }}{{ $hangout->user->avatar }}">
                            </div>
                        @endif
                        <div class="user-info">
                            <h5>{{ $hangout->user->name }}</h5>
                            <span class="sub-text">{{$hangout->user->email}}</span>
                        </div>
                    </div>
                    <hr>
                    <h6>Statistic</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Likes: </th>
                            <td>{{$hangout->reacts->count()}} likes</td>
                        </tr>
                        <tr>
                            <th>Comments: </th>
                            <td>{{$hangout->comments->count()}} comments</td>
                        </tr>
                        <tr>
                            <th>Offers: </th>
                            <td>{{$hangout->offers->count()}} offers <a href="/hangouts/{{$hangout->id}}/offers" class="btn btn-xs btn-secondary">Offers list</a>
                            </td>
                        </tr>
                        <tr>
                            <th>Specialities: </th>
                            <td>
                                <ul class="g-1" >
                                    @foreach($hangout->skills as $skill)
                                        <li class="btn-group">
                                            <button class="btn btn-xs btn-light btn-dim">{{ $skill->name }}</button>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')

@endsection


