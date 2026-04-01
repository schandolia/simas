@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-tag icon-gradient bg-deep-blue"></i>
                </div>
                <div>Requested Documents</div>
            </div>
            <!-- /.card-->
            <div class="row pt-3">
                <div class="col">
                    <table id="request-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Requested</th>
                                <th>Document Type</th>
                                <th>The Parties</th>
                                <th>Purpose Agreement</th>
                                <th>Status</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" id="acc_token" name="acc_token" value="USER">
    </main>
@endsection
@section ('modal')
<!-- Modal Add Folder-->
<div class="modal fade" id="newRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-title">
                    <div class="col">
                        <h2 class="modal-title"><b>Request Docs</b></h2>
                        <strong>Form Agreement Request (F1)</strong>
                    </div>
                    <div class="col text-right sub-step" id="submit-step">
                            Select Document
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form onkeydown="return event.key!='Enter';" id="frmSubmitRequest" name="frmSubmitRequest" class="form-horizontal" action="{{route('submitRequest')}}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#step1" role="tab" aria-selected="true">
                            Step 1
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#step2" role="tab" aria-selected="false">
                            Step 2
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#step3" role="tab" aria-selected="false">
                            Step 3
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#step4" role="tab" aria-selected="false">
                            Step 4
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="step1" role="tabpanel">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="document-type">Select Document Type</label>
                            <div class="col">
                                <select class="form-control" id="document-type" name="document-type">
                                    <option value="0">Document Type</option>
                                @foreach ($doctypes as $doctype)
                                    <option value="{{$doctype->id}}">{{$doctype->type}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="purpose">Purpose/Nature of Agreement</label>
                            <div class="col">
                                <input class="form-control" id="purpose" type="text" name="purpose" placeholder="Purpose of this agreement">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="parties">The Parties</label>
                            <div class="col">
                                <input class="form-control" id="parties" type="text" name="parties" placeholder="The parties involved">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Description/Notes</label>
                            <div class="col">
                                <div id="description"></div>
                            </div>
                        </div>
                        <div class="form-group row"></div>
                        <div class="form-group row"></div>
                        <div class="form-group row"></div>
                    </div>
                    <div class="tab-pane" id="step2" role="tabpanel">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="commercial-terms">Transaction/Commercial Terms</label>
                            <div class="col">
                                <input class="form-control" id="commercial-terms" type="text" name="commercial-terms" placeholder="Transaction/Commercial Terms">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="transaction-value">Value of Transaction</label>
                            <div class="col">
                                <input class="form-control" id="transaction-value" type="text" name="transaction-value" placeholder="Value of Transaction">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="toleration-late-payment">Toleration of Late Payment</label>
                            <div class="col">
                                <input class="form-control" id="toleration-late-payment" type="text" name="toleration-late-payment" placeholder="Toleration of Late Payment">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="condition-precedent">Condition Precedent</label>
                            <div class="col">
                                <input class="form-control" id="condition-precedent" type="text" name="condition-precedent" placeholder="Condition Precedent">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="termination-terms">Termination Terms</label>
                            <div class="col">
                                <input class="form-control" id="termination-terms" type="text" name="termination-terms" placeholder="Termination Terms">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="payment-terms">Term of Payment</label>
                            <div class="col">
                                <input class="form-control" id="payment-terms" type="text" name="payment-terms" placeholder="Term of Payment">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="delay-terms">Term of Delay and Percentage Penalty</label>
                            <div class="col">
                                <input class="form-control" id="delay-terms" type="text" name="delay-terms" placeholder="Term of Delay and Percentage Penalty">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="guarantee-security">Guarantee/Security</label>
                            <div class="col">
                                <input class="form-control" id="guarantee-security" type="text" name="guarantee-security" placeholder="Guarantee/Security">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="agreement-terms">Term of Agreement</label>
                            <div class="col">
                                <input class="form-control" id="agreement-terms" type="text" name="agreement-terms" placeholder="Term of Agreement">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="step3" role="tabpanel">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="akta">Akta (Corporate Deed/Document)</label>
                            <div class="col">
                                <input id="akta" type="file" name="akta">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="npwp">NPWP/PKP</label>
                            <div class="col">
                                <input id="npwp" type="file" name="npwp">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="tdp">TDP/SIUP/SITU/IUT (Permit & Lisence)</label>
                            <div class="col">
                                <input id="tdp" type="file" name="tdp">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="ktp">KTP Pengurus (ID of Management)</label>
                            <div class="col">
                                <input id="ktp" type="file" name="ktp">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="proposal">Proposal/Company Profile</label>
                            <div class="col">
                                <input id="proposal" type="file" name="proposal">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="others-attach">Others</label>
                            <div class="col">
                                <input id="others-attach" type="file" name="others-attach">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="step4" role="tabpanel">
                        <div class="form-group row">
                            <div class="col md-3">
                                Document Type
                            </div>
                            <div class="col" id="doc-type-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Purpose/Nature of Agreement
                            </div>
                            <div class="col" id="purpose-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                The Parties
                            </div>
                            <div class="col" id="parties-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Description/Notes
                            </div>
                            <div class="col" id="description-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Transaction/Commercial Terms
                            </div>
                            <div class="col" id="commercial-terms-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Value of Transaction
                            </div>
                            <div class="col" id="transaction-value-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Toleration of Late Payment
                            </div>
                            <div class="col" id="toleration-late-payment-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Condition Precedent
                            </div>
                            <div class="col" id="condition-precedent-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Termination Terms
                            </div>
                            <div class="col" id="termination-terms-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Term of Payment
                            </div>
                            <div class="col" id="payment-terms-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Term of Delay and Percentage Penalty
                            </div>
                            <div class="col" id="delay-terms-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Guarantee/Security
                            </div>
                            <div class="col" id="guarantee-security-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                Term of Agreement
                            </div>
                            <div class="col" id="agreement-terms-txt"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col md-3">
                                File Attachment
                            </div>
                            <div class="col">
                                <ul>
                                    <li id="akta-txt">Akta (Corporate Deed/Document)</li>
                                    <li id="npwp-txt">NPWP/PKP</li>
                                    <li id="tdp-txt">TDP/SIUP/SITU/IUT (Permit & Lisence)</li>
                                    <li id="ktp-txt">KTP Pengurus (ID of Management)</li>
                                    <li id="proposal-txt">Proposal/Company Profile</li>
                                    <li id="others-attach-txt">Others</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card col md">
                                <div class="card-body font-xs">
                                    <div class="row font-lg pb-2"><b>Service Level Agreement</b></div>
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
                        <div class="form-group row">
                            <div class="col pl-4">
                                <input class="form-check-input" id="confirm-chk" type="checkbox" value="approve" name="confirm-chk">
                                <label class="form-check-label" for="confirm-chk">I agree with the terms and Service Level Agreement</label>
                            </div>
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
<!-- Modal Add Folder-->
<div class="modal fade" id="requestDetailModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <a class="nav-link active" data-toggle="tab" href="#req-detail" role="tab" aria-selected="true">
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
@endsection
