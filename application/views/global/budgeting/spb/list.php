<div class="row btn-read">
    <div class="col-md-6 col-md-offset-3">
      <div class="thumbnail">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Year</label>
              <select class="select2-select-00 full-width-fix" id="Years">
                   <!-- <option></option> -->
               </select>
            </div>  
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Month</label>
              <select class="select2-select-00 full-width-fix" id="Month">
                   <!-- <option></option> -->
               </select>
            </div>  
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Event / Template</label>
              <select class="form-control SelectTemplate">
                   <option value = "%" selected>--No Selected--</option>
               </select>
            </div>  
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row btn-read">
	<div class="col-md-12">
		<div class="table-responsive" id = "DivTable">
			
		</div>
	</div>
</div>
<script type="text/javascript">
var G_Approver = <?php echo json_encode($G_Approver) ?>;
var m_type_user = <?php echo json_encode($m_type_user) ?>;
var G_ApproverLength = G_Approver.length;
var JsonStatus = [];
// get Departmentpu
var IDDepartementPUBudget = "<?php echo $this->session->userdata('IDDepartementPUBudget') ?>";
$(document).ready(function() {
	LoadFirstLoad();
	loadingEnd(500);
}); // exit document Function

function LoadFirstLoad()
{
  load_table_activated_period_years();
  __LoadTemplate().then(function(dataTemplate){
      var h = '';
          for (var i = 0; i < dataTemplate.length; i++) {
              h += '<option value = "'+dataTemplate[i].ID+'" '+''+' >'+dataTemplate[i].Name+'</option>';
          }
      $('.SelectTemplate').append(h);

    })
	// LoadDataForTable();
}

function __LoadTemplate()
{
  var def = jQuery.Deferred();
  var url = base_url_js+'rest2/__LoadTemplate';
  var data = {
      auth : 's3Cr3T-G4N',
      Active : 1,
  };
  var token = jwt_encode(data,"UAP)(*");
  $.post(url,{ token:token },function (resultJson) {
    
  }).done(function(resultJson) {
    def.resolve(resultJson);
  }).fail(function() {
    toastr.info('No Result Data');
    def.reject();  
  }).always(function() {
                  
  }); 
  return def.promise();
}  

function load_table_activated_period_years()
{
   // load Year
   $("#Years").empty();
   var url = base_url_js+'budgeting/table_all/cfg_dateperiod/1';
   var thisYear = (new Date()).getFullYear();
   for (var i = 2019; i <= thisYear; i++) {
     var selected = (i==0) ? 'selected' : '';
     $('#Years').append('<option value="'+i+'" '+selected+'>'+i+'</option>');
   }

   $('#Years').select2({
      //allowClear: true
   });

   // load bulan
   var S_bulan = $('#Month');
   SelectOptionloadBulan(S_bulan,'choice');

   LoadDataForTable();
   // $.post(url,function (resultJson) {
   //  var response = jQuery.parseJSON(resultJson);
   //  for(var i=0;i<response.length;i++){
   //      //var selected = (i==0) ? 'selected' : '';
   //      var selected = (response[i].Activated==1) ? 'selected' : '';
   //      $('#Years').append('<option value="'+response[i].Year+'" '+selected+'>'+response[i].Year+'</option>');
   //  }
   //  $('#Years').select2({
   //     //allowClear: true
   //  });

   //  // load bulan
   //  var S_bulan = $('#Month');
   //  SelectOptionloadBulan(S_bulan,'choice');

   //  LoadDataForTable();
   // }); 
}

$(document).off('change', '#Years,.SelectTemplate').on('change', '#Years,.SelectTemplate',function(e) {
  LoadDataForTable();
})

$(document).off('change', '#Month').on('change', '#Month',function(e) {
  LoadDataForTable();
})

