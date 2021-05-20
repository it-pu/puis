

<style>

#listQuestion {
    padding-inline-start: 15px;
}

.item-question:hover {
    cursor: pointer;
}

.item-question:hover div {
    background: lightyellow;
}

.item-question {
    position: relative;
}

.item-question div {
    border: 1px solid #ccc;
    padding: 10px 10px 0px 10px;
    border-radius: 5px;
    width: 90%;
    margin-bottom: 9px;

    max-height: 100px;
    overflow: auto;
}

.item-question button {
    position: absolute;
    top: 0px;
    right: 0px;
}
</style>

<div class="row">
<div class="col-md-10 col-md-offset-1">
    <!-- <h1
        style="text-align: center;margin-top: 0px;
        margin-bottom: 30px;"><b>List Capaian Pembelajaran Mata Kuliah (CPMK)</b></h1> -->
    <table class="table table-striped">
    <tbody>
        <tr>
            <td style="width: 15%;">Curriculum Code</td>
            <td style="width: 1%;">:</td>
            <td><?= $MKCode; ?></td>
        </tr>
        <tr>
            <td>Course</td>
            <td>:</td>
            <td><?= $Course; ?></td>
        </tr>
        <tr>
            <td>Base Prodi</td>
            <td>:</td>
            <td><?= $Prodi; ?></td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>:</td>
            <td><?= $Semester; ?></td>
        </tr>
        <tr>
            <td>Curriculum Year</td>
            <td>:</td>
            <td><?= $curriculumYear; ?></td>
        </tr>
        </tbody>
    </table>


</div>
</div>

