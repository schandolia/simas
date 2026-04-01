@extends('layout.app')

@section('content')
<script>
  $(document).ready(function(){
    var height = (window.innerHeight > 0) ? window.innerHeight : screen.height;
    console.log(height);
    if(height<=640)
      $('.app-body').css('margin-top','15px');
  });
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
        <div class="card-group">
            <div class="card p-4">
            <div class="card-body">
<a href="http://starlegal.id">
		<div class="text-center">
                    <div class="login-logo"></div>
		</div>
</a>
                <form method="POST" action="{{ route('login') }}">
                        @csrf
                    <h1 class="text-center">Welcome to {{env('APP_NAME','StarLegal')}}</h1>
                    <p class="text-muted">Sign In to your account</p>
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                        <i class="icon-user"></i>
                        </span>
                    </div>
                    <input id="email" type="email" placeholder="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                        <i class="icon-lock"></i>
                        </span>
                    </div>
                    <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <div class="input-group mb-4">
                        <div class="form-check form-check-inline mr-1">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                    <div class="row">

                    <div class="col-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </div>
                    <div class="col text-right">
                        <a class="btn btn-link"  href="#registerModal" data-toggle="modal" data-target="#registerModal">Register</a>
                    </div>
		    </div>
<div class="row pt-5">
	<div class="col-md text-center">Back to <a href="http://starlegal.id">StarLegal</a> website</div>
</div>
                </form>
            </div>
            </div>
        </div>
        </div>
    </div>
@endsection
@section ('modal')
<!-- Modal Add Folder-->
<div class="modal fade" id="registerModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-title">
                    <div class="col">
                        <h2 class="modal-title"><b>User Registration</b></h2>
                        <strong>Form User Registration</strong>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-register" action="{{route('registerUser')}}" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="input-email"><i class="fa fa-envelope-o pr-2"></i>Email</label>
                    <div class="col">
                        <input class="form-control" id="input-email" type="email" name="input-email" placeholder="Email Adress" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="input-username"><i class="fa fa-user-o pr-2"></i>Username</label>
                    <div class="col">
                        <input class="form-control" id="input-username" type="text" name="input-username" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="input-fullname"><i class="fa fa-vcard-o pr-2"></i>Full Name</label>
                    <div class="col">
                        <input class="form-control" id="input-fullname" type="text" name="input-fullname" placeholder="Full Name" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="input-password"><i class="fa fa-lock pr-2"></i>Password</label>
                    <div class="col">
                        <input class="form-control" id="input-password" type="password" name="input-password" placeholder="Password" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="input-password-conf"><i class="fa fa-check pr-2" id="password-check" required></i>Confirm Password</label>
                    <div class="col">
                        <input class="form-control" id="input-password-conf" type="password" name="input-password-conf" placeholder="Confirm Password"  required>
                    </div>
                </div>
                @csrf
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:90px">Close</button>
                <button class="btn btn-primary" type="submit">Register</button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
