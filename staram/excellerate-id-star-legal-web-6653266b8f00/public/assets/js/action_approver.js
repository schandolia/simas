window.$ = window.jQuery
const COL_DATE_WIDTH = 70;
const COL_DOCTYPE_WIDTH = 130;
const COL_PARTIES_WIDTH = 200;
const COL_STATUS_WIDTH = 80;
const ROLE_HEAD_LEGAL = 4;
var ATTACHMENT_TYPE = {
    'KIND_AKTA': 'Akta (Corporate Deed/Document)',
    'KIND_NPWP': 'NPWP/PKP',
    'KIND_TDP': 'TDP/SIUP/SITU/IUT (Permit & Lisence)',
    'KIND_KTP': 'KTP Pengurus (ID of Management)',
    'KIND_PROPOSAL': 'Proposal/Company Profile',
    'KIND_OTHER': 'Others'
};

var origin_url = "";
let pageMenu = "";
var docTypeList = null;
var role = $('#ACC').val();

function getUrl() {
    let scripts = document.getElementsByTagName('script');
    var urls = (scripts[scripts.length - 1].src).split('/');
    var url = "";
    for (var i = 0; i < urls.length - 3; ++i)
        url += urls[i] + '/';
    return url.substr(0, url.length - 1);
}

$(document).ready(function() {
    origin_url = getUrl();
    pageMenu = window.location.href.substr(origin_url.length + 1);
    $('.sidebar-minimizer.brand-minimizer').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $('body').toggleClass('brand-minimized');
        $('body').toggleClass('sidebar-minimized');
    });
    $('.navbar-toggler').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var toggle = event.currentTarget.dataset ? event.currentTarget.dataset.toggle : $(event.currentTarget).data('toggle');
        if (document.body.classList.contains(toggle))
            document.body.classList.remove(toggle);
        else
            document.body.classList.add(toggle);
    });

    if (pageMenu.indexOf('/') > 0)
        pageMenu = pageMenu.substr(0, pageMenu.indexOf('/'));
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $.ajax({
        type: 'GET',
        url: origin_url + '/api/getDocType',
        data: { get_param: 'value' },
        success: function(data) {
            docTypeList = data;
            switch (pageMenu) {
                case 'dashboard':
                    dashboardPage.drawPage();
                    dashboardPage.assignAction();
                    break;
                case 'request':
                    requestPage.drawPage();
                    requestPage.assignAction();
                    break;
                case 'review':
                    reviewPage.drawPage();
                    reviewPage.assignAction();
                    break;
                case 'tobeApproved':
                case 'approved':
                case 'hold':
                case 'processed':
                case 'complete':
                    genericPage.drawPage();
                    genericPage.assignAction();
                    break;
                case 'share':
                    sharePage.drawPage();
                    sharePage.assignAction();
                    break;
                case 'profile':
                    profilePage.drawPage();
                    profilePage.assignAction();
                    break;
                case 'userSetting':
                    userSettingPage.drawPage();
                    userSettingPage.assignAction();
            }
        }
    });
});

var dashboardPage = {
    drawPage: function() {
        $.ajax({
            url: origin_url + '/api/getTotalRequestInYear',
            type: 'GET',
            success: function(data) {
                var currentMonth = (new Date()).getMonth() + 1;
                var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                MONTHS.splice(currentMonth, 12 - currentMonth);
                data.detailsRequested.splice(currentMonth, 12 - currentMonth);
                data.detailsCompleted.splice(currentMonth, 12 - currentMonth);

                $('#total-req-txt').text(data.totalRequested);
                $('#total-complete-txt').text(data.totalCompleted);
                var barChartData = {
                    labels: MONTHS,
                    datasets: [{
                            label: 'Requested',
                            backgroundColor: '#63c2de',
                            borderColor: 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 1,
                            data: data.detailsRequested
                        },
                        {
                            label: 'Completed',
                            backgroundColor: '#4dbd74',
                            borderColor: 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 1,
                            data: data.detailsCompleted
                        }
                    ]
                };
                var ctx = document.getElementById('canvas');
                window.myBar = new Chart(ctx, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    stepSize: 10,
                                    beginAtZero: true,
                                    callback: (value, index, values) => {
                                        return value;
                                    },
                                }
                            }]
                        },
                        title: { display: false }
                    }
                });
            }
        });
    },
    assignAction: function() {

    }
}
var requestPage = {
    requestDataTable: null,
    assigneeMagicSuggest: null,
    assignNotesTxtArea: null,
    submissionNotesTxtArea: null,
    drawPage: function() {
        let rowsDefinition = [
            createColumn('SUBMITTED_DATE', true),
            createColumn('DOCUMENT_TYPE', true),
            createColumn('SUBMITTER', true),
            createColumn('PARTIES', true),
            createColumn('PURPOSE', false),
            createColumn('REQUEST_STATUS', true),
            createColumn('OWNER', true),
            createColumn('VERIFY_STATUS', true),
            createColumn('CEO_APPROVED', false),
            createColumn('CFO_APPROVED', false),
            createColumn('BU_APPROVED', false),
            createColumn('LEGAL_APPROVED', false),
            createColumn('STATUS', true),
            createColumn('DESCRIPTION', false)
        ];
        let buttonDefinition = [{
                text: '<i class="fa fa-th-large"></i>',
                attr: {
                    id: "dt-row-columnSelect",
                    "data-toggle": "dropdown",
                    "aria-haspopup": "true",
                    "aria-expanded": "false"
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Customize Columns'
            },
            {
                text: 'Status',
                attr: {
                    id: "dt-vis-status",
                    disabled: true
                },
                className: 'btn btn-square btn-primary',
                action: function(e, dt, node, config) {
                    $('#dt-vis-status').attr('disabled', true);
                    $('#dt-vis-approver').attr('disabled', false);
                    var dt = requestPage.requestDataTable;
                    var chkBoxes = $('#colsel-dropdown').children().toArray();
                    [8, 9, 10, 11].forEach(function(val) {
                        dt.column(val).visible(false);
                        $(chkBoxes[val]).hide();
                    });
                    [3, 4, 13].forEach(function(val) {
                        dt.column(val).visible(true);
                        $(chkBoxes[val]).show();
                        chkBoxes[val].children[0].checked = true
                    });
                }
            },
            {
                text: 'Approver',
                attr: {
                    id: "dt-vis-approver",
                    disabled: false
                },
                className: 'btn btn-square btn-primary',
                action: function(e, dt, node, config) {
                    $('#dt-vis-status').attr('disabled', false);
                    $('#dt-vis-approver').attr('disabled', true);
                    var dt = requestPage.requestDataTable;
                    var chkBoxes = $('#colsel-dropdown').children().toArray();
                    [8, 9, 10, 11].forEach(function(val) {
                        dt.column(val).visible(true);
                        $(chkBoxes[val]).show();
                        chkBoxes[val].children[0].checked = true
                    });
                    [3, 4, 13].forEach(function(val) {
                        dt.column(val).visible(false);
                        $(chkBoxes[val]).hide();
                    });
                }
            }
        ];
        var colSelArr = [{ lbl: 'Date Requested', def: true },
            { lbl: 'Document Type', def: true },
            { lbl: 'Proposed By', def: true },
            { lbl: 'The Parties', def: true },
            { lbl: 'Purpose Agreement', def: true },
            { lbl: 'Request Status', def: true },
            { lbl: 'Assigned To', def: true },
            { lbl: 'Verify Status', def: true },
            { lbl: 'Approved by CEO', def: true },
            { lbl: 'Approved by CFO', def: true },
            { lbl: 'Approved by BU Head', def: true },
            { lbl: 'Approved by Legal Head', def: true },
            { lbl: 'Document Status', def: true },
            { lbl: 'Description', def: false }
        ];

        if ($('#member-input').length > 0) {
            let magicSuggestOpt = Utils.generateUserSuggestOption(origin_url + '/api/getLegalPIC', $('input[name="_token"]').prop('value'));
            magicSuggestOpt['maxSelection'] = 1;
            requestPage.assigneeMagicSuggest = $('#member-input').magicSuggest(magicSuggestOpt);
            $('#ms-trigger').addClass('text-center');
        }
        requestPage.assignNotesTxtArea = new Quill('#notes-txtArea', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Notes..."
        });
        requestPage.submissionNotesTxtArea = new Quill('#subnotes-txtArea', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Notes..."
        });
        requestPage.requestDataTable = renderDataTable('#request-table', origin_url + '/api/getRequestDocs',
            rowsDefinition, buttonDefinition, null);
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + createColumnSelBtn(colSelArr) + '</div>').insertAfter("#dt-row-columnSelect");

        Utils.renderSlaTable('#sla-container', docTypeList);
        var chkBoxes = $('#colsel-dropdown').children().toArray();
        [8, 9, 10, 11].forEach(function(val) { $(chkBoxes[val]).hide(); });
    },
    assignAction: function() {
        $('#approveRequest-form').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });

            //submit a POST request with the form data
            if ($('#action-opt-approve').is(':checked'))
                formData['action'] = 'approve';
            else
                formData['action'] = 'hold';

            formData['notes'] = requestPage.assignNotesTxtArea.container.firstChild.innerHTML;

            if ((formData['action'] != 'approve') || (requestPage.assigneeMagicSuggest == null))
                formData['assignee'] = null;
            else {
                if (requestPage.assigneeMagicSuggest.getSelection().length != 0)
                    formData['assignee'] = requestPage.assigneeMagicSuggest.getSelection()[0].id;
                else
                    formData['assignee'] = null;
            }
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                if (response.fail != null && response.fail != 'undefined')
                    toastr.error(response.fail, "Error on Approve Request");
                else {
                    toastr.success('You have approve request ID:' + formData['request-id'], "Approve Request success");
                    $('#requestDetailModal').modal('hide');
                    requestPage.requestDataTable.ajax.reload();
                }
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("Error on Approving Request ID:" + formData['request-id'], "Error on Approve Request");
            });
            return false;
        });

        $('#colsel-dropdown').off('keydown', '#colsel-dropdown');

        $('.dropdown-menu.keep-open').on('click', function(e) {
            e.stopPropagation();
        });

        $('.colsel-chkbox').change(function() {
            if (this.checked)
                requestPage.requestDataTable.column($(this).val()).visible(true);
            else
                requestPage.requestDataTable.column($(this).val()).visible(false);
        });

        $('#request-table tbody').on('click', 'tr', function() {
            var reqID = requestPage.requestDataTable.row(this).data().id;
            $.ajax({
                type: 'GET',
                url: origin_url + '/api/getRequestDetails/' + reqID + '/mark',
                data: { get_param: 'value' },
                success: function(data) {
                    Utils.renderRequestDetail(data);
                    $('#requestDetailModal').modal('show');
                }
            });
        });

        $('#request-table tbody').on('click', 'button', function() {
            var reqID = requestPage.requestDataTable.row($(this).parents('tr')).data();
            $('#req-submission-docType').text(reqID.doc_type);
            if (reqID.approval_type == 'REQUEST') {
                $('#titlesub-txt').empty().append('<b>Request Docs</b>');
            } else {
                $('#titlesub-txt').empty().append('<b>Request Review Docs</b>');
            }
            reqID = reqID.id;

            $.ajax({
                type: 'GET',
                url: origin_url + '/api/getLatestSubmission/' + reqID,
                data: { get_param: 'value' },
                success: function(data) {
                    $('#sub-request-id').val(data.req_id);
                    $('#sub-date').text(Utils.getDateStr(data.date, false));
                    $('#sub-agreement-number').text(data.agreement_number);
                    $('#sub-parties').text(data.parties);
                    $('#sub-objective').text(data.transaction_objective);
                    $('#sub-period').text(data.time_period);
                    $('#sub-nominal').text(data.nominal_transaction);
                    $('#sub-terms').text(data.terms)
                    $('#sub-other').text(data.other);
                    if (data.attachment_name == null || data.attachment_name == 'undefined')
                        $('#sub-attachment').empty();
                    else
                        $('#sub-attachment').empty().append('<a href="' + origin_url + '/getSubmissionAttachment/' + data.req_id + '">' + data.attachment_name + '</a>');
                    $('#requestSubmissionModal').modal('show');
                }
            });
            return false;
        });

        $('#approveRequestSub-form').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });

            //submit a POST request with the form data
            if ($('#action-sub-approve').is(':checked'))
                formData['action'] = 'approve';
            else
                formData['action'] = 'reject';

            formData['notes'] = requestPage.submissionNotesTxtArea.container.firstChild.innerHTML;

            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                if (response.fail != null && response.fail != 'undefined')
                    toastr.error(response.fail, "Error on Approve Request");
                else {
                    if (formData['action'] == 'approve')
                        toastr.success('You have approve request ID:' + formData['sub-request-id'], "Approve Request success");
                    else
                        toastr.success('You have reject request ID:' + formData['sub-request-id'], "Reject Request success");
                    $('#requestSubmissionModal').modal('hide');
                    requestPage.requestDataTable.ajax.reload();
                }
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("Error on Approving Request ID:" + formData['request-id'], "Error on Approve Request");
            });
            return false;
        });
    }
}

