@extends('layouts::base')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <!-- <div class="nk-block-head-content">
                <h4 class="nk-block-title page-title"></h4>
            </div> -->
            <div class="nk-block-head-content">
                <a
                  href="javascript:void(0);"
                  onclick="window.history.back()"
                  class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                  <em class="icon ni ni-arrow-left"></em><span>Back</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-bordered">
                        @if($help->media && $help->media->count() > 0 ) <img class="card-img-top" src="{{ \Storage::disk('gcs')->url($help->media->first()->thumb) }}" /> @endif
                        <div class="card-inner">
                            <h5 class="card-title">{{ $help->title }}</h5>
                            <h5 class="card-title">{{ $help->amount }} USD</h5>
                            
                            <p class="card-text">{{ $help->description }}</p>
                            @if($help->type == "1")
                                <span  style="margin-bottom: 5px" class="badge badge-outline-primary">Single</span>
                                <p><b>Start: </b> {{$help->start}} - <b>End: </b> {{$help->end}}</p>
                            @else
                                <span style="margin-bottom: 5px" class="badge badge-outline-secondary">Anytime</span>
                                <p><b>Schedule: </b> #{{$help->schedule}} </p>
                            @endif
                            <!-- <a style="margin-top: 5px" href="{{ route('admin.help.delete', ['id' => $help->id]) }}" class="btn btn-primary">Delete This help</a> -->
                        </div>

                    </div>
                </div>
                <div class="col-md-8">
                    <div class="user-card">
                        @if ($help->user->avatar == null)
                            <div class="user-avatar lg bg-primary">
                                <span>{{ strtoupper(substr($help->user->name, 0, 2)) }}</span>
                            </div>
                        @endif
                        @if ($help->user->avatar != null)
                            <div class="user-avatar lg">
                                <img style="width: 82px; height: 82px!important;" src="{{ \Storage::disk('gcs')->url('') }}{{ $help->user->avatar }}">
                            </div>
                        @endif
                        <div class="user-info">
                            <h5>{{ $help->user->name }}</h5>
                            <span class="sub-text">{{$help->user->email}}</span>
                        </div>
                    </div>
                    <hr>
                    <h6>Statistic</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Likes: </th>
                            <td>{{$help->reacts->count()}} likes</td>
                        </tr>
                        <tr>
                            <th>Comments: </th>
                            <td>{{$help->comments->count()}} comments</td>
                        </tr>
                        <tr>
                            <th>Offers: </th>
                            <td>{{$help->offers->count()}} offers <a href="/helps/{{$help->id}}/offers" class="btn btn-xs btn-secondary">Offers list</a>
                            </td>
                        </tr>
                        <tr>
                            <th>Specialities: </th>
                            <td>
                                <ul class="g-1" >
                                    @foreach($help->skills as $skill)
                                        <li class="btn-group">
                                            <button class="btn btn-xs btn-light btn-dim">{{ $skill->name }}</button>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <h6>Offer Detail</h6>
                    <table class="table table-bordered">
                      
                        <tr>
                            <th width="200">Sender name: </th>
                            <td>{{$offer->sender->name}}</td>
                        </tr>
                        <tr>
                            <th width="200">Sender email: </th>
                            <td>{{$offer->sender->email}}</td>
                        </tr>
                        <tr>
                            <th width="200">status: </th>
                            <td>{{$offer->status}}</td>
                        </tr>
                        <tr>
                            <th width="200">Is Able to contact: </th>
                            <td>{{$offer->is_able_contact}}</td>
                        </tr>
                        <tr>
                            <th width="200">Subject reject: </th>
                            <td>{{$offer->subject_reject}}</td>
                        </tr>
                        <tr>
                            <th width="200">Message reject: </th>
                            <td>{{$offer->message_reject}}</td>
                        </tr>
                        <tr>
                            <th width="200">Is within time: </th>
                            <td>{{$offer->is_within_time}}</td>
                        </tr>
                        <tr>
                            <th width="200">Start: </th>
                            <td>{{$offer->start}}</td>
                        </tr>
                        <tr>
                            <th width="200">End: </th>
                            <td>{{$offer->end}}</td>
                        </tr>
                        <tr>
                            <th width="200">updated at: </th>
                            <td>{{$offer->updated_at}}</td>
                        </tr>
                        <tr>
                            <th width="200">Address: </th>
                            <td>{{$offer->address}}</td>
                        </tr>
                        <tr>
                            <th width="200">Media: </th>
                            <td>

                            @php
                                            $coverArr = explode(";", $offer->media_evidence);
                                        @endphp

                                        @foreach($coverArr as $mediaItem)
                                <img class="card-img-top" src="{{$mediaItem}}" />
                                @endforeach
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


