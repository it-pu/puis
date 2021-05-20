
<style>
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        color: #333333;
    }
</style>

<div class="col-md-12" id="modalAdd">
    <form class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-4 control-label">Jenis Kurikulum</label>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <select class="form-control curriculum" id="ModalJenisKurikulum">
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Base Prodi</label>
            <div class="col-sm-8">
                <select class="form-control curriculum" id="ModalSelectProdi">
                    <option value=""></option>
                </select>
            </div>
        </div>
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

        <?php if ($action=='addRPS'): ?>
            <div class="form-group">
                <label class="col-sm-4 control-label">Minggu Ke- 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-3">
                            <select class="form-control" id="modalRpsMinggu">
                                <option value="" selected="selected" disabled></option>
                                <?php for ($i=1; $i <17 ; $i++) { 
                                    echo "<option value='$i'>$i</option>";
                                } ?>
                            </select>
                        </div>
                        <span class="help-block spanSelectRpsMinggu" style="display: none;"></span>
                    </div>
                    
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Sub CPMK 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="modalRpsSubCPMK" rows="2"></textarea>
                    <span class="help-block spanInputSubCPMK" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Bahan Kajian 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="modalRpsBahanKajian" rows="2"></textarea>
                    <span class="help-block spanInputBahanKajian" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Penilaian Indikator 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="modalRpsPenilaianIndikator" rows="2"></textarea>
                    <span class="help-block spanInputPenilaianIndikator" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Penilaian Kriteria, Bentuk 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="modalRpsPenilaianKriteria" rows="2"></textarea>
                    <span class="help-block spanInputPenilaianKriteria" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Bentuk dan Metode Pembelajaran, Waktu, Penugasan 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="modalRpsMetodePembelajaran" rows="2"></textarea>
                    <span class="help-block spanInputMetodePembelajaran" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Nilai (%) 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <input id="modalRpsNilai" class="form-control" type="number" min="0" max="100">
                    <span class="help-block spanInputNilai" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Deskripsi Nilai 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                
                </label>
                <div class="col-sm-8">
                    <!-- <input id="modalRpsNilai" class="form-control" type="number" min="1" max="100">     -->
                    <textarea class="form-control" id="modalRpsDescNilai" rows="2"></textarea>
                    <span class="help-block spanInputDescNilai" style="display: none;"></span>    

                </div>
            </div>

            
             <div class="form-group">
                <label class="col-sm-4 control-label">Upload File <span>
                   <strong style='color: #fc4b6c;'>*</strong></span>
                   <br> (pdf - Maksimal 8MB)
                </label>
                <div class="col-sm-8">
                    <input class="actUpload" type="file" name="userfile" value="" accept="application/pdf">
                    <span class="help-block spanInputUpload" style="display: none;"></span>    

                </div>
            </div>
        <?php elseif ($action=='addCPMK'): ?>
            <div class="form-group">
                <label class="col-sm-4 control-label">Type
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" id="ModalSelectTypeCPMK">
                        <option value="" selected="selected" disabled></option>
                        <option value="sub">sub</option>
                        <option value="non-sub">non-sub</option>
                    </select>
                    <span class="help-block spanSelectTypeCPMK" style="display: none;"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">CPMK Code 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <input id="modalCPMKCode" type="text" class="form-control">
                    <span class="help-block spanInputCPMKCode" style="display: none;"></span>    
                    <!-- <textarea class="form-control" id="modalCPMKDescNilai" rows="2"></textarea> -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Deskripsi 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <!-- <input id="modalRpsNilai" class="form-control" type="number" min="1" max="100">     -->
                    <textarea class="form-control" id="modalCPMKDesc" rows="2"></textarea>
                    <span class="help-block spanInputCPMKDesc" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Order 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <input id="modalCPMKOrder" class="form-control" type="number" min="0">    
                    <span class="help-block spanInputCPMKOrder" style="display: none;"></span>    

                    <!-- <textarea class="form-control" id="modalCPMKDesc" rows="2"></textarea> -->
                </div>
            </div>
            
        <?php elseif ($action=='addDescMK'): ?>
            <div class="form-group">
                <label class="col-sm-4 control-label">Deskripsi 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <!-- <input id="modalRpsNilai" class="form-control" type="number" min="1" max="100">     -->
                    <textarea class="form-control" id="modalDescMK" rows="2"></textarea>
                    <span class="help-block spanInputDescMK" style="display: none;"></span>    
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Order 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <input id="modalDescMKOrder" class="form-control" type="number" type="number" min="0">    
                    <span class="help-block spanInputDescMKOrder" style="display: none;"></span>    
                    <!-- <textarea class="form-control" id="modalCPMKDesc" rows="2"></textarea> -->
                </div>
            </div>

        <?php else: ?>
            <div class="form-group">
                <label class="col-sm-4 control-label">Deskripsi 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <!-- <input id="modalRpsNilai" class="form-control" type="number" min="1" max="100">     -->
                    <textarea class="form-control" id="modalMaterialDesc" rows="2"></textarea>
                    <span class="help-block spanInputMaterialDesc" style="display: none;"></span>    

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Order 
                <span><strong style='color: #fc4b6c;'>*</strong></span>
                </label>
                <div class="col-sm-8">
                    <input id="modalMaterialOrder" class="form-control" type="number" min="0">    
                    <span class="help-block spanInputMaterialOrder" style="display: none;"></span>    

                </div>
            </div>

        <?php endif ?>

        <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">
            <div class="col-sm-12" id="BtnFooter">
                <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>
                <button type="button" id="ModalbtnAddForm" class="btn btn-default btn-default-success hide">Add Data</button>
            </div>
        </div>

    </form>
