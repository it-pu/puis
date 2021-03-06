

<style>
    #dataTablesPAM tr th, #dataTablesPAM tr td {
        text-align: center;
    }
</style>

<div class="well">

    <div class="row">

        <div class="col-md-3 form-data-edit" style="border-right: 1px solid #CCCCCC;">

            <div class="form-group">
                <label>Program Pendidikan</label>
                <input class="hide" id="formID">
                <select class="form-control" id="programpendidikan"></select>
            </div>

            <div class="form-group">
                <label>Tahun</label>
                <select class="form-control" id="yearstudy"></select>
            </div>

            <div class="form-group">
                <label>Persentase Kesesuaian Bidang Kerja</label>
                <input class="form-control" id="persentase">
            </div>

            <div class="form-group" style="text-align: right;">
                <button class="btn btn-primary" id="btnSaveKBKL">Save</button>
            </div>

        </div>
        <div class="col-md-9">
            <div id="viewTable"></div>
        </div>

    </div>

</div>

<script>

    $(document).ready(function () {

        window.act = "<?= $accessUser; ?>";
        if(parseInt(act)<=0){
            $('.form-data-edit').remove();
        } else {

        }

        loadDataKBKL();
        selectprogrampendidikan();
        selectyearstudy();

        $( "#formWaktuPenyelenggaraan" )
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
    });

    function selectprogrampendidikan() {

        var url = base_url_js+'api3/__crudAgregatorTB5';
        var token = jwt_encode({action : 'getprogrampendik'},'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {
            $('#programpendidikan').append('<option disabled selected></option>');
                for(var i=0;i<jsonResult.length;i++){
                   $('#programpendidikan').append('<option id="'+jsonResult[i].ID+'"> '+jsonResult[i].NamaProgramPendidikan+' </option>');
                }
            });
      }

    function selectyearstudy() {

        var url = base_url_js+'api3/__crudAgregatorTB5';
        var token = jwt_encode({action : 'yearstudy'},'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {
            $('#yearstudy').append('<option disabled selected></option>');
                for(var i=0;i<jsonResult.length;i++){
                   $('#yearstudy').append('<option id="'+jsonResult[i].Year+'"> '+jsonResult[i].Year+' </option>');
                }
            });
      }


    $('#btnSaveKBKL').click(function () {

        var formID = $('#formID').val();
        var programpendidikan = $('#programpendidikan option:selected').attr('id');
        var yearstudy = $('#yearstudy option:selected').attr('id');
        var persentase = $('#persentase').val();


        if(programpendidikan!='' && programpendidikan!=null &&
        	yearstudy!='' && yearstudy!=null &&
        	persentase!='' && persentase!=null) {

            //loading_buttonSm('#btnSaveKBKL');

            var data = {
                action : 'saveKBKL',
                //ID : (formID!='' && formID!=null) ? formID : '',
                dataForm : {
                    ID_programpendik : programpendidikan,
                    Year : yearstudy,
                    Persentase : persentase
                }
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api3/__crudAgregatorTB5';

            $.post(url,{token:token},function (result) {

            	if(result==0 || result=='0'){
                  //toastr.error('Maaf, Data Sudah Ada !','Error');
                  $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Data sudah ada! Apakah Data Mau di Update ? </b><hr/> ' +
		            '<button type="button" class="btn btn-primary btnActionUpdate" style="margin-right: 5px;" programpendidikan="'+programpendidikan+'" yearstudy="'+yearstudy+'" jumlah_lulusan="'+persentase+'" >Yes</button>' +
		            '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
		            '</div>');
		          $('#NotificationModal').modal('show');
                }
                else {
                	loadDataKBKL();
	                toastr.success('Data saved','Success');
	                $('#programpendidikan').val('');
	                $('#persentase').val('');
                }

                setTimeout(function (args) {
                    $('#btnSaveKBKL').html('Save').prop('disabled',false);
                },500);
            });

        } else {
            toastr.error('All form required','Error');
        }

    });

    function loadDataKBKL() {

        var thisYear = (new Date()).getFullYear();
		var startTahun = parseInt(thisYear) - parseInt(3);
		var selisih =  parseInt(thisYear) - parseInt(startTahun);

		var arr_years =[];
		for (var i = 0; i < 3; i++) {
			var y = parseInt(thisYear) - parseInt(i);
			arr_years.push(y);
		}

		var thYear = '';
		for (var i = 0; i < arr_years.length; i++) {
			thYear += '<th>'+arr_years[i]+'</th>';
		}

        $('#viewTable').html(' <table class="table" id="dataTablesPAM">' +
            '                <thead>' +
			'                <tr>    ' +
			'                    <th colspan="2" style="border-right: 1px solid #ccc;"></th> ' +
			'                    <th colspan="3" style="border-right: 1px solid #ccc;">Persentase Kesesuaian Bidang Kerja (%)</th>  ' +
			'                </tr>  ' +
            '                <tr>' +
            '                    <th style="width: 1%;">No</th>' +
            '                    <th>Program Pendidikan </th>' +
            					thYear+
            '                </tr>' +
            '                </thead>' +
            '                <tbody id="listData"></tbody>' +
            '            </table>');

        var data = {
            action : 'viewKesesuaian',
            Type : '1'
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api3/__crudAgregatorTB5';

        $.post(url,{token:token},function (jsonResult) {
        	console.log(jsonResult);
            if(jsonResult.length>0){

            		for (var i = 0; i < jsonResult.length; i++) {
            			var persentase = '';
            			var v = jsonResult[i];
            			var v_ID_programpendik = v.ID_programpendik;

					    var btn = '<div class="btn-group">' +
	                        '  <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
	                        '    <i class="fa fa-edit"></i> <span class="caret"></span>' +
	                        '  </button>' +
	                        '  <ul class="dropdown-menu">' +
	                        '    <li><a href="javascript:void(0);" data-id="'+v_ID_programpendik+'" class="btnEditMAP">Edit</a></li>' +
	                        '    <li role="separator" class="divider"></li>' +
	                        '    <li><a href="javascript:void(0);" data-id="'+v_ID_programpendik+'" class="btnRemoveMAP">Remove</a></li>' +
	                        '  </ul>' +
	                        '</div>' +
	                        '<textarea id="viewData_'+v_ID_programpendik+'" class="hide">'+JSON.stringify(v)+'</textarea>';

	                    for (var l = 0; l < arr_years.length; l++) {
	                    	persentase += '<td>'+v['Persentase_'+arr_years[l]]+'</td>';
	                    }

					 $('#listData').append('<tr>' +
                        '<td style="border-right: 1px solid #ccc;">'+(parseInt(i)+1 )+'</td>' +
                        '<td style="text-align: left;">'+v.NamaProgramPendidikan+'</td>' +
                        persentase+
                        //'<td style="border-left: 1px solid #ccc;">'+btn+'</td>' +
                        '</tr>');
            		}
            }
            // $('#dataTablesPAM').dataTable();
        });
    }



    $(document).on('click','.btnEditMAP',function () {

        var ID = $(this).attr('data-id');
        var viewData = $('#viewData_'+ID).val();
        var d = JSON.parse(viewData);


        $('#formID').val(d.ID);
        $('#formKegiatan').val(d.Kegiatan);

        $('#formTingkat').val(d.Tingkat);
        $('#formPrestasi').val(d.Prestasi);

        $('#formWaktuPenyelenggaraan').datepicker('setDate',new Date(d.WaktuPenyelenggaraan));

    });

    $(document).on('click','.btnRemoveMAP',function () {

        if(confirm('Hapus data?')){
            var ID = $(this).attr('data-id');
            var data = {
                action : 'removePAM',
                ID : ID
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api3/__crudAgregatorTB5';

            $.post(url,{token:token},function (jsonResult) {

                loadDataKBKL();
                toastr.success('Data removed','Success');

            });

        }
    });

</script>
