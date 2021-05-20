

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
                    <i class="fa fa-file-text-o"></i> Capaian Pembelajaran Mata Kuliah (CPMK)
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
                                        
                                        <div id="loadTableCPMK"></div>
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
    $(document).ready(function(){
        loadDataCPMK();
    });

    function loadDataCPMK() 
    {
        $('#loadTableCPMK').html('<table id="tableDataCPMK" class="table table-bordered table-striped table-centre" style="width:100%">' +
            '               <thead>' +
            '                <tr style="background: #eceff1;">' +
            '                    <th style="width: 1%;text-align: center;">No</th>'+
            // '                    <th style="width: 8%;text-align: center;">Curriculum Code</th>'+
            // '                    <th style="width: 16%;text-align: center;">Course</th>'+
            // '                    <th style="width: 11%;text-align: center;">Base Prodi</th>'+
            '                    <th style="width: 10%; text-align: center;">Type</th>'+
            '                    <th style="width: 10%; text-align: center;">Code</th>'+
            '                    <th style="text-align: center;">Description</th>'+
            //'                    <th style="width: 5%;text-align: left;">Order</th>'+ 

            '                    <th style="width: 10%;text-align: center;">Entered At</th>'+
            '                    <th style="width: 10%;text-align: center;">Entered By</th>'+
            '                    <th style="width: 5%;text-align: left;">Action</th>'+ 
            '                </tr>' +
            '                </thead>' +
            '           </table>');


        var data = {
            action : 'getDataCPMK',
            CDID : '<?= $CDID; ?>',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = "<?php echo base_url('rps/crud-CPMK'); ?>";

        var dataTable = $('#tableDataCPMK').DataTable( {
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




    $(document).on('click','.btnEditCPMK',function () {
        var CPMKID = $(this).attr('data-id');
        var MKCode = $(this).attr('data-mkcode');
        var CPMKType = $(this).attr('data-type');
        var CPMKCode = $(this).attr('data-code');
        var CPMKOrder = $(this).attr('data-order');
        var CPMKDesc = $(this).attr('data-description');
        var html = '<div class="col-md-12" id="modalEdit">'+
            '<form class="form-horizontal">'+
            '        <div class="form-group">'+
            '                <input type="hidden" id="modalIDCPMK" class="form-control" value="'+CPMKID+'">'+
            '            <label class="col-sm-4 control-label">Curriculum Code</label>'+
            '            <div class="col-sm-8">'+
            '                <input id="modalCDID" class="form-control" value="'+MKCode+'" disabled>'+
            '            </div>'+
            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Type '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+ 
            '            <div class="col-sm-8">'+
            '                <select class="form-control" id="ModalSelectTypeCPMK">'+
            '                    <option value="'+CPMKType+'" selected="selected">'+CPMKType+'</option>'+
            '                    <?php if ('+CPMKType+'=="sub"): ?>'+
            '                        <option value="non-sub">non-sub</option>'+
            '                    <?php else: ?>'+
            '                        <option value="sub">sub</option>'+
            '                    <?php endif ?>'+
            '                </select>'+
            '            </div>'+
            '       <span class="help-block spanInputTypeCPMK" style="display: none;"></span>'+ 

            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">CPMK Code'+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '            <div class="col-sm-8">'+
            '                <input id="modalCPMKCode" type="text" class="form-control" value="'+CPMKCode+'">'+
            '       <span class="help-block spanInputCPMKCode" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Deskripsi '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '            <div class="col-sm-8">'+
            '                <textarea class="form-control" id="modalCPMKDesc" rows="2" value="'+CPMKDesc+'">'+CPMKDesc+'</textarea>'+
            '       <span class="help-block spanInputCPMKDesc" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Order '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '</label>'+
            '            <div class="col-sm-8">'+
            '                <input id="modalCPMKOrder" type="number" class="form-control" value="'+CPMKOrder+'">'+
            '       <span class="help-block spanInputCPMKOrder" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '    <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">'+
            '        <div class="col-sm-12" id="BtnFooter">'+
            '            <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
            '            <button type="button" id="ModalbtnCPMK" class="btn btn-default btn-default-success hide">Edit Data</button>'+
            '        </div>'+
            '    </div>'+
            '</form>';

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Edit Capaian Pembelajaran Mata Kuliah (CPMK)</h4>');

        $('#GlobalModal .modal-body').html(html);

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#ModalbtnCPMK').removeClass('hide');
    });

    $(document).on('click','#ModalbtnCPMK',function () {
        if( $("#ModalSelectTypeCPMK").val() == null ||   $("#modalCPMKCode").val() == "" ||  $("#modalCPMKDesc").val() == "" ||  $("#modalCPMKOrder").val() == "" )
        {
            if($("#ModalSelectTypeCPMK").val() == null )
            {
                $(".spanInputTypeCPMK").css("display", "");
                $(".spanInputTypeCPMK").html("<strong style='color: #fc4b6c;'>Type tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputTypeCPMK").css("display", "none");
                    $(".spanInputTypeCPMK").html("");
                },3000);
            } 
            else if($("#modalCPMKCode").val() == "" )
            {
                $(".spanInputCPMKCode").css("display", "");
                $(".spanInputCPMKCode").html("<strong style='color: #fc4b6c;'>Code tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputCPMKCode").css("display", "none");
                    $(".spanInputCPMKCode").html("");
                },3000);
            } 
            else if($("#modalCPMKDesc").val() == "" )
            {
                $(".spanInputCPMKDesc").css("display", "");
                $(".spanInputCPMKDesc").html("<strong style='color: #fc4b6c;'>Deskripsi tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputCPMKDesc").css("display", "none");
                    $(".spanInputCPMKDesc").html("");
                },3000);
            } 
            else if($("#modalCPMKOrder").val() == "" )
            {
                $(".spanInputCPMKOrder").css("display", "");
                $(".spanInputCPMKOrder").html("<strong style='color: #fc4b6c;'>Order tidak boleh kosong !</strong>");
                setTimeout(function () {
                    $(".spanInputCPMKOrder").css("display", "none");
                    $(".spanInputCPMKOrder").html("");
                },3000);
            } 
        }
        else{
            var url = base_url_js+'rps/crud-CPMK';

            var dataEdit = {
                CPMKID: $("#modalIDCPMK").val(),
                CPMKType : $("#ModalSelectTypeCPMK").val(),
                CPMKCode : $("#modalCPMKCode").val(),
                CPMKDesc : $("#modalCPMKDesc").val(),
                CPMKOrder : $("#modalCPMKOrder").val(),

            };

            var dataToken = {
                action : 'EditCPMK',
                dataEdit : dataEdit,
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

    });

    $(document).on('click','.btnDeleteCPMK',function () {
        var ID_Attd = $(this).attr('data-id');
       
        if(confirm('Delete this CPMK?')){
            var url = base_url_js+'rps/crud-CPMK';
            var dataRemove = {
                idCPMK : $(this).attr('data-id')
            };

            var dataToken = {
                action : 'DeleteCPMK',
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


</script>