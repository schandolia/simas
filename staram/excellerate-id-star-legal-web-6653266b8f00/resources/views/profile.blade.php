@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-user icon-gradient bg-deep-blue"></i>
                </div>
                <div>User Profiles</div>
            </div>
            <!-- /.card-->
            <div class="row">
                <div class="col">
                    <table class="table table-responsive-sm text-left profile-info">
                        <tbody>
                            <tr>
                                <td rowspan="7" width="150px" class="text-center">
                                <img class="img-avatar"  src="{{url($userInfo->avatar)}}">
                                </td>
                                <th width="200px"  style="border-top:1px solid #c8ced3;"><i class="fa fa-user-o pr-2"></i>Username</th>
                                <td>{{$userInfo->name}}</td>
                            </tr>
                            <tr>
                                <th><i class="fa fa-vcard-o pr-2"></i>Full Name</th>
                                <td>{{$userInfo->fullname}}</td>
                            </tr>
                            <tr>
                                <th><i class="fa fa-envelope-o pr-2"></i>Email</th>
                                <td>{{$userInfo->email}}</td>
                            </tr>
                            <tr>
                                <th><i class="fa fa-sitemap pr-2"></i>Role</th>
                                <td>{{$userInfo->getRoleKind()}}</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <button class="btn btn-square btn-primary" type="button" data-toggle="modal" data-target="#change-pwd-modal"><i class="fa fa-lock pr-2"></i> Change Password</button>
                                    <button class="btn btn-square btn-primary" type="button" data-toggle="modal" data-target="#change-profile-modal"><i class="fa fa-pencil pr-2"></i> Update Profile</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
@section ('modal')
<!-- Modal Change Password Modal-->
<div id="change-pwd-modal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm-chg-password" class="form-horizontal" action="{{route('changePassword')}}" method="post">
            <div class="modal-body">
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="email-input">Old Password</label>
                      <div class="col-md-9">
                        <input class="form-control" id="old-password" type="password" name="old-password" placeholder="Old Password">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="password-input">New Password</label>
                      <div class="col-md-9">
                        <input class="form-control" id="new-password" type="password" name="new-password" placeholder="New Password">
                      </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password-input">Confirm New Password</label>
                        <div class="col-md-9">
                          <input class="form-control" id="confirm-password" type="password" name="confirm-password" placeholder="Confirm New Password">
                        </div>
                    </div>
                    @csrf
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-lock pr-2"></i>Change Password</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Change Profile Modal-->
<div class="modal fade" id="change-profile-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Profile Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm-upd-profile" class="form-horizontal" action="{{route('changeProfile')}}" method="post">
            <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="email-input">Username</label>
                        <div class="col-md-9">
                        <input class="form-control" id="username" type="text" name="username" placeholder="Username" value="{{$userInfo->name}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password-input">Full Name</label>
                        <div class="col-md-9">
                            <input class="form-control" id="fullname" type="text" name="fullname" placeholder="Full Name" value="{{$userInfo->fullname}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password-input">Email</label>
                        <div class="col-md-9">
                            <input class="form-control" id="email" type="email" name="email" placeholder="Email" value="{{$userInfo->email}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password-input">Role</label>
                        <div class="col-md-9">
                            <input class="role" id="role" type="password" name="role" placeholder="{{$userInfo->getRoleKind()}}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="password-input">Avatar</label>
                        <div class="col-md-9">
                            <input id="avatar" type="file" name="avatar" accept="image/*">
                        </div>
                    </div>
                    @csrf
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-pencil pr-2"></i>Update Profile</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
