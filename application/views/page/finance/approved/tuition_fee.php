<script type="text/javascript" src="<?php echo base_url();?>assets/custom/jquery.maskMoney.js"></script>
<div class="row">
    <div class="col-xs-12" >
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Approval</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div id="panel_web" class="" style="padding:30px;padding-top:0px;">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="javascript:void(0)" class="tab-btn-tuition-fee" data-page="tuition_fee/1">Approval Tuition Fee</a></li>
                            <li role="presentation"><a href="javascript:void(0)" class="tab-btn-tuition-fee" data-page="tuition_fee_approved/1">Tuition Fee Approved</a></li>
                        </ul>
                    </div>
                </div>
                <div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 10px">
                  <div class="col-xs-6 col-md-offset-3">
                    <div class="thumbnail" style="height: 80px">
                      <div class="col-xs-6 col-md-offset-3">
                        <label>Formulir Code</label>
                        <input type="text" name="FormulirCode" id = "FormulirCode" class="form-control" placeholder="All...">
                      </div>  
                    </div>   
                  </div>
                </div>
                <div class="row" style="margin-top: 10px;margin-left: 0px;margin-right: 10px">
                    <div id="dataPageLoad" style="margin-top:0px;">
                        
                    </div>
                    <div class="row" style="margin-top: 10px;margin-left: 0px;margin-right: 10px">
                      <div  class="col-xs-12" align="right" id="pagination_link"></div>  
                    </div>
                </div>          
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.pageHtml = '';
    window.Grade = [];
    $(document).ready(function () {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            $('#panel_web').addClass('wrap');
            $('#panel_web').css({"padding": "0px", "padding-right": "20px"});
        }
        loadPage('tuition_fee/1');
        FuncSearch();
    });

    $('.tab-btn-tuition-fee').click(function () {
        var page = $(this).attr('data-page');

        $('li[role=presentation]').removeClass('active');
        $(this).parent().addClass('active');
        loadPage(page);
    });

    function loadPage(page) { 
        loading_page('#dataPageLoad');
        var res = page.split("/");
        switch(res[0]) {
            case 'tuition_fee':
                var url = base_url_js+'finance/approved/tuition-fee/approve/'+res[1];
                var FormulirCode = $("#FormulirCode").val();
                var data = {
                    FormulirCode : FormulirCode,
                };
                var token = jwt_encode(data,"UAP)(*"); 
                $.post(url,{page:page,token:token},function (data_json) {
                    var obj = jQuery.parseJSON(data_json);
                    $("#dataPageLoad").html(obj.loadtable);
                    $("#pagination_link").html(obj.pagination_link);
                    Grade = obj.Grade;
                    $('#NotificationModal').modal('hide');
                    FunEvClickBtnSave();
                    FunEvClickBtnReject();
                });
                break;
            case 'tuition_fee_approved':
                var url = base_url_js+'finance/approved/tuition-fee/approved/'+res[1];
                var FormulirCode = $("#FormulirCode").val();
                var data = {
                    FormulirCode : FormulirCode,
                };
                var token = jwt_encode(data,"UAP)(*"); 
                $.post(url,{page:page,token:token},function (data_json) {
                    var obj = jQuery.parseJSON(data_json);
                    $("#dataPageLoad").html(obj.loadtable);
                    $("#pagination_link").html(obj.pagination_link);
                    $('#NotificationModal').modal('hide');
                });
                break;    
            default:
                'code block'
        }

        $(".widget_delete").remove();
        
    }

    function FuncSearch()
    {
      $("#FormulirCode").keyup(function(){
        if( this.value.length < 5 && this.value.length != 0 ) return;
           /* code to run below */
         loadPage(pageHtml+'/1'); 
      })
    }

    $(document).on("click", ".pagination li a", function(event){
      event.preventDefault();
      var page = $(this).data("ci-pagination-page");
      loadPage(pageHtml+'/'+page);
      // loadData_register_document(page);
    });

    function FunEvClickBtnSave()
    {
        $("#btn-Save").click(function(){
            switch(pageHtml)
            {
             case "tuition_fee" :
                process_tuition_fee();
             break;      
             case  "tuition_fee_delete" :
                   // process_tuition_fee_delete();
             break;
            }
        })
    }

    function FunEvClickBtnReject()
    {
        $("#btn-reject").click(function(){
            var arrValueCHK = getValueChecbox();
            if (arrValueCHK.length > 0) {
                $('#NotificationModal .modal-body').html('<div style="text-align: center;"><p>Input Reason</p><input type="text"  id="InputReason" class="form-control"><br>' +
                    '<button type="button" id="confirmYes" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                    '</div>');
                $('#NotificationModal').modal('show');

                $("#confirmYes").click(function(){
                    var InputReason = $("#InputReason").val();
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

                    var url = base_url_js+'finance/admission/set_tuition_fee/delete_data';
                    var data = arrValueCHK;
                    var token = jwt_encode(data,"UAP)(*");
                    $.post(url,{token:token,InputReason : InputReason},function (data_json) {
                        setTimeout(function () {
                           toastr.options.fadeOut = 10000;
                           toastr.success('Data berhasil disimpan', 'Success!');
                           loadPage('tuition_fee/1');
                           $(".widget_delete").remove();
                           $('#NotificationModal').modal('hide');
                        },500);
                    }).done(function() {
                      
                    }).fail(function() {
                      toastr.error('The Database connection error, please try again', 'Failed!!');
                    }).always(function() {
                      $('#NotificationModal').modal('hide');
                    });
                })

            }
            else
            {
                toastr.error("Silahkan checked dahulu", 'Failed!!');
            }
        }) // exit click btn-reject
    }

    function process_tuition_fee()
    {
        var arrValueCHK = getValueChecbox();
        if (arrValueCHK.length > 0) {
            $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Apakah anda yakin untuk melakukan request ini ?? </b> ' +
                '<button type="button" id="confirmYes" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>');
            $('#NotificationModal').modal('show');

            $("#confirmYes").click(function(){
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

                var inc = 1;
                var timeOut = 800;
                var url = base_url_js+'finance/approved/tuition-fee/approve_save';
                for (var i = 0; i < arrValueCHK.length; i++) {
                    var data = arrValueCHK[i];
                    var token = jwt_encode(data,"UAP)(*");
                    $.post(url,{token:token},function (data_json) {
                        // var obj = JSON.parse(data_json);
                        // window.open(base_url_js+'fileGet/'+obj,'_blank');
                    }).done(function() {
                      
                    }).fail(function() {
                      toastr.error('The Database connection error, please try again', 'Failed!!');
                    }).always(function() {
                      // $('#NotificationModal').modal('hide');
                    });
                    inc++;
                }

                var aa = parseInt(inc) * parseInt(timeOut);
                setTimeout(function () {
                     $(".widget_delete").remove();
                     loadPage('tuition_fee/1'); 
                },aa);
               
                // $('#NotificationModal').modal('hide');
                
            })

        }
        else
        {
            toastr.error("Silahkan checked dahulu", 'Failed!!');
        }
        
    }

    function getValueChecbox()
    {
         var allVals = [];
         $('.tableData input[type=checkbox]:checked').each(function() {
            if($(this).val() != 'nothing')
            {
                allVals.push($(this).val());
            }
           
         });
         return allVals;
    }

    $(document).on('click','#dataResultCheckAll', function () {
        $('input.uniform').not(this).prop('checked', this.checked);
    });


    // event cheklist
    $(document).on('change','.tableData input[type=checkbox]', function () {
        var Uniformvaluee = $(this).val();
        switch(pageHtml)
        {
         case "tuition_fee" :
            var Nama = $(this).attr('nama');
            if(this.checked) {
               var url = base_url_js + "get_detail_cicilan_fee_admisi";
               var data = {
                   ID_register_formulir : Uniformvaluee,
               }
               var token = jwt_encode(data,"UAP)(*");
               $.post(url,{token:token},function (data_json) {
                   // jsonData = data_json;
                   var obj = JSON.parse(data_json);
                   // console.log(obj);
                   var url2 = base_url_js + "get_nilai_from_admission";
                   var data2 = {
                       ID_register_formulir : Uniformvaluee,
                   }
                   var token2 = jwt_encode(data2,"UAP)(*");
                   $.post(url2,{token:token2},function (json) {
                        console.log(Grade);
                        var json2 = JSON.parse(json);
                        var bagi = 12 / json2.length;
                        var bagi = parseInt(bagi);
                        var divNilai = '<div class = "row" style = "margin-top:10px;margin-left:0px;margin-right:0px">'+
                                            '<div class = "col-md-12">'+
                                                '<h4>Nilai</h4>'+
                                                '<div class = "row">';
                        var l = 0;
                        var jml_total = 0;
                        for (var i = 0; i < json2.length; i++) {
                                // divNilai += '<li>'+json2[i]['NamaUjian']+ ' : '+json2[i]['Value']+'</li>';
                                if (l > 12) {
                                    var ascdee = parseInt(l) - 12;
                                    divNilai += '<div class = "col-xs-'+ascdee+'">'+
                                        '<label>'+json2[i]['NmMtPel']+' : '+json2[i]['Value']+'</label>'+
                                        '</div>';
                                }
                                else
                                {
                                    divNilai += '<div class = "col-xs-'+bagi+'">'+
                                        '<label>'+json2[i]['NmMtPel']+ ' : '+json2[i]['Value']+'</label>'+
                                        '</div>';
                                    l = parseInt(l) + parseInt(bagi);    
                                }

                            l++; 
                            // get total
                            jml_total = jml_total + parseInt(json2[i]['Value']);
                        }
                        divNilai += '</div></div></div>';

                        var rata_rata = jml_total / json2.length;
                        rata_rata = parseFloat(rata_rata).toFixed(2);
                        divNilai += '<div class = "row" style = "margin-top:10px;margin-left:0px;margin-right:0px">'+
                                        '<div class = "col-md-12">'+
                                            '<h4>Average</h4>'+
                                            '<div class = "row">'+
                                                '<div class = "col-xs-2">'+
                                                    '<label>Jumlah Bobot : '+jml_total+'</label>'+
                                                '</div>'+
                                                '<div class = "col-xs-2">'+
                                                    '<label>Rata-Rata : '+rata_rata+'</label>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div></div>';    


                        var bbb = '';
                        for (var i = 0; i < obj.length; i++) {
                            bbb += '<tr>'+
                                      '<td>'+ (parseInt(i)+1) + '</td>'+
                                      '<td>'+ formatRupiah(obj[i]['Invoice']) + '</td>'+
                                      '<td>'+ obj[i]['Deadline']+'</td>'+
                                    '</tr>';  
                        }
                        var aaa = '<div class = "row" style = "margin-right : 0px;margin-left : 0px;margin-top:10px">'+
                                    '<div class = "col-md-12">'+
                                        '<h4>Payment</h4>'+  
                                     '<div id = "tblData" class="table-responsive">'+
                                         '<table class="table table-striped table-bordered table-hover table-checkable">'+
                                         '<thead>'+
                                           '<tr>'+
                                             '<th style="width: 5px;">Cicilan ke </th>'+
                                             '<th style="width: 5px;">Invoice </th>'+
                                             '<th style="width: 5px;">Deadline </th>'+
                                            '<tr>'+ 
                                         '</thead>'+
                                         '<tbody>'+
                                         bbb+
                                         '</tbody>'+'</table></div>'+
                                    '</div>'+        
                                  '</div>';

                        var html = '<div class="widget box widget_'+Uniformvaluee+' widget_delete">'+
                            '<div class="widget-header">'+
                                '<h4 class="header"><i class="icon-reorder"></i> Detail Nilai & Payment '+Nama+'</h4>'+
                            '</div>'+
                            '<div class="widget-content">'+
                                divNilai+aaa
                            '</div>'+
                        '</div>';
                        $(".formAddFormKD").append(html);
                        $('html, body').animate({ scrollTop: $(".widget_"+Uniformvaluee).offset().top }, 'slow');


                   }) // exit post nilai
   
               }).done(function() {
                 
               }).fail(function() {
                
                 toastr.error('The Database connection error, please try again', 'Failed!!');
               }).always(function() {
                
               });
            }
            else
            {
                $(".widget_"+Uniformvaluee).remove();
            }  
         break;      
         case  "tuition_fee_approved" :
             var Nama = $(this).attr('nama');
             if(this.checked) {
                var url = base_url_js + "get_detail_cicilan_fee_admisi";
                var data = {
                    ID_register_formulir : Uniformvaluee,
                }
                var token = jwt_encode(data,"UAP)(*");
                $.post(url,{token:token},function (data_json) {
                    // jsonData = data_json;
                    var obj = JSON.parse(data_json);
                    console.log(obj);
                    var bbb = '';
                    for (var i = 0; i < obj.length; i++) {
                        bbb += '<tr>'+
                                  '<td>'+ (parseInt(i)+1) + '</td>'+
                                  '<td>'+ formatRupiah(obj[i]['Invoice']) + '</td>'+
                                  '<td>'+ obj[i]['Deadline']+'</td>'+
                                '</tr>';  
                    }
                    var aaa = '<div class = "row" style = "margin-right : 0px;margin-left : 0px;margin-top:0px">'+
                                '<div class = "col-md-12">'+    
                                 '<div id = "tblData" class="table-responsive">'+
                                     '<table class="table table-striped table-bordered table-hover table-checkable">'+
                                     '<thead>'+
                                       '<tr>'+
                                         '<th style="width: 5px;">Cicilan ke </th>'+
                                         '<th style="width: 5px;">Invoice </th>'+
                                         '<th style="width: 5px;">Deadline </th>'+
                                        '<tr>'+ 
                                     '</thead>'+
                                     '<tbody>'+
                                     bbb+
                                     '</tbody>'+'</table></div>'+
                                '</div>'+     
                              '</div>';

                    var html = '<div class="widget box widget_'+Uniformvaluee+' widget_delete">'+
                        '<div class="widget-header">'+
                            '<h4 class="header"><i class="icon-reorder"></i> Detail Payment '+Nama+'</h4>'+
                        '</div>'+
                        '<div class="widget-content">'+
                            aaa
                        '</div>'+
                    '</div>';
                    $(".formAddFormKD").append(html);
                    $('html, body').animate({ scrollTop: $(".widget_"+Uniformvaluee).offset().top }, 'slow');
                }).done(function() {
                  
                }).fail(function() {
                 
                  toastr.error('The Database connection error, please try again', 'Failed!!');
                }).always(function() {
                 
                });
             }
             else
             {
                 $(".widget_"+Uniformvaluee).remove();
             } 
         break;
        }
        
               
    });

    $(document).on('click','.show_a_href', function () {
        var file__  = $(this).attr('filee');
        var aaa = file__.split(",");
        if (aaa.length > 0) {
            var emaiil = $(this).attr('Email');
            for (var i = 0; i < aaa.length; i++) {
                window.open('<?php echo url_registration ?>'+'document/'+emaiil+'/'+aaa[i],'_blank');
            }
            
        }
        else
        {
            window.open('<?php echo url_registration ?>'+'document/'+emaiil+'/'+file__,'_blank');
        }
        
    });

    $(document).on('click','.showModal', function () {
      var ID_register_formulir = $(this).attr('id-register-formulir');
      var html = '';
      var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                    '<thead>'+
                        '<tr>'+
                            '<th style="width: 5px;">No</th>'+
                            '<th style="width: 55px;">Note</th>'+
                            '<th style="width: 55px;">Rev By</th>'+
                            '<th style="width: 55px;">Rev At</th>';
      table += '</tr>' ;  
      table += '</thead>' ; 
      table += '<tbody>' ;

      var url = base_url_js+'finance/getRevision_detail_admission';
      var data = {
          ID_register_formulir : ID_register_formulir,
      };
      var token = jwt_encode(data,'UAP)(*');
      $.post(url,{token:token},function (resultJson) {
         var DetailArr = jQuery.parseJSON(resultJson);
         
         var isi = '';
         for (var j = 0; j < DetailArr.length; j++) {
           isi += '<tr>'+
                   '<td>'+DetailArr[j]['RevNo'] + '</td>'+
                   '<td>'+DetailArr[j]['Note'] + '</td>'+
                   '<td>'+DetailArr[j]['Name'] + '</td>'+
                   '<td>'+DetailArr[j]['RevAt'] + '</td>'+
                '<tr>'; 
         }

         table += isi+'</tbody>' ; 
         table += '</table>' ;

         html += table;

         var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
             '';

         $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Detail Revision</h4>');
         $('#GlobalModalLarge .modal-body').html(html);
         $('#GlobalModalLarge .modal-footer').html(footer);
         $('#GlobalModalLarge').modal({
             'show' : true,
             'backdrop' : 'static'
         });   

      }).fail(function() {
        toastr.info('No Action...'); 
        // toastr.error('The Database connection error, please try again', 'Failed!!');
      }).always(function() {

      });

    });
    
</script>