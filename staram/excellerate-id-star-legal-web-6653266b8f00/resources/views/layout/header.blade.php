<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-brand">
          <img class="navbar-brand-full" src="{{URL::asset('/assets/images/logo.png')}}" width="120" height="30">
        </div>
        <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
          <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="nav navbar-nav ml-auto">
          <li class="nav-item dropdown">
                  <div class="nav-link">
                    <h5>Hi, {{$userInfo->name}}</h5>
                    <input type="hidden" id="acc_role" name="acc_role" value={{$userInfo->role_id}}>
                  </div>

          <li class="nav-item d-md-down-none">
            <a class="nav-link" href="{{route('profile')}}">
              <i class="icon-settings"></i>
            </a>
          </li>
          <li class="nav-item d-md-down-none">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a class="nav-link" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                title="{{ __('Logout') }}">
                <i class="icon-logout"></i>
            </a>
          </li>
        </ul>
      </header>
