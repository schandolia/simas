<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v2.1.15
* @link https://coreui.io
* Copyright (c) 2018 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Star Legal">
    <meta name="author" content="Star Legal">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('APP_NAME', 'Star Legal') }}</title>
    <link rel="icon" type="image/ico" href="{{url('/')}}/images/favicon.ico" sizes="any" />

    <!--Javascript-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    @if(Route::currentRouteName()=='dashboard')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js"></script>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/js/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/js/fileinput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/themes/fa/theme.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="{{URL::asset('/assets/js/vendor/magicsuggest/magicsuggest-min.js') }}"></script>
    
    <!-- Styles -->
    <link href="{{ URL::asset('/assets/css/main.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/assets/css/glyphicons/css/bootstrap.min.css') }}" rel="stylesheet">

</head>
@if(Route::currentRouteName()=='login')
<body class="app flex-row align-items-center">
    <div class="app-body">
    @yield('content')
    </div>
    <script src="{{ URL::asset('/assets/js/action_user.min.js') }}" defer></script>
@else
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    @include('layout.header')
    <div class="app-body">
    @include('layout.sidebar')
    @yield('content')
    </div>
    <footer class="app-footer text-center">
        <div>
            <a href="http://starlegal.id">Star Legal</a>
            <span>&copy; 2019</span>
        </div>
    </footer>
      @if($userInfo->getRoleKind()=='LEGAL')
        <script src="{{ URL::asset('/assets/js/action_legal.min.js') }}" defer></script>
        @elseif($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        <script src="{{ URL::asset('/assets/js/action_approver.min.js') }}" defer></script>
        @else
        <script src="{{ URL::asset('/assets/js/action_user.min.js') }}" defer></script>
    @endif
@endif
</body>
@yield('modal')
</html>
