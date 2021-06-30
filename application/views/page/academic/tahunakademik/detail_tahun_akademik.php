<style>
    .tr-invers {
        background: #0f1f4bc4;
        color: #fff;
    }

    #tableDetailTA .form-control[readonly],
    .form-tahun-akademik[readonly] {
        cursor: cell;
        background-color: #fff;
        color: #333;
    }

    #tableSPKRS tr th,
    #tableSPKRS tr td {
        text-align: center;
    }
</style>

<div class="alert alert-info" role="alert">
    <b><span id="nameTahunAkademik"></span></b>
</div>

<!--<input id="formSemesterID" value="--><?php //echo $ID; 
                                            ?>
<!--" readonly />-->
<table class="table table-bordered table-striped" id="tableDetailTA">
    <thead>
        <tr class="tr-invers">
            <th rowspan="2" class="th-center">Keterangan</th>
            <th colspan="2" class="th-center">
                Global Setting
            </th>
            <th rowspan="2" class="th-center">Action</th>
        </tr>
        <tr class="tr-invers">
            <th class="th-center">Start</th>
            <th class="th-center">End</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Bayar BPP</td>
            <td>
                <input type="text" id="bpp_start" nextElement="bpp_end" name="regular" class="form-control form-tahun-akademik" readonly>
            </td>
            <td>
                <input type="text" id="bpp_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                <!--            <a href="javascript:void(0);" data-head="KRS" data-load="prodi" class="btn btn-sm btn-warning btn-block more_details">Special Case</a>-->
                -
            </td>
        </tr>
        <tr>
            <td>Krs</td>
            <td colspan="3">
                <div style="text-align: center;color: #9E9E9E;font-weight: bold;">
                    <i class="fa fa-lock" style="margin-right: 5px;"></i> Locked by IT
                </div>
            </td>
            <td class="hide">
                <input type="text" id="krs_start" nextElement="krs_end" name="regular" class="form-control form-tahun-akademik" readonly>
            </td>
            <td class="hide">
                <input type="text" id="krs_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td class="hide">
                <a href="javascript:void(0);" id="idBtnSPKRS" data-head="KRS" data-load="prodi" class="btn btn-sm btn-warning btn-block">Special Case</a>
            </td>
        </tr>
        <tr>
            <td>Bayar SKS</td>
            <td>
                <input type="text" id="bayar_start" nextelement="bayar_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="bayar_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                <!--            <a href="javascript:void(0);" data-head="Bayar" data-load="prodi" class="btn btn-sm btn-warning btn-block more_details">Special Case</a>-->
                -
            </td>
        </tr>
        <tr>
            <td>Kuliah</td>
            <td>
                <input type="text" id="kuliah_start" nextelement="kuliah_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="kuliah_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                <!--            <a href="javascript:void(0);" data-head="Kuliah" data-load="prodi" class="btn btn-sm btn-warning btn-block more_details">Special Case</a>-->
                -
            </td>
        </tr>
        <tr>
            <td>UTS</td>
            <td>
                <input type="text" id="uts_start" nextelement="uts_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="uts_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                <!--            <a href="javascript:void(0);" data-head="UTS" data-load="prodi" class="btn btn-sm btn-warning btn-block more_details">Special Case</a>-->
                -
            </td>
        </tr>
        <tr>
            <td>Input Nilai UTS</td>
            <td>
                <input type="text" id="nilaiuts_start" nextelement="nilaiuts_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="nilaiuts_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                <a href="javascript:void(0);" data-head="Input Nilai UTS" data-type="5.1" class="btn btn-sm btn-warning btn-block more_details">Special Case</a>
            </td>
        </tr>
        <tr>
            <td>Show Nilai UTS</td>
            <td>
                <input type="text" id="show_nilai_uts" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>

            </td>
            <td></td>
        </tr>
        <tr>
            <td>UAS</td>
            <td>
                <input type="text" id="uas_start" nextelement="uas_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="uas_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                -
            </td>
        </tr>
        <tr>
            <td>Input Nilai UAS</td>
            <td>
                <input type="text" id="nilaiuas_start" nextelement="nilaiuas_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="nilaiuas_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                <a href="javascript:void(0);" data-head="Input Nilai UAS" data-type="8.1" class="btn btn-sm btn-warning btn-block more_details">Special Case</a>
            </td>
        </tr>


        <tr>
            <td>Maks. Date Input Tugas</td>
            <td>
                <input type="text" id="nilaitugas_end" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>Show Nilai UAS</td>
            <td>
                <input type="text" id="show_nilai_uas" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td></td>
            <td></td>
        </tr>


        <tr>
            <td>Show Nilai H</td>
            <td>
                <input type="text" id="show_nilai_h" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>Show Nilai T</td>
            <td>
                <input type="text" id="show_nilai_t" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>Update Transcript</td>
            <td>
                <input type="text" id="updateTranscript" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>Maximum Input & Approval Modify Attendance</td>
            <td>
                <input type="text" id="ModifyAttendance" nextelement="" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>Edom</td>
            <td>
                <input type="text" id="edom_start" nextelement="edom_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="edom_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                -
            </td>
        </tr>

        <tr>
            <td>Penilaian Sarana Prasarana</td>
            <td>
                <input type="text" id="edom2_start" nextelement="edom2_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="edom2_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                -
            </td>
        </tr>

        <tr>
            <td>Seminar TA Registration</td>
            <td>
                <input type="text" id="taReg_start" nextelement="taReg_end" name="regular" class="form-control form-tahun-akademik">
            </td>
            <td>
                <input type="text" id="taReg_end" name="regular" class="form-control form-tahun-akademik form-next">
            </td>
            <td>
                -
            </td>
        </tr>
    </tbody>
