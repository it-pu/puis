<?php $PositionMain = $this->session->userdata('PositionMain')?>
<div class="row" style="margin-top: 5px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class="header"><i class="icon-reorder"></i>Add User Previleges</h4>
            </div>
            <div class="widget-content">
                <div class = "row">	
					<div class="col-xs-3" style="">
						Name
						<input class="form-control" id="Nama" placeholder="Input Name...">
					</div>
                    <div class="col-xs-3">
                        Choice Group User Previleges :
                        <select class="full-width-fix" id="selectGroupuUser3">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-xs-4">
                        <br>
                        <button class="btn btn-primary btn-write" id="btn-save-previleges-add"><i class="fa fa-floppy-o" aria-hidden="true"></i>
                        Save</button>
                    </div>    
				</div>
                <br>
                <!-- <div class = "row"> -->
                    <div id='LoadTblGroupUser'></div> 
                <!-- </div> -->
                <div class="row" id='LoadBtnSbmt'></div>
            </div>
        </div>
    </div>
</div>

<div class="row <?php echo ($PositionMain['IDDivision'] != 12) ? '' : '' ?>" style="margin-top: 5px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class="header"><i class="icon-reorder"></i>Group Previleges</h4>
                <div class="toolbar no-padding">
                    <div class="btn-group">
                      <span data-smt="" class="btn btn-xs btn-add-groupP btn-add btn-write">
                        <i class="icon-plus"></i> Add Group Previleges
                       </span>
                    </div>
                </div>
            </div>
            <div class="widget-content">
                <div id= "loadtableGroupPrevileges" class="btn-read"></div>
            </div>
        </div>
    </div>
</div>

<div class="row <?php echo ($PositionMain['IDDivision'] != 12) ? '' : '' ?>" style="margin-top: 5px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class="header"><i class="icon-reorder"></i>Add Previleges</h4>
            </div>
            <div class="widget-content">
                <div class = "row"> 
                    <div class="col-xs-3">
                        Choice Group User Previleges :
                        <select class="full-width-fix" id="selectGroupuUser">
                            <option></option>
                        </select>
                    </div> 
                    <div class="col-xs-3">
                        Choice Menu :
                        <select class="full-width-fix" id="selectMenuUser">
                            <option></option>
                        </select>
                    </div>    
                </div>
                <div class = "row">
                    <div id='LoadSubMenu'></div> 
                </div>
                <div class="row" id='LoadBtnSbmt2'></div>
            </div>
        </div>
    </div>
</div>


<div class="row <?php echo ($PositionMain['IDDivision'] != 12) ? '' : '' ?>" style="margin-top: 5px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class="header"><i class="icon-reorder"></i>Edit & List Previleges</h4>
            </div>
            <div class="widget-content">
                <div class = "row"> 
                    <div class="col-xs-3">
                        Choice Group User Previleges :
                        <select class="full-width-fix" id="selectGroupuUser2">
                            <option></option>
                        </select>
                    </div> 
                </div>
                <br>
                <div class = "row">
                    <div id='LoadTblGroupUserPrevileges' class="col-md-12"></div> 
                </div>
            </div>
        </div>
    </div>
</div>	

