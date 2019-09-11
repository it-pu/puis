

<style>
    #tableData tr th {
        text-align: center;
    }
</style>

<div class="well">
    <div class="row">

        <div class="col-md-3 form-data-edit" style="border-right: 1px solid #CCCCCC;">

            <div style="text-align: right;margin-bottom: 20px;">
                <button class="btn btn-success btn-round" id="btnLembagaMitra"><i class="fa fa-cog margin-right"></i> Lembaga Mitra Kerjasama</button>
            </div>

            <div class="form-group">
                <label>Tipe Kerjasama</label>
                <input class="hide" id="formID" />
                <select class="form-control" id="formTingkat">
                    <option value="1">Kerjasama Pendidikan</option>
                    <option value="2">Kerjasama Penelitian</option>
                    <option value="3">Kerjasama Pengabdian Kepada Masyarakat </option>
                </select>
            </div>

            <div class="form-group">
                <label>Lembaga Mitra Kerjasama</label>
                <select id="formLembagaMitraID" class="form-control"></select>
            </div>
            <div class="form-group">
                <label>Tingkat</label>
                <select class="form-control" id="formTingkat">
                    <option value="Internasional">Internasional</option>
                    <option value="Nasional">Nasional</option>
                    <option value="Lokal">Wilayah / Lokal</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bentuk Kegiatan / Manfaat</label>
                <textarea class="form-control" id="formBenefit"></textarea>
            </div>
            <div class="form-group">
                <label>Masa Berlaku</label>
                <input class="form-control" id="formDueDate" />
            </div>
            <div class="form-group">
                <label>Bukti Kerjasama (.pdf | Maks 8 Mb)</label>
                <form id="formupload_files" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                    <input type="file" name="userfile" id="upload_files" accept="application/pdf">
                </form>
            </div>

            <div class="row">
                <div class="col-xs-8" id="viewFile"></div>
                <div class="col-xs-4">
                    <div style="text-align: right;">
                        <button class="btn btn-primary btn-round" id="btnSave"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-9">
            <div style="text-align: right; "><button class="btn btn-success btn-round" id="btndownloaadExcel" title="Dowload Excel"><i class="fa fa-file-excel-o"></i> Excel </button></div> 
             
            <div id="viewData" class="table-responsive"></div>

        </div>

    </div>
</div>