</table>

<div class="row">
    <!-- Student -->
    <div class="col-md-6">
        <hr />
        <div class="well" style="min-height: 100px;">
            <h3 style="margin-top: 0px;border-left: 7px solid #FF9800;padding-left: 7px;">Student Attendance</h3>
            <table>
                <tr>
                    <td style="width: 25%;">Open</td>
                    <td>:</td>
                    <td>
                        <select class="form-control" id="form_In_Session_Std">
                            <option value="1">Session Start</option>
                            <option value="2">Session End</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="form_In_Type_Std">
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" id="form_In_Time_Std">
                            <span class="input-group-addon">minute</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Close</td>
                    <td>:</td>
                    <td>
                        <select class="form-control" id="form_Out_Session_Std">
                            <option value="2">Session End</option>
                            <option value="1">Session Start</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="form_Out_Type_Std">
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" id="form_Out_Time_Std">
                            <span class="input-group-addon">minute</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Default Attendance</td>
                    <td>:</td>
                    <td>
                        <label class="radio-inline" style="color: red;">
                            <input type="radio" name="form_DefaultAttendance" id="DefAbs" value="0"> Absent
                        </label>
                        <label class="radio-inline" style="color: green;">
                            <input type="radio" name="form_DefaultAttendance" id="DefPres" value="1"> Present
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>Total Sessions</td>
                    <td></td>
                    <td>
                        <select class="form-control" id="form_TotalSession">
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <hr />
        <div class="well">
            <h3 style="margin-top: 0px;border-left: 7px solid #FF9800;padding-left: 7px;">Online Class
                <br />
                <small>Attendance requirements</small>
            </h3>
            <label class="checkbox-inline">
                <input type="checkbox" id="OnlineForum" value="1"> Forum
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="OnlineTask" value="1"> Task
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="OnlineQuiz" value="1"> Quiz
            </label>
        </div>
    </div>

    <!-- Lecturer -->
    <div class="col-md-6">
        <hr />
        <div class="well" style="min-height: 100px;">
            <h3 style="margin-top: 0px;border-left: 7px solid #FF9800;padding-left: 7px;">Lecturer Attendance</h3>
            <table>
                <tr>
                    <td style="width: 25%;">Open</td>
                    <td>:</td>
                    <td>
                        <select class="form-control" id="form_In_Session_Lec">
                            <option value="1">Session Start</option>
                            <option value="2">Session End</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="form_In_Type_Lec">
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" id="form_In_Time_Lec">
                            <span class="input-group-addon">minute</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Close</td>
                    <td>:</td>
                    <td>
                        <select class="form-control" id="form_Out_Session_Lec">
                            <option value="2">Session End</option>
                            <option value="1">Session Start</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="form_Out_Type_Lec">
                            <option value="before">Before</option>
                            <option value="after">After</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" id="form_Out_Time_Lec">
                            <span class="input-group-addon">minute</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr />
                        <div style="background: lightyellow;border: 1px solid #CCCCCC;padding: 10px;">
                            Button "Attendance Out" akan muncul pada status yg di checklist
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        User Need Attendance Out

                        <input id="totalStatus" class="hide">
                    </td>
                    <td>:</td>
                    <td colspan="2">
                        <div id="showStatus1"></div>
                    </td>
                    <td>
                        <div id="showStatus2"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<hr />
