@extends('layouts::_empty')

@section('body')
    <div class="nk-app-root">
        <div class="nk-main pattern-bg-top">
            <div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="/" class="logo-link nk-sidebar-logo">
                            <img class="logo-dark logo-img" src="{{ asset('admin-logo.png') }}"
                                 srcset="{{ asset('admin-logo.png') }} 2x" alt="kizuner-logo-dark">
                        </a>
                    </div>
                    <div class="nk-menu-trigger mr-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                                class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div>
                <div class="nk-sidebar-element pattern-bg">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                @include('_components::_sidebar')
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nk-wrap bg-transparent"
                 style="background: url({{asset('background.svg')}}) no-repeat; background-position: 95% 90%; background-size: 300px auto;">
                <div class="nk-header-fixed is-dark bg-transparent">
                    <div class="bg-white">
                        <div class="container-fluid ">
                            <div class="nk-header-wrap">
                                <div class="nk-menu-trigger d-xl-none ml-n1">
                                    <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em
                                            class="icon ni ni-menu"></em></a>
                                </div>
                                <div class="nk-header-brand d-xl-none">
                                    <a href="{{ route('admin.dashboard.index') }}" class="logo-link">
                                        <img class="logo-dark logo-img" src="{{ asset('admin-logo.png') }}"
                                             srcset="{{ asset('admin-logo.png') }} 2x" alt="logo-dark">
                                    </a>
                                </div>
                                <div class="nk-header-tools">
                                    <ul class="nk-quick-nav">
                                        @include('_components::_user')
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-content">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                @include('_components::_flash_messages')
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
                @include('_components::_footer')
            </div>
        </div>
    </div>
    <div class="modal fade" id="userModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User information</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.user.profile') }}" method="POST">
                        <div class="form-group">
                            @csrf
                            <label class="form-label" for="name">Name</label>
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <input value="{{ auth()->user()->name }}" type="text" required class="form-control" id="name" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <div class="form-control-wrap">
                                <input value="{{ auth()->user()->email }}"  type="email"  required class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <hr class="preview-hr">
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password_confirm">Password Confirmation</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

