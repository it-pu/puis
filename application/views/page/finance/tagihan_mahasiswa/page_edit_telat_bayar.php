<script type="text/javascript" src="<?php echo base_url();?>assets/custom/jquery.maskMoney.js"></script>
<style type="text/css">
  .btn-submit{
    background-color: #1ace37;
  }
</style>
<div class="row">
    <div class="col-md-12">
        <button href="<?php echo base_url().'finance/tagihan-mhs/cek-tagihan-mhs' ?>" class = "btn btn-default btn-back">Cancel</button>
        <hr/>
        <table class="table table-bordered datatable2 hide" id = "datatable2">
            <thead>
            <tr style="background: #333;color: #fff;">
                <th style="width: 12%;">Program Study</th>
                <th style="width: 20%;">Nama,NPM &  VA</th>
                <th style="width: 15%;">Payment Type</th>
                <th style="width: 5%;">Credit</th>
                <th style="width: 5%;">IPS</th>
                <th style="width: 5%;">IPK</th>
                <th style="width: 10%;">Discount</th>
                <th style="width: 10%;">Invoice</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Detail Payment</th>
            </tr>
            </thead>
            <tbody id="dataRow"></tbody>
        </table>
        <hr>
        <div id = "inputCicilan" class="hide">
          <div class="widget box">
              <div class="widget-header">
                  <h4 class="header"><i class="icon-reorder"></i>Edit</h4>
              </div>
              <div class="widget-content">
                  <!--  -->
                   
                  <!-- end widget -->
              </div>
              <hr/>
          </div>
        </div>
    </div>
    
</div>

