<!-- <?php $AuthDivisionCrud = array(16,12) ?> -->
<style type="text/css">
    #datatable_STD.dataTable tbody tr:hover {
       background-color:#71d1eb !important;
       cursor: pointer;
    }

    #datatable_employees.dataTable tbody tr:hover {
       background-color:#71d1eb !important;
       cursor: pointer;
    }
</style>
<div>
		<!-- Breadcrumbs line -->

		<!-- /Breadcrumbs line -->

		<!--=== Page Header ===-->
		<!-- <div class="page-header">
			<div class="page-title">
				<h3><b>Setting</b></h3>
				
			</div>
		</div> -->
		<!-- /Page Header -->

		<!-- Page Content -->
		<div class="row">
			<div class="col-md-4">
				<div class="widget box">
				    <div class="widget-header">
				        <h4 class="header"><i class="icon-reorder"></i>Master Group</h4>
				         
				        	<div class="toolbar no-padding">
				        	    <div class="btn-group">
				        	      <span data-smt="" class="btn btn-xs btn-add-master-group">
				        	        <i class="icon-plus"></i> Add
				        	       </span>
				        	    </div>
				        	</div>
				         
				    </div>
				    <div class="widget-content">
				       <div class="row">
				       	<div class="col-md-12">
				       		<div style="overflow-x: auto;">
				       			<table class="table table-striped table-bordered table-hover table-checkable  datatable" id="TableMasterGroup">
				       				<thead>
				       					<tr>
				       						<th>No</th>
				       						<th>Group Name</th>
				       						<th>Action</th>
				       					</tr>
				       				</thead>
				       				<tbody></tbody>
				       			</table>	
				       		</div>
				       	</div>
				       </div>
				    </div>
				    <hr/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="widget box">
				    <div class="widget-header">
				        <h4 class="header"><i class="icon-reorder"></i>Group</h4>
				        
				        	<div class="toolbar no-padding">
				        	    <div class="btn-group">
				        	      <span data-smt="" class="btn btn-xs btn-add-group">
				        	        <i class="icon-plus"></i> Add
				        	       </span>
				        	    </div>
				        	</div>
				        
				    </div>
				    <div class="widget-content">
				       <div class="row">
				       	<div class="col-md-12">
				       		<div style="overflow-x: auto;">
				       			<table class="table table-striped table-bordered table-hover table-checkable  datatable" id="TableGroup">
				       				<thead>
				       					<tr>
				       						<th>No</th>
				       						<th>Group Name</th>
				       						<th>Action</th>
				       					</tr>
				       				</thead>
				       				<tbody></tbody>
				       			</table>	
				       		</div>
				       	</div>
				       </div>
				    </div>
				    <hr/>
				</div>
			</div>
			<div class="col-md-4">
				<div class="widget box">
				    <div class="widget-header">
				        <h4 class="header"><i class="icon-reorder"></i>Member</h4>
				        
				        	<div class="toolbar no-padding">
				        	    <div class="btn-group">
				        	      <span data-smt="" class="btn btn-xs btn-add-member">
				        	        <i class="icon-plus"></i> Add
				        	       </span>
				        	    </div>
				        	</div>
				        
				    </div>
				    <div class="widget-content">
				       <div class="row">
				       	<div class="col-md-12">
				       		<div style="overflow-x: auto;">
				       			<table class="table table-striped table-bordered table-hover table-checkable  datatable" id="TableMember">
				       				<thead>
				       					<tr>
				       						<th>No</th>
				       						<th>Member Level</th>
				       						<th>Number</th>
				       						<th>Action</th>
				       					</tr>
				       				</thead>
				       				<tbody></tbody>
				       			</table>	
				       		</div>
				       	</div>
				       </div>
				    </div>
				    <hr/>
				</div>
			</div>
		</div>

		<div class="row" style="margin-top: 10px;">
			<div class="col-md-12">
				<div class="widget box">
				    <div class="widget-header">
				        <h4 class="header"><i class="icon-reorder"></i>List Member</h4>
				        
				        	<div class="toolbar no-padding">
				        	    <div class="btn-group">
				        	      <span data-smt="" class="btn btn-xs btn-add-list-member">
				        	        <i class="icon-plus"></i> Add
				        	       </span>
				        	    </div>
				        	</div>
				        
				    </div>
				    <div class="widget-content">
				    	<div class="row">
				    		<div class="col-md-6 col-md-offset-3">
				    			<div class="well">
				    				<div class="row">
				    					<div class="col-md-4">
				    						<div class="form-group">
				    							<label>Choose Maste Group</label>
				    							<select class="form-control" id ="ChooseMasterGroup">
				    								
				    							</select>
				    						</div>
				    					</div>
				    					<div class="col-md-4">
				    						<div class="form-group">
				    							<label>Choose Group</label>
				    							<select class="form-control" id ="ChooseGroup">
				    								
				    							</select>
				    						</div>
				    					</div>
				    					<div class="col-md-4">
				    						<div class="form-group">
				    							<label>Choose Member Level</label>
				    							<select class="form-control" id ="ChooseMember">
				    								
				    							</select>
				    						</div>
				    					</div>
				    				</div>
				    			</div>
				    		</div>
				    	</div>

				    	<div class="row" style="margin-top: 10px;">
				    		<div class="col-md-12">
				    			<div style="overflow-x: auto;">
				    				<table class="table table-striped table-bordered table-hover table-checkable  datatable" id="TableListMember">
				    					<thead>
				    						<tr>
				    							<th>No</th>
				    							<th>NIP / NPM</th>
				    							<th>MemberListName</th>
				    							<th>Level Member</th>
				    							<th>GroupMasterName</th>
				    							<th>GroupName</th>
				    							<th>NameUpdateBY</th>
				    							<th>UpdateAT</th>
				    							<th>Action</th>
				    						</tr>
				    					</thead>
				    					<tbody></tbody>
				    				</table>
				    			</div>
				    		</div>
				    	</div>		
				    </div>
				    <hr/>
				</div>
			</div>
		</div>
		<!-- /Page Content -->
	<!-- /.container -->

