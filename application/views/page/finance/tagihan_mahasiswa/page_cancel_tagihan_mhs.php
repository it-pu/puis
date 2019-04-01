
<style type="text/css">
  .btn-submit{
    background-color: #1ace37;
  }

  .btn-unapprove { background-color: hsl(41, 85%, 35%) !important; background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#efb73d", endColorstr="#a5750d"); background-image: -khtml-gradient(linear, left top, left bottom, from(#efb73d), to(#a5750d)); background-image: -moz-linear-gradient(top, #efb73d, #a5750d); background-image: -ms-linear-gradient(top, #efb73d, #a5750d); background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #efb73d), color-stop(100%, #a5750d)); background-image: -webkit-linear-gradient(top, #efb73d, #a5750d); background-image: -o-linear-gradient(top, #efb73d, #a5750d); background-image: linear-gradient(#efb73d, #a5750d); border-color: #a5750d #a5750d hsl(41, 85%, 29%); color: #fff !important; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.39); -webkit-font-smoothing: antialiased; }
</style>
<div class="row">
    <div class="col-xs-12" >
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Batal Tagihan</h4>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-top: 30px;">
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
                                  <input type="text" name="" class="form-control" placeholder="Input NPM Mahasiswa" id = "NIM">
                              </div>
                          </div>
                          <div class="col-md-3" style="margin-top: 10px">
                            <div class="thumbnail" style="min-height: 30px;padding: 10px;">
                                <select class="form-control" id="selectSemester">
                                </select>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div  class="col-xs-12" align="right" id="pagination_link"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered datatable2 hide" id = "datatable2">
                            <thead>
                            <tr style="background: #333;color: #fff;">
                                <th style="width: 3%;"><input type="checkbox" class="uniform" value="nothing" id ="dataResultCheckAll"></th>
                                <th style="width: 12%;">Program Study</th>
                                <!-- <th style="width: 10%;">Semester</th> -->
                                <th style="width: 20%;">Nama,NPM &  VA</th>
                                <!-- <th style="width: 5%;">NPM</th> -->
                                <!-- <th style="width: 5%;">Year</th> -->
                                <th style="width: 15%;">Payment Type</th>
                                <th style="width: 15%;">Email PU</th>
                                <th style="width: 15%;">IPS</th>
                                <th style="width: 15%;">IPK</th>
                                <th style="width: 10%;">Discount</th>
                                <th style="width: 10%;">Invoice</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 10%;">Detail Payment</th>
                            </tr>
                            </thead>
                            <tbody id="dataRow"></tbody>
                        </table>
                    </div>
                    <div  class="col-xs-12" align="right"><button class="btn btn-unapprove btn-cancel hide" id="btn-cancel"> <i class="fa fa-times" aria-hidden="true"></i> Cancel</button></div>
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
            var url = base_url_js+'finance/get_created_tagihan_mhs_not_approved/'+page;
            var data = {
                ta : ta,
                prodi : prodi,
                PTID  : PTID,
                NIM : NIM,
                Semester : Semester,
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               var resultJson = jQuery.parseJSON(resultJson);
               console.log(resultJson);
                var Data_mhs = resultJson.loadtable;
                data = Data_mhs;
                dataaModal = Data_mhs;
                var t_array = [];
               for(var i=0;i<Data_mhs.length;i++){
                    var ccc = 0;
                    var yy = (Data_mhs[i]['InvoicePayment'] != '') ? formatRupiah(Data_mhs[i]['InvoicePayment']) : '-';
                    // proses status
                    var status = '';
                    if(Data_mhs[i]['StatusPayment'] == 0)
                    {
                      status = 'Belum Approve ';
                      for (var j = 0; j < Data_mhs[i]['DetailPayment'].length; j++) {
                        var a = Data_mhs[i]['DetailPayment'][j]['Status'];
                        if(a== 1)
                        {
                          b = parseInt(b) + parseInt(Data_mhs[i]['DetailPayment'][j]['Invoice']);
                          //bayar = bayar + 1;
                        }
                        //cicilan = cicilan + 1;
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
                    }
                    else
                    {
                      status = 'Approve';
                      // check lunas atau tidak
                        // count jumlah pembayaran dengan status 1
                        var b = 0;
                        for (var j = 0; j < Data_mhs[i]['DetailPayment'].length; j++) {
                          var a = Data_mhs[i]['DetailPayment'][j]['Status'];
                          if(a== 1)
                          {
                            b = parseInt(b) + parseInt(Data_mhs[i]['DetailPayment'][j]['Invoice']);
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
                   }

                   if(Data_mhs[i]['StatusPayment'] == 0) // menandakan belum approve
                    {
                      // if (Data_mhs[i]['DetailPayment'].length == 1) 
                      if (Data_mhs[i]['DetailPayment'].length > 0) { 
                        // show bintang
                        var bintang = (Data_mhs[i]['Pay_Cond'] == 1) ? '<p style="color: red;">*</p>' : '<p style="color: red;">**</p>';
                        t_array.push(Data_mhs[i]['Nama']);
                        $('#dataRow').append(tr +
                                               '<td>'+inputCHK+'</td>' +
                                               '<td>'+Data_mhs[i]['ProdiEng']+'<br>'+Data_mhs[i]['SemesterName']+'</td>' +
                                               // '<td>'+Data_mhs[i]['SemesterName']+'</td>' +
                                               '<td>'+bintang+Data_mhs[i]['Nama']+'<br>'+Data_mhs[i]['NPM']+'<br>'+Data_mhs[i]['VA']+'</td>' +
                                               // '<td>'+Data_mhs[i]['NPM']+'</td>' +
                                               // '<td>'+Data_mhs[i]['Year']+'</td>' +
                                               '<td>'+Data_mhs[i]['PTIDDesc']+'</td>' +
                                               '<td>'+Data_mhs[i]['EmailPU']+'</td>' +
                                               '<td>'+getCustomtoFixed(Data_mhs[i]['IPS'],2)+'</td>' +
                                               '<td>'+getCustomtoFixed(Data_mhs[i]['IPK'],2)+'</td>' +
                                               '<td>'+Data_mhs[i]['Discount']+'%</td>' +
                                               '<td>'+yy+'</td>' +
                                               '<td>'+status+'</td>' +
                                               '<td>'+'<button class = "btn btn-default DetailPayment" NPM = "'+Data_mhs[i]['NPM']+'"><i class="fa fa-search" aria-hidden="true"></i> View</button>'+'</td>' +
                                               '</tr>');
                      }
                      
                    } 
                   
               }

               if(t_array.length > 0)
               {
                $("#btn-cancel").removeClass('hide');
                $('#datatable2').removeClass('hide');
                $("#pagination_link").html(resultJson.pagination_link);
               }
               
            }).fail(function() {
              
              toastr.info('No Result Data'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#NotificationModal').modal('hide');
            });
    }

    $(document).on('click','#dataResultCheckAll', function () {
        $('input.uniform').not(this).prop('checked', this.checked);
    });

    $(document).on('click','.DetailPayment', function () {
        var NPM = $(this).attr('NPM');
        var html = '';
        var table = '<div class = "row"><div class= col-md-12><table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                      '<thead>'+
                          '<tr>'+
                              '<th style="width: 5px;">No</th>'+
                              '<th style="width: 55px;">Nama</th>'+
                              '<th style="width: 55px;">Invoice</th>'+
                              '<th style="width: 55px;">BilingID</th>'+
                              '<th style="width: 55px;">Status</th>'+
                              '<th style="width: 55px;">Deadline</th>'+
                              '<th style="width: 55px;">UpdateAt</th>';
        table += '</tr>' ;  
        table += '</thead>' ; 
        table += '<tbody>' ;
        var isi = '';
        // console.log(dataaModal);
        var CancelPayment = [];
        for (var i = 0; i < dataaModal.length; i++) {
          if(dataaModal[i]['NPM'] == NPM)
          {
            CancelPayment = dataaModal[i]['cancelPay'];
            var totCancelPayment = CancelPayment.length;
            var DetailPaymentArr = dataaModal[i]['DetailPayment'];
            var Nama = dataaModal[i]['Nama'];
            for (var j = 0; j < DetailPaymentArr.length; j++) {
              var yy = (DetailPaymentArr[j]['Invoice'] != '') ? formatRupiah(DetailPaymentArr[j]['Invoice']) : '-';
              var status = (DetailPaymentArr[j]['Status'] == 0) ? 'Belum Bayar' : 'Sudah Bayar';
              isi += '<tr>'+
                    '<td>'+ (j+1) + '</td>'+
                    '<td>'+ Nama + '</td>'+
                    '<td>'+ yy + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['BilingID'] + '</td>'+
                    '<td>'+ status + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['Deadline'] + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['UpdateAt'] + '</td>'+
                  '<tr>'; 
            }
            break;
          }
        }

        table += isi+'</tbody>' ; 
        table += '</table></div></div>' ;
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

    $(document).on('click','#btn-cancel', function () {
        var arrValueCHK = getChecboxNPM();
        console.log(arrValueCHK);
        if (arrValueCHK.length > 0) {
            // check status jika 1
            var bool = true;
            var html = '';
            var table = '<div class = "row><div class = "col-md-12><table class="table table-striped table-bordered table-hover table-checkable tableData">'+
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
            table += '</table></div></div>' ;

            var htmlreason = '<div class ="row" style = "margin-top : 10px"><div class = "col-md-12">'+
                                '<label>Reason</label><textarea class="form-control TextareaReason"></textarea>'+
                              '</div></div>';  

            if (bool) {
              html += table+htmlreason;

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
            var Reason = $(".TextareaReason").val();
            if (Reason == '') {toastr.info('Reason is required');return}
            loading_button('#ModalbtnSaveForm');
            var url = base_url_js+'finance/cancel_created_tagihan_mhs';
            var data = {
                arrValueCHK : arrValueCHK,
                Reason : Reason,
            };
            console.log(data);
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