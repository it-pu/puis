
<style type="text/css">
  .btn-custom {
    background-color: hsl(86, 79%, 44%) !important;
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#daf6b5", endColorstr="#7cc817");
    background-image: -khtml-gradient(linear, left top, left bottom, from(#daf6b5), to(#7cc817));
    background-image: -moz-linear-gradient(top, #daf6b5, #7cc817);
    background-image: -ms-linear-gradient(top, #daf6b5, #7cc817);
    background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #daf6b5), color-stop(100%, #7cc817));
    background-image: -webkit-linear-gradient(top, #daf6b5, #7cc817);
    background-image: -o-linear-gradient(top, #daf6b5, #7cc817);
    background-image: linear-gradient(#daf6b5, #7cc817);
    border-color: #7cc817 #7cc817 hsl(86, 79%, 34%);
    color: #333 !important;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.66);
    -webkit-font-smoothing: antialiased;
  }

  .btn-unapprove { background-color: hsl(41, 85%, 35%) !important; background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#efb73d", endColorstr="#a5750d"); background-image: -khtml-gradient(linear, left top, left bottom, from(#efb73d), to(#a5750d)); background-image: -moz-linear-gradient(top, #efb73d, #a5750d); background-image: -ms-linear-gradient(top, #efb73d, #a5750d); background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #efb73d), color-stop(100%, #a5750d)); background-image: -webkit-linear-gradient(top, #efb73d, #a5750d); background-image: -o-linear-gradient(top, #efb73d, #a5750d); background-image: linear-gradient(#efb73d, #a5750d); border-color: #a5750d #a5750d hsl(41, 85%, 29%); color: #fff !important; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.39); -webkit-font-smoothing: antialiased; }

</style>
<div class="row">
    <div class="col-xs-12" >
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Daftar Tagihan Mahasiswa</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="well">
                      <h5>Search</h5>
                      <div class="row">
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
                        <div class="col-md-3">
                            <div class="thumbnail" style="min-height: 30px;padding: 10px;">
                                <select class="form-control" id="selectPTID">
                                    <option selected value = ''>--- All Payment Type ---</option>
                                    <option disabled>------</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="thumbnail" style="min-height: 30px;padding: 10px;">
                                <input type="text" name="" class="form-control" placeholder="Input NPM Mahasiswa" id = "NIM" value="<?php echo $NPM ?>">
                            </div>
                        </div>
                        <div class="col-md-3" style="margin-top: 10px">
                          <div class="thumbnail" style="min-height: 30px;padding: 10px;">
                              <select class="form-control" id="selectSemester">
                              </select>
                          </div>
                        </div>
                        <div class="col-md-3" style="margin-top: 10px">
                          <div class="thumbnail" style="min-height: 30px;padding: 10px;">
                              <select class="form-control" id="selectStatusPayment">
                                <option value="">All</option>
                                <option value="0">Belum lunas</option>
                                <option value="1">Lunas</option>
                              </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> 

                <br>
                <div class="thumbnail" style="padding: 10px;">
                    <b>Status : </b><i class="fa fa-circle" style="color:#8ED6EA;"></i> Approve & Paid Off | <i class="fa fa-circle" style="color: #eade8e;"></i> Approve & Not Yet Paid Off  
                </div>
                <div class="row">
                  <div class="col-xs-3" id = "ShowTotalData" style="margin-top: 45px;"></div>
                  <div  class="col-xs-6 col-md-offset-3" align="right" id="pagination_link"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table table-bordered datatable2 hide" id = "datatable2">
                              <thead>
                              <tr style="background: #333;color: #fff;">
                                  <th style="width: 2%;"><input type="checkbox" class="uniform" value="nothing" id ="dataResultCheckAll"></th>
                                  <th style="width: 8%;">Program Study</th>
                                  <!-- <th style="width: 10%;">Semester</th> -->
                                  <th style="width: 20%;">Nama,NPM &  VA</th>
                                  <!-- <th style="width: 5%;">NPM</th> -->
                                  <!-- <th style="width: 5%;">Year</th> -->
                                  <th style="width: 8%;">Payment Type</th>
                                  <!-- <th style="width: 15%;">Email PU</th> -->
                                  <th style="width: 5%;">Credit</th>
                                  <th style="width: 5%;">IPS</th>
                                  <th style="width: 5%;">IPK</th>
                                  <th style="width: 5%;">Discount</th>
                                  <th style="width: 10%;">Total Invoice</th>
                                  <th style="width: 10%;">Cicilan</th>
                                  <th style="width: 15%;">Status</th>
                                  <th style="width: 20%;">Detail Payment</th>
                              </tr>
                              </thead>
                              <tbody id="dataRow"></tbody>
                          </table>
                        </div>
                    </div>
                    <div  class="col-xs-12" align="right"><button class="btn btn-inverse btn-notification btn-unapprove hide" id="btn-submit-unapprove"><i class="fa fa-times" aria-hidden="true"></i>
                 Unapprove</button>&nbsp<button class="btn btn-inverse btn-notification btn-custom btn-submit hide" id="btn-submit"> <i class="fa fa-check" aria-hidden="true"></i>
                 Approve</button></div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    window.dataa = '';
    window.dataaModal = '';
    $(document).ready(function () {
        // loadData(1);
        loadSelectOptionCurriculum2('#selectCurriculum','');
        loadSelectOptionBaseProdi('#selectProdi','');
        loadSelectOptionPaymentTypeAll('#selectPTID','');
        loadSelectOptionSemesterByload('#selectSemester',1);
        getReloadTableSocket();
        // $("#btn-submit").addClass('hide');
        function loadSelectOptionSemesterByload(element,selected) {

            var token = jwt_encode({action:'read'},'UAP)(*');
            var url = base_url_js+'api/__crudTahunAkademik';
            $.post(url,{token:token},function (jsonResult) {

               if(jsonResult.length>0){
                   for(var i=0;i<jsonResult.length;i++){
                       var dt = jsonResult[i];
                       var sc = (selected==dt.Status) ? 'selected' : '';
                       // var v = (option=="Name") ? dt.Name : dt.ID;
                       $(element).append('<option value="'+dt.ID+'.'+dt.Name+'" '+sc+'>'+dt.Name+'</option>');
                   }
               }
               loadData(1);
            });

        }
    });

    $('#selectCurriculum').change(function () {
        loadData(1);
    });

    $('#selectSemester').change(function () {
        loadData(1);
    });

    $('#selectProdi').change(function () {
        loadData(1);
    });

    $('#selectPTID').change(function () {
        loadData(1);
    });

    $('#selectStatusPayment').change(function () {
        loadData(1);
    });

    $(document).on('keypress','#NIM', function (event)
    {

        if (event.keyCode == 10 || event.keyCode == 13) {
          loadData(1);
        }
   }); // exit enter

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
        $("#btn-submit").addClass('hide');
        $("#btn-submit-unapprove").addClass('hide');
        $("#datatable2").addClass('hide');

        $('#dataResultCheckAll').prop('checked', false); // Unchecks it
        $("span").removeClass('checked');

        var ta = $('#selectCurriculum').val();
        var prodi = $('#selectProdi').val();
        var PTID = $('#selectPTID').val();
        var NIM = $('#NIM').val().trim();
        var Semester = $('#selectSemester').val();
        var StatusPayment = $('#selectStatusPayment').val();
        Semester = Semester.split('.');
        Semester = Semester[0];
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
            });
            $('#dataRow').html('');
            var url = base_url_js+'finance/get_created_tagihan_mhs/'+page;
            var data = {
                ta : ta,
                prodi : prodi,
                PTID  : PTID,
                NIM : NIM,
                Semester : Semester,
                StatusPayment : StatusPayment,
            };
            // console.log(data);return;
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               var resultJson = jQuery.parseJSON(resultJson);
               console.log(resultJson);
                var Data_mhs = resultJson.loadtable;
                data = Data_mhs;
                dataaModal = Data_mhs;
                $('#ShowTotalData').html('<p><b style = "font-size: 14px;">Total Data : '+resultJson.totaldata+' </b></p>');
               for(var i=0;i<Data_mhs.length;i++){
                    var ccc = 0;
                    var yy = (Data_mhs[i]['InvoicePayment'] != '') ? formatRupiah(Data_mhs[i]['InvoicePayment']) : '-';
                    // proses status
                    var status = '';

                    var b = 0;
                    var cicilan = 0;
                    var bayar = 0;
                    var htmlCicilan = '';

                    if(Data_mhs[i]['StatusPayment'] == 0)
                    {
                      status = 'Belum Approve';
                      for (var j = 0; j < Data_mhs[i]['DetailPayment'].length; j++) {
                        var a = Data_mhs[i]['DetailPayment'][j]['Status'];
                        if(a== 1)
                        {
                          b = parseInt(b) + parseInt(Data_mhs[i]['DetailPayment'][j]['Invoice']);
                          bayar = bayar + 1;
                        }
                        cicilan = cicilan + 1;
                      }

                      if(b < Data_mhs[i]['InvoicePayment'])
                      {
                        status += '<br> Belum Lunas';
                        // ccc = 1;
                      }
                      else
                      {
                        status += '<br> Lunas';
                        // ccc = 2
                      }

                      if(cicilan == 1)
                      {
                        htmlCicilan = "Tidak Cicilan";
                      }
                      else
                      {
                        for (var k = 1; k <= cicilan; k++) {
                          var bayarStatus = (k > bayar) ? '<i class="fa fa-minus-circle" style="color: red;"></i>' : '<i class="fa fa-check-circle" style="color: green;"></i>';
                          htmlCicilan += '<p> '+k+ ': '+bayarStatus+'</p>';
                        }
                      }
                    }
                    else
                    {
                      status = 'Approve';
                      // check lunas atau tidak
                        // count jumlah pembayaran dengan status 1
                        for (var j = 0; j < Data_mhs[i]['DetailPayment'].length; j++) {
                          var a = Data_mhs[i]['DetailPayment'][j]['Status'];
                          if(a== 1)
                          {
                            b = parseInt(b) + parseInt(Data_mhs[i]['DetailPayment'][j]['Invoice']);
                            bayar = bayar + 1;
                          }
                          cicilan = cicilan + 1;
                        }
                        // console.log(cicilan);


                        if(cicilan == 1)
                        {
                          htmlCicilan = "Tidak Cicilan";
                        }
                        else
                        {
                          for (var k = 1; k <= cicilan; k++) {
                            var bayarStatus = (k > bayar) ? '<i class="fa fa-minus-circle" style="color: red;"></i>' : '<i class="fa fa-check-circle" style="color: green;"></i>';
                            htmlCicilan += '<p>Cicilan ke '+k+ ': '+bayarStatus+'</p>';
                          }
                        }

                        // console.log('b : '+b+ '  ..InvoicePayment : ' + Data_mhs[i]['InvoicePayment']);
                        if(b < Data_mhs[i]['InvoicePayment'])
                        {
                          status += '<br> Belum Lunas';
                          ccc = 1;
                        }
                        else
                        {
                          status += '<br> Lunas';
                          ccc = 2
                        }
                    }

                   var tr = '<tr NPM = "'+Data_mhs[i]['NPM']+'">';
                   var inputCHK = ''; 
                   if (ccc == 0) {
                    tr = '<tr NPM = "'+Data_mhs[i]['NPM']+'">';
                    inputCHK = '<input type="checkbox" class="uniform" value ="'+Data_mhs[i]['NPM']+'" Prodi = "'+Data_mhs[i]['ProdiEng']+'" Nama ="'+Data_mhs[i]['Nama']+'" semester = "'+Data_mhs[i]['SemesterID']+'" ta = "'+Data_mhs[i]['Year']+'" invoice = "'+Data_mhs[i]['InvoicePayment']+'" discount = "'+Data_mhs[i]['Discount']+'" PTID = "'+Data_mhs[i]['PTID']+'" PTName = "'+Data_mhs[i]['PTIDDesc']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'" Status = "'+ccc+'">'; 
                   } else if(ccc == 1) {
                      tr = '<tr style="background-color: #eade8e; color: black;" NPM = "'+Data_mhs[i]['NPM']+'">';
                      inputCHK = '<input type="checkbox" class="uniform" value ="'+Data_mhs[i]['NPM']+'" Prodi = "'+Data_mhs[i]['ProdiEng']+'" Nama ="'+Data_mhs[i]['Nama']+'" semester = "'+Data_mhs[i]['SemesterID']+'" ta = "'+Data_mhs[i]['Year']+'" invoice = "'+Data_mhs[i]['InvoicePayment']+'" discount = "'+Data_mhs[i]['Discount']+'" PTID = "'+Data_mhs[i]['PTID']+'" PTName = "'+Data_mhs[i]['PTIDDesc']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'" Status = "'+ccc+'">'; 
                   }
                   else
                   {
                    tr = '<tr style="background-color: #8ED6EA; color: black;" NPM = "'+Data_mhs[i]['NPM']+'">';
                    inputCHK = '';
                    <?php if ($this->session->userdata('finance_auth_Policy_SYS') == 0): ?>
                       inputCHK = '<input type="checkbox" class="uniform" value ="'+Data_mhs[i]['NPM']+'" Prodi = "'+Data_mhs[i]['ProdiEng']+'" Nama ="'+Data_mhs[i]['Nama']+'" semester = "'+Data_mhs[i]['SemesterID']+'" ta = "'+Data_mhs[i]['Year']+'" invoice = "'+Data_mhs[i]['InvoicePayment']+'" discount = "'+Data_mhs[i]['Discount']+'" PTID = "'+Data_mhs[i]['PTID']+'" PTName = "'+Data_mhs[i]['PTIDDesc']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'" Status = "'+ccc+'">'; 
                     <?php endif ?> 
                   } 

                   // show to fixed
                   var IPS = 0;
                   var IPK = 0;
                   try {
                       IPS = Data_mhs[i]['IPS'].toFixed(2);
                       IPK = Data_mhs[i]['IPK'].toFixed(2);
                   }
                   catch(err) {
                       IPS = 0;
                       IPK = 0;
                   }

                   // show bintang
                   var bintang = (Data_mhs[i]['Pay_Cond'] == 1) ? '<p style="color: red;">*</p>' : '<p style="color: red;">**</p>'; 

                   var btn_edit = '';
                   var btn_view = '<button class = "btn btn-default DetailPayment" NPM = "'+Data_mhs[i]['NPM']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'"><i class="fa fa-search" aria-hidden="true"></i> View</button>';
                   <?php if ($this->session->userdata('finance_auth_Policy_SYS') == 0): ?>
                     btn_edit = '<br><br> <button class = "btn btn-default edit" NPM = "'+Data_mhs[i]['NPM']+'" semester = "'+Data_mhs[i]['SemesterID']+'" PTID = "'+Data_mhs[i]['PTID']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
                     var btn_view = '<button class = " btn btn-primary DetailPayment" NPM = "'+Data_mhs[i]['NPM']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'">Pembayaran Manual </button>';
                   <?php endif ?>

                   $('#dataRow').append(tr +
                       '<td>'+inputCHK+'</td>' +
                       '<td>'+Data_mhs[i]['ProdiEng']+'<br>'+Data_mhs[i]['SemesterName']+'</td>' +
                       // '<td>'+Data_mhs[i]['SemesterName']+'</td>' +
                       '<td>'+bintang+Data_mhs[i]['Nama']+'<br>'+Data_mhs[i]['NPM']+'<br>'+Data_mhs[i]['VA']+'</td>' +
                       // '<td>'+Data_mhs[i]['NPM']+'</td>' +
                       // '<td>'+Data_mhs[i]['Year']+'</td>' +
                       '<td>'+Data_mhs[i]['PTIDDesc']+'</td>' +
                       '<td>'+Data_mhs[i]['Credit']+'</td>' +
                       // '<td>'+Data_mhs[i]['EmailPU']+'</td>' +
                       '<td>'+IPS+'</td>' +
                       '<td>'+IPK+'</td>' +
                       '<td>'+Data_mhs[i]['Discount']+'%</td>' +
                       '<td>'+yy+'</td>' +
                       '<td>'+htmlCicilan+'</td>'+
                       '<td>'+status+'</td>' +
                       '<td>'+btn_view+btn_edit+'</td>' +
                       '</tr>');
               }

               if(Data_mhs.length > 0)
               {
                $('#btn-submit').removeClass('hide');
                $("#btn-submit-unapprove").removeClass('hide');
                $('#datatable2').removeClass('hide');
                $("#pagination_link").html(resultJson.pagination_link);
               }
               
            }).fail(function() {
              
              toastr.info('No Result Data'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#NotificationModal').modal('hide');
            });
            $('#NIM').focus();
    }

    $(document).on('click','#dataResultCheckAll', function () {
        $('input.uniform').not(this).prop('checked', this.checked);
    });

    $(document).on('click','.edit', function () {
        var PaymentID = $(this).attr('PaymentID');
        var NPM = $(this).attr('NPM');
        var semester = $(this).attr('semester');
        var PTID = $(this).attr('ptid');
        var data = {
            PaymentID : PaymentID,
            NPM : NPM,
            semester  : semester,
            PTID : PTID,
        };
        var token = jwt_encode(data,'UAP)(*');
        window.open(base_url_js+'finance/edit_telat_bayar/'+token,'_blank');


    });

    $(document).on('click','.DetailPayment', function () {
        var NPM = $(this).attr('NPM');
        var PaymentID = $(this).attr('PaymentID');
        var html = '';
        var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                      '<thead>'+
                          '<tr>'+
                              '<th style="width: 5px;">No</th>'+
                              '<th style="width: 55px;">Nama</th>'+
                              '<th style="width: 55px;">Invoice</th>'+
                              '<th style="width: 55px;">BilingID</th>'+
                              '<th style="width: 55px;">Status</th>'+
                              '<th style="width: 55px;">Deadline</th>'+
                              '<th style="width: 55px;">Payment Date</th>'+
                              '<th style="width: 55px;">UpdateAt</th>';
        <?php if ($this->session->userdata('finance_auth_Policy_SYS') == 0): ?>
          table += '<th style="width: 55px;">Action</th>' ;                        
        <?php endif ?>                      
        table += '</tr>' ;  
        table += '</thead>' ; 
        table += '<tbody>' ;
        var isi = '';
        var CancelPayment = [];
        for (var i = 0; i < dataaModal.length; i++) {
          if(dataaModal[i]['PaymentID'] == PaymentID)
          {
            CancelPayment = dataaModal[i]['cancelPay'];
            var totCancelPayment = CancelPayment.length;
            var DetailPaymentArr = dataaModal[i]['DetailPayment'];
            var Nama = dataaModal[i]['Nama'];
            for (var j = 0; j < DetailPaymentArr.length; j++) {
              var yy = (DetailPaymentArr[j]['Invoice'] != '') ? formatRupiah(DetailPaymentArr[j]['Invoice']) : '-';
              var status = (DetailPaymentArr[j]['Status'] == 0) ? 'Belum Bayar' : 'Sudah Bayar';
              var btn_bayar = (DetailPaymentArr[j]['Status'] == 0) ? '<button class = "bayar" IDStudent = "'+DetailPaymentArr[j]['ID']+'" bayar = "1">Bayar</button>' : '<button class = "bayar" IDStudent = "'+DetailPaymentArr[j]['ID']+'" bayar = "0">Tidak Bayar</button>';
              var PaymentDate = (DetailPaymentArr[j]['DatePayment'] == '' || DetailPaymentArr[j]['DatePayment'] == null || DetailPaymentArr[j]['DatePayment'] == '0000-00-00 00:00:00') ? '' : DetailPaymentArr[j]['DatePayment'];
              var Deadline = (DetailPaymentArr[j]['Deadline'] == '' || DetailPaymentArr[j]['Deadline'] == null || DetailPaymentArr[j]['Deadline'] == '0000-00-00 00:00:00') ? '' : DetailPaymentArr[j]['Deadline'];
              var UpdateAt = (DetailPaymentArr[j]['UpdateAt'] == '' || DetailPaymentArr[j]['UpdateAt'] == null || DetailPaymentArr[j]['UpdateAt'] == '0000-00-00 00:00:00') ? '' : DetailPaymentArr[j]['UpdateAt']
              isi += '<tr>'+
                    '<td>'+ (j+1) + '</td>'+
                    '<td>'+ Nama + '</td>'+
                    '<td>'+ yy + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['BilingID'] + '</td>'+
                    '<td>'+ status + '</td>'+
                    '<td>'+ Deadline + '</td>'+
                    '<td>'+ PaymentDate + '</td>'+
                    '<td>'+UpdateAt + '</td>'+
                    <?php if ($this->session->userdata('finance_auth_Policy_SYS') == 0): ?>
                    '<td>'+ btn_bayar + '</td>'+
                    <?php endif ?>  
                  '<tr>'; 
            }
            break;
          }
        }

        table += isi+'</tbody>' ; 
        table += '</table>' ;

        html += table;
        var htmlReason = '<div class = "row"><div class= col-md-12><h5>List Cancel Payment</h5><table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                      '<thead>'+
                          '<tr>'+
                              '<th style="width: 5px;">No</th>'+
                              '<th style="width: 55px;">Reason</th>'+
                              '<th style="width: 55px;">CancelAt</th>'+
                              '<th style="width: 55px;">CancelBy</th>';
        htmlReason += '</tr>' ;  
        htmlReason += '</thead>' ; 
        htmlReason += '<tbody>' ;
        for (var i = 0; i < CancelPayment.length; i++) {
          var No = parseInt(i) + 1;
          htmlReason += '<tr>'+
                '<td>'+ (i+1) + '</td>'+
                '<td>'+ CancelPayment[i]['Reason'] + '</td>'+
                '<td>'+ CancelPayment[i]['CancelAt'] + '</td>'+
                '<td>'+ CancelPayment[i]['Name'] + '</td>'+
              '<tr>'; 
        }

        htmlReason += '</tbody>' ; 
        htmlReason += '</table></div></div>' ;
        if (CancelPayment.length > 0) {
          html += htmlReason;
        }
        var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
            '';

        $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Detail Payment'+'</h4>');
        $('#GlobalModalLarge .modal-body').html(html);
        $('#GlobalModalLarge .modal-footer').html(footer);
        $('#GlobalModalLarge').modal({
            'show' : true,
            'backdrop' : 'static'
        });    

    });


    function getChecboxNPM(element)
    {
         var allVals = [];
         $('.datatable2 :checked').each(function() {
            var NPM = $(this).val();
            var Invoice = $(this).attr('invoice');
            var Discount = $(this).attr('discount');
            var semester = $(this).attr('semester');
            var PTID = $(this).attr('PTID');
            var PTName = $(this).attr('PTName');
            var ta = $(this).attr('ta');
            var PaymentID = $(this).attr('PaymentID');
            var Status = $(this).attr('Status');

            if (Discount != null){
                var arr = {
                        Nama : $(this).attr('Nama'),
                        NPM : NPM,
                        semester : semester,
                        Prodi : $(this).attr('Prodi'),
                        Invoice : Invoice,
                        Discount : Discount,
                        PTID : PTID,
                        PTName : PTName,
                        ta : ta,
                        PaymentID : PaymentID,
                        Status : Status,

                };
                allVals.push(arr);
            }
            
         });
         return allVals;
    }


    $(document).on('click','.bayar', function () {
        var IDStudent = $(this).attr('IDStudent');
        var idget = $(this).attr('IDStudent');
        var bayar = $(this).attr('bayar');
        if (bayar == 1) {
          var html = '<div class="col-xs-12">'+
                        '<div id="datetimepicker1'+idget+'" class="input-group input-append date datetimepicker">'+
                            '<input data-format="yyyy-MM-dd" class="form-control" id="tgl'+idget+'" type=" text" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
                            '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
                        '</div>'+
                      '</div>';
          var btn_save = '<div class = "row" style = "margin-top : 10px"><div class = "col-xs-12"><button class = "btn btn-success save'+idget+'" idget = "'+idget+'">Save</button></div></div>';
          var rowhead = $( this )
            .closest('.row');
          var td = $( this )
            .closest('td')
            var htmlFirst = '';
          td
            .html(html+btn_save)

            $('#datetimepicker1'+idget).datetimepicker({
              format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
            });

            $('.save'+idget).click(function(){
              loading_button('.save'+idget);
              var url = base_url_js+'finance/bayar_manual_mahasiswa';
              var data = {
                  IDStudent : IDStudent,
                  bayar : bayar,
                  DatePayment :  $("#tgl"+idget).val()
              };
              var token = jwt_encode(data,'UAP)(*');
              $.post(url,{token:token},function (resultJson) {
                 // var resultJson = jQuery.parseJSON(resultJson);
                 loadData(1);
                 // $(".bayar[IDStudent='"+IDStudent+"']").remove();
                 td.empty();
              }).fail(function() {
                toastr.info('No Action...'); 
                // toastr.error('The Database connection error, please try again', 'Failed!!');
              }).always(function() {

              }); 
            })
        }
        else
        {
          loading_button(".bayar[IDStudent='"+IDStudent+"']");
          var url = base_url_js+'finance/bayar_manual_mahasiswa';
          var data = {
              IDStudent : IDStudent,
              bayar : bayar,
          };
          var token = jwt_encode(data,'UAP)(*');
          $.post(url,{token:token},function (resultJson) {
             // var resultJson = jQuery.parseJSON(resultJson);
             loadData(1);
             $(".bayar[IDStudent='"+IDStudent+"']").remove();
          }).fail(function() {
            toastr.info('No Action...'); 
            // toastr.error('The Database connection error, please try again', 'Failed!!');
          }).always(function() {

          }); 
        }
        
    });

    $(document).on('click','#btn-submit', function () {
        var arrValueCHK = getChecboxNPM();
        console.log(arrValueCHK);
        if (arrValueCHK.length > 0) {
            // check status jika 1
            var bool = true;
            var html = '';
            var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                          '<thead>'+
                              '<tr>'+
                                  '<th style="width: 5px;">No</th>'+
                                  '<th style="width: 55px;">Nama</th>'+
                                  '<th style="width: 55px;">NPM</th>'+
                                  '<th style="width: 55px;">Prodi</th>'+
                                  '<th style="width: 55px;">Payment Type</th>'+
                                  '<th style="width: 55px;">Discount</th>'+
                                  '<th style="width: 55px;">Invoice</th>';
            table += '</tr>' ;  
            table += '</thead>' ; 
            table += '<tbody>' ;
            var isi = '';
            for (var i = 0; i < arrValueCHK.length ; i++) {
              // console.log(arrValueCHK[i]['Status']);
              if (arrValueCHK[i]['Status'] == 1) {
                bool = false;
                break;
              }
              var yy = (arrValueCHK[i]['Invoice'] != '') ? formatRupiah(arrValueCHK[i]['Invoice']) : '-';
                isi += '<tr>'+
                      '<td>'+ (i+1) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Nama']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['NPM']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Prodi']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['PTName']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Discount']) + ' %</td>'+
                      '<td>'+ yy + '</td>'+
                    '<tr>';  
                
            }

            table += isi+'</tbody>' ; 
            table += '</table>' ;

            if (bool) {
              html += table;

              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>';
            } else {
              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '';
                  html = "Inputan data anda memiliki Status Approve, mohon periksa kembali";
            }
            

           $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'List Checklist Data'+'</h4>');
           $('#GlobalModalLarge .modal-body').html(html);
           $('#GlobalModalLarge .modal-footer').html(footer);
           $('#GlobalModalLarge').modal({
               'show' : true,
               'backdrop' : 'static'
           });
         
           $( "#ModalbtnSaveForm" ).click(function() {
            loading_button('#ModalbtnSaveForm');
            var url = base_url_js+'finance/approved_created_tagihan_mhs';
            var data = {
                arrValueCHK : arrValueCHK,
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               // var resultJson = jQuery.parseJSON(resultJson);
               loadData(1);
               $('#GlobalModalLarge').modal('hide');
            }).fail(function() {
              toastr.info('No Action...'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
            });
             
           }); // exit click function

        }
        else
        {
            toastr.error("Silahkan checked dahulu", 'Failed!!');
        }
    });

    $(document).on('click','#btn-submit-unapprove', function () {
      // $(".uniform[value=21150045]").addClass('hide');
      // $("#datatable2 table > tbody > tr [input[value=21150045]]").addClass('hide');
      // $(".uniform[value=21150045]").parent().addClass('hide');
      // $("tr[NPM=21150045]").addClass('hide'); Ok

        var arrValueCHK = getChecboxNPM();
        console.log(arrValueCHK);
        if (arrValueCHK.length > 0) {
            // check status jika 1
            var bool = true;
            var html = '';
            var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                          '<thead>'+
                              '<tr>'+
                                  '<th style="width: 5px;">No</th>'+
                                  '<th style="width: 55px;">Nama</th>'+
                                  '<th style="width: 55px;">NPM</th>'+
                                  '<th style="width: 55px;">Prodi</th>'+
                                  '<th style="width: 55px;">Payment Type</th>'+
                                  '<th style="width: 55px;">Discount</th>'+
                                  '<th style="width: 55px;">Invoice</th>';
            table += '</tr>' ;  
            table += '</thead>' ; 
            table += '<tbody>' ;
            var isi = '';
            for (var i = 0; i < arrValueCHK.length ; i++) {
              // console.log(arrValueCHK[i]['Status']);
              if (arrValueCHK[i]['Status'] == 0) {
                bool = false;
                break;
              }
              var yy = (arrValueCHK[i]['Invoice'] != '') ? formatRupiah(arrValueCHK[i]['Invoice']) : '-';
                isi += '<tr>'+
                      '<td>'+ (i+1) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Nama']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['NPM']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Prodi']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['PTName']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Discount']) + ' %</td>'+
                      '<td>'+ yy + '</td>'+
                    '<tr>';  
                
            }

            table += isi+'</tbody>' ; 
            table += '</table>' ;

            if (bool) {
              html += table;

              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>';
            } else {
              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '';
                  html = "Inputan data anda memiliki Status Approve, mohon periksa kembali";
            }
            

           $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'List Checklist Data'+'</h4>');
           $('#GlobalModalLarge .modal-body').html(html);
           $('#GlobalModalLarge .modal-footer').html(footer);
           $('#GlobalModalLarge').modal({
               'show' : true,
               'backdrop' : 'static'
           });
         
           $( "#ModalbtnSaveForm" ).click(function() {
            loading_button('#ModalbtnSaveForm');
            var url = base_url_js+'finance/unapproved_created_tagihan_mhs';
            var data = {
                arrValueCHK : arrValueCHK,
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               var resultJson = jQuery.parseJSON(resultJson);
               console.log(resultJson);
               if (resultJson != '')
               {
                toastr.info(resultJson); 
               }
               else
               {
                toastr.success('Data berhasil disimpan', 'Success!');
               }
               loadData(1);
               $('#GlobalModalLarge').modal('hide');
            }).fail(function() {
              toastr.info('No Action...'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
            });
             
           }); // exit click function
          

        }
        else
        {
            toastr.error("Silahkan checked dahulu", 'Failed!!');
        }
    });


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