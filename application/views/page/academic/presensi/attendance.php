<style>
    .form-attd[readonly] {
        cursor: cell;
        background-color: #fff;
        color: #333;
    }
</style>

<!--<h1>Menu</h1>-->
<!--<hr/>-->


<div class="row">
    <div class="col-md-6 col-md-offset-2">
        <div class="well">
            <div class="row">
                <div class="col-xs-4">
                    <select id="filterSemester" class="form-control filter-presensi"></select>
                </div>
                <div class="col-xs-8">
                    <div id="viewGroup"></div>
                </div>

            </div>
        </div>

        <hr />
    </div>
    <div class="col-md-2">
        <div class="well">
            <button class="btn btn-default btn-default-danger btn-block" id="btnBlaskAttendance">Blast Attendance</button>
        </div>
        <hr />
    </div>
</div>


<div class="row">
    <div id="divpagePresensi">
        <div style="text-align:center;">
            <h3 style="color: #ccc;font-weight: bold;">-- Data Not Yet --</h3>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#filterSemester').empty();
        loSelectOptionSemester('#filterSemester', '');



        window.loadFirstTime = setInterval(function() {
            loadGroupDiv();
        }, 1000);

    });

    $('#btnBlaskAttendance').click(function() {

        var filterSemester = $('#filterSemester').val();

        if (filterSemester != '' && filterSemester != null) {

            $('#NotificationModal .modal-body').html('<div>' +
                '<div style="text-align: center;">' +
                '<h4>Blast to All Students</h4>' +
                '<hr/>' +
                '</div> ' +
                '<div class="form-group">' +
                '<label>Select Session</label>' +
                '<select class="form-control" id="formBlastSession"></select>' +
                '</div>' +
                '<div class="form-group">' +
                '<label>Select Status</label>' +
                '<br/>' +
                '<label class="radio-inline" style="color: green;">' +
                '  <input type="radio" name="formBlastStatus" value="1" checked> Present' +
                '</label>' +
                '<label class="radio-inline" style="color: red;">' +
                '  <input type="radio" name="formBlastStatus" value="2"> Absent' +
                '</label>' +
                '</div>' +
                '<hr/>' +
                '<div style="text-align: right;">' +
                '<button type="button" class="btn btn-default" style="margin-right: 5px;" data-dismiss="modal">Close</button> ' +
                '<button type="button" class="btn btn-primary" id="btnBlastSubmit">Submit</button> ' +
                '</div>' +
                '</div>');

            var arrNotDC = [1, 2, 14];
            for (var i = 1; i <= 14; i++) {
                var ds = ($.inArray(i, arrNotDC) != -1) ? '' : 'disabled';
                $('#formBlastSession').append('<option value="' + i + '" ' + ds + '>' + i + '</option>');
            }

            $('#NotificationModal').modal('show');

        }

    });

    $(document).on('click', '#btnBlastSubmit', function() {
        var filterSemester = $('#filterSemester').val();
        var formBlastSession = $('#formBlastSession').val();
        var formBlastStatus = $('input[type=radio][name=formBlastStatus]:checked').val();

        console.log(filterSemester);
        console.log(formBlastSession);
        console.log(formBlastStatus);

        if (filterSemester != '' && filterSemester != null &&
            formBlastSession != '' && formBlastSession != null &&
            formBlastStatus != '' && formBlastStatus != null) {

            loading_buttonSm('#btnBlastSubmit');

            var data = {
                action: 'blastAttendance',
                SemesterID: filterSemester.split('.')[0],
                Meet: formBlastSession,
                Status: formBlastStatus
            };

            var token = jwt_encode(data, 'UAP)(*');

            var url = base_url_js + 'api/__crudAttendance';

            $.post(url, {
                token: token
            }, function(result) {
                toastr.success('Blast Attendance', 'Success');
                setTimeout(function() {
                    $('#NotificationModal').modal('hide');
                }, 100);
            });


        }
    });

    $(document).on('change', '#filterClassGroup', function() {
        checkPage();
    });

    // Change Smester ID
    $(document).on('change', '#filterSemester', function() {
        // checkPage();
        loadGroupDiv();
        $('#divpagePresensi').html('<div style="text-align:center;"><h3 style="color: #ccc;font-weight: bold;">-- Select Class Group --</h3></div>');
    });

    function loadGroupDiv() {
        $('#viewGroup').html('');
        $('#viewGroup').html('<select class="select2-select-00 full-width-fix"' +
            '                                size="5" id="filterClassGroup"><option></option></select>');
        var filterSemester = $('#filterSemester').val();
        if (filterSemester != '' && filterSemester != null) {
            var SemesterID = filterSemester.split('.')[0];
            loadSelectOptionClassGroupAttendance(SemesterID, '#filterClassGroup', '');
            $('#filterClassGroup').select2({
                allowClear: true
            });

            clearInterval(loadFirstTime);
        }

    }

    function checkPage() {
        var filterGroup = $('#filterClassGroup').val();
        var filterSemester = $('#filterSemester').val();
        // console.log(filterGroup);
        if (filterGroup != '' && filterGroup != null && filterSemester != null && filterSemester !== '') {

            var SemesterID = filterSemester.split('.')[0];
            loading_page('#divpagePresensi');

            var group = filterGroup.split('.')[1];

            var url = base_url_js + 'api/__getScheduleIDByClassGroup/' + SemesterID + '/' + group;
            $.getJSON(url, function(result) {
                if (result != 0 && result != '0') {
                    var data = {
                        page: 'inputPresensi',
                        ScheduleID: result
                    };
                    var token = jwt_encode(data, 'UAP)(*');
                    loadPagePresensi(token);
                } else {
                    setTimeout(function() {
                        $('#divpagePresensi').html('<div style="text-align:center;"><h3 style="color: #ccc;font-weight: bold;">-- Data Not Yet --</h3></div>');
                    }, 500);
                }

            });

        } else {
            $('#divpagePresensi').html('');
        }
    }

    function loadPagePresensi(token) {
        var url = base_url_js + 'academic/loadPagePresensi';


        $.post(url, {
            token: token
        }, function(html) {
            setTimeout(function() {
                $('#divpagePresensi').html(html);
            }, 500)
        });
    }
