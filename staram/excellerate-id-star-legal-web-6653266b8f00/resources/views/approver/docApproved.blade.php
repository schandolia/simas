@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="fa fa-legal icon-gradient bg-deep-blue"></i>
                </div>
                <div>Approved Documents</div>
            </div>
            <!-- /.card-->
            <div class="row pt-3">
                <div class="col">
                    <table id="generic-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Requested</th>
								<th>Activities</th>
                                <th>Document Type</th>
                                <th>Proposed By</th>
                                <th class="doc-stat">The Parties</th>
                                <th class="doc-stat">Purpose Agreement</th>
                                <th>Request Status</th>
                                <th>Assigned To</th>
                                <th>Verify Status</th>
                                <th class="app-stat">Approved by CEO</th>
                                <th class="app-stat">Approved by CFO</th>
                                <th class="app-stat">Approved by BU Head</th>
                                <th class="app-stat">Approved by Legal Head</th>
                                <th>Document Status</th>
                                <th class="doc-stat">Description</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" id="acc_token" name="acc_token" value="APPROVER">
        <input type="hidden" id="data-url" name="data-url" value="{{route('getApprovedDocs')}}">
    </main>
@endsection
@section ('modal')
<!-- Modal Add Folder-->
<div class="modal fade" id="genericDocDetailModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col">
                    <h2 class="modal-title" id="title-txt"></h2>
                    <strong>Form Agreement Request (F1)</strong>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a id="req-detail-tab-btn" class="nav-link active" data-toggle="tab" href="#req-detail" role="tab" aria-selected="true">
                            Description
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#req-attachment" role="tab" aria-selected="false">
                            Attachment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#req-history" role="tab" aria-selected="false">
                            History
                        </a>
                    </li>
                    <li class="nav-item" id="approval-tab-btn">
                        <a class="nav-link text-danger" data-toggle="tab" href="#req-approval" role="tab" aria-selected="false">
                            Approve *
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="req-detail" role="tabpanel">
                        <table class="table table-responsive-sm">
                            <tbody>
                                <tr>
                                    <td width="250px">Document Type</td>
                                    <td id="requestDetail-doc-type"></td>
                                </tr>
                                <tr id="proposed-by-info">
                                    <td width="250px">Proposed by</td>
                                    <td id="requestDetail-proposed-by"></td>
                                </tr>
                                <tr id="proposed-date-info">
                                    <td width="250px">Proposed Date</td>
                                    <td id="requestDetail-proposed-date"></td>
                                </tr>
                                <tr>
                                    <td>Purpose/Nature of Agreement</td>
                                    <td id="requestDetail-purpose"></td>
                                </tr>
                                <tr>
                                    <td>The Parties</td>
                                    <td id="requestDetail-parties"></td>
                                </tr>
                                <tr>
                                    <td>Description/Notes</td>
                                    <td id="requestDetail-description"></td>
                                </tr>
                                <tr>
                                    <td>Transaction/Commercial Terms</td>
                                    <td id="requestDetail-commercial"></td>
                                </tr>
                                <tr>
                                    <td>Value of Transaction</td>
                                    <td id="requestDetail-value"></td>
                                </tr>
                                <tr>
                                    <td>Toleration of Late Payment</td>
                                    <td id="requestDetail-toleration"></td>
                                </tr>
                                <tr>
                                    <td>Condition Precedent</td>
                                    <td id="requestDetail-condition"></td>
                                </tr>
                                <tr>
                                    <td>Termination Terms</td>
                                    <td id="requestDetail-termination"></td>
                                </tr>
                                <tr>
                                    <td>Term of Payment</td>
                                    <td id="requestDetail-payment"></td>
                                </tr>
                                <tr>
                                    <td>Term of Delay and Percentage Penalty</td>
                                    <td id="requestDetail-delay"></td>
                                </tr>
                                <tr>
                                    <td>Guarantee/Security</td>
                                    <td id="requestDetail-guarantee"></td>
                                </tr>
                                <tr>
                                    <td>Term of Agreement</td>
                                    <td id="requestDetail-agreement"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="req-attachment" role="tabpanel">
                        <table class="table table-responsive-sm">
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="req-history" role="tabpanel">
                        <table class="table table-responsive-sm fold-table" id="req-history-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Action</th>
                                    <th>At</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="req-approval" role="tabpanel">
                        <div class="row">
                            <div class="card col md">
                                <div class="card-body font-xs">
                                    <div class="row font-lg"><b>Service Level Agreement</b></div>
                                    <div class="row">
                                        <table id="sla-container" class="table"></table>
                                    </div>
                                    <div class="row font-lg"><b>Notes</b></div>
                                    <ul style="margin-left: -1.25rem">
                                        <li>The determined/estimated processing time is subject to the current number of jobs handled by legal team</li>
                                        <li>The number of pages of document, agreement and complications and bilingual format can increase processing time</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card col md">
                                <div class="card-body font-xs">
                                    <form id="approveRequest-form" action="{{route('approveRequest')}}" method="post">
                                        <div class="row font-lg"><b>Approval Form</b></div>
                                        <div class="row">
                                            <div class="col-md-5 col-form-label">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" id="action-opt-hold" type="radio" value="hold" name="action-option">
                                                    <label class="form-check-label" for="action-opt-hold">Reject</label>
                                                    <input type="hidden" name="request-id" id="request-id" value="">
                                                </div>
                                                <div class="form-check form-check-inline ml-2">
                                                    <input class="form-check-input" id="action-opt-approve" type="radio" value="approve" name="action-option" checked>
                                                    <label class="form-check-label" for="action-opt-approve">Approve</label>
                                                </div>
                                            </div>
                                            <div class="col-md text-right">
                                                <div class="row">
                                                    <div class="col">
                                                        @if(Auth::user()->getRoleName()=='Legal Head')
                                                        <input class="form-check-input" id="member-input" type="text" value="" name="member-input" placeholder="Member Name">
                                                        @endif
                                                    </div>
                                                    <div class="col col-md-4">
                                                        <button type="submit" class="btn btn-primary ml-2">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row"><b>Notes</b></div>
                                        <div class="form-group row">
                                            <div class="col">
                                                <div id="notes-txtArea"></div>
                                            </div>
                                        </div>
                                        @csrf
                                        <div class="form-group row">&nbsp;</div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:90px">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
