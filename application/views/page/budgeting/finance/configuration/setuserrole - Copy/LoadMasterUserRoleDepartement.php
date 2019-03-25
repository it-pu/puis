<style type="text/css">
	#tableData9 thead th,#tableData9 tfoot td {

	    text-align: center;
	    background: #20485A;
	    color: #FFFFFF;

	}

	#tableData9>thead>tr>th, #tableData9>tbody>tr>th, #tableData9>tfoot>tr>th, #tableData9>thead>tr>td, #tableData9>tbody>tr>td, #tableData9>tfoot>tr>td {
	    border: 1px solid #b7b7b7
	}

	.btn span.glyphicon {    			
		opacity: 0;				
	}
	.btn.active span.glyphicon {				
		opacity: 1;				
	}
</style>
<div class="row" style="margin-right: 0px;margin-left: 0px;margin-top: 10px" id = "loadPageTable">
	
</div>

<script type="text/javascript">
	var dt = <?php echo json_encode($dt) ?>;
	var cfg_m_userrole = <?php echo json_encode($cfg_m_userrole) ?>;
	$(document).ready(function() {
		LoadFirstLoad();
	}); // exit document Function

	function LoadFirstLoad()
	{
		loadingStart();
		var url = base_url_js+'budgeting/table_all/cfg_post/1';
		$.post(url,function (resultJson) {
			$("#loadPageTable").empty();
			var ActionAdd = '<div class = "row">'+
								'<div class = "col-xs-2">'+
									'<span data-smt="" class="btn btn-add">'+
	                    				'<i class="icon-plus"></i> Add'+
	               					'</span>'+
	               				'</div>'+
	               				'<div class = "col-xs-2">'+
									'<p style="color: red;font-size: 20px">(.000)</p>'+
	               				'</div>'+	
               				'</div>';	
			var TableGenerate = '<div class = "row" style = "margin-top : 10px;margin-left : 0px ; margin-right : 0px">'+
									'<div class = "col-md-12">'+
										'<div class="table-responsive">'+
											'<table class="table table-bordered tableData" id ="tableData9">'+
											'<thead>'+
											'<tr>'+
												'<th width = "3%">Post</th>'+
					                            '<th>Amount Limit</th>';
	        for (var i = 0; i < cfg_m_userrole.length; i++) {
	        	TableGenerate += '<th>'+cfg_m_userrole[i]['NameUserRole']+'</th>';
	        }
				TableGenerate += '</tr></thead>'	
								;
			TableGenerate += '<tbody></tbody></table></div></div></div>';
			var ActionSave = '<div class = "row sdsadasd" style = "margin-top : 10px;margin-left : 0px ; margin-right : 0px">'+
								'<div class = "col-md-2 col-md-offset-10" align = "right">'+
									'<button type="button" id="SaveForm" class="btn btn-success">Save</button>'+
								'</div>'+
							'</div>';		
			$("#loadPageTable").html(ActionAdd+TableGenerate+ActionSave);
			var resultJson = jQuery.parseJSON(resultJson);
			if (dt.length > 0) {
				// data existing show
				funcFillTbodyExisting(resultJson);
			}
			else
			{
				funcFillTbody(resultJson);
			}
			funcAddbtn(resultJson);
			funcsavebtn(resultJson);
		}).fail(function() {
		  toastr.info('No Result Data'); 
		}).always(function() {
		    loadingEnd(500);         	   
		});
	}

	function funcFillTbodyExisting(resultJson)
	{
		// var UserType = ['Admin','Approver 1','Approver 2','Approver 3','Approver 4'];
		var UserType = [];
		for (var i = 0; i < cfg_m_userrole.length; i++) {
			UserType.push(cfg_m_userrole[i]['NameUserRole']);
		}

			// grouping MaxLimit
			var NextInc = parseInt(resultJson.length) * parseInt(UserType.length);
			var MaxLimit = [];
			for (var i = 0; i < dt.length; i=i+NextInc) {
				MaxLimit.push(dt[i].MaxLimit)
			}

			var FieldType = ['Entry','Approved','Cancel'];
			var zStart = 0;
			for (var i = 0; i < MaxLimit.length; i++) {
				var fill = '<tr>';
				var fillPost = '';
				for (var ii = 0; ii < resultJson.length; ii++) {
					var Style = (ii != 0) ? 'style = "margin-top : 10px"' : '';
					fillPost += '<div class = "row" '+Style+'><div class = "col-md-12">'+resultJson[ii]['PostName']+'</div></div>';
				}
				var Post = '<td class = "post">'+fillPost+'</td>';
				fill += Post;
				var Limit = parseInt(MaxLimit[i]) / 1000;
				fill += '<td>'+'<input type = "text" class = "form-control AmountLimit" value = "'+Limit+'">'+'</td>';	
				for (var j = 0; j < UserType.length; j++) {
					var ID_user = j + 1;
					// find MaxLimit,ID_m_userrole && FieldType
					var temp = {};
					var chk = '';
					for (var z = zStart; z < dt.length; z++) {
						if (MaxLimit[i] == dt[z].MaxLimit && ID_user == dt[z].ID_m_userrole) 
						{
							
							temp = dt[z];
							zStart = z;
							break;
						}
					} // end loop find

					for (var k = 0; k < FieldType.length; k++) {
						for(var key in temp) {
							if (key == FieldType[k]) {
								var checked = (temp[key] == 1) ? 'checked' : '';
								chk += '<div class = "row">'+
										'<div class = "col-md-12">'+
											'<div class = "form-group">'+
												'<label>'+FieldType[k]+'</label>'+
												'<input type="checkbox" class = "form-control user" id_table ="'+ID_user+'" useraction = "'+key+'" style = "height : 15px" '+checked+'>'+
											'</div>'+	
										'</div>'+
									  '</div>';	
							}
						}
					} // end loop field type

					if (chk == '') { // adding for sisa
						chk = '<div class = "row">'+
							'<div class = "col-md-12">'+
								'<div class = "form-group">'+
									'<label>Entry</label>'+
									'<input type="checkbox" class = "form-control user" id_table ="'+ID_user+'" useraction = "'+'Entry'+'" style = "height : 15px">'+
								'</div>'+	
							'</div>'+
						  '</div>';	
						chk += '<div class = "row" '+'style = "margin-top : 10px"'+'>'+
									'<div class = "col-md-12">'+
										'<div class = "form-group">'+
											'<label>Approve</label>'+
											'<input type="checkbox" class = "form-control user" id_table ="'+ID_user+'" useraction = "'+'Approved'+'" style = "height : 15px">'+
										'</div>'+	
									'</div>'+
								  '</div>';	
						chk += '<div class = "row" '+'style = "margin-top : 10px"'+'>'+
									'<div class = "col-md-12">'+
										'<div class = "form-group">'+
											'<label>Cancel</label>'+
											'<input type="checkbox" class = "form-control user" id_table ="'+ID_user+'" useraction = "'+'Cancel'+'" style = "height : 15px">'+
										'</div>'+	
									'</div>'+
								  '</div>';		  		  
					}
					fill += '<td align = "center">'+chk+'</td>';
				} // end loop user type
				var action = '<button type="button" class="btn btn-danger btn-delete"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';
				fill += '<td>'+action+'</td>';
				fill += '</tr>';
				$('#tableData9 tbody').append(fill);
			}
			// $('.AmountLimit').val(0);
			$('.AmountLimit').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			$('.AmountLimit').maskMoney('mask', '9894');

			$(".btn-delete").click(function(){
				$( this )
				  .closest( 'tr'  )
				  .remove();
			})
	}

	function funcFillTbody(resultJson)
	{
		var fill = '<tr>';
			var fillPost = '';
			for (var i = 0; i < resultJson.length; i++) {
				var Style = (i != 0) ? 'style = "margin-top : 10px"' : '';
				fillPost += '<div class = "row" '+Style+'><div class = "col-md-12">'+resultJson[i]['PostName']+'</div></div>';
			}
			fill += '<td class = "post">'+fillPost+'</td>';
			fill += '<td>'+'<input type = "text" class = "form-control AmountLimit" value = "0">'+'</td>';
			for (var i = 0; i < cfg_m_userrole.length; i++) {
				var ID = i + 1;
				var chk = '<div class = "row">'+
							'<div class = "col-md-12">'+
								'<div class = "form-group">'+
									'<label>Entry</label>'+
									'<input type="checkbox" class = "form-control user" id_table ="'+ID+'" useraction = "'+'Entry'+'" style = "height : 15px">'+
								'</div>'+	
							'</div>'+
						  '</div>';	
				chk += '<div class = "row" '+'style = "margin-top : 10px"'+'>'+
							'<div class = "col-md-12">'+
								'<div class = "form-group">'+
									'<label>Approve</label>'+
									'<input type="checkbox" class = "form-control user" id_table ="'+ID+'" useraction = "'+'Approved'+'" style = "height : 15px">'+
								'</div>'+	
							'</div>'+
						  '</div>';	
				chk += '<div class = "row" '+'style = "margin-top : 10px"'+'>'+
							'<div class = "col-md-12">'+
								'<div class = "form-group">'+
									'<label>Cancel</label>'+
									'<input type="checkbox" class = "form-control user" id_table ="'+ID+'" useraction = "'+'Cancel'+'" style = "height : 15px">'+
								'</div>'+	
							'</div>'+
						  '</div>';		  		  
				fill += '<td align = "center">'+chk+'</td>';
			}
			var action = '<button type="button" class="btn btn-danger btn-delete"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';
			fill += '<td>'+action+'</td>';
			fill += '</tr>';
		$('#tableData9 tbody').append(fill);
		// $('.AmountLimit').val(0);
		$('.AmountLimit').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
		$('.AmountLimit').maskMoney('mask', '9894');

		$(".btn-delete").click(function(){
			$( this )
			  .closest( 'tr'  )
			  .remove();
		})
	}

	function funcAddbtn(resultJson)
	{
		$(".btn-add").click(function(){
			funcFillTbody(resultJson);
		})
	}

	function funcsavebtn(resultJson)
	{
		$("#SaveForm").click(function(){
			var AmountLimit = [];
			$(".AmountLimit").each(function(){
				AmountLimit.push($(this).val());
			})

			// dynamic
				var arr = [];
				for (var i = 0; i < cfg_m_userrole.length; i++) {
					var id_table = i + 1;
					var id_table_arr = []
					$('input[id_table="'+id_table+'"]').each(function(){
						if ($(this).is(':checked')) {
							aa = 1;
						}
						else
						{
							aa = 0;
						}
						var temp = {};
						temp[$(this).attr('useraction')] = aa;
						id_table_arr.push(temp);
					})

					var temp2 = {};
					// temp2['A'+id_table] = id_table_arr;
					arr['A'+id_table] = id_table_arr;
				}

				var FormInsert = [];
				for (var i = 0; i < AmountLimit.length; i++) {
					var GroupPrivileges = ((i+1) * 3);
					var Start = GroupPrivileges - 3;
					for (var l = 0; l < resultJson.length; l++) {
						var MaxLimit = AmountLimit[i];
						MaxLimit = findAndReplace(MaxLimit, ".","");
						MaxLimit = parseInt(MaxLimit) * 1000; // for ribuan
						var CodePost = resultJson[l].CodePost;
						for (var m = 0; m < cfg_m_userrole.length; m++) {
							ID_m_userrole = m+1;
							Temp = {
								MaxLimit : MaxLimit,
								CodePost : CodePost,
								ID_m_userrole : ID_m_userrole,
							};

							// check entry existing
							for (var j = Start; j < GroupPrivileges; j++) {
								var get = arr['A'+ID_m_userrole];
								get = get[j];
								for(var key in get) {
									Temp[key] = get[key];
								}
							}
							FormInsert.push(Temp);
						}

					}
				}

			loadingStart();
			var url = base_url_js+'budgeting/configRule/userroledepart_submit';
			var data = FormInsert;
    	    var token = jwt_encode(data,"UAP)(*");
    	    $.post(url,{token:token},function (data_json) {
    	    	var response = jQuery.parseJSON(data_json);
    	    	if (response == '') {
    	    		LoadMasterUserRoleDepartement();
    	    		toastr.success('Data berhasil disimpan', 'Success!');
    	    	}
    	    	else
    	    	{
    	    		toastr.error(response,'!Failed');
    	    	}
    	    	loadingEnd(500);
			}).fail(function() {
			  toastr.info('No Result Data'); 
			  loadingEnd(500);
			}).always(function() {
			    loadingEnd(500);         	   
			});
		})
	}
</script>