</script>

<!-- Schedule Exchange -->
<script>
    $(document).on('click', '#btnSaveScheduleEx', function() {
        var ID_Attd = $(this).attr('data-id');
        var Meeting = $(this).attr('data-no');
        checkSchedule(ID_Attd, Meeting);
    });

    $(document).on('click', '#btnDeleteScheduleEx', function() {
        var ID_Attd = $(this).attr('data-id');
        var Meeting = $(this).attr('data-no');

        if (confirm('Delete ?')) {
            var url = base_url_js + 'api/__crudScheduleExchange';
            var dataToken = {
                action: 'deleteSceduleEx',
                ID_Attd: ID_Attd,
                Meeting: Meeting
            };

            loading_buttonSm('#btnDeleteScheduleEx');
            $('.btn-schedule-exchange,.form-ScEx').prop('disabled', true);

            var token = jwt_encode(dataToken, 'UAP)(*');
            $.post(url, {
                token: token
            }, function(jsonResult) {
                getDataAttendance();
                toastr.success('Data Deleted', 'Deleted');
                $('#GlobalModal').modal('hide');
            });
        }
    });

    $(document).on('keyup', '#formReason', function() {
        var r = $('#formReason').val().length;
        $('#viewLengthReason').text(r);
        $('#viewLengthReason').css('color', 'green');
        if (r >= 40) {
            $('#viewLengthReason').css('color', 'red');
        } else if (r >= 30) {
            $('#viewLengthReason').css('color', '#d88a16');
        }

    });

    $(document).on('click', '.inputScheduleExchange', function() {

        var filterAttendance = $('#filterAttendance').val();
        var ID_Attd = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        var url = base_url_js + 'api/__crudScheduleExchange';
        var data = {
            action: 'readExchange',
            ID_Attd: ID_Attd,
            ScheduleID: filterAttendance.split('.')[1],
            SDID: filterAttendance.split('.')[2],
            Meeting: No
        };

        var token = jwt_encode(data, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            var Credit = jsonResult.S_Details.Credit;
            var TimePerCredit = jsonResult.S_Details.TimePerCredit;

            var data_start = (jsonResult.S_Exchange.length > 0 && jsonResult.S_Exchange[0].StartSessions != '' &&
                jsonResult.S_Exchange[0].StartSessions != null) ? jsonResult.S_Exchange[0].StartSessions.substr(0, 5) : '00:00';

            var data_end = (jsonResult.S_Exchange.length > 0 && [0].EndSessions != '' &&
                jsonResult.S_Exchange[0].EndSessions != null) ? jsonResult.S_Exchange[0].EndSessions.substr(0, 5) : '00:00';

            var data_ClassroomID = (jsonResult.S_Exchange.length > 0 && jsonResult.S_Exchange[0].ClassroomID != '' &&
                jsonResult.S_Exchange[0].ClassroomID != null) ? jsonResult.S_Exchange[0].ClassroomID : '';

            var btnActDel = (jsonResult.S_Exchange.length > 0) ? '' : 'disabled';


            var body_attd = '<div class="row">' +
                '    <div class="col-xs-12">' +
                '        <table class="table">' +
                '            <tr>' +
                '                <td style="width: 20%;">Lecturer</td>' +
                '                <td>' +
                '                    <select class="form-control form-ScEx" id="formLecturers"></select>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Date</td>' +
                '                <td>' +
                '                    <input class="form-control form-ScEx" id="formDateOriginal" style="width: 170px;">' +
                '                       <span>Original date</span>' +
                '                </td>' +
                '            </tr>' +
                '           <tr>' +
                '               <td>Reason</td>' +
                '               <td>' +
                '                   <textarea rows="2" class="form-control" id="formReason" placeholder="Maximum length 40 character..." maxlength="40"></textarea>' +
                '                   Length : <span id="viewLengthReason">0</span> of 40' +
                '                </td>' +
                '           </tr>' +
                '            <tr>' +
                '                <td>Date</td>' +
                '                <td>' +
                '                    <input class="form-control form-ScEx" id="formDate" style="width: 170px;">' +
                '                       <span>Exchange date</span>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Classroom</td>' +
                '                <td>' +
                '                    <select class="form-control form-ScEx" id="formClassroom"></select>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Start</td>' +
                '                <td>' +
                '                   <div class="row">' +
                '                   <div class="col-xs-4">' +
                '                       <div id="dataStart" class="input-group">' +
                '                           <input data-format="hh:mm" type="text" id="formStart" class="form-control form-ScEx" value="' + data_start + '"/>' +
                '                           <span class="add-on input-group-addon">' +
                '                               <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
                '                           </span>' +
                '                       </div>' +
                '                   </div> ' +
                '                   <div class="col-xs-8" style="padding-top:5px;">' +
                '                       <b>Credit : ' + Credit + ' | Time : ' + TimePerCredit + ' minutes/credit</b>' +
                '                   </div> ' +
                '                   </div>' +

                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>End</td>' +
                '                <td>' +
                '                    <input class="form-control form-ScEx" id="formEnd" value="' + data_end + '" style="width: 150px;">' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Action</td>' +
                '                <td>' +
                '                    <div class="checkbox" style="margin-top: 0px;">' +
                '                       <label>' +
                '                       <input type="checkbox" class="form-ScEx" id="formCkStatus" value="1"> Approve' +
                '                       </label>' +
                '                   </div>' +
                '                </td>' +
                '            </tr>' +
                '        </table>' +
                '       <div id="divAlertEx"></div>' +
                '    </div>' +
                '</div>';

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Schedule Exchange | Pertemuan ' + No + '</h4>');

            $('#GlobalModal .modal-body').html(body_attd);

            for (var l = 0; l < jsonResult.Lecturer.length; l++) {
                var sc = (jsonResult.S_Exchange.length > 0 && jsonResult.S_Exchange[0].NIP != '' &&
                    jsonResult.S_Exchange[0].NIP != null) ? 'selected' : '';
                $('#formLecturers').append('<option value="' + jsonResult.Lecturer[l].NIP + '" ' + sc + '>' + jsonResult.Lecturer[l].NIP + ' - ' + jsonResult.Lecturer[l].Name + '</option>');
            }

            loadSelectOptionClassroom('#formClassroom', data_ClassroomID);

            $("#formDate,#formDateOriginal").datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy'
            });

            if (jsonResult.S_Exchange.length > 0 && jsonResult.S_Exchange[0].Date !== '0000-00-00' &&
                jsonResult.S_Exchange[0].Date != null && jsonResult.S_Exchange[0].Date != '') {

                var dOri = new Date(jsonResult.S_Exchange[0].DateOriginal);
                $('#formDateOriginal').datepicker('setDate', dOri);

                var d = new Date(jsonResult.S_Exchange[0].Date);
                $('#formDate').datepicker('setDate', d);

            }

            if (jsonResult.S_Exchange.length > 0 && jsonResult.S_Exchange[0].Reason != '' && jsonResult.S_Exchange[0].Reason != null) {
                $('#formReason').val(jsonResult.S_Exchange[0].Reason);
                $('#viewLengthReason').html(jsonResult.S_Exchange[0].Reason.length);
            }

            $('#formCkStatus').prop('checked', false);
            if (jsonResult.S_Exchange.length > 0 && jsonResult.S_Exchange[0].Status == '1') {
                $('#formCkStatus').prop('checked', true);
            }

            $('#dataStart').datetimepicker({
                    pickDate: false,
                    pickSeconds: false
                })
                .on('changeDate', function(e) {
                    var d = new Date(e.localDate);
                    var totalTime = parseInt(TimePerCredit) * parseInt(Credit);

                    var sesiAkhir = moment()
                        .hours(d.getHours())
                        .minutes(d.getMinutes())
                        .add(parseInt(totalTime), 'minute').format('HH:mm');

                    $('#formEnd').val(sesiAkhir);

                });

            $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default btn-schedule-exchange" data-dismiss="modal">Close</button> | ' +
                '<button class="btn btn-danger btn-schedule-exchange" id="btnDeleteScheduleEx" ' + btnActDel + ' data-no="' + No + '" data-id="' + ID_Attd + '">Delete Permission</button> ' +
                '<button class="btn btn-success btn-schedule-exchange" id="btnSaveScheduleEx" data-no="' + No + '" data-id="' + ID_Attd + '">Save</button>');
            $('#GlobalModal').modal({
                'show': true,
                'backdrop': 'static'
            });


        });

    });

    function checkSchedule(ID_Attd, Meeting) {


        var filterSemester = $('#filterSemester').val();
        // var SemesterID = filterSemester.split('.')[0];
        var SemesterID = 13;

        var formLecturers = $('#formLecturers').val();
        var formDate = $('#formDate').datepicker("getDate");
        var formDateOriginal = $('#formDateOriginal').datepicker("getDate");
        var formReason = $('#formReason').val();
        var formClassroom = $('#formClassroom').val();
        var formStart = $('#formStart').val();
        var formEnd = $('#formEnd').val();


        if (filterSemester != null && filterSemester != '' &&
            formDateOriginal != null && formDateOriginal != '' &&
            formDate != null && formDate != '' &&
            formReason != null && formReason != '' &&
            formStart != null && formStart != '' && formStart != '00:00' &&
            formEnd != null && formEnd != '') {

            loading_buttonSm('#btnSaveScheduleEx');
            $('.btn-schedule-exchange,.form-ScEx').prop('disabled', true);

            var DayMoment = moment(formDate).day();
            var DayID = (DayMoment == 0) ? 7 : DayMoment;

            var Status = ($('#formCkStatus').is(':checked')) ? '1' : '0';

            var data = {
                action: 'check',
                formData: {
                    SemesterID: SemesterID,
                    IsSemesterAntara: '0',
                    ClassroomID: formClassroom,
                    DayID: DayID,
                    StartSessions: formStart,
                    EndSessions: formEnd
                }
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__checkSchedule';
            var element = '#divAlertEx';
            $.post(url, {
                token: token
            }, function(json_result) {
                $(element).html('');
                if (json_result.length > 0) {
                    $(element).append('<div class="row">' +
                        '                        <div class="col-xs-12" style="margin-top: 20px;">' +
                        '                            <div class="alert alert-danger" role="alert">' +
                        '                                <b><i class="fa fa-exclamation-triangle" aria-hidden="true" style="margin-right: 5px;"></i> Jadwal bentrok</b>, Silahklan rubah : Ruang / Hari / Jam' +
                        '                                <hr style="margin-bottom: 3px;margin-top: 10px;"/>' +
                        '                                <ol id="ulbentrok">' +
                        '                                </ol>' +
                        '                            </div>' +
                        '                        </div>' +
                        '' +
                        '                    </div>');

                    var ol = $('#ulbentrok');
                    for (var i = 0; i < json_result.length; i++) {
                        var data = json_result[i];
                        ol.append('<li>' +
                            'Group <strong style="color:#333;">' + data.ClassGroup + '</strong> : <span style="color: blue;">' + data.Room + ' | ' + daysEng[(parseInt(data.DayID) - 1)] + ' ' + data.StartSessions + ' - ' + data.EndSessions + '</span>' +
                            '<ul style="color: #607d8b;" id="dtMK' + i + '"></ul>' +
                            '</li>');

                        var ul = $('#dtMK' + i);
                        for (var m = 0; m < data.DetailsCourse.length; m++) {
                            var mk_ = data.DetailsCourse[m];
                            ul.append('<li>' + mk_.MKCode + ' | ' + mk_.NameEng + '</li>');
                        }
                    }

                } else {
                    var dataInsert = {
                        ID_Attd: ID_Attd,
                        NIP: formLecturers,
                        Meeting: Meeting,
                        ClassroomID: formClassroom,
                        DateOriginal: moment(formDateOriginal).format('YYYY-MM-DD'),
                        Date: moment(formDate).format('YYYY-MM-DD'),
                        Reason: formReason,
                        DayID: DayID,
                        StartSessions: formStart,
                        EndSessions: formEnd,
                        Status: Status
                    };

                    var url = base_url_js + 'api/__crudScheduleExchange';
                    var dataToken = {
                        action: 'addSceduleEx',
                        dataInsert: dataInsert
                    };
                    var token2 = jwt_encode(dataToken, 'UAP)(*');
                    $.post(url, {
                        token: token2
                    }, function(jsonResult) {

                        $('#GlobalModal').modal('hide');
                        getDataAttendance();
                        toastr.success('Data saved', 'Success');

                    });

                }

                setTimeout(function() {
                    $('#btnSaveScheduleEx').prop('disabled', false).html('Save');
                    $('.btn-schedule-exchange,.form-ScEx').prop('disabled', false);
                }, 1000);
            });

        } else {
            toastr.warning('Form Required', 'Warning');
        }

    }
</script>

<!-- Input Attd Students -->
<script>
    $(document).on('click', '.rdAttd', function() {
        var No = $(this).attr('data-no');
        var valu = $(this).val();
        // console.log(No);
        $('#trA' + No + ',#trP' + No).css('background', 'none');

        if (valu == '1') {
            $('#trP' + No).css('background', '#438848');
        } else {
            $('#trA' + No).css('background', '#884343');
        }

        countP_A();

    });

    $(document).on('click', '#ckAllP', function() {
        $('.trA,.trP').css('background', 'none');
        if ($('#ckAllP').is(':checked')) {
            $('.trP').css('background', '#438848');
            $('.rd-trA').prop('checked', false);
            $('.rd-trP').prop('checked', true);

            $('#ckAllA').prop('checked', false);
        } else {
            $('.rd-trP').prop('checked', false);
        }
        countP_A();
    });

    $(document).on('click', '#ckAllA', function() {
        $('.trA,.trP').css('background', 'none');
        if ($('#ckAllA').is(':checked')) {
            $('.trA').css('background', '#884343');
            $('.rd-trP').prop('checked', false);
            $('.rd-trA').prop('checked', true);

            $('#ckAllP').prop('checked', false);
        } else {
            $('.rd-trA').prop('checked', false);
        }
        countP_A();
    });

    $(document).on('click', '.inputStudentAttd', function() {

        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        var SemesterID = 13;

        var DataAttendance = $('#filterAttendance').val();
        var ScheduleID = DataAttendance.split('.')[1];
        var SDID = DataAttendance.split('.')[2];

        var url = base_url_js + 'api/__crudAttendance';
        var data = {
            action: 'getAttdStudents',
            SemesterID: SemesterID,
            ScheduleID: ScheduleID,
            SDID: SDID,
            Meeting: No
        };
        var token = jwt_encode(data, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {


            var body_attd = '<h3>Students Not Yet</h3>';
            if (jsonResult.length > 0) {
                body_attd = '<table class="table table-bordered table-striped" id="tableStdAttd">' +
                    '        <thead>' +
                    '        <tr style="background:#436f88;color: #ffffff;">' +
                    '            <th style="width: 1%;">No</th>' +
                    '            <th style="width: 10%;">NPM</th>' +
                    '            <th style="width: 35%;">Name</th>' +
                    '            <th  style="width: 5%;">' +
                    '                P' +
                    '                <br/>' +
                    '                <input id="ckAllP" type="checkbox">' +
                    '            </th>' +
                    '            <th style="width: 5%;">' +
                    '                A' +
                    '                <br/>' +
                    '                <input id="ckAllA" type="checkbox">' +
                    '            </th>' +
                    '            <th>Description</th>' +
                    '        </tr>' +
                    '        </thead>' +
                    '        <tbody id="trBody">' +
                    '        </tbody>' +
                    '    </table>';
            }

            $('#GlobalModalLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Attendance Students | Pertemuan ' + No + '</h4>');

            $('#GlobalModalLarge .modal-body').html(body_attd);

            if (jsonResult.length > 0) {

                window.totalMhs = jsonResult.length;
                var no_tr = 1;
                for (var i = 0; i < totalMhs; i++) {
                    var dataRowStd = jsonResult[i];
                    var p = '';
                    var bgP = '';
                    var a = 'checked';
                    var bgA = 'style="background: #884343;"';

                    if (dataRowStd.Status == 1) {
                        p = 'checked';
                        bgP = 'style="background: #438848;"';
                        a = '';
                        bgA = '';
                    }

                    var dc = (dataRowStd.Description != '' && dataRowStd.Description != null) ? dataRowStd.Description : '';

                    $('#trBody').append('<tr>' +
                        '<td>' + (no_tr++) + '</td>' +
                        '<td>' + dataRowStd.DetailStudent.NPM + '<input value="' + dataRowStd.ID_Attd_S + '" id="formID_Attd_S' + i + '" class="hide" hidden readonly/></td>' +
                        '<td style="text-align: left;font-weight: bold;">' + dataRowStd.DetailStudent.Name + '</td>' +
                        '<td class="trP" id="trP' + i + '" ' + bgP + '><input type="radio" class="rdAttd rd-trP form-attd-students" data-mhs="' + totalMhs + '" data-no="' + i + '" name="optRAttd' + i + '" value="1" ' + p + '></td>' +
                        '<td class="trA" id="trA' + i + '" ' + bgA + '><input type="radio" class="rdAttd rd-trA form-attd-students" data-mhs="' + totalMhs + '" data-no="' + i + '" name="optRAttd' + i + '" value="2" ' + a + '></td>' +
                        '<td><textarea class="form-control form-attd-students" id="formDesc' + i + '" rows="1">' + dc + '</textarea></td>' +
                        '</tr>');
                }
            }

            $('#GlobalModalLarge .modal-footer').html('Total : ' + totalMhs + ' | P : <span id="totalP"></span> | A : <span id="totalA"></span> | <button type="button" class="btn btn-default btn-attd-students" data-dismiss="modal">Close</button> ' +
                '<button class="btn btn-success" id="btnSaveAttdStudents" data-no="' + No + '" data-id="' + ID + '">Save</button>');
            $('#GlobalModalLarge').modal({
                'show': true,
                'backdrop': 'static'
            });

            countP_A();

        });


    });

    $(document).on('click', '#btnSaveAttdStudents', function() {
        var Meeting = $(this).attr('data-no');
        var dataUpdate = [];
        for (var s = 0; s < totalMhs; s++) {
            var arrData = {
                ID: $('#formID_Attd_S' + s).val(),
                Status: $('input[type=radio][name=optRAttd' + s + ']:checked').val(),
                Meeting: Meeting,
                Description: $('#formDesc' + s).val()
            }

            dataUpdate.push(arrData);
        }

        var data = {
            action: 'addAttdStudents',
            dataUpdate: dataUpdate
        };

        var url = base_url_js + 'api/__crudAttendance';
        var token = jwt_encode(data, 'UAP)(*');

        loading_buttonSm('#btnSaveAttdStudents');
        $('.form-attd-students,.btn-attd-students').prop('disabled', true);
        $.post(url, {
            token: token
        }, function(jsonResult) {
            getDataAttendance();
            toastr.success('Data Saved', 'Success');
            setTimeout(function() {
                $('#GlobalModalLarge').modal('hide');
            }, 1000);
        });

    });

    function countP_A() {

        var p = 0;
        var a = 0;
        for (var i = 0; i < totalMhs; i++) {
            var valu = $('input[type=radio][name=optRAttd' + i + ']:checked').val();
            if (valu == '1') {
                p += 1;
            } else {
                a += 1;
            }
        }

        if (totalMhs != a) {
            $('#ckAllA').prop('checked', false);
        } else {
            $('#ckAllA').prop('checked', true);
        }

        if (totalMhs != p) {
            $('#ckAllP').prop('checked', false);
        } else {
            $('#ckAllP').prop('checked', true);
        }

        $('#totalP').html(p);
        $('#totalA').html(a);
    }
</script>

<!-- Input Attd lecturers -->
<script>
    $(document).on('click', '.inputLecturerAttd', function() {

        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');
        var filterAttendance = $('#filterAttendance').val();

        if (filterAttendance != '' && filterAttendance != null) {

            var Credit = filterAttendance.split('.')[3];
            var TimePerCredit = filterAttendance.split('.')[4];

            var url = base_url_js + 'api/__crudAttendance';
            var data = {
                action: 'getAttdLecturers',
                ID: ID,
                No: No
            };
            var token = jwt_encode(data, 'UAP)(*');

            $.post(url, {
                token: token
            }, function(jsonResult) {

                $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Attendance ' + No + '</h4>');

                var attd_nip = jsonResult.NIP;
                var attd_bap = (jsonResult.BAP != '' && jsonResult.BAP != null) ? jsonResult.BAP : '';
                // var attd_date = (jsonResult.Date!='' && jsonResult.Date!=null) ? jsonResult.Date : '';
                var attd_in = (jsonResult.In != '' && jsonResult.In != null) ? jsonResult.In.substr(0, 5) : '00:00';
                var attd_out = (jsonResult.Out != '' && jsonResult.Out != null) ? jsonResult.Out.substr(0, 5) : '00:00';


                var body_attd = '<div class="row">' +
                    '                        <div class="col-xs-4">' +
                    '                            <div class="form-group">' +
                    '                                <label>Date</label>' +
                    '                               <input type="text" id="formDate" class="form-control form-attd" readonly>' +
                    '                               <input type="text" id="formCredit" class="hide" value="' + Credit + '" hidden readonly>' +
                    '                               <input type="text" id="formTimePerCredit" class="hide" value="' + TimePerCredit + '" hidden readonly>' +
                    '                            </div>' +
                    '                            <div class="form-group">' +
                    '                                <label>In</label>' +
                    '                                <div id="inputIn" class="input-group">' +
                    '                                    <input data-format="hh:mm" type="text" id="formIn" class="form-control form-attd" value="' + attd_in + '"/>' +
                    '                                    <span class="add-on input-group-addon">' +
                    '                                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
                    '                                    </span>' +
                    '                                </div>' +
                    '<p style="margin-top: 3px;font-size: 10px;color: #607D8B;font-weight: bold;">Credit : ' + Credit + ' | ' + TimePerCredit + ' minute/credit</p>' +
                    '                            </div>' +
                    '                            <div class="form-group">' +
                    '                                <label>Out</label>' +
                    '                               <input class="form-control" id="formOut"> ' +
                    // '                                <div id="inputOut" class="input-group">' +
                    // '                                    <input data-format="hh:mm" type="text" id="" class="form-control form-attd" value="'+attd_out+'"/>' +
                    // '                                    <span class="add-on input-group-addon">' +
                    // '                                      <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
                    // '                                    </span>' +
                    // '                                </div>' +
                    '                            </div>' +
                    '                        </div>' +
                    '                        <div class="col-xs-8">' +
                    '                           <div class="form-group">' +
                    '                               <label>Lecturer</label>' +
                    '                               <select class="form-control" id="formLecturer"></select>' +
                    '                           </div>' +
                    '                            <div class="form-group">' +
                    '                                <label>BAP</label>' +
                    '                                <textarea class="form-control" id="formBAP" rows="5">' + attd_bap + '</textarea>' +
                    '                            </div>' +
                    '                        </div>' +
                    '                    </div>';

                $('#GlobalModal .modal-body').html(body_attd);

                for (var t = 0; t < jsonResult.DetailLecturers.length; t++) {
                    var lec = jsonResult.DetailLecturers[t];
                    var sc = (attd_nip == lec.NIP) ? 'selected' : '';
                    $('#formLecturer').append('<option value="' + lec.NIP + '" ' + sc + '>' + lec.NIP + ' - ' + lec.Name + '</option>');
                }

                $('#inputIn').datetimepicker({
                    pickDate: false,
                    pickSeconds: false
                }).on('changeDate', function(e) {
                    var TimePerCredit = $('#formTimePerCredit').val();
                    var Credit = $('#formCredit').val();
                    var totalTime = parseInt(TimePerCredit) * parseInt(Credit);

                    var inputIn = $('#formIn').val().split(':');
                    var StartSessions = moment().hours(inputIn[0]).minutes(inputIn[1]);
                    var EndSessions = StartSessions.add(parseInt(totalTime), 'minute').format('HH:mm');
                    $('#formOut').val(EndSessions);
                });

                $("#formDate").datepicker({
                    showOtherMonths: true,
                    autoSize: true,
                    dateFormat: 'dd MM yy'
                });

                if (jsonResult.Date !== '0000-00-00' && jsonResult.Date != null) {
                    var d = new Date(jsonResult.Date);

                    $('#formDate').datepicker('setDate', d);
                }

                $('a.ui-state-default').attr('href', 'javascript:void(0)');

                $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
                    '<button class="btn btn-success" id="btnSaveAttdLecturer" data-no="' + No + '" data-id="' + ID + '">Save</button>');
                $('#GlobalModal').modal({
                    'show': true,
                    'backdrop': 'static'
                });
            });
        }

    });

    $(document).on('click', '#btnSaveAttdLecturer', function() {
        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');


        var formDate = $('#formDate').datepicker("getDate");
        var formIn = $('#formIn').val();
        var formOut = $('#formOut').val();
        var formBAP = $('#formBAP').val();
        var NIP = $('#formLecturer').val();


        if (formDate != null && formDate != '' &&
            formIn != null && formIn != '' &&
            formOut != null && formOut != '' &&
            formBAP != null && formBAP != '') {

            $('#formDate,#formIn,#formOut,#formBAP').prop('disabled', true);
            loading_buttonSm('#btnSaveAttdLecturer');

            var url = base_url_js + 'api/__crudAttendance';
            var data = {
                action: 'UpdtAttdLecturers',
                ID: ID,
                No: No,
                BAP: formBAP,
                insertAttdLecturer: {
                    ID_Attd: ID,
                    NIP: NIP,
                    Meet: No,
                    Date: moment(formDate).format('YYYY-MM-DD'),
                    In: formIn,
                    Out: formOut,
                }
            };
            var token = jwt_encode(data, 'UAP)(*');

            $.post(url, {
                token: token
            }, function(resultJson) {
                getDataAttendance();
                toastr.success('Data Saved', 'Success');
                $('#GlobalModal').modal('hide');
            });

        } else {
            toastr.warning('Form Required', 'Warning');
        }


    });

    $(document).on('click', '.btn-delete-attd', function() {

        if (confirm("Remove data ?")) {
            var ID = $(this).attr('data-id');

            var url = base_url_js + 'api/__crudAttendance';
            var data = {
                action: 'DeleteAttdLecturers',
                ID: ID
            };
            var token = jwt_encode(data, 'UAP)(*');
            $.post(url, {
                token: token
            }, function() {
                toastr.success('Data Removed', 'Remove Success');
                getDataAttendance();
            });
        }

    });
