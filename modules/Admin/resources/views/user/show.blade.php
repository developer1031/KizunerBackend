@extends('layouts::base')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title page-title">Customers / <strong class="text-primary small">{{ $user->name }}</strong>
                    @if($user->deleted) <small style="font-size: 18px">(deleted)</small> @endif
                </h4>
                <div class="nk-block-des text-soft">
                    <ul class="list-inline">
                        <li>User ID: <span class="text-base">{{ $user->id }}</span></li>
                        <li>Date join: <span class="text-base">{{ $user->created_at }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="javascript:void(0);" onclick="goBack()" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                <a href="javascript:void(0);" onclick="goBack()" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em class="icon ni ni-arrow-left"></em></a>
            </div>
        </div>
    </div>
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-aside-wrap">
                <div class="card-content">
                    <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#personal"><em class="icon ni ni-user-circle"></em><span>Personal</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#transaction"><em class="icon ni ni-repeat"></em><span>Transactions</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#user-info"><em class="icon ni ni-edit-alt-fill"></em><span>Update Information</span></a>
                        </li>
                        <li class="nav-item nav-item-trigger d-xxl-none">
                            <a href="#" class="toggle btn btn-icon btn-trigger" data-target="userAside"><em class="icon ni ni-user-list-fill"></em></a>
                        </li>
                    </ul><!-- .nav-tabs -->
                    <div class="card-inner tab-content" >
                        <div class="tab-pane active" id="personal">
                            <div class="nk-block">
                                <div class="nk-block-head">
                                    <h5 class="title">Personal Information</h5>
                                    <p>Basic info, like your name and address, that user use on Kizuner.</p>
                                </div><!-- .nk-block-head -->
                                <div class="profile-ud-list">
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Title</span>
                                            <span class="profile-ud-value">
                                            @if($user->gender === 0)
                                                    Miss.
                                                @elseif ($user->gender === 1)
                                                    Mr.
                                                @else
                                                    Unknown
                                                @endif
                                        </span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Username</span>
                                            <span class="profile-ud-value">{{ $user->username }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Full Name</span>
                                            <span class="profile-ud-value">{{ $user->name }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Date of Birth</span>
                                            <span class="profile-ud-value">{{ $user->birth_date }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Mobile Number</span>
                                            <span class="profile-ud-value">{{ $user->phone }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Email Address</span>
                                            <span class="profile-ud-value">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div><!-- .profile-ud-list -->
                            </div><!-- .nk-block -->
                            <div class="nk-block">
                                <div class="nk-block-head nk-block-head-line">
                                    <h6 class="title overline-title text-base">Additional Information</h6>
                                </div><!-- .nk-block-head -->
                                <div class="profile-ud-list">
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Joining Date</span>
                                            <span class="profile-ud-value">{{ $user->created_at }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-ud-item">
                                        <div class="profile-ud wider">
                                            <span class="profile-ud-label">Reg Method</span>
                                            <span class="profile-ud-value">
                                            @if($user->social_provider == 'google')
                                                    Google
                                                @endif
                                                @if($user->social_provider == 'facebook')
                                                    Facebook
                                                @endif
                                                @if($user->social_provider == null)
                                                    Email
                                                @endif
                                        </span>
                                        </div>
                                    </div>
                                </div><!-- .profile-ud-list -->
                            </div><!-- .nk-block -->
                            <div class="nk-divider divider md"></div>
                            <div class="nk-block">
                                <div class="nk-block-head nk-block-head-sm nk-block-between">
                                    <h5 class="title">Specialities</h5>
                                </div><!-- .nk-block-head -->
                                <div class="bq-note">
                                    <ul class="g-1">
                                        @foreach($skills as $skill)
                                            <li class="btn-group">
                                                <button class="btn btn-xs btn-light btn-dim">{{ $skill }}</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div><!-- .bq-note -->
                            </div><!-- .nk-block -->
                        </div>
                        <div class="tab-pane" id="transaction">
                            <span class="preview-title-lg overline-title">Transaction History</span>
                            <div class="table-responsive">
                                <table style="width: 100%" class="table kz-transaction-table table-tranx is-compact" id="transaction-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>User</th>
                                            <th>Issue Date</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="user-info">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h5 class="card-title">User Information</h5>
                                    </div>
                                    <form action="{{ route('admin.user.update',  ['id' => $user->id]) }}?type=info" method="POST">
                                        @csrf
                                        <div class="row g-4">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="full-name-1">Full Name</label>
                                                    <div class="form-control-wrap">
                                                        <input value="{{$user->name}}" required type="text" class="form-control" id="name" name="name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="email-address-1">Email address</label>
                                                    <div class="form-control-wrap">
                                                        <input value="{{$user->email}}" @if(!$user->social_provider) required @endif type="email" class="form-control" id="email" name="email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="phone-no-1">Phone No</label>
                                                    <div class="form-control-wrap">
                                                        <input value="{{$user->phone}}"  type="text" class="form-control" id="phone" name="phone">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="phone-no-1">Username</label>
                                                    <div class="form-control-wrap">
                                                        <input value="{{$user->username}}"  type="text" class="form-control" id="username" name="username">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label">Activation Status</label>
                                                    <ul class="custom-control-group g-3 align-center">
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->email_verified_at) checked @endif value="1" type="checkbox" class="custom-control-input" name="email_verified_at" id="email_verified_at">
                                                                <label class="custom-control-label" for="email_verified_at">Email verified</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->phone_verified_at) checked @endif value="1" type="checkbox" class="custom-control-input" name="phone_verified_at" id="phone_verified_at">
                                                                <label class="custom-control-label" for="phone_verified_at">Phone verified</label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Notification Setting</label>
                                                    <ul class="custom-control-group g-3 align-center">
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->hangout_help_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="hangout_help_notification" id="hangout_help_notification">
                                                                <label class="custom-control-label" for="hangout_help_notification">Hangout/Help notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->message_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="message_notification" id="message_notification">
                                                                <label class="custom-control-label" for="message_notification">Message notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->follow_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="follow_notification" id="follow_notification">
                                                                <label class="custom-control-label" for="follow_notification">Follow notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->comment_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="comment_notification" id="comment_notification">
                                                                <label class="custom-control-label" for="comment_notification">Comment notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->like_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="like_notification" id="like_notification">
                                                                <label class="custom-control-label" for="like_notification">Like notification</label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Email Notification Setting</label>
                                                    <ul class="custom-control-group g-3 align-center">
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->hangout_help_email_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="hangout_help_email_notification" id="hangout_help_email_notification">
                                                                <label class="custom-control-label" for="hangout_help_email_notification">Hangout/Help email notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->message_email_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="message_email_notification" id="message_email_notification">
                                                                <label class="custom-control-label" for="message_email_notification">Message email notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->follow_email_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="follow_email_notification" id="follow_email_notification">
                                                                <label class="custom-control-label" for="follow_email_notification">Follow email notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->comment_email_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="comment_email_notification" id="comment_email_notification">
                                                                <label class="custom-control-label" for="comment_email_notification">Comment email notification</label>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="custom-control custom-control-sm custom-checkbox">
                                                                <input @if($user->like_email_notification) checked @endif value="1" type="checkbox" class="custom-control-input" name="like_email_notification" id="like_email_notification">
                                                                <label class="custom-control-label" for="like_email_notification">Like email notification</label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-lg btn-primary">Save Informations</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- .card-inner -->
                </div><!-- .card-content -->
                <div class="card-aside card-aside-right user-aside toggle-slide toggle-slide-right toggle-break-xxl" data-content="userAside" data-toggle-screen="xxl" data-toggle-overlay="true" data-toggle-body="true">
                    <div class="card-inner-group" data-simplebar>
                        <div class="card-inner">
                            <div class="user-card user-card-s2">
                                @if ($user->avatar == null)
                                    <div class="user-avatar lg bg-primary">
                                        <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                @endif
                                @if ($user->avatar != null)
                                    <div class="user-avatar lg">
                                        <img style="width: 82px; height: 82px!important;" src="{{ \Storage::disk('gcs')->url('') }}{{ $user->avatar }}">
                                    </div>
                                    @endif
                                <div class="user-info" style="cursor: pointer">
                                    <div class="badge
                                            @if ($user->deleted == 0)
                                                block-user
                                            @endif
                                            @if($user->block == 0)
                                                badge-success
                                            @elseif ($user->block == 1 || $user->deleted == 1)
                                                badge-danger
                                            @endif
                                            badge-pill ucap">
                                        @if ($user->deleted == 1)
                                            <em class="icon ni ni-trash"></em>
                                            Deleted
                                        @elseif($user->block == 0)
                                            <em class="icon ni ni-na"></em>
                                            Active
                                        @elseif ($user->block == 1)
                                            <em class="icon ni ni-account-setting-fill"></em>
                                            Blocked
                                        @endif
                                    </div>
                                    <h5>{{ $user->name }}</h5>
                                    <span class="sub-text">{{$user->email}}</span>
                                </div>
                            </div>
                        </div><!-- .card-inner -->
                        <div class="card-inner card-inner-sm">
                            <ul class="btn-toolbar justify-center gx-1">
                                <li><a href="mailto:{{$user->email}}" class="btn btn-trigger btn-icon"><em class="icon ni ni-mail"></em></a></li>
                                <li><button class="delete-user btn btn-trigger btn-icon text-danger"><em class="icon ni ni-trash"></em></button></li>
                            </ul>
                        </div><!-- .card-inner -->
                        ><!-- .card-inner -->
                        <div class="card-inner">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="profile-stats">
                                        <span class="amount">{{ $statistic['offer'] }}</span>
                                        <span class="sub-text">Total Order</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="profile-stats">
                                        <span class="amount">{{ $statistic['complete'] }}</span>
                                        <span class="sub-text">Complete</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="profile-stats">
                                        <span class="amount">{{ $statistic['proccess'] }}</span>
                                        <span class="sub-text">Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .card-inner -->
                    </div><!-- .card-inner -->
                </div><!-- .card-aside -->
            </div><!-- .card-aside-wrap -->
        </div><!-- .card -->
    </div><!-- .nk-block -->
@endsection

@section('page-script')
    <div class="modal fade" tabindex="-1" id="transactionModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction information</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="250">Type</th>
                            <td class="transction-type"></td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td class="transction-user"></td>
                        </tr>
                        <tr>
                            <th>Hangout Time <small>(Offer type)</small></th>
                            <td class="transction-offer"></td>
                        </tr>
                        <tr>
                            <th>Purchase Amount <small>(Purchase type)</small></th>
                            <td class="transction-amount"></td>
                        </tr>
                        <tr>
                            <th>Card <small>(Purchase type)</small></th>
                            <td class="transction-card"></td>
                        </tr>
                    </table>
                </div>
{{--                <div class="modal-footer bg-light">--}}
{{--                    <button class="btn btn-danger" data-id="">Revert this transaction</button>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
    <script>
        var storagePath = '{{ \Storage::disk('gcs')->url('') }}'
        $('#transaction-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.user.transaction.data', ['id' => $user->id]) }}',
            columns: [
                {
                    data: 'name',
                    sDefaultContent: "",
                    bSortable: false,
                    render: function(data, type, row) {
                        if (row.user_avatar) {
                            return '<a href="/users/'+row.user_id+'"><div class="user-card" >' +
                                '<div class="user-avatar">' +
                                '    <img style="width: 40px; height: 40px"  src="'+storagePath + row.user_avatar +'" />' +
                                '</div>' +
                                '<div class="user-name">' +
                                '<span class="tb-lead">'+row.user_name+'</span>' +
                                '</div>' +
                                '</div></a>'
                        } else {
                            return '<a href="/users/'+row.user_id+'"><div class="user-card" >' +
                                '<div class="user-avatar bg-primary">' +
                                '    <span>'+row.user_name.toUpperCase().substring(0,2)+'</span>' +
                                '</div>' +
                                '<div class="user-name">' +
                                '<span class="tb-lead">'+row.user_name+'</span>' +
                                '</div>' +
                                '</div></a>'
                        }
                    }
                }, { data: "created_at", name: "created_at"},
                {
                    data: 'point',
                    searchable: false,
                    render: function(data, type, row) {
                        if (row.balance_type === 'add') {
                            return '<button class="btn btn-sm btn-outline-gray">+ ' + data + ' Kizuna</button>'
                        } else {
                            return '<button class="btn btn-sm btn-outline-primary">- ' + data + ' Kizuna</button>'
                        }
                    }
                },
                {
                    data: 'type',
                    searchable: false,
                    render: function(data, type, row) {
                        return '<button data-id="'+row.id+'" class="transaction-info btn btn-sm btn-outline-info"> ' + data.toUpperCase() + '</button>'
                    }
                },
            ]
        });
        $('.block-user').on('click', function(e) {
            var id = $(this).data('id')
            $.ajax({
                url: '{{ route("admin.user.update", ['id' => $user->id])}}?type=block',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    location.reload(true);
                }
            });

        });

        $('.delete-user').on('click', function(e) {
            var id = $(this).data('id')
            $.ajax({
                url: '{{ route("admin.user.update", ['id' => $user->id])}}?type=delete',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    location.reload(true);
                }
            });

        });

        $('#transaction-table').on('click', '.transaction-info', function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route('admin.user.transaction.show') }}',
                type: 'post',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    $('#transactionModal').modal('show');
                    $('#transactionModal .transction-type').html(data.type.toUpperCase());
                    $('#transactionModal .transction-point').html(data.data.point + ' Kizuna');
                    $('#transactionModal .transction-user').html('<a class="btn btn-primary btn-xs" href="/users/'+data.user.id+'">'+data.user.name+'</a>');
                    $('#transactionModal .transction-offer').html('None Avaiable');
                    $('#transactionModal .transction-amount').html('None Avaiable');
                    if (data.type == 'offer') {
                        $('#transactionModal .transction-offer').html(data.data.start + ' - ' + data.data.end);
                    }
                    if (data.type == 'purchase') {
                        $('#transactionModal .transction-amount').html(data.data.amount);
                        $('#transactionModal .transction-card').html(data.data.card);
                    }
                }
            });
        });

        function goBack() {
            // Use the stored previous page URL
            window.history.back()
        }
    </script>
@endsection


