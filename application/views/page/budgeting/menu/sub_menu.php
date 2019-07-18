<script type="text/javascript" src="<?php echo base_url();?>assets/custom/jquery.maskMoney.js"></script>
<div class="row" style="margin-top: 5px;" class="btn-read">
	 <div class="col-md-12">
	     <div class="widget box">
	         <div class="widget-header">
	             <h4 class="header"><i class="icon-reorder"></i>Daftar Sub Menu</h4>
	             <div class="toolbar no-padding">
	                 <div class="btn-group">
	                   <span data-smt="" class="btn btn-xs btn-write btn_add_sub_menu">
	                     <i class="icon-plus"></i> Add Sub Menu
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
	var S_Table_example_budget = '';
	var HTMLTbl = '<div class = "table-responsive">'+'<table class="table table-striped table-bordered table-hover table-checkable" id = "example_budget" style = "width: 1600px;overflow: auto;">'+
	             			'<thead>'+
	             				'<tr>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">'+
	             						'MENU'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:10%">'+
	             						'Sub Menu 1 (Empty as Default)'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:10%">'+
	             						'Sub Menu 2 (Empty as Default)'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:20%"">'+
	             						'URI'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:20%"">'+
	             						'Controler'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:4%">'+
	             						'Sort Submenu 1'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:4%">'+
	             						'Sort Submenu 2'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;width:10%">'+
	             						'Akses'+
	             					'</th>'+
	             					'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">'+
	             						'Action'+
	             					'</th>'+
	             				'</tr>'+
	             			'</thead>'+
	             			'<tbody id = "Tbodydatatable">'+
	             				
	             			'</tbody>'
	             		'</table>'+'</div>';
	var Arr_dt =  <?php echo json_encode($Arr_dt) ?>;             		
	var Arr_Menu =  <?php echo json_encode($Arr_Menu) ?>;             		
	$(document).ready(function () {
       LoadData(Arr_dt);
	});

	function LoadData(dt)
	{
		var html = '';
		var OPAction = function(write=1)
		{
			var h = '';
			h = '<select class = " form-control actionCh" style = "width : 80%">';
				var temp = ['Read','Write'];
				for (var i = 0; i < temp.length; i++) {
					var selected = (write == i) ? 'selected' : '';
					h += '<option value = "'+i+'" '+selected+' >'+temp[i]+'</option>';
				}
			h += '</select>';	

			return h;
		}

		var OPMenu = function(ID_Menu)
		{
			var h = '';
			h = '<select class = " form-control ID_Menu" style = "width : 80%">';
				for (var i = 0; i < Arr_Menu.length; i++) {
					var selected = (Arr_Menu[i].ID == ID_Menu) ? 'selected' : '';
					h += '<option value = "'+Arr_Menu[i].ID+'" '+selected+' >'+Arr_Menu[i].Menu+'</option>';
				}
			h += '</select>';	

			return h;
		}

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
			             return '<div class = "hide">'+full.Menu+'</div>'+OPMenu(full.ID_Menu)+
							'<div class = "hide">'+full.Menu+'</div>';
			         }
			      },
			      {
			         'targets': 1,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control SubMenu1" value="'+full.SubMenu1+'">'+
							'<div class = "hide">'+full.SubMenu1+'</div>'+
							'<div class = "hide">'+full.Menu+'</div>';
			         }
			      },
			      {
			         'targets': 2,
			         'render': function (data, type, full, meta){
			              return '<input type="text" class="form-control SubMenu2" value="'+full.SubMenu2+'">'+
							'<div class = "hide">'+full.SubMenu2+'</div>';
			         }
			      },
			      {
			         'targets': 3,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control Slug" value="'+full.Slug+'">'+
							'<div class = "hide">'+full.Slug+'</div>';
			         }
			      },
			      {
			         'targets': 4,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control Controller" value="'+full.Controller+'">'+
							'<div class = "hide">'+full.Controller+'</div>';
			         }
			      },
			      {
			         'targets': 5,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control Sort1" value="'+full.Sort1+'">'+
							'<div class = "hide">'+full.Sort1+'</div>';
			         }
			      },
			      {
			         'targets': 6,
			         'render': function (data, type, full, meta){
			             return '<input type="text" class="form-control Sort2" value="'+full.Sort2+'">'+
							'<div class = "hide">'+full.Sort2+'</div>';
			         }
			      },
			      {
			         'targets': 7,
			         'render': function (data, type, full, meta){
			            return OPAction(full.write)+
							'<div class = "hide">'+full.write+'</div>';
			         }
			      },
			       {
			         'targets': 8,
			         'render': function (data, type, full, meta){
			             return '<button class = "btn btn-primary btn-save btn-write" action = "edit"><i class="fa fa-floppy-o" aria-hidden="true"></i> </button>&nbsp'+'<button class = "btn btn-danger btn-delete btn-write"><i class="fa fa-trash"></i> </button>'+
			             	'<div class = "hide">'+full.Sort+'</div>';;
			         }
			      },
		      ],
		      'createdRow': function( row, data, dataIndex ) {
		      		$(row).attr('id-key',data.ID);

		      },
		      'order': [[8, 'asc'],[5, 'asc'],[6, 'asc']],

		});
		S_Table_example_budget = table;
		var rows = S_Table_example_budget.rows({ 'search': 'applied' }).nodes();
		$('.ID_Menu[tabindex!="-1"]', rows).select2({
		    		    //allowClear: true
		});

		$('.Sort1,.Sort2', rows).maskMoney({thousands:'', decimal:'', precision:0,allowZero: true});
		$('.Sort1,.Sort2', rows).maskMoney('mask', '9894');
	}

	$(document).off('click', '.btn-save').on('click', '.btn-save',function(e) {
		var ev = $(this).closest('tr');
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
			thiss.prop('disabled',true);

		   var ID = ev.attr('id-key');
		   var ID_Menu = ev.find('.ID_Menu option:selected').val();
		   var SubMenu1 = ev.find('.SubMenu1').val();
		   var SubMenu2 = ev.find('.SubMenu2').val();
		   var Slug = ev.find('.Slug').val();
		   var Controller = ev.find('.Controller').val();
		   var Sort1 = ev.find('.Sort1').val();
		   var Sort2 = ev.find('.Sort2').val();
		   var actionCh = ev.find('.actionCh').val();
		   var action = thiss.attr('action');
		   var url = base_url_js+"budgeting/menu/sub_menu/save";
		   var data = {
			    action : action,
			    ID : ID,
			    ID_Menu : ID_Menu,
			    SubMenu1 : SubMenu1,
			    SubMenu2 : SubMenu2,
			    Slug : Slug,
			    Controller : Controller,
			    Sort1 : Sort1,
			    Sort2 : Sort2,
			    actionCh : actionCh,
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

	$(document).off('click', '.btn_add_sub_menu').on('click', '.btn_add_sub_menu',function(e) {
		var OPAction = function(write=1)
		{
			var h = '';
			h = '<select class = " form-control actionCh_" style = "width : 80%">';
				var temp = ['Read','Write'];
				for (var i = 0; i < temp.length; i++) {
					var selected = (write == i) ? 'selected' : '';
					h += '<option value = "'+i+'" '+selected+' >'+temp[i]+'</option>';
				}
			h += '</select>';	

			return h;
		}

		var OPMenu = function(ID_Menu)
		{
			var h = '';
			h = '<select class = " form-control ID_Menu_" style = "width : 80%">';
				for (var i = 0; i < Arr_Menu.length; i++) {
					var selected = (Arr_Menu[i].ID == ID_Menu) ? 'selected' : '';
					h += '<option value = "'+Arr_Menu[i].ID+'" '+selected+' >'+Arr_Menu[i].Menu+'</option>';
				}
			h += '</select>';	

			return h;
		}

			// create new using modal
			var html = '';
				html = '<form class="form-horizontal" id="formModal">'+
		'<div class="form-group">'+ 
		     '<div class="row">   '+
		        '<div class="col-sm-4">'+
		            '<label class="control-label">Menu:</label>'+
		        '</div>'+    
		        '<div class="col-sm-6">'+
		            OPMenu(1)+
		        '</div>'+
		    '</div>'+
		'</div> '+
		'<div class="form-group">'+ 
		     '<div class="row">   '+
		        '<div class="col-sm-4">'+
		            '<label class="control-label">SubMenu1:</label>'+
		        '</div>'+    
		        '<div class="col-sm-6">'+
		            '<input type="text" class="form-control SubMenu1_">'+
		        '</div>'+
		    '</div>'+
		'</div> '+		
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">SubMenu2:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    '<input type="text" class="form-control SubMenu2_">'+
                '</div>'+
            '</div>'+
        '</div> '+
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Slug:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    '<input type="text" class="form-control Slug_">'+
                '</div>'+
            '</div>'+
        '</div> '+
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Controller:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    '<input type="text" class="form-control Controller_">'+
                '</div>'+
            '</div>'+
        '</div> '+
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Sort1:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    '<input type="text" class="form-control Sort1_">'+
                '</div>'+
            '</div>'+
        '</div> '+
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Sort2:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    '<input type="text" class="form-control Sort2_">'+
                '</div>'+
            '</div>'+
        '</div> '+
        '<div class="form-group">'+ 
             '<div class="row">   '+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Akses:</label>'+
                '</div>'+    
                '<div class="col-sm-6">'+
                    OPAction(1)+
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
			$('#GlobalModal .modal-header').html('<h4 class="modal-title">'+'Add Sub Menu'+'</h4>');
			$('#GlobalModal .modal-body').html(html);
			$('#GlobalModal .modal-footer').html(' ');
			$('#GlobalModal').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});

			    $('.ID_Menu_[tabindex!="-1"]').select2({
				    		    //allowClear: true
				});

				$('.Sort1_,.Sort2_').maskMoney({thousands:'', decimal:'', precision:0,allowZero: true});
				$('.Sort1_,.Sort2_').maskMoney('mask', '9894');

	});

	$(document).off('click', '#ModalbtnSaveForm').on('click', '#ModalbtnSaveForm',function(e) {
		if (confirm('Are you sure ?')) {
			loading_button('#ModalbtnSaveForm');
			var ID = '';
			var ID_Menu = $('.ID_Menu_ option:selected').val();
			var SubMenu1 = $('.SubMenu1_').val();
			var SubMenu2 = $('.SubMenu2_').val();
			var Slug = $('.Slug_').val();
			var Controller = $('.Controller_').val();
			var Sort1 = $('.Sort1_').val();
			var Sort2 = $('.Sort2_').val();
			var actionCh = $('.actionCh_').val();
			var action = 'add';
			   var url = base_url_js+"budgeting/menu/sub_menu/save";
			   var data = {
				    action : action,
				    ID : ID,
				    ID_Menu : ID_Menu,
				    SubMenu1 : SubMenu1,
				    SubMenu2 : SubMenu2,
				    Slug : Slug,
				    Controller : Controller,
				    Sort1 : Sort1,
				    Sort2 : Sort2,
				    actionCh : actionCh,
				};
				if (validation(data)) {
					var token = jwt_encode(data,"UAP)(*");
					$.post(url,{ token:token },function (resultJson) {
						response = jQuery.parseJSON(resultJson);
						Arr_dt = response.data;
						LoadData(Arr_dt);
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
		  var ID_Menu = ev.find('.ID_Menu option:selected').val();
		  var SubMenu1 = ev.find('.SubMenu1').val();
		  var SubMenu2 = ev.find('.SubMenu2').val();
		  var Slug = ev.find('.Slug').val();
		  var Controller = ev.find('.Controller').val();
		  var Sort1 = ev.find('.Sort1').val();
		  var Sort2 = ev.find('.Sort2').val();
		  var actionCh = ev.find('.actionCh').val();
		   var action = 'delete';
		   var url = base_url_js+"budgeting/menu/sub_menu/save";
		   var data = {
			    action : action,
			    ID : ID,
			    ID_Menu : ID_Menu,
			    SubMenu1 : SubMenu1,
			    SubMenu2 : SubMenu2,
			    Slug : Slug,
			    Controller : Controller,
			    Sort1 : Sort1,
			    Sort2 : Sort2,
			    actionCh : actionCh,
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
	      case  "SubMenu1" :
	      case  "SubMenu2" :
	      case  "Controller" :
	            result = Validation_required(arr[key],key);
	              if (result['status'] == 0) {
	                toatString += result['messages'] + "<br>";
	            }
	            break;
	      case  "Slug" :
	            // check SubMenu1 & SubMenu2
	            var SubMenu1 = arr['SubMenu1']; // 3 length
	            var SubMenu2 = arr['SubMenu2']; // 4 length
	            var sl = arr[key];
	            var aa = sl.split('/');
	            if (SubMenu2 == 'Empty' && SubMenu1 != 'Empty') {
	            	if (aa.length < 3) {
	            		toatString += key + ' harus memiliki minimal 3 uri segment' + "<br>";
	            	}
	            }
	            else if(SubMenu2 != 'Empty' && SubMenu1 == 'Empty')
	            {
	            	if (aa.length != 3) {
	            		toatString += key + ' error sub menu1' + "<br>";
	            	}
	            }
	            else if(SubMenu1 == 'Empty' && SubMenu2 == 'Empty')
	            {
	            	if (aa.length != 1) {
	            		toatString += key + ' harus memiliki 1 uri segment' + "<br>";
	            	}
	            }
	            else if(SubMenu1 != 'Empty' && SubMenu2 != 'Empty')
	            {
	            	if (aa.length < 4) {
	            		toatString += key + ' harus memiliki 4 uri segment' + "<br>";
	            	}
	            }
	            else
	            {
	            	toatString += key + ' error SLug' + "<br>";
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