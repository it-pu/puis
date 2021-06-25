

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
    <h1
        style="text-align: center;margin-top: 0px;
        margin-bottom: 30px;"><b>Manage Capaian Pembelajaran Lulusan (CPL)</b></h1>
    <table class="table table-striped">
    <tbody>
        <tr>
            <td style="width: 15%;">Code</td>
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

<!--<pre>-->
<!--    --><?php //print_r($dataSurvey); ?>
<!--</pre>-->





<div id="generate-edom" style="margin-top: 20px;">
    <div class="row">
    <div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"> List Capaian Pembelajaran Lulusan (CPL)</h4>
        </div>
        <div class="panel-body" style="min-height: 100px;">
            <div class="table-responsive">
            <div id="viewListQuestion"></div>
            </div>
        </div>
        
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-database margin-right"></i> Master Capaian Pembelajaran Lulusan (CPL)</h4>
        </div>
        <div class="panel-body" style="min-height: 100px;">
            <div class="table-responsive">
                <div id="loadTableCPL"></div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script>
    $(document).ready(function(){
        tableDataCPL();
        loadListQuestion();
    });

    $(document).on('click','.btnAddToCPL',function () {

        var CDID = "<?= $CDID; ?>";
        var ID = $(this).attr('data-id');

        var data = {
            action : 'addListToCPL',
            CDID : CDID,
            ID : ID,
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = "<?php echo base_url('rps/crud-CPL'); ?>";

        $.post(url,{token:token},function (jsonResult) {
            
            if(parseInt(JSON.parse(jsonResult).Status)>0){
                toastr.success('CPL added','Success');
                loadListQuestion();
            } else {
                toastr.warning('CPL already exist','Success');
            }
        });
    });

    function loadListQuestion() {

        var data = {
            action : 'DataInMyCPL',
            CDID : '<?= $CDID; ?>',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = "<?php echo base_url('rps/crud-CPL'); ?>";

        loading_page_simple('#viewListQuestion','center');

        $.post(url,{token:token},function (jsonResult) {

            var li = '';
            if(JSON.parse(jsonResult).length>0){
                $.each(JSON.parse(jsonResult),function (i,v) {

                    li = li+'<li class="item-question" data-idList="'+v.ID+'">' +
                        '<div>'+
                        '<span class="label label-default">'+v.Code+'</span><br>' +
                        v.Description+'</div>' +
                        '<button data-id="'+v.ID+'" class="btn btn-sm btn-danger btnRemoveQuestion">' +
                        '<i class="fa fa-trash"></i></button></li>';
                });
            }

            setTimeout(function () {

                if(JSON.parse(jsonResult).length>0){
                    $('#viewListQuestion').html('<ol id="listQuestion">'+li+'</ol>');

                        $('#listQuestion').sortable({
                            axis: 'y',
                            update: function (event, ui) {
                                var No = 1;
                                $('#listQuestion li.item-question').each(function () {
                                    var ID = $(this).attr('data-idList');
                                    updateQueue(ID,No);
                                    No += 1;
                                });
                            }
                        });
                } else {
                    $('#viewListQuestion').html('Tidak ada capaian pembelajaran lulusan');
                }

            },500);

        });

    }

    $(document).on('click','.btnRemoveQuestion',function () {


        if(confirm('Are you sure?')){

            var ID = $(this).attr('data-id');
console.log(ID);
            var data = {
                action : 'removeFromListCPL',
                ID : ID
            };
            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'rps/crud-CPL';

            $.post(url,{token:token},function (result) {
                loadListQuestion();
                toastr.success('CPL removed from list','Success');
            });

        }
    });

    

    function tableDataCPL() {

        $('#loadTableCPL').html('<table id="tableDataCPL" class="table table-bordered table-striped table-centre" style="width:100%">' +
            '               <thead>' +
            '                <tr style="background: #eceff1;">' +
            '                    <th style="width: 1%;text-align: center;">No</th>'+
            '                    <th style="width: 11%;text-align: center;">Code</th>'+
            '                    <th style="text-align: center;">Description</th>'+
            '                </thead>' +
            '           </table>');


        var data = {
            action : 'manageDataCPL'
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

    function updateQueue(ID,Queue) {

        var data = {
            action : 'updateQueueCPL',
            ID : ID,
            Queue : Queue
        };
        
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-CPL';

        $.post(url,{token:token},function (result) {
            // toastr.success('Question removed from survey','Success');
        });

    }
</script>