</div>
<script type="text/javascript">
	var oTableListMember;
	var oTableMasterGroup;
	var oTableGroup;
	var oTableMember;
	var App_list_member = {
		Loaded : function(){
			var firstLoad = setInterval(function () {
	            var ChooseGroup = $('#ChooseGroup').val();
	            var ChooseMasterGroup = $('#ChooseMasterGroup').val();
	            var ChooseMember = $('#ChooseMember').val();
	            if(ChooseGroup!='' && ChooseGroup!=null && ChooseMasterGroup!='' && ChooseMasterGroup!=null && ChooseMember!='' && ChooseMember!=null ){
	                /*
	                    LoadAction
	                */
	                App_list_member.LoadTable();
	                clearInterval(firstLoad);
	            }
	        },200);
	        setTimeout(function () {
	            clearInterval(firstLoad);
	        },5000);
		},

		LoadTable : function(){
			var recordTable = $('#TableListMember').DataTable({
			    "processing": true,
			    "serverSide": false,
			    "ajax":{
			        url : base_url_js+"__data_setting_listmember", // json datasource
			        ordering : false,
			        type: "post",  // method  , by default get
			        data : function(token){
			              // Read values
			               var data = {
			               		  action : 'datatables',
			               		  param : {
			               		  	ChooseGroup : $('#ChooseGroup option:selected').val(),
			               		  	ChooseMasterGroup : $('#ChooseMasterGroup option:selected').val(),
			               		  	ChooseMember : $('#ChooseMember option:selected').val(),
			               		  },
			                  };
			              // Append to data
			              token.token = jwt_encode(data,'UAP)(*');
			        }                                                                     
			     },
			      'columnDefs': [
			      	
			      	{
			      	   'targets': 0,
			      	   'searchable': false,
			      	   'orderable': false,
			      	   'className': 'dt-body-center',
			      	},
			        {
			            'targets': 7,
			            'searchable': false,
			            'orderable': false,
			            'className': 'dt-body-center',
			            'render': function (data, type, full, meta){
			            	   var btnaction = '';
			            	   // <?php if (in_array($this->session->userdata('DivisionID') , $AuthDivisionCrud)): ?>
			            	   		var btnEdit = '<button class = "btn btn-info btn-sm btnEditListMember" data-id="'+full[8]+'" datatoken = "'+full[9]+'" >Edit</button>';
			            	   		var btnDelete = '<button class = "btn btn-danger btn-sm btnDeleteListMember" data-id="'+full[8]+'">Delete</button> ';
			            	   		btnaction = btnEdit+' '+btnDelete;
			            	   // <?php endif ?>
			            	   return btnaction;
			            }
			        },
			         
			      ],
			    'createdRow': function( row, data, dataIndex ) {
			            
			    },
			    dom: 'l<"toolbar">frtip',
			    initComplete: function(){
			      
			    }  
			});

			oTableListMember = recordTable;
		},

		Form_modal : function(action='add',ID='',datatoken=''){
			var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
		              '<button type="button" id="ModalbtnSaveFormListMember" class="btn btn-success" action="'+action+'" data-id="'+ID+'">Save</button>'; 
		    var html = '<div class="row" page = "FrmListMember">'+
		    				'<div class = "col-md-12">'+
		    					'<div class = "form-group">'+
		    						'<label>Choose Master Group</label>'+
		    						'<select class = "form-control FrmListMember" id="ID_master_group" name = "ID_master_group"></select>'+
		    					'</div>'+
		    					'<div class = "form-group">'+
		    						'<label>Choose Group</label>'+
		    						'<select class = "form-control FrmListMember" id="ID_set_group" name = "ID_set_group"></select>'+
		    					'</div>'+
		    					'<div class = "form-group">'+
		    						'<label>Choose Level Member</label>'+
		    						'<select class = "form-control FrmListMember" name = "ID_set_member"></select>'+
		    					'</div>'+
		    					'<div class = "form-group">'+
		    						'<label>Type Member</label>'+
		    						'<select class="form-control" id ="TypeMember">'+
		    							'<option value="Employees">Employees</option>'+	
		    							'<option value="Student">Student</option>'+	
		    						'</select>'+
		    					'</div>'+
		    					'<div class = "form-group">'+
		    					    '<label>Choose Employees / Student</label>'+
		    					    '<div class="input-group">'+
		    					        '<input type="text" class="form-control FrmListMember" readonly name="NIPNPM">'+
		    					        '<span class="input-group-btn">'+
		    					            '<button class="btn btn-default SearchNIPNPM" type="button"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button>'+
		    					        '</span>'+
		    					    '</div>'+
		    					    '<label for="Name"></label>'+
		    					'</div>'+
		    				'</div>'+
		    			'</div>';	

			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Form List Member'+'</h4>');
			$('#GlobalModalLarge .modal-body').html(html);
			$('#GlobalModalLarge .modal-footer').html(footer);
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});
			var selectorOPGroup = $('.FrmListMember[name="ID_set_group"]');
			var selectorOPMasterGroup = $('.FrmListMember[name="ID_master_group"]');
			var selectorOPMember = $('.FrmListMember[name="ID_set_member"]');
			App_group.LoadSelectOP(selectorOPGroup,'','');
			App_master_group.LoadSelectOP(selectorOPMasterGroup,'','');
			App_member.LoadSelectOP(selectorOPMember,'','');
			if(action == 'edit'){
				$('#GlobalModalLarge').find('input').prop('disabled',true);
				$('#GlobalModalLarge').find('select').prop('disabled',true);
				$('#GlobalModalLarge').find('#ModalbtnSaveFormListMember').prop('disabled',true);
				$('#GlobalModalLarge').find('button').prop('disabled',true);
				var dt = jwt_decode(datatoken);
				var firstLoad = setInterval(function () {
			            var ChooseGroup = selectorOPGroup.val();
			            var ChooseMasterGroup = selectorOPMasterGroup.val();
			            var ChooseMember = selectorOPMember.val();
			            if(ChooseGroup!='' && ChooseGroup!=null && ChooseMasterGroup!='' && ChooseMasterGroup!=null && ChooseMember!='' && ChooseMember!=null ){
			                for (key in dt){
			                	if ($('.FrmListMember[name="'+key+'"]').length && $('.FrmListMember[name="'+key+'"]').is('select') ) {
			                		$(".FrmListMember[name='"+key+"'] option").filter(function() {
			                		   //may want to use $.trim in here
			                		   return $(this).val() == dt[key]; 
			                		}).prop("selected", true);
			                	}

			                	if ($('.FrmListMember[name="'+key+'"]').length && $('.FrmListMember[name="'+key+'"]').is('input') ) {
			                		$('.FrmListMember[name="'+key+'"]').val(dt[key]);
			                		$('.FrmListMember[name="'+key+'"]').closest('.form-group').find('label[for="Name"]').html(dt['MemberListName']);
			                	}

			                	if (key == 'TypeUser') {
			                		$("#TypeMember option").filter(function() {
			                		   //may want to use $.trim in here
			                		   return $(this).val() == dt[key]; 
			                		}).prop("selected", true);
			                	}
			                	
			                }	
			                $('#GlobalModalLarge').find('input').prop('disabled',false);
			                $('#GlobalModalLarge').find('select').prop('disabled',false);
			                $('#GlobalModalLarge').find('#ModalbtnSaveFormListMember').prop('disabled',false);
			                $('#GlobalModalLarge').find('button').prop('disabled',false);
			                clearInterval(firstLoad);
			            }
		        },200);
		        setTimeout(function () {
		            clearInterval(firstLoad);
		        },5000);
				
			}

		},

		searchMember : function(selector){
			var TypeMember = $('#TypeMember option:selected').val();
			if (TypeMember == 'Employees') {
				App_list_member.SearchNIPEMP(selector);
			}
			else
			{
				App_list_member.SearchNPMSTD(selector);
			}
		},

		SearchNIPEMP : function(selector){
		      var html = '';
		      html ='<div class = "row">'+
		              '<div class = "col-md-12">'+
		                '<div class="">'+
		                  '<table id="datatable_employees" class="table table-bordered display select" cellspacing="0" width="100%">'+
		         '<thead>'+
		            '<tr>'+
		               '<th style = "width:5%;">No</th>'+
		               '<th>NIP - Name</th>'+
		               '<th>Division</th>'+
		               '<th>Position</th>'+
		            '</tr>'+
		         '</thead>'+
		         '<tbody></tbody>'+
		    	'</table></div></div></div>';

		      $('#GlobalModalXtraLarge .modal-header').html('<h4 class="modal-title">'+'Select Employees'+'</h4>');
		      $('#GlobalModalXtraLarge .modal-body').html(html);
		      $('#GlobalModalXtraLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>');
		      $('#GlobalModalXtraLarge').modal({
		          'show' : true,
		          'backdrop' : 'static'
		      });
		      var urlPcam = 'https://pcam.podomorouniversity.ac.id/';
		      var table = $('#datatable_employees').DataTable({
		          "fixedHeader": true,
		          "processing": true,
		          "destroy": true,
		          "serverSide": true,
		          "lengthMenu": [
		              [10, 25],
		              [10, 25]
		          ],
		          "iDisplayLength": 10,
		          "ordering": false,
		          "language": {
		              "searchPlaceholder": "Search",
		          },
		          "ajax": {
		              url: urlPcam + "rest3/__LoadEmployees_server_side", // json datasource
		              ordering: false,
		              type: "post", // method  , by default get
		              data: function(token) {
		                  var data = {
		                      auth: 's3Cr3T-G4N',
		                  };
		                  var get_token = jwt_encode(data, "UAP)(*");
		                  token.token = get_token;
		              },
		              error: function() { // error handling
		                  $(".datatable_employees-grid-error").html("");
		                  $("#datatable_employees-grid").append(
		                      '<tbody class="datatable_employees-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
		                  );
		                  $("#datatable_employees-grid_processing").css("display", "none");
		              }
		          },
		          'createdRow': function(row, data, dataIndex) {
		              var dt = jwt_decode(data.data);
		              $(row).attr('datatoken',data.data);
		              $(row).find('td:eq(1)').html(dt['NIP']+' - '+dt['Name']);
		              $(row).find('td:eq(2)').html(dt['DepartmentName']);
		              $(row).find('td:eq(3)').html(dt['PositionName']);
		          },
		          dom: 'l<"toolbar">frtip',
		          "initComplete": function(settings, json) {

		          }
		      });

		     var S_Table_example_emp = table;

		      S_Table_example_emp.on( 'click', 'tr', function (e) {
		          var row = $(this);
		          var datatoken = jwt_decode(row.attr('datatoken'));
		          selector.closest('.input-group').find('.FrmListMember').val(datatoken['NIP']);
		          selector.closest('.input-group').find('.FrmListMember').attr('datatoken',row.attr('datatoken'));
		          selector.closest('.form-group').find('label[for="Name"]').html(datatoken['Name']);
		          $('#GlobalModalXtraLarge').modal('hide');
		      });

		},

		SearchNPMSTD : function(selector){
		      var html = '';
		      html ='<div class = "row">'+
		              '<div class = "col-md-12">'+
		                '<div class="">'+
		                  '<table id="datatable_STD" class="table table-bordered display select" cellspacing="0" width="100%">'+
		         '<thead>'+
		            '<tr>'+
		               '<th style = "width:5%;">No</th>'+
		               '<th>NPM - Name</th>'+
		               '<th>Prodi</th>'+
		            '</tr>'+
		         '</thead>'+
		         '<tbody></tbody>'+
		    	'</table></div></div></div>';

		      $('#GlobalModalXtraLarge .modal-header').html('<h4 class="modal-title">'+'Select Students'+'</h4>');
		      $('#GlobalModalXtraLarge .modal-body').html(html);
		      $('#GlobalModalXtraLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>');
		      $('#GlobalModalXtraLarge').modal({
		          'show' : true,
		          'backdrop' : 'static'
		      });
		      var urlPcam = 'https://pcam.podomorouniversity.ac.id/';
		      var table = $('#datatable_STD').DataTable({
		          "fixedHeader": true,
		          "processing": true,
		          "destroy": true,
		          "serverSide": true,
		          "lengthMenu": [
		              [10, 25],
		              [10, 25]
		          ],
		          "iDisplayLength": 10,
		          "ordering": false,
		          "language": {
		              "searchPlaceholder": "Search",
		          },
		          "ajax": {
		              url: urlPcam + "rest3/__LoadStudents_server_side", // json datasource
		              ordering: false,
		              type: "post", // method  , by default get
		              data: function(token) {
		                  var data = {
		                      auth: 's3Cr3T-G4N',
		                  };
		                  var get_token = jwt_encode(data, "UAP)(*");
		                  token.token = get_token;
		              },
		              error: function() { // error handling
		                  $(".datatable_STD-grid-error").html("");
		                  $("#datatable_STD-grid").append(
		                      '<tbody class="datatable_STD-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
		                  );
		                  $("#datatable_STD-grid_processing").css("display", "none");
		              }
		          },
		          'createdRow': function(row, data, dataIndex) {
		              var dt = jwt_decode(data.data);
		              $(row).attr('datatoken',data.data);
		              $(row).find('td:eq(1)').html(dt['NPM']+' - '+dt['Name']);
		              $(row).find('td:eq(2)').html(dt['ProdiName']);
		          },
		          dom: 'l<"toolbar">frtip',
		          "initComplete": function(settings, json) {

		          }
		      });

		      S_Table_example_mhs = table;

		      S_Table_example_mhs.on( 'click', 'tr', function (e) {
		          var row = $(this);
		          var datatoken = jwt_decode(row.attr('datatoken'));
		          selector.closest('.input-group').find('.FrmListMember').val(datatoken['NPM']);
		          selector.closest('.input-group').find('.FrmListMember').attr('datatoken',row.attr('datatoken'));
		          selector.closest('.form-group').find('label[for="Name"]').html(datatoken['Name']);
		          $('#GlobalModalXtraLarge').modal('hide');
		      });

		},

		SubmitData : function(selector,action,ID){
			var data = {};
			$('.FrmListMember').each(function(e){
				var name = $(this).attr('name');
				if ($(this).is('input')) {
					var v = $(this).val();
				}

				if ($(this).is('select')) {
					var v = $(this).find('option:selected').val();
				}

				data[name] =  v;

			})

			var validation = (action == 'delete') ? true : App_list_member.validation(data);
			if (validation) {
			    if (confirm('Are you sure ?')) {
			        loading_button2(selector);
			        var dataform = {
			            action : action,
			            data : data,
			            ID : ID,
			        };
			        var url = base_url_js+"__data_setting_listmember";
			        var token = jwt_encode(dataform,'UAP)(*');
			        AjaxSubmit(url,token).then(function(response){
			            if (response.status == 1) {
			            	toastr.success('Success');
			                oTableListMember.ajax.reload(null, false);
			                $('#GlobalModalLarge').modal('hide');
			            }
			            else
			            {
			                toastr.error(response.msg);
			                end_loading_button2(selector);
			            }
			        }).fail(function(response){
			           toastr.error('Connection error,please try again');
			           end_loading_button2(selector);     
			        })
			    }
			}

		},

		validation : function(arr){
			var toatString = "";
			var result = "";
			for (key in arr){
				switch(key)
				{
				 default:
				 	   var nm = '';
				 	   if (key == 'NIPNPM') {
				 	   	nm  = 'Employees / Student';
				 	   }
				 	   else if(key == 'ID_set_member'){
				 	   	nm  = 'Level Member';
				 	   }
				 	   else
				 	   {
				 	   		nm  = 'Group';
				 	   }

				       result = Validation_required(arr[key],nm);
				       if (result['status'] == 0) {
				         toatString += result['messages'] + "<br>";
				       }
				       break;
				}
			}

			if (toatString != "") {
			  toastr.error(toatString, 'Failed!!');
			  return false;
			}
			return true
		},
	};

	var App_master_group = {
		Loaded : function(){
			App_master_group.LoadTable();
		},

		LoadTable : function(){
			var recordTable = $('#TableMasterGroup').DataTable({
			    "processing": true,
			    "serverSide": false,
			    "lengthMenu": [
			        [5],
			        [5]
			    ],
			    "iDisplayLength": 5,
			    "ajax":{
			        url : base_url_js+"__data_setting_master_group", // json datasource
			        ordering : false,
			        type: "post",  // method  , by default get
			        data : function(token){
			              // Read values
			               var data = {
			               		  action : 'datatables',
			                  };
			              // Append to data
			              token.token = jwt_encode(data,'UAP)(*');
			        }                                                                     
			     },
			      'columnDefs': [
			      	
			      	{
			      	   'targets': 0,
			      	   'searchable': false,
			      	   'orderable': false,
			      	   'className': 'dt-body-center',
			      	},
			        {
			            'targets': 2,
			            'searchable': false,
			            'orderable': false,
			            'className': 'dt-body-center',
			            'render': function (data, type, full, meta){
			            	   var btnaction = '';
			            	   // <?php if (in_array($this->session->userdata('DivisionID') , $AuthDivisionCrud)): ?>
			            	   		var btnEdit = '<button class = "btn btn-info btn-sm btnEditMasterGroup" data-id="'+full[2]+'" datatoken = "'+full[3]+'" >Edit</button>';
			            	   		var btnDelete = '<button class = "btn btn-danger btn-sm btnDeleteMasterGroup" data-id="'+full[2]+'">Delete</button> ';
			            	   		btnaction = btnEdit+' '+btnDelete;
			            	   // <?php endif ?>
			            	   return btnaction;
			            }
			        },
			         
			      ],
			    'createdRow': function( row, data, dataIndex ) {
			            
			    },
			    dom: 'l<"toolbar">frtip',
			    initComplete: function(){
			      
			    }  
			});

			oTableMasterGroup = recordTable;
		},

		Form_modal : function(action='add',ID='',datatoken=''){
			var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
		              '<button type="button" id="ModalbtnSaveFormMasterGroup" class="btn btn-success" action="'+action+'" data-id="'+ID+'">Save</button>'; 
		    var html = '<div class="row" page = "FrmListMember">'+
		    				'<div class = "col-md-12">'+
		    					'<div class = "form-group">'+
		    						'<label>MasterGroupName</label>'+
		    						'<input type="text" class = "form-control FrmGroup" name = "MasterGroupName" />'+
		    					'</div>'+
		    				'</div>'+
		    			'</div>';	

			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Form Master Group Member'+'</h4>');
			$('#GlobalModalLarge .modal-body').html(html);
			$('#GlobalModalLarge .modal-footer').html(footer);
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});
			if(action == 'edit'){
				var dt = jwt_decode(datatoken);
				for (key in dt){
					$('.FrmGroup[name="'+key+'"]').val(dt[key]);
				}
			}

		},

		SubmitData : function(selector,action,ID){
			var data = {};
			$('.FrmGroup').each(function(e){
				var name = $(this).attr('name');
				if ($(this).is('input')) {
					var v = $(this).val();
				}

				if ($(this).is('select')) {
					var v = $(this).find('option:selected').val();
				}

				data[name] =  v;

			})

			var validation = (action == 'delete') ? true : App_master_group.validation(data);
			if (validation) {
			    if (confirm('Are you sure ?')) {
			        loading_button2(selector);
			        var dataform = {
			            action : action,
			            data : data,
			            ID : ID,
			        };
			        var url = base_url_js+"__data_setting_master_group";
			        var token = jwt_encode(dataform,'UAP)(*');
			        AjaxSubmit(url,token).then(function(response){
			            if (response.status == 1) {
			            	toastr.success('Success');
			                oTableMasterGroup.ajax.reload(null, false);
			                oTableListMember.ajax.reload(null, false);
			                var selectorOPMasterGroup = $('#ChooseMasterGroup');
			                App_master_group.LoadSelectOP(selectorOPMasterGroup,'','yes');
			                $('#GlobalModalLarge').modal('hide');
			            }
			            else
			            {
			                toastr.error(response.msg);
			                end_loading_button2(selector);
			            }
			        }).fail(function(response){
			           toastr.error('Connection error,please try again');
			           end_loading_button2(selector);     
			        })
			    }
			}

		},

		validation : function(arr){
			var toatString = "";
			var result = "";
			for (key in arr){
				switch(key)
				{
				 default:
				       result = Validation_required(arr[key],key);
				       if (result['status'] == 0) {
				         toatString += result['messages'] + "<br>";
				       }
				       break;
				}
			}

			if (toatString != "") {
			  toastr.error(toatString, 'Failed!!');
			  return false;
			}
			return true
		},

		LoadSelectOP : function(selector,dataselected='',filter='yes'){
			var url = base_url_js+'__data_setting_master_group';
			var dataform = {
				action : 'LoadData',
			}; 
			var token = jwt_encode(dataform,'UAP)(*');
		    AjaxSubmit(url,token).then(function(response){
		    	selector.empty();
		    	for (var i = 0; i < response.length; i++) {
		    		if (i==0 && filter == 'yes') {
		    			selector.append('<option value="'+'%'+'">'+'--All--'+'</option>')
		    		}
		    		var selected = (response[i].ID_master_group == dataselected) ? 'selected' : '';

		    		selector.append('<option value="'+response[i].ID_master_group+'" '+selected+' >'+response[i].MasterGroupName+'</option>');
		    	}
			}).fail(function(response){
		        toastr.error('No data result');
		    })
		},
	};

	var App_group = {
		Loaded : function(){
			App_group.LoadTable();
		},

		LoadTable : function(){
			var recordTable = $('#TableGroup').DataTable({
			    "processing": true,
			    "serverSide": false,
			    "lengthMenu": [
			        [5],
			        [5]
			    ],
			    "iDisplayLength": 5,
			    "ajax":{
			        url : base_url_js+"__data_setting_group", // json datasource
			        ordering : false,
			        type: "post",  // method  , by default get
			        data : function(token){
			              // Read values
			               var data = {
			               		  action : 'datatables',
			                  };
			              // Append to data
			              token.token = jwt_encode(data,'UAP)(*');
			        }                                                                     
			     },
			      'columnDefs': [
			      	
			      	{
			      	   'targets': 0,
			      	   'searchable': false,
			      	   'orderable': false,
			      	   'className': 'dt-body-center',
			      	},
			        {
			            'targets': 2,
			            'searchable': false,
			            'orderable': false,
			            'className': 'dt-body-center',
			            'render': function (data, type, full, meta){
			            	   var btnaction = '';
			            	   // <?php if (in_array($this->session->userdata('DivisionID') , $AuthDivisionCrud)): ?>
			            	   		var btnEdit = '<button class = "btn btn-info btn-sm btnEditGroup" data-id="'+full[2]+'" datatoken = "'+full[3]+'" >Edit</button>';
			            	   		var btnDelete = '<button class = "btn btn-danger btn-sm btnDeleteGroup" data-id="'+full[2]+'">Delete</button> ';
			            	   		btnaction = btnEdit+' '+btnDelete;
			            	   // <?php endif ?>
			            	   return btnaction;
			            }
			        },
			         
			      ],
			    'createdRow': function( row, data, dataIndex ) {
			            
			    },
			    dom: 'l<"toolbar">frtip',
			    initComplete: function(){
			      
			    }  
			});

			oTableGroup = recordTable;
		},

		Form_modal : function(action='add',ID='',datatoken=''){
			var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
		              '<button type="button" id="ModalbtnSaveFormGroup" class="btn btn-success" action="'+action+'" data-id="'+ID+'">Save</button>'; 
		    var html = '<div class="row" page = "FrmListMember">'+
		    				'<div class = "col-md-12">'+
		    					'<div class = "form-group">'+
		    						'<label>GroupName</label>'+
		    						'<input type="text" class = "form-control FrmGroup" name = "GroupName" />'+
		    					'</div>'+
		    					'<div class = "form-group">'+
		    						'<label>Choose Master Group</label>'+
		    						'<select class = "form-control FrmGroup" name = "ID_master_group"></select>'+
		    					'</div>'+
		    				'</div>'+
		    			'</div>';	

			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Form Group Member'+'</h4>');
			$('#GlobalModalLarge .modal-body').html(html);
			$('#GlobalModalLarge .modal-footer').html(footer);
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});
			var selectorOPMasterGroup = $('.FrmGroup[name="ID_master_group"]');
			App_master_group.LoadSelectOP(selectorOPMasterGroup,'','');
			if(action == 'edit'){
				var dt = jwt_decode(datatoken);
				var firstLoad = setInterval(function () {
		            var ChooseMasterGroup = selectorOPMasterGroup.val();
		            if(ChooseMasterGroup!='' && ChooseMasterGroup!=null){
		                for (key in dt){
		                	if ($('.FrmGroup[name="'+key+'"]').length && $('.FrmGroup[name="'+key+'"]').is('select') ) {
		                		$(".FrmGroup[name='"+key+"'] option").filter(function() {
		                		   //may want to use $.trim in here
		                		   return $(this).val() == dt[key]; 
		                		}).prop("selected", true);
		                	}

		                	if ($('.FrmGroup[name="'+key+'"]').length && $('.FrmGroup[name="'+key+'"]').is('input') ) {
		                		$('.FrmGroup[name="'+key+'"]').val(dt[key]);
		                		$('.FrmGroup[name="'+key+'"]').closest('.form-group').find('label[for="Name"]').html(dt['MemberListName']);
		                	}

		                	
		                	
		                }	
		                
		                clearInterval(firstLoad);
		            }
		        },200);
		        setTimeout(function () {
		            clearInterval(firstLoad);
		        },5000);
					
			}

		},

		SubmitData : function(selector,action,ID){
			var data = {};
			$('.FrmGroup').each(function(e){
				var name = $(this).attr('name');
				if ($(this).is('input')) {
					var v = $(this).val();
				}

				if ($(this).is('select')) {
					var v = $(this).find('option:selected').val();
				}

				data[name] =  v;
				// alert(data[name]);
				
			})

			var validation = (action == 'delete') ? true : App_group.validation(data);
			if (validation) {
			    if (confirm('Are you sure ?')) {
			        loading_button2(selector);
			        var dataform = {
			            action : action,
			            data : data,
			            ID : ID,
			        };			        
			        var url = base_url_js+"__data_setting_group";
			        var token = jwt_encode(dataform,'UAP)(*');
			        AjaxSubmit(url,token).then(function(response){
			            if (response.status == 1) {
			            	toastr.success('Success');
			                oTableGroup.ajax.reload(null, false);
			                oTableListMember.ajax.reload(null, false);
			                var selectorOPMasterGroup = $('#ChooseMasterGroup');
			                App_group.LoadSelectOP(selectorOPMasterGroup,'','yes');
			                $('#GlobalModalLarge').modal('hide');
			            }
			            else
			            {
			                toastr.error(response.msg);
			                end_loading_button2(selector);
			            }
			        }).fail(function(response){
			           toastr.error('Connection error,please try again');
			           end_loading_button2(selector);     
			        })
			    }
			}

		},

		validation : function(arr){
			var toatString = "";
			var result = "";
			for (key in arr){
				switch(key)
				{
				 default:
				       result = Validation_required(arr[key],key);
				       if (result['status'] == 0) {
				         toatString += result['messages'] + "<br>";
				       }
				       break;
				}
			}

			if (toatString != "") {
			  toastr.error(toatString, 'Failed!!');
			  return false;
			}
			return true
		},

		LoadSelectOP : function(selector,dataselected='',filter='yes'){
			var url = base_url_js+'__data_setting_group';
			var dataform = {
				action : 'LoadData',
			}; 
			var token = jwt_encode(dataform,'UAP)(*');
		    AjaxSubmit(url,token).then(function(response){
		    	selector.empty();
		    	for (var i = 0; i < response.length; i++) {
		    		if (i==0 && filter == 'yes') {
		    			selector.append('<option value="'+'%'+'">'+'--All--'+'</option>')
		    		}
		    		var selected = (response[i].ID_set_group == dataselected) ? 'selected' : '';
		    		selector.append('<option value="'+response[i].ID_set_group+'" '+selected+' >'+response[i].GroupName+'</option>');
		    	}
			}).fail(function(response){
		        toastr.error('No data result');
		    })
		},
	};

	var App_member = {
		Loaded : function(){
			App_member.LoadTable();
		},

		LoadTable : function(){
			var recordTable = $('#TableMember').DataTable({
			    "processing": true,
			    "serverSide": false,
			    "lengthMenu": [
			        [5],
			        [5]
			    ],
			    "iDisplayLength": 5,
			    "ajax":{
			        url : base_url_js+"__data_setting_member", // json datasource
			        ordering : false,
			        type: "post",  // method  , by default get
			        data : function(token){
			              // Read values
			               var data = {
			               		  action : 'datatables',
			                  };
			              // Append to data
			              token.token = jwt_encode(data,'UAP)(*');
			        }                                                                     
			     },
			      'columnDefs': [
			      	
			      	{
			      	   'targets': 0,
			      	   'searchable': false,
			      	   'orderable': false,
			      	   'className': 'dt-body-center',
			      	},
			        {
			            'targets': 3,
			            'searchable': false,
			            'orderable': false,
			            'className': 'dt-body-center',
			            'render': function (data, type, full, meta){
			            	   var btnaction = '';
			            	   // <?php if (in_array($this->session->userdata('DivisionID') , $AuthDivisionCrud)): ?>
			            	   		var btnEdit = '<button class = "btn btn-info btn-sm btnEditMember" data-id="'+full[3]+'" datatoken = "'+full[4]+'" >Edit</button>';
			            	   		var btnDelete = '<button class = "btn btn-danger btn-sm btnDeleteMember" data-id="'+full[3]+'">Delete</button> ';
			            	   		btnaction = btnEdit+' '+btnDelete;
			            	   // <?php endif ?>
			            	   return btnaction;
			            }
			        },
			         
			      ],
			    'createdRow': function( row, data, dataIndex ) {
			            
			    },
			    dom: 'l<"toolbar">frtip',
			    initComplete: function(){
			      
			    }  
			});

			oTableMember = recordTable;
		},

		Form_modal : function(action='add',ID='',datatoken=''){
			var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
		              '<button type="button" id="ModalbtnSaveFormMember" class="btn btn-success" action="'+action+'" data-id="'+ID+'">Save</button>'; 
		    var htmlOPLevel = '';
		    for (var i = 1; i <= 5; i++) {
		    	var selected = (i==2) ? 'selected' : '';
		    	htmlOPLevel += '<option value = "'+i+'">'+i+'</option>';
		    }
		    var html = '<div class="row" page = "FrmListMember">'+
		    				'<div class = "col-md-12">'+
		    					'<div class = "form-group">'+
		    						'<label>MemberName</label>'+
		    						'<input type="text" class = "form-control FrmMember" name = "MemberName" />'+
		    					'</div>'+
		    					'<div class = "form-group">'+
		    						'<label>Level</label>'+
		    						'<select class = "form-control FrmMember" name = "LevelSequence">'+
		    							htmlOPLevel+
		    						'</select>'+	
		    					'</div>'+
		    				'</div>'+
		    			'</div>';	

			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Form Member'+'</h4>');
			$('#GlobalModalLarge .modal-body').html(html);
			$('#GlobalModalLarge .modal-footer').html(footer);
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});
			if(action == 'edit'){
				var dt = jwt_decode(datatoken);
				for (key in dt){
					if ($('.FrmMember[name="'+key+'"]').length && $('.FrmMember[name="'+key+'"]').is('select') ) {
						$(".FrmMember[name='"+key+"'] option").filter(function() {
						   //may want to use $.trim in here
						   return $(this).val() == dt[key]; 
						}).prop("selected", true);
					}

					if ($('.FrmMember[name="'+key+'"]').length && $('.FrmMember[name="'+key+'"]').is('input') ) {
						$('.FrmMember[name="'+key+'"]').val(dt[key]);
					}
				}
			}

		},

		SubmitData : function(selector,action,ID){
			var data = {};
			$('.FrmMember').each(function(e){
				var name = $(this).attr('name');
				if ($(this).is('input')) {
					var v = $(this).val();
				}

				if ($(this).is('select')) {
					var v = $(this).find('option:selected').val();
				}

				data[name] =  v;

			})

			var validation = (action == 'delete') ? true : App_member.validation(data);
			if (validation) {
			    if (confirm('Are you sure ?')) {
			        loading_button2(selector);
			        var dataform = {
			            action : action,
			            data : data,
			            ID : ID,
			        };
			        var url = base_url_js+"__data_setting_member";
			        var token = jwt_encode(dataform,'UAP)(*');
			        AjaxSubmit(url,token).then(function(response){
			            if (response.status == 1) {
			            	toastr.success('Success');
			                oTableMember.ajax.reload(null, false);
			                oTableListMember.ajax.reload(null, false);
			                var selectorOP = $('#ChooseMember');
			                App_member.LoadSelectOP(selectorOP,'','yes');
			                $('#GlobalModalLarge').modal('hide');
			            }
			            else
			            {
			                toastr.error(response.msg);
			                end_loading_button2(selector);
			            }
			        }).fail(function(response){
			           toastr.error('Connection error,please try again');
			           end_loading_button2(selector);     
			        })
			    }
			}

		},

		validation : function(arr){
			var toatString = "";
			var result = "";
			for (key in arr){
				switch(key)
				{
				 default:
				       result = Validation_required(arr[key],key);
				       if (result['status'] == 0) {
				         toatString += result['messages'] + "<br>";
				       }
				       break;
				}
			}

			if (toatString != "") {
			  toastr.error(toatString, 'Failed!!');
			  return false;
			}
			return true
		},


		LoadSelectOP : function(selector,dataselected='',filter='yes'){
			var url = base_url_js+'__data_setting_member';
			var dataform = {
				action : 'LoadData',
			}; 
			var token = jwt_encode(dataform,'UAP)(*');
		    AjaxSubmit(url,token).then(function(response){
		    	selector.empty();
		    	for (var i = 0; i < response.length; i++) {
		    		if (i==0 && filter == 'yes') {
		    			selector.append('<option value="'+'%'+'">'+'--All--'+'</option>')
		    		}
		    		var selected = (response[i].ID_set_member == dataselected) ? 'selected' : '';
		    		selector.append('<option value="'+response[i].ID_set_member+'" '+selected+' >'+response[i].MemberName+'</option>');
		    	}
			}).fail(function(response){
		        toastr.error('No data result');
		    })
		},
	};

	var App_default = {
		Loaded : function(){
			var selectorOPGroup = $('#ChooseGroup');
			App_group.LoadSelectOP(selectorOPGroup,'','yes');
			var selectorOPMasterGroup = $('#ChooseMasterGroup');		
			App_master_group.LoadSelectOP(selectorOPMasterGroup,'','yes');
			var selectorOPMember = $('#ChooseMember');
			App_member.LoadSelectOP(selectorOPMember,'','yes');
			App_list_member.Loaded();
			App_master_group.Loaded();
			App_group.Loaded();
			App_member.Loaded();
		},
	};


	$(document).ready(function(e){
		App_default.Loaded();
	})

	$(document).off('click', '.btn-add-list-member').on('click', '.btn-add-list-member',function(e) {
	   App_list_member.Form_modal();
	})

	$(document).off('change', '#TypeMember').on('change', '#TypeMember',function(e) {
		$('.FrmListMember[name="NIPNPM"]').val('');
		$('.FrmListMember[name="NIPNPM"]').closest('.form-group').find('label[for="Name"]').html('');
	})

	$(document).off('change', '#ID_master_group').on('change', '#ID_master_group',function(e) {
		var dataMaster = $('.FrmListMember[name="ID_master_group"]').val();
		var selectorOPsetGroup = $('.FrmListMember[name="ID_set_group"]').val('');
		// alert(selectorOPMasterGroup);
		App_group.LoadSelectGroup(selectorOPsetGroup,'',dataMaster,'yes');
	})

	$(document).off('click', '.SearchNIPNPM').on('click', '.SearchNIPNPM',function(e) {
		var selector = $(this);
		App_list_member.searchMember(selector);	
	})

	$(document).off('click', '#ModalbtnSaveFormListMember').on('click', '#ModalbtnSaveFormListMember',function(e) {
		var selector = $(this);
		var action = selector.attr('action');
		var ID = selector.attr('data-id');
		App_list_member.SubmitData(selector,action,ID);
	})

	$(document).off('change', '#ChooseGroup, #ChooseMasterGroup,#ChooseMember').on('change', '#ChooseGroup,#ChooseMember',function(e) {
		oTableListMember.ajax.reload(null, false);
	})
	
	$(document).off('click', '.btnEditListMember').on('click', '.btnEditListMember',function(e) {
		var action = 'edit';
		var ID = $(this).attr('data-id');
		var datatoken = $(this).attr('datatoken');
		App_list_member.Form_modal(action,ID,datatoken);
	})

	$(document).off('click', '.btnDeleteListMember').on('click', '.btnDeleteListMember',function(e) {
		var selector = $(this);
		var action = 'delete';
		var ID = selector.attr('data-id');
		App_list_member.SubmitData(selector,action,ID);
	})

	$(document).off('click', '.btn-add-master-group').on('click', '.btn-add-master-group',function(e) {
	   App_master_group.Form_modal();
	})
	
	$(document).off('click', '.btn-add-group').on('click', '.btn-add-group',function(e) {
	   App_group.Form_modal();
	}) 

	$(document).off('click', '#ModalbtnSaveFormMasterGroup').on('click', '#ModalbtnSaveFormMasterGroup',function(e) {
		var selector = $(this);
		var action = selector.attr('action');
		var ID = selector.attr('data-id');
		App_master_group.SubmitData(selector,action,ID);
	})

	$(document).off('click', '.btnEditMasterGroup').on('click', '.btnEditMasterGroup',function(e) {
		var action = 'edit';
		var ID = $(this).attr('data-id');
		var datatoken = $(this).attr('datatoken');
		App_master_group.Form_modal(action,ID,datatoken);
	})

	$(document).off('click', '.btnDeleteMasterGroup').on('click', '.btnDeleteMasterGroup',function(e) {
		var selector = $(this);
		var action = 'delete';
		var ID = selector.attr('data-id');
		App_master_group.SubmitData(selector,action,ID);
	})

	$(document).off('click', '.btnEditGroup').on('click', '.btnEditGroup',function(e) {
		var action = 'edit';
		var ID = $(this).attr('data-id');
		var datatoken = $(this).attr('datatoken');
		App_group.Form_modal(action,ID,datatoken);
	})

	$(document).off('click', '.btnDeleteGroup').on('click', '.btnDeleteGroup',function(e) {
		var selector = $(this);
		var action = 'delete';
		var ID = selector.attr('data-id');
		App_group.SubmitData(selector,action,ID);
	})

	$(document).off('click', '#ModalbtnSaveFormGroup').on('click', '#ModalbtnSaveFormGroup',function(e) {
		var selector = $(this);
		var action = selector.attr('action');
		var ID = selector.attr('data-id');
		App_group.SubmitData(selector,action,ID);
	})
	

	$(document).off('click', '.btn-add-member').on('click', '.btn-add-member',function(e) {
	   App_member.Form_modal();
	})

	$(document).off('click', '#ModalbtnSaveFormMember').on('click', '#ModalbtnSaveFormMember',function(e) {
		var selector = $(this);
		var action = selector.attr('action');
		var ID = selector.attr('data-id');
		App_member.SubmitData(selector,action,ID);
	})
	 
	$(document).off('click', '.btnEditMember').on('click', '.btnEditMember',function(e) {
		var action = 'edit';
		var ID = $(this).attr('data-id');
		var datatoken = $(this).attr('datatoken');
		App_member.Form_modal(action,ID,datatoken);
	})

	$(document).off('click', '.btnDeleteMember').on('click', '.btnDeleteMember',function(e) {
		var selector = $(this);
		var action = 'delete';
		var ID = selector.attr('data-id');
		App_member.SubmitData(selector,action,ID);
	})
	
</script>
