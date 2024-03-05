@extends('layouts::base')

@section('content')
    <script>
        var user = {!! json_encode($user['users']) !!}
        var months = {!! json_encode($months) !!}

        var hangout_statistic = {!! json_encode($hangout_statistic) !!}
        var hangout_offer_statistic = {!! json_encode($hangout_statistic_offer) !!}
        var help_statistic = {!! json_encode($help_statistic) !!}
        var help_offer_statistic = {!! json_encode($help_statistic_offer) !!}

        var likeCount_statistic = {!! json_encode($likeCount_statistic) !!}
        var shareCount_statistic = {!! json_encode($shareCount_statistic) !!}
        var commentsCount_statistic = {!! json_encode($commentsCount_statistic) !!}
    </script>
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Overview</h3>
                <div class="nk-block-des text-soft">
                    <p>Welcome to Kizuner Dashboard.</p>
                </div>

            </div>
        </div>
        <form action="">
            <div class="row">
                <div class="col-md-3">
                    From:<input type="date" class="form-control" name="from" required value="{{ $from }}">
                </div>
                <div class="col-md-3">
                    To:<input type="date" class="form-control" name="to" required value="{{ $to }}">
                </div>
                <div class="col-md-3">
                    &nbsp;<br/>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-4">
                <canvas id="hangout_help" style="display: block; max-height: 800px;"></canvas>
            </div>
            <div class="col-4">
                <canvas id="offer_hangout_help" style="display: block; max-height: 800px;"></canvas>
            </div>
        </div>
        <div class="row g-gs">

            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Users Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Total users"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $user['total'] }}</span>
                                <span class="sub-title"><span class="change  @if($user['gap'] < 0)  down text-danger @else up text-success @endif">
                                     @if($user['gap'] < 0)
                                        <em class="icon ni ni-arrow-long-down"></em>
                                     @else
                                       <em class="icon ni ni-arrow-long-up"></em>
                                     @endif
                                    @if($user['gap'] < 0) -  @else + @endif {{ $user['gap'] }}
                                    </span>users since last month</span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="newUserSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Hangout Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $hangoutCount }}</span>
                                <span class="sub-title"><span class="change up text-success">Hangouts since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="hangoutStatisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Hangout Offer Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $offerCount }}</span>
                                <span class="sub-title"><span class="change up text-success">Hangout Offers since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="hangoutOfferStatisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Helps Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $helpCount }}</span>
                                <span class="sub-title"><span class="change up text-success">Helps since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="helpStatisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Helps Offers Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $offerHelpCount }}</span>
                                <span class="sub-title"><span class="change up text-success">Help Offers since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="helpOfferStatisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>

            <!-- Like -->
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Likes Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $likeCount }}</span>
                                <span class="sub-title"><span class="change up text-success">All Likes since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="likeCount_statisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>

            <!-- Share -->
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Shares Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $shareCount }}</span>
                                <span class="sub-title"><span class="change up text-success">All Shares since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="shareCount_statisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>

            <!-- Comments -->
            <div class="col-sm-4 col-lg-6 col-xxl-4">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start mb-2">
                            <div class="card-title">
                                <h6 class="title">Comments Statistic</h6>
                            </div>
                            <div class="card-tools">
                                <em class="card-hint icon ni ni-help-fill" data-toggle="tooltip" data-placement="left" title="Daily Avg. subscription"></em>
                            </div>
                        </div>
                        <div class="align-end flex-sm-wrap g-4 flex-md-nowrap">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $commentsCount }}</span>
                                <span class="sub-title"><span class="change up text-success">All Comments since last month</span></span>
                            </div>
                            <div class="nk-sales-ck">
                                <canvas class="general-bar-chart" id="commentsCount_statisticSub"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>

            <!-- Cast/Guest by Locations -->
            <div class="col-xxl-5">
                <div class="card card-bordered card-full">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Casts/Guests by locations</h6>
                                </div>
                                <div class="card-tools"></div>
                            </div>
                        </div>
                        <div class="card-inner card-inner-md">
                            <div class="user-card">
                                <table class="table kz-table table-tranx is-compact">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Country</th>
                                        <th width="150px;">Casts No.</th>
                                        <th width="150px;">Guests No.</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($casts_guests as $key => $casts_guest)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $casts_guest['cast'] }}</td>
                                        <td>{{ $casts_guest['guest'] }}</td>
                                    </tr>
                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>

            <!-- Speciality Statistic -->
            <div class="col-xxl-5">
                <div class="card card-bordered card-full">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Speciality Statistic</h6>
                                </div>
                                <div class="card-tools">
                                    <a href="{{ route('admin.skill.index') }}" class="link">View All</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner card-inner-md">
                            <div class="user-card">
                                <table class="table kz-table table-tranx is-compact" id="speciality-table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Speciality</th>
                                        <th width="150px;">Hangouts No.</th>
                                        <th width="150px;">Helps No.</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>

            <div class="col-xxl-5">
                <div class="card card-bordered card-full">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">New Users</h6>
                                </div>
                                <div class="card-tools">
                                    <a href="{{ route('admin.user.index') }}" class="link">View All</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner card-inner-md">
                            <div class="user-card">
                                <table class="table kz-table table-tranx is-compact" id="users-table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div>


        </div><!-- .row -->
    </div><!-- .nk-block -->