</script>


<!-- Edit Students -->
<script>
    $(document).on('click', '.editStudentAttd', function() {
        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        var SemesterID = 13;

        var DataAttendance = $('#filterAttendance').val();
        var ScheduleID = DataAttendance.split('.')[1];
        var SDID = DataAttendance.split('.')[2];

        var url = base_url_js + 'api/__crudAttendance';
        var data = {
            action: 'getAttdStudentsToEdit',
            SemesterID: SemesterID,
            ScheduleID: ScheduleID,
            SDID: SDID,
            Meeting: No
        };
        var token = jwt_encode(data, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            var body_attd = '<h3>Students Not Yet</h3>';
            if (jsonResult.length > 0) {
                body_attd = '<div class="well">' +
                    '<input id="schedule" value="' + SemesterID + '.' + ScheduleID + '" class="hide" hidden readonly /> ' +
                    '<div class="input-group">' +
                    '   <input type="text" id="formSimpleSearchAttd" class="form-control" placeholder="Search by NIM, Name . . .">' +
                    '   <span class="input-group-btn">' +
                    '<button class="btn btn-default" type="button">' +
                    '<i class="fa fa-search" aria-hidden="true"></i>' +
                    '</button>' +
                    '</span></div>' +
                    '<hr/>' +
                    '<table class="table table-bordered">' +
                    '<thead>' +
                    '<tr style="background: #4f8742ab;color:#fff;">' +
                    '<th style="width: 20%;">NIM</th>' +
                    '<th>Name</th>' +
                    '<th style="width: 9%;">Action</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody id="rowStdAttd"></tbody>' +
                    '</table>' +
                    '</div>' +
                    '<table class="table table-bordered table-striped" id="tableStdAttd">' +
                    '        <thead>' +
                    '        <tr style="background:#436f88;color: #ffffff;">' +
                    '            <th style="width: 1%;">No</th>' +
                    '            <th style="width: 10%;">NIM</th>' +
                    '            <th>Name</th>' +
                    '            <th style="width: 9%;">Action</th>' +
                    '        </tr>' +
                    '        </thead>' +
                    '        <tbody id="trBody">' +
                    '        </tbody>' +
                    '    </table>';
            }

            $('#GlobalModalLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Edit Students</h4>');

            $('#GlobalModalLarge .modal-body').html(body_attd);

            $('#filterCurriculumAttd').empty();
            loadSelectOptionCurriculum('#filterCurriculumAttd', '');

            if (jsonResult.length > 0) {

                window.totalMhs = jsonResult.length;
                var no_tr = 1;
                for (var i = 0; i < totalMhs; i++) {
                    var dataRowStd = jsonResult[i];


                    $('#trBody').append('<tr id="trN' + dataRowStd.ID_Attd_S + '">' +
                        '<td>' + (no_tr++) + '</td>' +
                        '<td>' + dataRowStd.DetailStudent.NPM + '<input value="' + dataRowStd.ID_Attd_S + '" id="formID_Attd_S' + i + '" class="hide" hidden readonly/></td>' +
                        '<td style="text-align: left;font-weight: bold;">' + dataRowStd.DetailStudent.Name + '</td>' +
                        '<td><button class="btn btn-danger btn-del-attd-s" data-npm="' + dataRowStd.DetailStudent.NPM + '" data-db="' + dataRowStd.DBStudent + '" ' +
                        'data-id-attd-s="' + dataRowStd.ID_Attd_S + '" data-id-attd="' + dataRowStd.ID_Attd + '" ' +
                        'data-sc="' + dataRowStd.SemesterID + '.' + dataRowStd.ScheduleID + '"><i class="fa fa-trash"></i></button></td>' +
                        '</tr>');
                }
            }

            $('#GlobalModalLarge .modal-footer').html('<button type="button" class="btn btn-default btn-attd-students" data-dismiss="modal">Close</button>');
            $('#GlobalModalLarge').modal({
                'show': true,
                'backdrop': 'static'
            });


        });
    });

    $(document).on('keyup', '#formSimpleSearchAttd', function() {
        var formSimpleSearch = $('#formSimpleSearchAttd').val();
        if (formSimpleSearch != '' && formSimpleSearch != null) {
            var url = base_url_js + 'api/__getSimpleSearchStudents?key=' + formSimpleSearch;
            $.getJSON(url, function(jsonResult) {

                $('#rowStdAttd').empty();
                if (jsonResult.length > 0) {
                    for (var i = 0; i < jsonResult.length; i++) {
                        var d = jsonResult[i];

                        // console.log(d);

                        $('#rowStdAttd').append('<tr id="addB' + d.Username + '">' +
                            '<td style="text-align: left;">' + d.Username + '</td>' +
                            '<td>' + d.Name + '</td>' +
                            '<td><button class="btn btn-success btn-block btn-add-attd-s" ' +
                            'data-name="' + d.Name + '" ' +
                            'data-npm="' + d.Username + '" ' +
                            'data-db="' + d.DB_Student + '"><i class="fa fa-download"></i></button></td>' +
                            '</tr>');
                    }
                } else {
                    $('#rowStdAttd').empty();
                    $('#rowStdAttd').append('<tr>' +
                        '<td colspan="3">-- Data Not Yet --</td>' +
                        '</tr>');
                }
            });
        }
    });

    $(document).on('click', '.btn-del-attd-s', function() {

        var NPM = $(this).attr('data-npm');
        var ID_Attd = $(this).attr('data-id-attd');
        var ID_Attd_S = $(this).attr('data-id-attd-s');
        var db_student = $(this).attr('data-db');
        var Schedule = $(this).attr('data-sc');

        loading_buttonSm('.btn-del-attd-s[data-id-attd-s=' + ID_Attd_S + ']');

        var url = base_url_js + 'api/__crudAttendance';
        var data = {
            action: 'delAttendanceStudents',
            NPM: NPM,
            SemesterID: Schedule.split('.')[0].trim(),
            ScheduleID: Schedule.split('.')[1].trim(),
            ID_Attd: ID_Attd,
            ID_Attd_S: ID_Attd_S,
            db_student: db_student
        };

        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.Status == 1 || jsonResult.Status == '1') {
                toastr.success('Delete Success', 'Success');
                getDataAttendance();
                setTimeout(function() {
                    $('#trN' + ID_Attd_S).animateCss('zoomOut', function() {
                        $('#trN' + ID_Attd_S).remove();
                    });
                }, 500);
            } else {
                toastr.error(jsonResult.Msg, 'Error');
                setTimeout(function() {
                    $('.btn-del-attd-s[data-id-attd-s=' + ID_Attd_S + ']').prop('disabled', false).html('<i class="fa fa-trash"></i>');
                }, 500);
            }

        });


    });

    $(document).on('click', '.btn-add-attd-s', function() {
        var Schedule = $('#schedule').val();
        var Name = $(this).attr('data-name');
        var NPM = $(this).attr('data-npm');
        var DB_Student = $(this).attr('data-db');

        loading_buttonSm('.btn-add-attd-s[data-npm=' + NPM + ']');

        var url = base_url_js + 'api/__crudAttendance';
        var data = {
            action: 'addAttendanceStudents',
            NPM: NPM,
            DB_Student: DB_Student,
            SemesterID: Schedule.split('.')[0].trim(),
            ScheduleID: Schedule.split('.')[1].trim()
        };

        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.Status == 1 || jsonResult.Status == '1') {
                toastr.success(jsonResult.Msg, 'Success');
                $('#trBody').append('<tr>' +
                    '<td>-</td>' +
                    '<td>' + NPM + '</td>' +
                    '<td style="text-align: left;">' + Name + '</td>' +
                    '<td>-</td>' +
                    '</tr>');
            } else {
                toastr.error(jsonResult.Msg, 'Error');
            }

            setTimeout(function() {
                $('.btn-add-attd-s[data-npm=' + NPM + ']').prop('disabled', false)
                    .html('<i class="fa fa-download"></i>');
            }, 500);

        });
    });
