<style>
    .q-scroll {
        overflow: auto;
        max-height: 250px;
    }

    .panel-link {
        font-size: 16px;
        font-weight: 500;
        color: #1189e9;
        background: #ebe9e9;
        padding: 10px;
        border-radius: 8px;
    }

    #tableData tr td:nth-child(4),
    #tableData tr td:nth-child(5) {
        background: #e7f7ff !important;
    }

    #tableData tr td:nth-child(6) {
        background: #eeffdb !important;
    }

    .key {
        background: #e6e6e6;
        padding: 3px 10px;
        border-radius: 5px;
    }
</style>

<div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
        <button class="btn btn-success pull-right" id="btnAddSurvey">Create Survey</button>
        <button class="btn btn-default pull-right hide" id="btnRecapSurvey">Recap Semua Survey</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12" id="loadTable"></div>
</div>

<script>
    $(document).ready(function() {
        setLoadFullPage();
        loadTableSurveyList();
    });

    $('#btnRecapSurvey').click(function() {

        var data = {
            action: 'genrateClose'
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function() {

        });

    });

    function loadTableSurveyList() {
        $('#loadTable').html('<table id="tableData" class="table table-bordered table-striped table-centre">' +
            '            <thead>' +
            '            <tr style="background: #eceff1;">' +
            '                <th style="width: 3%;">No</th>' +
            '                <th>Title</th>' +
            '                <th style="width: 5%;">Timer</th>' +
            '                <th style="width: 5%;">Question</th>' +
            '                <th style="width: 5%; color: #2196f3;">Internal</th>' +
            '                <th style="width: 5%; color: #ffa013;">External</th>' +
            '                <th style="width: 5%;">Total</th>' +
            '                <th style="width: 7%;"><i class="fa fa-cog"></i></th>' +
            '                <th style="width: 17%;">Publication Date</th>' +
            '                <th style="width: 5%;">Publication</th>' +
            '                <th style="width: 5%;">Status</th>' +
            '            </tr>' +
            '            </thead>' +
            '            <tbody></tbody>' +
            '        </table>');

        var data = {
            action: 'getListSurvey',
            DepartmentID: sessionIDdepartementNavigation,
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        var dataTable = $('#tableData').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 10,
            "ordering": false,
            "language": {
                "searchPlaceholder": "Question..."
            },
            "ajax": {
                url: url, // json datasource
                data: {
                    token: token
                },
                ordering: false,
                type: "post", // method  , by default get
                error: function() { // error handling
                    loading_modal_hide();
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display", "none");
                }
            }
        });


    }

    $(document).on('click', '.btnShareToPublic', function() {
        var ID = $(this).attr('data-id');
        var data = {
            action: 'setPublicSurvey',
            SurveyID: ID,
            NIP: sessionNIP
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Share to public</h4>');

            var shareLink = base_url_sign_out + 'form/' + jsonResult.Key;

            var tokenLink = jwt_encode({
                ID: ID,
                shareLink: shareLink
            }, 'UAP)(*');


            if (jsonResult.isPublicSurvey == 0 || jsonResult.isPublicSurvey == null) {
                var htmlss = '<div style="text-align: center;">' +
                    '<img src="' + jsonResult.QRCode + '" />' +
                    '<div class="form-group">' +
                    '<div class="panel-link">' + shareLink + '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<a href="' + base_url_js + 'save2pdf/share-survey/' + tokenLink + '" target="_blank" class="btn btn-primary">Download QR Code PDF</a> ' +
                    '<a href="data:image/png;base64,' + jsonResult.Encode + '" download="QR-Code-' + jsonResult.Key + '.png" class="btn btn-primary">Download QR Code Image</a>' +
                    '</div>' +
                    '</div>' +
                    '<input type="hidden" id="status" value="' + jsonResult.Sts + '" />' +
                    '<input type="checkbox" id="myCheck" onclick="myFunction(' + ID + ')" value="' + jsonResult.isPublicSurvey + '"> Share to public' +
                    '<a href="javascript:void(0)" id="btnSelectQuestion" style="display: none;" onclick="questionlist(' + ID + ');"> Select Question</a> ';

            } else {
                var htmlss = '<div style="text-align: center;">' +
                    '<img src="' + jsonResult.QRCode + '" />' +
                    '<div class="form-group">' +
                    '<div class="panel-link">' + shareLink + '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<a href="' + base_url_js + 'save2pdf/share-survey/' + tokenLink + '" target="_blank" class="btn btn-primary">Download QR Code PDF</a> ' +
                    '<a href="data:image/png;base64,' + jsonResult.Encode + '" download="QR-Code-' + jsonResult.Key + '.png" class="btn btn-primary">Download QR Code Image</a>' +
                    '</div>' +
                    '</div>' +
                    '<input type="hidden" id="status" value="' + jsonResult.Sts + '" />' +

                    '<input type="checkbox" id="myCheck" onclick="myFunction(' + ID + ')" value="' + jsonResult.isPublicSurvey + '" checked="checked"> Share to public' +
                    '<a href="javascript:void(0)" id="btnSelectQuestion" style="display: block;" onclick="questionlist(' + ID + ');">Select Question</a> ';
            }



            $('#GlobalModal .modal-footer').html('<button type="button" id="test" class="btn btn-default" data-dismiss="modal">Close</button>');

            $('#GlobalModal .modal-body').html(htmlss);


            $('#GlobalModal').modal({
                'show': true,
                'backdrop': 'static'
            });


        });

    });

    function questionlist(id) {
        var ID = id;
        var data = {
            action: 'showQuestioninSurveyShare',
            SurveyID: ID,
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = "<?php echo base_url('survey/share-public'); ?>";
        $('#GlobalModal').modal('hide');
        $('#GlobalModalXtraLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Show Question</h4>' + '<input type="hidden" id="idsurv" name="ID" value=' + ID + ' />');

        var htmlss = '<div id="panelShowQuestion"></div>';

        $('#GlobalModalXtraLarge .modal-footer').html('<button type="button" id="btnSelect" class="btn btn-primary">Share</button> <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

        $('#GlobalModalXtraLarge .modal-body').html(htmlss);

        loading_page('#panelShowQuestion');

        $('#GlobalModalXtraLarge').modal({
            'show': true,
            'backdrop': 'static'
        });

        $.post(url, {
            token: token
        }, function(jsonResult) {
            $('#GlobalModal').modal('hide');
            if (jsonResult.length > 0) {

                var tr = '';
                $.each(JSON.parse(jsonResult), function(i, v) {

                    tr = tr + '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align: left;"><span class="label label-primary">' + v.Category + '</span>' +
                        ' <span class="label label-success">' + v.Type + '</span>' +
                        '       <div class="q-scroll">' + v.Question + '</div></td>' +
                        '<td>' + v.AverageRate + '</td>' +
                        '</tr>';
                })

            }

            var dataListQuestion = '<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre">' +
                '        <thead>' +
                '        <tr style="background: #eceff1;">' +
                '            <th style="width: 3%">No</th>' +
                '            <th>Question</th>' +
                '            <th style="width: 5%"><input type="checkbox" id="selectAllQuestion" name="selectQuestion"></input> </th>' +
                '        </tr>' +
                '        </thead>' +
                '        <tbody>' + tr + '</tbody>' +
                '    </table>' +
                '</div>';

            setTimeout(function() {
                $('#panelShowQuestion').html(dataListQuestion);
            }, 500);

        });
    }

    async function myFunction($id) {
        var i = document.getElementById("myCheck").checked;
        var x = document.getElementById("btnSelectQuestion");

        var ID = $id;
        if (i == true) {
            x.style.display = "block";

        } else {
            x.style.display = "none";
            var shareAtPublic = 0;

            var cekData = {
                action: 'sharetoPublic',
                ID: ID,
                shareAtPublic: shareAtPublic,
            };


            var token = jwt_encode(cekData, 'UAP)(*');
            var url = "<?php echo base_url('survey/share-public'); ?>";

            $.post(url, {
                token: token
            }, function(jsonResult) {
                toastr.success('Data saved', 'Success');
            });
        }
    }


    $(document).ready(function() {

        $(document).on('click', '#selectAllQuestion', function() {
            if (this.checked) {
                $('.selectQuestion').each(function() {
                    this.checked = true;
                });
            } else {
                $('.selectQuestion').each(function() {
                    this.checked = false;
                });
            }
        });
        $(document).on('click', '.selectQuestion', function() {
            if ($('.selectQuestion:checked').length == $('.selectQuestion').length) {
                $('#selectAllQuestion').prop('checked', true);
            } else {
                $('#selectAllQuestion').prop('checked', false);
            }
        });

        $(document).on('click', '#btnSelect', function() {
            var ID = $("#idsurv").val();
            var sts = $("#status").val();

            if (sts != 1) {
                $('#GlobalModalXtraLarge').modal('hide');
                $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Notification</h4>');
                var htmlss = '<div style="text-align: center;">Unpublish survey status. <br> Please change status to publish before sharing!' +

                    '</div>';

                $('#GlobalModal .modal-footer').html('<button type="button" id="test" class="btn btn-primary" data-dismiss="modal">OK</button>');

                $('#GlobalModal .modal-body').html(htmlss);

                $('#GlobalModal').modal({
                    'show': true,
                    'backdrop': 'static'
                });

            } else {

                var selectCheck = [];
                $('.selectQuestion').each(function() {
                    if ($(this).is(":checked")) {
                        selectCheck.push($(this).val());
                    }

                });
                selectCheck = selectCheck.toString();


                if (selectCheck == '' || selectCheck == null) {
                    toastr.error('Please select question!', 'Error');

                } else {
                    var data = {
                        action: 'selectQuestionSurvey',
                        ID: ID,
                        QuestionID: selectCheck,
                    };
                    var token = jwt_encode(data, 'UAP)(*');
                    var url = "<?php echo base_url('survey/share-public'); ?>";

                    $.post(url, {
                        token: token
                    }, function(jsonResult) {

                        toastr.success('Data saved', 'Success');
                        setTimeout(function() {
                            window.location = "";
                        }, 500);
                    });
                }
            }

        });
    })



    $(document).on('click', '.showQuestionList', function() {

        var ID = $(this).attr('data-id');
        var data = {
            action: 'showQuestionInSurvey',
            SurveyID: ID,
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $('#GlobalModalXtraLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Show Question</h4>');

        var htmlss = '<div id="panelShowQuestion"></div>';

        $('#GlobalModalXtraLarge .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

        $('#GlobalModalXtraLarge .modal-body').html(htmlss);

        loading_page('#panelShowQuestion');

        $('#GlobalModalXtraLarge').modal({
            'show': true,
            'backdrop': 'static'
        });

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {

                var tr = '';
                $.each(jsonResult, function(i, v) {
                    tr = tr + '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align: left;"><span class="label label-primary">' + v.Category + '</span>' +
                        ' <span class="label label-success">' + v.Type + '</span>' +
                        '       <div class="q-scroll">' + v.Question + '</div></td>' +
                        '<td>' + v.AverageRate + '</td>' +
                        '</tr>';
                })

            }

            var dataListQuestion = '<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre">' +
                '        <thead>' +
                '        <tr style="background: #eceff1;">' +
                '            <th style="width: 3%">No</th>' +
                '            <th>Question</th>' +
                '            <th style="width: 13%">Average</th>' +
                '        </tr>' +
                '        </thead>' +
                '        <tbody>' + tr + '</tbody>' +
                '    </table>' +
                '</div>';

            setTimeout(function() {
                $('#panelShowQuestion').html(dataListQuestion);
            }, 500);

        });



    });

    $(document).on('click', '.showAlreadyFillOut', function() {


        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Show Detail Already Fill Out</h4>');

        var htmlss = '<div class="">' +
            '    <table class="table table-bordered table-striped table-centre" id="tableShowUserAlreadyFillOut">' +
            '        <thead>' +
            '        <tr>' +
            '            <th style="width: 1%;">No</th>' +
            '            <th>User</th>' +
            '            <th style="width: 10%;">Type</th>' +
            '            <th style="width: 25%;">Entred At</th>' +
            '        </tr>' +
            '        </thead>' +
            '    </table>' +
            '</div>';

        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

        $('#GlobalModal .modal-body').html(htmlss);

        var ID = $(this).attr('data-id');
        var Status = $(this).attr('data-status');
        var Type = $(this).attr('data-type');
        var data = {
            action: 'showUserAlreadyFill',
            SurveyID: ID,
            Status: Status,
            Type: Type
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';


        var dataTable = $('#tableShowUserAlreadyFillOut').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 10,
            "ordering": false,
            "language": {
                "searchPlaceholder": "Username, Name..."
            },
            "ajax": {
                url: url, // json datasource
                data: {
                    token: token
                },
                ordering: false,
                type: "post", // method  , by default get
                error: function() { // error handling
                    loading_modal_hide();
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display", "none");
                }
            }
        });

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });

    });

    $('#btnAddSurvey').click(function() {
        updateSurvey('');
    });

    $(document).on('click', '.btnEditSurvey', function() {
        var ID = $(this).attr('data-id');
        var data = {
            action: 'getOneDataSurvey',
            ID: ID
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {
                localStorage.setItem('dataSurvey', JSON.stringify(jsonResult[0]));
                updateSurvey(ID);
            }
        });


    });

    function updateSurvey(ID) {

        var Title = '';
        var StartDate = '';
        var EndDate = '';
        var Note = '';
        var useTimer = '';
        var TimerType = '';

        var btnSave = '<button class="btn btn-success" id="btnCreateSurvey">Create</button>';
        var formDisabled = '';

        var dataUpdate = '';

        if (ID != '') {
            var dataSurvey = localStorage.getItem('dataSurvey');
            var d = JSON.parse(dataSurvey);
            dataUpdate = JSON.stringify(d);
            Title = d.Title;
            StartDate = d.StartDate;
            EndDate = d.EndDate;
            Note = d.Note;

            if (d.Status != '0') {
                btnSave = '';
                formDisabled = 'disabled'
            } else {
                btnSave = '<button class="btn btn-success" id="btnCreateSurvey">Edit</button>';
            }

            useTimer = (d.useTimer == '1') ? 'checked' : '';
            TimerType = (d.TimerType != null && d.TimerType != '') ? d.TimerType : '';

        }


        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Create Survey</h4><textarea id="dataValueSurvey" hidden>' + dataUpdate + '</textarea>');

        var formUseTimer = /*html*/ `<div class="well"><div clss="">
        <div class="row">
        <div class="col-md-12">
            <div class="checkbox" ${formDisabled}>
                <label>
                <input type="checkbox" id="useTimer" ${useTimer} ${formDisabled}> Use Timer
                </label>
            </div>
            <div id="view_TimerType"></div>
            <div id="view_TimerOption"></div>
        </div>
        </div>
        </div></div>`;

        var htmlss = '<div class="form-group">' +
            '                    <label>Title</label>' +
            '                    <input id="formSurveyID" class="hide" ' + formDisabled + ' value="' + ID + '">' +
            '                    <input id="formSurveyTitle" class="form-control" ' + formDisabled + ' value="' + Title + '">' +
            '                </div>' +
            '                <div class="form-group">' +
            '                    <div class="row">' +
            '                        <div class="col-md-6">' +
            '                            <label>Start</label>' +
            '                            <input id="formSurveyStartDate" ' + formDisabled + ' value="' + StartDate + '" class="form-control range-date-survey" type="date">' +
            '                        </div>' +
            '                        <div class="col-md-6">' +
            '                            <label>End</label>' +
            '                            <input id="formSurveyEndDate" ' + formDisabled + ' value="' + EndDate + '" class="form-control range-date-survey" type="date">' +
            '                        </div>' +
            '                    </div>' +
            '                </div>' + formUseTimer +
            '                <div class="form-group">' +
            '                    <label>Note</label>' +
            '                    <textarea class="form-control" id="formSurveyNote" ' + formDisabled + ' rows="2">' + Note + '</textarea>' +
            '                </div>';

        $('#GlobalModal .modal-body').html(htmlss);

        if (useTimer != '') {
            showTimerType(TimerType, formDisabled);
        }



        $('#formSurveyTitle,#formSurveyStartDate,#formSurveyEndDate,#formSurveyNote').css('color', '#333');

        $('#GlobalModal .modal-footer').html('' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' + btnSave +
            '' +
            '');

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });
    }

    $(document).on('change', '#useTimer', function() {
        showTimerType('', '');
    });

    function showTimerType(v, formDisabled) {
        if ($('#useTimer').is(':checked')) {
            $('#view_TimerType').html(`<div class="form-group">
            <label>Timer Type</label>
            <select id="TimerType" class="form-control" style="width:200px;" ${formDisabled}>
                <option value="" disabled selected>--- Select Timer Type ---</option>
                <option value="flexible" ${(v=='flexible') ? 'selected' : ''}>Flexible Timer</option>
                <option value="fixed" ${(v=='fixed') ? 'selected' : ''}>Fixed Timer</option>
            </select></div>`);
            if (v != '') {
                showDateOption(formDisabled);
            }
        } else {
            $('#view_TimerType').empty();
        }
    }

    $(document).on('change', '#TimerType', function() {
        showDateOption('');
    });

    $(document).on('change', '.range-date-survey', function() {
        showDateOption('');
    });

    function showDateOption(formDisabled) {

        var dataValueSurvey = $('#dataValueSurvey').val();
        var Duration = '';

        var formInputDate = '';
        var labelDate = 'Select Date';
        var viewDate = '';
        var TimerStart = '00:00';
        var TimerEnd = '00:00';
        if (dataValueSurvey != '') {
            var d = JSON.parse(dataValueSurvey);
            Duration = (d.Duration != null && d.Duration != '') ? d.Duration : '';
            TimerStart = (d.TimerStart != null && d.TimerStart != '') ?
                d.TimerStart.substring(0, 5) : '00:00';
            TimerEnd = (d.TimerEnd != null && d.TimerEnd != '') ?
                d.TimerEnd.substring(0, 5) : '00:00';

            labelDate = 'Select Date to Update';
            formInputDate = (d.TimerDate != null && d.TimerDate != '') ? d.TimerDate : '';
            viewDate = (formInputDate != '') ? moment(formInputDate).format('DD MMMM YYYY') : '';
        }

        var TimerType = $('#TimerType').val();
        if (TimerType == 'flexible') {
            $('#view_TimerOption').html(`<div class="form-group">
            <label>Duration</label>
            <div class="input-group" style="width:200px;">
                <input type="number" id="Duration" value="${Duration}" ${formDisabled} class="form-control" aria-describedby="basic-addon2">
                <span class="input-group-addon" id="basic-addon2">minutes</span>
            </div>
            </div>`);
        } else if (TimerType == 'fixed') {

            var formSurveyStartDate = $('#formSurveyStartDate').val();
            var formSurveyEndDate = $('#formSurveyEndDate').val();

            if (formSurveyStartDate != '' && formSurveyStartDate != null &&
                formSurveyEndDate != '' && formSurveyEndDate != null) {

                $('#view_TimerOption').html(`<div class="form-group">
                        <label>${labelDate}</label>
                        <input type="text" id="formDate" style="width:200px;color: #333;background: #fff;" readonly ${formDisabled} class="form-control form-exam form-datetime" placeholder="${viewDate}">
                        <input id="formInputDate" value="${formInputDate}" hidden readonly>
                        <p>${viewDate}</p>
                    </div>
                    <div class="form-group">
                        <div class="row">
                        <div class="col-md-6">
                            <label>Start</label>
                            <div id="inputStart" class="input-group">
                                        <input data-format="hh:mm" type="text" ${formDisabled} id="TimerStart" class="form-control form-exam" value="${TimerStart}"/>
                                        <span class="add-on input-group-addon">
                                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>End</label>
                            <div id="inputEnd" class="input-group">
                                        <input data-format="hh:mm" type="text" ${formDisabled} id="TimerEnd" class="form-control form-exam" value="${TimerEnd}"/>
                                        <span class="add-on input-group-addon">
                                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                        </div>
                    </div>`);

                var splitStart = formSurveyStartDate.split('-');
                var C_dateStart_Y = splitStart[0].trim();
                var C_dateStart_M = parseInt(splitStart[1].trim()) - 1;
                var C_dateStart_D = splitStart[2].trim();

                var splitEnd = formSurveyEndDate.split('-');
                var C_dateEnd_Y = splitEnd[0].trim();
                var C_dateEnd_M = parseInt(splitEnd[1].trim()) - 1;
                var C_dateEnd_D = splitEnd[2].trim();

                $('#formDate').datepicker({
                    showOtherMonths: true,
                    autoSize: true,
                    dateFormat: 'dd MM yy',
                    minDate: new Date(C_dateStart_Y, C_dateStart_M, C_dateStart_D),
                    maxDate: new Date(C_dateEnd_Y, C_dateEnd_M, C_dateEnd_D),
                    onSelect: function() {
                        var data_date = $(this).val().split(' ');
                        var momentDate = moment(data_date[2] + '-' + (parseInt(convertDateMMtomm(data_date[1])) + 1) + '-' + data_date[0]);
                        var CustomMoment = momentDate.day();
                        var day = (CustomMoment == 0) ? 7 : CustomMoment;
                        $('#formInputDate').val(momentDate.format('YYYY-MM-DD'));
                    }
                });
                $('#inputStart,#inputEnd').datetimepicker({
                    pickDate: false,
                    pickSeconds: false
                });

            } else {
                alert('Please set a start and end date!');
                $('#view_TimerOption').empty();
                $('#TimerType').val('');
            }

        } else {
            $('#view_TimerOption').empty();
        }

    }

    $(document).on('click', '.btnPublishSurvey', function() {
        var ID = $(this).attr('data-id');
        updateStatusSurvey(ID, '1',
            'After the survey is published you cannot withdraw it, are you sure?');
    });

    $(document).on('click', '.btnCloseSurvey', function() {
        var ID = $(this).attr('data-id');
        updateStatusSurvey(ID, '2',
            'If the survey is closed, the user cannot fill out your survey, are you sure?');

    });

    function updateStatusSurvey(ID, Status, msg) {
        if (confirm(msg)) {
            var data = {
                action: 'setStatusSurvey',
                ID: ID,
                Status: Status,
                NIP: sessionNIP
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'apimenu/__crudSurvey';

            $.post(url, {
                token: token
            }, function(jsonResult) {

                loadTableSurveyList();

                // $('#viewStatusSurvey_'+ID).html(jsonResult.Label);
                //
                // if(Status==1){
                //     $('#li_btn_Publish_'+ID).remove();
                //     $('#li_btn_Close_'+ID).removeClass('hide');
                // } else if(Status==2){
                //     $('#li_btn_Publish_'+ID+',#li_btn_Close_'+ID).remove();
                // }



            });

        }
    }

    $(document).on('click', '#btnCreateSurvey', function() {
        var formSurveyTitle = $('#formSurveyTitle').val();
        var formSurveyStartDate = $('#formSurveyStartDate').val();
        var formSurveyEndDate = $('#formSurveyEndDate').val();
        var formSurveyNote = $('#formSurveyNote').val();

        var formTimer = false;
        // cek timer used
        var useTimer = ($('#useTimer').is(':checked')) ? '1' : '0';
        var TimerType = null;
        var TimerDate = null;
        var TimerStart = null;
        var TimerEnd = null;
        var Duration = null;
        if (useTimer == '1') {
            TimerType = $('#TimerType').val();

            if (TimerType == 'flexible') {
                var d_Duration = $('#Duration').val();
                if (d_Duration != '' && d_Duration != null) {
                    Duration = d_Duration;
                    formTimer = true;
                }
            } else if (TimerType == 'fixed') {

                var d_formDate = $('#formDate').val();
                var d_TimerStart = $('#TimerStart').val();
                var d_TimerEnd = $('#TimerEnd').val();

                if (d_formDate != '' && d_formDate != null && d_TimerStart != '00:00' && d_TimerStart != '' && d_TimerStart != null &&
                    d_TimerEnd != '00:00' && d_TimerEnd != '' && d_TimerEnd != null) {
                    TimerDate = $('#formInputDate').val();
                    TimerStart = d_TimerStart;
                    TimerEnd = d_TimerEnd;
                    formTimer = true;
                }

            }

        } else {
            formTimer = true;
        }

        if (formSurveyTitle != '' && formSurveyTitle != null &&
            formSurveyStartDate != '' && formSurveyStartDate != null &&
            formSurveyEndDate != '' && formSurveyEndDate != null && formTimer) {

            loading_button('#btnCreateSurvey');


            var formSurveyID = $('#formSurveyID').val();
            var data = {
                action: 'updateSurvey',
                ID: (formSurveyID != '' && formSurveyID != null) ? formSurveyID : '',
                NIP: sessionNIP,
                dataSurvey: {
                    DepartmentID: sessionIDdepartementNavigation,
                    Title: formSurveyTitle,
                    StartDate: formSurveyStartDate,
                    EndDate: formSurveyEndDate,
                    Note: formSurveyNote,
                    useTimer: useTimer,
                    TimerType: TimerType,
                    TimerDate: TimerDate,
                    TimerStart: TimerStart,
                    TimerEnd: TimerEnd,
                    Duration: Duration,
                }
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'apimenu/__crudSurvey';

            $.post(url, {
                token: token
            }, function(jsonResult) {
                toastr.success('Data saved', 'Success');
                loadTableSurveyList();
                setTimeout(function() {
                    $('#GlobalModal').modal('hide');
                }, 500);

            });

        } else {
            toastr.warning('All form are required', 'Warning');
        }
    });

    $(document).on('click', '.btnManageTarget', function() {
        var ID = $(this).attr('data-id');

        var data = {
            action: 'getDataTargetSurvey',
            ID: ID
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            // 0 = Unpublish, 1 = Publish, 2 = Close
            var btnSave = (parseInt(jsonResult.Status) <= 0) ?
                '<button class="btn btn-success" id="btnSaveTarget" data-id="' + ID + '">Save</button>' : '';

            var disabledForm = (parseInt(jsonResult.Status) <= 0) ? '' : 'disabled';

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Manage Target</h4>');

            // 1 = All emp, 2 = Hanya dosen, 3 = Hanya tenaga pendidik (selain dosen)

            var htmlss = '<div class="panel panel-default">' +
                '            <div class="panel-heading">' +
                '                <h4 class="panel-title">Target Employees</h4>' +
                '            </div>' +
                '            <div class="panel-body">' +
                '                <div style="color: blue;border-bottom: 1px solid #ccc;padding-bottom: 10px;margin-bottom: 10px;">' +
                '                    <label class="radio-inline">' +
                '                        <input type="radio" name="survUserEmp" ' + disabledForm + ' value="-1" checked> Bukan untuk Dosen & Tenaga Pendidik' +
                '                    </label>' +
                '                </div>' +
                '                <div>' +
                '                    <label class="radio-inline">' +
                '                        <input type="radio" name="survUserEmp" ' + disabledForm + ' value="1"> Semua Dosen & Tenga Pendidik' +
                '                    </label>' +
                '                </div>' +
                '                <div>' +
                '                    <label class="radio-inline">' +
                '                        <input type="radio" name="survUserEmp" ' + disabledForm + ' value="2"> Semua Dosen (selain tenaga pendidik)' +
                '                    </label>' +
                '                </div>' +
                '                <div>' +
                '                    <label class="radio-inline">' +
                '                        <input type="radio" name="survUserEmp" ' + disabledForm + ' value="3"> Semua Tenga Pendidik (selain dosen)' +
                '                    </label>' +
                '                </div>' +
                '            </div>' +
                '        </div>' +
                '' +
                '' +
                '<div class="panel panel-default">' +
                '            <div class="panel-heading">' +
                '                <h4 class="panel-title">Target Student</h4>' +
                '            </div>' +
                '            <div class="panel-body">' +
                '                <div style="margin-bottom: 15px;">' +
                '                    <div style="color: blue;border-bottom: 1px solid #ccc;padding-bottom: 10px;margin-bottom: 10px;">' +
                '                        <label class="radio-inline">' +
                '                            <input type="radio" name="surv_std_TypeUser" ' + disabledForm + ' value="-1" checked> Bukan untuk mahasiswa' +
                '                        </label>' +
                '                    </div>' +
                '                    <div>' +
                '                        <label class="radio-inline">' +
                '                            <input type="radio" name="surv_std_TypeUser" ' + disabledForm + ' value="1"> Semua mahasiswa' +
                '                        </label>' +
                '                    </div>' +
                '                    <div>' +
                '                        <label class="radio-inline">' +
                '                            <input type="radio" name="surv_std_TypeUser" ' + disabledForm + ' value="2"> Semua mahasiswa aktif' +
                '                        </label>' +
                '                    </div>' +
                '                    <div>' +
                '                        <label class="radio-inline">' +
                '                            <input type="radio" name="surv_std_TypeUser" ' + disabledForm + ' value="3"> Semua Alumni' +
                '                        </label>' +
                '                    </div>' +
                '                    <div>' +
                '                        <label class="radio-inline">' +
                '                            <input type="radio" name="surv_std_TypeUser" ' + disabledForm + ' value="0"> Custom' +
                '                        </label>' +
                '                    </div>' +
                '                </div>' +
                '                <div id="panelCustomStd" class="hide">' +
                '                    <div class="well" id="panelAddCustomStd">' +
                '                        <div class="row">' +
                '                            <div class="col-md-3">' +
                '                                <label>Class Of</label>' +
                '                                <select class="form-control" id="formUsr_ClassOf"></select>' +
                '                            </div>' +
                '                            <div class="col-md-5">' +
                '                                <label>Prodi</label>' +
                '                                <select class="form-control" id="formUsr_ProdiID"></select>' +
                '                            </div>' +
                '                            <div class="col-md-4">' +
                '                                <label>Status Student</label>' +
                '                                <select class="form-control" id="formUsr_StatusStudentID"></select>' +
                '                            </div>' +
                '                        </div>' +
                '                        <div class="row" style="margin-top: 10px;">' +
                '                            <div class="col-md-12 text-right">' +
                '                                <button id="btnAddCustomTargetStd" data-id="' + ID + '" class="btn btn-sm btn-primary">Add</button>' +
                '                            </div>' +
                '                        </div>' +
                '                    </div>' +
                '                   <input class="hide" id="dataStatusSurvey" value="' + jsonResult.Status + '"/>' +
                '                    <div id="viewTableTargetCustom"></div>' +
                '                </div>' +
                '' +
                '            </div>' +
                '        </div>';

            $('#GlobalModal .modal-body').html(htmlss);

            // Employees
            var dataEmployee = jsonResult.Employee;
            if (dataEmployee.length > 0) {
                $('input[name=survUserEmp][value=' + dataEmployee[0].TypeUser + ']').attr('checked', true);
            }

            var dataStudent = jsonResult.Student;
            if (dataStudent.length > 0) {
                $('input[name=surv_std_TypeUser][value=' + dataStudent[0].TypeUser + ']').attr('checked', true);
                if (dataStudent[0].TypeUser == 0 || dataStudent[0].TypeUser == '0') {
                    $('#panelCustomStd').removeClass('hide');
                } else {
                    $('#panelCustomStd').addClass('hide');
                }
                loadDataTargetCustomStudent(ID);
            }

            if ((parseInt(jsonResult.Status) <= 0)) {
                loadSelectOptionClassOf_DESC('#formUsr_ClassOf', '', 'HideLabel');
                loadSelectOptionBaseProdi('#formUsr_ProdiID', '');
                loadSelectOptionStatusStudent('#formUsr_StatusStudentID', 3);
            } else {
                $('#panelAddCustomStd').remove();
            }



            $('#GlobalModal .modal-footer').html('' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' + btnSave +
                '' +
                '');

            $('#GlobalModal').on('shown.bs.modal', function() {
                $('#formSimpleSearch').focus();
            });

            $('#GlobalModal').modal({
                'show': true,
                'backdrop': 'static'
            });

        });

    });

    function loadDataTargetCustomStudent(ID) {

        var data = {
            action: 'getDataTargetCustomStudent',
            ID: ID
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        loading_page('#viewTableTargetCustom');
        var dataStatusSurvey = $('#dataStatusSurvey').val();

        $.post(url, {
            token: token
        }, function(jsonResult) {

            var tr = '';
            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {

                    var btnRemove = (parseInt(dataStatusSurvey) <= 0) ?
                        '<button class="btn btn-danger btn-sm removeCustomTargetStudent" ' +
                        'data-id="' + v.TargetUserID + '" data-id-survey="' + ID + '"><i class="fa fa-trash"></i></button>' :
                        '-';

                    tr = tr + '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td style="text-align: left;"><b>' + v.Prodi + '</b><br/>' + v.ClassOf + ' - ' + v.Description + '</td>' +
                        '<td>' + btnRemove + '</td>' +
                        '</tr>';
                });
            } else {
                tr = '<tr><td colspan="3">No data</td></tr>'
            }

            setTimeout(function() {
                $('#viewTableTargetCustom').html('<div class="table-responsive">' +
                    '    <table class="table table-bordered table-striped table-centre">' +
                    '        <thead>' +
                    '        <tr>' +
                    '            <th style="width: 3%;">No</th>' +
                    '            <th>Program Study</th>' +
                    '            <th style="width: 10%;"><i class="fa fa-cog"></i></th>' +
                    '        </tr>' +
                    '        </thead>' +
                    '       <tbody>' + tr + '</tbody>' +
                    '    </table>' +
                    '</div>');
            }, 500);
        });

    }

    $(document).on('change', 'input[type=radio][name="surv_std_TypeUser"]', function() {
        var val = $('input[type=radio][name="surv_std_TypeUser"]:checked').val();
        if (val == '0') {
            $('#panelCustomStd').removeClass('hide');
        } else {
            $('#panelCustomStd').addClass('hide');
        }
    });

    $(document).on('click', '#btnSaveTarget', function() {

        loading_button('#btnSaveTarget');

        var ID = $(this).attr('data-id');
        var survUserEmp = $('input[name=survUserEmp]:checked').val();
        var survUserStd = $('input[type=radio][name="surv_std_TypeUser"]:checked').val();
        var data = {
            action: 'updateTargetSurvey',
            ID: ID,
            surv_survey_usr_emp: survUserEmp,
            surv_survey_usr_std: survUserStd,
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            toastr.success('Data saved', 'Success');

            setTimeout(function() {
                $('#btnSaveTarget').html('Save').prop('disabled', false);
            }, 500);

        });
    });

    $(document).on('click', '#btnAddCustomTargetStd', function() {

        var ID = $(this).attr('data-id');

        var formUsr_ClassOf = $('#formUsr_ClassOf').val();
        var formUsr_ProdiID = $('#formUsr_ProdiID').val();
        var formUsr_StatusStudentID = $('#formUsr_StatusStudentID').val();

        if (formUsr_ClassOf != '' && formUsr_ClassOf != null &&
            formUsr_ProdiID != '' && formUsr_ProdiID != null &&
            formUsr_StatusStudentID != '' && formUsr_StatusStudentID != null) {

            var ClassOf = formUsr_ClassOf;
            var ProdiID = formUsr_ProdiID.split('.')[0];
            var StatusStudentId = formUsr_StatusStudentID;

            var data = {
                action: 'setDataTargetUsrtStdDetail',
                ID: ID,
                dataForm: {
                    ClassOf: ClassOf,
                    ProdiID: ProdiID,
                    StatusStudentId: StatusStudentId
                }
            };
            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'apimenu/__crudSurvey';

            $.post(url, {
                token: token
            }, function(jsonResult) {
                if (jsonResult.Status == 1) {
                    loadDataTargetCustomStudent(ID);
                    toastr.success('Data saved', 'Success');
                } else {
                    toastr.warning('Data has been entered', 'Warning');
                }
            });


        } else {
            toastr.warning('All form are required', 'Warning');
        }

    });

    $(document).on('click', '.removeCustomTargetStudent', function() {
        if (confirm('Are you sure?')) {

            $('.removeCustomTargetStudent').prop('disabled', true);

            var TargetUserID = $(this).attr('data-id');
            var ID = $(this).attr('data-id-survey');
            var data = {
                action: 'removeDataFromTargetUsrtStdDetail',
                ID: TargetUserID
            };
            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'apimenu/__crudSurvey';

            $.post(url, {
                token: token
            }, function(jsonResult) {
                loadDataTargetCustomStudent(ID);
                toastr.success('Removed Data', 'Success');
            });
        }
    });


    $(document).on('click', '.btnAddNewDate', function() {

        var ID = $(this).attr('data-id');

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Add New Date</h4>');

        var htmlss = '<div class="form-group">' +
            '                    <div class="row">' +
            '                        <div class="col-md-6">' +
            '                            <label>Start</label>' +
            '                            <input id="formSurveyStartDate" class="form-control" type="date">' +
            '                           <input id="formSurveyID" class="hide" value="' + ID + '">' +
            '                        </div>' +
            '                        <div class="col-md-6">' +
            '                            <label>End</label>' +
            '                            <input id="formSurveyEndDate" class="form-control" type="date">' +
            '                        </div>' +
            '                    </div>' +
            '                </div>';

        $('#GlobalModal .modal-body').html(htmlss);

        $('#formSurveyTitle,#formSurveyStartDate,#formSurveyEndDate,#formSurveyNote').css('color', '#333');

        $('#GlobalModal .modal-footer').html('' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
            '<button class="btn btn-success" id="btnSubmitNewDate">Submit</button>');

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });


        $(document).off('click', '#btnSubmitNewDate').on('click', '#btnSubmitNewDate', function() {

            loading_button('#btnSubmitNewDate');

            var formSurveyStartDate = $('#formSurveyStartDate').val();
            var formSurveyEndDate = $('#formSurveyEndDate').val();

            if (formSurveyStartDate != '' && formSurveyStartDate != null &&
                formSurveyEndDate != '' && formSurveyEndDate != null) {

                var data = {
                    action: 'createNewDateSurvey',
                    SurveyID: ID,
                    StartDate: formSurveyStartDate,
                    EndDate: formSurveyEndDate,
                    NIP: sessionNIP
                };
                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'apimenu/__crudSurvey';

                $.post(url, {
                    token: token
                }, function(jsonResult) {
                    loadTableSurveyList();
                    setTimeout(function() {
                        $('#GlobalModal').modal('hide');
                    }, 500);
                });

            } else {
                toastr.warning('Range of date are required', 'Warning');
            }
        });

    });

    $(document).on('click', '.showAllPublication', function() {

        var ID = $(this).attr('data-id');

        var data = {
            action: 'dataAllPublication',
            SurveyID: ID
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            console.log(jsonResult);

            var tr = '';

            $.each(jsonResult, function(i, v) {

                var Start = moment(v.StartDate).format('DD MMM YYYY');
                var End = moment(v.EndDate).format('DD MMM YYYY');

                var tokenRecap = jwt_encode({
                    SurveyID: v.SurveyID,
                    RecapID: v.RecapID
                }, 'UAP)(*');

                var hideShare = (parseInt(v.Status) == 2) ? '' : 'hide';

                tr = tr + '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + v.Question + '</td>' +
                    '<td>' + Start + ' - ' + End + '</td>' +
                    '<td>' + v.TotalAnswer + '</td>' +
                    '<td>' +
                    '   <a href="' + base_url_js + 'save2excel/survey/' + tokenRecap + '" class="btn btn-sm btn-default"><i class="fa fa-download"></i></a>' +
                    '   <button class="btn btn-sm btn-default ' + hideShare + '" role="button" data-toggle="collapse" href="#collapseExample_' + i + '" aria-expanded="false" aria-controls="collapseExample_' + i + '"><i class="fa fa-mail-forward"></i></button>' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td colspan="5" class="collapse" id="collapseExample_' + i + '">' +
                    '<div class="row" style="margin-top: 15px;margin-bottom: 15px;">' +
                    '   <div class="col-md-5">' +
                    '       <input placeholder="Name..." class="form-control" id="formName_' + i + '" /> ' +
                    '       <input value="' + tokenRecap + '" id="formtokenRecap_' + i + '" class="hide" /> ' +
                    '   </div>' +
                    '   <div class="col-md-5">' +
                    '       <input placeholder="Email..." id="formEmail_' + i + '" class="form-control" /> ' +
                    '   </div>' +
                    '   <div class="col-md-2">' +
                    '       <button class="btn btn-sm btn-default btn-block btnSentMail" data-i="' + i + '">Sent</button>' +
                    '   </div>' +
                    '</div>' +
                    '<div class="row" style="margin-bottom: 15px;">' +
                    '   <div class="col-md-12">' +
                    '       <div class="text-right">' +
                    '           <a role="button" data-toggle="collapse" ' +
                    '                   data-i="' + i + '" ' +
                    '                   href="#detailHistory_' + i + '" ' +
                    '                   aria-expanded="false" ' +
                    '                   aria-controls="detailHistory_' + i + '" class="showListSendMail">Email delivery history</a>' +
                    '       </div>' +
                    '       <div class="collapse" id="detailHistory_' + i + '"></div>' +
                    '   </div>' +
                    '</div>' +
                    '</td>' +
                    '</tr>';
            });

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">List Of Publication Dates</h4>');

            var htmlss = '<table class="table table-bordered table-centre table-striped">' +
                '<thead>' +
                '<tr style="background: #eceff1;">' +
                '   <td style="width: 1%;">No</td>' +
                '   <td style="width: 5%;">Question</td>' +
                '   <td>Publication Date</td>' +
                '   <td style="width: 5%;"><i class="fa fa-users"></i></td>' +
                '   <td style="width: 25%;">Report</td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' + tr + '</tbody>' +
                '</table>';

            $('#GlobalModal .modal-body').html(htmlss);

            $('#formSurveyTitle,#formSurveyStartDate,#formSurveyEndDate,#formSurveyNote').css('color', '#333');

            $('#GlobalModal .modal-footer').html('' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

            $('#GlobalModal').modal({
                'show': true,
                'backdrop': 'static'
            });

        });

    });

    $(document).on('click', '.showListSendMail', function() {
        var i = $(this).attr('data-i');
        var formtokenRecap = $('#formtokenRecap_' + i).val();
        var d = jwt_decode(formtokenRecap, 'UAP)(*');
        console.log(d);
        loadList(i, d.SurveyID, d.RecapID);
    });

    function loadList(i, SurveyID, RecapID) {
        var data = {
            action: 'getListHistorySendEmail',
            SurveyID: SurveyID,
            RecapID: RecapID
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $('#detailHistory_' + i).empty();

        $.post(url, {
            token: token
        }, function(jsonResult) {

            var li = '';
            $.each(jsonResult, function(i, v) {
                var dateSent = moment(v.EntredAt).format('DD MMM YYYY HH.mm');
                li = li + '<li><div style="text-align: left;margin-bottom: 10px;">' +
                    '<b>' + v.Name + '</b> (' + v.Email + ')' +
                    '<br/><span style="color: #a9a9a9;">Sent by : ' + v.EntredByName + ' | ' + dateSent + '</span>' +
                    '</div>' +
                    '</li>';
            });

            $('#detailHistory_' + i).html('<ol>' + li + '</ol>');

        });
    }

    $(document).on('click', '.btnSentMail', function() {
        var i = $(this).attr('data-i');
        var formName = $('#formName_' + i).val();
        var tokenRecap = $('#formtokenRecap_' + i).val();
        var formEmail = $('#formEmail_' + i).val();

        if (formName != '' && formName != null &&
            tokenRecap != '' && tokenRecap != null &&
            formEmail != '' && formEmail != null) {

            $('.btnSentMail').prop('disabled', true);
            loading_buttonSm('.btnSentMail[data-i="' + i + '"]');

            var data = {
                action: 'shareRecap2email',
                tokenRecap: tokenRecap,
                Name: formName,
                Email: formEmail,
                NIP: sessionNIP,
                SentBy: sessionName,
                SentAt: getDateTimeNow()
            };
            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'apimenu/__crudSurvey';

            $.post(url, {
                token: token
            }, function(jsonResult) {

                loadList(i, jsonResult.SurveyID, jsonResult.RecapID);

                setTimeout(function() {
                    $('.btnSentMail').prop('disabled', false);
                    $('.btnSentMail[data-i="' + i + '"]').html('Sent');
                }, 500);

            });


        } else {
            toastr.warning('Name & Email are required', 'Warning');
        }
    });
</script>