<script>
    window.CanBeEdit = 1;
    window.CanBeDelete = 1;
    window.totMin = 0;
    window.get_Invoice = '';
    window.dataa = '';
    window.dataaModal = '';
    $(document).ready(function () {
        var NPM = "<?php echo $NPM ?>";
        loadData(1,NPM);
    });

    $(document).on('keypress','#NIM', function (event)
    {

        if (event.keyCode == 10 || event.keyCode == 13) {
          valuee = $(this).val();
          loadData(1,valuee);
        }
    }); // exit enter

    $(document).on('click','#idbtn-cari', function () {
        var NPM = $("#NIM").val();
        result = Validation_required(NPM,'NPM');
        if (result['status'] == 0) {
          toastr.error(result['messages'], 'Failed!!');
        }
        else
        {
          loadData(1,NPM);
        }
    });

    function loadData(page,NPM) {
        var NIM = NPM;
        CanBeDelete = 1;
        getTotCicilan = 0;
        $(".widget-content").empty();
        $("#inputCicilan").addClass('hide');
        $('#datatable2').addClass('hide');

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
            var PTID = '<?php echo $PTID ?>';
            var url = base_url_js+'finance/get_created_tagihan_mhs/'+page;
            var data = {
                ta : '',
                prodi : '',
                PTID  : '<?php echo $PTID ?>',
                NIM : NIM,
                Semester : "<?php echo $semester ?>",
            };
            // console.log(data)
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               
            }).done(function(resultJson) {
                var resultJson = jQuery.parseJSON(resultJson);
                console.log(resultJson);
                 var Data_mhs = resultJson.loadtable;
                 dataaModal = Data_mhs;
                 // Cek Deadline Semester Antara
                   var CekDeadline = ''; 
                   if (PTID == 5 || PTID == 6) {
                     ajax_data_deadline_semester_antara(Data_mhs[0]['SemesterID']).then(function(data){
                         if (data.status == 1) {
                           CekDeadline = false;
                           HTMLWrite(Data_mhs);
                         }
                         else{
                          toastr.info('Tanggal KRS Semester Antara belum berakhir Period('+data.StartKRS+' - '+data.EndKRS+'), tidak bisa melakukan action '); 
                         }          
                     })
                   }
                   else
                   {
                    HTMLWrite(Data_mhs);
                   }
                  
            }).fail(function() {
              
              toastr.info('No Result Data'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#NotificationModal').modal('hide');
            });
    }

    $(document).on('keyup','.costInput', function () {
        var arrTemp = [];
        var setMinimal = 500000;
        // console.log(totMin);
        // get_Invoice = get_Invoice - totMin ;
        var total = get_Invoice;
        var setPengurangan = totMin;
        total = total - setPengurangan;
        // console.log(setPengurangan);
        $('.costInput').each(function(){
            var temp = findAndReplace($(this).val(), ".","");
            /*if (temp < setMinimal) {
                $(this).val(setMinimal);
            }*/
            var arr2 = {
                id : $(this).attr('id'),
                valuee : $(this).val(),
            }
            arrTemp.push(arr2);
        })

        console.log(arrTemp);

        var arrValue = [];
        var count = 0;
        for (var i = 0; i < arrTemp.length; i++) {
            var temp = findAndReplace(arrTemp[i]['valuee'], ".","");
            if (count > total) {
                var splitcicilan = parseInt(total) / parseInt(arrTemp.length);
                var splitcicilan = (splitcicilan < setMinimal) ? setMinimal : parseInt(splitcicilan);
                var cost = 0;
                var cost_value = splitcicilan;
                for (var j = 0; j < arrTemp.length; j++) {

                   if (j == (arrTemp.length - 1)) {
                       cost_value = parseInt(total) - parseInt(cost);
                   }
                   var getID = parseInt(j) + 1;
                   var IDCost = arrTemp[j]['id'];
                   $("#"+IDCost).val(cost_value);
                   cost = cost + cost_value; 
                }
            }
            else
            {
                if ((arrTemp.length - 1) == i) {
                    var getID = parseInt(i) + 1;
                    var IDCost = arrTemp[i]['id'];
                    console.log(IDCost);
                    $("#"+IDCost).val(parseInt(total) - parseInt(count));
                }
            }
            count += parseInt(temp);
        }
        $('.costInput').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
        $('.costInput').maskMoney('mask', '9894');
    });

    $(document).on('click','.btn-edit', function () {
       loading_button('.btn-edit'); 
       // get all input
        var arrTemp = [];
        $('.costInput').each(function(){
            var Invoice = $(this).val();
            for(i = 0; i <Invoice.length; i++) {
             Invoice = Invoice.replace(".", "");
            }
            var BilingID =  $(this).attr('BilingID')
            var SID = $(this).attr('SID');
            var Deadline = $("#datetime_deadline"+SID).val();
            var cicilan = $(this).attr('cicilan');
            var ID = $(this).attr('IDStudent');
            data = {
              Invoice : Invoice,
              BilingID  : BilingID,
              Deadline       : Deadline,
              cicilan     : cicilan,
              ID : ID,
            }
            arrTemp.push(data);
        })

        console.log(arrTemp);
        // check cicilan != 0 dan Deadline is empty
          var bool = true;
          var msg = '';
          for (var i = 0; i < arrTemp.length; i++) {
            if (arrTemp[i].Invoice == 0) {
              msg = 'Price Cicilan tidak boleh 0';
              bool = false
              break;
            }

            if (arrTemp[i].Deadline == "") {
              msg = 'Deadline belum diisi';
              bool = false
              break;
            }  
          }

          if (bool) {
            console.log(arrTemp);
            // hitung tanggal tidak boleh melewati cicilan sebelumnya
              var bool2 = true;
              for (var i = 0; i < arrTemp.length; i++) {
                var date1 = arrTemp[i].Deadline;
                date1 = date1.substring(0, 10);
                 for (var j = 0; j < arrTemp.length; j++) {
                  if (i < j) {
                     var date2 = arrTemp[j].Deadline;
                     date2 = date2.substring(0, 10);

                     var startDate = moment(date1, "YYYY-MM-DD");
                     var endDate = moment(date2, "YYYY-MM-DD");
                     var result = endDate.diff(startDate, 'days');
                     result = parseInt(result);
                     console.log(result);
                     if (result <= 0) {
                      bool2 = false;
                      console.log('i ' + date1 + '< j : ' + date2);
                      break;
                     } 
                  }
                  
                 }

                 if (!bool2) {
                    break;
                    console.log('i < j');
                 }

              }
            // hitung tanggal tidak boleh melewati cicilan sebelumnya

            if (bool2) {
              // console.log('ok');
              var url = base_url_js + "finance/tagihan-mhs/set-edit-cicilan-tagihan-mhs/submit";
              var data = arrTemp
              var token = jwt_encode(data,"UAP)(*");
              $.post(url,{token:token},function (data_json) {
                  // jsonData = data_json;
                  var obj = JSON.parse(data_json); 
                  if(obj != ''){
                      $('.btn-edit').prop('disabled',false).html('<i class="fa fa-pencil-square-o"></i> Edit'); 
                      toastr.error(obj, 'Failed!!');
                  }
                  else
                  {
                      window.location.reload(true); 
                  }

              }).done(function() {
                // $('#btn-Save').prop('disabled',false).html('Submit');
              }).fail(function() {
                $('.btn-edit').prop('disabled',false).html('<i class="fa fa-pencil-square-o"></i> Edit');  
                toastr.error('The Database connection error, please try again', 'Failed!!');
              }).always(function() {
               $('.btn-edit').prop('disabled',false).html('<i class="fa fa-pencil-square-o"></i> Edit');
              });
            } else {
              toastr.error('Tanggal Deadline cicilan tidak boleh mendahului tanggal cicilan sebelumnya', 'Failed!!');
              $('.btn-edit').prop('disabled',false).html('<i class="fa fa-pencil-square-o"></i> Edit');
            }

          } else {
            toastr.error(msg, 'Failed!!');
            $('.btn-edit').prop('disabled',false).html('<i class="fa fa-pencil-square-o"></i> Edit');
          }

    });

    $(document).on('click','.DetailPayment', function () {
        var NPM = $(this).attr('NPM');
        var PaymentID = $(this).attr('PaymentID');
        var html = modal_detail_payment(dataaModal,NPM,PaymentID)

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

    function ajax_data_deadline_semester_antara(SemesterID)
    {
      var def = jQuery.Deferred();
      var data = {
          SemesterID : SemesterID,
          auth : 's3Cr3T-G4N',
      };
      var token = jwt_encode(data,"UAP)(*");
      var url = base_url_js+'rest/__cek_deadline_payment_semester_antara';
      $.post(url,{ token:token },function () {

      }).done(function(data_json) {
        def.resolve(data_json);
      }).fail(function() {
          def.reject();
      });
      return def.promise();
    }

    function HTMLWrite(Data_mhs)
    {
      if (Data_mhs.length == 1) {
          for(var i=0;i<Data_mhs.length;i++){
               var b = 0;
               var ccc = 0;
               var yy = (Data_mhs[i]['InvoicePayment'] != '') ? formatRupiah(Data_mhs[i]['InvoicePayment']) : '-';
               get_Invoice = Data_mhs[i]['InvoicePayment'];
                var n = get_Invoice.indexOf(".");
               get_Invoice = get_Invoice.substring(0, n);
               dataa = {ID : Data_mhs[i]['PaymentID'],PTID : Data_mhs[i]['PTID'],SemesterID : Data_mhs[i]['SemesterID']};
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
                    var IPSTo = '';
                    var IPKTo = '';
                    try {
                        IPSTo = getCustomtoFixed(Data_mhs[i]['IPS'],2);
                        IPKTo = getCustomtoFixed(Data_mhs[i]['IPK'],2);
                    }
                    catch(err) {
                        IPSTo = '';
                        IPKTo = '';
                    } 
                    // show bintang
                    // var bintang = (Data_mhs[i]['Pay_Cond'] == 1) ? '<p style="color: red;">*</p>' : '<p style="color: red;">**</p>';
                    var bintang = setBintangFinance(Data_mhs[i]['Pay_Cond']);
                    if (Data_mhs[i]['DetailPayment'].length > 0) { // menandakan memiliki cicilan lebih dari 1
                     $('#dataRow').append(tr +
                                            '<td>'+Data_mhs[i]['ProdiEng']+'<br>'+Data_mhs[i]['SemesterName']+'</td>' +
                                            '<td>'+bintang+'<br/>'+Data_mhs[i]['Nama']+'<br>'+Data_mhs[i]['NPM']+'<br>'+Data_mhs[i]['VA']+'</td>' +
                                            '<td>'+Data_mhs[i]['PTIDDesc']+'</td>' +
                                            '<td>'+Data_mhs[i]['Credit']+'</td>' +
                                            '<td>'+IPSTo+'</td>' +
                                            '<td>'+IPKTo+'</td>' +
                                            '<td>'+Data_mhs[i]['Discount']+'%</td>' +
                                            '<td>'+yy+'</td>' +
                                            '<td>'+status+'</td>' +
                                            '<td>'+'<button class = "DetailPayment" NPM = "'+Data_mhs[i]['NPM']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'">View</button>'+'</td>' +
                                            '</tr>');
                    }   
                  
               }
               else
               {
                  toastr.info('Mohon Unapprove dahulu sebelum di edit'); 
               } 
              
          }

          if(Data_mhs.length == 1)
          {
            if (Data_mhs[0]['StatusPayment'] == 0) {
              if (Data_mhs[0]['DetailPayment'].length > 0) {
                    $('#datatable2').removeClass('hide');
                    var DetailPayment = Data_mhs[0]['DetailPayment'];
                    // buat table cicilan beserta input
                      var div = '';
                      var enddiv = '</div>';
                      var table = '';
                      div = '<div id = "tblData" class="table-responsive">';
                      table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                      '<thead>'+
                          '<tr>'
                      for (var i = 0; i < DetailPayment.length; i++) {
                            var a = parseInt(i) + 1
                            table += '<th style="width: 75px;">'+'Cicilan '+a+'</th>' ;
                      }
                      table += '<th style="width: 70px;">Action</th>';  
                      table += '</tr>' ;  
                      table += '</thead>' ; 
                      table += '<tbody>' ;  
                      table += '</tbody>' ; 
                      table += '</table>' ; 
                    $(".widget-content").html(div+table+enddiv);
                    $("#inputCicilan").removeClass('hide');
                    var tbodyTbl = '<tr>';
                    totMin = 0;
                    for (var i = 0; i < DetailPayment.length; i++) {
                      var cicilan = parseInt(i) + 1;
                      var Cost = DetailPayment[i]['Invoice'];
                      var n = Cost.indexOf(".");
                      var Cost = Cost.substring(0, n);
                      if (DetailPayment[i]['Status'] == 1) {
                        totMin = parseInt(totMin) + parseInt(Cost);
                      }

                      if (CanBeDelete == 1) {
                        if (DetailPayment[i]['Status'] == 1) {
                            CanBeDelete = 0;
                        }
                      }

                      var Invoice = (DetailPayment[i]['Status'] == 1) ? '<label>'+formatRupiah(DetailPayment[i]['Invoice'])+'</label><br>' : '<input type="text" id = "cost'+i+'" value = "'+Cost+'" class = "form-control costInput" cicilan = "'+cicilan+'" BilingID = "'+DetailPayment[i]['BilingID']+'" SID = "'+i+'" IDStudent = "'+DetailPayment[i]['ID']+'"><br>';
                      var Deadline = (DetailPayment[i]['Status'] == 1) ? '<label>'+DetailPayment[i]['UpdateAt']+'</label><br>Sudah Bayar' : 'Deadline<div id="datetimepicker'+i+'" class="input-group input-append date datetimepicker">'+
                    '<input data-format="yyyy-MM-dd hh:mm:ss" class="form-control datetimepickerClass"  id="datetime_deadline'+i+'" type="text" cicilan = "'+cicilan+'" value = "'+DetailPayment[i]['Deadline']+'" BilingID = "'+DetailPayment[i]['BilingID']+'" IDStudent = "'+DetailPayment[i]['ID']+'"></input>'+
                    '<span class="input-group-addon add-on">'+
                      '<i data-time-icon="icon-time" data-date-icon="icon-calendar">'+
                      '</i>'+
                    '</span>'+
                '</div>';
                      tbodyTbl += '<td>'+Invoice+Deadline+'</td>';
                    }
                    var btn_edit = '<span data-smt="" class="btn btn-xs btn-edit">'+
                                         '<i class="fa fa-pencil-square-o"></i> Edit'+
                                        '</span>';
                    var btn_delete = '';                  
                    tbodyTbl += '<td>'+btn_edit+btn_delete+'</td>';
                    tbodyTbl += '</tr>';
                    $(".tableData tbody").append(tbodyTbl);

                    Date.prototype.addDays = function(days) {
                        var date = new Date(this.valueOf());
                        date.setDate(date.getDate() + days);
                        return date;
                    }
                    var date = new Date();
                    $('.datetimepicker').datetimepicker({
                     // startDate: today,
                     // startDate: '+2d',
                     startDate: date.addDays(1),
                    });
                    $('.datetimepickerClass').prop('readonly',true);
                    $('.costInput').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
                    $('.costInput').maskMoney('mask', '9894');
              }
            }        

          }
      } else {
        toastr.error('Error', 'Failed!!');
      } 
    }

    $(document).on('click','.btn-back',function(e){
      window.history.back();
    })


</script>