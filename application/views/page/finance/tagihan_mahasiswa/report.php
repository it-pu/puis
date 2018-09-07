<style type="text/css">
    table {
            border:solid #000 !important;
            border-width:1px 0 0 1px !important;
            font-size: 12px;
            }
        th, td {
          border:solid #000 !important;
          border-width:0 1px 1px 0 !important;
        }
</style>
<div class="row" style="margin-top: 30px;">
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectCurriculum">
                <option selected value = ''>--- All Curriculum ---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectProdi">
                <option selected value = ''>--- All Prodi---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
    <!-- <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectPTID">
                <option selected value = ''>--- All Payment Type ---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div> -->
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <input type="text" name="" class="form-control" placeholder="Input NPM Mahasiswa" id = "NIM">
        </div>
    </div>
</div>

<div class="col-md-12">
      <hr/>
      <div class="col-md-12">
        <div class="DTTT btn-group">
          <button type="button" class="btn btn-convert" id="export_excel"><i class="fa fa-download" aria-hidden="true"></i> Excel</button>
          <!--<a class="btn DTTT_button_pdf" id="ToolTables_DataTables_Table_0_1">
            <span><i class="fa fa-download" aria-hidden="true"></i> PDF
            </span>
          </a>-->
        </div>
        <div id="DataTables_Table_0_filter" class="dataTables_filter">
          <label>
            <div class="input-group">
              <div class="thumbnail" style="min-height: 30px;padding: 10px;">
                  <select class="form-control" id="selectSemester">
                  </select>
              </div>
            </div>
          </label>
        </div>
      </div>  
</div>                        
<br>
<br>
<br>
<br>
<div class="row" id='conTainJS'>
    <!--<div class="col-md-12">
        <hr/>
        <div align="right">
            <div class="btn-group">
                <button type="button" class="btn btn-convert">
                  <i class="fa fa-download" aria-hidden="true"></i> Excel
                </button>
                <button type="button" class="btn btn-convert">
                  <i class="fa fa-download" aria-hidden="true"></i> PDF
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered datatable2 " id = "datatable2">
            <thead>
            <tr style="background: #333;color: #fff;">
                <th style="width: 12%;">Program Study</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 5%;">Payment Type</th>
                <th style="width: 5%;">BilingID</th>
                <th style="width: 10%;">Invoice</th>
                <th style="width: 5%;">Status</th>
                <th style="width: 20%;">Ket</th>
                <th style="width: 5%;">Cetak Faktur</th>
            </tr>
            </thead>
            <tbody id="dataRow"></tbody>
        </table>
    </div>
    <div  class="col-xs-12" align="right" id="pagination_link"></div>-->
</div>

