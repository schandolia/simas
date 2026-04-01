@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-people icon-gradient bg-deep-blue"></i>
                </div>
                <div>Available PIC</div>
            </div>
            <!-- /.card-->
            <div class="row pt-3">
                <div class="col">
                    <table class="table table-responsive-sm table-hover table-outline mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="60px" class="text-center">
                                    <i class="icon-people"></i>
                                </th>
                                <th>User</th>
                                <th width="150px" class="text-center">Total Assigned Documents</th>
                                <th width="150px" class="text-center">Total Completed Documents</th>
                                <th width="150px" class="text-center">Current Assigned Document</th>
                                <th width="150px" class="text-center">Document Need to Revise</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                            <tr>
                                <td class="text-center">
                                    <div class="avatar">
                                        <img class="img-avatar" src="{{$member->avatar}}">
                                        <span class="avatar-status {{$member->logged_in?'badge-success':'badge-secondary'}}"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{$member->fullname}}</div>
                                        <div class="small text-muted">
                                        <span>{{$member->role}}</span> | {{$member->email}}</div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left">
                                                <strong>{{$member->assigned_docs}} documents</strong>
                                            </div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left">
                                                <strong>{{$member->completed_docs}} documents</strong>
                                            </div>
                                        </div>
                                        <div class="progress progress-xs">
                                        <div class="progress-bar bg-success" role="progressbar" style="width:{{$member->completed_docs==0?0:($member->completed_docs/$member->assigned_docs)*100}}%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left">
                                            <strong>{{$member->notCompleted_docs}} documents</strong>
                                            </div>
                                        </div>
                                        <div class="progress progress-xs">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width:{{$member->notCompleted_docs==0?0:($member->notCompleted_docs/Config::get('Constants.DOCUMENT_ASSIGNMENT_LIMIT'))*100}}%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left">
                                            <strong>{{$member->revise_docs}} documents</strong>
                                            </div>
                                        </div>
                                        <div class="progress progress-xs">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width:{{$member->revise_docs==0?0:($member->revise_docs/Config::get('Constants.DOCUMENT_REVISE_LIMIT'))*100}}%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Last login</div>
                                        <strong>{{\App\Utils\Utils::get_date_diff($member->last_login)}}</strong>
                                    </td>
                                    </tr>
                            @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" id="acc_token" name="acc_token" value="USER">
    </main>
@endsection