</div>


<script>

    const tambujin = async(url,token,arrfiles=[]) => {
        const res = await AjaxSubmitRestTicketing(url,token,arrfiles);
    }

    $(document).ready(function () {


        loadSelectOptionConf('#ModalJenisKurikulum','curriculum_types');
        loadSelectOptionConf('#ModalKelompokMK','courses_groups');

        window.action = '<?php echo $action; ?>';
        window.ID = 0;
        window.StatusPrecondition = 0;

            $('#modalAdd .curriculum,' +
                '#modalAdd .select2-select-00,' +
                '#modalAdd #ModalPrasyarat,' +
                '#modalAdd input[type=radio],' +
                '#modalAdd .btn[data-toggle=collapse]')
                .prop('disabled',true);

            // $('#ModalbtnSaveForm').addClass('hide');
            $('#ModalbtnAddForm').removeClass('hide');

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

            });
       



    });

    
    

    $('#ModalbtnAddForm').click(function () {
        if(action=='addRPS'){
            if( $("#modalRpsMinggu").val() == null || $(".actUpload").val() == "" ||  $("#modalRpsSubCPMK").val() == "" || $("#modalRpsBahanKajian").val() == "" || $("#modalRpsPenilaianIndikator").val() == "" || $("#modalRpsPenilaianKriteria").val() == "" || $("#modalRpsMetodePembelajaran").val() == "" || $("#modalRpsNilai").val() == "" || $("#modalRpsNilai").val() > 100 || $("#modalRpsDescNilai").val() == "")
            {
                if($("#modalRpsMinggu").val() == null )
                {
                    $(".spanSelectRpsMinggu").css("display", "");
                    $(".spanSelectRpsMinggu").html("<strong style='color: #fc4b6c;'>Minggu ke- tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanSelectRpsMinggu").css("display", "none");
                        $(".spanSelectRpsMinggu").html("");
                    },3000);
                } 
                else if($("#modalRpsSubCPMK").val() == "")
                {
                    $(".spanInputSubCPMK").css("display", "");
                    $(".spanInputSubCPMK").html("<strong style='color: #fc4b6c;'>Sub CPMK tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputSubCPMK").css("display", "none");
                        $(".spanInputSubCPMK").html("");
                    },3000);
                }
                else if($("#modalRpsBahanKajian").val() == "")
                {
                    $(".spanInputBahanKajian").css("display", "");
                    $(".spanInputBahanKajian").html("<strong style='color: #fc4b6c;'>Bahan kajian tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputBahanKajian").css("display", "none");
                        $(".spanInputBahanKajian").html("");
                    },3000);
                }
                else if($("#modalRpsPenilaianIndikator").val() == "")
                {
                    $(".spanInputPenilaianIndikator").css("display", "");
                    $(".spanInputPenilaianIndikator").html("<strong style='color: #fc4b6c;'>Penilaian indikator tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputPenilaianIndikator").css("display", "none");
                        $(".spanInputPenilaianIndikator").html("");
                    },3000);
                }
                else if($("#modalRpsPenilaianKriteria").val() == "")
                {
                    $(".spanInputPenilaianKriteria").css("display", "");
                    $(".spanInputPenilaianKriteria").html("<strong style='color: #fc4b6c;'>Penilaian kriteria tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputPenilaianKriteria").css("display", "none");
                        $(".spanInputPenilaianKriteria").html("");
                    },3000);
                }
                else if($("#modalRpsMetodePembelajaran").val() == "")
                {
                    $(".spanInputMetodePembelajaran").css("display", "");
                    $(".spanInputMetodePembelajaran").html("<strong style='color: #fc4b6c;'>Metode pembelajaran tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputMetodePembelajaran").css("display", "none");
                        $(".spanInputMetodePembelajaran").html("");
                    },3000);
                }
                else if($("#modalRpsNilai").val() == "" || $("#modalRpsNilai").val() > 100 )
            {
                if ($("#modalRpsNilai").val() == "" ) {
                    $(".spanInputRpsNilai").css("display", "");
                    $(".spanInputRpsNilai").html("<strong style='color: #fc4b6c;'>Nilai tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputRpsNilai").css("display", "none");
                        $(".spanInputRpsNilai").html("");
                    },3000);
                } else if ($("#modalRpsNilai").val() > 100){
                    $(".spanInputRpsNilai").css("display", "");
                    $(".spanInputRpsNilai").html("<strong style='color: #fc4b6c;'>Nilai tidak boleh lebih dari 100 !</strong>");
                    setTimeout(function () {
                        $(".spanInputRpsNilai").css("display", "none");
                        $(".spanInputRpsNilai").html("");
                    },3000);
                }
                
            }
                else if($("#modalRpsDescNilai").val() == "")
                {
                    $(".spanInputDescNilai").css("display", "");
                    $(".spanInputDescNilai").html("<strong style='color: #fc4b6c;'>Deskripsi nilai tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputDescNilai").css("display", "none");
                        $(".spanInputDescNilai").html("");
                    },3000);
                }
                else if($(".actUpload").val() == "" )
                {
                    $(".spanInputUpload").css("display", "");
                    $(".spanInputUpload").html("<strong style='color: #fc4b6c;'>file harus diisi !</strong>");
                    setTimeout(function () {
                        $(".spanInputUpload").css("display", "none");
                        $(".spanInputUpload").html("");
                    },3000);
                } 
                  
            }
            else{
                var CDID = '<?php echo $CDID; ?>';
                var url = base_url_js+'rps/crud-RPS';
                var input = $('.actUpload');
                var files = input[0].files[0];

                var sz = parseFloat(files.size) / 1000000; // ukuran MB
                var ext = files.type.split('/');

     
                if (Math.floor(sz) <= 8) {

                    var filesname = 'RPS_' + CDID + '_' + $("#modalRpsMinggu").val();
        
                    var dataaddRPS = {
                        CDID : CDID,
                        RPSSubCPMK : $("#modalRpsSubCPMK").val(),
                        RPSMinggu : $("#modalRpsMinggu").val(),
                        RPSBahanKajian : $("#modalRpsBahanKajian").val(),
                        RPSPenilaianIndikator : $("#modalRpsPenilaianIndikator").val(),
                        RPSPenilaianKriteria : $("#modalRpsPenilaianKriteria").val(),
                        RPSPenilaianMetodePembelajaran : $("#modalRpsMetodePembelajaran").val(),
                        RPSNilai : $("#modalRpsNilai").val(),
                        RPSDescNilai : $("#modalRpsDescNilai").val(),
                        filesname: filesname
                    };

                    var dataToken = {
                        action : 'AddRPS',
                        dataAdd : dataaddRPS,
                    }

                    var token = jwt_encode(dataToken,'UAP)(*');

                    var ArrUploadFilesSelector = [];
                    var UploadFile = $('.actUpload');
                    var valUploadFile = UploadFile.val();
                    if (valUploadFile) {
                        var NameField = UploadFile.attr('name');
                        var temp = {
                            NameField : NameField,
                            Selector : UploadFile,
                        };
                        ArrUploadFilesSelector.push(temp);
                    }

                    tambujin(url,token,ArrUploadFilesSelector);
                    toastr.success('Data capaian pembelajaran mata kuliah tersimpan','Success');
                    $('#GlobalModal').modal('hide');
                    setTimeout(function () {
                        window.location = '';
                    },1000);
                        
                } else {
                    toastr.warning('Maximum file size 8 mb', 'Warning');
                    alert('Maximum file size 8 mb');
                }
            }
        }
        else if(action=='addCPMK'){

            if( $("#ModalSelectTypeCPMK").val() == null || $("#modalCPMKCode").val() == "" || $("#modalCPMKDesc").val() == "" || $("#modalCPMKOrder").val() == "" )
            {
                if($("#ModalSelectTypeCPMK").val() == null )
                {
                    $(".spanSelectTypeCPMK").css("display", "");
                    $(".spanSelectTypeCPMK").html("<strong style='color: #fc4b6c;'>Type must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanSelectTypeCPMK").css("display", "none");
                        $(".spanSelectTypeCPMK").html("");
                    },3000);
                } 
                else if($("#modalCPMKCode").val() == "")
                {
                    $(".spanInputCPMKCode").css("display", "");
                    $(".spanInputCPMKCode").html("<strong style='color: #fc4b6c;'>Code must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputCPMKCode").css("display", "none");
                        $(".spanInputCPMKCode").html("");
                    },3000);
                }
                else if($("#modalCPMKDesc").val() == "")
                {
                    $(".spanInputCPMKDesc").css("display", "");
                    $(".spanInputCPMKDesc").html("<strong style='color: #fc4b6c;'>Description must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputCPMKDesc").css("display", "none");
                        $(".spanInputCPMKDesc").html("");
                    },3000);
                }
                else 
                {
                    $(".spanInputCPMKOrder").css("display", "");
                    $(".spanInputCPMKOrder").html("<strong style='color: #fc4b6c;'>Order must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputCPMKOrder").css("display", "none");
                        $(".spanInputCPMKOrder").html("");
                    },3000);
                }
                  
            }else{
                var CDID = '<?php echo $CDID; ?>';
                var url = base_url_js+'rps/crud-CPMK';

                var dataaddDescMK = {
                    CDID : CDID,
                    subCPMK : $("#ModalSelectTypeCPMK").val(),
                    codeCPMK : $("#modalCPMKCode").val(),
                    descCPMK : $("#modalCPMKDesc").val(),
                    orderCPMK : $("#modalCPMKOrder").val(),
                };

                var dataToken = {
                    action : 'AddCPMK',
                    dataAdd : dataaddDescMK,
                }

                var token = jwt_encode(dataToken,'UAP)(*');
                $.post(url,{token:token},function (jsonResult) {

                    if(parseInt(JSON.parse(jsonResult).Status)>0){
                        toastr.success('Data capaian pembelajaran mata kuliah tersimpan','Success');
                        $('#GlobalModal').modal('hide');
                        setTimeout(function () {
                            window.location = '';
                        },1000);
                    } else {
                        $(".spanInputCPMKOrder").css("display", "");
                        $(".spanInputCPMKOrder").html("<strong style='color: #fc4b6c;'>Order sudah ada !</strong>");
                        setTimeout(function () {
                            $(".spanInputCPMKOrder").css("display", "none");
                            $(".spanInputCPMKOrder").html("");
                        },3000);
                    }
                    
                });
            }
           
        }
        else if(action=='addDescMK'){

            if ($("#modalDescMK").val() == "" || $("#modalDescMKOrder").val() == "") {
                if($("#modalDescMK").val() == "" )
                {
                    $(".spanInputDescMK").css("display", "");
                    $(".spanInputDescMK").html("<strong style='color: #fc4b6c;'>Description must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputDescMK").css("display", "none");
                        $(".spanInputDescMK").html("");
                    },3000);
                    
                } 
                else if($("#modalDescMKOrder").val() == "")
                {
                    $(".spanInputDescMKOrder").css("display", "");
                    $(".spanInputDescMKOrder").html("<strong style='color: #fc4b6c;'>Order must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputDescMKOrder").css("display", "none");
                        $(".spanInputDescMKOrder").html("");
                    },3000);
                }
            } else {
                var CDID = '<?php echo $CDID; ?>';
                var url = base_url_js+'rps/crud-desc-MK';

                var dataaddDescMK = {
                    CDID : CDID,
                    descMK : $("#modalDescMK").val(),
                    orderMK : $("#modalDescMKOrder").val(),
                };

                var dataToken = {
                    action : 'AddDescMK',
                    dataAdd : dataaddDescMK,
                }

                var token = jwt_encode(dataToken,'UAP)(*');
                $.post(url,{token:token},function (jsonResult) {

                    if(parseInt(JSON.parse(jsonResult).Status)>0){
                        toastr.success('Data deskripsi mata kuliah tersimpan','Success');
                        $('#GlobalModal').modal('hide');
                        setTimeout(function () {
                            window.location = '';
                        },1000);
                    } else {
                        $(".spanInputDescMKOrder").css("display", "");
                        $(".spanInputDescMKOrder").html("<strong style='color: #fc4b6c;'>Order sudah ada !</strong>");
                        setTimeout(function () {
                            $(".spanInputDescMKOrder").css("display", "none");
                            $(".spanInputDescMKOrder").html("");
                        },3000);
                    }
                    
                });
            }
        }
        else {
            if ($("#modalMaterialDesc").val() == "" || $("#modalMaterialOrder").val() == "") {
                if($("#modalMaterialDesc").val() == "" )
                {
                    $(".spanInputMaterialDesc").css("display", "");
                    $(".spanInputMaterialDesc").html("<strong style='color: #fc4b6c;'>Description must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputMaterialDesc").css("display", "none");
                        $(".spanInputMaterialDesc").html("");
                    },3000);
                } 
                else if($("#modalMaterialOrder").val() == "")
                {
                    $(".spanInputMaterialOrder").css("display", "");
                    $(".spanInputMaterialOrder").html("<strong style='color: #fc4b6c;'>Order must not be empty !</strong>");
                    setTimeout(function () {
                        $(".spanInputMaterialOrder").css("display", "none");
                        $(".spanInputMaterialOrder").html("");
                    },3000);
                }
            } else {
                var CDID = '<?php echo $CDID; ?>';
                var url = base_url_js+'rps/crud-bahan-kajian';

                var dataaddMaterial = {
                    CDID : CDID,
                    descMaterial : $("#modalMaterialDesc").val(),
                    orderMaterial : $("#modalMaterialOrder").val(),
                };

                var dataToken = {
                    action : 'AddMaterial',
                    dataAdd : dataaddMaterial,
                }

                var token = jwt_encode(dataToken,'UAP)(*');
                $.post(url,{token:token},function (jsonResult) {
                    if(parseInt(JSON.parse(jsonResult).Status)>0){
                        toastr.success('Data bahan kajian tersimpan','Success');
                        $('#GlobalModal').modal('hide');
                        setTimeout(function () {
                            window.location = '';
                        },1000);
                    } else {
                        $(".spanInputMaterialOrder").css("display", "");
                        $(".spanInputMaterialOrder").html("<strong style='color: #fc4b6c;'>Order sudah ada !</strong>");
                        setTimeout(function () {
                            $(".spanInputMaterialOrder").css("display", "none");
                            $(".spanInputMaterialOrder").html("");
                        },3000);
                    }
                    
                    
                });
            }
        }
    });

    function AjaxSubmitRestTicketing(url='',token='',ArrUploadFilesSelector=[]){
         var def = jQuery.Deferred();
         var form_data = new FormData();
         form_data.append('token',token);
         if (ArrUploadFilesSelector.length>0) {
            var Selector = ArrUploadFilesSelector[0].Selector;
            var UploadFile = Selector[0].files;
            var NameField = ArrUploadFilesSelector[0].NameField;
            form_data.append(NameField, UploadFile[0]);
         }


         $.ajax({
           type:"POST",
           url:url,
           data: form_data,
           contentType: false,       // The content type used when sending data to the server.
           cache: false,             // To unable request pages to be cached
           processData:false,
           dataType: "json",
           success:function(data)
           {
            def.resolve(data);
           },  
           error: function (data) {
             // toastr.info('No Result Data'); 
             def.reject();
           }
         })
         return def.promise();
    }

</script>