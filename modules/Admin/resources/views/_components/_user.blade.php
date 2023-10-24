<li class="dropdown user-dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <div class="user-toggle">
            <div class="user-avatar sm">
                <em class="icon ni ni-user-alt"></em>
            </div>
            <div class="user-info d-none d-md-block">
                <div class="user-status">{{ auth()->user()->about }}</div>
                <div class="user-name dropdown-indicator">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-s1">
        <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
            <div class="user-card">
                <div class="user-avatar">
                    <span>AB</span>
                </div>
                <div class="user-info">
                    <span class="lead-text">{{ auth()->user()->name }}</span>
                    <span class="sub-text">{{ auth()->user()->email }}</span>
                </div>
            </div>
        </div>
        <div class="dropdown-inner">
            <ul class="link-list">
                <li><a href="{{ route('admin.user.profile') }}" id="update-profile"><em class="icon ni ni-user-alt"></em><span>Update Profile</span></a></li>
            </ul>
        </div>
        <div class="dropdown-inner">
            <ul class="link-list">
                <li><a href="{{ route('admin.user.logout') }}"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
            </ul>
        </div>
    </div>
</li>

<script>
    $(document).ready(function(){
        $('#update-profile').on('click', function(e){
            e.preventDefault()
            $('#userModal').modal('show')
        })
    })
</script>
