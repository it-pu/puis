

<div class="row">
<div class="col-md-12">

    <h1
        style="text-align: left;margin-top: 0px;
        margin-bottom: 30px;"><b>Rencana Pembelajaran Semester (RPS)</b></h1>
        <div class="form-group">
                    <label class="col-sm-1 control-label" style="text-align: left;">Code 
                    </label>
                    <label class="col-sm-5 control-label" style="text-align: left;">:  <?= $MKCode; ?> 
                    </label>
                    <label class="col-sm-1 control-label" style="text-align: left;" >Semester
                    </label>
                    <label class="col-sm-5 control-label" style="text-align: left;">:  <?= $Semester; ?> 
                    </label>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label" style="text-align: left;">Course 
                    </label>
                    <label class="col-sm-5 control-label" style="text-align: left;">:  <?= $Course; ?> 
                    </label>
                    <label class="col-sm-1 control-label" style="text-align: left;" >Curriculum Year
                    </label>
                    <label class="col-sm-5 control-label" style="text-align: left;">:  <?= $curriculumYear; ?> 
                    </label>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label" style="text-align: left;">Base Prodi 
                    </label>
                    <label class="col-sm-11 control-label" style="text-align: left;">:  <?= $Prodi; ?> 
                    </label>
                </div>


</div>
</div>

    <div class="row" style="margin-top: 20px;">
                
                        <div class="col-sm-12">   
                                <div class="panel-body">
                                <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-md-12">
                                            <button class="hide btn btn-success pull-right btnDowloadRPS"><i class="fa fa-download margin-right"></i> View PDF</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important" >
        <thead>
    
                    <tr style="background: #eceff1;">
                        <th style="text-align: left;">Capaian Pembelajaran</th>
                    </tr>
        </thead>
    </table>
                                        <div id="loadTableCPL"></div>
                                        <div id="loadTableCPMK"></div>
                                        <div id="loadTableSubCPMK"></div>
                                        <div id="loadTableDescMK"></div>
                                        <div id="loadTableMaterial"></div>
                                        <div id="loadTablePenilaian"></div>
                                        <div id="loadTableRPS"></div>



                                    </div>
                                </div>
                        </div>
    </div>