var reviewPage = {
    reviewDataTable: null,
    userMagicSuggest: null,
    assignNotesTxtArea: null,
    submissionNotesTxtArea: null,
    drawPage: function() {
        let rowsDefinition = [
            createColumn('SUBMITTED_DATE', true),
            createColumn('DOCUMENT_TYPE', true),
            createColumn('SUBMITTER', true),
            createColumn('PARTIES', true),
            createColumn('PURPOSE', false),
            createColumn('REQUEST_STATUS', true),
            createColumn('OWNER', true),
            createColumn('VERIFY_STATUS', true),
            createColumn('CEO_APPROVED', false),
            createColumn('CFO_APPROVED', false),
            createColumn('BU_APPROVED', false),
            createColumn('LEGAL_APPROVED', false),
            createColumn('STATUS', true),
            createColumn('DESCRIPTION', false)
        ];
        let buttonDefinition = [{
                text: '<i class="fa fa-th-large"></i>',
                attr: {
                    id: "dt-row-columnSelect",
                    "data-toggle": "dropdown",
                    "aria-haspopup": "true",
                    "aria-expanded": "false"
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Customize Columns'
            },
            {
                text: 'Status',
                attr: {
                    id: "dt-vis-status",
                    disabled: true
                },
                className: 'btn btn-square btn-primary',
                action: function(e, dt, node, config) {
                    $('#dt-vis-status').attr('disabled', true);
                    $('#dt-vis-approver').attr('disabled', false);
                    var dt = reviewPage.reviewDataTable;
                    var chkBoxes = $('#colsel-dropdown').children().toArray();
                    [8, 9, 10, 11].forEach(function(val) {
                        dt.column(val).visible(false);
                        $(chkBoxes[val]).hide();
                    });
                    [3, 4, 13].forEach(function(val) {
                        dt.column(val).visible(true);
                        $(chkBoxes[val]).show();
                        chkBoxes[val].children[0].checked = true
                    });
                }
            },
            {
                text: 'Approver',
                attr: {
                    id: "dt-vis-approver",
                    disabled: false
                },
                className: 'btn btn-square btn-primary',
                action: function(e, dt, node, config) {
                    $('#dt-vis-status').attr('disabled', false);
                    $('#dt-vis-approver').attr('disabled', true);
                    var dt = reviewPage.reviewDataTable;
                    var chkBoxes = $('#colsel-dropdown').children().toArray();
                    [8, 9, 10, 11].forEach(function(val) {
                        dt.column(val).visible(true);
                        $(chkBoxes[val]).show();
                        chkBoxes[val].children[0].checked = true
                    });
                    [3, 4, 13].forEach(function(val) {
                        dt.column(val).visible(false);
                        $(chkBoxes[val]).hide();
                    });
                }
            }
        ];
        var colSelArr = [{ lbl: 'Date Requested', def: true },
            { lbl: 'Document Type', def: true },
            { lbl: 'Proposed By', def: true },
            { lbl: 'The Parties', def: true },
            { lbl: 'Purpose Agreement', def: true },
            { lbl: 'Request Status', def: true },
            { lbl: 'Assigned To', def: true },
            { lbl: 'Verify Status', def: true },
            { lbl: 'Approved by CEO', def: true },
            { lbl: 'Approved by CFO', def: true },
            { lbl: 'Approved by BU Head', def: true },
            { lbl: 'Approved by Legal Head', def: true },
            { lbl: 'Document Status', def: true },
            { lbl: 'Description', def: false }
        ];

        reviewPage.reviewDataTable = renderDataTable('#review-table', origin_url + '/api/getReviewDocs',
            rowsDefinition, buttonDefinition, null);
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + createColumnSelBtn(colSelArr) + '</div>').insertAfter("#dt-row-columnSelect");
        if ($('#member-input').length > 0) {
            let magicSuggestOpt = Utils.generateUserSuggestOption(origin_url + '/api/getLegalPIC', $('input[name="_token"]').prop('value'));
            magicSuggestOpt['maxSelection'] = 1;
            reviewPage.userMagicSuggest = $('#member-input').magicSuggest(magicSuggestOpt);
            $('#ms-trigger').addClass('text-center');
        }

        reviewPage.assignNotesTxtArea = new Quill('#notes-txtArea', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Notes..."
        });
        reviewPage.submissionNotesTxtArea = new Quill('#subnotes-txtArea', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Notes..."
        });
        Utils.renderSlaTable('#sla-container', docTypeList);
    },
    assignAction: function() {
        $('#approveRequest-form').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });

            //submit a POST request with the form data
            if ($('#action-opt-approve').is(':checked'))
                formData['action'] = 'approve';
            else
                formData['action'] = 'hold';

            formData['notes'] = reviewPage.assignNotesTxtArea.container.firstChild.innerHTML;

            if ((formData['action'] != 'approve') || (reviewPage.userMagicSuggest == null))
                formData['assignee'] = null;
            else {
                if (reviewPage.userMagicSuggest.getSelection().length != 0)
                    formData['assignee'] = reviewPage.userMagicSuggest.getSelection()[0].id;
                else
                    formData['assignee'] = null;
            }
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                if (response.fail != null && response.fail != 'undefined')
                    toastr.error(response.fail, "Error on Approve Request");
                else {
                    toastr.success('You have approve request ID:' + formData['request-id'], "Approve Request success");
                    $('#reviewDetailModal').modal('hide');
                    reviewPage.reviewDataTable.ajax.reload();
                }
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("Error on Approving Request ID:" + formData['request-id'], "Error on Approve Request");
            });
            return false;
        });

        $('#colsel-dropdown').off('keydown', '#colsel-dropdown');

        $('.dropdown-menu.keep-open').on('click', function(e) {
            e.stopPropagation();
        });

        $('.colsel-chkbox').change(function() {
            if (this.checked)
                reviewPage.reviewDataTable.column($(this).val()).visible(true);
            else
                reviewPage.reviewDataTable.column($(this).val()).visible(false);
        });
        $('#review-table tbody').on('click', 'tr', function() {
            var reqID = reviewPage.reviewDataTable.row(this).data().id;
            $.ajax({
                type: 'GET',
                url: origin_url + '/api/getRequestDetails/' + reqID + '/mark',
                data: { get_param: 'value' },
                success: function(data) {
                    Utils.renderRequestDetail(data);
                    $('#reviewDetailModal').modal('show');
                }
            });
        });
        $('#review-table tbody').on('click', 'button', function() {
            var reqID = reviewPage.reviewDataTable.row($(this).parents('tr')).data();
            $('#req-submission-docType').text(reqID.doc_type);
            if (reqID.approval_type == 'REQUEST') {
                $('#titlesub-txt').empty().append('<b>Request Docs</b>');
            } else {
                $('#titlesub-txt').empty().append('<b>Request Review Docs</b>');
            }
            reqID = reqID.id;
            $.ajax({
                type: 'GET',
                url: origin_url + '/api/getLatestSubmission/' + reqID,
                data: { get_param: 'value' },
                success: function(data) {
                    $('#sub-request-id').val(data.req_id);
                    $('#sub-date').text(Utils.getDateStr(data.date, false));
                    $('#sub-agreement-number').text(data.agreement_number);
                    $('#sub-parties').text(data.parties);
                    $('#sub-objective').text(data.transaction_objective);
                    $('#sub-period').text(data.time_period);
                    $('#sub-nominal').text(data.nominal_transaction);
                    $('#sub-terms').text(data.terms)
                    $('#sub-other').text(data.other);
                    $('#sub-attachment').empty().append('<a href="' + origin_url + '/getSubmissionAttachment/' + data.req_id + '">' + data.attachment_name + '</a>');
                    $('#requestSubmissionModal').modal('show');
                }
            });
            return false;
        });

        $('#approveRequestSub-form').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });

            //submit a POST request with the form data
            if ($('#action-sub-approve').is(':checked'))
                formData['action'] = 'approve';
            else
                formData['action'] = 'reject';

            formData['notes'] = reviewPage.submissionNotesTxtArea.container.firstChild.innerHTML;

            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                if (response.fail != null && response.fail != 'undefined')
                    toastr.error(response.fail, "Error on Approve Request");
                else {
                    if (formData['action'] == 'approve')
                        toastr.success('You have approve request ID:' + formData['sub-request-id'], "Approve Request success");
                    else
                        toastr.success('You have reject request ID:' + formData['sub-request-id'], "Reject Request success");
                    $('#requestSubmissionModal').modal('hide');
                    reviewPage.reviewDataTable.ajax.reload();
                }
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("Error on Approving Request ID:" + formData['request-id'], "Error on Approve Request");
            });
            return false;
        });
    },
}

