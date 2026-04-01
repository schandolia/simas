window.$ = window.jQuery

const COL_DATE_WIDTH = 70;
const COL_DOCTYPE_WIDTH = 130;
const COL_PARTIES_WIDTH = 200;
const COL_STATUS_WIDTH = 80;

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
    if (pageMenu == 'login') {
        loginPage.drawPage();
        loginPage.assignAction();
        return;
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
                case 'processed':
                case 'complete':
                    genericPage.drawPage();
                    genericPage.assignAction();
                    break;
                case 'profile':
                    profilePage.drawPage();
                    profilePage.assignAction();
                    break;

            }
        }
    });
});

var dashboardPage = {
    emailContentTxtArea: null,
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
        dashboardPage.emailContentTxtArea = new Quill('#email-body', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Description..."
        });
    },
    assignAction: function() {
        $('#form-emailus').submit(function(e) {
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
            formData['email-body'] = dashboardPage.emailContentTxtArea.container.firstChild.innerHTML;
            toastr.info('Your email is being sent, please wait', "Email send info");
            $.post(url, formData, function(response) {
                Utils.btnloadState(btn, false);
                toastr.success('Your email has been successfully sent', "Email send successfully");
                $('#email-modal').modal('hide');
            }).fail(function(response) {
                Utils.btnloadState(btn, false);
                let errors = response['responseJSON']['errors'];
                Array.prototype.forEach.call(Object.keys(errors), prop => {
                    toastr.error(errors[prop][0], "Error on Sending email");
                });
            });
            return false;
        })
    }
}
var requestPage = {
    req_step: 1,
    newRequestDescriptionTxtArea: null,
    requestDataTable: null,
    submitStep: ['Select Document', 'Descriptions', 'Document Requirements', 'Summary'],
    drawPage: function() {
        let rowsDefinition = [
            createColumn('SUBMITTED_DATE', true),
            createColumn('DOCUMENT_TYPE', true),
            createColumn('PARTIES', true),
            createColumn('PURPOSE', true),
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
                text: '<i class="fa fa-plus pr-2"></i> New Request',
                className: 'btn btn-square btn-primary ml-1',
                action: function(e, dt, node, config) {
                    $('#newRequestModal').modal('show');
                    requestPage.req_step = 1;
                    $('.nav-tabs a[href="#step1"]').tab('show');
                    $('#next-request-btn').text('Next');
                    $('#back-btn').text('close').attr('class', 'btn btn-secondary');
                }
            }
        ];
        let colSelArr = [{ lbl: 'Date Requested', def: true },
            { lbl: 'Document Type', def: true },
            { lbl: 'The Parties', def: true },
            { lbl: 'Purpose Agreement', def: true },
            { lbl: 'Status', def: true },
            { lbl: 'Description', def: false }
        ];

        requestPage.newRequestDescriptionTxtArea = new Quill('#description', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Description..."
        });

        requestPage.requestDataTable = renderDataTable('#request-table', origin_url + '/api/getRequestDocs',
            rowsDefinition, buttonDefinition, null);
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + createColumnSelBtn(colSelArr) + '</div>').insertAfter("#dt-row-columnSelect");

        Utils.renderSlaTable('#sla-container', docTypeList);
    },
    assignAction: function() {
        $(document).on('click', '#next-request-btn', function(e) {
            requestPage.req_step++;
            $('#submit-step').text(requestPage.submitStep[requestPage.req_step - 1]);
            $('#submit-request-btn').hide();
            if (requestPage.req_step == 2)
                $('#back-btn').text('Back').attr('class', 'btn btn-primary');
            if (requestPage.req_step < 4)
                $('.nav-tabs a[href="#step' + requestPage.req_step + '"]').tab('show');
            else if (requestPage.req_step == 4) {
                $('.nav-tabs a[href="#step' + requestPage.req_step + '"]').tab('show');
                $('#next-request-btn').hide();
                $('#submit-request-btn').show();
                requestPage.fillRequestConfirmation();
            }
        });

        $(document).on('click', '#back-btn', function(e) {
            requestPage.req_step--;
            $('#submit-step').text(requestPage.submitStep[requestPage.req_step - 1]);
            if (requestPage.req_step == 3) {
                $('#next-request-btn').show;
                $('#submit-request-btn').hide();
            }
            if (requestPage.req_step == 1)
                $(this).text('close').attr('class', 'btn btn-secondary');
            if (requestPage.req_step > 0)
                $('.nav-tabs a[href="#step' + requestPage.req_step + '"]').tab('show');
            else
                $('#newRequestModal').modal('hide');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr("href") // activated tab
            requestPage.req_step = Number(target.replace('#step', ''));
            $('#submit-step').text(requestPage.submitStep[requestPage.req_step - 1]);
            if (requestPage.req_step == 1)
                $('#back-btn').text('close').attr('class', 'btn btn-secondary');
            else
                $('#back-btn').text('Back').attr('class', 'btn btn-primary');

            if (requestPage.req_step == 4) {
                $('#next-request-btn').hide();
                $('#submit-request-btn').show();
                requestPage.fillRequestConfirmation();
            } else {
                $('#next-request-btn').show();
                $('#submit-request-btn').hide();
            }
        });

        $('#submit-request-btn').on('click', function() {
            if (!$('#confirm-chk').is(':checked')) {
                toastr.error('To submit new request, you must agree with terms and conditions', "Error on Creating New Review");
                return false;
            }
            return true;
        });

        $('#frmSubmitRequest').submit(function(e) {
            var $form = $(this);
            var urlDest = $form.attr('action');
            var formData = new FormData(this);
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData.append($(this).attr('name'), Utils.ltrim($(this).val()));
            });
            formData.append("description", requestPage.newRequestDescriptionTxtArea.container.firstChild.innerHTML)
            formData.append("document-type", $('#document-type').val());

            $('#frmSubmitRequest input[type=file]').toArray().forEach(function(value) {
                formData.append(value.id, value.files[0]);
            });

            $.ajax({
                url: urlDest,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Utils.btnloadState(btn, false);
                    toastr.success('New request has been submitted', "Submit new request success");
                    $('#newRequestModal').modal('hide');
                    requestPage.requestDataTable.ajax.reload();
                },
                error: function(request, status, error) {
                    Utils.btnloadState(btn, false);
                    if (request.status == '422') {
                        let errors = request['responseJSON']['errors'];
                        Array.prototype.forEach.call(Object.keys(errors), prop => {
                            toastr.error(errors[prop][0], "Error on Creating New Request");
                        });
                    } else if (request.status == '413')
                        toastr.error("File to be uploaded is too large", "Error on Creating New Request");
                    else
                        toastr.error("Unknown Error", "Error on Creating New Request");
                }
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
    },
    fillRequestConfirmation: function() {
        let docType = $('#document-type').val();
        if (docType != 0)
            $('#doc-type-txt').text(docTypeList[docType - 1].type);
        $('#frmSubmitRequest input[type=text]').toArray().forEach(function(value) {
            $('#' + value.id + '-txt').text(Utils.ltrim($(value).val()));
        })
        $('#description-txt').empty();
        $('#description-txt').append(requestPage.newRequestDescriptionTxtArea.container.firstChild.innerHTML);
        $('#frmSubmitRequest input[type=file]').toArray().forEach(function(value) {
            if (Utils.isNullOrWhitespace(value.value))
                $('#' + value.id + '-txt').hide();
            else
                $('#' + value.id + '-txt').show();
        });
    }
}