@endsection
@section('page-script')
    <script async="" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        //Handout_help data
        var data_hangout_help = {
            datasets: [{
                data: [{{$hangoutCount}}, {{$helpCount}}],
                backgroundColor:  [
                    window.chartColors.red,
                    window.chartColors.orange,
                ]
            }],
            labels: [
                'Hangouts',
                'Helps',
            ],
        };

        var hangout_help_ctx = document.getElementById('hangout_help');
        new Chart(hangout_help_ctx, {
            type: 'pie',
            data: data_hangout_help,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Hangouts/Helps Statistic'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        //offer data
        var data_offer_hangout_help = {
            datasets: [{
                data: [{{$offerCount}}, {{$offerHelpCount}}],
                backgroundColor:  [
                    window.chartColors.blue,
                    window.chartColors.purple,
                ]
            }],
            labels: [
                'Hangout offers',
                'Helps offers',
            ],
        };
        var offer_hangout_help_ctx = document.getElementById('offer_hangout_help');
        new Chart(offer_hangout_help_ctx, {
            type: 'pie',
            data: data_offer_hangout_help,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Hangouts/Helps Offers Statistic'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

    </script>

    <script>
        !function (NioApp, $) {
            "use strict";
            var newUserSub = {
                labels: months,
                dataUnit: 'users',
                stacked: true,
                datasets: [{
                    label: "Register User",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: user
                }]
            };

            var hangoutStatisticSub = {
                labels: months,
                dataUnit: 'hangout_statistic',
                stacked: true,
                datasets: [{
                    label: "Hangouts",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: hangout_statistic
                }]
            };

            var hangoutOfferStatisticSub = {
                labels: months,
                dataUnit: 'hangout_offer_statistic',
                stacked: true,
                datasets: [{
                    label: "Hangout Offers",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: hangout_offer_statistic
                }]
            };

            var helpStatisticSub = {
                labels: months,
                dataUnit: 'help_statistic',
                stacked: true,
                datasets: [{
                    label: "Helps",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: help_statistic
                }]
            };

            var helpOfferStatisticSub = {
                labels: months,
                dataUnit: 'help_offer_statistic',
                stacked: true,
                datasets: [{
                    label: "Help offers",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: help_offer_statistic
                }]
            };

            //Likes
            var likeCount_statisticSub = {
                labels: months,
                dataUnit: 'likeCount_statistic',
                stacked: true,
                datasets: [{
                    label: "Likes",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: likeCount_statistic
                }]
            };

            //Shares
            var shareCount_statisticSub = {
                labels: months,
                dataUnit: 'shareCount_statistic',
                stacked: true,
                datasets: [{
                    label: "Shares",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: shareCount_statistic
                }]
            };

            //Comments
            var commentsCount_statisticSub = {
                labels: months,
                dataUnit: 'commentsCount_statistic',
                stacked: true,
                datasets: [{
                    label: "Shares",
                    color: ["#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#e9ecff", "#6576ff"],
                    data: commentsCount_statistic
                }]
            };

            function generealBarChart(selector, set_data) {
                var $selector = selector ? $(selector) : $('.general-bar-chart');
                $selector.each(function () {
                    var $self = $(this),
                        _self_id = $self.attr('id'),
                        _get_data = typeof set_data === 'undefined' ? eval(_self_id) : set_data,
                        _d_legend = typeof _get_data.legend === 'undefined' ? false : _get_data.legend;

                    var selectCanvas = document.getElementById(_self_id).getContext("2d");
                    var chart_data = [];

                    for (var i = 0; i < _get_data.datasets.length; i++) {
                        chart_data.push({
                            label: _get_data.datasets[i].label,
                            data: _get_data.datasets[i].data,
                            // Styles
                            backgroundColor: _get_data.datasets[i].color,
                            borderWidth: 2,
                            borderColor: 'transparent',
                            hoverBorderColor: 'transparent',
                            borderSkipped: 'bottom',
                            barPercentage: .7,
                            categoryPercentage: .7
                        });
                    }

                    var chart = new Chart(selectCanvas, {
                        type: 'bar',
                        data: {
                            labels: _get_data.labels,
                            datasets: chart_data
                        },
                        options: {
                            legend: {
                                display: _get_data.legend ? _get_data.legend : false,
                                labels: {
                                    boxWidth: 30,
                                    padding: 20,
                                    fontColor: '#6783b8'
                                }
                            },
                            maintainAspectRatio: false,
                            tooltips: {
                                enabled: true,
                                callbacks: {
                                    title: function title(tooltipItem, data) {
                                        return false;
                                    },
                                    label: function label(tooltipItem, data) {
                                        return data['labels'][tooltipItem['index']] + ' ' + data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']];
                                    }
                                },
                                backgroundColor: '#eff6ff',
                                titleFontSize: 11,
                                titleFontColor: '#6783b8',
                                titleMarginBottom: 4,
                                bodyFontColor: '#9eaecf',
                                bodyFontSize: 10,
                                bodySpacing: 3,
                                yPadding: 8,
                                xPadding: 8,
                                footerMarginTop: 0,
                                displayColors: false
                            },
                            scales: {
                                yAxes: [{
                                    display: false,
                                    stacked: _get_data.stacked ? _get_data.stacked : false,
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }],
                                xAxes: [{
                                    display: false,
                                    stacked: _get_data.stacked ? _get_data.stacked : false
                                }]
                            }
                        }
                    });
                });
            } // init chart

            NioApp.coms.docReady.push(function () {
                generealBarChart();
            });
        }(NioApp, jQuery);
        var storagePath = '{{ \Storage::disk('gcs')->url('') }}'

        $('#speciality-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            stateSaveCallback: function (settings, data) {
                localStorage.setItem(
                    'DataTables_Special' + settings.sInstance,
                    JSON.stringify(data)
                );
            },
            stateLoadCallback: function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Special' + settings.sInstance));
            },
            ajax: '{{ route('admin.skill.data-speciality-dashboard') }}',
            columns: [
                { data: 'name' },
                {
                    data: 'hangouts_count',
                    searchable: false,
                },
                {
                    data: 'helps_count',
                    searchable: false,
                }
            ]
        });

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            stateSaveCallback: function (settings, data) {
                localStorage.setItem(
                    'DataTables_Users' + settings.sInstance,
                    JSON.stringify(data)
                );
            },
            stateLoadCallback: function (settings) {
                return JSON.parse(localStorage.getItem('DataTables_Users' + settings.sInstance));
            },
            ajax: '{{ route('admin.dashboard.user.data') }}',
            columns: [
                {
                    data: 'name',
                    sDefaultContent: "",
                    searching: true,
                    render: function(data, type, row) {
                        if (row.thumb) {
                            return '<div class="user-card" >' +
                                '<div class="user-avatar">' +
                                '    <img style="width: 40px; height: 40px"  src="'+storagePath + row.thumb +'" />' +
                                '</div>' +
                                '<div class="user-name">' +
                                    '<span class="tb-lead lead-text">'+row.name+'</span>' +
                                    '<span class="tb-lead sub-text">'+row.email+'</span>' +
                                '</div>' +
                                '</div>'
                        } else {
                            if(row.name) {
                                return '<div class="user-card" >' +
                                    '<div class="user-avatar bg-primary">' +
                                    '    <span>'+row.name.toUpperCase().substring(0,2)+'</span>' +
                                    '</div>' +
                                    '<div class="user-name">' +
                                    '<span class="tb-lead">'+row.name+'</span>' +
                                    '<span class="tb-lead sub-text">'+row.email+'</span>' +
                                    '</div>' +
                                    '</div>'
                            }
                            else {
                                return '<div class="user-card" >' +
                                    '<div class="user-avatar bg-primary">' +
                                    '    <span>'+row.email.toUpperCase().substring(0,2)+'</span>' +
                                    '</div>' +
                                    '<div class="user-name">' +
                                    '<span class="tb-lead">'+row.email+'</span>' +
                                    '<span class="tb-lead sub-text"></span>' +
                                    '</div>' +
                                    '</div>'
                            }

                        }
                    }
                },
                {
                    data: 'id',
                    ordering: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<center><a href="/users/'+data+'" class="btn btn-sm btn-outline-primary">View</a></center>'
                    }
                }
            ]
        });
    </script>
@endsection