var genericPage = {
    requestDataTable: null,
    assigneeMagicSuggest: null,
    assignNotesTxtArea: null,
    submissionNotesTxtArea: null,
    drawPage: function() {
        let rowsDefinition = [
            createColumn('SUBMITTED_DATE', true),
            createColumn('ACTIVITIES', true),
            createColumn('DOCUMENT_TYPE', true),
            createColumn('SUBMITTER', true),
            createColumn('PARTIES', true),
            createColumn('PURPOSE', true),
            createColumn('REQUEST_STATUS', true),
            createColumn('OWNER', true),
            createColumn('VERIFY_STATUS', true),
            createColumn('CEO_APPROVED', false),
            createColumn('CFO_APPROVED', false),
            createColumn('BU_APPROVED', false),
            createColumn('LEGAL_APPROVED', false),
            createColumn('STATUS', true),
            createColumn('DESCRIPTION', true)
        ];

        let buttonDefinition = [{
                text: '<i class="fa fa-th-large"></i>',
                attr: {
                    id: "dt-row-columnSelect",
                    "data-toggle": "dropdown",
                    "aria-haspopup": "true",
                    "aria-expanded": "false"
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Customize Columns'
            },
            {
                text: 'Status',
                attr: {
                    id: "dt-vis-status",
                    disabled: true
                },
                className: 'btn btn-square btn-primary',
                action: function(e, dt, node, config) {
                    $('#dt-vis-status').attr('disabled', true);
                    $('#dt-vis-approver').attr('disabled', false);
                    var dt = genericPage.requestDataTable;
                    var chkBoxes = $('#colsel-dropdown').children().toArray();
                    if (pageMenu == 'complete') {
                        arr1 = [8, 9, 10, 11];
                        arr2 = [4, 5, 13];
                    } else {
                        arr1 = [9, 10, 11, 12];
                        arr2 = [4, 5, 14];
                    }
                    arr1.forEach(function(val) {
                        dt.column(val).visible(false);
                        $(chkBoxes[val]).hide();
                    });

                    arr2.forEach(function(val) {
                        dt.column(val).visible(true);

                        $(chkBoxes[val]).show();
                        chkBoxes[val].children[0].checked = true
                    });
                }
            },
            {
                text: 'Approver',
                attr: {
                    id: "dt-vis-approver",
                    disabled: false
                },
                className: 'btn btn-square btn-primary',
                action: function(e, dt, node, config) {
                    $('#dt-vis-status').attr('disabled', false);
                    $('#dt-vis-approver').attr('disabled', true);
                    var dt = genericPage.requestDataTable;
                    var chkBoxes = $('#colsel-dropdown').children().toArray();
                    var arr1 = null;
                    var arr2 = null;
                    if (pageMenu == 'complete') {
                        arr1 = [8, 9, 10, 11];
                        arr2 = [4, 5, 13];
                    } else {
                        arr1 = [9, 10, 11, 12];
                        arr2 = [4, 5, 14];
                    }
                    arr1.forEach(function(val) {
                        dt.column(val).visible(true);
                        $(chkBoxes[val]).show();
                        chkBoxes[val].children[0].checked = true
                    });
                    arr2.forEach(function(val) {
                        dt.column(val).visible(false);
                        $(chkBoxes[val]).hide();
                    });
                }
            }
        ];
        var colSelArr = [{ lbl: 'Date Requested', def: true },
            { lbl: 'Activities', def: true },
            { lbl: 'Document Type', def: true },
            { lbl: 'Proposed By', def: true },
            { lbl: 'The Parties', def: true },
            { lbl: 'Purpose Agreement', def: true },
            { lbl: 'Request Status', def: true },
            { lbl: 'Assigned To', def: true },
            { lbl: 'Verify Status', def: true },
            { lbl: 'Approved by CEO', def: false },
            { lbl: 'Approved by CFO', def: false },
            { lbl: 'Approved by BU Head', def: false },
            { lbl: 'Approved by Legal Head', def: false },
            { lbl: 'Document Status', def: true },
            { lbl: 'Description', def: false }
        ];

        if (pageMenu == 'hold') {
            rowsDefinition[6] = createColumn('HOLDER', true);
            colSelArr[6].lbl = 'Hold By';

        } else if (pageMenu == 'complete') {
            rowsDefinition[14].visible = false;
            rowsDefinition[13] = createColumn('COMPLETED_DATE', true);
            colSelArr[13].lbl = "Completed Date";
            rowsDefinition.splice(6, 1);
            colSelArr.splice(6, 1);
            rowsDefinition.push(createColumn('REVISION_VERSION', true));
            colSelArr.push({ lbl: 'Revision Version', def: true });
        }

        if (pageMenu != 'hold') {
            if ($('#member-input').length > 0) {
                let magicSuggestOpt = Utils.generateUserSuggestOption(origin_url + '/api/getLegalPIC', $('input[name="_token"]').prop('value'));
                magicSuggestOpt['maxSelection'] = 1;
                genericPage.assigneeMagicSuggest = $('#member-input').magicSuggest(magicSuggestOpt);
                $('#ms-trigger').addClass('text-center');
            }
            if ($('#notes-txtArea').length != 0) {
                genericPage.assignNotesTxtArea = new Quill('#notes-txtArea', {
                    modules: {
                        toolbar: toolbarOptions
                    },
                    theme: 'snow',
                    placeholder: "Notes..."
                });
            }
            if ($('#subnotes-txtArea').length != 0) {
                genericPage.submissionNotesTxtArea = new Quill('#subnotes-txtArea', {
                    modules: {
                        toolbar: toolbarOptions
                    },
                    theme: 'snow',
                    placeholder: "Notes..."
                });
            }
        }
        genericPage.requestDataTable = renderDataTable('#generic-table', $('#data-url').val(),
            rowsDefinition, buttonDefinition, null);
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + createColumnSelBtn(colSelArr) + '</div>').insertAfter("#dt-row-columnSelect");

        Utils.renderSlaTable('#sla-container', docTypeList);
        var chkBoxes = $('#colsel-dropdown').children().toArray();
        if (pageMenu == 'complete')
            [8, 9, 10, 11].forEach(function(val) { $(chkBoxes[val]).hide(); });
        else
            [9, 10, 11, 12].forEach(function(val) { $(chkBoxes[val]).hide(); });
    },
    assignAction: function() {
        $('#approveRequest-form').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });

            //submit a POST request with the form data
            if ($('#action-opt-approve').is(':checked'))
                formData['action'] = 'approve';
            else
                formData['action'] = 'hold';

            formData['notes'] = genericPage.assignNotesTxtArea.container.firstChild.innerHTML;

            if ((formData['action'] != 'approve') || (genericPage.assigneeMagicSuggest == null))
                formData['assignee'] = null;
            else {
                if (genericPage.assigneeMagicSuggest.getSelection().length != 0)
                    formData['assignee'] = genericPage.assigneeMagicSuggest.getSelection()[0].id;
                else
                    formData['assignee'] = null;
            }
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                if (response.fail != null && response.fail != 'undefined')
                    toastr.error(response.fail, "Error on Approve Request");
                else {
                    toastr.success('You have approve request ID:' + formData['request-id'], "Approve Request success");
                    $('#genericDocDetailModal').modal('hide');
                    genericPage.requestDataTable.ajax.reload();
                }
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("Error on Approving Request ID:" + formData['request-id'], "Error on Approve Request");
            });
            return false;
        });

        $('#colsel-dropdown').off('keydown', '#colsel-dropdown');

        $('.dropdown-menu.keep-open').on('click', function(e) {
            e.stopPropagation();
        });

        $('.colsel-chkbox').change(function() {
            if (this.checked)
                genericPage.requestDataTable.column($(this).val()).visible(true);
            else
                genericPage.requestDataTable.column($(this).val()).visible(false);
        });

        $('#generic-table tbody').on('click', 'button', function() {
            var data = genericPage.requestDataTable.row($(this).parents('tr')).data();
            if (!data.is_active) {
                var formData = {};
                formData['request-id'] = data.id;
                formData['_token'] = $('[name="_token"]').val();
                $.post(origin_url + '/activateRequest', formData, function(response) {
                    if (response.fail != null && response.fail != 'undefined')
                        toastr.error(response.fail, "Error on Activating Request");
                    else {
                        toastr.success('You have reactivate request ID:' + formData['request-id'], "Reactivate Request success");
                        genericPage.requestDataTable.ajax.reload();
                    }
                }).fail(function(response) {
                    toastr.error("Error on Reactivate Request ID:" + formData['request-id'], "Error on Reactivate Request");
                });
            } else {
                var reqID = genericPage.requestDataTable.row($(this).parents('tr')).data();
                $('#req-submission-docType').text(reqID.doc_type);
                if (reqID.approval_type == 'REQUEST') {
                    $('#titlesub-txt').empty().append('<b>Request Docs</b>');
                } else {
                    $('#titlesub-txt').empty().append('<b>Request Review Docs</b>');
                }
                reqID = reqID.id;
                $.ajax({
                    type: 'GET',
                    url: origin_url + '/api/getLatestSubmission/' + reqID,
                    data: { get_param: 'value' },
                    success: function(data) {
                        $('#sub-request-id').val(data.req_id);
                        $('#sub-date').text(Utils.getDateStr(data.date, false));
                        $('#sub-agreement-number').text(data.agreement_number);
                        $('#sub-parties').text(data.parties);
                        $('#sub-objective').text(data.transaction_objective);
                        $('#sub-period').text(data.time_period);
                        $('#sub-nominal').text(data.nominal_transaction);
                        $('#sub-terms').text(data.terms)
                        $('#sub-other').text(data.other);
                        $('#sub-attachment').empty().append('<a href="' + origin_url + '/getSubmissionAttachment/' + data.req_id + '">' + data.attachment_name + '</a>');
                        $('#requestSubmissionModal').modal('show');
                    }
                });
            }
            return false;
        });

        $('#generic-table tbody').on('click', 'tr', function() {
            var reqID = genericPage.requestDataTable.row(this).data().id;
            $.ajax({
                type: 'GET',
                url: origin_url + '/api/getRequestDetails/' + reqID + (pageMenu == 'complete' ? '/mark' : ''),
                data: { get_param: 'value' },
                success: function(data) {
                    Utils.renderRequestDetail(data);
                    $('#genericDocDetailModal').modal('show');
                }
            });
        });

        $('#approveRequestSub-form').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });

            //submit a POST request with the form data
            if ($('#action-sub-approve').is(':checked'))
                formData['action'] = 'approve';
            else
                formData['action'] = 'reject';

            formData['notes'] = genericPage.submissionNotesTxtArea.container.firstChild.innerHTML;

            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                if (response.fail != null && response.fail != 'undefined')
                    toastr.error(response.fail, "Error on Approve Request");
                else {
                    if (formData['action'] == 'approve')
                        toastr.success('You have approve request ID:' + formData['sub-request-id'], "Approve Request success");
                    else
                        toastr.success('You have reject request ID:' + formData['sub-request-id'], "Reject Request success");
                    $('#requestSubmissionModal').modal('hide');
                    genericPage.requestDataTable.ajax.reload();
                }
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("Error on Approving Request ID:" + formData['request-id'], "Error on Approve Request");
            });
            return false;
        });
    }
}

