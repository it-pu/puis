<style>
    #tableMonAttd tr th {
        text-align: center;
        background: #607d8b;
        color: #FFFFFF;
    }

    #tableMonAttd tr td {
        text-align: center;
    }

    .btnDeleteAttd {
        padding: 0px 4px;
        border-radius: 11px;
    }

    #viewExcDateOri,
    #viewExcDate {
        color: #333 !important;
        background: #FFFFFF !important;
        cursor: pointer;
    }
</style>

<div class="row">
    <div class="col-md-3">
        <a href="<?php echo base_url('academic/attendance'); ?>" class="btn btn-warning"><i class="fa fa-arrow-left margin-right"></i> Back to List</a>
    </div>
    <div class="col-md-6">
        <div class="" style="text-align: center;background: lightyellow;padding: 15px;border: 1px solid #CCCCCC;margin-bottom: 15px;">
            <h3 style="margin-top: 0px;" id="viewCourse">-</h3>
        </div>
        <textarea id="viewLecturer" class="hide"></textarea>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="well">
            <select class="form-control" id="filterSD"></select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12" style="text-align: right;margin-bottom: 15px;">
        <hr />
        <input id="totalSession" value="14" class="hide" />
        <button class="btn btn-default btnEdAttd" disabled id="btnBAP"><i class="fa fa-edit margin-right"></i> BAP</button> |
        <button class="btn btn-primary btnEdAttd" disabled id="btnLecAttd"><i class="fa fa-edit margin-right"></i> Lecturer Attendance</button> |
        <button class="btn btn-success btnEdAttd" disabled id="btnStdAttd"><i class="fa fa-edit margin-right"></i> Student Attendance</button> |
        <button class="btn btn-danger btnEdAttd" disabled id="btnClearAttd"><i class="fa fa-trash margin-right"></i> Clear Attendance</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="divLoadAttd"></div>
    </div>
</div>