<script>

    $(document).ready(function () {

        window.act = "<?= $accessUser; ?>";
        if(parseInt(act)<=0){
            $('.form-data-edit').remove();
        } else {
            loadDataLembagaMitra();
            $( "#formDueDate" )
                .datepicker({
                    showOtherMonths:true,
                    autoSize: true,
                    dateFormat: 'dd MM yy',
                    // minDate: new Date(moment().year(),moment().month(),moment().date()),
                    onSelect : function () {
                        // var data_date = $(this).val().split(' ');
                        // var nextelement = $(this).attr('nextelement');
                        // nextDatePick(data_date,nextelement);
                    }
                });
        }

        loadDataTable();

    });


    $("#btndownloaadExcel").click(function(){
        var akred = "0";

        var url = base_url_js+'agregator/excel-kerjasama-perguruantinggi';
        data = {
          akred : akred
        }
        var token = jwt_encode(data,"UAP)(*");
        FormSubmitAuto(url, 'POST', [
            { name: 'token', value: token },
        ]);
    })

    $('#btnLembagaMitra').click(function () {

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Lembaga Mitra Kerjasama</h4>');

        var body = '<div class="row">' +
            '    <div class="col-md-5">' +
            '        <div class="well">' +
            '            <div class="form-group">' +
            '                <input class="hide" id="formID">' +
            '                <input class="form-control" id="formLembaga" placeholder="Input lembaga..">' +
            '            </div>' +
            '            <div class="form-group">' +
            '                <textarea class="form-control" id="formDescription" placeholder="Input description..."></textarea>' +
            '            </div>' +
            '            <div style="text-align:right;">' +
            '                <button class="btn btn-success btn-round" id="btnSaveLembaga"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '    ' +
            '    <div class="col-md-7">' +
            '        <table class="table table-striped table-bordered" id="tableViewLemabagaSurview">' +
            '            <thead>' +
            '            <tr style="background: #20485A;color: #FFFFFF;">' +
            '                <th style="width: 1%;">No</th>' +
            '                <th>Lembaga</th>' +
            '                <th style="width: 2%;text-align: center;"><i class="fa fa-cog"></i></th>' +
            '            </tr>' +
            '            </thead>' +
            '           <tbody id="listLembaga"></tbody>' +
            '        </table>' +
            '    </div>' +
            '</div>';

        $('#GlobalModal .modal-body').html(body);

        loadDataLembagaMitra();

        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-primary btn-round" data-dismiss="modal"><i class="fa fa-close"></i>  Close</button>');
        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#btnSaveLembaga').click(function () {

            var formID = $('#formID').val();
            var formLembaga = $('#formLembaga').val();
            var formDescription = $('#formDescription').val();

            if(formLembaga!='' && formLembaga!=null &&
                formDescription!='' && formDescription!=null){

                loading_buttonSm('#btnSaveLembaga');
                var data = {
                    action : 'updateLembagaMitraKerjasama' ,
                    ID : (formID!='' && formID!=null) ? formID : '',
                    Lembaga : formLembaga,
                    Description : formDescription
                };

                var token = jwt_encode(data,'UAP)(*');
                var url = base_url_js+'api3/__crudAgregatorTB1';

                $.post(url,{token:token},function (jsonResult) {

                    if(jsonResult==0 || jsonResult=='0') { 
                        toastr.error('Maaf nama Lembaga sudah Ada!','Error');
                        $('#btnSaveLembaga').html('Save').prop('disabled',false);
                    } 
                    else {

                        $('#formID').val('');
                        $('#formLembaga').val('');
                        $('#formDescription').val('');
                        toastr.success('Data saved','Success');
                        loadDataLembagaMitra();
                        setTimeout(function () {
                            $('#btnSaveLembaga').html('Save').prop('disabled',false);
                        },500);

                    }

                });

            } else {
                toastr.warning('All form is required','Warning');
            }

        });

    });
    
    function loadDataLembagaMitra() {

        var url = base_url_js+'api3/__crudAgregatorTB1';
        var token = jwt_encode({action : 'readLembagaMitraKerjasama'},'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {
            $('#listLembaga,#formLembagaMitraID').empty();

            if(jsonResult.length>0){

                $.each(jsonResult,function (i,v) {
                    var no = i+1;
                    $('#listLembaga').append('<tr>' +
                        '<td>'+no+'</td>' +
                        '<td style="text-align: left;"><b>'+v.Lembaga+'</b><br/>'+v.Description+'</td>' +
                        //'<td><button class="btn btn-default btn-sm btnEditLV" data-no="'+no+'"><i class="fa fa-edit"></i></button>' +
                         '<td style="text-align: left;"><div class="btn-group btnAction"> ' +
                        '    <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ' +
                        '        <i class="fa fa-pencil"></i> <span class="caret"></span> '+
                        '    </button> '+
                        '    <ul class="dropdown-menu"> '+
                        '        <li><a class="btnEditLV" data-no="'+v.ID+'"  data-lembaga="'+v.Lembaga+'" data-desc="'+v.Description+'"> <i class="fa fa fa-edit"></i> Edit</a></li> '+
                        '        <li role="separator" class="divider"></li> '+
                        '        <li><a class="btnDeleteLV" data-id="'+v.ID+'"><i class="fa fa fa-trash"></i> Remove</a></li> '+
                        '    </ul> '+
                        '</div> </td>'+

                        //'<textarea id="btnEditLV_'+no+'" class="hide">'+JSON.stringify(v)+'</textarea></td>' +
                        '</tr>');

                    $('#formLembagaMitraID').append('<option value="'+v.ID+'">'+v.Lembaga+'</option>');
                });

            } else {
                $('#listLembaga').append('<tr><td colspan="3">-- Tidak ada data --</td></tr>');
            }

        });

    }

    $(document).on('click','.btnEditLV',function () {

        var ID = $(this).attr('data-no');
        var Lembaga = $(this).attr('data-lembaga');
        var Description = $(this).attr('data-desc');

        $('#formID').val(ID);
        $('#formLembaga').val(Lembaga);
        $('#formDescription').val(Description);

    });



    $(document).on('click','.btnDeleteLV',function () {
        
        if(confirm('Yakin Hapus data?')) {
    
            $('.btnDeleteLV').prop('disabled',true);
    
            var no = $(this).attr('data-id');
            var url = base_url_js+'api3/__crudAgregatorTB1';
    
            var data = {
                action: 'removeKerjasama',
                ID : no
            };
    
            var token = jwt_encode(data,'UAP)(*');
    
            $.post(url,{token:token},function (result) {
    
                $('#formID').val('');
                $('#formLembaga').val('');
                $('#formDescription').val('');

                toastr.success('Data saved','Success');
                loadDataLembagaMitra();

                setTimeout(function () {
                    //loadDataTable();
                },500);
    
           });
        }
    });

    function UploadFile(ID,FileNameOld) {

        var input = $('#upload_files');
        var files = input[0].files[0];

        var sz = parseFloat(files.size) / 1000000; // ukuran MB
        var ext = files.type.split('/')[1];

        if(Math.floor(sz)<=8){

            var fileName = moment().unix()+'_'+sessionNIP+'.'+ext;
            var formData = new FormData( $("#formupload_files")[0]);
            var url = base_url_js+'agregator/uploadFile?fileName='+fileName+'&old='+FileNameOld+'&&id='+ID;

            $.ajax({
                url : url,  // Controller URL
                type : 'POST',
                data : formData,
                async : false,
                cache : false,
                contentType : false,
                processData : false,
                success : function(data) {
                    toastr.success('Upload Success','Saved');
                    setTimeout(function () {
                        window.location.href = '';
                    },500);
                    // loadDataEmployees();

                }
            });

        }

    }


    $('#btnSave').click(function () {

        var file = $('#upload_files').val();

        var formID = $('#formID').val();
        var formLembagaMitraID = $('#formLembagaMitraID').val();
        var formTingkat = $('#formTingkat').val();
        var formBenefit = $('#formBenefit').val();
        var formDueDate = $('#formDueDate').datepicker("getDate");

        if(formLembagaMitraID!='' && formLembagaMitraID!=null &&
            formTingkat!='' && formTingkat!=null &&
            formBenefit!='' && formBenefit!=null &&
            formDueDate!='' && formDueDate!=null){

            var url = base_url_js+'api3/__crudAgregatorTB1';
            var data = {
              action : 'crudKPT',
                ID : (formID!='' && formID!=null) ? formID : '',
                dataForm : {
                    LembagaMitraID : formLembagaMitraID,
                    Tingkat : formTingkat,
                    Benefit : formBenefit,
                    DueDate : moment(formDueDate).format('YYYY-MM-DD')
                }
            };

            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {

                var ID = jsonResult.ID;
                var FileName = jsonResult.FileName;

                if(file!='' && file!=null){
                    UploadFile(ID,FileName);
                } else {
                    toastr.success('Data Saved','Success');
                    setTimeout(function () {
                        window.location.href = '';
                    },500);
                }


            });

        } else {
            toastr.warning('All form required','Warning');
        }

    });


    function loadDataTable() {

        $('#viewData').html('<table class="table table-striped table-bordered" id="tableData">' +
            '                    <thead>' +
            '                    <tr style="background: #20485A;color: #FFFFFF;">' +
            '                        <th style="width: 1%">No</th>' +
            '                        <th style="width: 25%">Lembaga Mitra Kerjasama</th>' +
            '                        <th style="width: 7%;">Tingkat</th>' +
            '                        <th>Bentuk Kegiatan / Manfaat</th>' +
            '                        <th style="width: 10%">Masa Berlaku</th>' +
            '                        <th style="width: 8%">Bukti Kerjasama</th>' +
            '                        <th style="width: 5%"><i class="fa fa-cog"></i></th>' +
            '                    </tr>' +
            '                    </thead>' +
            '                </table>');


        var token = jwt_encode({action:'viewListKPT',Previlege:act},'UAP)(*');
        var url = base_url_js+'api3/__crudAgregatorTB1';

        var dataTable = $('#tableData').DataTable( {
            "processing": true,
            "serverSide": true,
            "iDisplayLength" : 10,
            "ordering" : false,
            "language": {
                "searchPlaceholder": "Lembaga, Sertifikat, Lingkup, Tingkat"
            },
            "ajax":{
                url : url, // json datasource
                data : {token:token},
                ordering : false,
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                }
            }
        });

    }

    $(document).on('click','.btnEditAE',function () {

        var no = $(this).attr('data-no');
        var dataDetail = $('#viewDetail_'+no).val();
        dataDetail = JSON.parse(dataDetail);

        console.log(dataDetail);


        $('#formID').val(dataDetail.ID);

        $('#formLembagaMitraID').val(dataDetail.LembagaMitraID);
        $('#formTingkat').val(dataDetail.Tingkat);
        $('#formBenefit').val(dataDetail.Benefit);

        (dataDetail.DueDate!=='0000-00-00' && dataDetail.DueDate!==null)
            ? $('#formDueDate').datepicker('setDate',new Date(dataDetail.DueDate))
            : '';

        (dataDetail.File!=null && dataDetail.File!='')
        ?
        $('#viewFile').html('<iframe src="'+base_url_js+'uploads/agregator/'+dataDetail.File+'" width="100%;" height="200"></iframe>')
            : '';


    });



</script>