<script type="text/javascript">
    window.dataa = '';
    $(document).ready(function () {
        loadSelectOptionCurriculum('#selectCurriculum','');
        loadSelectOptionBaseProdi('#selectProdi','');
        loadSelectOptionPaymentTypeAll('#selectPTID','');
        loadSelectOptionSemesterByload('#selectSemester',0);
        getReloadTableSocket();
    });

    function loadSelectOptionSemesterByload(element,selected) {

        var token = jwt_encode({action:'read'},'UAP)(*');
        var url = base_url_js+'api/__crudTahunAkademik';
        $.post(url,{token:token},function (jsonResult) {

           if(jsonResult.length>0){
               for(var i=0;i<jsonResult.length;i++){
                   var dt = jsonResult[i];
                   var sc = (selected==dt.ID) ? 'selected' : '';
                   // var v = (option=="Name") ? dt.Name : dt.ID;
                   $(element).append('<option value="'+dt.ID+'.'+dt.Name+'" '+sc+'>'+dt.Name+'</option>');
               }
           }
           loadData(1);
        });

    }

    $('#selectCurriculum').change(function () {
        loadData(1);
    });

    $('#selectProdi').change(function () {
        loadData(1);
    });

    $('#selectPTID').change(function () {
        loadData(1);
    });

    $('#selectSemester').change(function () {
        loadData(1);
    });

    $(document).on('keypress','#NIM', function (event)
     {

         if (event.keyCode == 10 || event.keyCode == 13) {
           loadData(1);
         }
    }); // exit enter

    function submit(action, method, values) {
        var form = $('<form/>', {
            action: action,
            method: method
        });
        $.each(values, function() {
            form.append($('<input/>', {
                type: 'hidden',
                name: this.name,
                value: this.value
            }));    
        });
        form.attr('target', '_blank');
        form.appendTo('body').submit();
    }

    $(document).on("click", "#export_excel", function(event){
      /*loading_button('#export_excel');
      $('#NotificationModal .modal-header').addClass('hide');
      $('#NotificationModal .modal-body').html('<center>' +
          '                    <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>' +
          '                    <br/>' +
          '                    Loading Data . . .' +
          '                </center>');
      $('#NotificationModal .modal-footer').addClass('hide');
      $('#NotificationModal').modal({
          'backdrop' : 'static',
          'show' : true
      });*/
      var url = base_url_js+'finance/export_excel_report';
      data = {
        Data : dataa,
        Semester : $('#selectSemester').val(),
      }
      var token = jwt_encode(data,"UAP)(*");
      submit(url, 'POST', [
          { name: 'token', value: token },
      ]);
      //window.open(url+'/'+token,'_blank');
     

          /*$.post(url,{token:token},function (data_json) {
              var response = jQuery.parseJSON(data_json);
              //console.log(response);
              //window.location.href = base_url_js+'fileGet/'+response;
              window.open(base_url_js+'download/'+response,'_blank');
          }).done(function() {
            // loadTableEvent(loadDataEvent);
          }).fail(function() {
            toastr.error('The Database connection error, please try again', 'Failed!!');
          }).always(function() {
           $('#export_excel').prop('disabled',false).html('<i class="fa fa-download" aria-hidden="true"></i> Excel');

          });*/

    });

    $(document).on("click", ".btn-print", function(event){
      var npm = $(this).attr('npm');
      var semester = $(this).attr('semester');
      var ptid = $(this).attr('ptid');
      var va = $(this).attr('va');
      var bilingid = $(this).attr('bilingid');
      var invoice = $(this).attr('invoice');
      var nama = $(this).attr('nama');
      var prodi = $(this).attr('prodi');
      var Time = $(this).attr('Time');

      var data = {
         npm : npm,
         semester : semester,
         ptid : ptid,
         va : va,
         bilingid : bilingid,
         invoice : invoice,
         nama : nama,
         prodi : prodi,
         Time : Time,
       };
       var token = jwt_encode(data,"UAP)(*");
      window.open(base_url_js+'save2pdf/getpdfkwitansi/'+token,'_blank');

    });

    $(document).on("click", ".pagination li a", function(event){
      event.preventDefault();
      var page = $(this).attr("data-ci-pagination-page");
      if (page == null){
          page = 1;
      }
      loadData(page);
      // loadData_register_document(page);
    });

    function loadData(page) {
        loading_page('#conTainJS');
        dataa = '';

        // get proses data dari database
        var ta = $('#selectCurriculum').val();
        var prodi = $('#selectProdi').val();
        // var PTID = $('#selectPTID').val();
        var NIM = $('#NIM').val().trim();
        try{
          var Semester = $('#selectSemester').val();
          // console.log(Semester);
          Semester = Semester.split('.');
          Semester = Semester[0];
        }
        catch(e)
        {

        }

        var url = base_url_js+'finance/get_reporting/'+page;
        var data = {
            ta : ta,
            prodi : prodi,
            // PTID  : PTID,
            NIM : NIM,
            Semester : Semester,
        };
        var token = jwt_encode(data,'UAP)(*');
        var htmlDy = '<div class="col-md-12">'+
                        '<table class="table table-bordered datatable2 " id = "datatable2">'+
                            '<thead>'+
                            '<tr style="background: #333;color: #fff;">'+
                            '    <th style="width: 1%;" rowspan = "2">No</th>'+
                            '    <th style="width: 25%;" rowspan = "2">Nama & NPM</th>'+
                            '    <th style="width: 5%;" rowspan = "2">Jurusan</th>'+
                            '    <th style="width: 20%;text-align:center" rowspan = "1" colspan = "2">Tagihan</th>'+
                            '    <th style="width: 10%;" rowspan = "2">Total Tagihan</th>'+
                            '    <th style="width: 10%;" rowspan = "2">Total Pembayaran</th>'+
                            '    <th style="width: 10%;" rowspan = "2">Piutang</th>'+
                            '    <th rowspan = "2">Keterangan</th>'+
                            '</tr>'+
                            '<tr style="background: #333;color: #fff;">'+
                              '<th>BPP</th>'+
                              '<th>Credit</th>'+
                            '</tr>'+  
                            '</thead>'+
                            '<tbody id="dataRow"></tbody>'+
                        '</table>'+
                    '</div>'+
                    '<div  class="col-xs-12" align="right" id="pagination_link"></div>';
   
        $.post(url,{token:token},function (resultJson) {
            var resultJson = jQuery.parseJSON(resultJson);
            // console.log(resultJson);
            var Data_mhs = resultJson.loadtable;
            dataa = Data_mhs;
            setTimeout(function () {
                $("#conTainJS").html(htmlDy);
                $("#pagination_link").html(resultJson.pagination_link);
                if (Data_mhs.length > 0) {
                        for (var i = 0; i < Data_mhs.length; i++) {
                            var  no = parseInt(i) + 1;
                            // var timee = (Data_mhs[i]['Time'] == null) ? '-' : Data_mhs[i]['Time'];
                            // var yy = (Data_mhs[i]['InvoiceStudents'] != '') ? formatRupiah(Data_mhs[i]['InvoiceStudents']) : '-';
                            // var btnPrint = '<span data-smt="'+Data_mhs[i]['ID_payment_students']+'" class="btn btn-xs btn-print" NPM = "'+Data_mhs[i]['NPM']+'" Semester = "'+Data_mhs[i]['SemesterName']+'" PTID = "'+Data_mhs[i]['PTIDDesc']+'" VA = "'+Data_mhs[i]['VA']+'" BilingID = "'+Data_mhs[i]['BilingID']+'" Invoice = "'+yy+'" Nama = "'+Data_mhs[i]['Nama']+'"  Prodi = "'+Data_mhs[i]['ProdiEng']+'" Time = "'+timee+'"><i class="fa fa-print"></i> Print</span>';
                            var Total_tagihan = parseInt(Data_mhs[i]['BPP'])  + parseInt(Data_mhs[i]['SPP']) ;
                            var Total_pembayaran = parseInt(Data_mhs[i]['PayBPP'])  + parseInt(Data_mhs[i]['PaySPP']) ;
                            var Piutang = parseInt(Data_mhs[i]['SisaSPP'])  + parseInt(Data_mhs[i]['SisaBPP']) ;
                            var keterangan = '';
                            var keteranganBPP = '';
                            var keteranganSPP = '';

                            if (Piutang > 0) {
                              if(Data_mhs[i]['DetailPaymentBPP'] != '')
                              {
                                var DetailPaymentBPP = Data_mhs[i]['DetailPaymentBPP'];
                                keteranganBPP = '<ul>BPP';
                                for (var l = 0; l < DetailPaymentBPP.length; l++) {
                                  var lno = parseInt(l) + 1;
                                  var StatusPay = (DetailPaymentBPP[l]['Status'] == 1)? 'Sudah Bayar' : 'Belum Bayar';
                                  if (DetailPaymentBPP[l]['Status'] == 0) {
                                    keteranganBPP += '<li>Pembayaran : '+lno+'</li>';
                                    keteranganBPP += '<li>Deadline : '+DetailPaymentBPP[l]['Deadline']+'</li>';
                                    keteranganBPP += '<li>Status : '+StatusPay+'</li>';
                                  }
                                  
                                }
                                  keteranganBPP += '</ul>';
                              }

                              if(Data_mhs[i]['DetailPaymentSPP'] != '')
                              {
                                var DetailPaymentSPP = Data_mhs[i]['DetailPaymentSPP'];
                                keteranganSPP = '<ul>SPP';
                                for (var l = 0; l < DetailPaymentSPP.length; l++) {
                                  var lno = parseInt(l) + 1;
                                  var StatusPay = (DetailPaymentSPP[l]['Status'] == 1)? 'Sudah Bayar' : 'Belum Bayar';
                                  if(DetailPaymentSPP[l]['Status'] == 0)
                                  {
                                    keteranganSPP += '<li>Pembayaran : '+lno+'</li>';
                                    keteranganSPP += '<li>Deadline : '+DetailPaymentSPP[l]['Deadline']+'</li>';
                                    keteranganSPP += '<li>Status : '+StatusPay+'</li>';
                                  }
                                  
                                }
                                  keteranganSPP += '</ul>';

                              }
                            }

                            keterangan = keteranganBPP + keteranganSPP;

                            $('#dataRow').append('<tr>' +
                                '<td>'+no+'</td>' +
                                '<td>'+Data_mhs[i]['Name']+'<br>'+Data_mhs[i]['NPM']+'<br>'+'</td>' +
                                '<td>'+Data_mhs[i]['ProdiENG']+'</td>' +
                                '<td>'+formatRupiah(Data_mhs[i]['BPP'])+'</td>' +
                                '<td>'+formatRupiah(Data_mhs[i]['SPP'])+'</td>' +
                                '<td>'+formatRupiah(Total_tagihan)+'</td>' +
                                '<td>'+formatRupiah(Total_pembayaran)+'</td>' +
                                '<td>'+formatRupiah(Piutang)+'</td>' +
                                '<td>'+keterangan+'</td>' +
                                '</tr>');
                        }
                        $('#NIM').focus();
                } else {
                    $('#dataRow').append('<tr><td colspan="8" align = "center">No Result Data</td></tr>');
                }
            },500);    
        }).fail(function() {
          toastr.info('No Result Data'); 
          // toastr.error('The Database connection error, please try again', 'Failed!!');
        }).always(function() {
            // $('#NotificationModal').modal('hide');
        });
    }

    function getReloadTableSocket()
    {
      var socket = io.connect( 'http://'+window.location.hostname+':3000' );
      // var socket = io.connect( '<?php echo serverRoot ?>'+':3000' );

      socket.on( 'update_notifikasi', function( data ) {

          //$( "#new_count_message" ).html( data.new_count_message );
          //$('#notif_audio')[0].play();
          if (data.update_notifikasi == 1) {
              // action
              loadData(1);
          }

      }); // exit socket
    }    
</script>