function LoadDataForTable()
{
	$("#DivTable").empty();
	var LoopApprover = '';
	for (var i = 0; i < G_ApproverLength; i++) {
		var ap = i +1;
		LoopApprover += '<th style = "text-align: center;background: #20485A;color: #FFFFFF;" id = "thapprover'+ap+'">'+ap+'</th>';
	}

	var table_html = '<table class="table table-bordered" id = "tableData_po" style="width: 100%;">'+
	            '<thead>'+
	            '<tr>'+
	                '<th rowspan = "2" width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
	                '<th rowspan = "2" style = "text-align: center;background: #20485A;color: #FFFFFF;">Code</th>'+
	                '<th rowspan = "2" style = "text-align: center;background: #20485A;color: #FFFFFF;">Department</th>'+
	                '<th rowspan = "2" style = "text-align: center;background: #20485A;color: #FFFFFF;">Status</th>'+
	                '<th rowspan = "2" style = "text-align: center;background: #20485A;color: #FFFFFF;">Info</th>'+
	                '<th colspan = "'+G_ApproverLength+'" style = "text-align: center;background: #20485A;color: #FFFFFF;" id = "parent_th_approver">Approver</th>'+
	            '</tr>'+
	            '<tr>'+
	            	LoopApprover+
	            '</tr>'+	
	            '</thead>'+
	            '<tbody id="dataRow"></tbody>'+
	        '</table>';
	$("#DivTable").html(table_html);
  var Years = $('#Years option:selected').val();
  var Month = $('#Month option:selected').val();
  var SelectTemplate = $('.SelectTemplate option:selected').val();
	var data = {
    	IDDepartementPUBudget : IDDepartementPUBudget,
    	sessionNIP : sessionNIP,
	    auth : 's3Cr3T-G4N',
	    length : G_ApproverLength,
      Years : Years,
      Month : Month,
      SelectTemplate : SelectTemplate,
	};
  // console.log(data);return;
	var token = jwt_encode(data,"UAP)(*");

	var arr_add = [];

	var table = $('#tableData_po').DataTable({
		"fixedHeader": true,
	    "processing": true,
	    "destroy": true,
	    "serverSide": true,
      "lengthMenu": [[5], [5]],
	    "iDisplayLength" : 5,
	    "ordering" : false,
      "language": {
          "searchPlaceholder": "PRCode, PO Code & SPB Code",
      },
	    "ajax":{
	        url : base_url_js+"rest2/__get_data_spb", // json datasource
	        ordering : false,
	        type: "post",  // method  , by default get
	        data : {token : token},
	        error: function(){  // error handling
	            $(".employee-grid-error").html("");
	            $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
	            $("#employee-grid_processing").css("display","none");
	        }
	    },
   	    'createdRow': function( row, data, dataIndex ) {
   	    	$( row ).find('td:eq(0)').attr('align','center');
          var ListPR = data[parseInt(data.length) - 1];
          var PRHTML = '';
          if (ListPR[0].length > 0) {
            PRHTML += '<li>'+ListPR[0]+'</li>';
          }
          var ID_payment = ListPR[1].ID_payment;
          var CodeSPB = ListPR[1].CodeSPB;
          var TypeCode = ListPR[1].TypeCode;
          var code_url2 = findAndReplace(CodeSPB,'/','-');
          var Code_po_create = '';
          if (data[1] != null && data[1] != '') {
          	var Code_po_create = data[1];
          }

          if (ListPR[0].length > 0) {
            var URLRedirect = 'global/purchasing/transaction/spb/list/'+code_url2;
          }
          else
          {
            code_url2 = jwt_encode(ID_payment,"UAP)(*");
            var URLRedirect = 'budgeting_menu/pembayaran/spb/'+code_url2;
          }

   	    	$( row ).find('td:eq(1)').html('<div align = "left">'+'<a href="'+base_url_js+URLRedirect+'" code="'+CodeSPB+'">'+CodeSPB+'</a><br>'+'<label>'+Code_po_create+'</label><br>Created : '+data[parseInt(data.length) - 2]+'<br>'+PRHTML+'</div>');
   	    	
   	    	$( row ).find('td:eq(2)').attr('align','center');
   	    	$( row ).find('td:eq(4)').attr('align','center');
   	    	$( row ).find('td:eq(4)').html('<a href="javascript:void(0)" class="btn btn-info btn_circulation_sheet" id_payment="'+ID_payment+'">Info</a>');
   	    		
   	    },
        dom: 'l<"toolbar">frtip',
   	    "initComplete": function(settings, json) {

   	    }
	});
}

	$(document).off('click', '.btn_circulation_sheet').on('click', '.btn_circulation_sheet',function(e) {
	    var url = base_url_js+'rest2/__show_info_payment';
	    var ID_payment = $(this).attr('id_payment');
   		var data = {
   		    ID_payment : ID_payment,
   		    auth : 's3Cr3T-G4N',
   		};
   		var token = jwt_encode(data,"UAP)(*");
   		$.post(url,{ token:token },function (data_json) {
   			var html = '<div class = "row"><div class="col-md-12"><div class="well">';
   				html += '<table class="table table-striped table-bordered table-hover table-checkable tableData" id = "TblModal">'+
                      '<caption><h4>Circulation Sheet</h4></caption>'+
                      '<thead>'+
                          '<tr>'+
                              '<th style="width: 5px;">No</th>'+
                              '<th style="width: 55px;">Desc</th>'+
                              '<th style="width: 55px;">Date</th>'+
                              '<th style="width: 55px;">By</th>';
		        html += '</tr>' ;
		        html += '</thead>' ;
		        html += '<tbody>' ;
		        html += '</tbody>' ;
		        html += '</table></div></div></div>' ;

   			var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
   			    '';
   			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Info Payment'+'</h4>');
   			$('#GlobalModalLarge .modal-body').html(html);
   			$('#GlobalModalLarge .modal-footer').html(footer);
   			$('#GlobalModalLarge').modal({
   			    'show' : true,
   			    'backdrop' : 'static'
   			});

   			// make datatable
   				var table = $('#TblModal').DataTable({
   				      "data" : data_json['payment_circulation_sheet'],
   				      'columnDefs': [
   					      {
   					         'targets': 0,
   					         'searchable': false,
   					         'orderable': false,
   					         'className': 'dt-body-center',
   					         'render': function (data, type, full, meta){
   					             return '';
   					         }
   					      },
   					      {
   					         'targets': 1,
   					         'render': function (data, type, full, meta){
   					             return full.Desc;
   					         }
   					      },
   					      {
   					         'targets': 2,
   					         'render': function (data, type, full, meta){
   					             return full.Date;
   					         }
   					      },
   					      {
   					         'targets': 3,
   					         'render': function (data, type, full, meta){
   					             return full.Name;
   					         }
   					      },
   				      ],
   				      'createdRow': function( row, data, dataIndex ) {
   				      		$(row).find('td:eq(0)').attr('style','width : 10px;')
   				      	
   				      },
   				});

   				table.on( 'order.dt search.dt', function () {
   				        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
   				            cell.innerHTML = i+1;
   				        } );
   				} ).draw();

   		});
	})
</script>