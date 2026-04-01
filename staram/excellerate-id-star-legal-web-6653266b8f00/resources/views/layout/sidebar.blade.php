<div class="sidebar">
    <nav class="sidebar-nav">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="{{route('dashboard')}}">
            <i class="nav-icon icon-speedometer"></i> Dashboard
          </a>
        </li>
        @if($userInfo->getRoleKind()=='ADMIN')
        <li class="nav-item">
            <a class="nav-link" href="{{route('userSetting')}}">
                <i class="nav-icon icon-people"></i> User Setting
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" href="{{route('request')}}">
                <i class="nav-icon icons icon-tag"></i> Requested
                @if($notif->request)
                <span class="badge badge-pill badge-danger">{{$notif->request}}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('review')}}">
                <i class="nav-icon icon-magnifier"></i> Reviewed
                @if($notif->review>0)
                <span class="badge badge-pill badge-info">{{$notif->review}}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('complete')}}">
                <i class="nav-icon icon-star"></i> Completed
                @if($notif->complete)
                <span class="badge badge-pill badge-success">{{$notif->complete}}</span>
                @endif
            </a>
        </li>
        @if($userInfo->getRoleKind()=='APPROVER' || $userInfo->getRoleKind()=='LEGAL'||$userInfo->getRoleKind()=='ADMIN')
        <li class="nav-item">
            <a class="nav-link" href="{{route('share')}}">
                <i class="nav-icon icon-cursor"></i> Shared
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-icon icon-logout"></i> Logout
            </a>
        </li>
      </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
  </div>
