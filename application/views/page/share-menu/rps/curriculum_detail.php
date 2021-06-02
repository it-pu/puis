

<style>
    .tab-pane {
        margin-bottom: 20px;
    }
    .table-smt thead th {
        text-align: center;
    }
    .btn-silabus,.btn-sap {
        border-radius: 20px;
        padding: 1px;
        padding-left: 5px;
        padding-right: 5px;
    }
</style>

<div class="col-md-12">
    <div class="tabbable tabbable-custom tabbable-full-width">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_mata_kuliah" data-toggle="tab">Mata Kuliah</a></li>
        </ul>
        <div class="tab-content row">
            <!--=== Overview ===-->
            <div class="tab-pane active" id="tab_mata_kuliah">

                <div id="DataMataKuliah"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        var token = "<?php echo $token; ?>";
        //var url = base_url_js+"api/__getKurikulumByYear";
        var url = base_url_js+"rps/getKurikulumByYearforRPS";

        window.allSmt = [];


        $.post(url,{token:token},function (data_json) {
            allSmt = [];
           var res = JSON.parse(data_json);

            if(res!=''){
                if(res.MataKuliah.length>0){
                    LoadDetailMK(res.MataKuliah);
                } else {
                    $('#DataMataKuliah').html('--- Mata Kuliah Belum Ditambahkan ---');
                   // loading_modal_hide();
                }
               // loadSemesterAdd();
            } else {
               // loading_modal_hide();
            }


        });
    });

    $(document).on('click','.btn-add-mksmt',function () {
       var CurriculumID = $(this).attr('data-id');
       var Semester = $(this).attr('data-smt');
    });

    function LoadDetailMK(MataKuliah) {

         // console.log(MataKuliah);
        for(var i=0;i<MataKuliah.length;i++){
            // if(MataKuliah.length==8){
            //     $('.btn-addsmt').prop('disabled',true);
            // } else {
            //     $('.btn-addsmt').prop('disabled',false);
            // }

            allSmt.push(parseInt(MataKuliah[i].Semester));

            $('#DataMataKuliah').append('<div class="col-md-12"> <div class="widget box">' +
                '                    <div class="widget-header" id="widgetSmt'+i+'">' +
                '                        <h4><i class="icon-reorder"></i> SEMESTER '+MataKuliah[i].Semester+'</h4>' +
                // '<div class="toolbar no-padding">' +
                // '    <div class="btn-group">' +
                // '                        <span data-smt="'+MataKuliah[i].Semester+'" class="btn btn-xs btn-add-mksmt">' +
                // '    <i class="icon-plus"></i> Add Mata Kuliah' +
                // '     </span>' +
                // '    </div>' +
                // '</div>' +
                '                    </div>' +
                '                    <div class="widget-content no-padding ">' +
                '                        <table id="tableSemester'+i+'" class="table table-bordered table-striped table-smt">' +
                '                            <thead>' +
                '                            <tr>' +
                '                                <th style="width:10px;">No</th>' +
                '                                <th style="width:110px;">Code</th>' +
                '                                <th>Course</th>' +
                // '                                <th style="width: 15%;">Lecturer</th>' +
                '                                <th style="width: 12%;">Base Prodi</th>' +
                // '                                <th style="width: 5%;">Ed Level</th>' +
                '                                <th style="width: 8%;">Action</th>' +
                '                                <th style="width: 14%;">Status</th>' +
                // '                                <th style="width: 9%;">Syllabus</th>' +
                // '                                <th style="width: 9%;">SAP</th>' +
                '                            </tr>' +
                // '                            <tr>' +
                // '                                <th style="width:80px;">T</th>' +
                // '                                <th style="width:80px;">P</th>' +
                // '                                <th style="width:80px;">PKL</th>' +
                // '                            </tr>' +
                '                            </thead>' +
                '                            <tbody id="dataSmt'+i+'"></tbody>' +
                '                        </table>' +
                '                    </div>' +
                '                </div></div>');

            var detailSemester = MataKuliah[i].DetailSemester;
            var no=1;
            for(var s=0;s<detailSemester.length;s++){


                var StatusPrecondition = (detailSemester[s].StatusPrecondition==1) ? '<i class="fa fa-check-circle" style="color: green;"></i>' : '<i class="fa fa-minus-circle" style="color: red;"></i>';

                var silabus = (detailSemester[s].StatusSilabus==1) ? '<button class="btn btn-sm btn-default btn-default-danger btn-silabus hide"><i class="fa fa-download" aria-hidden="true"></i></button> ' +
                    '<label class="btn btn-sm btn-default btn-default-success btn-silabus"><i class="fa fa-upload" aria-hidden="true"></i> Upload <input type="file" style="display: none;"></label>' : '<i class="fa fa-minus-circle" style="color: red;"></i>';

                var sap = (detailSemester[s].StatusSAP==1) ? '<button class="btn btn-sm btn-default btn-default-success btn-silabus hide"><i class="fa fa-download" aria-hidden="true"></i></button> ' +
                    '<label class="btn btn-sm btn-default btn-default-success btn-silabus"><i class="fa fa-upload" aria-hidden="true"></i> Upload <input type="file" style="display: none;"></label>' : '<i class="fa fa-minus-circle" style="color: red;"></i>';

                var StatusMK = (detailSemester[s].StatusMK==1) ? 'green' : 'red';

                var urlPDF = base_url_js+'uploads/syllabus_sap/'+detailSemester[s].Syllabus;
                var syllabus = (detailSemester[s].Syllabus!=null && detailSemester[s].Syllabus!=0 && UrlExists(urlPDF)) ?
                    '<a href="'+urlPDF+'" class="btn btn-sm btn-default btn-default-success btn-silabus" download="Syllabus.pdf"><i class="fa fa-download" aria-hidden="true"></i> Download<a/>' :
                    '<label class="btn btn-sm btn-default btn-default-danger btn-silabus"><i class="fa fa-upload" aria-hidden="true"></i> Upload <input type="file" style="display: none;"></label>';

                   

                $('#dataSmt'+i).append('<tr>' +
               
                    '<td class="td-center">'+(no++)+'</td>' +
                    '<td class="td-center"><span style="color:'+StatusMK+';">'+detailSemester[s].MKCode+'</span></td>' +
                    '<td><div><b>'+detailSemester[s].NameMKEng+'</b>' +
                    '</td>' +
                    // '<td><div>'+detailSemester[s].NameLecturer+'</td>' +
                    '<td>'+detailSemester[s].ProdiNameEng+'</td>' +
                    // '<td class="td-center"><div>'+detailSemester[s].EducationLevel+'</div></td>' +
                    '<td class="td-center"><div class="btn-group">' +
                    '          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                    '            <i class="fa fa-pencil"></i> <span class="caret"></span>' +
                    '          </button>' +
                    '          <ul class="dropdown-menu">' +
                    // '            <li class="'.$btnPublish.'" id="li_btn_Publish_'.$row['ID'].'">' +
                    // '                    <a href="javascript:void(0);" class="btnPublishSurvey" data-id="'.$row['ID'].'">Publish</a>' +
                    // '                    </li>
                    // '            <li class="'.$btnClose.'" id="li_btn_Close_'.$row['ID'].'">' +
                    // '                    <a href="javascript:void(0);" class="btnCloseSurvey" data-id="'.$row['ID'].'" style="color: red;">Close</a>' +
                    // '             </li>' +
                    // '             '.$showBtnAddNewDate.'' +
                    // '            <li role="separator" class="divider"></li>' +
                    // '            <li role="separator" class="divider"></li>' +
                   
                    // '            <li role="separator" class="divider"></li>' +
                    
                    '            <li><a href="javascript:void(0);" class="btnManageCPL" data-smt="'+MataKuliah[i].Semester+'" data-mkcode="'+detailSemester[s].MKCode +'" data-id="'+detailSemester[s].CDID+'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'">1. CPL (Capaian Pembelajaran Lulusan)</a></li>' +
                    '            <li><a href="javascript:void(0);" class="btnAddCPMK" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'">2. CPMK (Capaian Pembelajaran Mata Kuliah)</a></li>' +
                    '            <li><a href="javascript:void(0);" class="btnAddDescMK" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'">3. Deskripsi MK</a></li>' +
                    '            <li><a href="javascript:void(0);" class="btnAddMaterial" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'">4. Bahan Kajian    </a></li>' +
                    '            <li><a href="javascript:void(0);" class="btnAddRPS" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'">5. RPS (Rencana Pembelajaran Semester)</a></li>' +
                    // '            <li role="separator" class="divider"></li>' +
                    // '            <li><a href="javascript:void(0);" class="btnShareToPublic">Share to the public</a></li>' +
                    // '            <li class="'.$btnRemove.'"><a href="javascript:void(0);">Remove</a></li>' +
                    '          </ul>' +
                    '        </div></td>' +
                    '       <td class="td-center"><div>'+
                    '       <span class="label label-danger" id="spanCPL">CPL : '+detailSemester[s].CPL+'</span>'+
                    '           <a href="javascript:void(0);" class="btnViewCPMK" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'"><span class="label label-danger" id="spanCPMK">CPMK : '+detailSemester[s].CPMK+'</span></a>'+
                    '           <a href="javascript:void(0);" class="btnViewDescMK" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'"><span class="label label-danger" id="spanDESCMK">Deskripsi MK : '+detailSemester[s].DESCMK+'</span></a>'+
                    '           <a href="javascript:void(0);" class="btnViewMaterial" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'"><span class="label label-danger" id="spanmaterial">Bahan Kajian : '+detailSemester[s].material+'</span></a>'+
                    
                    '           <a href="javascript:void(0);" class="btnViewRPS" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'" data-mkcode="'+detailSemester[s].MKCode +'" data-prodi="'+detailSemester[s].ProdiNameEng+'" data-course="'+detailSemester[s].NameMKEng+'"><span class="label label-danger" id="spanRPS">RPS : '+detailSemester[s].RPS+'</span></a>'+
                    '       </div></td>' +
                    // '<td class="td-center"><div>'+silabus+'</div></td>' +
                    // '<td class="td-center"><div>'+syllabus+'</div></td>' +
                    // '<td class="td-center"><div>'+sap+'</div></td>' +
                    // '<td class="td-center"><div>'+detailSemester[s].SKSPraktikLapangan+'</div></td>' +
                    '</tr>');

                    if (detailSemester[s].CPL!=0) {
                        $("#spanCPL").removeClass("label-danger").addClass("label-success");
                    } 
                    if (detailSemester[s].CPMK!=0){
                        $("#spanCPMK").removeClass("label-danger").addClass("label-success");
                    }
                    if (detailSemester[s].DESCMK!=0){
                        $("#spanDESCMK").removeClass("label-danger").addClass("label-success");
                    }
                    if (detailSemester[s].material!=0){
                        $("#spanmaterial").removeClass("label-danger").addClass("label-success");
                    }
                    if (detailSemester[s].RPS!=0){
                        $("#spanRPS").removeClass("label-danger").addClass("label-success");
                    }
            }
   

            LoaddataTable(i);
        }

        loading_modal_hide();


    }


    function loadSemesterAdd() {

        $('#addSmt').empty();
        for(var i2=1;i2<=8;i2++){
            if($.inArray(i2,allSmt)==-1){
                $('#addSmt').append('<li><a href="javascript:void(0)" data-smt="'+i2+'" data-action="add-semester" class="btn-control">Semester '+i2+'</a></li>');
            }

        }
        $('.btn-addsmt').prop('disabled',false);
    }

    function LoaddataTable(element) {
        var table = $('#tableSemester'+element).DataTable({
            'iDisplayLength' : 5,
            'ordering' : false,
        });
    }

</script>