<div id="generate-edom" style="margin-top: 20px;">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                    <i class="fa fa-file-text-o"></i> Rencana Pembelajaran Semester (RPS)
                    </h4>
                </div>
                
                

                    <div class="row">
                        <div class="col-sm-12">
                            <!-- <div class="panel panel-default"> -->      
                                <div class="panel-body">
                                    <!-- <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-md-12">
                                            <button class="btn btn-default pull-left" onclick="location.href='<?php echo base_url('rps/data-curriculum'); ?>'"> < Back</button>
                                        </div>
                                    </div> -->
                                    <div class="table-responsive">
                                        
                                        <div id="loadTableRPS"></div>
                                    </div>
                                </div>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    const tambujin = async(url,token,arrfiles=[]) => {
        const res = await AjaxSubmitRestTicketing(url,token,arrfiles);
    }
    $(document).ready(function(){
        loadDataRPS();
    });

    function loadDataRPS() 
    {
        $('#loadTableRPS').html('<table id="tableDataRPS" class="table table-bordered table-striped table-centre" style="width:100%">' +
            '               <thead>' +
            '                <tr style="background: #eceff1;">' +
            //'                    <th style="width: 1%;text-align: center;">No</th>'+
            // '                    <th style="width: 8%;text-align: center;">Curriculum Code</th>'+
            // '                    <th style="width: 16%;text-align: center;">Course</th>'+
            // '                    <th style="width: 11%;text-align: center;">Base Prodi</th>'+
            '                <th style="width: 1%;">Minggu Ke</th>' +
            '                <th>Sub CPMK</th>' +
            '                <th style="width: 10%;">Bahan Kajian</th>' +
            '                <th style="width: 10%;">Penilaian Indikator</th>' +
            '                <th style="width: 10%;">Penilaian Kriteria, Bentuk</th>' +
            '                <th style="width: 10%;">Bentuk dan Metode Pembelajaran, Waktu, Penugasan</th>' +
            '                <th style="width: 10%;">Nilai (%)</th>' +
            '                <th style="width: 10%;">File</th>' +

            '                    <th style="width: 10%;text-align: center;">Entered At</th>'+
            '                    <th style="width: 10%;text-align: center;">Entered By</th>'+
            '                    <th style="width: 5%;text-align: left;">Action</th>'+ 
            '                </tr>' +
            '                </thead>' +
            '           </table>');


        var data = {
            action : 'getDataRPS',
            CDID : '<?= $CDID; ?>',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = "<?php echo base_url('rps/crud-RPS'); ?>";

        var dataTable = $('#tableDataRPS').DataTable( {
            "processing": true,
            "serverSide": true,
            "iDisplayLength" : 10,
            "ordering" : false,
            "language": {
                "searchPlaceholder": "Search..."
            },
            "ajax":{
                url :url, // json datasource
                data : {token:token},
                ordering : false,
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    loading_modal_hide();
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                }
            }
        } );

    }


    $(document).on('click','.btnEditRPS',function () {
        var CDID = <?= $CDID; ?>;
        var RPSID = $(this).attr('data-id');
        var RPSWeek = $(this).attr('data-week');
        var RPSSubCPMK = $(this).attr('data-subcpmk');
        var RPSMaterial = $(this).attr('data-material');
        var RPSIndikator = $(this).attr('data-indikator');
        var RPSKriteria = $(this).attr('data-kriteria');
        var RPSDesc = $(this).attr('data-desc');
        var RPSNilai = $(this).attr('data-nilai');
        var RPSDescNilai = $(this).attr('data-descnilai');
        var RPSFile = $(this).attr('data-file');


        var html = '<div class="col-md-12" id="modalEdit">'+
            '<form class="form-horizontal">'+
            '<div class="form-group">'+
            '<label class="col-sm-4 control-label">Minggu Ke-</label>'+
            '                <input type="hidden" id="modalIDRPS" class="form-control" value="'+RPSID+'">'+

            '<div class="col-sm-8">'+
            '    <div class="row">'+
            '        <div class="col-sm-3">'+
            '        <select class="form-control" id="modalRpsMinggu" disabled="disabled">'+
            '                    <option value="'+RPSWeek+'" disabled="disabled">'+RPSWeek+'</option>'+
            '<?php for ($i=1; $i <17 ; $i++) { 
                echo "<option value=$i>$i</option>";
            } ?>'+
            '        </select>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '</div>'+

            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Sub CPMK '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <textarea class="form-control" id="modalRpsSubCPMK" rows="2" value="'+RPSSubCPMK+'">'+RPSSubCPMK+'</textarea>'+
            '       <span class="help-block spanInputRpsSubCPMK" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Bahan Kajian '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <textarea class="form-control" id="modalRpsBahanKajian" rows="2" value"'+RPSMaterial+'">'+RPSMaterial+'</textarea>'+
            '       <span class="help-block spanInputRpsBahanKajian" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Penilaian Indikator '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <textarea class="form-control" id="modalRpsPenilaianIndikator" rows="2" value"'+RPSIndikator+'">'+RPSIndikator+'</textarea>'+
            '       <span class="help-block spanInputRpsPenilaianIndikator" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Penilaian Kriteria, Bentuk '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <textarea class="form-control" id="modalRpsPenilaianKriteria" rows="2" value"'+RPSKriteria+'">'+RPSKriteria+'</textarea>'+
            '       <span class="help-block spanInputRpsPenilaianKriteria" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Bentuk dan Metode Pembelajaran, Waktu, Penugasan '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <textarea class="form-control" id="modalRpsMetodePembelajaran" rows="2" value"'+RPSDesc+'">'+RPSDesc+'</textarea>'+
            '       <span class="help-block spanInputRpsMetodePembelajaran" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Nilai (%) '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <input id="modalRpsNilai" class="form-control" type="number" min="0" max="100" value="'+RPSNilai+'">'+
            '       <span class="help-block spanInputRpsNilai" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Deskripsi Nilai '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '    <div class="col-sm-8">'+
            '        <textarea class="form-control" id="modalRpsDescNilai" rows="2" value="'+RPSDescNilai+'">'+RPSDescNilai+'</textarea>'+
            '       <span class="help-block spanInputRpsDescNilai" style="display: none;"></span>'+ 

            '    </div>'+
            '</div>'+
            '<div class="form-group">'+
            '    <label class="col-sm-4 control-label">Upload File <span>'+
            '       <strong style="color: #fc4b6c;">*</strong></span>'+
            '       <br> (pdf - Maksimal 8MB)'+
            '    </label>'+
            '    <div class="col-sm-8">'+
            '        <input class="actUpload" type="file" name="userfile" value="'+RPSFile+'" accept="application/pdf">'+
            '        <span class="help-block"><a href ="'+base_url_js+'fileGetAny/document-RPS_'+CDID+'_'+RPSWeek+'-'+RPSFile+'" target="_blank" class="spanInputUploadOld" >'+RPSFile+'</a></span>'+    
            '        <span class="help-block spanInputUpload" style="display: none;"></span>'+    
            '    </div>'+
            '</div>'+
            '    <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">'+
            '        <div class="col-sm-12" id="BtnFooter">'+
            '            <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
            '            <button type="button" id="ModalbtnRPS" class="btn btn-default btn-default-success hide">Edit Data</button>'+
            '        </div>'+
            '    </div>'+
            '</form>';

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Edit Rencana Pembelajaran Semester (RPS)</h4>');

        $('#GlobalModal .modal-body').html(html);

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#ModalbtnRPS').removeClass('hide');
    });

    $(document).on('change','.actUpload',function () {
        
        if ($(".actUpload").val() == "") {
            $(".spanInputUploadOld").css("display", "");
        } else {
            $(".spanInputUploadOld").css("display", "none");
        }
    });

    $(document).on('click','#ModalbtnRPS',function () {
        if( $("#modalRpsSubCPMK").val() == "" || $(".actUpload").val() == "" ||  $("#modalRpsBahanKajian").val() == "" ||  $("#modalRpsPenilaianIndikator").val() == "" ||  $("#modalRpsPenilaianKriteria").val() == "" ||   $("#modalRpsMetodePembelajaran").val() == "" ||   $("#modalRpsNilai").val() == "" || $("#modalRpsNilai").val() > 100 ||  $("#modalRpsDescNilai").val() == "")
        {
            if($("#modalRpsSubCPMK").val() == "" )
            {
                $(".spanInputRpsSubCPMK").css("display", "");
                $(".spanInputRpsSubCPMK").html("<strong style='color: #fc4b6c;'>Sub CPMK tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputRpsSubCPMK").css("display", "none");
                    $(".spanInputRpsSubCPMK").html("");
                },3000);
            } 
            else if($("#modalRpsBahanKajian").val() == "" )
            {
                $(".spanInputRpsBahanKajian").css("display", "");
                $(".spanInputRpsBahanKajian").html("<strong style='color: #fc4b6c;'>Bahan kajian tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputRpsBahanKajian").css("display", "none");
                    $(".spanInputRpsBahanKajian").html("");
                },3000);
            } 
            else if($("#modalRpsPenilaianIndikator").val() == "" )
            {
                $(".spanInputRpsPenilaianIndikator").css("display", "");
                $(".spanInputRpsPenilaianIndikator").html("<strong style='color: #fc4b6c;'>Penilaian indikator tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputRpsPenilaianIndikator").css("display", "none");
                    $(".spanInputRpsPenilaianIndikator").html("");
                },3000);
            } 
            else if($("#modalRpsPenilaianKriteria").val() == "" )
            {
                $(".spanInputRpsPenilaianKriteria").css("display", "");
                $(".spanInputRpsPenilaianKriteria").html("<strong style='color: #fc4b6c;'>Penilaian kriteria tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputRpsPenilaianKriteria").css("display", "none");
                    $(".spanInputRpsPenilaianKriteria").html("");
                },3000);
            } 
            else if($("#modalRpsMetodePembelajaran").val() == "" )
            {
                $(".spanInputRpsMetodePembelajaran").css("display", "");
                $(".spanInputRpsMetodePembelajaran").html("<strong style='color: #fc4b6c;'>Metode pembelajaran tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputRpsMetodePembelajaran").css("display", "none");
                    $(".spanInputRpsMetodePembelajaran").html("");
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
            else if($("#modalRpsDescNilai").val() == "" )
            {
                $(".spanInputRpsDescNilai").css("display", "");
                $(".spanInputRpsDescNilai").html("<strong style='color: #fc4b6c;'>Deskripsi nilai tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputRpsDescNilai").css("display", "none");
                    $(".spanInputRpsDescNilai").html("");
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
        

                    var dataEdit = {
                        idRPS: $("#modalIDRPS").val(),
                        RPSMinggu : $("#modalRpsMinggu").val(),
                        RPSSubCPMK : $("#modalRpsSubCPMK").val(),
                        RPSBahanKajian : $("#modalRpsBahanKajian").val(),
                        RPSPenilaianIndikator : $("#modalRpsPenilaianIndikator").val(),
                        RPSPenilaianKriteria : $("#modalRpsPenilaianKriteria").val(),
                        RPSPenilaianMetodePembelajaran : $("#modalRpsMetodePembelajaran").val(),
                        RPSNilai : $("#modalRpsNilai").val(),
                        RPSDescNilai : $("#modalRpsDescNilai").val(),
                        filesname: filesname

                    };

                    var dataToken = {
                        action : 'EditRPS',
                        dataEdit : dataEdit,
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
    });

    $(document).on('click','.btnDeleteRPS',function () {
      
        if(confirm('Delete this RPS?')){
            var url = base_url_js+'rps/crud-RPS';
            var dataRemove = {
                idRPS : $(this).attr('data-id')
            };

            var dataToken = {
                action : 'DeleteRPS',
                dataRemove : dataRemove,
            }

            var token = jwt_encode(dataToken,'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {
                toastr.success('Data berhasil dihapus','Deleted');
                $('#GlobalModal').modal('hide');
                setTimeout(function () {
                    window.location = '';
                },1000);
            });
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