<div style="text-align: right;">
    <a href="<?php echo base_url('academic/academic-year'); ?>" id="btnBack" class="btn btn-info"><i class="fa fa-arrow-circle-left right-margin" aria-hidden="true"></i> Back</a>
    <button class="btn btn-success" id="btnSaveDetail">Save</button>
</div>



<script>
    $(document).ready(function() {
        window.ID = '<?php echo $ID; ?>';
        loadData(ID);
        $('.form-tahun-akademik').prop('readonly', true);
        $("#bpp_start,#krs_start ,#bayar_start,#kuliah_start,#edom_start,#edom2_start," +
                "#uts_start,#show_nilai_uts,#nilaiuts_start," +
                "#uas_start,#nilaiuas_start,#nilaitugas_end,#show_nilai_uas,#show_nilai_h,#show_nilai_t," +
                "#updateTranscript,#ModifyAttendance,#taReg_start")
            .datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                // minDate: new Date(moment().year(),moment().month(),moment().date()),
                onSelect: function() {
                    var data_date = $(this).val().split(' ');
                    var nextelement = $(this).attr('nextelement');
                    nextDatePick(data_date, nextelement);
                }
            });
    });

    $('#btnSaveDetail').click(function() {


        if (confirm('Are you sure?')) {
            loading_modal_show();
            var form_In_Time_Std = $('#form_In_Time_Std').val();
            var form_Out_Time_Std = $('#form_Out_Time_Std').val();

            var form_In_Time_Lec = $('#form_In_Time_Lec').val();
            var form_Out_Time_Lec = $('#form_Out_Time_Lec').val();

            var totalStatus = parseInt($('#totalStatus').val());
            var Out_User_Lec = [];
            if (totalStatus > 0) {
                for (var a = 0; a < totalStatus; a++) {
                    if ($('#formUserLec' + a).is(':checked')) {
                        Out_User_Lec.push($('#formUserLec' + a).val());
                    }
                }
            }

            var data = {
                action: 'editDetailAcademicYear',
                SemesterID: ID,
                dataForm: {
                    SemesterID: ID,
                    bayarBPPStart: ($('#bpp_start').datepicker("getDate") != null) ? moment($('#bpp_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    bayarBPPEnd: ($('#bpp_end').datepicker("getDate") != null) ? moment($('#bpp_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    krsStart: ($('#krs_start').datepicker("getDate") != null) ? moment($('#krs_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    krsEnd: ($('#krs_end').datepicker("getDate") != null) ? moment($('#krs_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    bayarStart: ($('#bayar_start').datepicker("getDate") != null) ? moment($('#bayar_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    bayarEnd: ($('#bayar_end').datepicker("getDate") != null) ? moment($('#bayar_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    kuliahStart: ($('#kuliah_start').datepicker("getDate") != null) ? moment($('#kuliah_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    kuliahEnd: ($('#kuliah_end').datepicker("getDate") != null) ? moment($('#kuliah_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    utsStart: ($('#uts_start').datepicker("getDate") != null) ? moment($('#uts_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    utsEnd: ($('#uts_end').datepicker("getDate") != null) ? moment($('#uts_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    utsInputNilaiStart: ($('#nilaiuts_start').datepicker("getDate") != null) ? moment($('#nilaiuts_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    utsInputNilaiEnd: ($('#nilaiuts_end').datepicker("getDate") != null) ? moment($('#nilaiuts_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    showNilaiUts: ($('#show_nilai_uts').datepicker("getDate") != null) ? moment($('#show_nilai_uts').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    uasStart: ($('#uas_start').datepicker("getDate") != null) ? moment($('#uas_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    uasEnd: ($('#uas_end').datepicker("getDate") != null) ? moment($('#uas_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    uasInputNilaiStart: ($('#nilaiuas_start').datepicker("getDate") != null) ? moment($('#nilaiuas_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    uasInputNilaiEnd: ($('#nilaiuas_end').datepicker("getDate") != null) ? moment($('#nilaiuas_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    assgInputEnd: ($('#nilaitugas_end').datepicker("getDate") != null) ? moment($('#nilaitugas_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    showNilaiUas: ($('#show_nilai_uas').datepicker("getDate") != null) ? moment($('#show_nilai_uas').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    showNilai_H: ($('#show_nilai_h').datepicker("getDate") != null) ? moment($('#show_nilai_h').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    showNilai_T: ($('#show_nilai_t').datepicker("getDate") != null) ? moment($('#show_nilai_t').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    updateTranscript: ($('#updateTranscript').datepicker("getDate") != null) ? moment($('#updateTranscript').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    edomStart: ($('#edom_start').datepicker("getDate") != null) ? moment($('#edom_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    edomEnd: ($('#edom_end').datepicker("getDate") != null) ? moment($('#edom_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    edom2Start: ($('#edom2_start').datepicker("getDate") != null) ? moment($('#edom2_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    edom2End: ($('#edom2_end').datepicker("getDate") != null) ? moment($('#edom2_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    TARegStart: ($('#taReg_start').datepicker("getDate") != null) ? moment($('#taReg_start').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    TARegEnd: ($('#taReg_end').datepicker("getDate") != null) ? moment($('#taReg_end').datepicker("getDate")).format('YYYY-MM-DD') : '',
                    totalSession: $('#form_TotalSession').val(),
                },
                dataFormAttd: {
                    In_Session_Std: $('#form_In_Session_Std').val(),
                    In_Type_Std: $('#form_In_Type_Std').val(),
                    In_Time_Std: (form_In_Time_Std != '' && form_In_Time_Std != null && form_In_Time_Std != 0) ? form_In_Time_Std : 0,
                    Out_Session_Std: $('#form_Out_Session_Std').val(),
                    Out_Type_Std: $('#form_Out_Type_Std').val(),
                    Out_Time_Std: (form_Out_Time_Std != '' && form_Out_Time_Std != null && form_Out_Time_Std != 0) ? form_Out_Time_Std : 0,
                    In_Session_Lec: $('#form_In_Session_Lec').val(),
                    In_Type_Lec: $('#form_In_Type_Lec').val(),
                    In_Time_Lec: (form_In_Time_Lec != '' && form_In_Time_Lec != null && form_In_Time_Lec != 0) ? form_In_Time_Lec : 0,
                    Out_Session_Lec: $('#form_Out_Session_Lec').val(),
                    Out_Type_Lec: $('#form_Out_Type_Lec').val(),
                    Out_Time_Lec: (form_Out_Time_Lec != '' && form_Out_Time_Lec != null && form_Out_Time_Lec != 0) ? form_Out_Time_Lec : 0,
                    Out_User_Lec: JSON.stringify(Out_User_Lec),
                    DefaultAttendance: $("input[name=form_DefaultAttendance]:checked").val(),
                    ModifyAttendance: ($('#ModifyAttendance').datepicker("getDate") != null) ?
                        moment($('#ModifyAttendance').datepicker("getDate")).format('YYYY-MM-DD') : moment($('#kuliah_end').datepicker("getDate")).format('YYYY-MM-DD'),
                    UpdateBy: sessionNIP,
                    UpdateAt: dateTimeNow()
                },
                dataOnlineClass: {
                    Forum: ($('#OnlineForum').is(':checked')) ? '1' : '0',
                    Task: ($('#OnlineTask').is(':checked')) ? '1' : '0',
                    Quiz: ($('#OnlineQuiz').is(':checked')) ? '1' : '0',
                    UpdatedBy: sessionNIP,
                    UpdatedAt: dateTimeNow()
                }
            };

            loading_button('#btnSaveDetail');
            $('.form-tahun-akademik,#btnBack').prop('disabled', true);

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__crudDataDetailTahunAkademik';
            $.post(url, {
                token: token
            }, function(data) {
                toastr.success('Data saved', 'Success!');
                setTimeout(function() {
                    loading_modal_hide();
                    $('#btnSaveDetail').prop('disabled', false).html('Save');
                    $('.form-tahun-akademik,#btnBack').prop('disabled', false);
                }, 2000);
            });
        }



    });

    function loadData(ID) {
        var url = base_url_js + 'api/__crudDataDetailTahunAkademik';
        var data = {
            action: 'readDetailAcademicYear',
            ID: ID
        };
        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(data) {

            $('#nameTahunAkademik').html(data.TahunAkademik.Name);

            (data.DetailTA.bayarBPPStart !== '0000-00-00' && data.DetailTA.bayarBPPStart !== null) ? $('#bpp_start').datepicker('setDate', new Date(data.DetailTA.bayarBPPStart)): '';
            (data.DetailTA.bayarBPPEnd !== '0000-00-00' && data.DetailTA.bayarBPPEnd !== null) ? $('#bpp_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.bayarBPPStart)
            }).datepicker('setDate', new Date(data.DetailTA.bayarBPPEnd)): '';

            (data.DetailTA.krsStart !== '0000-00-00' && data.DetailTA.krsStart !== null) ? $('#krs_start').datepicker('setDate', new Date(data.DetailTA.krsStart)): '';
            (data.DetailTA.krsEnd !== '0000-00-00' && data.DetailTA.krsEnd !== null) ? $('#krs_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.krsStart)
            }).datepicker('setDate', new Date(data.DetailTA.krsEnd)): '';

            (data.DetailTA.bayarStart !== '0000-00-00' && data.DetailTA.bayarStart !== null) ? $('#bayar_start').datepicker('setDate', new Date(data.DetailTA.bayarStart)): '';
            (data.DetailTA.bayarEnd !== '0000-00-00' && data.DetailTA.bayarEnd !== null) ? $('#bayar_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.bayarStart)
            }).datepicker('setDate', new Date(data.DetailTA.bayarEnd)): '';

            (data.DetailTA.kuliahStart !== '0000-00-00' && data.DetailTA.kuliahStart !== null) ? $('#kuliah_start').datepicker('setDate', new Date(data.DetailTA.kuliahStart)): '';
            (data.DetailTA.kuliahEnd !== '0000-00-00' && data.DetailTA.kuliahEnd !== null) ? $('#kuliah_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.kuliahStart)
            }).datepicker('setDate', new Date(data.DetailTA.kuliahEnd)): '';

            (data.DetailTA.utsStart !== '0000-00-00' && data.DetailTA.utsStart !== null) ? $('#uts_start').datepicker('setDate', new Date(data.DetailTA.utsStart)): '';
            (data.DetailTA.utsEnd !== '0000-00-00' && data.DetailTA.utsEnd !== null) ? $('#uts_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.utsStart)
            }).datepicker('setDate', new Date(data.DetailTA.utsEnd)): '';

            (data.DetailTA.utsInputNilaiStart !== '0000-00-00' && data.DetailTA.utsInputNilaiStart !== null) ? $('#nilaiuts_start').datepicker('setDate', new Date(data.DetailTA.utsInputNilaiStart)): '';
            (data.DetailTA.utsInputNilaiEnd !== '0000-00-00' && data.DetailTA.utsInputNilaiEnd !== null) ? $('#nilaiuts_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.utsInputNilaiStart)
            }).datepicker('setDate', new Date(data.DetailTA.utsInputNilaiEnd)): '';

            (data.DetailTA.showNilaiUts !== '0000-00-00' && data.DetailTA.showNilaiUts !== null) ? $('#show_nilai_uts').datepicker('setDate', new Date(data.DetailTA.showNilaiUts)): '';

            (data.DetailTA.uasStart !== '0000-00-00' && data.DetailTA.uasStart !== null) ? $('#uas_start').datepicker('setDate', new Date(data.DetailTA.uasStart)): '';
            (data.DetailTA.uasEnd !== '0000-00-00' && data.DetailTA.uasEnd !== null) ? $('#uas_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.uasStart)
            }).datepicker('setDate', new Date(data.DetailTA.uasEnd)): '';

            (data.DetailTA.uasInputNilaiStart !== '0000-00-00' && data.DetailTA.uasInputNilaiStart !== null) ? $('#nilaiuas_start').datepicker('setDate', new Date(data.DetailTA.uasInputNilaiStart)): '';
            (data.DetailTA.uasInputNilaiEnd !== '0000-00-00' && data.DetailTA.uasInputNilaiEnd !== null) ? $('#nilaiuas_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.uasInputNilaiStart)
            }).datepicker('setDate', new Date(data.DetailTA.uasInputNilaiEnd)): '';

            (data.DetailTA.assgInputEnd !== '0000-00-00' && data.DetailTA.assgInputEnd !== null) ? $('#nilaitugas_end').datepicker('setDate', new Date(data.DetailTA.assgInputEnd)): '';

            (data.DetailTA.showNilaiUas !== '0000-00-00' && data.DetailTA.showNilaiUas !== null) ? $('#show_nilai_uas').datepicker('setDate', new Date(data.DetailTA.showNilaiUas)): '';

            (data.DetailTA.showNilai_H !== '0000-00-00' && data.DetailTA.showNilai_H !== null) ? $('#show_nilai_h').datepicker('setDate', new Date(data.DetailTA.showNilai_H)): '';
            (data.DetailTA.showNilai_T !== '0000-00-00' && data.DetailTA.showNilai_T !== null) ? $('#show_nilai_t').datepicker('setDate', new Date(data.DetailTA.showNilai_T)): '';
            (data.DetailTA.updateTranscript !== '0000-00-00' && data.DetailTA.updateTranscript !== null) ? $('#updateTranscript').datepicker('setDate', new Date(data.DetailTA.updateTranscript)): '';

            (data.DetailTA.edomStart !== '0000-00-00' && data.DetailTA.edomStart !== null) ? $('#edom_start').datepicker('setDate', new Date(data.DetailTA.edomStart)): '';
            (data.DetailTA.edomEnd !== '0000-00-00' && data.DetailTA.edomEnd !== null) ? $('#edom_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.edomStart)
            }).datepicker('setDate', new Date(data.DetailTA.edomEnd)): '';

            (data.DetailTA.edom2Start !== '0000-00-00' && data.DetailTA.edom2Start !== null) ? $('#edom2_start').datepicker('setDate', new Date(data.DetailTA.edom2Start)): '';
            (data.DetailTA.edom2End !== '0000-00-00' && data.DetailTA.edom2End !== null) ? $('#edom2_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.edom2Start)
            }).datepicker('setDate', new Date(data.DetailTA.edom2End)): '';

            (data.DetailTA.TARegStart !== '0000-00-00' && data.DetailTA.TARegStart !== null) ? $('#taReg_start').datepicker('setDate', new Date(data.DetailTA.TARegStart)): '';
            (data.DetailTA.TARegEnd !== '0000-00-00' && data.DetailTA.TARegEnd !== null) ? $('#taReg_end').datepicker({
                showOtherMonths: true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                minDate: new Date(data.DetailTA.TARegEnd)
            }).datepicker('setDate', new Date(data.DetailTA.TARegEnd)): '';


            $('#form_In_Time_Lec').val(0);
            $('#form_Out_Time_Lec').val(0);
            $('#form_In_Time_Std').val(0);
            $('#form_Out_Time_Std').val(0);


            var Out_User_Lec = [];
            // Setting Attendace
            if (data.AttdSetting.length > 0) {
                var d = data.AttdSetting[0];

                (data.DetailTA.updateTranscript !== '0000-00-00' && d.ModifyAttendance !== null) ?
                $('#ModifyAttendance').datepicker('setDate', new Date(d.ModifyAttendance)): '';



                $('#form_In_Session_Std').val(d.In_Session_Std);
                $('#form_In_Type_Std').val(d.In_Type_Std);
                $('#form_In_Time_Std').val(d.In_Time_Std);
                $('#form_Out_Session_Std').val(d.Out_Session_Std);
                $('#form_Out_Type_Std').val(d.Out_Type_Std);
                $('#form_Out_Time_Std').val(d.Out_Time_Std);

                $('#form_In_Session_Lec').val(d.In_Session_Lec);
                $('#form_In_Type_Lec').val(d.In_Type_Lec);
                $('#form_In_Time_Lec').val(d.In_Time_Lec);
                $('#form_Out_Session_Lec').val(d.Out_Session_Lec);
                $('#form_Out_Type_Lec').val(d.Out_Type_Lec);
                $('#form_Out_Time_Lec').val(d.Out_Time_Lec);

                $('#form_TotalSession').val(d.totalSession);

                if (d.DefaultAttendance == 1 || d.DefaultAttendance == '1') {
                    $('#DefPres').prop('checked', true);
                } else {
                    $('#DefAbs').prop('checked', true);
                }

                Out_User_Lec = JSON.parse(d.Out_User_Lec);

            }


            $('#totalStatus').val(data.StatusEmployees.length);
            if (data.StatusEmployees.length > 0) {
                for (var a = 0; a < data.StatusEmployees.length; a++) {
                    var elm = $('#showStatus1');
                    if (a > 2) {
                        elm = $('#showStatus2');
                    }

                    var d_a = data.StatusEmployees[a];
                    var sc = ($.inArray(d_a.IDStatus, Out_User_Lec) != -1) ? 'checked' : '';
                    elm.append('<div class="checkbox">' +
                        '  <label>' +
                        '    <input type="checkbox" id="formUserLec' + a + '" value="' + d_a.IDStatus + '" ' + sc + ' >' + d_a.Description + ' ' +
                        '  </label>' +
                        '</div>')
                }
            }


            if (data.OnlineClass.length > 0) {
                $('#OnlineForum').prop('checked', (parseInt(data.OnlineClass[0].Forum) == 1) ? true : false);
                $('#OnlineTask').prop('checked', (parseInt(data.OnlineClass[0].Task) == 1) ? true : false);
                $('#OnlineQuiz').prop('checked', (parseInt(data.OnlineClass[0].Quiz) == 1) ? true : false);
            }



        });
    }

    function nextDatePick(value, nextElement) {

        // $('#'+nextElement).prop('disabled',false);

        var data_date = value;
        var CustomMoment = moment(data_date[2] + '-' + (parseInt(convertDateMMtomm(data_date[1])) + 1) + '-' + data_date[0]).add(1, 'days');
        var CustomMomentYear = CustomMoment.year();
        var CustomMomentMonth = CustomMoment.month();
        var CustomMomentDate = CustomMoment.date();

        $("#" + nextElement).val('');
        $("#" + nextElement).datepicker("destroy");
        $("#" + nextElement).datepicker({
            showOtherMonths: true,
            autoSize: true,
            dateFormat: 'dd MM yy',
            minDate: new Date(CustomMomentYear, CustomMomentMonth, CustomMomentDate)
        });
    }
