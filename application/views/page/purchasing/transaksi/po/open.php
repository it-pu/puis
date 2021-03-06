<style type="text/css">
	.thumbnail {
	    display: inline-block;
	    display: block;
	    height: auto;
	    max-width: 100%;
	    padding: 16px;
	    line-height: 1.428571429;
	    background-color: #fff;
	    border: 1px solid #aec10b;
	    border-radius: 20px;
	    -webkit-transition: all .2s ease-in-out;
	    transition: all .2s ease-in-out;
	}

	#datatablesServer.dataTable tbody tr:hover {
	   background-color:#71d1eb !important;
	   cursor: pointer;
	}

	h3.header-blue {
	    margin-top: 0px;
	    border-left: 7px solid #2196F3;
	    padding-left: 10px;
	    font-weight: bold;
	}
</style>
<div class="row">
	<div class="col-xs-4">
		<div class="thumbnail">
			<div id = "page_pr_list"></div>
		</div>	
	</div>
	<div class="col-xs-8">
		<div class="thumbnail">
			<div id = "page_pr_item_list"></div>
		</div>	
	</div>
</div>

<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12">
		<div class="thumbnail">
			<div id = "page_pr_selected_list"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var ClassDt = {
		Dt_selection : [],
		ThisTableSelect : '',
		Dt_ChooseSelectPR : [],
		action_mode : '<?php echo $action_mode ?>',
		POCode : '<?php echo $POCode ?>',
		POData : [],
		htmlPage_pr_list : function(){
			var html = '';
			html = '<div class = "row" style = "margin-right : 0px;margin-left:0px;">'+
					 '<div class col-md-12>'+
					 	'<div style="padding: 5px;">'+
					 		'<h3 class="header-blue">Choose PR</h3>'+
					 	'</div>'+
					 	'<div class = "table-responsive">'+
					 	'<table class="table table-bordered datatable2" id = "tableData_pr">'+
					 		'<thead>'+
					 			'<tr>'+
					 				'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
					 				'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">PR Code</th>'+
					 				'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Department</th>'+
					 				'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">File</th>'+
					 			'</tr>'+
					 		'<thead>'+
					 		'<tbody id="dataRow" style="background-color: #8ED6EA;"></tbody>'+
		        		'</table>'+
		        		'</div>'+
		        	 '</div>'+
		        	'</div>';

		    return html;    	 				
		},	
	};

	$(document).ready(function() {
		// Show number PO if edit
		if (ClassDt.POCode != '') {
			var r = $('#page_pr_list').closest('.row');
			var html = '<div class = "row">'+
							'<div class = "col-xs-12" align="center">'+
								'<h3><b>Code : '+ClassDt.POCode+'</b></h3>'+
							'</div>'+
						'</div>';

			r.before(html);					

		}
	    $('#page_pr_list').html(ClassDt.htmlPage_pr_list);
	    skip_error_dt_table();
		    Get_data_pr().then(function(data){
		    	// cek data apakah edit atau new
		    	if (ClassDt.POCode == '') {
		    		loadingEnd(500);
		    		$('.C_radio_pr:first').prop('checked',true);
		    		$('.C_radio_pr:first').trigger('change');
		    	}
		    	else
		    	{
		    		Get_data_open_po_created_detail().then(function(data){
		    			ClassDt.POData = data;
		    			var resultJson = data['po_detail'];
		    			var temp = ClassDt.Dt_selection;
		    			for (var i = 0; i < resultJson.length; i++) {
		    				temp.push(resultJson[i].ID_pr_detail);
		    			}
		    			ClassDt.Dt_selection = temp;
		    			var waitForEl = function(selector, callback) {
		    			  if (jQuery(selector).length) {
		    			    callback();
		    			  } else {
		    			    setTimeout(function() {
		    			      waitForEl(selector, callback);
		    			    }, 100);
		    			  }
		    			};

		    			// get ALL PR
    						LoadPRSelected(data).then(function(data){
    							// SelectedPR_selection(data);
    							waitForEl(".id_pr_detail", function() {
    							  $('.id_pr_detail:first').trigger('change');
    							});

    							$('.C_radio_pr:first').prop('checked',true);
    							$('.C_radio_pr:first').trigger('change');
    							loadingEnd(1000);
    						})
		    		})
		    	}
		        
		        
		    })
	}); // exit document Function

	function LoadPRSelected(dt)
	{
	   var po_detail = dt['po_detail'];
	   var arr = [];
	   for (var i = 0; i < po_detail.length; i++) {
	   	arr.push(po_detail[i].PRCode);
	   }
       var def = jQuery.Deferred();
       var url = base_url_js + 'rest/__show_pr_detail_multiple_pr_code';
       var data = {
           PRCode : arr,
           auth : 's3Cr3T-G4N',
           POCode : ClassDt.POCode,
       };
       var token = jwt_encode(data,"UAP)(*");
       $.post(url,{ token:token },function (resultJson) {
       		def.resolve(resultJson);
       }).fail(function() {
       	  def.reject();
		  toastr.error('The Database connection error, please try again', 'Failed!!');
		}).always(function() {

		});
       return def.promise();
	}

	function Get_data_open_po_created_detail(){
       var def = jQuery.Deferred();
       var url = base_url_js + 'rest2/__Get_data_po_by_Code';
       var data = {
           auth : 's3Cr3T-G4N',
           Code : ClassDt.POCode,
       };
       var token = jwt_encode(data,"UAP)(*");
       $.post(url,{ token:token },function (resultJson) {
       		def.resolve(resultJson);
       }).fail(function() {
       	  def.reject();
		  toastr.error('The Database connection error, please try again', 'Failed!!');
		}).always(function() {

		});
       return def.promise();
	}

	function Get_data_pr(){
	   var action_edit = (ClassDt.POCode == '') ? '': 'edit';
       var def = jQuery.Deferred();
       var data = {
           PurchasingStatus : '!=2',
           auth : 's3Cr3T-G4N',
           Item_pending : '>0',
           action_edit : action_edit,
           POCode : ClassDt.POCode,
       };
       var token = jwt_encode(data,"UAP)(*");
       	var table = $('#tableData_pr').DataTable({
       		"fixedHeader": true,
       	    "processing": true,
       	    "destroy": true,
       	    "serverSide": true,
       	    "lengthMenu": [[5], [5]],
       	    "iDisplayLength" : 5,
       	    "ordering" : false,
       	    "ajax":{
       	        url : base_url_js+"rest/__get_data_pr/2", // json datasource
       	        ordering : false,
       	        type: "post",  // method  , by default get
       	        data : {token : token},
       	        error: function(){  // error handling
       	            $(".employee-grid-error").html("");
       	            $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
       	            $("#employee-grid_processing").css("display","none");
       	            def.reject();

       	        },
       	    },
       	    "columns": [
       	                { "data": "No" },
       	                { "data": "PRCode" },
       	                { "data": "NameDepartement" },
       	                { "data": "Supporting_documents" },
       	    ],
       	    'createdRow': function( row, data, dataIndex ) {
       	    		$(row).find('td:eq(1)').html('<label><input type="radio" name="optradio" prcode = "'+data['PRCode']+'" class = "C_radio_pr">&nbsp'+data['PRCode']+'</label>');
       	    		$( row ).find('td:eq(2)').attr('align','center');
       	    		$( row ).find('td:eq(0)').attr('align','center');
       	    		var FileJson = jQuery.parseJSON(data['Supporting_documents']);
       	    		var html = '';
       	    		for (var i = 0; i < FileJson.length; i++) {
       	    			html += '<li>'+'<a href = "'+base_url_js+'fileGetAny/budgeting-pr-'+FileJson[i]+'" target="_blank" class = "Fileexist">File '+(i+1)+'</a>'+'</li>';
       	    		}
       	    		$( row ).find('td:eq(3)').html(html);
       	    },
       	    "initComplete": function(settings, json) {
       	        def.resolve(json);
       	    }
       	});
       return def.promise();
	}

	function skip_error_dt_table()
	{
		$.fn.dataTable.ext.errMode = 'throw';
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
	}

	$(document).off('change', '.C_radio_pr:checked').on('change', '.C_radio_pr:checked',function(e) {
		var PRCode = $(this).attr('prcode');
		var url = base_url_js+'rest/__show_pr_detail';
   		var data = {
   		    PRCode : PRCode,
   		    auth : 's3Cr3T-G4N',
   		    POCode : ClassDt.POCode,
   		};
   		var token = jwt_encode(data,"UAP)(*");
   		$.post(url,{ token:token },function () {

		}).done(function(data_json) {
	    	MakeDom_page_pr_item_list(data_json);
	    });

	})

	function MakeDom_page_pr_item_list(dt)
	{
		var pr_detail = dt.pr_detail;
		var IsiInputPR = '';
		var Dt_selection = ClassDt.Dt_selection;
		for (var i = 0; i < pr_detail.length; i++) {

			// for detail catalog
				var Desc = pr_detail[i]['Desc'];
				var EstimaValue = pr_detail[i]['EstimaValue'];
				var arr_Photo = pr_detail[i]['Photo'];
				htmlPhoto = '';
				if (arr_Photo != '' && arr_Photo != undefined && arr_Photo != null) {
					arr_Photo = arr_Photo.split(',');
					htmlPhoto = '<ul>';
					for (var j = 0; j < arr_Photo.length; j++) {
						htmlPhoto += '<li><a href = "'+base_url_js+'fileGetAny/budgeting-catalog-'+arr_Photo[j]+'" target="_blank">'+
											arr_Photo[j]+'</a></li>';
					}
					htmlPhoto += '</ul>';
				}
				
				var DetailCatalog = jQuery.parseJSON(pr_detail[i]['DetailCatalog']);
				var htmlDetailCatalog = '';
				for (var prop in DetailCatalog) {
					htmlDetailCatalog += prop + ' :  '+DetailCatalog[prop]+'<br>';
				}
				var Item = pr_detail[i]['Item'];
				var arr = Item+'@@'+Desc+'@@'+EstimaValue+'@@'+htmlPhoto+'@@'+htmlDetailCatalog;
				arr = findAndReplace(arr, "\"","'");

				var SpecAdd = (pr_detail[i]['Spec_add'] == '' || pr_detail[i]['Spec_add'] == null || pr_detail[i]['Spec_add'] == 'null') ? '' : pr_detail[i]['Spec_add'];
				var Need = (pr_detail[i]['Need'] == '' || pr_detail[i]['Need'] == null || pr_detail[i]['Need'] == 'null') ? '' : pr_detail[i]['Need'];

			var checked = '';
			for (var j = 0; j < Dt_selection.length; j++) {
				if (Dt_selection[j]== pr_detail[i]['ID']) {
					checked = 'checked';
					break;
				}
			}

			// fileupload
			var Jsonfileupload = jQuery.parseJSON(pr_detail[i]['UploadFile']);
			var Htmlfileupload = '';
			for (var k = 0; k < Jsonfileupload.length; k++) {
					Htmlfileupload += '<li>'+'<a href = "'+base_url_js+'fileGetAny/budgeting-pr-'+Jsonfileupload[i]+'" target="_blank" class = "Fileexist">File '+(i+1)+'</a>'+'</li>';
				}	
			IsiInputPR += '<tr>'+
							'<td>'+(i+1)+'</td>'+
							'<td>'+	'<input type = "checkbox" id_pr_detail = "'+pr_detail[i]['ID']+'" '+checked+' class = "id_pr_detail"> '+pr_detail[i]['Item']+'</td>'+
							'<td>'+'<button class = "btn btn-primary Detail" data = "'+arr+'">Detail</button>'+'</td>'+
							'<td>'+	SpecAdd+'</td>'+
							'<td>'+	Need+'</td>'+
							'<td>'+	pr_detail[i]['Qty']+'</td>'+
							'<td>'+	formatRupiah(pr_detail[i]['UnitCost'])+'</td>'+
							'<td>'+	parseInt(pr_detail[i]['PPH'])+'</td>'+
							'<td>'+	formatRupiah(pr_detail[i]['SubTotal'])+'</td>'+
							'<td>'+	pr_detail[i]['DateNeeded']+'</td>'+
							'<td>'+	Htmlfileupload+'</td>'+
						  '</tr>';	

		}

		var  htmlInputPR= '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "Page_PR">'+
							'<div style="padding: 5px;">'+
								'<h3 class="header-blue">'+$('.C_radio_pr:checked').attr('prcode')+'</h3>'+
							'</div>'+
							'<div class = "col-md-12">'+
								'<div class="table-responsive">'+
									'<table class="table table-bordered tableData" id ="table_data_pr_detail">'+
										'<thead>'+
										'<tr>'+
											'<th width = "3%" style = "text-align: center;background: #607D8B;color: #FFFFFF;">No</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;width : 150px;">Catalog</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;">Detail</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;">Spec+</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;">Desc</th>'+
				                            '<th width = "4%" style = "text-align: center;background: #607D8B;color: #FFFFFF;width : 78px;">Qty</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;width : 150px;">Cost</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;width : 78px;">PPN(%)</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;width : 150px;">Sub Total</th>'+
				                            '<th width = "150px" style = "text-align: center;background: #607D8B;color: #FFFFFF;">Date Needed</th>'+
				                            '<th style = "text-align: center;background: #607D8B;color: #FFFFFF;">File</th>'+
										'</tr>'+
										'</thead>'+
										'<tbody style = "background-color : #eade8e;">'+IsiInputPR+'</tbody></table>'+
									'</div>'+
								'</div>'+
							'</div>';

		$('#page_pr_item_list').html(htmlInputPR);
		SelectedPR_selection(dt);
							
	}

	function SelectedPR_selection(data)
	{
		var Dt_ChooseSelectPR = ClassDt.Dt_ChooseSelectPR;
		if (Dt_ChooseSelectPR.length > 0) {
			var pr_create = data.pr_create;
			var PRCode = pr_create[0]['PRCode'];
			var bool = true;
			for (var i = 0; i < Dt_ChooseSelectPR.length; i++) {
				var pr_create_ = Dt_ChooseSelectPR[i].pr_create;
				var PRCode_ = pr_create_[0]['PRCode'];
				if (PRCode == PRCode_) {
					bool = false;
					break;
				}
			}

			if (bool) {
				Dt_ChooseSelectPR.push(data);
			}
		}
		else
		{
			Dt_ChooseSelectPR.push(data);
		}

	}

	$(document).off('change', '.id_pr_detail').on('change', '.id_pr_detail',function(e) {
		var ID_pr_detail = $(this).attr('id_pr_detail');
		var Dt_selection = ClassDt.Dt_selection;
		if ($(this).is(':checked')) {
			var bool = true;
			for (var i = 0; i < Dt_selection.length; i++) {
				if (Dt_selection[i] == ID_pr_detail) {
					bool = false;
					break;
				}
			}

			if (bool) {
				Dt_selection.push(ID_pr_detail);
			}
		}
		else
		{
			var arr = [];
			for (var i = 0; i < Dt_selection.length; i++) {
				if (Dt_selection[i] != ID_pr_detail) {
					arr.push(Dt_selection[i]);
				}
			}

			Dt_selection = arr;
			ClassDt.Dt_selection = Dt_selection;
		}
		ShowClassDt_selection();
	})

	function ShowClassDt_selection()
	{
		var html = '';
		var IsiInputPR = '';
		var Dt_ChooseSelectPR = ClassDt.Dt_ChooseSelectPR;
		var Dt_selection = ClassDt.Dt_selection;

		for (var i = 0; i < Dt_selection.length; i++) {
			var ID_pr_detail = Dt_selection[i];
			for (var j = 0; j < Dt_ChooseSelectPR.length; j++) {
				var pr_detail = Dt_ChooseSelectPR[j].pr_detail;
				for (var k = 0; k < pr_detail.length; k++) {
					var ID_pr_detail_ = pr_detail[k].ID;
					if (ID_pr_detail_ == ID_pr_detail) {
						// for detail catalog
							var Desc = pr_detail[k]['Desc'];
							var EstimaValue = pr_detail[k]['EstimaValue'];
							var arr_Photo = pr_detail[k]['Photo'];
							htmlPhoto = '';
							if (arr_Photo != '' && arr_Photo != null && arr_Photo != undefined) {
								arr_Photo = arr_Photo.split(',');
								htmlPhoto = '<ul>';
								for (var l = 0; l < arr_Photo.length; l++) {
									htmlPhoto += '<li><a href = "'+base_url_js+'fileGetAny/budgeting-catalog-'+arr_Photo[l]+'" target="_blank">'+
														arr_Photo[l]+'</a></li>';
								}
								htmlPhoto += '</ul>';
							}
							
							var DetailCatalog = jQuery.parseJSON(pr_detail[k]['DetailCatalog']);
							var htmlDetailCatalog = '';
							for (var prop in DetailCatalog) {
								htmlDetailCatalog += prop + ' :  '+DetailCatalog[prop]+'<br>';
							}
							var Item = pr_detail[k]['Item'];
							var arr = Item+'@@'+Desc+'@@'+EstimaValue+'@@'+htmlPhoto+'@@'+htmlDetailCatalog;
							arr = findAndReplace(arr, "\"","'");

							var SpecAdd = (pr_detail[k]['Spec_add'] == '' || pr_detail[k]['Spec_add'] == null || pr_detail[k]['Spec_add'] == 'null') ? '' : pr_detail[k]['Spec_add'];
							var Need = (pr_detail[k]['Need'] == '' || pr_detail[k]['Need'] == null || pr_detail[k]['Need'] == 'null') ? '' : pr_detail[k]['Need'];


						IsiInputPR += '<tr>'+
							'<td>'+(i+1)+' <input type = "checkbox" id_pr_detail = "'+pr_detail[k]['ID']+'" '+'checked'+' class = "id_pr_detail_selected">'+'</td>'+
							'<td>'+pr_detail[k]['PRCode']+'</td>'+
							'<td>'+	' '+pr_detail[k]['Item']+'</td>'+
							'<td>'+'<button class = "btn btn-primary Detail" data = "'+arr+'">Detail</button>'+'</td>'+
							'<td>'+	SpecAdd+'</td>'+
							'<td>'+	Need+'</td>'+
							'<td>'+	pr_detail[k]['Qty']+'</td>'+
							'<td>'+	formatRupiah(pr_detail[k]['UnitCost'])+'</td>'+
							'<td>'+	parseInt(pr_detail[k]['PPH'])+'</td>'+
							'<td>'+	formatRupiah(pr_detail[k]['SubTotal'])+'</td>'+
							'<td>'+	pr_detail[k]['DateNeeded']+'</td>'+
						  '</tr>';						
						j = parseInt(Dt_ChooseSelectPR.length) + 1; 
						break;
					}
				}
			}
		}

			html = '<div class = "row" style = "margin-right : 0px;margin-left:0px;">'+
					 '<div class col-md-12>'+
					 	'<div style="padding: 5px;">'+
					 		'<h3 class="header-blue">PR Selected</h3>'+
					 	'</div>'+
					 	'<div class = "table-responsive">'+
					 	'<table class="table table-bordered datatable2" id = "tableData_pr_selected">'+
					 		'<thead>'+
								'<tr>'+
									'<th width = "3%" style = "text-align: center;background: #67a9a2;color: #FFFFFF;">No</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;width : 150px;">PR Code</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;width : 150px;">Catalog</th>'+
		                            '<th width = "4%" style = "text-align: center;background: #67a9a2;color: #FFFFFF;">Detail</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;">Spec+</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;">Desc</th>'+
		                            '<th width = "4%" style = "text-align: center;background: #67a9a2;color: #FFFFFF;width : 78px;">Qty</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;width : 150px;">Cost</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;width : 78px;">PPN(%)</th>'+
		                            '<th style = "text-align: center;background: #67a9a2;color: #FFFFFF;width : 150px;">Sub Total</th>'+
		                            '<th width = "150px" style = "text-align: center;background: #67a9a2;color: #FFFFFF;">Date Needed</th>'+
								'</tr>'+
							'</thead>'+
					 		'<tbody>'+IsiInputPR+'</tbody>'+
		        		'</table>'+
		        		'</div>'+
		        	 '</div>'+
		        	'</div>'+
		        	'<div class = "row" style = "margin-top : 10px;">'+
		        		'<div class = "col-md-12">'+
		        			'<div style="padding: 5px;">'+
		        				'<h3 class="header-blue">Choose Vendor</h3>'+
		        			'</div>'+
		        			'<div class = "row">'+
		        				'<div class = "col-xs-3">'+
		        					'<div class="thumbnail">'+
		        						'<div class="form-group">'+
				                            '<label style= "color : red">Choose Total Vendor</label>'+
				                            '<select class="form-control" id="ChooseTotVendor" style = "width : 140px;">'+
				                            	'<option value="1">1</option>'+
				                            	'<option value="2">2</option>'+
				                            	'<option value="3" selected>3</option>'+
				                            	'<option value="4">4</option>'+
				                            	'<option value="5">5</option>'+
				                            '</select>'+
				                        '</div>'+
				                    '</div>'+
				                '</div>'+
				                '<div class = "col-xs-9">'+
				                	'<div class="thumbnail">'+
				                		'<div id = "PageSearchVendor"></div>'+
				                	'</div>'+	
				                '<div>'+
				            '</div>'+
				        '</div>'+
				    '</div>'+
				    '<div class = "row" style = "margin-top : 10px;">'+
				    	'<div class = "col-md-4">'+
				    		'<button class="btn btn-primary" id="OpenPO">Open PO</button>'+' '+
				    		'<button class="btn btn-success" id="OpenSPK">Open SPK</button>'+' '+
				    		'<button class="btn btn-warning" id="Clear">Clear</button>'+
				    	'</div>'+
				    '</div>'		
		        	;
		$('#page_pr_selected_list').html(html);
		// for edit
			if (ClassDt.POCode != '') {
				$('#Clear').remove();
				var POData = ClassDt.POData;
				var po_create = POData['po_create'];
				if (po_create[0]['TypeCode'] == 'PO') {
					$('#OpenSPK').remove();
				}
				else
				{
					$('#OpenPO').remove();
				}
			
				Get_data_open_po_created_supplier().then(function(data){
					var Tot = data.length;
					
					$("#ChooseTotVendor option").filter(function() {
					   //may want to use $.trim in here
					   return $(this).val() == Tot; 
					 }).prop("selected", true);
					$('#ChooseTotVendor').trigger('change'); 
					// fill isi table supplier
					var TblTbody = $('#Tbl_selectVendor tbody');
					for (var i = 0; i < Tot; i++) {
						var html = '<b>'+data[i].NamaSupplier+'</b>'+'</br>'+data[i].Website+'</br>'+data[i].PICName+'</br>'+data[i].Alamat;
						TblTbody.find('tr:eq('+i+')').find('td:eq(1)').find('.LblNmVendor').html(html);
						TblTbody.find('tr:eq('+i+')').find('td:eq(1)').find('.LblNmVendor').attr('idtable',data[i].CodeSupplier);
						var aa = html;	
						var arr = '1'+'@@'+'<div align="center"><b>'+data[i].CategoryName+'</b></div>'+'@@'+aa+'@@'+'';
						TblTbody.find('tr:eq('+i+')').find('.Detail_Vendor').attr('data',arr);
						var LinkFileOffer = jQuery.parseJSON(data[i].FileOffer);
						var S_link = TblTbody.find('tr:eq('+i+')').find('td:eq(3)').find('div');
						html = '';
						for (var j = 0; j < LinkFileOffer.length; j++) {
							html += '<li><a href= "'+base_url_js+'fileGetAny/budgeting-po-'+LinkFileOffer[j]+'" target = "_blank">File Offer</a></li>';
						}

						S_link.after(html);
						// console.log(data[i]);
						if (data[i].ApproveSupplier == 1) {
							TblTbody.find('tr:eq('+i+')').find('.C_radio_approve[value="1"]').prop('checked',true);
							TblTbody.find('tr:eq('+i+')').find('.C_radio_approve[value="0"]').prop('checked',false);
							TblTbody.find('tr:eq('+i+')').find('.C_radio_approve[value="1"]').closest('td').append('<div class = "DivNote">'+
									'<label>Reason</label>'+
									'<textarea class = "form-control Notes">'+data[i].Desc+'</textarea>'+
							  '</div>');
						}
						else
						{
							TblTbody.find('tr:eq('+i+')').find('.C_radio_approve[value="1"]').prop('checked',false);
							TblTbody.find('tr:eq('+i+')').find('.C_radio_approve[value="0"]').prop('checked',true);
						}
					}

				})
			}
			else{
				$('#ChooseTotVendor').trigger('change'); 
			}	
		       	
	}

	function Get_data_open_po_created_supplier()
	{
       var def = jQuery.Deferred();
       var url = base_url_js + 'rest2/__Get_supplier_po_by_Code';
       var data = {
           auth : 's3Cr3T-G4N',
           Code : ClassDt.POCode,
       };
       var token = jwt_encode(data,"UAP)(*");
       $.post(url,{ token:token },function (resultJson) {
       		def.resolve(resultJson);
       }).fail(function() {
       	  def.reject();
		  toastr.error('The Database connection error, please try again', 'Failed!!');
		}).always(function() {

		});
       return def.promise();
	}

	$(document).off('click', '#Clear').on('click', '#Clear',function(e) {
		$('.C_radio_pr:checked').prop('checked',false);
		$('#page_pr_item_list').empty();
		ClassDt.Dt_selection = [];
		ClassDt.Dt_ChooseSelectPR = [];
		ShowClassDt_selection();
	})

	$(document).off('click', '.Detail').on('click', '.Detail',function(e) {
		var data = $(this).attr('data');
		var arr = data.split('@@');
		var html = '';
			html ='<div class = "row">'+
					'<div class = "col-md-12">'+
						'<table id="example" class="table table-bordered display select" cellspacing="0" width="100%">'+
               '<thead>'+
                  '<tr>'+
                     '<th>Item</th>'+
                     '<th>Desc</th>'+
                     '<th>Estimate Value</th>'+
                     '<th>Photo</th>'+
                     '<th>DetailCatalog</th>'+
                  '</tr>'+
               '</thead>'+
               '<tbody><tr>';
               		for (var i = 0; i < arr.length; i++) {
               			var v = (i == 2) ? formatRupiah(arr[i]) : arr[i];
               			html += '<td>'+v+'</td>';
               		}
               		html += '</tr></tbody>';
         html += '</table></div></div>';
		$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Catalog'+'</h4>');
		$('#GlobalModalLarge .modal-body').html(html);
		$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>');
		$('#GlobalModalLarge').modal({
		    'show' : true,
		    'backdrop' : 'static'
		});
	})

	$(document).off('change', '#ChooseTotVendor').on('change', '#ChooseTotVendor',function(e) {
		var v = $('#ChooseTotVendor option:selected').val();
		MakeDom_page_PageSearchVendor(v);
	})

	function MakeDom_page_PageSearchVendor(Tot_vendor)
	{
		var html ='<div class = "row">'+
					'<div class ="col-md-12">'+'<label style= "color : red">Select Vendor</label>'+
						'<table class = "table table-bordered" id = "Tbl_selectVendor">'+
							'<thead>'+
								'<tr>'+
									'<th style = "text-align: center;background: #7da962;color: #FFFFFF;">No</th>'+
									'<th style = "text-align: center;background: #7da962;color: #FFFFFF;width:327px">Select Vendor</th>'+
									'<th style = "text-align: center;background: #7da962;color: #FFFFFF;">Detail</th>'+
									'<th style = "text-align: center;background: #7da962;color: #FFFFFF;">File Offer</th>'+
									'<th style = "text-align: center;background: #7da962;color: #FFFFFF;">Approve</th>'+
								'<tr>'+
							'</thead>'+
							'<tbody></tbody>'+
						'</table>'+
					'</div>'+
				 '</div>';
		
		if (!$('#Tbl_selectVendor').length) {
			$('#PageSearchVendor').html(html);	
		}
			 					

		var rowCount = $('#Tbl_selectVendor tbody tr').length;
		if (Tot_vendor < rowCount) {
			var v = rowCount - Tot_vendor;
			for (var i = 0; i < v; i++) {
				$('#Tbl_selectVendor tbody tr:last').remove();
			}
		}
		else if(Tot_vendor > rowCount){
			var v = Tot_vendor - rowCount;
			// console.log(Tot_vendor);
			// console.log(rowCount);
			var htmlWr = function(No){
				var html = '';
					html = '<tr>'+
								'<td style = "text-align:center;">'+No+'</td>'+
								'<td>'+'<div align = "center"><button class="btn btn-default SearchVendor" type="button"><i class="fa fa-search" aria-hidden="true"></i></button></div>'+'<div style = "margin-top : 5px;" class = "LblNmVendor"></div></td>'+
								'<td style = "text-align:center;"><button class="btn btn-primary Detail_Vendor" data="">Detail</button></td>'+
								'<td><div align = "center"><input type="file" data-style="fileinput" class="BrowseFile" multiple="" accept="image/*,application/pdf"></div></td>'+
								// '<td><div align = "center"><select class="form-control" class ="OpApprove_vendor" style = "width : 100px;">'+
								// 		'<option value = "0" selected>No</option>'+
								// 		'<option value = "1">Yes</option>'+
								// 	 '</select></div>'+
								// '</td>'+	
								'<td><div align = "center"><label><input type="radio" name="optradio'+No+'" class="C_radio_approve" value = "0" checked> No</label> &nbsp <label><input type="radio" name="optradio'+No+'" class="C_radio_approve" value = "1"> Yes</label></div>'+
								'</td>'+ 	
							'</tr>';	
				return html;
			}

			var NOGet = $('#Tbl_selectVendor tbody tr:last').find('td:eq(0)').html();
			if (NOGet != '' && NOGet != null && NOGet != undefined) {
				NO = parseInt(NOGet) + 1;
			}
			else
			{
				var NO = 1;
			}
			for (var i = 0; i < v; i++) {
				$('#Tbl_selectVendor tbody').append(htmlWr(NO));
				NO++;
			}

		}

	}

	$(document).off('click', '.C_radio_approve').on('click', '.C_radio_approve',function(e) {
		var v = $(this).val();
		if (v == 1) {
			var htmlDivNote = '<div class = "DivNote">'+
									'<label>Reason</label>'+
									'<textarea class = "form-control Notes"></textarea>'+
							  '</div>';	
			//$('.C_radio_approve[value="1"]').prop('checked',false);
			$('.C_radio_approve[value="0"]').prop('checked',true);
			$(this).prop('checked',true);
			var ev = $(this).closest('td');
			if (ev.find('.DivNote').length) {
				ev.find('.DivNote').remove();
			}

			$('.C_radio_approve').each(function(){
				var td = $(this).closest('td');
				td.find('.DivNote').remove();
			})
			ev.append(htmlDivNote);

		}
		else
		{
			var td = $(this).closest('td');
			td.find('.DivNote').remove();
		}
	})

	$(document).off('click', '.SearchVendor').on('click', '.SearchVendor',function(e) {
		var SelectorFirst = $(this);
		var html = '';
			html = '<div class="row">'+
						'<div class = "col-md-12">'+
							'<div class="thumbnail" style="padding: 10px;">'+
			           			'<b>Status : </b><i class="fa fa-circle" style="color: #eade8e;"></i> Already Selected'+
			                '</div><br>'+
							'<div class="table-responsive">'+
								'<table class="table table-bordered tableData" id ="datatablesServer">'+
			        				'<thead>'+
			        					'<tr>'+
			        						'<th width = "2%" style = "text-align: center;background: #EE556C;color: #FFFFFF;">No</th>'+
			        						'<th style = "text-align: center;background: #EE556C;color: #FFFFFF;">Category Supplier</th>'+
			        						'<th style = "text-align: center;background: #EE556C;color: #FFFFFF;width: 250px;">Supplier</th>'+
			        						'<th style = "text-align: center;background: #EE556C;color: #FFFFFF;">Detail Item</th>'+
			        					'</tr>'+
			        				'</thead>'+
			        				'<tbody>'+
			        				'</tbody>'+
			        			'</table>'+
							'<div>'+
						'</div>'+
					'</div>';						

			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Vendor'+'</h4>');
			$('#GlobalModalLarge .modal-body').html(html);
			$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>');
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});

			// load data
				$("#datatablesServer tbody").empty();
				$.fn.dataTable.ext.errMode = 'throw';
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

				var dataTable = $('#datatablesServer').DataTable( {
				    "processing": true,
				    "destroy": true,
				    "serverSide": true,
				    "iDisplayLength" : 10,
				    "ordering" : false,
				    "ajax":{
				        url : base_url_js+"purchasing/page/supplier/DataIntable/server_side", // json datasource
				        ordering : false,
				        type: "post",  // method  , by default get
				        data : {action : "All_approval"},
				        error: function(){  // error handling
				            $(".employee-grid-error").html("");
				            $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				            $("#employee-grid_processing").css("display","none");
				        },
				    },
				    'createdRow': function( row, data, dataIndex ) {
				    	// cek data ada pada LblNmVendor
				    	var bool = true;
				    	$(".LblNmVendor").each(function(){
				    		var idtable = $(this).attr('idtable');
				    		if (bool) {
				    			if (data[1] == idtable) {
				    				bool = false;
				    			}
				    		}
				    	})

				    	if (bool) {
				    		$(row).find('td:eq(1)').html('<div align="center"><b>'+data[4]+'</b></div>');
				    		$(row).find('td:eq(3)').html('<div align="left">'+data[6]+'</div>');
				    		$(row).attr('idtable',data[1]);
				    	}
				    	else
				    	{
				    		$(row).prop('disabled',true);
				    		$(row).attr('style','background-color: #eade8e;');
				    		$(row).find('td:eq(1)').html('<div align="center"><b>'+data[4]+'</b></div>');
				    		$(row).find('td:eq(3)').html('<div align="left">'+data[6]+'</div>');
				    		$(row).attr('idtable',data[1]);
				    	}

				    },
				    
				} );

				dataTable.on( 'click', 'tr', function (e) {
				       var row = $(this);
				       var idtable = row.attr('idtable');
				       var SuplierNM = row.find('td:eq(2)').html();
				       // get selector LblNmVendor
				       var tdParent = SelectorFirst.closest('td');
				       tdParent.find('.LblNmVendor').html(SuplierNM);
				       tdParent.find('.LblNmVendor').attr('idtable',idtable);
				       var arr = row.find('td:eq(0)').html()+'@@'+row.find('td:eq(1)').html()+'@@'+row.find('td:eq(2)').html()+'@@'+row.find('td:eq(3)').html();
				       var trParent = SelectorFirst.closest('tr');
				       trParent.find('.Detail_Vendor').attr('data',arr);
				       $('#GlobalModalLarge').modal('hide');
				} );		

	})


	// Detail_Vendor
	$(document).off('click', '.Detail_Vendor').on('click', '.Detail_Vendor',function(e) {
		var data = $(this).attr('data');
		if (data != '' && data != undefined && data != null) {
			var html = '';
			var arr = data.split('@@');
			var	isian = '<tr style = "background-color : #eade8e;">';
				for (var i = 0; i < arr.length; i++) {
					isian += '<td>'+arr[i]+'</td>';
				}

				isian += '</tr>';

				html = '<div class="row">'+
							'<div class = "col-md-12">'+
								'<div class="thumbnail" style="padding: 10px;">'+
				           			'<b>Status : </b><i class="fa fa-circle" style="color: #eade8e;"></i> Already Selected'+
				                '</div><br>'+
								'<div class="table-responsive">'+
									'<table class="table table-bordered tableData" id ="datatablesServer">'+
				        				'<thead>'+
				        					'<tr>'+
				        						'<th width = "2%" style = "text-align: center;background: #EE556C;color: #FFFFFF;">No</th>'+
				        						'<th style = "text-align: center;background: #EE556C;color: #FFFFFF;">Category Supplier</th>'+
				        						'<th style = "text-align: center;background: #EE556C;color: #FFFFFF;width: 250px;">Supplier</th>'+
				        						'<th style = "text-align: center;background: #EE556C;color: #FFFFFF;">Detail Item</th>'+
				        					'</tr>'+
				        				'</thead>'+
				        				'<tbody>'+
				        					isian+
				        				'</tbody>'+
				        			'</table>'+
								'<div>'+
							'</div>'+
						'</div>';						

				$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Vendor'+'</h4>');
				$('#GlobalModalLarge .modal-body').html(html);
				$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>');
				$('#GlobalModalLarge').modal({
				    'show' : true,
				    'backdrop' : 'static'
				});
		}		
	})

	$(document).off('click', '#OpenPO').on('click', '#OpenPO',function(e) {
		if (confirm('Are you sure ?')) {
			// check pr selected harus lebih dari 0
				var arr_pr_detail_selected = [];
				$('.id_pr_detail_selected:checked').each(function(){
					var id_pr_detail = $(this).attr('id_pr_detail');
					arr_pr_detail_selected.push(id_pr_detail);
				})

			// lock open po process hanya satu PR
			var LockOnePR = __LockOnePR(arr_pr_detail_selected);	
			

			// Select vendor harus ada yang approve = 1
				var count_vendor_ok = $('.C_radio_approve[value="1"]:checked').length;

			// check Choose vendor dengan select vendor
				var ChooseTotVendor = $('#ChooseTotVendor option:selected').val();
				var c = 0;
				$('.LblNmVendor').each(function(){
					var idtable = $(this).attr('idtable');
					if (idtable != '' && idtable != null && idtable != undefined) {
						c++;
					}
				})

				// validation file
					var BoolFile = true;
				$(".BrowseFile").each(function(){
					var ev = $(this);
					var tr = ev.closest('tr');
					var td = ev.closest('td');
					// cek apakah ada file exist
					var bool22 = true;
					if (td.find('li').length) {
						bool22 = false;
					}

					if (BoolFile && bool22) {
						BoolFile = file_validation(ev);
					}
					
				})

				// validation Notes
					var __vnotes = true;
					if ($('.Notes').length) {
						if ($('.Notes').val() == '') {
							__vnotes = false;
						}
					}
					else
					{
						__vnotes = false;
					}


				if (arr_pr_detail_selected.length > 0 && count_vendor_ok > 0 && ChooseTotVendor == c && BoolFile && LockOnePR && __vnotes) {
					loading_button('#OpenPO');
					var action_submit = 'PO';
					var id_selector = '#OpenPO';
					_Create_PO_SPK(action_submit,id_selector).then(function(data){
						if (data.status == 1) {
							window.location.href = base_url_js+data['url'];
						}
						else
						{
							toastr.info(data.message);
						}
					    
					})
				}
				else
				{
					toastr.info('<li>PR Selected must be having less one checked & 1 PR in 1 PO</li>'+
								'<li>Select Vendor must be having less one approve</li>'+
								'<li>Total Vendor must be same with total selected vendor</li>'+
								'<li>Reason is required</li>'
								);
				}
		}
		else
		{

		}

		// dapatkan no po dan show page PO created
	})

	$(document).off('click', '#OpenSPK').on('click', '#OpenSPK',function(e) {
		if (confirm('Are you sure ?')) {
			// check pr selected harus lebih dari 0
				var arr_pr_detail_selected = [];
				$('.id_pr_detail_selected:checked').each(function(){
					var id_pr_detail = $(this).attr('id_pr_detail');
					arr_pr_detail_selected.push(id_pr_detail);
				})

			// lock open po process hanya satu PR
			var LockOnePR = __LockOnePR(arr_pr_detail_selected);	

			// Select vendor harus ada yang approve = 1
				var count_vendor_ok = $('.C_radio_approve[value="1"]:checked').length;

			// check Choose vendor dengan select vendor
				var ChooseTotVendor = $('#ChooseTotVendor option:selected').val();
				var c = 0;
				$('.LblNmVendor').each(function(){
					var idtable = $(this).attr('idtable');
					if (idtable != '' && idtable != null && idtable != undefined) {
						c++;
					}
				})

				// validation file
					var BoolFile = true;
				$(".BrowseFile").each(function(){
					var ev = $(this);
					var tr = ev.closest('tr');
					var td = ev.closest('td');
					// cek apakah ada file exist
					var bool22 = true;
					if (td.find('li').length) {
						bool22 = false;
					}

					if (BoolFile && bool22) {
						BoolFile = file_validation(ev);
					}
					
				})

				// validation Notes
					var __vnotes = true;
					if ($('.Notes').length) {
						if ($('.Notes').val() == '') {
							__vnotes = false;
						}
					}
					else
					{
						__vnotes = false;
					}


				if (arr_pr_detail_selected.length > 0 && count_vendor_ok > 0 && ChooseTotVendor == c && BoolFile && LockOnePR && __vnotes) {
					loading_button('#OpenSPK');
					var action_submit = 'SPK';
					var id_selector = '#OpenSPK';
					_Create_PO_SPK(action_submit,id_selector).then(function(data){
						if (data.status == 1) {
							window.location.href = base_url_js+data['url'];
						}
						else
						{
							toastr.info(data.message);
						}
					    
					})
				}
				else
				{
					toastr.info('<li>PR Selected must be having less one checked</li>'+
								'<li>Select Vendor must be having less one approve</li>'+
								'<li>Total Vendor must be same with total selected vendor</li>'+
								'<li>Reason is required</li>'
								);
				}
		}
		else
		{

		}

		// dapatkan no po dan show page PO created
	})

	function _Create_PO_SPK(action_submit,id_selector)
	{
		var def = jQuery.Deferred();

		var nmbtn = '';
		if (id_selector == '#OpenPO') {
			nmbtn = 'Open PO';
		}
		else if(id_selector == '#OpenSPK')
		{
			nmbtn = 'Open SPK';
		}
		
		var arr_pr_detail_selected = [];
			$('.id_pr_detail_selected:checked').each(function(){
				var id_pr_detail = $(this).attr('id_pr_detail');
				arr_pr_detail_selected.push(id_pr_detail);
			})

		var arr_supplier = [];
		var form_data = new FormData();
		var PassNumber = 0; 
			$(".LblNmVendor").each(function(){
				var idtable = $(this).attr('idtable');
				var fillItem = $(this).closest('tr');
				if (idtable != '' && idtable != null && idtable != undefined) {
					if ( fillItem.find('.BrowseFile').length ) {
						var UploadFile = fillItem.find('.BrowseFile')[0].files;
						for(var count = 0; count<UploadFile.length; count++)
						{
						 form_data.append("UploadFile"+PassNumber+"[]", UploadFile[count]);
						}
					}

					// approve
						var approve = fillItem.find('.C_radio_approve:checked').val();
						var Desc = '';
						if (fillItem.find('.Notes').length) {
							Desc = $('.Notes').val();
						}

					var temp = {
						CodeSupplier : idtable,
						Approve : approve,
						Desc : Desc,
					}
					arr_supplier.push(temp);
				}

				PassNumber++;
			})

		var token = jwt_encode(arr_pr_detail_selected,"UAP)(*");
		form_data.append('arr_pr_detail',token);

		var token = jwt_encode(arr_supplier,"UAP)(*");
		form_data.append('arr_supplier',token);

		var token = jwt_encode(ClassDt.action_mode,"UAP)(*");
		form_data.append('action_mode',token);

		var token = jwt_encode(action_submit,"UAP)(*");
		form_data.append('action_submit',token);

		var token = jwt_encode(ClassDt.POCode,"UAP)(*");
		form_data.append('Code',token);
		

		var url = base_url_js + "po_spk/submit_create"
		$.ajax({
		  type:"POST",
		  url:url,
		  data: form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		  contentType: false,       // The content type used when sending data to the server.
		  cache: false,             // To unable request pages to be cached
		  processData:false,
		  dataType: "json",
		  success:function(data)
		  {
		    def.resolve(data)
		    $(id_selector).prop('disabled',false).html(nmbtn);
		  },
		  error: function (data) {
		  	$(id_selector).prop('disabled',false).html(nmbtn);
		    def.reject();
		  }
		})	

		return def.promise();
	}

	function file_validation(ev)
	{
		var error = '';
		var msgStr = '';
	    var files = ev[0].files;
	    if (files.length > 0) {
    	   	var name = files[0].name;
    	   	var extension = name.split('.').pop().toLowerCase();
    	   	if(jQuery.inArray(extension, ['jpg' ,'png','jpeg','pdf','doc','docx']) == -1)
    		{
    		    msgStr += 'Invalid Type File<br>';
    		}

    	   	var oFReader = new FileReader();
    	   	oFReader.readAsDataURL(files[0]);
    	   	var f = files[0];
    	   	var fsize = f.size||f.fileSize;

    	   	if(fsize > 2000000) // 2mb
    		{
    		    msgStr += 'Image File Size is very big<br>';
    		}
	    }
	    else
	    {
	    	msgStr += 'File Offer required<br>';
	    }
	   	

	   if (msgStr != '') {
	     toastr.error(msgStr, 'Failed!!');
	     return false;
	   }
	   else
	   {
	     return true;
	   }
	}	


	function __LockOnePR(arr_pr_detail_selected)
	{
		var Dt_ChooseSelectPR = ClassDt.Dt_ChooseSelectPR;
		var arr_pr = [];
		var bool = false;
		for (var z = 0; z < arr_pr_detail_selected.length; z++) {
			var Item = arr_pr_detail_selected[z];
			// search number PR in Dt_ChooseSelectPR
			for (var i = 0; i < Dt_ChooseSelectPR.length; i++) {
				var pr_detail = Dt_ChooseSelectPR[i].pr_detail;
				for (var j = 0; j < pr_detail.length; j++) {
					if (Item == pr_detail[j].ID) {
						var PRCode =pr_detail[j].PRCode;
						var bool2 = true;
						for (var x = 0; x < arr_pr.length; x++) {
							if (arr_pr[x]==PRCode) {
								bool2 = false;
								break;
							}
						}

						if (bool2) {
							arr_pr.push(PRCode);
						}
					}
				}
			}
		}

		// console.log(arr_pr)
		if (arr_pr.length == 1) {
			bool = true;
			// console.log('__LockOnePR')
		}

		return bool
	}
</script>