var sharePage = {
    newFolderDescriptionTxtArea: null,
    newFileDescriptionTxtArea: null,
    newFileRemarkTxtArea: null,
    shareDataTable: null,
    userMagicSuggest: null,
    drawPage: function() {
        let origin = origin_url;
        let shareDataTable = renderDataTable('#share-table', window.location.href.replace('/share', '/api/getSharedFoldersDocs'), [{
                targets: 0,
                data: null,
                className: 'select-checkbox',
                searchable: false,
                orderable: false,
                render: function(data, type, full, meta) {
                    return '<input type="checkbox" class="_check" name="check" value="' + data.doc_id + '"">';
                }
            },
            {
                data: "doc_name",
                width: COL_DOCTYPE_WIDTH + 'px',
                render: function(data, type, row) {
                    if (row.type == 'DOC') {
                        if (origin_url == 'http://127.0.0.1:8000')
                            return '<a class="preview-doc-btn" data-toggle="modal" href="#fileDetailModal" data-link="http://www.snee.com/xml/xslt/sample.doc">' +
                                '<i class="fa fa-file-text-o pr-2"></i>' + row.doc_name + '</a>';
                        else
                            return '<a class="preview-doc-btn" data-toggle="modal" href="#fileDetailModal" data-link="' + origin_url + '/' + row.attachment + '">' +
                                '<i class="fa fa-file-text-o pr-2"></i>' + row.doc_name + '</a>';
                    } else
                        return '<a href="' + origin + '/share/' + row.doc_id + '"><i class="fa fa-folder-o pr-2"></i>' + row.doc_name + '</a>';
                }
            },
            Utils.renderColDate("date_creation", "row.date_creation", true),
            { data: "company_name", width: COL_DOCTYPE_WIDTH + 'px' },
            {
                data: "doc_type",
                visible: false,
                render: function(data, type, row) {
                    let doctypes = docTypeList;
                    if (row.type == 'DOC') {
                        for (var i = 0; i < doctypes.length; ++i) {
                            if (doctypes[i].id == row.doc_type)
                                return doctypes[i].type;
                        };
                        return "undefined";
                    }
                    return "";
                }
            },
            { data: "agreement_number", width: COL_DOCTYPE_WIDTH + 'px' },
            Utils.renderColDate("agreement_date", "row.agreement_date", false),
            { data: "parties", visible: false },
            Utils.renderColDate("expire_date", "row.expire_date", false),
            { data: "description", visible: false },
            { data: "remark" }
        ], [{
                text: '<i class="fa fa-th-large"></i>',
                attr: {
                    id: "dt-row-columnSelect",
                    "data-toggle": "dropdown",
                    "aria-haspopup": "true",
                    "aria-expanded": "false"
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Customize Columns'
            },
            {
                text: '<i class="fa fa-folder-o"></i>',
                className: 'btn btn-square btn-primary',
                titleAttr: 'New Folder',
                action: function(e, dt, node, config) {
                    $('#addFolderModal').modal('show');
                }
            },
            {
                text: '<i class="fa fa-file-text-o"></i>',
                className: 'btn btn-square btn-primary',
                titleAttr: 'New Document',
                action: function(e, dt, node, config) {
                    $(document).off('drop', 'body');
                    $('#addFileModal').modal('show');
                }
            },
            {
                text: '<i class="fa fa-share-alt"></i>',
                attr: {
                    id: "dt-row-shareBtn",
                    disabled: true
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Share',
                action: function(e, dt, node, config) {
                    $('#shareModal').modal('show');
                }
            },
            {
                text: '<i class="fa fa-download"></i>',
                attr: {
                    id: "dt-row-downloadBtn",
                    disabled: true
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Download',
                action: function(e, dt, node, config) {
                    var docIds = [];
                    var formData = {};
                    sharePage.shareDataTable.rows({ selected: true }).data().toArray().forEach(element => {
                        docIds.push({ type: element.type, id: element.doc_id })
                    });
                    formData["_token"] = $('input[name="_token"]')[0].value;
                    formData["doc-ids"] = JSON.stringify(docIds);
                    toastr.info('Server is preparing the files to be downloaded', 'File download');
                    $.post(origin_url + '/api/getDownloadLinks', formData, function(response) {
                        sharePage.shareDataTable.rows().deselect();
                        setTimeout(function() {
                            sharePage.downloadLoop = 0;
                            sharePage.loopDownloadLinks(response.links);
                        }, 1000);
                    }).fail(function(response) {
                        toastr.error("Server cannot provide download links to unknown issue", "Server execution error");
                    });
                }
            },
            {
                text: '<i class="fa fa-remove"></i>',
                attr: {
                    id: "dt-row-deleteBtn",
                    disabled: true
                },
                className: 'btn btn-square btn-primary',
                titleAttr: 'Delete',
                action: function(e, dt, node, config) {
                    $('#deleteModal').modal('show');
                }
            }
        ], [
            [1, 'asc']
        ]);

        sharePage.newFolderDescriptionTxtArea = new Quill('#folder-description', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Description..."
        });
        sharePage.newFileDescriptionTxtArea = new Quill('#newfile-description', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Description..."
        });
        sharePage.newFileRemarkTxtArea = new Quill('#newfile-remarks', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Remarks..."
        });
        sharePage.bsFileInput = $("#file-attachment").fileinput({
            showUpload: false,
            previewFileIcon: '<i class="fas fa-file"></i>',
            allowedPreviewTypes: ['image', 'text'], // allow only preview of image & text files
            uploadAsync: false,
            previewFileIcon: '<i class="fa fa-file"></i>',
            allowedPreviewTypes: null, // set to empty, null or false to disable preview for all types
            previewFileIconSettings: {
                'doc': '<i class="fa fa-file-word-o"></i>',
                'xls': '<i class="fa fa-file-excel-o"></i>',
                'ppt': '<i class="fa fa-file-powerpoint-o"></i>',
                'pdf': '<i class="fa fa-file-pdf-o"></i>',
                'zip': '<i class="fa fa-file-zip-o"></i>',
                'txt': '<i class="fa fa-file-text-o"></i>'
            },
            previewFileExtSettings: {
                'doc': function(ext) {
                    return ext.match(/(doc|docx)$/i);
                },
                'xls': function(ext) {
                    return ext.match(/(xls|xlsx)$/i);
                },
                'ppt': function(ext) {
                    return ext.match(/(ppt|pptx)$/i);
                },
                'zip': function(ext) {
                    return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
                },
                'txt': function(ext) {
                    return ext.match(/(txt|ini|md)$/i);
                }
            }
        });
        var colSelArr = ['', 'Document Name', 'Submitted Date', 'Company Name', 'Document Type', 'Agreement Number',
            'Agreement Date', 'Parties', 'Expire Date', 'Description', 'Remarks'
        ];
        var colSelVisArr = [false, true, true, true, false, true, true, false, true, false, true];
        var colSelectionText = '';
        for (var i = 1; i < colSelArr.length; ++i) {
            colSelectionText += '<div class="dropdown-item">' +
                '<input id="colsel-chkbox' + i + '" class="form-check-input colsel-chkbox" type="checkbox" value="' + i + '" ' + (colSelVisArr[i] ? 'checked' : '') + '/>' +
                '<label class="form-check-label" for="colsel-chkbox' + i + '">' + colSelArr[i] + '</label></div>';
        }
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + colSelectionText +
            '</div>').insertAfter("#dt-row-columnSelect");

        $('<div class="input-group">' +
            '<input class="form-control" id="inputSearch-dtTable" type="text" name="inputSearch-dtTable" placeholder="Document name">' +
            '<span class="input-group-append">' +
            '<button class="btn btn-primary" type="button" id="btnSearch-dtTable"><i class="fa fa-search"></button>' +
            '</span>' +
            '</div>').appendTo('#share-table_filter');
        $('#share-table_filter label').remove();

        sharePage.shareDataTable = shareDataTable;
        $('#dt-row-selector').prop('checked', false);
        sharePage.userMagicSuggest = $('#username-mgcSuggest').magicSuggest(
            Utils.generateUserSuggestOption(origin_url + '/api/getAuthorizeUser', $('input[name="_token"]').prop('value')));
    },
    assignAction: function() {
        $(document).on('drop', 'body', function(e) {
            var dt = e.originalEvent.dataTransfer;
            if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) {
                $(document).off('drop', 'body');
                document.querySelector('#file-attachment').files = dt.files;
                $('#addFileModal').modal('show');
                $('#file-attachment').fileinput('refresh');
            }
        });

        $(document).on('hide.bs.modal', '#addFileModal', function(e) {
            $(document).on('drop', 'body', function(e) {
                var dt = e.originalEvent.dataTransfer;
                if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) {
                    $(document).off('drop', 'body');
                    document.querySelector('#file-attachment').files = dt.files;
                    $('#addFileModal').modal('show');
                    $('#file-attachment').fileinput('refresh');
                }
            });
        });

        $('#frmAddFolder').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            //submit a POST request with the form data
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });
            formData['folder-name'] = Utils.ltrim(formData['folder-name']);
            formData['description'] = sharePage.newFolderDescriptionTxtArea.container.firstChild.innerHTML;
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                toastr.success(formData['folder-name'] + ' has been created successfully', "Create Folder success");
                $('#addFolderModal').modal('hide');
                sharePage.shareDataTable.ajax.reload();
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                let errors = response['responseJSON']['errors'];
                Array.prototype.forEach.call(Object.keys(errors), prop => {
                    toastr.error(errors[prop][0], "Error on Create Folder");
                });
            });
            return false;
        });

        $('#frmAddFile').submit(function(e) {

            var $form = $(this);
            var urlDest = $form.attr('action');

            var formData = new FormData(this);
            var fileInput = document.getElementById('file-attachment');
            var file = fileInput.files[0];
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData.append($(this).attr('name'), Utils.ltrim($(this).val()));
            });

            formData.append("document-type", $('#document-type').val());
            formData.append("description", sharePage.newFileDescriptionTxtArea.container.firstChild.innerHTML);
            formData.append("remark", sharePage.newFileRemarkTxtArea.container.firstChild.innerHTML);
            formData.append('file-attachment', file);
            if (formData.get('agreement-date').length > 0) {
                var d = new Date(formData.get('agreement-date'));
                if (!d.isValid()) {
                    Utils.btnloadState(btn, false);
                    toastr.error("Please use YYYY-mm-dd format on Agreement Date", "Error on Creating shared file");
                    return false;
                }
            }
            if (formData.get('expire-date').length > 0) {
                var d = new Date(formData.get('expire-date'));
                if (!d.isValid()) {
                    Utils.btnloadState(btn, false);
                    toastr.error("Please use YYYY-mm-dd format on Expire Date", "Error on Creating shared file");
                    return false;
                }
            }

            // This new FormData instance is all you need to pass on the send() call:
            $.ajax({
                url: urlDest,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Utils.btnloadState(btn, false);
                    toastr.success(file.name + ' has been uploaded', "Creating shared file success");
                    $('#addFileModal').modal('hide');
                    sharePage.shareDataTable.ajax.reload();
                },
                error: function(request, status, error) {
                    Utils.btnloadState(btn, false);
                    if (request.status == '422') {
                        let errors = request['responseJSON']['errors'];
                        Array.prototype.forEach.call(Object.keys(errors), prop => {
                            toastr.error(errors[prop][0], "Error on Creating shared file");
                        });
                    } else if (request.status == '413')
                        toastr.error("File to be uploaded is too large", "Error on Creating shared file");
                    else
                        toastr.error("Unknown Error", "Error on Creating shared file");
                }
            });
            return false;
        });

        $('#frmShareFile').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            var userIds = [];
            sharePage.userMagicSuggest.getSelection().forEach(element => {
                userIds.push(element.id);
            });
            var docIds = [];
            sharePage.shareDataTable.rows({ selected: true }).data().toArray().forEach(element => {
                docIds.push({ type: element.type, id: element.doc_id })
            });
            formData["_token"] = $('input[name="_token"]')[0].value;
            formData["user-ids"] = JSON.stringify(userIds);
            formData["doc-ids"] = JSON.stringify(docIds);
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                toastr.success('Selected folder/file has been shared', "Share File/Folder success");
                $('#shareModal').modal('hide');
                sharePage.shareDataTable.ajax.reload();
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                let errors = response['responseJSON']['errors'];
                Array.prototype.forEach.call(Object.keys(errors), prop => {
                    toastr.error(errors[prop][0], "Error of sharing file");
                });
            });
            return false;
        });

        $('#frmDeleteFile').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            var docIds = [];
            sharePage.shareDataTable.rows({ selected: true }).data().toArray().forEach(element => {
                docIds.push({ type: element.type, id: element.doc_id })
            });
            formData["_token"] = $('input[name="_token"]')[0].value;
            formData["doc-ids"] = JSON.stringify(docIds);
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                toastr.success('Selected folders/files been deleted successfully', "Delete Folder/File success");
                $('#deleteModal').modal('hide');
                sharePage.shareDataTable.ajax.reload();
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                let errors = response['responseJSON']['errors'];
                Array.prototype.forEach.call(Object.keys(errors), prop => {
                    toastr.error(errors[prop][0], "Error on Create Folder");
                });
            });
            return false;
        });

        $('#dt-row-selector').on('click', function(e) {
            var trArr = $(sharePage.shareDataTable.table().body()).children().toArray();
            if ($(e.target).prop('checked')) {
                sharePage.shareDataTable.rows().select();
                sharePage.toggleDataTableButtons(true);
                trArr.forEach(function(value, index) {
                    $(value.children[0].children[0]).prop('checked', true);
                });
            } else {
                sharePage.shareDataTable.rows().deselect();
                sharePage.toggleDataTableButtons(false);
                trArr.forEach(function(value, index) {
                    $(value.children[0].children[0]).prop('checked', false);
                });
            }
        });

        $('#btnSearch-dtTable').on('click', function(e) {
            let phrase = $('#inputSearch-dtTable').val();
            phrase = phrase.replace(/^[ ]+|[ ]+$/g, '')
            phrase = phrase.replace(/\s+/g, "+");
            window.location.href = origin_url + '/share/q/' + phrase;
        });

        $('#share-table tbody').on('click', 'tr', function(e) {
            if (e.originalEvent.target.tagName == "A")
                return;
            $(this).toggleClass('selected');
            let $chkBox = $($(this).children('.select-checkbox')[0].children[0]);
            if ($(this).hasClass('selected')) {
                sharePage.shareDataTable.row(this).select();
                $chkBox.prop('checked', true);
                sharePage.toggleDataTableButtons(true);
            } else {
                sharePage.shareDataTable.row(this).deselect();
                $chkBox.prop('checked', false);
                let checked = false;
                let trArr = $(this).parent().children().toArray();
                for (var i = 0; i < trArr.length; ++i) {
                    if ($(trArr[i].children[0].children[0]).prop('checked')) {
                        checked = true;
                        break;
                    }
                }
                sharePage.toggleDataTableButtons(checked);
            }
        });

        $('#share-table').on('page.dt', function() {
            $('#dt-row-selector').prop('checked', false);
            sharePage.shareDataTable.rows().deselect();
            var trArr = $(sharePage.shareDataTable.table().body()).children().toArray();
            trArr.forEach(function(value, index) {
                $(value.children[0].children[0]).prop('checked', false);
            });
            sharePage.toggleDataTableButtons();
        });

        $('#colsel-dropdown').off('keydown', '#colsel-dropdown');

        $('.dropdown-menu.keep-open').on('click', function(e) {
            e.stopPropagation();
        });

        $('.colsel-chkbox').change(function() {
            if (this.checked) {
                sharePage.shareDataTable.column($(this).val()).visible(true);
            } else {
                sharePage.shareDataTable.column($(this).val()).visible(false);
            }
        });

        $(document).on('click', '.preview-doc-btn', function(e) {
            $('#sharedFolderPreview').attr('src', 'http://docs.google.com/gview?url=' + $(this).data('link') + '&embedded=true&timestamp=' + Date.now());
        });
    },
    toggleDataTableButtons: function(condition) {
        $('#dt-row-shareBtn').prop('disabled', !condition);
        $('#dt-row-downloadBtn').prop('disabled', !condition);
        $('#dt-row-deleteBtn').prop('disabled', !condition);
    },
    downloadLoop: 0,
    loopDownloadLinks: function(links) {
        setTimeout(function() {
            toastr.info('Start downloading ' + links[sharePage.downloadLoop].name,
                'Downloading ' + (sharePage.downloadLoop + 1) + ' out of ' + links.length + ' documents');
            $('#file-downlod-frame').attr('src', links[sharePage.downloadLoop].link);
            sharePage.downloadLoop++;
            if (sharePage.downloadLoop < links.length) {
                sharePage.loopDownloadLinks(links); //sampai sini
            }
            sharePage.shareDataTable.rows().deselect();
        }, 3000)
    }
}