var reviewPage = {
    req_step: 1,
    newReviewDescriptionTxtArea: null,
    reviewDataTable: null,
    submitStep: ['Select Document', 'Descriptions', 'Document Requirements'],
    drawPage: function() {
        let rowsDefinition = [
            createColumn('SUBMITTED_DATE', true),
            createColumn('DOCUMENT_TYPE', true),
            createColumn('PARTIES', true),
            createColumn('PURPOSE', true),
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
                text: '<i class="fa fa-plus pr-2"></i> New Review',
                className: 'btn btn-square btn-primary ml-1',
                action: function(e, dt, node, config) {
                    $('#newReviewModal').modal('show');
                    reviewPage.req_step = 1;
                    $('.nav-tabs a[href="#step1"]').tab('show');
                    $('#back-btn').text('close').attr('class', 'btn btn-secondary');
                }
            }
        ];
        var colSelArr = [{ lbl: 'Date Requested', def: true },
            { lbl: 'Document Type', def: true },
            { lbl: 'The Parties', def: true },
            { lbl: 'Purpose Agreement', def: true },
            { lbl: 'Status', def: true },
            { lbl: 'Description', def: false }
        ];

        reviewPage.reviewDataTable = renderDataTable('#review-table', origin_url + '/api/getReviewDocs',
            rowsDefinition, buttonDefinition, null);
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + createColumnSelBtn(colSelArr) + '</div>').insertAfter("#dt-row-columnSelect");

        reviewPage.newReviewDescriptionTxtArea = new Quill('#description', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Description..."
        });

        Utils.renderSlaTable('#sla-container', docTypeList);
    },
    assignAction: function() {
        $(document).on('click', '#next-review-btn', function(e) {
            reviewPage.req_step++;
            $('#submit-step').text(reviewPage.submitStep[reviewPage.req_step - 1]);
            $('#submit-review-btn').hide();
            if (reviewPage.req_step == 2)
                $('#back-btn').text('Back').attr('class', 'btn btn-primary');
            if (reviewPage.req_step < 3)
                $('.nav-tabs a[href="#step' + reviewPage.req_step + '"]').tab('show');
            else if (reviewPage.req_step == 3) {
                $('.nav-tabs a[href="#step' + reviewPage.req_step + '"]').tab('show');
                $('#next-review-btn').hide();
                $('#submit-review-btn').show();
                reviewPage.fillReviewConfirmation();
            }
        });

        $(document).on('click', '#back-btn', function(e) {
            reviewPage.req_step--;
            $('#submit-step').text(reviewPage.submitStep[reviewPage.req_step - 1]);
            if (reviewPage.req_step == 2) {
                $('#next-review-btn').show;
                $('#submit-review-btn').hide();
            }
            if (reviewPage.req_step == 1)
                $(this).text('close').attr('class', 'btn btn-secondary');
            if (reviewPage.req_step > 0)
                $('.nav-tabs a[href="#step' + reviewPage.req_step + '"]').tab('show');
            else
                $('#newReviewModal').modal('hide');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr("href") // activated tab
            reviewPage.req_step = Number(target.replace('#step', ''));
            $('#submit-step').text(reviewPage.submitStep[reviewPage.req_step - 1]);
            if (reviewPage.req_step == 1)
                $('#back-btn').text('close').attr('class', 'btn btn-secondary');
            else
                $('#back-btn').text('Back').attr('class', 'btn btn-primary');

            if (reviewPage.req_step == 3) {
                $('#next-review-btn').hide();
                $('#submit-review-btn').show();
                reviewPage.fillReviewConfirmation();
            } else {
                $('#next-review-btn').show();
                $('#submit-review-btn').hide();
            }
        });

        $('#submit-review-btn').on('click', function() {
            if (!$('#confirm-chk').is(':checked')) {
                toastr.error('To submit new request, you must agree with terms and conditions', "Error on Creating New Review");
                return false;
            }
            return true;
        });

        $('#frmSubmitReview').submit(function(e) {
            var $form = $(this);
            var urlDest = $form.attr('action');
            var formData = new FormData(this);
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData.append($(this).attr('name'), Utils.ltrim($(this).val()));
            });
            formData.append("description", reviewPage.newReviewDescriptionTxtArea.container.firstChild.innerHTML)
            formData.append("document-type", $('#document-type').val());

            var fileInput = document.getElementById('attachment');
            var file = fileInput.files[0];
            formData.append('attachment', file);
            if (formData.get('date').length > 0) {
                var d = new Date(formData.get('date'));
                if (!d.isValid()) {
                    Utils.btnloadState(btn, false);
                    toastr.error("Please use YYYY-mm-dd format on Date", "Error on Creating New Review");
                    return false;
                }
            }
            $.ajax({
                url: urlDest,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Utils.btnloadState(btn, false);
                    toastr.success('New document review has been submitted', "Submit new review document success");
                    $('#newReviewModal').modal('hide');
                    reviewPage.reviewDataTable.ajax.reload();
                },
                error: function(request, status, error) {
                    Utils.btnloadState(btn, false);
                    if (request.status == '422') {
                        let errors = request['responseJSON']['errors'];
                        Array.prototype.forEach.call(Object.keys(errors), prop => {
                            toastr.error(errors[prop][0], "Error on Creating New Review");
                        });
                    } else if (request.status == '413')
                        toastr.error("File to be uploaded is too large", "Error on Creating New Review");
                    else
                        toastr.error("Unknown Error", "Error on Creating New Review");
                }
            });
            return false;
        });

        $('#colsel-dropdown').off('keydown', '#colsel-dropdown');

        $('.dropdown-menu.keep-open').on('click', function(e) {
            e.stopPropagation();
        });

        $('.colsel-chkbox').change(function() {
            if (this.checked) {
                reviewPage.reviewDataTable.column($(this).val()).visible(true);
            } else {
                reviewPage.reviewDataTable.column($(this).val()).visible(false);
            }
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
    },
    fillReviewConfirmation: function() {
        let docType = $('#document-type').val();
        if (docType != 0)
            $('#doc-type-txt').text(docTypeList[docType - 1].type);
        $('#frmSubmitReview input[type=text]').toArray().forEach(function(value) {
            $('#' + value.id + '-txt').text(Utils.ltrim($(value).val()));
        });
        console.log('1:' + $('#date').val());
        $('#date-txt').text(Utils.getDateStr($('#date').val(), false));
        $('#description-txt').empty();
        $('#attachment-txt').text($('#attachment').val().replace("C:\\fakepath\\", ""));
        $('#description-txt').append(reviewPage.newReviewDescriptionTxtArea.container.firstChild.innerHTML);
    }
}

