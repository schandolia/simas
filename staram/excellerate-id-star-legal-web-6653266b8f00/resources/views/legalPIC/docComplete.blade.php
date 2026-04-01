@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-star icon-gradient bg-malibu-beach"></i>
                </div>
                <div>Completed Documents</div>
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
                                <th>The Parties</th>
                                <th>Purpose Agreement</th>
                                <th>Assigned From</th>
                                <th>Status</th>
                                <th class="app-stat">Approved by CEO</th>
                                <th class="app-stat">Approved by CFO</th>
                                <th class="app-stat">Approved by BU Head</th>
                                <th class="app-stat">Approved by Legal Head</th>
                                <th>Notes</th>
                                <th>Version</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" id="acc_token" name="acc_token" value="LEGAL">
        <input type="hidden" id="data-url" name="data-url" value="{{route('getCompletedDocs')}}">
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:90px">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Request Finalization Submission-->
<div class="modal fade" id="requestSubmissionModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col">
                    <h2 class="modal-title" id="titlesub-txt"></h2>
                    <strong>Form Agreement Request (F2)</strong>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form onkeydown="return event.key!='Enter';" id="frmRequestSubmission" name="frmRequestSubmission" class="form-horizontal" action="{{route('processRequest')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#step1" role="tab" aria-selected="true">
                            Description
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#step2" role="tab" aria-selected="false">
                            Summary
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="step1" role="tabpanel">
                        <div class="form-group row">
                            <div class="col-md-3 col-form-label">
                                <strong id="req-submission-docType"></strong>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" id="req-submission-docId" type="text" name="req-submission-docId">
                            <label class="col-md-3 col-form-label" for="input-agreement-number">Agreement Number</label>
                            <div class="col">
                                <input class="form-control" id="input-agreement-number" type="text" name="input-agreement-number" placeholder="Agreement Number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-parties">Parties</label>
                            <div class="col">
                                <input class="form-control" id="input-parties" type="text" name="input-parties" placeholder="Parties">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-objective">Transaction Objectives</label>
                            <div class="col">
                                <input class="form-control" id="input-objective" type="text" name="input-objective" placeholder="Transaction Objectives">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-period">Time Period</label>
                            <div class="col">
                                    <input class="form-control" id="input-period" type="text" name="input-period" placeholder="Time Period">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-nominal">Nominal Transaction</label>
                            <div class="col">
                                    <input class="form-control" id="input-nominal" type="text" name="input-nominal" placeholder="Nominal Transaction">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-terms">Parties Terms and Conditions</label>
                            <div class="col">
                                    <input class="form-control" id="input-terms" type="text" name="input-terms" placeholder="Parties Terms and Conditions">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-other">Other</label>
                            <div class="col">
                                    <input class="form-control" id="input-other" type="text" name="input-other" placeholder="Other">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-version">Version</label>
                            <div class="col">
                                <input class="form-control" id="input-version" type="text" name="input-version" placeholder="Version">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="input-attachment">Attachment</label>
                            <div class="col">
                                <input id="input-attachment" type="file" name="input-attachment">
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="step2" role="tabpanel">
                        <div class="form-group row">
                            <div class="col-md-3 col-form-label">
                                <strong id="req-submission-docType2"></strong>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Date</div>
                            <div class="col" id="txt-date"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Agreement Number</div>
                            <div class="col" id="txt-agreement-number"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Parties</div>
                            <div class="col" id="txt-parties"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Transaction Objectives</div>
                            <div class="col" id="txt-objective"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Time Period</div>
                            <div class="col" id="txt-period"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Nominal Transaction</div>
                            <div class="col" id="txt-nominal"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Parties Terms and Conditions</div>
                            <div class="col" id="txt-terms"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Other</div>
                            <div class="col" id="txt-other"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">Attachment</div>
                            <div class="col" id="txt-attachment"></div>
                        </div>
                    </div>
                </div>
            </div>
            @csrf
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="back-btn" style="width:90px">Close</button>
                <button type="button" class="btn btn-primary" id="next-request-btn" style="width:90px">Next</button>
                <button type="submit" class="btn btn-primary" id="submit-request-btn" style="width:90px;display:none">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