var profilePage = {
    drawPage: function() {
        $("#avatar").fileinput({
            showUpload: false,
            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
            maxImageWidth: 360,
            maxImageHeight: 360
        });
    },
    assignAction: function() {
        $('#frm-chg-password').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            //submit a POST request with the form data
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                toastr.success('Password has been changed successfully', "Change Password Success");
                $('#change-pwd-modal').modal('hide');
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                let errors = response['responseJSON']['errors'];
                Array.prototype.forEach.call(Object.keys(errors), prop => {
                    toastr.error(errors[prop][0], "Change Password Failed");
                });
            });
            return false;
        });
        $('#frm-upd-profile').submit(function(e) {
            var $form = $(this);
            var urlDest = $form.attr('action');
            var formData = new FormData(this);
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData.append($(this).attr('name'), Utils.ltrim($(this).val()));
            });

            // This new FormData instance is all you need to pass on the send() call:
            $.ajax({
                url: urlDest,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    window.location.reload();
                },
                error: function(request, status, error) {
                    Utils.btnloadState(btn, false);
                    if (request.status == '422') {
                        let errors = request['responseJSON']['errors'];
                        Array.prototype.forEach.call(Object.keys(errors), prop => {
                            toastr.error(errors[prop][0], "Change profile Error");
                        });
                    } else if (request.status == '413')
                        toastr.error("File to be uploaded is too large", "Change profile Error");
                    else
                        toastr.error("Unknown Error", "Change profile Error");
                }
            });
            return false;
        });
    }
}

