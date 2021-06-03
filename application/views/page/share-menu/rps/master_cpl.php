

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
                    <i class="fa fa-file-text-o"></i> Capaian Pembelajaran Lulusan (CPL)
                    </h4>
                </div>
                
                

                    <div class="row">
                        <div class="col-sm-12">
                            <!-- <div class="panel panel-default"> -->      
                                <div class="panel-body">
                                    <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-md-12">
                                            <button class="btn btn-success pull-right" id="btnCreateCPLMaster">Create CPL Master</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        
                                        <div id="loadTableCPL"></div>
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
        loadDataCPL();
    });

    function loadDataCPL() 
    {
        $('#loadTableCPL').html('<table id="tableDataCPL" class="table table-bordered table-striped table-centre" style="width:100%">' +
            '               <thead>' +
            '                <tr style="background: #eceff1;">' +
            '                    <th style="width: 1%;text-align: center;">No</th>'+
            '                    <th style="width: 11%;text-align: center;">Code</th>'+
            '                    <th style="text-align: center;">Description</th>'+
            '                    <th style="width: 10%;text-align: center;">Entered At</th>'+
            '                    <th style="width: 10%;text-align: center;">Entered By</th>'+
            '                    <th style="width: 5%;text-align: left;">Action</th>'+ 
            '                </tr>' +
            '                </thead>' +
            '           </table>');

        var data = {
            action : 'getDataCPL'
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = "<?php echo base_url('rps/crud-CPL'); ?>";

        var dataTable = $('#tableDataCPL').DataTable( {
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

    $(document).on('click','#btnCreateCPLMaster',function () {
        var html = '<div class="col-md-12" id="modalAdd">'+
            '<form class="form-horizontal">'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Code '+
            '               <span><strong style="color: #fc4b6c;">*</strong></span>'+
            '            </label>'+
            '                        <div class="col-sm-8">'+
            '                            <input id="modalCodeCPL" type="text" class="form-control" >'+
            '                            <span class="help-block spanInputCodeCPL" style="display: none;"></span>'+ 
            '                        </div>'+
            '                    </div>'+
            '                    <div class="form-group">'+
            '                        <label class="col-sm-4 control-label">Deskripsi '+
            '                <span><strong style="color: #fc4b6c;">*</strong></span>'+
            '            </label>'+
            '            <div class="col-sm-8">'+
            '                <textarea class="form-control" id="modalDescCPL" rows="2"></textarea>'+
            '            <span class="help-block spanInputDescCPL" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '    <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">'+
            '        <div class="col-sm-12" id="BtnFooter">'+
            '            <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
            '            <button type="button" id="ModalbtnAddCPL" class="btn btn-default btn-default-success hide">Add Data</button>'+
            '        </div>'+
            '    </div>'+
            '</form>';

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Add Master Capaian Pembelajaran Lulusan (CPL)</h4>');

        $('#GlobalModal .modal-body').html(html);

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#ModalbtnAddCPL').removeClass('hide');

    });

    $(document).on('click','#ModalbtnAddCPL',function () {
        if( $("#modalCodeCPL").val() == "" ||   $("#modalDescCPL").val() == "" )
        {
            if($("#modalCodeCPL").val() == "" )
                {
                    $(".spanInputCodeCPL").css("display", "");
                    $(".spanInputCodeCPL").html("<strong style='color: #fc4b6c;'>Code tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputCodeCPL").css("display", "none");
                        $(".spanInputCodeCPL").html("");
                    },3000);
                } 
            else if($("#modalDescCPL").val() == "" )
                {
                    $(".spanInputDescCPL").css("display", "");
                    $(".spanInputDescCPL").html("<strong style='color: #fc4b6c;'>Deskripsi tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputDescCPL").css("display", "none");
                        $(".spanInputDescCPL").html("");
                    },3000);
                } 
        }
        else{
            var url = base_url_js+'rps/crud-CPL';

            var dataAdd = {
                codeCPL : $("#modalCodeCPL").val(),
                descCPL : $("#modalDescCPL").val(),
            };

            var dataToken = {
                action : 'AddCPL',
                dataAdd : dataAdd,
            }

            var token = jwt_encode(dataToken,'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {
            
                toastr.success('Data CPL tersimpan','Success');
                $('#GlobalModal').modal('hide');
                setTimeout(function () {
                    window.location = '';
                },1000);
                
            });
        }
        
    });

    $(document).on('click','.btnEditCPLMaster',function () {
        var CPLID = $(this).attr('data-id');
        var CPLCode = $(this).attr('data-code');
        var CPLDesc = $(this).attr('data-description');
        var html = '<div class="col-md-12" id="modalEdit">'+
            '<form class="form-horizontal">'+
            '        <div class="form-group">'+
            '                <input type="hidden" id="modalIDCPL" class="form-control" value="'+CPLID+'">'+
            '            <label class="col-sm-4 control-label">Code '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '            </label>'+
            '            <div class="col-sm-8">'+
            '                <input id="modalCodeCPL" class="form-control" value="'+CPLCode+'">'+
            '                <span class="help-block spanInputCodeCPL" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '        <div class="form-group">'+
            '            <label class="col-sm-4 control-label">Deskripsi '+
            '    <span><strong style="color: #fc4b6c;">*</strong></span>'+

            '            </label>'+
            '            <div class="col-sm-8">'+
            '                <textarea class="form-control" id="modalDescCPL" rows="2" value="'+CPLDesc+'">'+CPLDesc+'</textarea>'+
            '                <span class="help-block spanInputDescCPL" style="display: none;"></span>'+ 

            '            </div>'+
            '        </div>'+
            '    <div class="form-group" style="border-top: 1px solid #d3d3d3;padding-top: 10px;text-align: right;">'+
            '        <div class="col-sm-12" id="BtnFooter">'+
            '            <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
            '            <button type="button" id="ModalbtnEditCPL" class="btn btn-default btn-default-success hide">Edit Data</button>'+
            '        </div>'+
            '    </div>'+
            '</form>';

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Edit Master Capaian Pembelajaran Lulusan (CPL)</h4>');

        $('#GlobalModal .modal-body').html(html);

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#ModalbtnEditCPL').removeClass('hide');
    });

    $(document).on('click','#ModalbtnEditCPL',function () {
        if( $("#modalCodeCPL").val() == "" ||   $("#modalDescCPL").val() == "" )
        {
            if($("#modalCodeCPL").val() == "" )
                {
                    $(".spanInputCodeCPL").css("display", "");
                    $(".spanInputCodeCPL").html("<strong style='color: #fc4b6c;'>Code tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputCodeCPL").css("display", "none");
                        $(".spanInputCodeCPL").html("");
                    },3000);
                } 
            else if($("#modalDescCPL").val() == "" )
                {
                    $(".spanInputDescCPL").css("display", "");
                    $(".spanInputDescCPL").html("<strong style='color: #fc4b6c;'>Deskripsi tidak boleh kosong !</strong>");
                    setTimeout(function () {
                        $(".spanInputDescCPL").css("display", "none");
                        $(".spanInputDescCPL").html("");
                    },3000);
                } 
        }
        else{
            var url = base_url_js+'rps/crud-CPL';

            var dataEdit = {
                idCPL : $("#modalIDCPL").val(),
                codeCPL : $("#modalCodeCPL").val(),
                descCPL : $("#modalDescCPL").val(),
            };

            var dataToken = {
                action : 'EditCPL',
                dataEdit : dataEdit,
            }

            var token = jwt_encode(dataToken,'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {
        
                toastr.success('Data capaian pembelajaran lulusan tersimpan','Success');
                $('#GlobalModal').modal('hide');
                setTimeout(function () {
                    window.location = '';
                },1000);
            });
        }
    });

    $(document).on('click','.btnDeleteCPLMaster',function () {
        var ID_Attd = $(this).attr('data-id');
       
        if(confirm('Delete CPL?')){
            var url = base_url_js+'rps/crud-CPL';
            var dataRemove = {
                idCPL : $(this).attr('data-id')
            };

            var dataToken = {
                action : 'DeleteCPL',
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


    

  function loadDataRequest2() {

$('#loadTable2').html('<table id="tableDataRequest2" class="table table-bordered table-striped table-centre" style="width:100%">' +
    '               <thead>' +
    '                <tr style="background: #eceff1;">' +
    '                    <th style="width: 1%;text-align: center;">No</th>'+
    '                    <th style="width: 11%;text-align: center;">Code</th>'+
    '                    <th style="text-align: center;">Description</th>'+
    // '                    <th style="width: 18%;text-align: center;">Email</th>'+
    // '                    <th style="width: 12%;text-align: center;">New Password</th>'+
    // '                    <th style="width: 15%;text-align: center;">Entered At</th>'+
    // '                    <th style="width: 8%;text-align: center;">Status</th>'+
    '                    <th style="width: 18%;text-align: left;">Action</th>'+ 
    '                </tr>' +
    '                </thead>' +
    '           </table>');


var data = {
    action : 'getDataCPL'
};

var token = jwt_encode(data,'UAP)(*');
var url = "<?php echo base_url('rps/crud-CPL'); ?>";

var dataTable = $('#tableDataRequest2').DataTable( {
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

function loadDataRequest3() {

$('#loadTable3').html('<table id="tableDataRequest3" class="table table-bordered table-striped table-centre" style="width:100%">' +
    '               <thead>' +
    '                <tr style="background: #eceff1;">' +
    '                    <th style="width: 1%;text-align: center;">No</th>'+
    '                    <th style="width: 11%;text-align: center;">Code</th>'+
    '                    <th style="text-align: center;">Description</th>'+
    // '                    <th style="width: 18%;text-align: center;">Email</th>'+
    // '                    <th style="width: 12%;text-align: center;">New Password</th>'+
    // '                    <th style="width: 15%;text-align: center;">Entered At</th>'+
    // '                    <th style="width: 8%;text-align: center;">Status</th>'+
    '                    <th style="width: 18%;text-align: left;">Action</th>'+ 
    '                </tr>' +
    '                </thead>' +
    '           </table>');


var data = {
    action : 'getDataCPL'
};

var token = jwt_encode(data,'UAP)(*');
var url = "<?php echo base_url('rps/crud-CPL'); ?>";

var dataTable = $('#tableDataRequest3').DataTable( {
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
</script>
<!-- 
<div class="row" style="margin-top: 20px;">
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-file-text-o"></i> Capaian Pembelajaran Mata Kuliah (CPMK)</h4>
        </div>
        <div class="panel-body" style="min-height: 100px;">
            <div id="viewListQuestion"></div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-file-text-o"></i> Sub CPMK</h4>
        </div>
        <div class="panel-body" style="min-height: 100px;">
            <div class="well">
                <div class="row">
                    <div class="col-md-6">
                        <label>Question Type</label>
                        <select class="form-control filter-table" id="filterType">
                            <option value="">--- All Type ---</option>
                            <option disabled>-----------------------</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Question Category</label>
                        <select class="form-control filter-table" id="filterQuestionCategory">
                            <option value="">--- All Category ---</option>
                            <option disabled>-----------------------</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="loadTable" style="margin-top: 20px;"></div>
        </div>
    </div>
</div>
</div>



<script>

$(document).ready(function () {
    setLoadFullPage();
    loadSelectOptionSurvQuestionType('#filterType','');
    loadSelectOptionSurvQuestionCategory('#filterQuestionCategory','');

    loadMasterQuestion();

    loadListQuestion();
});

$('.filter-table').change(function () {
    loadMasterQuestion();
});

function loadMasterQuestion() {

    $('#loadTable').html('<table id="tableData" class="table table-bordered table-striped table-centre">' +
        '               <thead>' +
        '                <tr style="background: #eceff1;">' +
        '                    <th style="width: 5%;">No</th>' +
        '                    <th>Question</th>' +
        '                    <th style="width: 20%;">Category</th>' +
        '                </tr>' +
        '                </thead>' +
        '           </table>');

    var filterType = $('#filterType').val();
    var filterQuestionCategory = $('#filterQuestionCategory').val();

    var data = {
        action : 'getMasterQuestion',
        DepartmentID : sessionIDdepartementNavigation,
        Type : filterType,
        QuestionCategory : filterQuestionCategory
    };

    var token = jwt_encode(data,'UAP)(*');
    var url = base_url_js+'apimenu/__crudSurvey';

    var dataTable = $('#tableData').DataTable( {
        "processing": true,
        "serverSide": true,
        "iDisplayLength" : 10,
        "ordering" : false,
        "language": {
            "searchPlaceholder": "Question..."
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

$(document).on('click','.btnAddToSurvey',function () {

    var Status = "<?= $dataSurvey['Status']; ?>";
    if(parseInt(Status)<=0){

        var ID = $(this).attr('data-id');

        var data = {
            action : 'addQuestionToSurvey',
            SurveyID : '<?= $dataSurvey['ID']; ?>',
            QuestionID : ID,
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'apimenu/__crudSurvey';

        $.post(url,{token:token},function (jsonResult) {

            if(parseInt(jsonResult.Status)>0){
                toastr.success('Question added','Success');
                loadListQuestion();
            } else {
                toastr.warning('Question already exist','Success');
            }
        });

    } else {
        toastr.error('The list of questions cannot be changed','Error');
    }




});

function loadListQuestion() {

    var data = {
        action : 'QuestionInMySurvey',
        SurveyID : '<?= $dataSurvey['ID']; ?>',
    };
    var token = jwt_encode(data,'UAP)(*');
    var url = base_url_js+'apimenu/__crudSurvey';

    loading_page_simple('#viewListQuestion','center');

    $.post(url,{token:token},function (jsonResult) {

        console.log(jsonResult);

        var li = '';
        if(jsonResult.length>0){
            $.each(jsonResult,function (i,v) {

                li = li+'<li class="item-question" data-id="'+v.ID+'">' +
                    '<div>'+
                    '<span class="label label-default">'+v.Category+'</span>' +
                    v.Question+'</div>' +
                    '<button data-id="'+v.ID+'" class="btn btn-sm btn-danger btnRemoveQuestion">' +
                    '<i class="fa fa-trash"></i></button></li>';

            });
        }

        setTimeout(function () {

            if(jsonResult.length>0){
                $('#viewListQuestion').html('<ol id="listQuestion">'+li+'</ol>');

                var StatusSurvey = "<?= $dataSurvey['Status']; ?>";

                if(parseInt(StatusSurvey)<=0){
                    $('#listQuestion').sortable({
                        axis: 'y',
                        update: function (event, ui) {
                            var No = 1;
                            $('#listQuestion li.item-question').each(function () {
                                var ID = $(this).attr('data-id');
                                updateQueue(ID,No);
                                No += 1;
                            });

                            // $('#dataTempQuiz').val(JSON.stringify(dataUpdate));

                        }
                    });
                }


            } else {
                $('#viewListQuestion').html('No question');
            }

        },500);

    });

}

$(document).on('click','.btnRemoveQuestion',function () {

    var Status = "<?= $dataSurvey['Status']; ?>";

    if(parseInt(Status)<=0){
        if(confirm('Are you sure?')){

            var ID = $(this).attr('data-id');

            var data = {
                action : 'removeQUestionFromSurvey',
                ID : ID
            };
            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'apimenu/__crudSurvey';

            $.post(url,{token:token},function (result) {
                loadListQuestion();
                toastr.success('Question removed from survey','Success');
            });

        }
    } else {
        toastr.error('The list of questions cannot be changed','Error');
    }



});

function updateQueue(ID,Queue) {

    var data = {
        action : 'updateQueueQuestion',
        ID : ID,
        Queue : Queue
    };
    var token = jwt_encode(data,'UAP)(*');
    var url = base_url_js+'apimenu/__crudSurvey';

    $.post(url,{token:token},function (result) {
        // toastr.success('Question removed from survey','Success');
    });

}
</script> -->