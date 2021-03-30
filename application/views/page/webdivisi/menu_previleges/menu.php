<script type="text/javascript" src="<?php echo base_url();?>assets/custom/jquery.maskMoney.js"></script>
<div class="row" style="margin-top: 5px;" class="btn-read">
	 <div class="col-md-12">
	     <div class="widget box">
	         <div class="widget-header">
	             <h4 class="header"><i class="icon-reorder"></i>Daftar Menu</h4>
	             <div class="toolbar no-padding">
	                 <div class="btn-group">
	                   <span data-smt="" class="btn btn-xs btn-write btn_add_menu">
	                     <i class="icon-plus"></i> Add Menu
	                    </span>
	                 </div>
	             </div>
	         </div>
	         <div class="widget-content">
	             <div class="row">
	             	<div class="col-md-12" id = "PageTables">
	             		
	             	</div>
	             </div>
	         </div>
	     </div>
	 </div>
</div>
<script type="text/javascript">
	var Arr_Menu =  <?php echo json_encode($Arr_Menu) ?>;
	var S_Table_example_budget = '';
	var HTMLTbl = '<table class="table table-striped table-bordered table-hover table-checkable" id = "example_budget">'+
	             			'<thead>'+
	             				'<tr>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">'+
	             						'MENU'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">'+
	             						'ICON (https://fontawesome.com/v4.7.0/icons/)'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">'+
	             						'SORT'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">'+
	             						'Action'+
	             					'</th>'+
	             				'</tr>'+
	             			'</thead>'+
	             			'<tbody id = "Tbodydatatable">'+
	             				
	             			'</tbody>'
	             		'</table>';
	$(document).ready(function () {
       LoadData(Arr_Menu);
       $('.btn_add_menu').addClass(hideClass);
	});

	function LoadData(dt)
	{
		var html = '';

		// create table
		$('#PageTables').empty();
		$('#PageTables').html(HTMLTbl);

		var table = $('#example_budget').DataTable({
		      "data" : dt,
		      'iDisplayLength' : 10,
		      'columnDefs': [
			      {
			         'targets': 0,
			         'searchable': false,
			         'orderable': false,
			         'className': 'dt-body-center',
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control NameMenu" value="'+full.Menu+'">'+
							'<div class = "hide">'+full.Menu+'</div>';
			         }
			      },
			      {
			         'targets': 1,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control NameIcon" value="'+full.Icon+'" '+disAccess+'>'+
							'<div class = "hide">'+full.Icon+'</div>';
			         }
			      },
			      {
			         'targets': 2,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control Sort" value="'+full.Sort+'">'+
							'<div class = "hide">'+full.Sort+'</div>';
			         }
			      },
			       {
			         'targets': 3,
			         'render': function (data, type, full, meta){
			         	
			            return '<button class = "btn btn-primary btn-save btn-write" action = "edit"> <i class="fa fa-floppy-o" aria-hidden="true"></i> </button>&nbsp'+'<button class = "btn btn-danger btn-delete btn-write '+hideClass+'"><i class="fa fa-trash"></i> </button>';
			         }
			      },
		      ],
		      'createdRow': function( row, data, dataIndex ) {
		      		$(row).attr('id-key',data.ID);

		      },
		      'order': [[2, 'asc']],

		});
		S_Table_example_budget = table;
		var rows = S_Table_example_budget.rows({ 'search': 'applied' }).nodes();

		$('.Sort', rows).maskMoney({thousands:'', decimal:'', precision:0,allowZero: true});
		$('.Sort', rows).maskMoney('mask', '9894');
	}

	$(document).off('click', '.btn-save').on('click', '.btn-save',function(e) {
		var ev = $(this).closest('tr');
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
			thiss.prop('disabled',true);

		   var ID = ev.attr('id-key');
		   var Name = ev.find('.NameMenu').val();
		   var Icon = ev.find('.NameIcon').val();
		   var Sort = ev.find('.Sort').val();
		   var action = thiss.attr('action');
		   var url = base_url_js+"prodi/menu/save_menu";
		   var data = {
			    action : action,
			    ID : ID,
			    Menu : Name,
			    Icon : Icon,
			    Sort : Sort,
			};
			if (validation(data)) {
					var token = jwt_encode(data,"UAP)(*");
					$.post(url,{ token:token },function (resultJson) {

							toastr.success('Saved');
				    }).fail(function() {
					  toastr.info('No Result Data'); 
					}).always(function() {
					   thiss.prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> ');          
					});
			}
			else
			{
				thiss.prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> ');   
			}
			
		}
	});

	$(document).off('click', '.btn_add_menu').on('click', '.btn_add_menu',function(e) {
			// create new using modal
			var html = '';
				html = '<form class="form-horizontal" id="formModal">'+
		'<div class="form-group">'+ 
		     '<div class="row">   '+
		        '<div class="col-sm-4">'+
		            '<label class="control-label">Menu:</label>'+
		        '</div>'+    
		        '<div class="col-sm-6">'+
		            '<input type="text" class="form-control NameMenu_">'+
		        '</div>'+
		    '</div>'+
		'</div> '+
		'<div class="form-group">'+ 
		     '<div class="row">   '+
		        '<div class="col-sm-4">'+
		            '<label class="control-label">Icon:</label>'+
		        '</div>'+    
		        '<div class="col-sm-6">'+
		            '<input type="text" class="form-control NameIcon_">'+
		        '</div>'+
		    '</div>'+
		'</div> '+		
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Sort:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    '<input type="text" class="form-control Sort_">'+
                '</div>'+
            '</div>'+
        '</div> '+
        '<div style="text-align: center;">  '+     
    		'<div class="col-sm-12" id="BtnFooter">'+
                '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>'+
    		'</div>'+
        '</div> '+   
    '</form>';
			$('#GlobalModal .modal-header').html('<h4 class="modal-title">'+'Add Menu'+'</h4>');
			$('#GlobalModal .modal-body').html(html);
			$('#GlobalModal .modal-footer').html(' ');
			$('#GlobalModal').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});

				$('.Sort_').maskMoney({thousands:'', decimal:'', precision:0,allowZero: true});
				$('.Sort_').maskMoney('mask', '9894');
	});

	$(document).off('click', '#ModalbtnSaveForm').on('click', '#ModalbtnSaveForm',function(e) {
		if (confirm('Are you sure ?')) {
			loading_button('#ModalbtnSaveForm');
			var Name =$('.NameMenu_').val();
			var Icon =$('.NameIcon_').val();
			var Sort =$('.Sort_').val();
			var ID = '';
			var action = 'add';
			   var url = base_url_js+"prodi/menu/save_menu";
			   var data = {
				    action : action,
				    ID : ID,
				    Menu : Name,
				    Icon : Icon,
				    Sort : Sort,
				};
				if (validation(data)) {
					var token = jwt_encode(data,"UAP)(*");
					$.post(url,{ token:token },function (resultJson) {
						response = jQuery.parseJSON(resultJson);
						Arr_Menu = response.data;
						LoadData(Arr_Menu);
						toastr.success('Saved');
						$('#GlobalModal').modal('hide');
				    }).fail(function() {
					  toastr.info('No Result Data'); 
					}).always(function() {
					    $('#ModalbtnSaveForm').prop('disabled',false).html('Save');         
					});
				}
				else
				{
					$('#ModalbtnSaveForm').prop('disabled',false).html('Save');    
				}
				
		}
	})

	$(document).off('click', '.btn-delete').on('click', '.btn-delete',function(e) {
		var ev = $(this).closest('tr');
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
			thiss.prop('disabled',true);

		   var ID = ev.attr('id-key');
		   var Name = ev.find('.NameMenu').val();
		   var Icon = ev.find('.NameIcon').val();
		   var Sort = ev.find('.Sort').val();
		   var action = 'delete';
		   var url = base_url_js+"prodi/menu/save_menu";
		   var data = {
			    action : action,
			    ID : ID,
			    Menu : Name,
			    Icon : Icon,
			    Sort : Sort,
			};
			var token = jwt_encode(data,"UAP)(*");
			$.post(url,{ token:token },function (resultJson) {
				S_Table_example_budget
				        .row( ev )
				        .remove()
				        .draw();
		    }).fail(function() {
			  toastr.info('No Result Data'); 
			}).always(function() {
			    thiss.prop('disabled',false).html('<i class="fa fa-trash"></i> ');          
			});
		}
	})

	function validation(arr)
	{
	  var toatString = "";
	  var result = "";
	  for(var key in arr) {
	     switch(key)
	     {
	      case  "Menu" :
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

	  return true;
	}
</script>