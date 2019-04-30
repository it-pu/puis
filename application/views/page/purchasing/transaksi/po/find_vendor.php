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
					 			'</tr>'+
					 		'<thead>'+
					 		'<tbody id="dataRow"></tbody>'+
		        		'</table>'+
		        		'</div>'+
		        	 '</div>'+
		        	'</div>';

		    return html;    	 				
		},	
	};

	$(document).ready(function() {
	    $('#page_pr_list').html(ClassDt.htmlPage_pr_list);
	    skip_error_dt_table();
		    Get_data_pr().then(function(data){
		        loadingEnd(500);
		        $('.C_radio_pr:first').prop('checked',true);
		        $('.C_radio_pr:first').trigger('change');
		        
		    })
	}); // exit document Function

	function Get_data_pr(){
       var def = jQuery.Deferred();
       var data = {
           PurchasingStatus : '!=2',
           auth : 's3Cr3T-G4N',
       };
       var token = jwt_encode(data,"UAP)(*");
       	var table = $('#tableData_pr').DataTable({
       		"fixedHeader": true,
       	    "processing": true,
       	    "destroy": true,
       	    "serverSide": true,
       	    "iDisplayLength" : 10,
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
       	    ],
       	    'createdRow': function( row, data, dataIndex ) {
       	    		$(row).find('td:eq(1)').html('<label><input type="radio" name="optradio" prcode = "'+data['PRCode']+'" class = "C_radio_pr">&nbsp'+data['PRCode']+'</label>');
       	    		$( row ).find('td:eq(2)').attr('align','center');
       	    		$( row ).find('td:eq(0)').attr('align','center');
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
				var EstimaValue = pr_detail[i]['UnitCost'];
				var arr_Photo = pr_detail[i]['Photo'];
				arr_Photo = arr_Photo.split(',');
				htmlPhoto = '<ul>';
				for (var j = 0; j < arr_Photo.length; j++) {
					htmlPhoto += '<li><a href = "'+base_url_js+'fileGetAny/budgeting-catalog-'+arr_Photo[j]+'" target="_blank">'+
										arr_Photo[j]+'</a></li>';
				}
				htmlPhoto += '</ul>';
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
						  '</tr>';	

		}

		var  htmlInputPR= '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "Page_PR">'+
							'<div style="padding: 5px;">'+
								'<h3 class="header-blue">'+pr_detail[0]['PRCode']+'</h3>'+
							'</div>'+
							'<div class = "col-md-12">'+
								'<div class="table-responsive">'+
									'<table class="table table-bordered tableData" id ="table_data_pr_detail">'+
										'<thead>'+
										'<tr>'+
											'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Catalog</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Detail</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Spec+</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Desc</th>'+
				                            '<th width = "4%" style = "text-align: center;background: #20485A;color: #FFFFFF;width : 78px;">Qty</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Cost</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 78px;">PPH(%)</th>'+
				                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Sub Total</th>'+
				                            '<th width = "150px" style = "text-align: center;background: #20485A;color: #FFFFFF;">Date Needed</th>'+
										'</tr>'+
										'</thead>'+
										'<tbody>'+IsiInputPR+'</tbody></table>'+
									'</div>'+
								'</div>'+
							'</div>';

		$('#page_pr_item_list').html(htmlInputPR);
		SelectedPR_selection(dt)							
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
							var EstimaValue = pr_detail[k]['UnitCost'];
							var arr_Photo = pr_detail[k]['Photo'];
							arr_Photo = arr_Photo.split(',');
							htmlPhoto = '<ul>';
							for (var l = 0; l < arr_Photo.length; l++) {
								htmlPhoto += '<li><a href = "'+base_url_js+'fileGetAny/budgeting-catalog-'+arr_Photo[l]+'" target="_blank">'+
													arr_Photo[l]+'</a></li>';
							}
							htmlPhoto += '</ul>';
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
									'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">PR Code</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Catalog</th>'+
		                            '<th width = "4%" style = "text-align: center;background: #20485A;color: #FFFFFF;">Detail</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Spec+</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Desc</th>'+
		                            '<th width = "4%" style = "text-align: center;background: #20485A;color: #FFFFFF;width : 78px;">Qty</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Cost</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 78px;">PPH(%)</th>'+
		                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Sub Total</th>'+
		                            '<th width = "150px" style = "text-align: center;background: #20485A;color: #FFFFFF;">Date Needed</th>'+
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
				                            '<label>Choose Total Vendor</label>'+
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
				    '</div>'                 
		        	;
		$('#page_pr_selected_list').html(html);
		$('#ChooseTotVendor').trigger('change');        	
	}

	$(document).off('change', '#ChooseTotVendor').on('change', '#ChooseTotVendor',function(e) {
		
	})
</script>