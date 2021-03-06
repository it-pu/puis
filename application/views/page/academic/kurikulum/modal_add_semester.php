
<style>
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        color: #333333;
    }
</style>

<div class="col-md-12" id="modalAddSmester">
    <form class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-4 control-label">Jenis Kurikulum</label>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-10">
                        <select class="form-control" id="ModalJenisKurikulum">
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-default btn-default-success" type="button" data-toggle="collapse" data-target="#addJenisKurikulum" aria-expanded="false" aria-controls="addJenisKurikulum">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="collapse" id="addJenisKurikulum" style="margin-top: 10px;">
                            <div class="well">
                                <div class="row">
                                    <div class="col-xs-9">
                                        <input class="form-control" id="FormAddItemJenisKurikulum" placeholder="Input jenis kurikulum...">
                                    </div>
                                    <label class="col-xs-3">
                                        <a href="javascript:void(0)" id="btnAddItemJenisKurikulum" class="btn btn-default btn-block btn-default-success"><i class="fa fa-floppy-o" aria-hidden="true"></i></a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Base Prodi</label>
            <div class="col-sm-8">
                <select class="form-control" id="ModalSelectProdi">
                    <option value=""></option>
                </select>
            </div>
        </div>
<!--        <div class="form-group">-->
<!--            <label class="col-sm-4 control-label">Jenjang</label>-->
<!--            <div class="col-sm-8">-->
<!--                <select class="form-control" id="ModalSelectJenjang">-->
<!--                    <option value=""></option>-->
<!--                </select>-->
<!--            </div>-->
<!--        </div>-->
        <div class="form-group">
            <label class="col-sm-4 control-label">Mata Kuliah</label>
            <div class="col-sm-8">
                <span id="ModalSelectMKView" style="line-height: 2.3;font-weight: bold;" class="hide"></span>
                <select class="select2-select-00 full-width-fix"
                        size="5" id="ModalSelectMK">
                    <option value=""></option>
                </select>
                <input type="hide" id="ModalSelectMKVal" class="hide">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Jenis Mata Kuliah</label>
            <div class="col-sm-8">
                <label class="radio-inline">
                    <input type="radio" name="jenisMK" value="1" checked> Wajib
                </label>
                <label class="radio-inline">
                    <input type="radio" name="jenisMK" value="0"> Pilihan
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Pra Syarat</label>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <input id="ModalPrasyaratVal" value="0" type="hide" class="hide" />
                        <label class="checkbox-inline">
                            <input type="checkbox" id="ModalPrasyarat" value="0" checked> Tidak Ada
                        </label>

                        <div style="margin-top: 10px;">
                            <span class="hide" id="ModalPrasyaratSelectMKView"></span>
                            <select class="select2-select-00 full-width-fix form-edit" size="5" multiple disabled id="ModalPrasyaratSelectMK">
                                <option value=""></option>
                            </select>
                            <input class="hide" readonly id="ModalPrasyaratSelectMKVal" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