var userSettingPage = {
    dataTable: null,
    drawPage: function() {
        var rowsDefinition = [{
                data: "name",
                width: '150px',
                render: function(data, type, row) {
                    return '<img class="img-avatar mr-2" src="' + origin_url + '/' + row.avatar + '" width="36px"></img>' + row.name;
                }
            },
            { data: "email" },
            { data: "fullname" },
            { data: "role" },
            {
                data: "logged_in",
                render: function(data, type, row) {
                    if (row.logged_in)
                        return '<span class="badge badge-pill badge-success ml-2">Active</span>';
                    else
                        return '<span class="badge badge-pill badge-secondary ml-2">Inactive</span>';
                }
            },
            {
                data: "logged_in",
                render: function(data, type, row) {
                    if (row.role_id == 1) {
                        if (row.buhead_id == null)
                            return '<span class="badge badge-pill badge-danger ml-2">Not Assigned</span>';
                        else
                            return row.buhead_name;
                    } else
                        return '';
                }
            },
            {
                data: "last_login",
                render: function(data, type, row) {
                    if (row.last_login == null)
                        return '-';
                    return Utils.getDateStr(row.last_login);
                }
            }
        ];
        userSettingPage.dataTable = renderDataTable('#user-table', origin_url + '/userall',
            rowsDefinition, null, null);
    },
    assignAction: function() {
        $('#user-table tbody').on('click', 'tr', function() {
            var userData = userSettingPage.dataTable.row(this).data();
            $('#avatar-txt').attr('src', origin_url + '/' + userData.avatar);
            $('#username-txt').text(userData.name);
            $('#fullname-txt').text(userData.fullname);
            $('#email-txt').text(userData.email);
            document.getElementById("role-id").value = userData.role_id;
            $('#user-id').val(userData.id);
            $('#usersDetailModal').modal('show');

            if (userData.role_id == 1) {
                if (userData.buhead_id != null)
                    document.getElementById("buhead-id").value = userData.buhead_id;
                else
                    document.getElementById("buhead-id").value = 0;
                $('#tr-buhead').show();
            } else
                $('#tr-buhead').hide();
        });

        $('#role-id').on('change', function(e) {
            if (e.target.value == 1)
                $('#tr-buhead').show();
            else
                $('#tr-buhead').hide();
        });

        $('#form-chgRole').submit(function(e) {
            var $form = $(this);
            var url = $form.attr('action');
            var formData = {};
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);
            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData[$(this).attr('name')] = $(this).val();
            });
            formData['role-id'] = document.getElementById("role-id").value;
            formData['buhead-id'] = document.getElementById("buhead-id").value;
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                toastr.success($('#fullname-txt').text() + ' has been set as ' + $('#role-id').children()[formData['role-id']].text, "Change User Role success");
                $('#usersDetailModal').modal('hide');
                userSettingPage.dataTable.ajax.reload();
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                toastr.error("", "Error on Change User Role");
            });
            return false;
        });
    }
}