</script>

<!-- Delete Attendance -->
<script>
    $(document).on('click', '.deleteAttendance', function() {


        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        $('#NotificationModal .modal-body').html('<div style="text-align: center;"><h3>Delete Attendance in <b>Session ' + No + '</b> ??</h3><hr/> ' +
            '<button type="button" class="btn btn-danger" id="btnDeleteAttendance"  data-no="' + No + '" data-id="' + ID + '" style="margin-right: 5px;">Yes</button>' +
            '<button type="button" class="btn btn-default" id="btnCloseDelAttd" data-dismiss="modal">No</button>' +
            '</div>');
        $('#NotificationModal').modal({
            'show': true,
            'backdrop': 'static'
        });
    });

    $(document).on('click', '#btnDeleteAttendance', function() {
        var No = $(this).attr('data-no');
        var ID = $(this).attr('data-id');

        loading_buttonSm('#btnDeleteAttendance');
        $('#btnCloseDelAttd').prop('disabled', true);


        var url = base_url_js + 'api/__crudAttendance';
        var data = {
            action: 'DeleteAttendance',
            ID_Attd: ID,
            Meet: No
        };
        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function() {
            toastr.success('Data Removed', 'Remove Success');
            getDataAttendance();
            setTimeout(function(args) {
                $('#NotificationModal').modal('hide');
            }, 500);
        });
    });
</script>