<!--        <div class="form-group">-->
<!--            <label class="col-sm-4 control-label">Dosen Pengajar</label>-->
<!--            <div class="col-sm-8">-->
<!--                <span id="ModalLecturersView" style="line-height: 2.3;font-weight: bold;" class="hide"></span>-->
<!--                <select class="select2-select-00 full-width-fix" size="5" id="ModalLecturers">-->
<!--                    <option value=""></option>-->
<!--                </select>-->
<!--                <input class="hide" id="ModalLecturersVal" type="hide"/>-->
<!--            </div>-->
<!--        </div>-->
        <!-- Untuk membuka form dosen silahkan search "--- Dosen" lalu buka setiap komentarnya -->

        <div class="form-group">
            <label class="col-sm-4 control-label">Kelompok</label>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-10">
                        <select class="form-control form-edit" id="ModalKelompokMK">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-default btn-default-success" type="button" data-toggle="collapse" data-target="#addKelompokMK" aria-expanded="false" aria-controls="addKelompokMK">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="collapse" id="addKelompokMK" style="margin-top: 10px;">
                            <div class="well">
                                <div class="row">
                                    <div class="col-xs-9">
                                        <input class="form-control form-edit" id="FormAddItemMK" placeholder="Input kelompok...">
                                    </div>
                                    <label class="col-xs-3">
                                        <a href="javascript:void(0)" id="btnAddItemMK" class="btn btn-default btn-block btn-default-success"><i class="fa fa-floppy-o" aria-hidden="true"></i></a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group" style="border-top: 1px solid #d3d3d3;border-bottom: 1px solid #d3d3d3; padding: 15px 0px 15px 0px;background: lightyellow;">

            <div class="col-sm-3">
                <label>Total Credit</label>
                <input class="form-control form-edit" id="ModalFormTotalSKS" type="number">
            </div>
            <div class="col-sm-3">
                <label>Teori</label>
                <input class="form-control form-edit" id="ModalFormSKSTeori" type="number">
            </div>
            <div class="col-sm-3">
                <label>Praktek</label>
                <input class="form-control form-edit" id="ModalFormSKSPraktek" type="number">
            </div>
            <div class="col-sm-3">
                <label>Praktek Lap.</label>
                <input class="form-control form-edit" id="ModalFormSKSPraktekLapangan" type="number">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">Status MK</label>
            <div class="col-sm-8" style="border-top: 1px solid #d3d3d3;">
                <label class="radio-inline">
                    <input type="radio" name="statusMK" value="1" checked> Aktif
                </label>
                <label class="radio-inline">
                    <input type="radio" name="statusMK" value="0"> Tidak Aktif
                </label>
            </div>
        </div>

        <div class="form-group hide">
            <label class="col-sm-4 control-label">Silabus</label>
            <div class="col-sm-8" style="border-top: 1px solid #d3d3d3;">
                <label class="radio-inline">
                    <input type="radio" name="silabus" value="1"> Ada
                </label>
                <label class="radio-inline">
                    <input type="radio" name="silabus" value="0" checked> Tidak Ada
                </label>
            </div>
        </div>

        <div class="form-group hide">
            <label class="col-sm-4 control-label">SAP</label>
            <div class="col-sm-8" style="border-top: 1px solid #d3d3d3;">
                <label class="radio-inline">
                    <input type="radio" name="sap" value="1"> Ada
                </label>
                <label class="radio-inline">
                    <input type="radio" name="sap" value="0" checked> Tidak Ada
                </label>
            </div>
        </div>


        <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">
            <div class="col-sm-12" id="BtnFooter">
                <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>
                <button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>
                <button type="button" id="ModalbtnEditForm" class="btn btn-default btn-default-success hide">Edit Data</button>
            </div>
        </div>

        <div id="alertCourse"></div>

    </form>
</div>