<script>
    $(document).ready(function(){
        loadDataCPL();
        loadDataSubCPMK();
        loadDataCPMK();
        loadDataDescMK();
        loadDataMaterial();
        loadDataPenilaian();
        loadDataRPS();

    });

    function loadDataCPL() 
    {
        var data = {
            action : 'showCPL',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-CPL';

        $.post(url,{token:token},function (jsonResult) {

if(JSON.parse(jsonResult).length>0){

    var tr = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {

                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
                    '<td><div style="text-align: left;">'+v.Code+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Descriptions+'</div></td>' +

                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableCPL').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
'        <thead>' +
    '                <tr style="background: #eceff1;">' +
            '                    <th style=" text-align: left;" colspan="2">Capaian Pembelajaran Lulusan (CPL) Prodi yang dibebankan pada MK</th>'+
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    } 

            },500);

    }else {
                    $('#loadTableCPL').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" colspan="2">Capaian Pembelajaran Lulusan (CPL) Prodi yang dibebankan pada MK</th>'+
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }

    function loadDataCPMK() 
    {
        var data = {
            action : 'showNonSubCPMK',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-CPMK';

        $.post(url,{token:token},function (jsonResult) {

if(JSON.parse(jsonResult).length>0){

    var tr = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {

                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
                    '<td><div style="text-align: left;">'+v.Code+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Description+'</div></td>' +

                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableCPMK').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important" >' +
'        <thead>' +
    '                <tr style="background: #eceff1;">' +
            '                    <th style=" text-align: left;" colspan="2">Capaian Pembelajaran Mata Kuliah (CPMK)</th>'+
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    } 

            },500);

    }else {
                    $('#loadTableCPMK').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" colspan="2">Capaian Pembelajaran Mata Kuliah (CPMK)</th>'+
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }

    function loadDataCPMK() 
    {
        var data = {
            action : 'showNonSubCPMK',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-CPMK';

        $.post(url,{token:token},function (jsonResult) {

if(JSON.parse(jsonResult).length>0){

    var tr = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {

                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
                    '<td><div style="text-align: left;">'+v.Code+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Description+'</div></td>' +

                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableMaterial').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
'        <thead>' +
    '                <tr style="background: #eceff1;">' +
            '                    <th style=" text-align: left;" colspan="2">Bahan Kajian</th>'+
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    } 

            },500);

    }else {
                    $('#loadTableMaterial').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" colspan="2">Bahan Kajian</th>'+
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }

    function loadDataSubCPMK() 
    {
        var data = {
            action : 'showSubCPMK',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-CPMK';

        $.post(url,{token:token},function (jsonResult) {

if(JSON.parse(jsonResult).length>0){

    var tr = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {

                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
                    '<td><div style="text-align: left;">'+v.Code+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Description+'</div></td>' +

                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableSubCPMK').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
'        <thead>' +
    '                <tr style="background: #eceff1;">' +
            '                    <th style=" text-align: left;" colspan="2">Sub CPMK</th>'+
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    } 

            },500);

    }else {
                    $('#loadTableSubCPMK').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important" >' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" colspan="2">Sub CPMK</th>'+
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }

    function loadDataDescMK() 
    {
        var data = {
            action : 'showModalDescMK',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-desc-MK';

        $.post(url,{token:token},function (jsonResult) {

if(JSON.parse(jsonResult).length>0){

    var tr = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {

                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
               
                    '<td><div style="text-align: left;">'+v.Description+'</div></td>' +

                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableDescMK').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
'        <thead>' +
    '                <tr style="background: #eceff1;">' +
            '                    <th style=" text-align: left;">Deskripsi MK</th>'+
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    } 

            },500);

    }else {
                    $('#loadTableDescMK').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" >Deskripsi MK</th>'+
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }

    function loadDataMaterial() 
    {
        var data = {
            action : 'showModalMaterial',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-bahan-kajian';

        $.post(url,{token:token},function (jsonResult) {

if(JSON.parse(jsonResult).length>0){

    var tr = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {

                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
                '<td>'+(i + 1)+'</td>' +
                    '<td><div style="text-align: left;">'+v.Description+'</div></td>' +

                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableMaterial').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
'        <thead>' +
    '                <tr style="background: #eceff1;">' +
            '                    <th style=" text-align: left;" colspan="2">Bahan Kajian</th>'+
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    } 

            },500);

    }else {
                    $('#loadTableMaterial').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 0px !important">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" colspan="2">Bahan Kajian</th>'+
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }

    function loadDataPenilaian() 
    {
        
        var tr = '';

                tr = tr+'<tr class="item-question" >' +
               
                    '<td><div style="text-align: left;">Pustaka Utama</div></td>' +
                    '<td colspan="6"><div style="text-align: left;" ></div></td>' +

                    '</tr>'+
                    '<tr class="item-question" >' +
               
                    '<td><div style="text-align: left;">Pustaka Pendukung</div></td>' +
                    '<td colspan="6"><div style="text-align: left;" ></div></td>' +

                    '</tr>'+
                    '<tr class="item-question" >' +

                    '<td><div style="text-align: left;">Mata Kuliah Prasyarat</div></td>' +
                    '<td colspan="6"><div style="text-align: left;" ></div></td>' +

                    '</tr>'+
                    '<tr class="item-question" >' +

                    '<td><div style="text-align: left;">Dosen Team Teaching</div></td>' +
                    '<td colspan="6"><div style="text-align: left;" ></div></td>' +

                    '</tr>'+
                    '<tr class="item-question" >' +

                    '<td><div style="text-align: left;">Media Pembelajaran</div></td>' +
                    '<td colspan="6"><div style="text-align: left;" ></div></td>' +

                    '</tr>';
          

        
                    $('#loadTablePenilaian').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre" style="margin-bottom: 20px !important">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
            '                    <th style="text-align: left;" >Penilaian</th>'+
            '                    <th style="text-align: left;" >Tugas</th>'+
            '                    <th style="text-align: left;" >45%</th>'+
            '                    <th style="text-align: left;" >UTS</th>'+
            '                    <th style="text-align: left;" >25%</th>'+
            '                    <th style="text-align: left;" >UAS</th>'+
            '                    <th style="text-align: left;" >30%</th>'+


            '                </tr>' +
                '        </thead>' +
       '        <tbody id="listQuestion">'+tr+'</tbody>' +

                '    </table>' +
                '</div>');
       

    }

    function loadDataRPS() 
    {
        var data = {
            action : 'showModalRPS',
            CDID : '<?= $CDID; ?>',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rps/crud-RPS';

        $.post(url,{token:token},function (jsonResult) {
            console.log(JSON.parse(jsonResult).length);

if(JSON.parse(jsonResult).length>0){

    var tr = '';
    var nilai = '';
        if(JSON.parse(jsonResult).length>0){
            $.each(JSON.parse(jsonResult),function (i,v) {
                if (v.ValueDesc=="") {
                    nilai = '<div style="text-align: left;">'+v.Value+'</div>';
                } else {
                    nilai = '<div style="text-align: left;">'+v.Value+'% ('+v.ValueDesc+')</div>';
                }
                tr = tr+'<tr class="item-question" data-id="'+v.ID+'">' +
                    '<td><div style="text-align: left;">'+v.Week+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.SubCPMK+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Material+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Indikator+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Kriteria+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.Description+'</div></td>' +
                    '<td>'+nilai+'</td>'+
                    '<td><div style="text-align: left;"><a href ="'+base_url_js+'fileGetAny/document-RPS_'+v.CDID+'_'+v.Week+'-'+v.File+'" target="_blank">'+v.File+'</a></div></td>' +
                    '<td><div style="text-align: left;">'+v.EntredAt+'</div></td>' +
                    '<td><div style="text-align: left;">'+v.EntredBy+'</div></td>' +


                    '</tr>';
            });
        }

        setTimeout(function () {

if(JSON.parse(jsonResult).length>0){
    $('#loadTableRPS').html('<div class="table-responsive">' +
'    <table class="table table-bordered table-centre">' +
'        <thead>' +
'                <tr style="background: #eceff1;">' +
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
            
            '                </tr>' +
'        </thead>' +
'        <tbody id="listQuestion">'+tr+'</tbody>' +
'    </table>' +
'</div>');

  
      

    }

            },500);

    }else {
                    $('#loadTableRPS').html('<div class="table-responsive">' +
                '    <table class="table table-bordered table-centre">' +
                '        <thead>' +
                '                <tr style="background: #eceff1;">' +
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
            
            '                </tr>' +
                '        </thead>' +
                '    </table>' +
                '</div>');
                }

});


    }


    $(document).on('click','.btnEditCPMK',function () {
        var CPMKID = $(this).attr('data-id');
        var MKCode = $(this).attr('data-mkcode');
        var CPMKType = $(this).attr('data-type');
        var CPMKCode = $(this).attr('data-code');
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
        if( $("#ModalSelectTypeCPMK").val() == null ||   $("#modalCPMKCode").val() == "" ||  $("#modalCPMKDesc").val() == "" )
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
        }
        else{
            var url = base_url_js+'rps/crud-CPMK';

            var dataEdit = {
                CPMKID: $("#modalIDCPMK").val(),
                CPMKType : $("#ModalSelectTypeCPMK").val(),
                CPMKCode : $("#modalCPMKCode").val(),
                CPMKDesc : $("#modalCPMKDesc").val(),

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