function renderDataTable(container, url, columnDefinitions, btnSetting, orderType = null) {
    var setting = {
        colReorder: true,
        pageLength: 25,
        columns: columnDefinitions,
        processing: false,
        serverSide: false,
        ajax: null,
        sAjaxDataProp: "",
        // dom:null,
        buttons: null,
    }
    if (url != null)
        setting.ajax = url;
    if (btnSetting != null) {
        setting.dom = 'Bfrtip';
        setting.buttons = btnSetting;
    }
    if (orderType != null)
        setting.order = orderType;
    else
        setting.order = [
            [0, "desc"]
        ];
    $(container).DataTable().destroy();

    return $(container).DataTable(setting);
}

var Utils = {
    monthName: ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
    getDateStr: function(date, isDatetime = true) {
        try {
            if (isDatetime)
                date = date.substr(0, date.indexOf(' '));
            dateComponents = date.split('-');

            return dateComponents[2] + ' ' + this.monthName[parseInt(dateComponents[1])] + ' ' + dateComponents[0];
        } catch (e) {
            return '';
        }
    },
    renderColDate: function(dataEntry, date, isDateTime = true) {
        let ret = {
            data: dataEntry,
            width: COL_DATE_WIDTH + 'px',
            sType: "date",
            render: function(data, type, row) {
                if (eval(date) == null)
                    return "";
                return '<div data-date = "' + eval(date) + '">' + Utils.getDateStr(eval(date), isDateTime) + '</div>';
            }
        }
        return ret;
    },
    renderSlaTable: function(container, data) {
        var data = data.slice();
        data.sort(function(a, b) { return (a.sla_min + a.sla_max) - (b.sla_min + b.sla_max) });
        var groupedData = [data[0]];
        for (var i = 1, j = 0; i < data.length; ++i) {
            if ((groupedData[j].sla_min == data[i].sla_min) && (groupedData[j].sla_max == data[i].sla_max))
                groupedData[j].type += ' / ' + data[i].type;
            else {
                groupedData.push(data[i]);
                j++;
            }
        }
        var tableStr = '';
        $(container).empty();
        for (var i = 0; i < groupedData.length; ++i) {
            tableStr += '<tr>' +
                '<td class="pt-1 pb-1" width="75%">' + groupedData[i].type + '</td>' +
                '<td class="pt-1 pb-1">' + (groupedData[i].sla_min == groupedData[i].sla_max ? groupedData[i].sla_min : groupedData[i].sla_min + '-' + groupedData[i].sla_max) + ' Working day(s).</td>' +
                '</tr>';
        }
        $(tableStr).appendTo(container);
    },
    ltrim: function(str) {
        if (str == null) return str;
        return str.replace(/^\s+/g, '');
    },
    isNullOrWhitespace: function(input) {

        if (typeof input === 'undefined' || input == null) return true;

        return input.replace(/\s/g, '').length < 1;
    },
    generateUserSuggestOption: function(url, token) {
        var userSuggestOptions = {
            renderer: function(data) {
                if (data.role_name == null)
                    return data.fullname;
                else
                    return '<div class="row ml-1 mr-1 pt-1 pb-1 border-bottom">' +
                        '<div class="col md-9">' +
                        '<div>' + data.fullname + '</div>' +
                        '<div class="font-xs text-muted" style="line-height:1"><span>' + data.role_name + '</span></div>' +
                        '</div>' +
                        '</div>';
            },
            placeholder: 'Please enter name of user',
            method: 'post',
            dataUrlParams: { _token: token },
            data: url,
            displayField: 'fullname',
            valueField: 'fullname',
            ajaxConfig: {
                xhrFields: {
                    withCredentials: true,
                }
            }
        }
        return userSuggestOptions;
    },
    renderRequestDetail: function(data) {
        $('#req-detail-tab-btn').tab('show').addClass('active');
        if (data.approval_type == 'REQUEST') {
            $('#proposed-by-info').hide();
            $('#proposed-date-info').hide();
            $('#title-txt').empty().append('<b>Request Docs</b>');
        } else {
            $('#proposed-by-info').show();
            $('#proposed-date-info').show();
            $('#requestDetail-proposed-by').text(data.proposed_by);
            $('#requestDetail-proposed-date').text(Utils.getDateStr(data.proposed_date, false));
            $('#title-txt').empty().append('<b>Request Review Docs</b>');
        }
        $('#requestDetail-doc-type').text(data.doc_type);
        $('#requestDetail-purpose').text(data.purpose);
        $('#requestDetail-parties').text(data.parties);
        $('#requestDetail-description').empty().append(data.description);
        $('#requestDetail-commercial').text(data.commercial_terms);
        $('#requestDetail-value').text(data.transaction_value);
        $('#requestDetail-toleration').text(data.late_payment_toleration);
        $('#requestDetail-condition').text(data.condition_precedent);
        $('#requestDetail-termination').text(data.termination_terms);
        $('#requestDetail-payment').text(data.payment_terms);
        $('#requestDetail-delay').text(data.delay_penalty);
        $('#requestDetail-guarantee').text(data.guarantee);
        $('#requestDetail-agreement').text(data.agreement_terms);
        $('#req-attachment table tbody').empty();
        for (var i = 0; i < data.attachments.length; ++i) {
            $('#req-attachment table tbody').append('<tr>' +
                '<td width="250px">' + ATTACHMENT_TYPE[data.attachments[i].kind] + '</td>' +
                '<td><a href="' + origin_url + '/api/getRequestAttachment/' + data.attachments[i].id + '">' +
                data.attachments[i].filename +
                '</a></td>' +
                '</tr>');
        }
        $('#req-history-table tbody').empty();
        var dateString;
        var assigneeStr;
        for (var i = 0; i < data.history.length; ++i) {
            dateString = '-';
            assigneeStr = '-'
            if (data.history[i].updated_at != null) {
                dateString = moment(data.history[i].updated_at, "YYYY-MM-DD HH:mm:ss").toDate().toUTCString();
                dateString = dateString.split(' ').slice(0, 4).join(' ');
                assigneeStr = data.history[i].fullname;
            }
            var isCommented = Utils.isCommentExist(data.history[i].comments);
            $('#req-history-table tbody').append('<tr ' + (isCommented ? 'class="view"' : '') + '>' +
                '<td>' + assigneeStr + '</td>' +
                '<td>' + data.history[i].state + '</td>' +
                '<td>' + dateString + (isCommented ? ' <i class="fa fa-comment-o ml-2"></i>' : '') + '</td>' +
                '</tr>');
            if (isCommented) {
                $('#req-history-table tbody').append('<tr class="fold">' +
                    '<td colspan="3">' + data.history[i].comments + '</td>' +
                    '</tr>');
            }
        }

        if (data.notif && data.is_active && data.submission == null)
            $('#approval-tab-btn').show();
        else
            $('#approval-tab-btn').hide();

        $('#request-id').val(data.id);

        $(".fold-table tr.view").on("click", function() {
            $(this).toggleClass("open").next(".fold").toggleClass("open");
        });
    },
    btnloadState: function($btn, isLoad) {
        var txt = $btn.html();
        if (isLoad) {
            $btn.attr('disabled', true);
            $btn.empty().append('<i class="fa fa-circle-o-notch fa-spin"></i> ' + txt);
        } else {
            $btn.attr('disabled', false);
            $btn.empty().append(txt.replace('<i class="fa fa-circle-o-notch fa-spin"></i> ', ''));
        }
    },
    isCommentExist: function(str) {
        if ((str === null) || (str === ''))
            return false;
        else
            str = str.toString();
        str = str.replace(/(<([^>]+)>)/ig, '');
        return !Utils.isNullOrWhitespace(str);
    }
}

