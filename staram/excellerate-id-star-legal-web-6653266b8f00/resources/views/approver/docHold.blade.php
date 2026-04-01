@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="fa fa-hourglass-1 icon-gradient bg-deep-blue"></i>
                </div>
                <div>Rejected Documents</div>
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
                                <th>Hold By</th>
                                <th>Assigned To</th>
                                <th>Action</th>
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
        <input type="hidden" id="data-url" name="data-url" value="{{route('getHoldDocs')}}">
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:90px">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
