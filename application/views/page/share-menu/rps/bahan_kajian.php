

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


<div id="generate-edom">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                    <i class="fa fa-file-text-o"></i> Bahan Kajian
                    </h4>
                </div>
                
                

                    <div class="row">
                        <div class="col-sm-12">
                            <!-- <div class="panel panel-default"> -->      
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        
                                        <div id="loadTableMaterial"></div>
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
        loadDataMaterial();
    });

    function loadDataMaterial() 
    {
        $('#loadTableMaterial').html('<table id="tableDataMaterial" class="table table-bordered table-striped table-centre" style="width:100%">' +
            '               <thead>' +
            '                <tr style="background: #eceff1;">' +
            '                    <th style="width: 1%;text-align: center;">No</th>'+
            // '                    <th style="width: 8%;text-align: center;">Curriculum Code</th>'+
            // '                    <th style="width: 16%;text-align: center;">Course</th>'+
            // '                    <th style="width: 11%;text-align: center;">Base Prodi</th>'+
            '                    <th style="text-align: center;">Description</th>'+
            //'                    <th style="width: 5%;text-align: left;">Order</th>'+ 

            '                    <th style="width: 10%;text-align: center;">Entered At</th>'+
            '                    <th style="width: 10%;text-align: center;">Entered By</th>'+
            '                    <th style="width: 5%;text-align: left;">Action</th>'+ 
            '                </tr>' +
            '                </thead>' +
            '           </table>');

        var data = {
            action : 'getDataMaterial',
            CDID : '<?= $CDID; ?>',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = "<?php echo base_url('rps/crud-bahan-kajian'); ?>";

        var dataTable = $('#tableDataMaterial').DataTable( {
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




    $(document).on('click','.btnEditMaterial',function () {
        var MaterialID = $(this).attr('data-id');
        var MaterialMKCode = $(this).attr('data-mkscode');
        var MaterialOrder = $(this).attr('data-order');
        var MaterialDesc = $(this).attr('data-description');
        var html = '<div class="col-md-12" id="modalEdit">'+
            '<form class="form-horizontal">'+
            '        <div class="form-group">'+
            '                <input type="hidden" id="modalIDMaterial" class="form-control" value="'+MaterialID+'">'+
            '            <label class="col-sm-4 control-label">Curriculum Code</label>'+
            '            <div class="col-sm-8">'+
            '                <input id="modalCodeMaterial" class="form-control" value="'+MaterialMKCode+'" disabled>'+
            '            </div>'+
            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Deskripsi '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+
            '</label>'+
            '            <div class="col-sm-8">'+
            '                <textarea class="form-control" id="modalMaterialDesc" rows="2" value="'+MaterialDesc+'">'+MaterialDesc+'</textarea>'+
            '       <span class="help-block spanInputMaterialDesc" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Order '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+
            '</label>'+
            '            <div class="col-sm-8">'+
            '                <input id="modalMaterialOrder" type="number" class="form-control" value="'+MaterialOrder+'">'+
            '       <span class="help-block spanInputMaterialOrder" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '    <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">'+
            '        <div class="col-sm-12" id="BtnFooter">'+
            '            <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
            '            <button type="button" id="ModalbtnMaterial" class="btn btn-default btn-default-success hide">Edit Data</button>'+
            '        </div>'+
            '    </div>'+
            '</form>';

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Edit Bahan Kajian</h4>');

        $('#GlobalModal .modal-body').html(html);

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#ModalbtnMaterial').removeClass('hide');
    });

    $(document).on('click','#ModalbtnMaterial',function () {
        if( $("#modalMaterialDesc").val() == "" ||   $("#modalMaterialOrder").val() == "" )
        {
            if($("#modalMaterialDesc").val() == "" )
                {
                    $(".spanInputMaterialDesc").css("display", "");
                    $(".spanInputMaterialDesc").html("<strong style='color: #fc4b6c;'>Deskripsi tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputMaterialDesc").css("display", "none");
                        $(".spanInputMaterialDesc").html("");
                    },3000);
                } 
            else if($("#modalMaterialOrder").val() == "" )
                {
                    $(".spanInputMaterialOrder").css("display", "");
                    $(".spanInputMaterialOrder").html("<strong style='color: #fc4b6c;'>Order tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputMaterialOrder").css("display", "none");
                        $(".spanInputMaterialOrder").html("");
                    },3000);
                } 
        }
        else{
            var url = base_url_js+'rps/crud-bahan-kajian';

            var dataEdit = {
                MaterialID: $("#modalIDMaterial").val(),
                MaterialCode : $("#modalCodeMaterial").val(),
                MaterialDesc : $("#modalMaterialDesc").val(),
                MaterialOrder : $("#modalMaterialOrder").val(),

            };

            var dataToken = {
                action : 'EditMaterial',
                dataEdit : dataEdit,
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

    });

    $(document).on('click','.btnDeleteMaterial',function () {
        var ID_Attd = $(this).attr('data-id');
       
        if(confirm('Delete ?')){
            var url = base_url_js+'rps/crud-bahan-kajian';
            var dataRemove = {
                idMaterial : $(this).attr('data-id')
            };

            var dataToken = {
                action : 'DeleteMaterial',
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