<script type="text/javascript">
	window.temp__;
	var S_Table_example_budget = '';
	var HTMLTblGroupPrevileges = '<table class="table table-striped table-bordered table-hover table-checkable datatable2">'+
            '<thead>'+
                '<tr>'+
                    '<th style="width: 10%;">Group Name</th>'+
                    '<th style="width: 4%;">Action</th>'+
                '</tr>'+
            '</thead>'+
            '<tbody>'+
            '</tbody>'+
            '</table>';
    var HTMLTblMenuPrevilegesGroupUser = '<table class="table table-striped table-bordered table-hover table-checkable" id ="MenuPrevilegesGroupUser">'+
            '<thead>'+
                '<tr>'+
                    '<th style="width: 106px;">Group Name</th>'+
                    '<th style="width: 106px;">Menu</th>'+
                    '<th style="width: 15px;">Sub Menu 1</th>'+
                    '<th style="width: 15px;">Sub Menu 2</th>'+
                    '<th style="width: 15px;">Akses</th>'+
                    '<th style="width: 15px;">Action</th>'+
                '</tr>'+
            '</thead>'+
            '<tbody>'+
            '</tbody>'+
            '</table>';
    var HTMLTblPrevilegesGroupUser = '<div class = "col-md-12"><div class="table-responsive"> <table class="table table-striped table-bordered table-hover table-checkable datatable" id ="IDTblGroupUser">'+
        '<thead>'+
            '<tr>'+
                '<th style="width: 106px;">NIP</th>'+
                '<th style="width: 106px;">Name</th>'+
                '<th style="width: 15px;">Group Previleges</th>'+
                '<th style="width: 15px;">Action</th>'+
            '</tr>'+
        '</thead>'+
        '<tbody>'+
        '</tbody>'+
        '</table></div></div>';
	$(document).ready(function () {
       load_data_GroupPrevileges();
       // loadDataGroupUser();
       loadAutoCompleteUser();
	});

	function loadDataGroupUser()
	{
		$('#LoadTblGroupUser').empty();
		$('#LoadTblGroupUser').html(HTMLTblPrevilegesGroupUser);
	    $.fn.dataTable.ext.errMode = 'throw';
	    //alert('hsdjad');
	    $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings)
	              {
	                  return {
	                      "iStart": oSettings._iDisplayStart,
	                      "iEnd": oSettings.fnDisplayEnd(),
	                      "iLength": oSettings._iDisplayLength,
	                      "iTotal": oSettings.fnRecordsTotal(),
	                      "iFilteredTotal": oSettings.fnRecordsDisplay(),
	                      "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
	                      "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
	                  };
	              };

	    var dataTable = $('#IDTblGroupUser').DataTable( {
	        "processing": true,
	        "destroy": true,
	        "serverSide": true,
	        "iDisplayLength" : 10,
	        "lengthMenu": [[10, 25, 50], [10, 25, 50]],
	        "ordering" : false,
	        "ajax":{
	            url : base_url_js+"prodi/config/getAuthDataTables", // json datasource
	            ordering : false,
	            type: "post",  // method  , by default get
	            error: function(){  // error handling
	                $(".employee-grid-error").html("");
	                $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
	                $("#employee-grid_processing").css("display","none");
	            }
	        },
	        'createdRow': function( row, data, dataIndex ) {
	            /*var no = 'row'+(dataIndex + 1);
	              $(row).attr('id', no);*/
	        },
	    } );          
	}

	function loadAutoCompleteUser()
	{
	    temp__ = '';
	    $("#Nama").autocomplete({
	      minLength: 3,
	      select: function (event, ui) {
	        event.preventDefault();
	        var selectedObj = ui.item;
	        // console.log(selectedObj);
	        // $("#Nama").appendTo(".foo");
	        $("#Nama").val(selectedObj.value); 
	        temp__ =  selectedObj.value;
	        //loadSubMenu();
	        // console.log(temp);
	      },
	      /*select: function (event,  ui)
	      {

	      },*/
	      source:
	      function(req, add)
	      {
	        var url = base_url_js+'autocompleteAllUser';
	        var Nama = $('#Nama').val();
	        var data = {
	                    Nama : Nama,
	                    };
	        var token = jwt_encode(data,"UAP)(*");          
	        $.post(url,{token:token},function (data_json) {
	            var obj = JSON.parse(data_json);
	            add(obj.message) 
	        })
	      } 
	    })

	}

	$(document).on('click','.btn-delete-groupauth', function () {
	  //loading_button('.btn-edit');
	  NIP = $(this).attr('NIP');
	  $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Are you sure ? </b> ' +
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
	      var url =base_url_js+'prodi/config/authUser/cud';
	      var data = {NIP : NIP,action:'delete'};
	      var token = jwt_encode(data,'UAP)(*');
	      $.post(url,{token:token},function (data_json) {
	        loadDataGroupUser();
	        $('#NotificationModal').modal('hide');
	      });
	  })
	});

	$(document).on('click','.btn-save-groupauth', function () {
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
						thiss.prop('disabled',true);
			var NIP = $(this).closest('tr').find('.grouPAuth').attr('NIP');
			var valuee = $(this).closest('tr').find('.grouPAuth').val();
			var url =base_url_js+'prodi/config/authUser/cud';
			var data = {NIP : NIP,valuee : valuee,action:'edit'};
			var token = jwt_encode(data,'UAP)(*');
			$.post(url,{token:token},function (data_json) {
			  
			}).done(function () {
			  thiss.prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> ');
			  toastr.success('Saved');
			});			
			
		}
	});

	$(document).on('click','#btn-save-previleges-add', function () {
	    loading_button('#btn-save-previleges-add');
	    var NIP = temp__;
	    var GroupUser = $("#selectGroupuUser3").val();
	    result = Validation_required(NIP,'The Name');
	    if (result['status'] == 0) {
	        toastr.error(result['messages'], 'Failed!!');
	        $('#btn-save-previleges-add').prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save'); 
	        temp__ = '';
	    }
	    else
	    {
	        var url =base_url_js+'prodi/config/authUser/cud';
	        var data = {NIP : NIP,GroupUser : GroupUser,action:'add'};
	        var token = jwt_encode(data,'UAP)(*');
	        $.post(url,{token:token},function (data_json) {
	          var response = jQuery.parseJSON(data_json);
	          if (response != 1) {
	          	toastr.error(response);
	          }
	          else
	          {
	          	$('#Nama').val('');
	          }
	          loadDataGroupUser();
	          $('#btn-save-previleges-add').prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save');
	        }).done(function () {
	          $('#btn-save-previleges-add').prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save');
	        }).always(function() {
				$('#btn-save-previleges-add').prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Save');         
			});
	    }
	});

	function load_data_GroupPrevileges()
	{
		$("#loadtableGroupPrevileges").empty();
		$("#loadtableGroupPrevileges").html('');
		$('#loadtableGroupPrevileges').html(HTMLTblGroupPrevileges);
		    var url = base_url_js+'prodi/menu/group_previleges/crud'
			var data = {
				    action : 'read',
			};
		var token = jwt_encode(data,'UAP)(*');
		    $.post(url,{token : token},function (data_json) {
		        var response = jQuery.parseJSON(data_json);
		        // $("#loadingProcess").remove();
		        var startI = (IDDivision == 12) ? 0 : 1;
		        for (var i = startI; i < response.length; i++) {
		            $(".datatable2 tbody").append(
		                '<tr>'+
		                    '<td><input type = "text" class = "form-control GroupPrevileges" value ="'+response[i]['GroupAuth']+'" id-key = "'+response[i]['ID']+'"><div class = "hide">'+response[i]['GroupAuth']+'</div></td>'+
		                    '<td class = "btn-delete"><span class = "btn btn-xs btn-write btn-primary btn-save" id-key = "'+response[i]['ID']+'"><i class="fa fa-floppy-o" aria-hidden="true"></i> </span>&nbsp<span class = "btn btn-xs btn-delete btn-delete-groupp btn-danger btn-write" id-key = "'+response[i]['ID']+'"><i class="fa fa-trash"></i></span>'+'</td>'+
		                '</tr>' 
		                );
		        }
		    }).done(function() {
		        LoaddataTableStandard('.datatable2');
		        loadSelectGroupUser();
		    })
	}

	$(document).on('click','.btn-add-groupP', function () {
		var html = '';
		html = '<form class="form-horizontal" id="formModal">'+
        '<div class="form-group"> '+
            '<div class="row">'+
                '<div class="col-sm-4">'+
                    '<label class="control-label">Group Name:</label>'+
                '</div>   '+ 
                '<div class="col-sm-6">'+
                  '  <input type="text" id="groupName"  class="form-control" placeholder="">'+
               ' </div>'+
            '</div>'+
        '</div>'+
       ' <div style="text-align: center;">  '+     
    		'<div class="col-sm-12" id="BtnFooter">'+
                '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                '<button type="button" id="ModalbtnSaveForm2" class="btn btn-success">Save</button>'+
    		'</div>'+
        '</div>  '+  
    '</form>';
	   $('#GlobalModal .modal-header').html('<h4 class="modal-title">'+'Add Group User'+'</h4>');
	   $('#GlobalModal .modal-body').html(html);
	   $('#GlobalModal .modal-footer').html(' ');
	   $('#GlobalModal').modal({
	       'show' : true,
	       'backdrop' : 'static'
	   });
	});

	$(document).on('click','#ModalbtnSaveForm2', function () {
	    // $.removeCookie('__tawkuuid', { path: '/' });
	    loading_button('#ModalbtnSaveForm2');
	    var url = base_url_js+'prodi/menu/group_previleges/crud';
	    var groupName = $('#groupName').val();
	    var data = {
	    			action : 'add',
	                groupName : groupName,
	                };
	    var token = jwt_encode(data,"UAP)(*");
	    if (validation2(data)) {
	        $.post(url,{token:token},function (data_json) {

	        }).done(function() {
	          load_data_GroupPrevileges();
	          loadSelectGroupUser();
	          $('#GlobalModal').modal('hide');
	          toastr.success('Saved');
	        }).fail(function() {
	          toastr.error('The Database connection error, please try again', 'Failed!!');
	        }).always(function() {
	         $('#ModalbtnSaveForm2').prop('disabled',false).html('Save');

	        });
	    }
	    else
	    {
	        $('#ModalbtnSaveForm2').prop('disabled',false).html('Save');
	    }          
	    
	});

	$(document).on('click','.btn-save', function (e) {
		var ID = $(this).closest('tr').find('.GroupPrevileges').attr('id-key');
		var GroupAuth = $(this).closest('tr').find('.GroupPrevileges').val();
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
			thiss.prop('disabled',true);

			var url = base_url_js+'prodi/menu/group_previleges/crud';
			var data = {
						action : 'edit',
			            ID : ID,
			            GroupAuth : GroupAuth,
			            };
			var token = jwt_encode(data,"UAP)(*");          
			$.post(url,{token:token},function (data_json) {
			   
			}).done(function() {
			  	loadSelectGroupUser();
				toastr.success('Saved');
			}).fail(function() {
			  toastr.error('The Database connection error, please try again', 'Failed!!');
			}).always(function() {
				thiss.prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> ');  
			});	
		}
		
	});

	$(document).on('click','.btn-delete-groupp', function (e) {
	    var ID = $(this).attr('id-key');
	    var url = base_url_js+'prodi/menu/group_previleges/crud';
	    var data = {
	    			action : 'delete',
	                ID : ID,
	                };
	    var token = jwt_encode(data,"UAP)(*");
	    $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Are you sure ? </b> ' +
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
	        $.post(url,{token:token},function (data_json) {
	            $('#NotificationModal').modal('hide');
	        }).done(function() {
	          load_data_GroupPrevileges();
	          $('#NotificationModal').modal('hide');
	        }).fail(function() {
	          toastr.error('The Database connection error, please try again', 'Failed!!');
	          $('#NotificationModal').modal('hide');
	        }).always(function() {
	            $('#NotificationModal').modal('hide');
	        });
	    })          
	});

	function validation2(arr)
	{
	  var toatString = "";
	  var result = "";
	  for(var key in arr) {
	     switch(key)
	     {
	      case  "groupName" :
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

	function loadSelectGroupUser()
	{
	    var url = base_url_js+"prodi/menu/group_previleges/crud";
	    $('#selectGroupuUser').empty()
	    $('#selectGroupuUser2').empty()
	    $('#selectGroupuUser3').empty()
	    var data = {
	    			action : 'read',
	                };
	    var token = jwt_encode(data,"UAP)(*");
	    $.post(url,{token : token},function (data_json) {
	        var obj = JSON.parse(data_json);
	        	var startI = (IDDivision == 12) ? 0 : 1;
	          for(var i=startI;i<obj.length;i++){
	              var selected = (i==0) ? 'selected' : '';

	              $('#selectGroupuUser').append('<option value="'+obj[i].ID+'" '+selected+'>'+obj[i].GroupAuth+'</option>');
	              $('#selectGroupuUser2').append('<option value="'+obj[i].ID+'" '+selected+'>'+obj[i].GroupAuth+'</option>');
	              $('#selectGroupuUser3').append('<option value="'+obj[i].ID+'" '+selected+'>'+obj[i].GroupAuth+'</option>');
	          }
	          $('#selectGroupuUser').select2({
	             //allowClear: true
	          });

	          $('#selectGroupuUser2').select2({
	             //allowClear: true
	          });

	          $('#selectGroupuUser3').select2({
	             //allowClear: true
	          });
	    }).done(function () {
	    	loadSelectMenuUser();
	       // loadSubMenu();
	       loadDatamenuPrevilegesGroupUser();
	       loadDataGroupUser();
	    });
	}

	function loadSelectMenuUser()
	{
	    var url = base_url_js+"prodi/menu/save_menu";
	    $('#selectMenuUser').empty()
	    var data = {
	    			action : 'read',
	                };
	    var token = jwt_encode(data,"UAP)(*");
	    $.post(url,{token : token},function (data_json) {
	        var obj = JSON.parse(data_json);
	          for(var i=0;i<obj.length;i++){
	              var selected = (i==0) ? 'selected' : '';
	              //var selected = (data_json[i].RegionName=='Kota Jakarta Pusat') ? 'selected' : '';
	              $('#selectMenuUser').append('<option value="'+obj[i].ID+'" '+selected+'>'+obj[i].Menu+'</option>');
	          }
	          $('#selectMenuUser').select2({
	             //allowClear: true
	          });
	    }).done(function () {
	       loadSubMenu_to_group();
	      // console.log('loadmenu success');
	      // loadSelectGroupUser();
	    });
	}

	$(document).on('change','#selectMenuUser', function (e) {
		loadSubMenu_to_group();
	});

	$(document).on('change','#selectGroupuUser', function (e) {
		loadSubMenu_to_group();
	});

	function loadSubMenu_to_group()
	{
	    $("#LoadSubMenu").empty();
	    $("#LoadBtnSbmt2").empty();
	    var value = $("#selectMenuUser").val();
	    var GroupUser = $("#selectGroupuUser").val();

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

	    var url = base_url_js+"prodi/menu/group_previleges/get_submenu_by_menu";
	    var data = {
	                Menu : value,
	                GroupUser : GroupUser
	                };
	    var token = jwt_encode(data,"UAP)(*");          
	    $.post(url,{token:token},function (data_json) {
	        var obj = JSON.parse(data_json);
	        // console.log(obj);
	        var tmp1 = '<br><div class="col-sm-8">';
	        var tbl1 = '<table class="table" id ="tbl_set_submenu">';
	        var isi = '';
	        for (var i = 0; i < obj.length; i++) {
	            // var read = (obj[i]['read'] == 1) ? '<input type="checkbox" name="chkPrevilegesUser" class = "chkPrevilegesUser" value="Read" id-key= "'+obj[i]['ID']+'">&nbsp Read' : '';
	            // var write = (obj[i]['write'] == 1) ? '<input type="checkbox" name="chkPrevilegesUser" class = "chkPrevilegesUser" value="Write" id-key= "'+obj[i]['ID']+'">&nbsp Write' : '';
	            // var update = (obj[i]['update'] == 1) ? '<input type="checkbox" name="chkPrevilegesUser" class = "chkPrevilegesUser" value="Update" id-key= "'+obj[i]['ID']+'">&nbsp Update' : '';
	            // var deletee = (obj[i]['delete'] == 1) ? '<input type="checkbox" name="chkPrevilegesUser" class = "chkPrevilegesUser" value="Delete" id-key= "'+obj[i]['ID']+'">&nbsp Delete' : '';

	            isi += '<tr id-key= "'+obj[i]['ID']+'">'+
	                        '<td><input type = "checkbox"> '+'Submenu 1 :'+obj[i].SubMenu1+'</td>'+
	                        '<td>Submenu 2 :'+obj[i].SubMenu2+'</td>'+
	                        '<td>'+OPAction(obj[i]['write'])+'</td>'+
	                    '</tr>'    

	        }
	        var tbl2 = '</table>';
	        var tmp2 = '</div>';
	        $("#LoadSubMenu").html(tmp1+tbl1+isi+tbl2+tmp2);
	        if (obj.length > 0) {
	            $("#LoadBtnSbmt2").html('<div class="col-xs-12" align = "right"><button class="btn btn-inverse btn-notification btn-approve  btn-write" id="btn-sbmt-user">Submit</button></div>');
	        }
	        if (obj.length == 0) {
	            $("#LoadSubMenu").html('<br><p align="center">---No Result Data---</p>');
	        }
	        
	    })
	    
	}

	$(document).on('click','#btn-sbmt-user', function () {
		if (confirm('Are you sure ?')) {
			var getData = getAllData();
			if (getData.length > 0) {
				loading_button('#btn-sbmt-user');
				var url = base_url_js+'prodi/menu/group_previleges/save_submenu_by_menu';
				var ID_Menu = $("#selectMenuUser").val();
				var GroupUser = $("#selectGroupuUser").val();
				var data = {
				            getData : getData,
				            ID_Menu : ID_Menu,
				            ID_GroupUSer : GroupUser
				            };
				var token = jwt_encode(data,"UAP)(*");
				$.post(url,{token:token},function (data_json) {
				    // jsonData = data_json;
				    // var obj = JSON.parse(data_json); 
				    // console.log(obj);
				}).done(function() {
				  $('#btn-sbmt-user').prop('disabled',false).html('Submit');
				  loadSubMenu_to_group();
				  loadDatamenuPrevilegesGroupUser();
				  toastr.success('Saved');
				}).fail(function() {
				  toastr.error('The Database connection error, please try again', 'Failed!!');
				}).always(function() {
				    $('#btn-sbmt-user').prop('disabled',false).html('Submit');
				});
			}
			
		}

	});

	function getAllData()
	{
	    var allVals = [];
	    $('#tbl_set_submenu tbody tr').each(function() {
	    	if ($(this).find('input').is(':checked')) {
	    		var data = {
	    		     value : $(this).find('.actionCh_ option:selected').val(),
	    		     ID : $(this).attr('id-key')
	    		};
	    		allVals.push(data);
	    	}
	    });
	    return allVals;

	}


	function loadDatamenuPrevilegesGroupUser()
	{
		$('#LoadTblGroupUserPrevileges').empty();
		$('#LoadTblGroupUserPrevileges').html(HTMLTblMenuPrevilegesGroupUser);

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


	    var url = base_url_js+'prodi/menu/group_previleges/rud';
	    var Nama_search = $("#selectGroupuUser2").val();
	    var data =  {
	                    Nama_search : Nama_search,
	                    action : 'read',
	                };
	    var token = jwt_encode(data,"UAP)(*");
	// loading_page('#loadtableNow');
	    $.post(url,{token:token},function (data_json) {
	        var response = jQuery.parseJSON(data_json);
	        // console.log(response);
	        // $("#loadingProcess").remove();
	        for (var i = 0; i < response.length; i++) {
	            $("#MenuPrevilegesGroupUser tbody").append(
	                '<tr id-key = "'+response[i]['ID_previleges']+'">'+
	                    '<td>'+response[i]['GroupAuth']+'</td>'+
	                    '<td>'+response[i]['Menu']+'</td>'+
	                    '<td>'+response[i]['SubMenu1']+'</td>'+
	                    '<td>'+response[i]['SubMenu2']+'</td>'+
	                    '<td>'+OPAction(response[i]['write'])+'</td>'+
	                    '<td class = ""><span class="btn btn-xs btn-write btn-primary btn-save-previleges"><i class="fa fa-floppy-o" aria-hidden="true"></i> </span>&nbsp<span class = "btn btn-xs btn-delete-previleges btn-danger btn-write" id-key = "'+response[i]['ID_previleges']+'"><i class="fa fa-trash"></i></span>'+'</td>'+
	                '</tr>' 
	                );
	        }
	    }).done(function() {
	        // LoaddataTableStandard('#MenuPrevilegesGroupUser');
	        var table = $('#MenuPrevilegesGroupUser').DataTable({
	            'iDisplayLength' : 10,
	            'ordering' : true,
	            // "sDom": "<'row'<'dataTables_header clearfix'<'col-md-3'l><'col-md-9'Tf>r>>t<'row'<'dataTables_footer clearfix'<'col-md-6'i><'col-md-6'p>>>", // T is new
	        });
	        S_Table_example_budget = table;
	    })
	}

	$(document).on('click','.btn-save-previleges', function (e) {
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
			thiss.prop('disabled',true);
			var ID = $(this).closest('tr').attr('id-key');
			var actionCh = $(this).closest('tr').find('.actionCh option:selected').val();
			var url = base_url_js+'prodi/menu/group_previleges/rud';
			var data = {
			            ID : ID,
			            actionCh : actionCh,
			            action : 'edit',
			            };
			var token = jwt_encode(data,"UAP)(*");          
			$.post(url,{token:token},function (data_json) {
			    // jsonData = data_json;
			    // var obj = JSON.parse(data_json); 
			    // console.log(obj);
			}).done(function() {
			 	toastr.success('Saved');
			}).fail(function() {
			  toastr.error('The Database connection error, please try again', 'Failed!!');
			}).always(function() {
				thiss.prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> '); 
			}); 
		}
	    
	});

	$(document).on('click','.btn-delete-previleges', function (e) {
		var thiss = $(this);
		if (confirm('Are you sure ?')) {
			thiss.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
			thiss.prop('disabled',true);
			var ID = $(this).closest('tr').attr('id-key');
			var url = base_url_js+'prodi/menu/group_previleges/rud';
			var data = {
			            ID : ID,
			            action : 'delete',
			            };
			var token = jwt_encode(data,"UAP)(*");          
			$.post(url,{token:token},function (data_json) {
			    // jsonData = data_json;
			    // var obj = JSON.parse(data_json); 
			    // console.log(obj);
			   S_Table_example_budget
			           .row( thiss.closest('tr') )
			           .remove()
			           .draw();
			    loadSubMenu_to_group();
			}).done(function() {
			 	toastr.success('Saved');
			}).fail(function() {
			  toastr.error('The Database connection error, please try again', 'Failed!!');
			}).always(function() {
				thiss.prop('disabled',false).html('<i class="fa fa-floppy-o" aria-hidden="true"></i> '); 
			}); 
		}
	    
	});
	
	$(document).on('change','#selectGroupuUser2', function (e) {
		loadDatamenuPrevilegesGroupUser();
	});

</script>