</script>



<script>
    $('#idBtnSPKRS').click(function() {

        loadDataSPKRS();
        var dataBody = '<div class="well" style="padding: 10px;">' +
            '    <div class="row">' +
            '        <div class="col-md-3">' +
            '            <div class="form-group">' +
            '                <label>Class Of</label>' +
            '                <select class="form-control" id="formSPClassOf"></select>' +
            '            </div>' +
            '        </div>' +
            '        <div class="col-md-6">' +
            '            <div class="form-group">' +
            '                <label>Programme Study</label>' +
            '                <select class="form-control" id="formSPProdiID"></select>' +
            '            </div>' +
            '        </div>' +
            '        <div class="col-md-3">' +
            '            <div class="form-group">' +
            '                <label>Prodi Group</label>' +
            '                <select class="form-control" id="formSPGroupProdiID"><option value="" selected>All Group</option></select>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '    <div class="row">' +
            '        <div class="col-md-6">' +
            '            <div class="form-group">' +
            '                <label>Start</label>' +
            '                <input id="formStart_SPKRS" class="form-control form-tahun-akademik" readonly>' +
            '            </div>' +
            '        </div>' +
            '        <div class="col-md-6">' +
            '            <div class="form-group">' +
            '                <label>End</label>' +
            '                <input id="formEnd_SPKRS" class="form-control form-tahun-akademik" readonly>' +
            '               <button id="btnSave_SPKRS" class="btn btn-primary" style="float: right;margin-top:15px;">Save</button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-md-12">' +
            '        <table class="table table-bordered table-striped" id="tableSPKRS">' +
            '            <thead>' +
            '            <tr style="background: #437e88;color: #ffffff;">' +
            '                <th style="width: 8%;">No</th>' +
            '                <th style="width: 17%;">Class Of</th>' +
            '                <th>Prodi</th>' +
            '                <th style="width: 29%;">Date</th>' +
            '                <th style="width: 8%;"><i class="fa fa-cog"></i></th>' +
            '            </tr>' +
            '            </thead>' +
            '            <tbody id="dataTrSPKRS"></tbody>' +
            '        </table>' +
            '    </div>' +
            '</div>';
        var dataBody2 = '<div class="well">' +
            '    <div class="row">' +
            '        <div class="col-xs-12">' +
            '            <div class="form-group">' +
            '                <label>Prodi</label>' +
            '                <select class="form-control" id="formProdi_SPKRS"></select>' +
            '            </div>' +
            '        </div>' +
            '        <div class="col-xs-6">' +
            '            <label>Start</label>' +
            '            <input id="formStart_SPKRS" class="form-control form-tahun-akademik" readonly>' +
            '        </div>' +
            '        <div class="col-xs-6">' +
            '            <label>End</label>' +
            '            <input id="formEnd_SPKRS" class="form-control form-tahun-akademik" readonly>' +
            '           <button id="btnSave_SPKRS" class="btn btn-primary" style="float: right;margin-top:15px;">Save</button>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-xs-12">' +
            '        <table class="table table-bordered" id="tableSPKRS">' +
            '            <thead>' +
            '            <tr style="background: #437e88;color: #ffffff;">' +
            '                <th style="width: 15%;">Prodi</th>' +
            '                <th>Start</th>' +
            '                <th>End</th>' +
            '                <th style="width: 15%;">Act</th>' +
            '            </tr>' +
            '            </thead>' +
            '           <tbody id="dataTrSPKRS"></tbody>' +
            '        </table>' +
            '    </div>' +
            '</div>';

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Special Case - KRS</h4>');
        $('#GlobalModal .modal-body').html(dataBody);

        loadSelectOptionClassOf_ASC('#formSPClassOf', '');

        $('#formSPProdiID').append('<option value="" selected>--- All Programme Study ---</option>' +
            '<option disabled>------------------------------------------</option>');
        loadSelectOptionBaseProdi('#formSPProdiID', '');


        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');


        $('#formStart_SPKRS').datepicker({
            showOtherMonths: true,
            autoSize: true,
            dateFormat: 'dd MM yy',
            // minDate: new Date(moment().year(),moment().month(),moment().date()),
            onSelect: function() {
                var data_date = $(this).val().split(' ');
                nextDatePick(data_date, 'formEnd_SPKRS');
            }
        });

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });
    });

    function loadDataSPKRS() {

        var url = base_url_js + 'api/__crudSpecialCaseKRS';
        var data = {
            action: 'readSP_KRS',
            SemesterID: ID
        };

        var token = jwt_encode(data, 'UAP)(*');
        $('#dataTrSPKRS').empty();
        $.post(url, {
            token: token
        }, function(jsonResult) {

            $('#dataTrSPKRS').empty();
            if (jsonResult.length > 0) {
                var no = 1;
                for (var i = 0; i < jsonResult.length; i++) {
                    var d = jsonResult[i];


                    var prodi = (d.ProdiID != '' && d.ProdiID != null) ?
                        d.ProdiEng : '<b style="color:red;">All Progamme Study</b>';

                    var groupP = (d.ProdiGroupID != '' && d.ProdiGroupID != null) ?
                        ' - <b>' + d.ProdiGroup + '</b>' : '';


                    $('#dataTrSPKRS').append('<tr>' +
                        '<td>' + (no++) + '</td>' +
                        '<td>' + d.ClassOf + '' + groupP + '</td>' +
                        '<td style="text-align: left;">' + prodi + '</td>' +
                        '<td style="text-align: left;">' +
                        '<div>Start : <b>' + moment(d.StartDate).format('DD MMM YYYY') + '</b></div>' +
                        '<div>End : <b>' + moment(d.EndDate).format('DD MMM YYYY') + '</b></div>' +
                        '</td>' +
                        '<td><button class="btn btn-sm btn-danger btn-delete-aysc-krs" data-id="' + d.ID + '"><i class="fa fa-trash"></i></button></td>' +
                        '</tr>');
                }
            } else {
                $('#dataTrSPKRS').append('<tr><td colspan="4">-- Data Not Yet --</td></tr>');
            }

            return false;

            if (jsonResult.length > 0) {
                for (var i = 0; i < jsonResult.length; i++) {
                    var d = jsonResult[i];
                    $('#dataTrSPKRS').append('<tr id="rwTr' + d.ID + '">' +
                        '<td>' + d.Code + '</td>' +
                        '<td>' + moment(d.Start).format('DD MMM YYYY') + '</td>' +
                        '<td>' + moment(d.End).format('DD MMM YYYY') + '</td>' +
                        '<td><button data-id="' + d.ID + '" class="btn btn-danger btn-delete-aysc-krs"><i class="fa fa-trash" aria-hidden="true"></i></button></td>' +
                        '</tr>');
                    $('#formProdi_SPKRS option[value="' + d.ProdiID + '.' + d.Code + '"]')
                        .prop('disabled', true).css('background', '#00800040');
                }
            }
        });

    }
</script>