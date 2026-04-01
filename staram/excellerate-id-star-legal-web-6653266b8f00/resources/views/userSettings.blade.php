@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-people icon-gradient bg-deep-blue"></i>
                </div>
                <div>User Settings</div>
            </div>
            <!-- /.card-->
            <div class="row pt-3">
                <div class="col">
                    <table id="user-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>BU Head Responsible</th>
                                <th>Last Logged In</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
@section ('modal')
<!-- Modal Add Folder-->
<div class="modal fade" id="usersDetailModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-title">
                    <div class="col">
                        <h2 class="modal-title"><b>Change User</b></h2>
                        <strong>Form Change User Setting</strong>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-chgRole" action="{{route('changeRole')}}">
            <div class="modal-body">
                <table class="table table-responsive-sm text-left profile-info">
                    <tbody>
                        <tr>
                            <td rowspan="7" width="150px" class="text-center">
                            <img class="img-avatar" id="avatar-txt"  src="">
                            </td>
                            <th width="200px"  style="border-top:1px solid #c8ced3;"><i class="fa fa-user-o pr-2"></i>Username</th>
                            <td id="username-txt"></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-vcard-o pr-2"></i>Full Name</th>
                            <td id="fullname-txt"></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-envelope-o pr-2"></i>Email</th>
                            <td id="email-txt"></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-sitemap pr-2"></i>Role</th>
                            <td>
                                <select class="form-control" id="role-id" name="role-id">
                                    <option value="0">Role</option>
                                    @foreach ($doctypes as $doctype)
                                    <option value="{{$doctype->id}}">{{$doctype->role_name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr id="tr-buhead">
                            <th><i class=" fa fa-black-tie pr-2"></i>BU Head Responsible</th>
                            <td>
                                <select class="form-control" id="buhead-id" name="buhead-id">
                                    <option value="0">Role</option>
                                    @foreach ($buheads as $buhead)
                                    <option value="{{$buhead->id}}">{{$buhead->fullname}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border:0" class="text-right">
                                <input type="hidden" id="user-id" name="user-id">
                                @csrf
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:90px">Close</button>
                <button class="btn btn-square btn-primary" type="submit">Assign Role</button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
