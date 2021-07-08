<style>
    .table thead>tr>th,
    .table tbody>tr>th,
    .table tfoot>tr>th,
    .table thead>tr>td,
    .table tbody>tr>td,
    .table tfoot>tr>td {
        border-top: none;
    }

    .table tbody+tbody {
        border-top: 1px solid #ddd;
    }

    #tableCourse thead tr th,
    #tableTeamTeaching thead tr th {
        text-align: center;
        background: #607d8b;
        color: #FFFFFF;
    }

    #tableCourse tbody tr td,
    #tableTeamTeaching tbody tr td {
        text-align: center;
    }

    #formProgramsCampusID,
    #formBaseProdi {
        max-width: 250px;
    }

    .radio,
    .checkbox {
        margin-top: 0px;
    }

    .panel-input-share-bkd {
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .panel-input-share-bkd input {
        width: 77px;
        display: initial;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }
</style>

<div class="row" style="margin-bottom: 50px;">


    <div class="col-md-8 col-md-offset-2">
        <a href="<?php echo base_url('academic/timetables'); ?>" class="btn btn-warning"><i class="fa fa-arrow-left margin-right"></i> Back to List</a>
        <hr />
        <div id="showAlertEditingCourse"></div>
        <table class="table panel-edit-course hide">
            <tr>
                <td style="width: 20%;">Academic Year</td>
                <td style="width: 1%;">:</td>
                <td>
                    <b id="viewSemester">-</b>
                    <input id="formSemesterID" class="hide" type="hidden" readonly />
                    <input id="formScheduleID" class="hide" type="hidden" readonly />
                </td>
            </tr>
            <tr>
                <td>Program</td>
                <td>:</td>
                <td>
                    <select class="form-control" id="formProgramsCampusID"></select>
                </td>
            </tr>
            <tbody>
                <tr>
                    <td>Programme Study</td>
                    <td>:</td>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" id="formBaseProdi">
                                    <option value="" selected disabled>--- Select Programme Study ---</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="formGroupProdi" style="max-width: 200px;" disabled>
                                    <option selected disabled>-- Select Group Prodi --</option>
                                </select>
                                <input class="hide" readonly value="0" id="viewGroupProdi" />
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Course</td>
                    <td>:</td>
                    <td>
                        <div id="dataMK"></div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><button class="btn btn-sm btn-default btn-default-success" id="btnAddNewCourse">Add Course</button></td>
                </tr>
            </tbody>
        </table>

        <hr />

        <table class="table table-bordered table-striped" id="tableCourse">
            <thead>
                <tr>
                    <th rowspan="2">Course</th>
                    <th rowspan="2" style="width: 20%;">Prodi</th>
                    <th rowspan="2" style="width: 10%;">Class Of</th>
                    <th rowspan="2" style="width: 7%;">Smt</th>
                    <th rowspan="2" style="width: 7%;">Offer to <br /> <small>(Smt)</small></th>
                    <th colspan="2" style="width: 16%;">Student</th>
                    <th class="panel-edit-course hide" rowspan="2" style="width: 7%;">Action</th>
                </tr>
                <tr>
                    <th style="width: 8%;">Plan</th>
                    <th style="width: 8%;">Apprv</th>
                </tr>
            </thead>
            <tbody id="dataRow"></tbody>
        </table>

        <hr />
        <table class="table panel-edit-course hide">
            <tr>
                <td style="width: 20%;">Attendance</td>
                <td style="width: 1%;">:</td>
                <td>
                    <div class="checkbox checbox-switch switch-primary">
                        <label>
                            <input type="checkbox" id="formAttendance">
                            <span></span>
                            <i> | Filter Attendance in UAS (75%)</i>
                        </label>
                    </div>
                </td>

            <tr>
            <tr>
                <td>Online Learning</td>
                <td>:</td>
                <td>
                    <div class="checkbox checbox-switch switch-primary">
                        <label>
                            <input type="checkbox" id="formOnlineLearning">
                            <span></span>
                            <!--                            <i> | Filter Attendance in UAS (75%)</i>-->
                        </label>
                    </div>
                </td>

            <tr>
                <td>Class Group</td>
                <td>:</td>
                <td><input class="form-control" id="formClassGroup" style="max-width: 130px;" /> </td>
            </tr>
            <tr>
                <td>Coordinator</td>
                <td>:</td>
                <td>
                    <select class="select2-select-00 full-width-fix form-jadwal-edit-sc" size="5" id="formCoordinator">
                        <option value=""></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Team Teaching</td>
                <td>:</td>
                <td>
                    <select class="select2-select-00 full-width-fix form-jadwal-edit-sc" size="5" multiple id="formTeamTeaching"></select>
                </td>
            </tr>

            <tr style="border-top: 1px solid #ccc;">
                <td>Point Credit BKD</td>
                <td>:</td>
                <td>
                    <input id="valTotalCredit" class="hide">
                    <textarea id="valShareCreditBKD_Ori" class="hide"></textarea>
                    <textarea id="valShareCreditBKD" class="hide"></textarea>
                    <div id="viewListShareCredit"></div>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <div style="text-align: right;">
                        <button class="btn btn-success" id="btnSaveEditInfo">Save</button>
                    </div>
                </td>
            </tr>

        </table>
    </div>

</div>

<script>
    $(document).ready(function() {
        window.SemesterAntara = 0;
        window.EditTimeTable = null;
        loading_modal_show();
        checkAccessToEditTimeTable();
        loadEditCourseSchedule();

        loadSelectOptionBaseProdi('#formBaseProdi');
    });


    function searchShareCredit(nameKey, myArray) {
        for (var i = 0; i < myArray.length; i++) {
            if (myArray[i].NIP === nameKey) {
                return myArray[i];
            }
        }
    }


    function checkAccessToEditTimeTable() {

        var SemesterID = parseInt("<?php echo $SemesterID ?>");

        var data = {
            action: 'getDateKRSOnline',
            SemesterID: SemesterID
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api/__crudKurikulum';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                var d = jsonResult[0];
                if (d.EditTimeTable == '0') {
                    // $('.panel-edit-course').removeClass('hide');
                    $('.panel-edit-course').remove();

                    $('#showAlertEditingCourse').html('<div class="alert alert-danger" role="alert" style="text-align: center;">' +
                        '            <b><i class="fa fa-warning fa-3x"></i>' +
                        '                <br/>Unable to make changes or delete the current schedule</b>' +
                        '            <div>' +
                        '                for more information please contact the <u>IT Development Team</u>' +
                        '            </div>' +
                        '        </div>');

                } else {
                    $('.panel-edit-course').removeClass('hide');
                }

                window.EditTimeTable = d.EditTimeTable;
                loadEditCourse();

                setTimeout(function() {
                    loading_modal_hide();
                }, 1000);
            }





        });

    }


    // ==== Control Edit Course ====

    $(document).on('change', '#formBaseProdi', function() {

        var Prodi = $(this).val();
        if (Prodi != '') {
            var ProdiID = Prodi.split('.')[0];
            getCourseOfferings(ProdiID, '');
            loadProdiGroup(ProdiID, '');
        }

    });

    function loadProdiGroup(ProdiID, divNum) {
        var data = {
            action: 'readProdiGroup',
            ProdiID: ProdiID
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api/__crudSchedule';
        $('#formGroupProdi' + divNum).empty();
        $('#formGroupProdi' + divNum).append('<option selected disabled>-- Select Group Prodi --</option>');
        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {
                $('#formGroupProdi' + divNum).prop('disabled', false);
                $('#viewGroupProdi' + divNum).val(1);
                for (var i = 0; i < jsonResult.length; i++) {
                    var d = jsonResult[i];
                    $('#formGroupProdi' + divNum).append('<option value="' + d.ID + '">' + d.Code + '</option>');
                }
            } else {
                $('#formGroupProdi' + divNum).prop('disabled', true);
                $('#viewGroupProdi' + divNum).val(0);
            }
        });
    }

    $('#btnAddNewCourse').click(function() {

        var formScheduleID = $('#formScheduleID').val();
        var formBaseProdi = $('#formBaseProdi').val();
        var formMataKuliah = $('#formMataKuliah').val();

        var viewGroupProdi = parseInt($('#viewGroupProdi').val());
        var formGroupProdi = $('#formGroupProdi').val();

        if (viewGroupProdi == 1) {
            if (formBaseProdi != '' && formBaseProdi != null &&
                formMataKuliah != '' && formMataKuliah != null &&
                formGroupProdi != '' && formGroupProdi != null
            ) {

                var ProdiID = formBaseProdi.split('.')[0];
                var CDID = formMataKuliah.split('|')[0];
                var MKID = formMataKuliah.split('|')[1];

                var dataWhere = {
                    ScheduleID: formScheduleID,
                    ProdiID: ProdiID,
                    CDID: CDID,
                    MKID: MKID
                };
                if (viewGroupProdi == 1) {
                    dataWhere.ProdiGroupID = formGroupProdi;
                }

                var data = {
                    action: 'checktoAddNewCourse',
                    dataWhere: dataWhere,
                    UpdateLog: {
                        UpdateBy: sessionNIP,
                        UpdateAt: dateTimeNow()
                    }
                };

                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api/__crudSchedule';
                $.post(url, {
                    token: token
                }, function(jsonResult) {
                    if (jsonResult.Status == 0 || jsonResult.Status == '0') {
                        toastr.warning('This course exist in schedule', 'Warning');
                    } else {
                        var formClassGroup = $('#formClassGroup').val();
                        var arrToken = {
                            Subject: 'Updating Timetable - Add Course | Group : ' + formClassGroup,
                            URL: 'academic/timetables/list',
                            From: sessionName,
                            Icon: sessionUrlPhoto
                        };
                        var dataToken = jwt_encode(arrToken, 'UAP)(*');
                        addNotification(dataToken, null);
                        loadEditCourse();
                        toastr.success('Data saved', 'Success');
                    }
                });

            } else {
                toastr.error('All form required', 'Error');
            }
        } else {
            if (formBaseProdi != '' && formBaseProdi != null &&
                formMataKuliah != '' && formMataKuliah != null) {

                var ProdiID = formBaseProdi.split('.')[0];
                var CDID = formMataKuliah.split('|')[0];
                var MKID = formMataKuliah.split('|')[1];

                var dataWhere = {
                    ScheduleID: formScheduleID,
                    ProdiID: ProdiID,
                    CDID: CDID,
                    MKID: MKID
                };

                var data = {
                    action: 'checktoAddNewCourse',
                    dataWhere: dataWhere,
                    UpdateLog: {
                        UpdateBy: sessionNIP,
                        UpdateAt: dateTimeNow()
                    }
                };

                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api/__crudSchedule';
                $.post(url, {
                    token: token
                }, function(jsonResult) {
                    if (jsonResult.Status == 0 || jsonResult.Status == '0') {
                        toastr.warning('This course exist in schedule', 'Warning');
                    } else {
                        var formClassGroup = $('#formClassGroup').val();
                        var arrToken = {
                            Subject: 'Updating Timetable - Add Course | Group : ' + formClassGroup,
                            URL: 'academic/timetables/list',
                            From: sessionName,
                            Icon: sessionUrlPhoto
                        };
                        var dataToken = jwt_encode(arrToken, 'UAP)(*');
                        addNotification(dataToken, null);
                        loadEditCourse();
                        toastr.success('Data saved', 'Success');
                    }
                });

            } else {
                toastr.error('All form required', 'Error');
            }
        }

    });

    $(document).on('click', '.btnDelEditCourse', function() {
        var SDCID = $(this).attr('data-sdc');
        var TotalCourse = $(this).attr('data-totalcourse');

        if (parseInt(TotalCourse) == 1) {
            $('#NotificationModal .modal-body').html('' +
                '<div style="text-align: center;"><div style="background:lightyellow;padding:10px;border:1px solid red;margin-bottom:15px;"><span style="color:red;">This course cannot delete</span></div> ' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>');
        } else {
            $('#NotificationModal .modal-body').html('<div style="text-align: center;">' +
                '<div style="background:lightyellow;padding:10px;border:1px solid red;margin-bottom:15px;"><span style="color:red;">If you delete this data, then the data student in the study planning will be deleted too</span></div> ' +
                '<button type="button" class="btn btn-danger" id="btnActionDeleteEditCourse" data-id="' + SDCID + '" style="margin-right: 5px;">Yes</button>' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>');
        }

        $('#NotificationModal').modal({
            'backdrop': 'static',
            'show': true
        });

    });

    $(document).on('click', '#btnActionDeleteEditCourse', function() {
        var SemesterID = parseInt("<?php echo $SemesterID ?>");
        var ScheduleID = parseInt("<?php echo $ScheduleID ?>");
        var SDCID = $(this).attr('data-id');

        if (SemesterID != '' && SemesterID != null &&
            ScheduleID != '' && ScheduleID != null &&
            SDCID != '' && SDCID != null) {

            loading_button('#btnActionDeleteEditCourse');
            $('button[data-dismiss=modal]').prop('disabled', true);

            var data = {
                action: 'deleteInEditCourse',
                SemesterID: SemesterID,
                ScheduleID: ScheduleID,
                SDCID: SDCID,
                UpdateLog: {
                    UpdateBy: sessionNIP,
                    UpdateAt: dateTimeNow()
                }
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__crudSchedule';
            $.post(url, {
                token: token
            }, function(result) {

                var formClassGroup = $('#formClassGroup').val();
                var arrToken = {
                    Subject: 'Updating Timetable - Deleting Course | Group : ' + formClassGroup,
                    URL: 'academic/timetables/list',
                    From: sessionName,
                    Icon: sessionUrlPhoto
                };
                var dataToken = jwt_encode(arrToken, 'UAP)(*');
                addNotification(dataToken, null);

                loadEditCourse();
                setTimeout(function() {
                    $('#NotificationModal').modal('hide');
                }, 500);
            });
        }

    });

    function loadEditCourse() {

        var SemesterID = parseInt("<?php echo $SemesterID ?>");
        var ScheduleID = parseInt("<?php echo $ScheduleID ?>");

        var data = {
            action: 'loadEditCourse',
            SemesterID: SemesterID,
            ScheduleID: ScheduleID
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api/__crudSchedule';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.ScheduleDetails.length > 0) {
                $('#dataRow').empty();
                for (var i = 0; i < jsonResult.ScheduleDetails.length; i++) {
                    var d = jsonResult.ScheduleDetails[i];

                    var c_of = (d.ProdiGroup != null && d.ProdiGroup != '') ? d.Year + ' - <span style="color: #2196f3;"><b>' + d.ProdiGroup + '</b></span>' : d.Year;

                    var btnActCourse = (EditTimeTable == '1') ?
                        '<td><button class="btn btn-sm btn-default btn-default-danger btnDelEditCourse" data-totalcourse="' + jsonResult.ScheduleDetails.length + '" data-sdc="' + d.SDCID + '"><i class="fa fa-trash"></i></button></td>' :
                        '';

                    $('#dataRow').append('<tr>' +
                        '<td style="text-align: left;"><b>' + d.MKCode + ' - ' + d.MKNameEng + '</b><br/><i>' + d.MKNameEng + '</i></td>' +
                        '<td style="text-align: left;">' + d.Prodi + '</td>' +
                        '<td>' + c_of + '</td>' +
                        '<td>' + d.Semester + '</td>' +
                        '<td style="background: lightyellow;">' + d.Offerto + '</td>' +
                        '<td>' + d.TotalStd_Plan.length + '</td>' +
                        '<td>' + d.TotalStd_Approve.length + '</td>' + btnActCourse +
                        '</tr>');
                }
            }

        });

    }

    function getCourseOfferings(ProdiID, divNum) {
        var url = base_url_js + 'api/__crudCourseOfferings';
        var SemesterID = $('#formSemesterID').val();
        var data = {
            action: 'readToSchedule',
            formData: {
                SemesterID: SemesterID,
                ProdiID: ProdiID,
                IsSemesterAntara: '' + SemesterAntara
            }
        };
        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {

            // console.log(jsonResult);

            if (jsonResult.length > 0) {
                $('#dataMK' + divNum).html('<select class="select2-select-00 full-width-fix" size="5" id="formMataKuliah' + divNum + '">' +
                    '                        <option value=""></option>' +
                    '                    </select>');

                for (var i = 0; i < jsonResult.length; i++) {
                    var semester = jsonResult[i].Offerings.Semester;

                    var mk = jsonResult[i].Details;
                    for (var m = 0; m < mk.length; m++) {
                        var dataMK = mk[m];
                        var asalSmt = (semester != dataMK.Semester) ? '(' + dataMK.Semester + ')' : '';
                        var schDisabled = (dataMK.ScheduleID != "") ? '' : '';
                        var schMK = (dataMK.ScheduleID != "") ? 'highlighted' : '';
                        $('#formMataKuliah' + divNum).append('<option value="' + dataMK.CDID + '|' + dataMK.ID + '|' + dataMK.TotalSKS + '" class="' + schMK + '" ' + schDisabled + '>Smt ' + semester + ' ' + asalSmt + ' - ' + dataMK.MKCode + ' | ' + dataMK.MKNameEng + '</option>');
                    }

                    $('#formMataKuliah' + divNum).append('<option disabled>-------</option>');

                }

                $('#formMataKuliah' + divNum).select2({
                    allowClear: true
                });
            } else {
                $('#dataMK' + divNum).html('<b>No Course To Offerings</b>')
            }
        });
    }

    // ===========================



    // ===== Control Edit Group, Coordinator, Team Teaching

    $('#btnSaveEditInfo').click(function() {



        var SemesterID = parseInt("<?php echo $SemesterID ?>");
        var ScheduleID = parseInt("<?php echo $ScheduleID ?>");

        var totalCreditShare = 0;
        var dataCreditSahre = [];
        $('.input-share-credit').each(function(i, v) {
            var val = $(this).val();
            var NIP = $(this).attr('data-nip');
            var CreditOri = $(this).attr('data-credit-ori');
            var Credit = $(this).attr('data-credit');
            totalCreditShare = totalCreditShare + parseFloat(val);

            var arr = {
                ScheduleID: ScheduleID,
                NIP: NIP,
                ShareCredit: val,
                CreditOri: parseFloat(CreditOri),
                Credit: parseFloat(Credit)
            };
            dataCreditSahre.push(arr);
        });

        var formClassGroup = $('#formClassGroup').val();
        var formCoordinator = $('#formCoordinator').val();
        var formTeamTeaching = $('#formTeamTeaching').val();

        if (formClassGroup != '' && formClassGroup != null &&
            formCoordinator != '' && formCoordinator != null &&
            totalCreditShare == 100) {

            loading_button('#btnSaveEditInfo');

            var TeamTeaching = '0';
            var dataTeamTeaching = [];
            if (formTeamTeaching != null) {
                TeamTeaching = '1';
                for (var i = 0; i < formTeamTeaching.length; i++) {
                    var d = formTeamTeaching[i];
                    var arr = {
                        ScheduleID: ScheduleID,
                        NIP: d,
                        Status: '0'
                    };
                    dataTeamTeaching.push(arr);
                }
            }

            var Attendance = ($('#formAttendance').is(':checked')) ? '1' : '0';
            var OnlineLearning = ($('#formOnlineLearning').is(':checked')) ? '1' : '0';

            var data = {
                action: 'updateInfoInEditCourse',
                SemesterID: SemesterID,
                ScheduleID: ScheduleID,
                UpdateForm: {
                    OnlineLearning: OnlineLearning,
                    Attendance: Attendance,
                    ClassGroup: formClassGroup,
                    Coordinator: formCoordinator,
                    TeamTeaching: TeamTeaching
                },
                dataTeamTeaching: dataTeamTeaching,
                dataCreditSahre: dataCreditSahre,
                UpdateLog: {
                    UpdateBy: sessionNIP,
                    UpdateAt: dateTimeNow()
                }
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__crudSchedule';
            $.post(url, {
                token: token
            }, function(jsonResult) {

                $('#formClassGroup').css('border', '1px solid #ccc');
                setTimeout(function() {
                    if (jsonResult.Status == 0 || jsonResult.Status == '0') {
                        toastr.warning('Class Group is exist', 'Warning');
                        $('#formClassGroup').css('border', '1px solid red');
                    } else {

                        var arrToken = {
                            Subject: 'Updating Timetable - Info Class | Group : ' + formClassGroup,
                            URL: 'academic/timetables/list',
                            From: sessionName,
                            Icon: sessionUrlPhoto
                        };
                        var dataToken = jwt_encode(arrToken, 'UAP)(*');
                        addNotification(dataToken, null);

                        toastr.success('Update data saved', 'Success');
                    }
                    $('#btnSaveEditInfo').prop('disabled', false).html('Save');
                }, 500);
            });

        } else if (totalCreditShare != 100) {
            toastr.warning('Total Credit BKD must be equal 100', 'Warning');
        }

    });

    function loadEditCourseSchedule() {

        var SemesterID = parseInt("<?php echo $SemesterID ?>");
        var ScheduleID = parseInt("<?php echo $ScheduleID ?>");

        var data = {
            action: 'loadEditCourseSchedule',
            SemesterID: SemesterID,
            ScheduleID: ScheduleID
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api/__crudSchedule';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            var s = jsonResult.Schedule[0];
            loadSelectOptionConf('#formProgramsCampusID', 'programs_campus', s.ProgramsCampusID);
            $('#viewSemester').text(s.SemesterName);
            $('#formSemesterID').val(s.SemesterID);

            var ct_attenance = (s.Attendance == '1') ? true : false;
            $('#formAttendance').prop('checked', ct_attenance);

            var ct_OnlineLearning = (s.OnlineLearning == '1') ? true : false;
            $('#formOnlineLearning').prop('checked', ct_OnlineLearning);

            $('#formClassGroup').val(s.ClassGroup);
            $('#formScheduleID').val(s.ScheduleID);


            loadSelectOptionLecturersSingle('#formCoordinator', s.Coordinator);
            $('#formCoordinator').select2({
                allowClear: true
            });



            var team = '';
            if (s.TeamTeaching == 1) {
                team = s.TeamTeachingNIP;
            }
            loadSelectOptionLecturersSingle('#formTeamTeaching', team);
            $('#formTeamTeaching').select2({
                allowClear: true
            });

            $('#formCoordinator').change(function() {
                var valShareCreditBKD = $('#valShareCreditBKD').val();

                if (valShareCreditBKD != '' && valShareCreditBKD != null) {
                    var d = JSON.parse(valShareCreditBKD);
                    // Perubahan
                    var formCoordinator = $('#formCoordinator').val();

                    if (formCoordinator != '' && formCoordinator != null) {
                        if (formCoordinator != d.Coordinator.NIP) {
                            d.Coordinator.NIP = formCoordinator;
                            var formCoordinatorName = $('#formCoordinator option:selected').text();
                            d.Coordinator.Name = formCoordinatorName.split('|')[1];
                        }
                    } else {
                        d.Coordinator.NIP = '';
                        d.Coordinator.Name = '<span style="color: red;">Coordinator are required</span>';
                    }

                    $('#valShareCreditBKD').val(JSON.stringify(d));

                }

                loadShareCreditBKD();
            });

            $('#formTeamTeaching').change(function() {

                var valShareCreditBKD = $('#valShareCreditBKD_Ori').val();

                console.log(valShareCreditBKD);

                if (valShareCreditBKD != '') {
                    var d = JSON.parse(valShareCreditBKD);
                    var dataTeam = d.Team;
                    // Perubahan
                    var formTeamTeaching = $('#formTeamTeaching').val();

                    console.log(formTeamTeaching);

                    var newTeam = [];

                    if (formTeamTeaching != '' && formTeamTeaching != null) {
                        for (var i = 0; i < formTeamTeaching.length; i++) {
                            var NIP = formTeamTeaching[i];

                            console.log(dataTeam);

                            if (dataTeam.length > 0) {
                                var resultObject = searchShareCredit(NIP, dataTeam);
                                if (typeof resultObject !== "undefined") {
                                    newTeam.push(resultObject);
                                } else {
                                    var formTeamTeachingName = $('#formTeamTeaching option[value="' + NIP + '"]').text().split('|')[1];
                                    var newArr = {
                                        Name: formTeamTeachingName,
                                        NIP: NIP,
                                        ShareCredit: 0
                                    };
                                    newTeam.push(newArr);
                                }
                            } else {
                                var formTeamTeachingName = $('#formTeamTeaching option[value="' + NIP + '"]').text().split('|')[1];
                                var newArr = {
                                    Name: formTeamTeachingName,
                                    NIP: NIP,
                                    ShareCredit: 0
                                };
                                newTeam.push(newArr);
                            }


                        }
                    }

                    console.log(newTeam);

                    if (newTeam.length > 0) {
                        d.Team = newTeam;
                        $('#valShareCreditBKD').val(JSON.stringify(d));
                    }

                    loadShareCreditBKD();


                }

            });

            var arr_valShareCreditBKD = {
                Coordinator: {
                    NIP: s.Coordinator,
                    Name: s.CoordinatorName,
                    ShareCredit: s.CoordinatorCreditPoint
                },
                Team: s.TeamTeachingDetail
            };

            $('#valShareCreditBKD').val(JSON.stringify(arr_valShareCreditBKD));
            $('#valShareCreditBKD_Ori').val(JSON.stringify(arr_valShareCreditBKD));

            $('#valTotalCredit').val(s.TotalCredit);

            loadShareCreditBKD();


        });

    }

    function loadShareCreditBKD() {

        loading_page('#viewListShareCredit');

        // Cek setelah dilakuakn change

        var valShareCreditBKD = $('#valShareCreditBKD').val();

        var valTotalCredit = $('#valTotalCredit').val();

        if (valShareCreditBKD != '') {
            var d = JSON.parse(valShareCreditBKD);

            var listTeam = '';

            var TotalPointCredit = 0;

            if (d.Team.length > 0) {
                var formTeamTeaching = $('#formTeamTeaching').val();
                console.log(formTeamTeaching);
                if (formTeamTeaching != null && formTeamTeaching != '') {
                    $.each(d.Team, function(i, v) {
                        var valCr = (v.ShareCredit != '' && v.ShareCredit != null) ? parseFloat(v.ShareCredit) : 0;

                        var CreditOri = (valCr > 0) ? (valCr / 100) * parseInt(valTotalCredit) : 0;
                        var CreditResult = (CreditOri > 0) ? parseFloat(CreditOri).toFixed(2) : 0;

                        listTeam = listTeam + '<li class="list-group-item">' + v.NIP + ' | ' + v.Name + '' +
                            '                           <div class="panel-input-share-bkd">' +
                            '                              <i class="fa fa-percent"></i> <input class="form-control input-sm input-share-credit" data-credit-ori="' + CreditOri + '" data-credit="' + CreditResult + '" data-nip="' + v.NIP + '" value="' + valCr + '" type="number">' +
                            '  <span id="crdt_' + v.NIP + '"> | ' + CreditOri + ' | ' + CreditResult + '</span>' +
                            '                           </div>' +
                            '                        </li>';

                        TotalPointCredit = TotalPointCredit + valCr;

                    });
                }

            }

            setTimeout(function() {

                var valCr = (d.Coordinator.ShareCredit != '' && d.Coordinator.ShareCredit != null) ? d.Coordinator.ShareCredit : 0;
                var CreditOri = (valCr > 0) ? (valCr / 100) * parseInt(valTotalCredit) : 0;
                var CreditResult = (CreditOri > 0) ? parseFloat(CreditOri).toFixed(2) : 0;

                var NIP = (d.Coordinator.NIP != '' && d.Coordinator.NIP != null) ? d.Coordinator.NIP + ' | ' : '';

                TotalPointCredit = TotalPointCredit + parseFloat(valCr);

                $('#viewListShareCredit').html('' +
                    '<ul class="list-group">' +
                    '                        <li class="list-group-item">' + NIP + d.Coordinator.Name + '' +
                    '                           <div class="panel-input-share-bkd">' +
                    '                               <i class="fa fa-percent"></i> <input class="form-control input-sm input-share-credit" data-credit-ori="' + CreditOri + '" data-credit="' + CreditResult + '" data-nip="' + d.Coordinator.NIP + '" value="' + valCr + '" type="number">' +
                    '  <span id="crdt_' + d.Coordinator.NIP + '"> | ' + CreditOri + ' | ' + CreditResult + '</span>' +
                    '                           </div>' +
                    '                        </li>' + listTeam +
                    '                    </ul>Total Point : <span id="viewTotalPointCredit">' + TotalPointCredit + '</span> | Total Credit : ' + valTotalCredit);

            }, 500);

        }

    }

    $(document).on('keyup', '.input-share-credit', function() {
        var valTotalCredit = $('#valTotalCredit').val();
        var NIP = $(this).attr('data-nip');
        var valCr = $(this).val();

        var CreditOri = 0;
        var CreditResult = 0;

        if (parseFloat(valCr) > 0) {
            CreditOri = (valCr > 0) ? (valCr / 100) * parseInt(valTotalCredit) : 0;
            CreditResult = (CreditOri > 0) ? parseFloat(CreditOri).toFixed(2) : 0;
        }

        $(this).attr('data-credit-ori', CreditOri);
        $(this).attr('data-credit', CreditResult);

        $('#crdt_' + NIP).html(' | ' + CreditOri + ' | ' + CreditResult);

        var TotalPointCredit = 0;
        $('.input-share-credit').each(function(i, v) {
            var val = $(this).val();
            TotalPointCredit = TotalPointCredit + parseFloat(val);
        });

        $('#viewTotalPointCredit').html(TotalPointCredit);

    });
</script>