<script>
    $(document).ready(function () {


        loadSelectOptionConf('#ModalJenisKurikulum','curriculum_types');
        loadSelectOptionConf('#ModalKelompokMK','courses_groups');

        window.action = '<?php echo $action; ?>';
        window.ID = 0;
        window.StatusPrecondition = 0;
        if(action=='add')
        {
            loadSelectOptionAllMataKuliah('#ModalPrasyaratSelectMK');
            loadSelectOptionBaseProdi('#ModalSelectProdi','');
            // loadSelectOptionEducationLevel('#ModalSelectJenjang','');
            loadSelectOptionAllMataKuliahSingle('#ModalSelectMK','');

            $('#ModalPrasyaratSelectMK, #ModalSelectMK').select2({allowClear: true});


            //---- Dosen
            // loadSelectOptionLecturersSingle('#ModalLecturers','');
            // $('#ModalLecturers').select2({allowClear: true});

        }
        else if(action=='edit')
        {
            $('#modalAddSmester .form-control,' +
                '#modalAddSmester .select2-select-00,' +
                '#modalAddSmester #ModalPrasyarat,' +
                '#modalAddSmester input[type=radio],' +
                '#modalAddSmester .btn[data-toggle=collapse]')
                .prop('disabled',true);

            $('#ModalbtnSaveForm').addClass('hide');
            $('#ModalbtnEditForm').removeClass('hide');

            var CDID = '<?php echo $CDID; ?>';
            var data = {
                CDID : CDID
            };
            ID = CDID;

            var url = base_url_js+"api/__getdetailKurikulum";
            var token = jwt_encode(data,"UAP)(*");
            $.post(url,{token:token},function (data_json) {
                var data = data_json[0];

                // console.log(data);
                // $('#BtnFooter').append('<button type="button" class="btn btn-danger" disabled data-id="'+data.ID+'" id="ModalbtnDeleteForm" style="float:left;">Delete</button>');


                $('#ModalJenisKurikulum').val(data.CurriculumTypeID);

                loadSelectOptionBaseProdi('#ModalSelectProdi',data.ProdiID);
                // loadSelectOptionEducationLevel('#ModalSelectJenjang',data.EducationLevelID);
                // $('#ModalSelectJenjang').val(data.EducationLevelID);



                $('#ModalSelectMK').addClass('hide');
                $('#ModalSelectMKView').removeClass('hide').html(data.NameMKEng);
                $('#ModalSelectMKVal').val(data.MKID+'.'+data.MKCode);

                // --- Dosen
                // $('#ModalLecturers').addClass('hide');
                // $('#ModalLecturersView').removeClass('hide').html(data.NameLecturer);
                // $('#ModalLecturersVal').val(data.LecturerNIP);
                // $('#ModalLecturersVal').html(data.LecturerNIP+' | '+data.NameLecturer);


                $('#ModalPrasyaratSelectMK').addClass('hide');


                $('#ModalPrasyaratVal').val(data.StatusPrecondition);
                $('#ModalPrasyaratSelectMKView').removeClass('hide').html('<ul id="ulpre"></ul>');
                if(data.StatusPrecondition==1){
                    for(var p=0;p<data.DetailPrecondition.length;p++){
                        var preD = data.DetailPrecondition[p];
                        $('#ulpre').append('<li>'+preD.NameEng+'</li>');
                    }
                }
                $('#ModalPrasyaratSelectMKVal').val(data.DataPrecondition);


                $('input[name=jenisMK][value='+data.MKType+']').prop('checked',true);



                $('#ModalKelompokMK').val(data.CoursesGroupsID);


                StatusPrecondition = data.StatusPrecondition;
                if(StatusPrecondition==1){
                    $('#ModalPrasyarat').prop('checked',false);
                    // $('#ModalPrasyaratSelectMK').prop('disabled',false);
                } else {

                    // $('#ModalPrasyarat').prop('checked',true);
                }


                $('#ModalKelompokMKVal').html(data.NameCoursesGroups);

                $('#ModalFormTotalSKS').val(data.TotalSKS);
                $('#ModalFormSKSTeori').val(data.SKSTeori);
                $('#ModalFormSKSPraktek').val(data.SKSPraktikum);
                $('#ModalFormSKSPraktekLapangan').val(data.SKSPraktikLapangan);

                $('input[name=statusMK][value='+data.StatusMK+']').prop('checked',true);
                $('input[name=silabus][value='+data.StatusSilabus+']').prop('checked',true);
                $('input[name=sap][value='+data.StatusSAP+']').prop('checked',true);

            });
        }



    });

    $(document).on('change','#ModalPrasyarat',function () {
       if($(this).is(":checked")){
           $('#ModalPrasyaratVal').val(0);
           if(action=='add'){
               $("#ModalPrasyaratSelectMK").select2("val", null);
           }
           $('#ModalPrasyaratSelectMK').prop('disabled',true);
       } else {
           $('#ModalPrasyaratVal').val(1);
           $('#ModalPrasyaratSelectMK').prop('disabled',false);
       }
    });

    $(document).on('click','#ModalbtnDeleteForm',function () {
        var ID = $(this).attr('data-id');
        $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Hapus data ?? </b> ' +
            '<button type="button" data-id="'+ID+'" id="btnActionDeleteYes" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
            '<button type="button" class="btn btn-default" id="btnActionDeleteNo" data-dismiss="modal">No</button>' +
            '</div>');
        $('#NotificationModal').modal('show');
    });

    $(document).on('click','#btnActionDeleteYes',function () {
        loading_buttonSm('#btnActionDeleteYes');
        var ID = $(this).attr('data-id');
        var url = base_url_js+"api/__crudDetailMK";
        var data = {
          action : 'delete',
          ID : ID
        };

        var token = jwt_encode(data,'UAP)(*');

        $.post(url,{token:token},function (result) {
            pageKurikulum();
            setTimeout(function () {
                $('#NotificationModal,#GlobalModal').modal('hide');
            },500);

        });

    });

    $('#btnAddItemJenisKurikulum').click(function () {
        var name = $('#FormAddItemJenisKurikulum').val();
        if(name==''){
            $('#FormAddItemJenisKurikulum').css('border','1px solid red');
        } else {
            addConf(name,'curriculum_types','#FormAddItemJenisKurikulum',
                '#btnAddItemJenisKurikulum','#ModalJenisKurikulum');
        }
    });

    $('#btnAddItemMK').click(function () {
        var name = $('#FormAddItemMK').val();

        if(name==''){
            $('#FormAddItemMK').css('border','1px solid red');
        } else {
            addConf(name,'courses_groups','#FormAddItemMK',
                '#btnAddItemMK','#ModalKelompokMK');
        }
    });

    $('#ModalbtnSaveForm').click(function () {

        var process = [];

        if(action=='add'){
            var kurikulum = $('#selectKurikulum').find(':selected').val().split('.');
            var CurriculumID = kurikulum[0];
            var Semester = '<?php echo $semester; ?>';
            var CurriculumTypeID = $('#ModalJenisKurikulum').val();


            var ModalSelectProdi = $('#ModalSelectProdi').find(':selected').val();
            process.push(formRequiredError(ModalSelectProdi,'#ModalSelectProdi'));

            // var EducationLevelID = $('#ModalSelectJenjang').find(':selected').val();
            // process.push(formRequiredError(EducationLevelID,'#ModalSelectJenjang'));

            var Datamk = $('#ModalSelectMK').val();
            if(Datamk!=''){
                mk = Datamk.split('.');
                var MKID = mk[0].trim();
                var MKCode = mk[1].trim();
                $('#s2id_ModalSelectMK a').css('border','1px solid #ccc');
            } else {
                $('#s2id_ModalSelectMK a').css('border','1px solid red');
                process.push(0);
            }
        }



        var MKType = $('input[name=jenisMK]:checked').val();

        var StatusPrecondition = $('#ModalPrasyaratVal').val();
        var DataPraSyart = $('#ModalPrasyaratSelectMK').val();
        var DataPR__ = '';
        if(StatusPrecondition==1){
        //     StatusPrecondition = 0;
        // } else {
        //     StatusPrecondition = 1;
            if(DataPraSyart==null){
                $('#s2id_ModalPrasyaratSelectMK').css('border','1px solid red');
                process.push(0);
            } else if(DataPraSyart[0]=='') {
                $('#s2id_ModalPrasyaratSelectMK').css('border','1px solid red');
                process.push(0);
            } else {
                DataPR__ = JSON.stringify(DataPraSyart);
            }

        }

        // // --- Dosen
        // var LecturerNIP = $('#ModalLecturers').val();
        // process = formRequiredError(LecturerNIP,'#s2id_ModalLecturers');

        var ModalKelompokMK = $('#ModalKelompokMK').val();
        var CoursesGroupsID = (ModalKelompokMK!='' && ModalKelompokMK!=null) ? ModalKelompokMK : null;
        var TotalSKS = $('#ModalFormTotalSKS').val();
        process.push(formRequiredError(TotalSKS,'#ModalFormTotalSKS'));
        var SKSTeori = $('#ModalFormSKSTeori').val();
        var SKSPraktikum = $('#ModalFormSKSPraktek').val();
        var SKSPraktikLapangan = $('#ModalFormSKSPraktekLapangan').val();

        var StatusMK = $('input[name=statusMK]:checked').val();
        var StatusSilabus = $('input[name=silabus]:checked').val();
        var StatusSAP = $('input[name=sap]:checked').val();



        // Proses Dulu
        if($.inArray(0,process)==-1){
            $('#modalAddSmester .form-control,' +
                '#modalAddSmester .select2-select-00,' +
                '#modalAddSmester #ModalPrasyarat,' +
                '#modalAddSmester input[type=radio],' +
                '#modalAddSmester .btn')
                .prop('disabled',true);
            var data;

            if(action=='add'){
                var ProdiID = (ModalSelectProdi!='' && ModalSelectProdi!=null) ? ModalSelectProdi.split('.')[0] : '';
                data = {
                    action : action,
                    ID : ID,
                    DataPraSyart : DataPraSyart,
                    dataForm : {
                        CurriculumID : CurriculumID,
                        Semester : Semester,
                        CurriculumTypeID : CurriculumTypeID,
                        ProdiID : ProdiID,
                        // EducationLevelID : EducationLevelID,

                        MKID : MKID,
                        MKType : MKType,

                        StatusPrecondition : StatusPrecondition,
                        DataPrecondition : JSON.stringify(DataPraSyart),

                        // --- Dosen
                        // LecturerNIP : LecturerNIP,
                        CoursesGroupsID : CoursesGroupsID,
                        TotalSKS : TotalSKS,
                        SKSTeori : SKSTeori,
                        SKSPraktikum : SKSPraktikum,
                        SKSPraktikLapangan : SKSPraktikLapangan,

                        StatusMK : StatusMK,
                        StatusSilabus : StatusSilabus,
                        StatusSAP : StatusSAP,

                        UpdateBy : sessionNIP,
                        UpdateAt : dateTimeNow()
                    }
                };
            } else if(action=='edit'){
                data = {
                    action : action,
                    ID : ID,
                    DataPraSyart : DataPraSyart,
                    dataForm : {
                        // CurriculumID : CurriculumID,
                        // Semester : Semester,
                        // CurriculumTypeID : CurriculumTypeID,
                        // ProdiID : ProdiID,
                        // EducationLevelID : EducationLevelID,
                        //
                        // MKID : MKID,
                        // MKCode : MKCode,
                        MKType : MKType,

                        StatusPrecondition : StatusPrecondition,
                        DataPrecondition : JSON.stringify(DataPraSyart),

                        // --- Dosen
                        // LecturerNIP : LecturerNIP,
                        CoursesGroupsID : CoursesGroupsID,
                        TotalSKS : TotalSKS,
                        SKSTeori : SKSTeori,
                        SKSPraktikum : SKSPraktikum,
                        SKSPraktikLapangan : SKSPraktikLapangan,

                        StatusMK : StatusMK,
                        StatusSilabus : StatusSilabus,
                        StatusSAP : StatusSAP,

                        UpdateBy : sessionNIP,
                        UpdateAt : dateTimeNow()
                    }
                };
            }

            // return false;

            var token = jwt_encode(data,"UAP)(*");
            var url = base_url_js+"api/__crudDetailMK";
            loading_button('#ModalbtnSaveForm');
            // return false;
            $.post(url,{token:token},function (result) {

                // if(result)

                // console.log(result.smg);

                $('#alertCourse').html('');
                setTimeout(function () {

                    $('#ModalbtnSaveForm').html('Save');
                    $('#modalAddSmester .form-control,' +
                        '#modalAddSmester .select2-select-00,' +
                        '#modalAddSmester #ModalPrasyarat,' +
                        '#modalAddSmester input[type=radio],' +
                        '#modalAddSmester .btn')
                        .prop('disabled',false);

                    if(action=='add' && result.msg==0){
                        toastr.error('Data have been inserted','Error');

                        $('#alertCourse').html('<div class="alert alert-danger" role="alert">Courses have been inserted in Semester '+result.Semester+'</div>');
                    } else if(action=='add' && result.msg!=0){
                        pageKurikulum();

                        resetForm();
                        toastr.success('Data tersimpan','Success');

                    } else if(action=='edit'){
                        pageKurikulum();

                        toastr.success('Data tersimpan','Success');

                        if(StatusPrecondition!=1){
                            $('#ModalPrasyarat').prop('checked',true);

                            $("#ModalPrasyaratSelectMK").select2("val", null);
                            $('#ModalPrasyaratSelectMK').prop('disabled',true);
                        }
                    }




                },2000);
            });
        } else {
            toastr.error('Form Required Wajib Diisi','Error!!');
        }




    });

    $('#ModalbtnEditForm').click(function () {
        $(this).addClass('hide');
        $('#ModalbtnSaveForm').removeClass('hide');

        // $('#modalAddSmester .form-control,' +
        //     '#modalAddSmester .select2-select-00,' +
        //     '#modalAddSmester #ModalPrasyarat,' +
        //     '#modalAddSmester input[type=radio],' +
        //     '#modalAddSmester .btn[data-toggle=collapse]')
        //     .prop('disabled',false);

        $('#modalAddSmester .form-edit,#modalAddSmester #ModalPrasyarat,#modalAddSmester input[type=radio],#modalAddSmester .btn[data-toggle=collapse]')
            .prop('disabled',false);

        // var mkVal = $('#ModalSelectMKVal').val();
        // loadSelectOptionAllMataKuliahSingle('#ModalSelectMK',mkVal);
        // $('#ModalSelectMK').removeClass('hide');

        // --- Dosen
        // var lecVal = $('#ModalLecturersVal').val();
        // loadSelectOptionLecturersSingle('#ModalLecturers',lecVal);
        // $('#ModalLecturers').removeClass('hide');
        // $('#ModalLecturers').select2({allowClear: true});

        var prForm = $('#ModalPrasyaratSelectMKVal').val();
        if(prForm!='' && prForm!='null'){
            loadSelectOptionAllMataKuliahForPraSyarat('#ModalPrasyaratSelectMK',JSON.parse(prForm));
        } else {
            loadSelectOptionAllMataKuliah('#ModalPrasyaratSelectMK');
        }

        $('#ModalPrasyaratSelectMK').removeClass('hide');

        $('#ModalPrasyaratSelectMK').select2();
        $('#ModalSelectMK').select2({allowClear: true});


        if(StatusPrecondition!=1){
            $('#ModalPrasyaratSelectMK').prop('disabled',true);
        }
    });

    function resetForm() {

        $('#ModalSelectProdi').val('');
        // $('#ModalSelectJenjang').val('');
        $('#ModalSelectMK').val(null).trigger('change');
        $('input[name=jenisMK][value=1]').prop('checked',true);

        $('#ModalPrasyarat').prop('checked',true);

        $("#ModalPrasyaratSelectMK").select2("val", null);
        $('#ModalPrasyaratSelectMK').prop('disabled',true);

        // --- Dosen
        // $('#ModalLecturers').val(null).trigger('change');

        $('#ModalKelompokMK').val('');

        $('#ModalFormTotalSKS').val('');
        $('#ModalFormSKSTeori').val('');
        $('#ModalFormSKSPraktek').val('');
        $('#ModalFormSKSPraktekLapangan').val('');

        $('input[name=statusMK][value=1]').prop('checked',true);

        $('input[name=silabus][value=0]').prop('checked',true);
        $('input[name=sap][value=0]').prop('checked',true);

    }

    function addConf(Name,table,form,btn,selectOPtion) {

        $(''+form).css('border','1px solid #ccc');
        loading_buttonSm(btn);
        $(''+form).prop('disabled',true);
        var data = {
            action : 'add',
            ID : '',
            table : table,
            data_insert : {
                Name : Name,
                UpdateBy : sessionNIP,
                UpdateAt : dateTimeNow()
            }
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+"api/__crudKurikulum";
        $.post(url,{token : token},function (result) {
            setTimeout(function () {

                $(''+selectOPtion).empty();
                loadSelectOptionConf(''+selectOPtion,table);

                $(''+btn).html('<i class="fa fa-floppy-o" aria-hidden="true"></i>');
                toastr.success('Data tersimpan','Success');
                $(btn+', '+form).prop('disabled',false);
                $(''+form).val('');
            },3000);



        });
    }

    function formRequiredError(value,element) {
        if(value=='' || value==0 || value==null){
            $(''+element).css('border','1px solid red');
            setTimeout(function () {
                $(''+element).css('border','1px solid #ccc');
            },5000);
            return 0;
        } else {
            $(''+element).css('border','1px solid #ccc');
            return 1;
        }

    }

</script>