var genericPage = {
    dataTable: null,
    submissionNotesTxtArea: null,
    drawPage: function() {
        let rowsDefinition = [
            createColumn('SUBMITTED_DATE', false),
            createColumn('DOCUMENT_TYPE'),
            createColumn('ACTIVITIES'),
            createColumn('PARTIES'),
            createColumn('PURPOSE', false),
            createColumn('COMPLETED_DATE', true),
            createColumn('VERIFY_STATUS', true),
            createColumn('REVISION_VERSION', true),
            createColumn('STATUS', false),
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
        }];
        var colSelArr = [
            { lbl: 'Date Requested', def: false },
            { lbl: 'Document Type', def: true },
            { lbl: 'Activities', def: true },
            { lbl: 'The Parties', def: true },
            { lbl: 'Purpose Agreement', def: false },
            { lbl: 'Completed Date', def: true },
            { lbl: 'Action', def: true },
            { lbl: 'Revision Version', def: true },
            { lbl: 'Status', def: false },
            { lbl: 'Description', def: false }
        ];

        if (pageMenu == 'processed') {
            //Remove Status
            colSelArr[8].def = true;
            rowsDefinition[8].visible = true;
            rowsDefinition.splice(5, 3);
            colSelArr.splice(5, 3);
        }
        genericPage.dataTable = renderDataTable('#generic-table', $('#data-tbl-url').val(),
            rowsDefinition, buttonDefinition, null);

        genericPage.submissionNotesTxtArea = new Quill('#req-submission-notes', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: "Notes..."
        });
        $('<div class="dropdown-menu dropdown-menu-left keep-open" id="colsel-dropdown">' + createColumnSelBtn(colSelArr) +
            '</div>').insertAfter("#dt-row-columnSelect");
    },
    assignAction: function() {
        $('#colsel-dropdown').off('keydown', '#colsel-dropdown');

        $('.dropdown-menu.keep-open').on('click', function(e) {
            e.stopPropagation();
        });

        $('.colsel-chkbox').change(function() {
            if (this.checked)
                genericPage.dataTable.column($(this).val()).visible(true);
            else
                genericPage.dataTable.column($(this).val()).visible(false);
        });

        $('#generic-table tbody').on('click', 'tr', function() {
            var reqID = genericPage.dataTable.row(this).data().id;
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

        $('#generic-table tbody').on('click', 'button', function() {
            var req = genericPage.dataTable.row($(this).parents('tr')).data();
            $('#request-submission-title').text('Verify Requested Document [ Request ID : ' + req.id + ' ]')
            $('#req-submission-docType').text(req.doc_type);
            $('#req-submission-docId').val(req.id);
            $('#req-submission-date').text(Utils.getDateStr(req.date, false));
            $('#req-submission-version').text(req.version);
            $('#requestSubmissionModal').modal('show');
            return false;
        });

        $('#frmRequestSubmission').submit(function(e) {
            var $form = $(this);
            var urlDest = $form.attr('action');

            var formData = new FormData(this);
            var fileInput = document.getElementById('input-attachment');
            var file = fileInput.files[0];
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            formData.append("notes", genericPage.submissionNotesTxtArea.container.firstChild.innerHTML);
            formData.append('file-attachment', file);
            $.ajax({
                url: urlDest,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Utils.btnloadState(btn, false);
                    toastr.success('Your request has been successfully submitted', "Revise Document Request Success");
                    $('#requestSubmissionModal').modal('hide');
                    genericPage.dataTable.ajax.reload();
                },
                error: function(request, status, error) {
                    Utils.btnloadState(btn, false);
                    if (request.status == '422') {
                        let errors = request['responseJSON']['errors'];
                        Array.prototype.forEach.call(Object.keys(errors), prop => {
                            toastr.error(errors[prop][0], "Error on Requesting Revise Document");
                        });
                    } else if (request.status == '413')
                        toastr.error("File to be uploaded is too large", "Error on Requesting Revise Document");
                    else
                        toastr.error("Unknown Error", "Error on Requesting Revise Document");
                }
            });
            return false;
        });
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

var loginPage = {
    drawPage: function() {
        //do nothing
    },
    assignAction: function() {
        $('#input-password-conf, #input-password').on('keyup', function() {
            if ($('#input-password').val() == $('#input-password-conf').val())
                $('#password-check').removeClass('text-danger').addClass('text-success');
            else
                $('#password-check').removeClass('text-success').addClass('text-danger');
        });

        $('#form-register').submit(function(e) {
            var $form = $(this);
            var urlDest = $form.attr('action');
            var btn = $form.find('button[type=submit]');
            Utils.btnloadState(btn, true);

            var formData = new FormData(this);

            $form.find('input', 'select').each(function() {
                if (typeof($(this).attr('name')) != 'undefined')
                    formData.append($(this).attr('name'), Utils.ltrim($(this).val()));
            });

            $.ajax({
                url: urlDest,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Utils.btnloadState(btn, false);
                    toastr.success('Your account has been created', "Register User Success");
                    $('#registerModal').modal('hide');
                },
                error: function(request, status, error) {
                    Utils.btnloadState(btn, false);
                    if (request.status == '422') {
                        let errors = request['responseJSON']['errors'];
                        Array.prototype.forEach.call(Object.keys(errors), prop => {
                            toastr.error(errors[prop][0], "Fail on Registering User");
                        });
                    } else if (request.status == '413')
                        toastr.error("File to be uploaded is too large", "Fail on Registering User");
                    else
                        toastr.error("Unknown Error", "Fail on Registering User");
                }
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
        console.log('2' + date);
        if (date == null || date == '')
            return '';
        if (isDatetime)
            date = date.substr(0, date.indexOf(' '));
        dateComponents = date.split('-');

        return dateComponents[2] + ' ' + this.monthName[parseInt(dateComponents[1])] + ' ' + dateComponents[0];
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
    }
}

function createColumnSelBtn(colSelection) {
    let colSelectionText = '';
    for (var i = 0; i < colSelection.length; ++i) {
        colSelectionText += '<div class="dropdown-item">' +
            '<input id="colsel-chkbox' + i + '" class="form-check-input colsel-chkbox" type="checkbox" value="' + i + '" ' + (colSelection[i].def ? 'checked' : '') + '/>' +
            '<label class="form-check-label" for="colsel-chkbox' + i + '">' + colSelection[i].lbl + '</label></label></div>';
    }
    return colSelectionText;
}

function createColumn(btnName, visible) {
    if (visible == null || visible == 'undefined')
        visible = true;
    let btn = null;
    switch (btnName) {
        case 'SUBMITTED_DATE':
            btn = Utils.renderColDate("created_at", "row.created_at", true);
            break;
        case 'COMPLETED_DATE':
            btn = Utils.renderColDate("updated_at", "row.updated_at", true);
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
        case 'STATUS':
            btn = { data: "status", width: COL_STATUS_WIDTH + 'px' };
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
        case 'REVISION_VERSION':
            btn = {
                data: "version",
                width: COL_DOCTYPE_WIDTH + 'px',
                render: function(data, type, row) {
                    return '<a href="' + origin_url + '/getSubmissionAttachment/' + row.id + '"><span><i class="fa fa-download mr-2"></i></span>Version ' + row.version + '</a>';
                }
            }
            break;
        case 'VERIFY_STATUS':
            btn = {
                data: 'verify_status',
                width: '150px',
                render: function(data, type, row) {
                    if (row.verify_status == 'STATE_APPROVED')
                        return '<button type="button" class="btn btn-primary">Revise</button>';
                    return 'Verifying';
                }
            };
            break;
    }
    if (btn != null)
        btn.visible = visible;
    return btn;
};

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
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike'], // toggled buttons
    ['blockquote', 'code-block'],
    [
        { 'list': 'ordered' },
        { 'list': 'bullet' }
    ],
    [
        { 'script': 'sub' },
        { 'script': 'super' }
    ], // superscript/subscript
    [
        { 'indent': '-1' },
        { 'indent': '+1' }
    ], // outdent/indent
    [{ 'align': [] }],
    ['clean'] // remove formatting button
];

Date.prototype.isValid = function() {
    // An invalid date object returns NaN for getTime() and NaN is the only
    // object not strictly equal to itself.
    return this.getTime() === this.getTime();
};