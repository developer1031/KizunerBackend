@if ($message = Session::get('success'))
    <div class="alert alert-pro alert-success alert-dismissible" role="alert">
        <div class="alert-text">
            <h6>Success</h6>
            <p>{{$message}}</p>
        </div>
        <button class="close" data-dismiss="alert"></button>
    </div>
@endif


@if ($message = Session::get('error'))
    <div class="alert alert-pro alert-danger alert-dismissible" role="alert">
        <div class="alert-text">
            <h6>Error</h6>
            <p>{{ $message }}</p>
        </div>
        <button class="close" data-dismiss="alert"></button>
    </div>
@endif


@if ($message = Session::get('warning'))
    <div class="alert alert-pro alert-warning alert-dismissible" role="alert">
        <div class="alert-text">
            <h6>Warning</h6>
            <p>{{ $message }}</p>
        </div>
        <button class="close" data-dismiss="alert"></button>
    </div>
@endif


@if ($message = Session::get('info'))
    <div class="alert alert-pro alert-info alert-dismissible" role="alert">
        <div class="alert-text">
            <h6>Noti</h6>
            <p>{{ $message }}</p>
        </div>
        <button class="close" data-dismiss="alert"></button>
    </div>
@endif


@if ($errors->any())
    <div class="alert alert-pro alert-primary alert-dismissible" role="alert">
        <div class="alert-text">
            <h6>Information</h6>
            <p>There are something wrong, please check again!</p>
        </div>
        <button class="close" data-dismiss="alert"></button>
    </div>
@endif