<script>
    $(document).ready(function() {
        setLoadFullPage();
        window.ScheduleID = "<?php echo $ScheduleID; ?>";
        loadSchedule();

        var loadFirs = setInterval(function() {
            var filterSD = $('#filterSD').val();
            if (filterSD != '' && filterSD != null) {
                loadAttendace();
                clearInterval(loadFirs);
            }
        }, 1000);

    });

    function loadSchedule() {
        var data = {
            action: 'loadScheduleDetails',
            ScheduleID: ScheduleID
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api2/__crudAttendance2';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            var Course = jsonResult.Course[0];
            $('#viewCourse').html(Course.MKCode + ' - ' + Course.CourseEng + '<br/><small>Group : ' + Course.ClassGroup + '</small>');

            $('#viewLecturer').val(JSON.stringify(jsonResult.Lecturer));

            $('#totalSession').val(Course.totalSession);

            $.each(jsonResult.Schedule, function(index, val) {
                var sesi = val.StartSessions.substr(0, 5) + ' - ' + val.EndSessions.substr(0, 5) + ' | ' + val.Room;
                $('#filterSD').append('<option value="' + val.SDID + '.' + val.ID_Attd + '">' + val.DayEng + ', ' + sesi + '</option>');
            });

        });
    }

    function loadAttendace() {

        $('.btnEdAttd').prop('disabled', true);


        var filterSD = $('#filterSD').val();
        if (filterSD != '' && filterSD != null) {

            loading_page('#divLoadAttd');

            var SDID = filterSD.split('.')[0];

            var data = {
                action: 'readDetailAttendance',
                ScheduleID: ScheduleID,
                SDID: SDID
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api2/__crudAttendance2';

            $.post(url, {
                token: token
            }, function(jsonResult) {

                setTimeout(function() {
                    $('.btnEdAttd').prop('disabled', false);
                    $('#divLoadAttd').html('<table class="table table-bordered table-striped" id="tableMonAttd">' +
                        '            <thead>' +
                        '            <tr>' +
                        '                <th rowspan="2" style="width: 1%;">Sesi</th>' +
                        '                <th rowspan="2" style="width: 12%;">Subject</th>' +
                        '                <th rowspan="2" style="width: 12%;">Material</th>' +
                        '                <th rowspan="2" style="width: 12%;">Description</th>' +
                        '                <th colspan="2" style="width: 6%;">Attendance</th>' +
                        '                <th colspan="2">Signature</th>' +
                        '                <th rowspan="2" style="width: 10%;">Schedule Exchange</th>' +
                        '                <th rowspan="2" style="width: 15%;">Review</th>' +
                        '            </tr>' +
                        '            <tr>' +
                        '                <th style="width: 3%;">P</th>' +
                        '                <th style="width: 3%;">A</th>' +
                        '                <th style="width: 15%;">Lecturer</th>' +
                        '                <th style="width: 10%;">Student</th>' +
                        '            </tr>' +
                        '            </thead>' +
                        '            <tbody id="dataRowBAB"></tbody>' +
                        '        </table>');

                    var no = 1;
                    for (var i = 0; i < jsonResult.length; i++) {
                        var d = jsonResult[i];
                        var dataLecturer = d.Lecturer;
                        var dataBAP = d.BAP;

                        var Subject = (dataBAP.length > 0) ? dataBAP[0].Subject : '';
                        var Material = (dataBAP.length > 0) ? dataBAP[0].Material : '';
                        var Description = (dataBAP.length > 0) ? dataBAP[0].Description : '';

                        var dataBAPToReset = (dataBAP.length > 0) ? 1 : 0;

                        var lecturer = '';
                        if (dataLecturer.length > 0) {
                            for (var l = 0; l < dataLecturer.length; l++) {
                                var d_l = dataLecturer[l];
                                var Start = (d_l.In != '' && d_l.In != null) ? d_l.In.substr(0, 5) : '';
                                var End = (d_l.Out != '' && d_l.Out != null) ? d_l.Out.substr(0, 5) : '';

                                var time = (Start != '' && End != '') ? '<span style="color: #2196f3;">' + Start + ' - ' + End + '</span>' : '';

                                var date = moment(d_l.Date).format('dddd, DD MMM YYYY');
                                lecturer = lecturer + '<div><i class="fa fa-user margin-right"></i> ' + d_l.Lecturer + ' | <button data-id="' + d_l.ID + '" class="btn btn-sm btn-default btn-default-danger btnDeleteAttd"><i class="fa fa-minus-circle"></i></button> <br/><span style="color: #ff5722;">' + date + ' | ' + time + '</span></div>';
                            }
                        }

                        var btnSignStudent = (dataBAP.length > 0 && dataBAP[0].StudentSignBy != null && dataBAP[0].StudentSignBy != '') ?
                            '<div style="text-align: left;">' + dataBAP[0].StudentSignBy + ' - ' + dataBAP[0].Student + '<br/><span style="color: #9E9E9E;">' + moment(dataBAP[0].StudentSignAt).format('dddd, DD MMM YYYY HH:mm') + '</span></div>' :
                            '-';

                        var exchange = (d.Exchange.length > 0) ?
                            '<a href="javascript:void(0);" data-sesi="' + i + '" class="showExchange" style="color: #607d8b;">' + moment(d.Exchange[0].Date).format('ddd, DD MMM YYYY') + '<br/>' +
                            d.Exchange[0].StartSessions.substr(0, 5) + ' - ' + d.Exchange[0].EndSessions.substr(0, 5) +
                            ' | <b>' + d.Exchange[0].Room + '</b></a><textarea id="viewDataExchange' + i + '" class="hide">' + JSON.stringify(d.Exchange[0]) + '</textarea>' :
                            '-';
                        var ClassExc = (d.Exchange.length > 0) ? '<br/><span class="label label-warning">Exchange</span>' : '';

                        var Review = (dataBAP.length > 0 && dataBAP[0].Review != '' && dataBAP[0].Review != null) ? dataBAP[0].Review : '';

                        var needClearAll = (lecturer == '' && d.Present == '-' && d.Absent == '-' && (d.StatusSesi == 1 || d.StatusSesi == '1')) ?
                            '<span class="label label-danger">Need Clear</span>' : '';

                        $('#dataRowBAB').append('<tr>' +
                            '<td>' + no + ' ' + ClassExc + ' ' + needClearAll + '</td>' +
                            '<td style="text-align: left;"><span class="viewerBAP viewerBAP' + no + '" id="viewSubject' + no + '">' + Subject + '</span><textarea class="form-control hide formBAP formBAP' + no + '" rows="3" id="formSubject' + no + '">' + Subject + '</textarea></td>' +
                            '<td style="text-align: left;"><span class="viewerBAP viewerBAP' + no + '" id="viewMaterial' + no + '">' + Material + '</span><textarea class="form-control hide formBAP formBAP' + no + '" rows="3" id="formMaterial' + no + '">' + Material + '</textarea></td>' +
                            '<td style="text-align: left;"><span class="viewerBAP viewerBAP' + no + '" id="viewDescription' + no + '">' + Description + '</span><textarea class="form-control hide formBAP formBAP' + no + '" rows="3" id="formDescription' + no + '">' + Description + '</textarea></td>' +
                            '<td>' + d.Present + '</td>' +
                            '<td>' + d.Absent + '</td>' +
                            '<td style="text-align: left;">' + lecturer + '</td>' +
                            '<td>' + btnSignStudent + '</td>' +
                            '<td>' + exchange + '</td>' +
                            '<td style="text-align: left;">' + Review + '</td>' +
                            '</tr>');
                        no += 1;
                    }
                }, 500);


            });

        }



    }

    $('#filterSD').change(function() {
        loadAttendace();
    });

    $('#btnBAP').click(function() {
        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">BAP</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4 col-md-offset-4">' +
            '        <div class="form-group">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="formBAP_Sesi"></select>' +
            '        </div><hr/>' +
            '    </div>' +
            '</div>' +
            '<div id="divViewBap"></div>' +
            '<div class="row">' +
            '    <div class="col-md-12" style="text-align: right;">' +
            '        <hr/>' +
            '        <button type="button" class="btn btn-success" id="btnActSubmitBAP">Submit</button> | ' +
            '        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '    </div>' +
            '</div>');

        var totalSession = $('#totalSession').val();
        for (var i = 1; i <= parseInt(totalSession); i++) {
            $('#formBAP_Sesi').append('<option value="' + i + '">' + i + '</option>');
        }

        loadBAP();

        $('#formBAP_Sesi').change(function() {
            loadBAP();
        });

        $('#btnActSubmitBAP').click(function() {

            var Sesi = $('#formBAP_Sesi').val();

            var filterSD = $('#filterSD').val();
            var ID_Attd = filterSD.split('.')[1];

            var formBAP_Subject = $('#formBAP_Subject').val();
            var formBAP_Material = $('#formBAP_Material').val();
            var formBAP_Description = $('#formBAP_Description').val();

            var data = {
                action: ''
            };

        });

        $('#GlobalModal .modal-footer').addClass('hide');
        // $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
        //     '<button type="button" class="btn btn-primary"><i class="fa fa-paper-plane-o right-margin" aria-hidden="true"></i> Publish</button>');

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });
    });

    function loadBAP() {

        loading_page('#divViewBap');

        var formBAP_Sesi = $('#formBAP_Sesi').val();
        var Sesi = (formBAP_Sesi != '' && formBAP_Sesi != 1 && formBAP_Sesi != null && formBAP_Sesi != '1') ?
            formBAP_Sesi :
            1;

        var filterSD = $('#filterSD').val();

        var ID_Attd = filterSD.split('.')[1];

        var data = {
            action: 'loadBAP',
            Sesi: Sesi,
            ID_Attd: ID_Attd
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api2/__crudAttendance2';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            setTimeout(function() {
                $('#divViewBap').html('<div class="row">' +
                    '    <div class="col-md-12">' +
                    '        <div class="form-group">' +
                    '            <label>Subject</label>' +
                    '            <textarea class="form-control" id="formBAP_Subject" rows="4"></textarea>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="col-md-12">' +
                    '        <div class="form-group">' +
                    '            <label>Material</label>' +
                    '            <textarea class="form-control" id="formBAP_Material" rows="4"></textarea>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="col-md-12">' +
                    '        <div class="form-group">' +
                    '            <label>Description</label>' +
                    '            <textarea class="form-control" id="formBAP_Description" rows="4"></textarea>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>');

                var formBAP_Subject = (jsonResult.length > 0) ? jsonResult[0].Subject : '';
                var formBAP_Material = (jsonResult.length > 0) ? jsonResult[0].Material : '';
                var formBAP_Description = (jsonResult.length > 0) ? jsonResult[0].Description : '';

                $('#formBAP_Subject').val(formBAP_Subject);
                $('#formBAP_Material').val(formBAP_Material);
                $('#formBAP_Description').val(formBAP_Description);

            }, 500);

        });

    }

    $('#btnLecAttd').click(function() {
        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Lecturer Attendance</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4">' +
            '        <div class="form-group">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="formLecAttd_Sesi"></select>' +
            '        </div>' +
            '    </div>' +
            '    <div class="col-md-8">' +
            '        <div class="form-group">' +
            '            <label>Lecturer</label>' +
            '            <select class="form-control" id="formLecAttd_Lecturer"></select>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-md-6">' +
            '        <label>Date</label>' +
            '        <input class="hide" id="formLecAttd_Date">' +
            '        <input class="form-control" id="viewDate">' +
            '    </div>' +
            '    <div class="col-md-3">' +
            '        <label>Start</label>' +
            '        <div id="div_formSesiAwal" data-no="1" class="input-group">' +
            '            <input data-format="hh:mm" type="text" id="formLecAttd_Start" class="form-control" value="00:00"/>' +
            '            <span class="add-on input-group-addon">' +
            '                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
            '            </span>' +
            '        </div>' +
            '    </div>' +
            '    <div class="col-md-3">' +
            '        <label>End</label>' +
            '        <div id="div_formSesiEnd" data-no="1" class="input-group">' +
            '            <input data-format="hh:mm" type="text" id="formLecAttd_End" class="form-control" value="00:00"/>' +
            '            <span class="add-on input-group-addon">' +
            '                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
            '            </span>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-md-12" style="text-align: right;">' +
            '       <hr/>' +
            '        <button class="btn btn-success" id="btnSubmitLecAttd">Submit</button> | ' +
            '        <button class="btn btn-default" data-dismiss="modal">Close</button>' +
            '    </div>' +
            '</div>');

        var totalSession = $('#totalSession').val();
        for (var i = 1; i <= parseInt(totalSession); i++) {
            $('#formLecAttd_Sesi').append('<option value="' + i + '">' + i + '</option>');
        }

        // Load Lecturer
        var viewLecturer = JSON.parse($('#viewLecturer').val());
        if (viewLecturer.length > 0) {
            $.each(viewLecturer, function(i, v) {
                $('#formLecAttd_Lecturer').append('<option value="' + v.NIP + '">' + v.NIP + ' - ' + v.Name + '</option>');
            });
        }

        $("#viewDate").datepicker({
            showOtherMonths: true,
            autoSize: true,
            dateFormat: 'dd MM yy',
            // minDate: new Date(moment().year(),moment().month(),moment().date()),
            onSelect: function() {
                var d = moment($(this).datepicker("getDate")).format('YYYY-MM-DD');
                $('#formLecAttd_Date').val(d);
            }
        });

        $('#div_formSesiAwal,#div_formSesiEnd').datetimepicker({
            pickDate: false,
            pickSeconds: false
        });

        $('#GlobalModal .modal-footer').addClass('hide');
        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });


        $('#btnSubmitLecAttd').click(function() {

            var formLecAttd_Sesi = $('#formLecAttd_Sesi').val();
            var formLecAttd_Lecturer = $('#formLecAttd_Lecturer').val();
            var formLecAttd_Date = $('#formLecAttd_Date').val();
            var formLecAttd_Start = $('#formLecAttd_Start').val();
            var formLecAttd_End = $('#formLecAttd_End').val();

            if (formLecAttd_Sesi != null && formLecAttd_Sesi != '' &&
                formLecAttd_Lecturer != null && formLecAttd_Lecturer != '' &&
                formLecAttd_Date != null && formLecAttd_Date != '' &&
                formLecAttd_Start != null && formLecAttd_Start != '' &&
                formLecAttd_End != null && formLecAttd_End != '') {

                loading_buttonSm('#btnSubmitLecAttd');
                $('button[data-dismiss=modal]').prop('disabled', true);

                var filterSD = $('#filterSD').val();
                var ID_Attd = filterSD.split('.')[1];

                var data = {
                    action: 'updateAttendanceLecturer',
                    ScheduleID: ScheduleID,
                    ID_Attd: ID_Attd,
                    Meet: formLecAttd_Sesi,
                    NIP: formLecAttd_Lecturer,
                    Date: formLecAttd_Date,
                    In: formLecAttd_Start,
                    Out: formLecAttd_End
                };

                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api2/__crudAttendance2';

                $.post(url, {
                    token: token
                }, function(jsonResult) {
                    toastr.success('Attendance Saved', 'Success');
                    loadAttendace();
                    setTimeout(function() {
                        $('#GlobalModal').modal('hide');
                    }, 500);
                });

            } else {
                toastr.warning('All form required', 'Warning');
            }

        });
    });

    $('#btnStdAttd').click(function() {

        $('#GlobalModal .modal-header').html('<h4 class="modal-title">Students Attendance</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4 col-md-offset-4">' +
            '        <div class="">' +
            '        <div class="form-group">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="formAttdStd_Sesi"></select>' +
            '               <input id="formAttdStd_TotalStd" class="hide">' +
            '        </div><hr/>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '' +
            '<div class="row">' +
            '    <div class="col-md-12">' +
            '       <div id="divStdAttd"></div>' +
            '        <hr>' +
            '        <div style="text-align: right;">' +
            '            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> | ' +
            '            <button type="button" class="btn btn-success" id="btnSubmitAttdStd" disabled>Submit</button>' +
            '        </div>' +
            '    </div>' +
            '</div>');
        $('#GlobalModal .modal-footer').addClass('hide');

        var totalSession = $('#totalSession').val();
        for (var i = 1; i <= parseInt(totalSession); i++) {
            $('#formAttdStd_Sesi').append('<option value="' + i + '">' + i + '</option>');
        }

        $('#formAttdStd_Sesi').change(function() {
            loadStdAttd();
        });


        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });

        loadStdAttd();

        $('#btnSubmitAttdStd').click(function() {
            var Sesi = $('#formAttdStd_Sesi').val();
            var formAttdStd_TotalStd = $('#formAttdStd_TotalStd').val();

            if (parseInt(formAttdStd_TotalStd) > 0) {

                loading_buttonSm('#btnSubmitAttdStd');
                $('button[data-dismiss=modal]').prop('disabled', true);

                var attdStudent = [];
                for (var i = 1; i <= formAttdStd_TotalStd; i++) {
                    var m = ($('#formAttdStd_Attd' + i).is(':checked')) ? '1' : '2';
                    var arr = {
                        ID: $('#formAttdStd_ID' + i).val(),
                        M: m,
                        D: $('#formAttdStd_Reason' + i).val()
                    };
                    attdStudent.push(arr);
                }

                var data = {
                    action: 'UpdateStudentAttd',
                    Meet: Sesi,
                    attdStudent: attdStudent
                };

                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api2/__crudAttendance2';

                $.post(url, {
                    token: token
                }, function(result) {
                    toastr.success('Attendance Saved', 'Success');
                    loadAttendace();
                    setTimeout(function() {
                        $('button[data-dismiss=modal],#btnSubmitAttdStd').prop('disabled', false);
                        $('#btnSubmitAttdStd').html('Submit');
                    }, 500);
                });

            } else {
                toastr.warning('Students Not Yet', 'Warning');
            }

        });

    });

    function loadStdAttd() {

        loading_page('#divStdAttd');
        $('#btnSubmitAttdStd').prop('disabled', true);

        var formAttdStd_Sesi = $('#formAttdStd_Sesi').val();
        var Meet = (formAttdStd_Sesi != 1 && formAttdStd_Sesi != '' && formAttdStd_Sesi != null) ? formAttdStd_Sesi : 1;

        var filterSD = $('#filterSD').val();

        var ID_Attd = filterSD.split('.')[1];
        var data = {
            action: 'readAttdStudent',
            Meet: Meet,
            ID_Attd: ID_Attd
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api2/__crudAttendance2';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            setTimeout(function() {
                $('#divStdAttd').html('<table class="table">' +
                    '    <thead>' +
                    '    <tr>' +
                    '        <th style="width: 1%;">No</th>' +
                    '        <th>Students</th>' +
                    '        <th style="width: 15%;">Attd</th>' +
                    '        <th style="width: 25%;">Reason</th>' +
                    '    </tr>' +
                    '    </thead>' +
                    '    <tbody id="rowAtdStd"></tbody>' +
                    '   <tr style="background: #f4f4f4;">' +
                    '       <td style="text-align: right;" colspan="2">Total</td>' +
                    '       <td><span id="viewStd"></span></td>' +
                    '       <td>-</td>' +
                    '   </tr>' +
                    '</table>');

                var StatusMeet = jsonResult.Attendance[0].StatusMeet;
                var DefaultAttendance = jsonResult.Setting[0].DefaultAttendance;

                $('#formAttdStd_TotalStd').val(jsonResult.Students.length);
                if (jsonResult.Students.length > 0) {
                    var no = 1;
                    $.each(jsonResult.Students, function(i, v) {

                        var check = 'checked';
                        if (StatusMeet == 1 || StatusMeet == '1') {
                            check = (v.M == 1 || v.M == '1') ? 'checked' : '';
                        } else {
                            check = (DefaultAttendance == 1 || DefaultAttendance == '1') ? 'checked' : '';
                        }

                        var d = (v.D != null && v.D != '') ? v.D : '';

                        $('#rowAtdStd').append('<tr id="trStd_' + no + '">' +
                            '<td>' + no + '</td>' +
                            '<td>' +
                            '<span style="font-size: 15px;">' + v.Name + '</span><br/>' + v.NPM + '<input class="hide" value="' + v.ID + '" id="formAttdStd_ID' + no + '">' +
                            '</td>' +
                            '<td>' +
                            '       <div class="checkbox checbox-switch switch-success ">' +
                            '           <label>' +
                            '               <input type="checkbox" class="checkAttdStd" ' + check + ' id="formAttdStd_Attd' + no + '">' +
                            '               <span></span>' +
                            '           </label>' +
                            '       </div>' +
                            '</td>' +
                            '<td><textarea class="form-control" id="formAttdStd_Reason' + no + '" rows="2">' + d + '</textarea></td>' +
                            '</tr>');

                        no += 1;
                    });
                    checkAttd();
                }

                $('#btnSubmitAttdStd').prop('disabled', false);
            }, 500);

        });
    }

    $('#btnClearAttd').click(function() {
        $('#GlobalModal .modal-header').html('<h4 class="modal-title">Clear Attendance</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4 col-md-offset-2">' +
            '        <div class="form-goup">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="form_Sesi"></select>' +
            '        </div>' +
            '    </div>' +
            '    <div class="col-md-4">' +
            '        <div class="form-goup">' +
            '            <label>User</label>' +
            '            <select class="form-control" id="form_User">' +
            '               <option value="0">- Clear All -</option>' +
            '               <option value="1">Student</option>' +
            '               <option value="2">Lecturer</option>' +
            '           </select>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-md-12" style="text-align: center;">' +
            '        <hr/>' +
            '        <button class="btn btn-danger" id="btnCleanAction">Clear</button>' +
            '        <button class="btn btn-default" data-dismiss="modal">Close</button>' +
            '    </div>' +
            '</div>');

        var totalSession = $('#totalSession').val();
        for (var i = 1; i <= parseInt(totalSession); i++) {
            $('#form_Sesi').append('<option value="' + i + '">' + i + '</option>');
        }

        $('#GlobalModal .modal-footer').addClass('hide');

        $('#btnCleanAction').click(function() {

            if (confirm('Are you sure?')) {
                loading_buttonSm('#btnCleanAction');
                $('button[data-dismiss=modal]').prop('disabled', true);

                var Sesi = $('#form_Sesi').val();
                var User = $('#form_User').val();

                var filterSD = $('#filterSD').val();
                var ID_Attd = filterSD.split('.')[1];

                var data = {
                    action: 'cleanAttendance',
                    Sesi: Sesi,
                    ID_Attd: ID_Attd,
                    User: User
                };

                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api2/__crudAttendance2';

                $.post(url, {
                    token: token
                }, function(result) {
                    toastr.success('Attd. Cleaned', 'Success');
                    loadAttendace();

                    setTimeout(function() {
                        // $('#GlobalModal').modal('hide');
                        $('#btnCleanAction').html('Clean');
                        $('button[data-dismiss=modal],#btnCleanAction').prop('disabled', false);

                    }, 500);
                });
            }




        });


        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });
    });



    $(document).on('change', '.checkAttdStd', function() {
        checkAttd();
    });

    function checkAttd() {
        var formAttdStd_TotalStd = $('#formAttdStd_TotalStd').val();

        var p = 0;
        if (parseInt(formAttdStd_TotalStd) > 0) {
            for (var i = 1; i <= formAttdStd_TotalStd; i++) {
                if ($('#formAttdStd_Attd' + i).is(':checked')) {
                    $('#trStd_' + i).css({
                        'background': '#ffffff',
                        'color': '#333'
                    });
                    p += 1;
                } else {
                    $('#trStd_' + i).css({
                        'background': '#ff000014',
                        'color': 'red'
                    });
                }
            }
        }

        $('#viewStd').html('<span style="color: blue;">' + p + '</span> of ' + formAttdStd_TotalStd);

    }

    // Remove Attendance Lecturer
    $(document).on('click', '.btnDeleteAttd', function() {

        if (confirm('Are you sure to remove?')) {
            var ID = $(this).attr('data-id');
            var data = {
                action: 'deleteAttendanceLecturer',
                ID: ID
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api2/__crudAttendance2';

            $.post(url, {
                token: token
            }, function(result) {
                toastr.success('Data removed', 'Success');
                loadAttendace();
            });
        }



    });


    // Echange
    $(document).on('click', '.showExchange', function() {
        var Sesi = $(this).attr('data-sesi');
        var viewDataExchange = $('#viewDataExchange' + Sesi).val();

        var dataExch = JSON.parse(viewDataExchange);

        $('#GlobalModal .modal-header').html('<h4 class="modal-title">Schedele Exchange</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-12">' +
            '        <table class="table">' +
            '            <tr>' +
            '                <td style="width: 20%;">Date Original</td>' +
            '                <td style="width: 1%;">:</td>' +
            '                <td>' +
            '                    <input data-fm="formExcDateOri" class="form-control" id="viewExcDateOri" readonly>' +
            '                    <input class="hide" id="formExcDateOri" readonly>' +
            '                </td>' +
            '            </tr>' +
            '            <tr>' +
            '                <td>Exchange to (date)</td>' +
            '                <td>:</td>' +
            '                <td>' +
            '                    <input data-fm="formExcDate" class="form-control" id="viewExcDate" readonly>' +
            '                    <input class="hide" id="formExcDate" readonly>' +
            '                </td>' +
            '            </tr>' +
            '            <tr>' +
            '                <td>Time</td>' +
            '                <td>:</td>' +
            '                <td>' +
            '                    <div class="row">' +
            '                        <div class="col-xs-4">' +
            '                            <div id="div_formExcStart" data-no="1" class="input-group">' +
            '                                <input data-format="hh:mm" type="text" id="formExcStart" class="form-control" value="' + dataExch.StartSessions.substr(0, 5) + '" />' +
            '                                <span class="add-on input-group-addon">' +
            '                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
            '                                </span>' +
            '                            </div>' +
            '                        </div>' +
            '                        <div class="col-xs-4">' +
            '                            <div id="div_formExcEnd" data-no="1" class="input-group">' +
            '                                <input data-format="hh:mm" type="text" id="formExcEnd" class="form-control" value="' + dataExch.EndSessions.substr(0, 5) + '" />' +
            '                                <span class="add-on input-group-addon">' +
            '                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
            '                                </span>' +
            '                            </div>' +
            '                        </div>' +
            '                    </div>' +
            '                </td>' +
            '            </tr>' +
            '            <tr>' +
            '                <td>Room</td>' +
            '                <td>:</td>' +
            '                <td>' +
            '                    <select class="select2-select-00 form-exam" style="max-width: 300px !important;" size="5"  id="formExcClassroom"></select>' +
            '                </td>' +
            '            </tr>' +
            '            <tr>' +
            '                <td>Reason</td>' +
            '                <td>:</td>' +
            '                <td>' +
            '                    <textarea class="form-control" rows="3" id="formExcReason"></textarea>' +
            '                </td>' +
            '            </tr>' +
            '            <tr>' +
            '                <td colspan="3" style="text-align: right;">' +
            '                    <button style="float: left" class="btn btn-danger" id="btnDeleteExch">Delete</button>' +
            '                    <button class="btn btn-primary" id="btnSubmitExch">Submit</button> ' +
            '                    <button class="btn btn-default" data-dismiss="modal">Close</button>' +
            '                </td>' +
            '            </tr>' +
            '        </table>' +
            '' +
            '<div class="alert alert-danger hide" id="viewBentrolSc"></div>' +
            '<div class="alert alert-warning hide" id="viewBentrolExc"></div>' +
            '<div class="alert alert-info hide" id="viewVanueBentrok">Bentrok dengan venue</div>' +
            '    </div>' +
            '</div>');

        // Load Room

        // Cek apakah ruangan sudah ada atau belum

        var selectRoom = (dataExch.ClassroomID != '' && dataExch.ClassroomID != null) ?
            dataExch.ClassroomID + '.' + dataExch.Seat + '.' + dataExch.SeatForExam :
            '';
        loadSelect2OptionClassroom('#formExcClassroom', selectRoom);
        $('#formExcClassroom').select2({
            allowClear: true
        });
        //

        $("#viewExcDateOri,#viewExcDate")
            .datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                onSelect: function() {
                    var form = $(this).attr('data-fm');
                    var data_date = moment($(this).datepicker("getDate")).format('YYYY-MM-DD');
                    $('#' + form).val(data_date);
                }
            });

        $('#viewExcDateOri').datepicker('setDate', new Date(dataExch.DateOriginal));
        $('#viewExcDate').datepicker('setDate', new Date(dataExch.Date));
        $('#formExcDateOri').val(dataExch.DateOriginal);
        $('#formExcDate').val(dataExch.Date);

        // $('#formExcStart').val();
        // $('#formExcEnd').val();


        $('#div_formExcStart,#div_formExcEnd').datetimepicker({
            pickDate: false,
            pickSeconds: false
        });

        $('#formExcReason').val(dataExch.Reason);

        $('#GlobalModal .modal-footer').addClass('hide');
        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });

        // console.log(JSON.parse(viewDataExchange));

        $('#btnSubmitExch').click(function() {

            var formExcDateOri = $('#formExcDateOri').val();
            var formExcDate = $('#formExcDate').val();
            var formExcStart = $('#formExcStart').val();
            var formExcEnd = $('#formExcEnd').val();
            var formExcClassroom = $('#formExcClassroom').val();
            var formExcReason = $('#formExcReason').val();

            if (formExcDateOri != '' && formExcDateOri != null &&
                formExcDate != '' && formExcDate != null &&
                formExcStart != '' && formExcStart != null &&
                formExcEnd != '' && formExcEnd != null &&
                formExcClassroom != '' && formExcClassroom != null &&
                formExcReason != '' && formExcReason != null) {

                var url2 = base_url_js + 'api2/__checkConflict_Venue';
                var textRoom = $('#formExcClassroom option:selected').text();
                var RoomName = textRoom.split('|');
                var data2 = {
                    Start: formExcDate + ' ' + formExcStart + ':00',
                    End: formExcDate + ' ' + formExcEnd + ':00',
                    RoomName: RoomName[0].trim()
                };
                var token2 = jwt_encode(data2, "UAP)(*");

                $.post(url2, {
                    token: token2
                }, function(result) {

                    var bool = result.bool;

                    if (bool) {
                        post2Exchange(dataExch.ID);
                    } else {
                        if (confirm('Conflict with Venue, are you sure?')) {
                            post2Exchange(dataExch.ID);
                        }
                    }

                });




            } else {
                toastr.error('All form required', 'Error');
            }


        });


        // BTN Delete
        $('#btnDeleteExch').click(function() {
            if (confirm('Are you sure?')) {

                loading_buttonSm('#btnDeleteExch');
                $('#btnSubmitExch,button[data-dismiss=modal]').prop('disabled', true);

                var data = {
                    action: 'delteExhange',
                    EXID: dataExch.ID,
                    UserID: dataExch.NIP,
                    Updated1By: dataExch.Updated1By,
                    Logging: {
                        Icon: sessionUrlPhoto,
                        Title: '<i class="fa fa-minus-circle margin-right" style="color: red;"></i> Schedule Exchange - Removed',
                        Description: "Your request has been removed by Academic (" + sessionName + ")",
                        URLDirect: "",
                        URLDirectStudent: "",
                        URLDirectLecturer: "attendance/schedule-exchange",
                        URLDirectLecturerKaprodi: "attendance/monitoring-schedule-exchange",
                        CreatedBy: sessionNIP,
                        CreatedName: sessionName,
                        CreatedAt: dateTimeNow()
                    }
                };
                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api2/__crudAttendance2';
                $.post(url, {
                    token: token
                }, function(result) {
                    toastr.success('Data deleted', 'Success');
                    loadAttendace();
                    setTimeout(function() {
                        $('#GlobalModal').modal('hide');
                    }, 500);
                });
            }
        });

    });

    function post2Exchange(EXID) {

        var formExcDateOri = $('#formExcDateOri').val();
        var formExcDate = $('#formExcDate').val();
        var formExcStart = $('#formExcStart').val();
        var formExcEnd = $('#formExcEnd').val();
        var formExcClassroom = $('#formExcClassroom').val();
        var formExcReason = $('#formExcReason').val();

        if (formExcDateOri != '' && formExcDateOri != null &&
            formExcDate != '' && formExcDate != null &&
            formExcStart != '' && formExcStart != null &&
            formExcEnd != '' && formExcEnd != null &&
            formExcClassroom != '' && formExcClassroom != null &&
            formExcReason != '' && formExcReason != null) {

            var ClassroomID = formExcClassroom.split('.')[0];

            loading_buttonSm('#btnSubmitExch');
            $('button[data-dismiss=modal],#btnDeleteExch').prop('disabled', true);

            var dayID = (moment(formExcDate).days() == 0) ? 7 : moment(formExcDate).days();

            var data = {
                action: 'updateExhange',
                EXID: EXID,
                dataUpdate: {
                    DateOriginal: formExcDateOri,
                    Date: formExcDate,
                    DayID: dayID,
                    StartSessions: formExcStart,
                    EndSessions: formExcEnd,
                    ClassroomID: ClassroomID,
                    Reason: formExcReason,
                    Status: '2'
                }
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api2/__crudAttendance2';

            $.post(url, {
                token: token
            }, function(jsonResult) {

                $('#viewVanueBentrok').addClass('hide');
                if (parseInt(jsonResult.Vanue) > 0) {
                    $('#viewVanueBentrok').removeClass('hide');
                    $.getJSON(base_url_js + 'api/__checkBentrokScheduleAPI');
                }

                if (jsonResult.Status == 1 || jsonResult.Status == '1') {
                    toastr.success('Data updated', 'Success');
                    loadAttendace();

                    var url3 = base_url_js + 'api/__checkBentrokScheduleAPI';
                    var data3 = {
                        EXID: EXID
                    };
                    var token3 = jwt_encode(data3, 'UAP)(*');

                    $.post(url3, {
                        token: token3
                    }, function(result) {
                        setTimeout(function() {
                            $('#GlobalModal').modal('hide');
                        }, 500);
                    });



                } else {
                    $('#viewBentrolSc').addClass('hide');
                    $('#viewBentrolExc').addClass('hide');

                    var Schedule = jsonResult.Schedule;
                    var Exchange = jsonResult.Exchange;

                    if (Schedule.length > 0) {
                        $('#viewBentrolSc').removeClass('hide');
                        $('#viewBentrolSc').html('<ul id="ulBentrok"></ul>');
                        $.each(Schedule, function(i, v) {

                            var dC = v.DetailsCourse[0];

                            $('#ulBentrok').append('<li>' +
                                '<b>' + dC.MKCode + ' - ' + dC.NameEng + '</b>' +
                                '<br/>Group : ' + v.ClassGroup +
                                '<br/>' + v.DayEng + ', ' + v.StartSessions.substr(0, 5) + ' - ' + v.EndSessions.substr(0, 5) + ' | ' + v.Room +
                                '</li>');
                        });
                    }

                    if (Exchange.length > 0) {
                        $('#viewBentrolExc').removeClass('hide');
                        $('#viewBentrolExc').html('<ul id="ulBentrokExc"></ul>');
                        $.each(Exchange, function(i, v) {
                            $('#ulBentrokExc').append('<li>' +
                                '<b>' + v.MKCode + ' - ' + v.CourseEng + '</b>' +
                                '<br/>Group : ' + v.ClassGroup +
                                '<br/>' + moment(v.Date).format('dddd, DD MMM YYYY') +
                                ' ' + v.StartSessions.substr(0, 5) + ' - ' + v.EndSessions.substr(0, 5) +
                                ' | ' + v.Room +
                                '</li>');
                        });
                    }

                    setTimeout(function() {
                        $('#btnSubmitExch').html('Submit');
                        $('button[data-dismiss=modal],#btnDeleteExch,#btnSubmitExch').prop('disabled', false);
                    }, 500);
                }


            });

        }



    }
</script>