function createColumn(btnName, visible) {
    if (visible == null || visible == 'undefined')
        visible = true;
    let btn = null;
    switch (btnName) {
        case 'SUBMITTED_DATE':
            btn = Utils.renderColDate("created_at", "row.created_at", true);
            break;
        case 'SUBMITTER':
            btn = {
                data: "requester_name",
                width: '150px',
                render: function(data, type, row) {
                    return row.requester_name;
                }
            };
            break;
        case 'OWNER':
            btn = {
                data: "owner_name",
                width: '150px',
                render: function(data, type, row) {
                    if (row.owner_name != null)
                        return row.owner_name;
                    else
                        return '';
                }
            };
            break;
        case 'HOLDER':
            btn = {
                data: "holder_name",
                width: '150px',
                render: function(data, type, row) {
                    if (row.holder_name != null)
                        return row.holder_name;
                    else
                        return '';
                }
            };
            break;
        case 'COMPLETED_DATE':
            btn = Utils.renderColDate("updated_at", "row.updated_at", true);
            break;
        case 'REVISION_VERSION':
            btn = {
                data: "version",
                width: COL_DOCTYPE_WIDTH + 'px',
                render: function(data, type, row) {
                    return '<a href="' + origin_url + '/getSubmissionAttachment/' + row.id + '"><span><i class="fa fa-download mr-2"></i></span>Version ' + row.version + '</a>';
                }
            }
            break;
        case 'ACTIVITIES':
            btn = {
                data: "approval_type",
                width: COL_DOCTYPE_WIDTH + 'px',
                render: function(data, type, row) {
                    if (row.approval_type == 'REQUEST')
                        return '<i class="nav-icon icons icon-tag pr-2"></i>Request';
                    else
                        return '<i class="nav-icon icons icon-badge pr-2"></i>Review';
                }
            };
            break;
        case 'PARTIES':
            btn = { data: "parties", width: COL_PARTIES_WIDTH + 'px' };
            break;
        case 'PURPOSE':
            btn = { data: "purpose" };
            break;
        case 'REQUEST_STATUS':
            btn = {
                data: "notif",
                width: '150px',
                render: function(data, type, row) {
                    if ($('input[name="acc_role"]').val() == 5) {
                        if (row.notif && row.verify_status == null)
                            return '<span class="badge badge-pill badge-danger ml-2">Need to Approve</span>';
                        if (row.approved)
                            return '<span class="badge badge-pill badge-secondary ml-2">Approved</span>';
                        return '';
                    }
                    if (row.next_status_id == null)
                        return '<span class="badge badge-pill badge-success ml-2">Completed</span>';
                    if (!row.is_active)
                        return '<span class="badge badge-pill badge-warning ml-2">Rejected</span>';
                    if (row.notif && row.verify_status == null)
                        return '<span class="badge badge-pill badge-danger ml-2">Need to Approve</span>';
                    if (row.approved || row.verify_status != null)
                        return '<span class="badge badge-pill badge-secondary ml-2">Approved</span>';
                    if (row.owner_name != null)
                        return '<span class="badge badge-pill badge-info ml-2">Processed by PIC</span>';
                    return '<span class="badge badge-pill badge-info ml-2">Pending Approval</span>';
                }
            }
            break;
        case 'VERIFY_STATUS':
            btn = {
                data: 'verify_status',
                width: '150px',
                render: function(data, type, row) {
                    if (row.verify_status == null)
                        return 'N/A';
                    else if ((row.verify_status == 'STATE_DONE' || row.verify_status == 'STATE_TOBE_REVISE') &&
                        role == ROLE_HEAD_LEGAL)
                        return '<button type="button" class="btn btn-primary">Need to Verify</button>';
                    else if (row.verify_status == 'STATE_APPROVED')
                        return 'Verified';
                    return 'Verifying';
                }
            };
            break;
        case 'STATUS':
            btn = { data: "status", width: COL_STATUS_WIDTH + 'px' };
            break;
        case 'CEO_APPROVED':
        case 'CFO_APPROVED':
        case 'BU_APPROVED':
        case 'LEGAL_APPROVED':
            btn = {
                data: 'verify_status',
                width: '150px',
                render: function(data, type, row) {
                    btnName = btnName.toLowerCase();
                    if (row[btnName] == null)
                        return 'N/A';
                    else if (row[btnName] == 1)
                        return 'Approved';
                    else if (row[btnName] == 0)
                        return 'Pending';
                    return '';
                }
            };
            break;
        case 'DESCRIPTION':
            btn = { data: "description", width: COL_STATUS_WIDTH + 'px' };
            break;
        case 'DOCUMENT_TYPE':
            btn = {
                data: "doc_type",
                width: COL_DOCTYPE_WIDTH + 'px',
                render: function(data, type, row) {
                    let badge = '';
                    if (row.new)
                        badge = '<span class="badge badge-pill badge-success ml-2">New</span>';
                    return row.doc_type + badge;
                }
            }
            break;
    }
    if (btn != null)
        btn.visible = visible;
    return btn;
};

function createColumnSelBtn(colSelection) {
    let colSelectionText = '';
    for (var i = 0; i < colSelection.length; ++i) {
        colSelectionText += '<div class="dropdown-item">' +
            '<input id="colsel-chkbox' + i + '" class="form-check-input colsel-chkbox" type="checkbox" value="' + i + '" ' + (colSelection[i].def ? 'checked' : '') + '/>' +
            '<label class="form-check-label" for="colsel-chkbox' + i + '">' + colSelection[i].lbl + '</label></label></div>';
    }
    return colSelectionText;
}

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-pre": function(a) {
        return $(a).data("date");
    },
    "date-asc": function(a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-desc": function(a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

var toolbarOptions = [
    [{
        'header': [1, 2, 3, 4, 5, 6, false]
    }],
    ['bold', 'italic', 'underline', 'strike'], // toggled buttons
    ['blockquote', 'code-block'],
    [{
        'list': 'ordered'
    }, {
        'list': 'bullet'
    }],
    [{
        'script': 'sub'
    }, {
        'script': 'super'
    }], // superscript/subscript
    [{
        'indent': '-1'
    }, {
        'indent': '+1'
    }], // outdent/indent
    [{
        'align': []
    }],
    ['clean'] // remove formatting button
];

Date.prototype.isValid = function() {
    // An invalid date object returns NaN for getTime() and NaN is the only
    // object not strictly equal to itself.
    return this.getTime() === this.getTime();
};