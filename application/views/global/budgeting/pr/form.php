<div class="row" id = "dataselected">
	
</div>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-8 col-md-offset-2">
			<div class="thumbnail">
				<div class="row" style="margin-top: 10px">
					<div class="col-md-3 col-md-offset-1">
						<div class="well">
							<div class="form-group">
								<label class="control-label">Year</label>
								<select class = "select2-select-00 full-width-fix" id = "Year">

								</select>
							</div>
							<div class="form-group">
								<label class="control-label">Department</label>
								<select class = "select2-select-00 full-width-fix" id = "DepartementPost">

								</select>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-md-offset-1">
						<div class="well">
							<div style="margin-top: -15px">
								<label>Budget Remaining</label>
							</div>
							<div id = "Page_Budget_Remaining">
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- <pre> -->
	<?php 
	//print_r($this->session->all_userdata());
	 ?>
<!-- </pre>  -->
<div id ="Page_Input_PR" style="margin-top: 10px">
	
</div>
<script type="text/javascript">
	var arr_Year = <?php echo json_encode($arr_Year) ?>;
	var PRCodeVal = "<?php echo $PRCodeVal ?>"; // if filled for edit
	var BudgetMax = 0;
	var BudgetRemaining = [];
	var PostBudgetDepartment = [];
	var ResponseAjaxEdit = [];
	var No = 1;
	var UserAccess = '';
	var RuleAccess = [];
	var MaxLimit =0;
	var DepartementArr = [];
	var PostBudgetDepartmentCombine = [];
	var tbl_element = '';
	// document.addEventListener('contextmenu', function(e) {
	//   e.preventDefault();
	// });
	$(document).ready(function() {
		LoadFirstLoad();

		function LoadFirstLoad()
		{
			// check Rule for Input
				var url = base_url_js+"budgeting/checkruleinput";
				var data = {
					NIP : "<?php echo $this->session->userdata('NIP') ?>",
				};
				<?php if (isset($Departement)): ?>
					data = {
						NIP : "<?php echo $this->session->userdata('NIP') ?>",
						Departement : "<?php echo $Departement ?>",
						PRCodeVal : PRCodeVal,
					};
				<?php endif ?>
				var token = jwt_encode(data,"UAP)(*");
				$.post(url,{ token:token },function (resultJson) {
					var response = jQuery.parseJSON(resultJson);
					console.log(response);
					var access = response['access'];
					if (access.length > 0) {
						UserAccess = access[0]['ID_m_userrole'];
						RuleAccess = response['rule'];
						loadYear();
						getAllDepartementPU();
						loadShowBUdgetRemaining(BudgetRemaining);
					}
					else
					{
						$("#pageContent").empty();
						$("#pageContent").html('<h2 align = "center">Your not authorize these modul</h2>');
					}
					
				})

		}

		function loadYear()
		{
			$("#Year").empty();
			var OPYear = '';
			OPYear = '';
			for (var i = 0; i < arr_Year.length; i++) {
				var selected = (arr_Year[i].Year == "<?php echo $Year ?>") ? 'selected' : '';
				OPYear += '<option value ="'+arr_Year[i].Year+'" '+selected+'>'+arr_Year[i].Year+' - '+(parseInt(arr_Year[i].Year) + 1)+'</option>';
			}
			$("#Year").append(OPYear);
			$( "#Year" ).prop( "disabled", true );
			$('#Year').select2({
			   //allowClear: true
			});
			
		}

		function getAllDepartementPU()
		{
			<?php if (isset($Departement)): ?>
			var Div = "<?php echo $Departement ?>";
			<?php else: ?>
			var Div = "<?php echo $this->session->userdata('IDDepartementPUBudget') ?>";
			<?php endif ?>
		  var url = base_url_js+"api/__getAllDepartementPU";
		  $('#DepartementPost').empty();
		  $.post(url,function (data_json) {
		  	DepartementArr = data_json;
		    for (var i = 0; i < data_json.length; i++) {
		        var selected = (data_json[i]['Code']==Div) ? 'selected' : '';
		        $('#DepartementPost').append('<option value="'+ data_json[i]['Code']  +'" '+selected+'>'+data_json[i]['Name2']+'</option>');
		    }
		   	$( "#DepartementPost" ).prop( "disabled", true );
		    $('#DepartementPost').select2({
		       //allowClear: true
		    });
		    Load_input_PR();
		  })
		}

		function Load_input_PR()
		{
			var Year = $("#Year").val();
			var Departement = $("#DepartementPost").val();
			var url = base_url_js+"budgeting/detail_budgeting_remaining";
			var data = {
					    Year : Year,
						Departement : Departement,
					};
			var token = jwt_encode(data,'UAP)(*');
			$.post(url,{token:token},function (resultJson) {
				var response = jQuery.parseJSON(resultJson);
				PostBudgetDepartment = response.data;
				// Simpan di localstorage
					localStorage.setItem("PostBudgetDepartment", JSON.stringify(PostBudgetDepartment));
				// check if edit
					if (PRCodeVal != '') {
						loading_page("#Page_Input_PR");
						var PRCode = PRCodeVal;
						var url = base_url_js+'budgeting/GetDataPR';
						var data = {
						    PRCode : PRCode,
						};
						var token = jwt_encode(data,"UAP)(*");
						$.post(url,{ token:token },function (data_json) {
							var response = jQuery.parseJSON(data_json);
							// Concat PostBudgetDepartment if Cross
								var Div = "<?php echo $this->session->userdata('IDDepartementPUBudget') ?>";
								var pr_detail = response['pr_detail'];
								var CrossArr = [];
								var urlInarray = [];
								for (var i = 0; i < pr_detail.length; i++) {
									if (pr_detail[i]['Departement'] != Div) {
										CrossArr.push(pr_detail[i]['Departement']);
										urlInarray.push(base_url_js+'budgeting/detail_budgeting_remaining');
										var DepartementCross= pr_detail[i]['Departement'];
										// get ajax post modal
											var url = base_url_js+"budgeting/detail_budgeting_remaining";
											var data = {
													    Year : Year,
														Departement : DepartementCross,
													};
											var token = jwt_encode(data,'UAP)(*');
											$.post(url,{token:token},function (resultJson) {
												var response = jQuery.parseJSON(resultJson);
												var PostBudgetDepartmentModal = response.data;
												// PostBudgetDepartment
												if (PostBudgetDepartmentModal.length > 0) {
													// PostBudgetDepartment = PostBudgetDepartment.concat(PostBudgetDepartmentModal);
													var P_result = Filtering_PostBudgetDepartment(PostBudgetDepartment,PostBudgetDepartmentModal);
													PostBudgetDepartment = P_result; 
												}

											}).fail(function() {
											  toastr.info('No Result Data'); 
											}).always(function() {
											                
											});	
									} // end if
								}

								// ResponseAjaxEdit = response;
								// htmlPRDetail(response);

								if (CrossArr.length > 0) {
									var bool = 0;
									for (var i = 0; i < CrossArr.length; i++) {
										$( document ).ajaxSuccess(function( event, xhr, settings ) {
										   if (jQuery.inArray( settings.url, urlInarray )) {
										       bool++;
										       if (bool == CrossArr.length) {
										           setTimeout(function(){ 
										           	ResponseAjaxEdit = response;
										           	htmlPRDetail(response);
										           }, 500);
										          
										       }
										   }
										});
									} // end for
									
								}
								else
								{
									ResponseAjaxEdit = response;
									htmlPRDetail(response);
								}
								
							
						}); 
					}
					else
					{
						htmlPRDetail('');
					}

			}).fail(function() {
			  toastr.info('No Result Data'); 
			}).always(function() {
			                
			});

		}

		function htmlPRDetail(response)
		{
			var html = '<div class = "row" style = "margin-left : 0px">'+
							'<div class = "col-md-3">'+
								'<button type="button" class="btn btn-default btn-add-pr"> <i class="icon-plus"></i> Add</button>'+
							'</div>'+
						'</div>'+
						'<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+		
							'<div class = "col-md-12">'+
								'<div class = "table-responsive">'+
									'<table class="table table-bordered tableData" id ="table_input_pr">'+
									'<thead>'+
									'<tr>'+
										'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
										'<th width = "100px" style = "text-align: center;background: #20485A;color: #FFFFFF;">Budget Status</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Select Post Budget Item</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Item</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Desc</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Spesification Additional</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Need</th>'+
			                            '<th width = "4%" style = "text-align: center;background: #20485A;color: #FFFFFF;">Qty</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Unit Cost</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Sub Total</th>'+
			                            '<th width = "150px" style = "text-align: center;background: #20485A;color: #FFFFFF;">Date Needed</th>'+
			                            // '<th width = "100px" style = "text-align: center;background: #20485A;color: #FFFFFF;">Budget Status</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Upload Files</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Combine Budget</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Action</th>'+
									'</tr></thead>'+
									'<tbody></tbody></table></div></div></div>';
			var SaveBtn = '<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
								'<div class = "col-md-12">'+
									'<div class = "pull-right">'+
										'<button class = "btn btn-success" id = "SaveBudget" action = "0">Save to Draft</button>'+
									'</div>'+
								'</div>'+
							'</div>';
			
			if (response != '') { // for edit
				var pr_create = response['pr_create'];
				var Status = pr_create[0]['Status'];
				if (Status != 3) { // Event sebelum approval all
	              // if finance, add approver by kabag finance
	              <?php if ($this->session->userdata('IDdepartementNavigation') == 9): ?>
	              		<?php $PositionMain = $this->session->userdata('PositionMain');$Position=$PositionMain['IDPosition']; ?>
	              		<?php if ($Position==11): ?>
	              			var  parent_th_approver = 'Approver &nbsp'+
								    				'<a href = "javascript:void(0)"  class="btn btn-default btn-default-success" type="button" id = "add_approver" prcode = "'+PRCodeVal+'">'+
						                        			'<i class="fa fa-plus-circle" aria-hidden="true"></i>'+
						                    		'</a>';
	              		$("#parent_th_approver").html(parent_th_approver);
	              		<?php endif ?>
	              <?php endif ?>	
				}
				switch (Status)
				{
				   case "0":
				   		var SaveBtn = '<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
				   					'<div class = "col-md-12">'+
				   						'<div class = "pull-right">'+
				   							'<button class="btn btn-success" id="SaveBudget" action="0" PRCode = "'+PRCodeVal+'">Save to Draft</button>'+ '&nbsp&nbsp'+'<button class="btn btn-primary" id="BtnIssued" action="1" PRCode = "'+PRCodeVal+'">Issued</button>'+
				   						'</div>'+
				   					'</div>'+
				   				'</div>';
				   		break;
				   	case "3":
				   			var SaveBtn = '<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
				   						'<div class = "col-md-12">'+
				   							'<div class = "pull-right">'+
				   								'<button class="btn btn-success" id="SaveBudget" action="0" PRCode = "'+PRCodeVal+'">Save to Draft</button>'+ '&nbsp&nbsp'+'<button class="btn btn-default" id="pdfprint" PRCode = "'+PRCodeVal+'"> <i class = "fa fa-file-pdf-o"></i> Print PDF</button>'+
				   							'</div>'+
				   						'</div>'+
				   					'</div>';
				   			break;	
				   default:
				   		var SaveBtn = '<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
				   					'<div class = "col-md-12">'+
				   						'<div class = "pull-right">'+
				   							'<button class="btn btn-default" id="pdfprint" PRCode = "'+PRCodeVal+'"> <i class = "fa fa-file-pdf-o"></i> Print PDF</button>'+ '&nbsp&nbsp'+'<!--<button class="btn btn-default" id="excelprint" PRCode = "'+PRCodeVal+'"><i class = "fa fa-file-excel-o"></i> Print Excel</button>-->'+
				   						'</div>'+
				   					'</div>'+
				   				'</div>';
				}
			}										
			
			var InputTax = 	'<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
								'<div class = "col-sm-2">'+
									'<div class = "form-group">'+
										'<label>PPN</label>'+
										'<input type = "text" class = "form-control" id = "ppn"><b>%</b>'+
									'</div>'+
								'</div>'+
							'</div>';
			var Notes = 	'<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
								'<div class = "col-md-6">'+
									'<div class = "form-group">'+
										'<label>Note</label>'+
										'<textarea id= "Notes" class = "form-control" rows = "4"></textarea>'+
									'</div>'+
								'</div>'+
								'<div class = "col-md-6">'+
									'<h3 id = "phtmltotal" align = "right"> Total : '+formatRupiah(0)+'</h3>'+
								'</div>'+
							'</div>';

			var Supporting_documents = '<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
								'<div class = "col-md-6">'+
									'<div class = "form-group">'+
										'<label>Supporting documents</label>'+
										'<input type="file" data-style="fileinput" class="BrowseFileSD" id="BrowseFileSD" multiple="" accept="image/*,application/pdf">'+
									'</div>'+
								'</div>'+
							'</div>';			

			$("#Page_Input_PR").html(InputTax+html+Notes+Supporting_documents+SaveBtn);
			$("#ppn").maskMoney({thousands:'', decimal:'', precision:0,allowZero: true});
			$("#ppn").maskMoney('mask', '9894');
			AddingTable();

			if (response != '') { // for edit
				var url = base_url_js+'rest/Catalog/__Get_Item';
				var data = {
					action : 'choices',
					auth : 's3Cr3T-G4N',
					department : $("#DepartementPost").val(),
				};
			    var token = jwt_encode(data,"UAP)(*");
			    $.post(url,{ token:token },function (ResponseCatalog) {
    				var pr_create = response['pr_create'];
    				var PPN = pr_create[0]['PPN'];
    				var n = PPN.indexOf(".");
    				PPN = PPN.substring(0, n);
    				$("#ppn").val(PPN);
    				$("#ppn").maskMoney({thousands:'', decimal:'', precision:0,allowZero: true});
    				$("#ppn").maskMoney('mask', '9894');

    				var Notes = pr_create[0]['Notes'];
    				$("#Notes").val(Notes);

    				// Show Supporting_documents if exist
    					var Supporting_documents = jQuery.parseJSON(pr_create[0]['Supporting_documents']);
    					// console.log(Supporting_documents);
    					var htmlSupporting_documents = '';
    					if (Supporting_documents != null) {
    						if (Supporting_documents.length > 0) {
    							for (var i = 0; i < Supporting_documents.length; i++) {
    								htmlSupporting_documents += '<li><a href = "'+base_url_js+'fileGetAny/budgeting-pr-'+Supporting_documents[i]+'" target="_blank" class = "Fileexist">File '+(i+1)+'</a></li>';
    							}
    						}
    					}

    					$('#BrowseFileSD').closest('.col-md-6').append(htmlSupporting_documents);


    				var fill = '';
    				var getfill = function(No,response,ResponseCatalog){
    					// console.log(response);
    					var ID_budget_left = response['ID_budget_left'];
    					var PostBudgetItem = response['PostName']+'-'+response['RealisasiPostName'];
    					var NameDepartement = response['NameDepartement'];
    					var ID_m_catalog = response['ID_m_catalog'];
    					var Desc = '';
    					var EstimaValue = '';
    					var Photo = '';
    					var DetailCatalog = '';
    					var Item = response['Item'];
    					ResponseCatalog = ResponseCatalog['data'];
    					for (var i = 0; i < ResponseCatalog.length; i++) {
    						if (ID_m_catalog == ResponseCatalog[i][6]) {
    							Desc = ResponseCatalog[i][2];
    							EstimaValue = ResponseCatalog[i][3];
    							Photo = ResponseCatalog[i][4];
    							DetailCatalog = ResponseCatalog[i][5];
    							break;
    						}
    					}
    					var arr = Item+'@@'+Desc+'@@'+EstimaValue+'@@'+Photo+'@@'+DetailCatalog;
    					arr = findAndReplace(arr, "\"","'");
    					var Qty = response['Qty'];
    					var SpecAdd = (response['Spec_add'] == '' || response['Spec_add'] == null || response['Spec_add'] == 'null') ? '' : response['Spec_add'];
    					var Need = (response['Need'] == '' || response['Need'] == null || response['Need'] == 'null') ? '' : response['Need'];
    					var SubTotal = response['SubTotal'];
    					var n = SubTotal.indexOf(".");
    					SubTotal = SubTotal.substring(0, n);
    					var UnitCost = response['UnitCost'];
    					var n = UnitCost.indexOf(".");
    					UnitCost = UnitCost.substring(0, n);
    					var DateNeeded = response['DateNeeded'];
    					var UploadFile = response['UploadFile'];
    					UploadFile = jQuery.parseJSON(UploadFile);
    					var htmlUploadFile = '';
    					if (UploadFile != null) {
    						if (UploadFile.length > 0) {
    							for (var i = 0; i < UploadFile.length; i++) {
    								htmlUploadFile += '<li><a href = "'+base_url_js+'fileGetAny/budgeting-pr-'+UploadFile[i]+'" target="_blank" class = "Fileexist">File '+(i+1)+'</a></li>';
    							}
    						}
    					}

    					var OP = [
    							{
    								name  : 'IN',
    								color : 'green'
    							},
    							{
    								name  : 'Exceed',
    								color : 'red'
    							},
    							{
    								name  : 'Cross',
    								color : 'yellow'
    							},
    						];

    						var html = '<select class = "form-control BudgetStatus">';
    						var DefaultName = response['BudgetStatus'];
    						for (var i = 0; i < OP.length; i++) {
    							if (DefaultName == 'IN') {
    								if (OP[i].name == 'Exceed') {
    									continue;
    								}
    							}
    							var selected = (DefaultName == OP[i].name) ? 'selected' : '';
    							html += '<option value = "'+OP[i].name+'"'+selected+'>'+OP[i].name+'</option>';
    						}
    						html += '</select>';
		
    					var action = '<td><button type="button" class="btn btn-danger btn-delete btn-delete-item"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button></td>';
    					var a = '<tr>'+
    								'<td>'+No+'</td>'+
    								'<td>'+html+'</td>'+
    								'<td>'+
    									'<div class="input-group">'+
    										'<input type="text" class="form-control PostBudgetItem" readonly id_budget_left = "'+ID_budget_left+'" value = "'+PostBudgetItem+'" namedepartement = "'+NameDepartement+'">'+
    										'<span class="input-group-btn">'+
    											'<button class="btn btn-default SearchPostBudget" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>'+
    										'</span>'+
    									'</div>'+
    								'</td>'+
    								'<td>'+
    									'<div class="input-group">'+
    										'<input type="text" class="form-control Item" readonly savevalue= "'+ID_m_catalog+'" value = "'+Item+'">'+
    										'<span class="input-group-btn">'+
    											'<button class="btn btn-default SearchItem" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>'+
    										'</span>'+
    									'</div>'+
    								'</td>'+
    								'<td><button class = "btn btn-primary Detail" data = "'+arr+'">Detail</button></td>'+
    								'<td>'+
    									'<textarea class = "form-control SpecAdd" rows = "2">'+SpecAdd+'</textarea>'+
    									// '<input type="text" class="form-control SpecAdd" value="'+SpecAdd+'">'+
    								'</td>'+
    								'<td>'+
    									'<textarea class = "form-control Need" rows = "2">'+Need+'</textarea>'+
    									// '<input type="text" class="form-control Need" value="'+Need+'">'+
    								'</td>'+
    								'<td><input type="number" min = "1" class="form-control qty"  value="'+Qty+'"></td>'+
    								'<td><input type="text" class="form-control UnitCost"  value = "'+UnitCost+'"></td>'+
    								'<td><input type="text" class="form-control SubTotal" disabled value = "'+SubTotal+'"></td>'+
    								'<td>'+
    									'<div id="datetimepicker1'+No+'" class="input-group input-append date datetimepicker">'+
    			                            '<input data-format="yyyy-MM-dd" class="form-control" id="tgl'+No+'" type=" text" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
    			                            '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
    	                        		'</div>'+
    	                        	'</td>'+
    	                        	'<td><input type="file" data-style="fileinput" class = "BrowseFile" ID = "BrowseFile'+No+'" multiple accept="image/*,application/pdf">'+htmlUploadFile+'</td>'+action
    	                        '</tr>';	

    					return a;				
    				}

    				var pr_detail = response['pr_detail'];
    				$('#table_input_pr tbody tr:first').remove();
    				for (var i = 0; i < pr_detail.length; i++) {
    					fill = getfill(No,pr_detail[i],ResponseCatalog);
    					$('#table_input_pr tbody').append(fill);
    					$('.datetimepicker').datetimepicker();
    					No++;
    				}
    				$(".SubTotal").maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
    				$(".SubTotal").maskMoney('mask', '9894');
    				$(".UnitCost").maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
    				$(".UnitCost").maskMoney('mask', '9894');
    				_BudgetRemaining();

    				// FuncBudgetStatus();

    				var Status = pr_create[0]['Status'];
    				if (Status >= 1) {
    					// jika reject and user access = 1 then dont disable
    					if ( !(Status == 3 && UserAccess == 1) ) {
    						$('button:not([id="pdfprint"]):not([id="excelprint"]):not([id="btnBackToHome"])').prop('disabled', true);
    						$(".Detail").prop('disabled', false);
    						$("input").prop('disabled', true);
    						$("select").prop('disabled', true);
    						$("textarea").prop('disabled', true);
    						$(".input-group-addon").remove();
    					}
    					if (UserAccess > 1) {
    						var NIP = "<?php echo $this->session->userdata('NIP') ?>";
    						JsonStatus = jQuery.parseJSON(pr_create[0]['JsonStatus']);
    						var bool = false;
    						var HierarkiApproval = 0; // for check hierarki approval;
    						var NumberOfApproval = 0; // for check hierarki approval;
    						for (var i = 0; i < JsonStatus.length; i++) {
    							NumberOfApproval++;
    							if (JsonStatus[i]['Status'] == 0) {
    								// check status before
    								if (i > 0) {
    									var ii = i - 1;
    									if (JsonStatus[ii]['Status'] == 1) {
    										HierarkiApproval++;
    									}
    								}
    								else
    								{
    									HierarkiApproval++;
    								}
    								
    								if (NIP == JsonStatus[i]['ApprovedBy']) {
    									bool = true;
    									break;
    								}
    							}
    							else
    							{
    								HierarkiApproval++;
    							}
    							
    						}

    						// if kabag finance, dapat approve setelah approvalnya
    						var CustomAttr = '';
    						<?php if ($this->session->userdata('IDdepartementNavigation') == 9): ?>
    							<?php $PositionMain = $this->session->userdata('PositionMain');$Position=$PositionMain['IDPosition']; ?>
    							<?php if ($Position==11): ?>
		    						if (!bool) {
		    							// find what this approver ? in indeks array
			    							for (var ap = 0; ap < JsonStatus.length; ap++) {
			    								if (NIP == JsonStatus[ap]['ApprovedBy']) {
			    									break;
			    								}
			    							}

			    							// hitung approver dari satu
			    								ap = ap + 1;
			    								if (HierarkiApproval > ap) {
			    									if (Status == 1) {
			    										bool = true;
			    										NumberOfApproval = HierarkiApproval;
			    									}
			    									
			    								}

			    							// hitung UserAccess unt key indeks array pas save data
			    								var RepresentedNIP = '';
			    								var RepresentedName = '';
			    								for (var ap = 0; ap < JsonStatus.length; ap++) {
			    									if (JsonStatus[ap]['Status'] != 1) {
			    										RepresentedNIP = JsonStatus[ap]['ApprovedBy'];
			    										RepresentedName = JsonStatus[ap]['NameAprrovedBy'];
			    										break;
			    									}
			    								}

			    								ap = ap + 2; // array dimulai dari 0 maka tambah 1, user access 1 adalah admin karena itu ditambah satu lagi
			    								UserAccess = ap;

			    							// write custom attr untuk save deteksi represented approver
			    								if (bool) {
			    									CustomAttr = 'RepresentedNIP = "'+RepresentedNIP+'" RepresentedName="'+RepresentedName+'"';
			    								}	

		    						}
		    					<?php endif ?>
		    				<?php endif ?>
		    				// end if kabag finance, dapat approve setelah approvalnya		

		    				// console.log(HierarkiApproval);
		    				// console.log(NumberOfApproval);
		    				// console.log(bool);
    						if (bool && HierarkiApproval == NumberOfApproval) { // rule approval
	    						var ApprovalBtn = '<div class = "row" style = "margin-top : 10px;margin-left : 0px;margin-right : 0px">'+
					   								'<div class = "col-md-12">'+
					   									'<div class = "pull-right">'+
					   										'<button class = "btn btn-primary" id = "approve" userAccess = "'+UserAccess+'" PRCode = "'+PRCodeVal+'" '+CustomAttr+'> <i class = "fa fa-handshake-o"> </i> Approve</button>'+
					   										'&nbsp&nbsp'+
					   										'<button class = "btn btn-inverse" id = "reject" userAccess = "'+UserAccess+'" PRCode = "'+PRCodeVal+'" '+CustomAttr+'> <i class = "fa fa-remove"> </i> Reject</button>'+
					   									'</div>'+
					   								'</div>'+
					   							 '</div>';
					   			$('#Page_Input_PR').append(ApprovalBtn);
    						} // End rule approval
    										
    					}
    				}

    				if ($("#p_prcode").length) {
    					$("#p_prcode").html('PRCode : '+PRCodeVal)
    				}
    				else
    				{
    					$(".thumbnail").find('.row:first').before('<p style = "color : red" id = "p_prcode">PRCode : '+PRCodeVal+'</p>');
    				}

    				if (Status == 0) {
	    				if ($(".Fileexist").length) {
	    					$(".btn-add-pr").after('<br><p style = "color : red">*** Please reupload file</p>')
	    				}
	    			}	
    				
			    }); 
		
			} // end edit

			// find auth entry
				for (var i = 0; i < RuleAccess.length; i++) {
					if (RuleAccess[i]['Entry'] == 1) {
						MaxLimit = RuleAccess[i].MaxLimit;
					}
				}
			
			if (MaxLimit == 0) {
				$('button:not([id="pdfprint"]):not([id="excelprint"]):not([id="btnBackToHome"])').prop('disabled', true);
				$(".Detail").prop('disabled', false);
				$("input").prop('disabled', true);
				$("select").prop('disabled', true);
				$("textarea").prop('disabled', true);
				$(".input-group-addon").remove();
			}

			// console.log(MaxLimit);
		}

		$(document).off('click', '.btn-add-pr').on('click', '.btn-add-pr',function(e) {
			AddingTable();
		})

		$(document).off('change', '#ppn').on('change', '#ppn',function(e) {
			// delete row combine is exist 
			var PostBudgetDepartmentCombine = [];
			var tableRow = $('#table_input_pr td:eq(12)').filter(function() {
			    return $(this).text() != 'No';
			}).closest("tr");
			if (tableRow.length) {
							tableRow
				              .remove();
				toastr.info('Row data containing combine have been deleted');              
			}

			_BudgetRemaining();
			SortByNumbering();
			              
		})

		function AddingTable()
		{
			var fill = '';
			var getfill = function(No){
				var action = '<td></td>';
				if (No >= 1) {
					action = '<td><button type="button" class="btn btn-danger btn-delete btn-delete-item"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button></td>';
				}

				var opBudgetStatus = function()
				{
					var html = '<select class = "form-control BudgetStatus">';
					var OP = [
							{
								name  : 'IN',
								color : 'green'
							},
							// {
							// 	name  : 'Exceed',
							// 	color : 'red'
							// },
							{
								name  : 'Cross',
								color : 'yellow'
							},
						];

						var DefaultName = 'IN';
						for (var i = 0; i < OP.length; i++) {
							var selected = (DefaultName == OP[i].name) ? 'selected' : '';
							html += '<option value = "'+OP[i].name+'"'+selected+'>'+OP[i].name+'</option>';
						}
						html += '</select>';
						return html;
				};


				var a = '<tr>'+
							'<td>'+No+'</td>'+
							'<td>'+
								opBudgetStatus()+
							'</td>'+
							'<td>'+
								'<div class="input-group">'+
									'<input type="text" class="form-control PostBudgetItem" readonly>'+
									'<span class="input-group-btn">'+
										'<button class="btn btn-default SearchPostBudget" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>'+
									'</span>'+
								'</div>'+
							'</td>'+
							'<td>'+
								'<div class="input-group">'+
									'<input type="text" class="form-control Item" readonly>'+
									'<span class="input-group-btn">'+
										'<button class="btn btn-default SearchItem" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>'+
									'</span>'+
								'</div>'+
							'</td>'+
							'<td><button class = "btn btn-primary Detail">Detail</button></td>'+
							'<td>'+
								'<textarea class = "form-control SpecAdd" rows = "2"></textarea>'+
								// '<input type="text" class="form-control SpecAdd">'+
							'</td>'+
							'<td>'+
								'<textarea class = "form-control Need" rows = "2"></textarea>'+
								// '<input type="text" class="form-control Need">'+
							'</td>'+
							'<td><input type="number" min = "1" class="form-control qty"  value="1" disabled></td>'+
							'<td><input type="text" class="form-control UnitCost" disabled></td>'+
							'<td><input type="text" class="form-control SubTotal" disabled value = "0"></td>'+
							'<td>'+
								'<div id="datetimepicker1'+No+'" class="input-group input-append date datetimepicker">'+
		                            '<input data-format="yyyy-MM-dd" class="form-control" id="tgl'+No+'" type=" text" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
		                            '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
                        		'</div>'+
                        	'</td>'+
                        	'<td><input type="file" data-style="fileinput" class = "BrowseFile" ID = "BrowseFile'+No+'" multiple accept="image/*,application/pdf"></td>'+
                        	'<td>No</td>'+
                        	action
                        '</tr>';	

				return a;				
			}

			var rowCount = $('#table_input_pr tr').length;
			No = rowCount;


			if ($("#table_input_pr tbody").children().length == 0) {
				fill = getfill(No);
				$('#table_input_pr tbody').append(fill);
				$('.datetimepicker').datetimepicker();
			}
			else
			{
				No = $('#table_input_pr > tbody > tr:last').find('td:eq(0)').text();
				No++;
				fill = getfill(No);
				$('#table_input_pr tbody').append(fill);
				$('.datetimepicker').datetimepicker();
			}
			//eventTableFunction();
		}
		
		$(document).off('click', '.SearchPostBudget').on('click', '.SearchPostBudget',function(e) {
			var ev = $(this);
			PostBudgetDepartment = JSON.parse(localStorage.getItem("PostBudgetDepartment"));
			// check Budget Status apakah in atau cross
				var BudgetStatus = ev.closest('tr').find('td:eq(1)').find('.BudgetStatus').val();

				if (BudgetStatus == 'IN') {
					var html = '';
					html ='<div class = "row">'+
							'<div class = "col-md-12">'+
								'<table id="example" class="table table-bordered display select" cellspacing="0" width="100%">'+
		               '<thead>'+
		                  '<tr>'+
		                     // '<th><input type="checkbox" name="select_all" value="1" id="example-select-all"></th>'+
		                     '<th></th>'+
		                     '<th>Post Budget Item</th>'+
		                     '<th>Remaining</th>'+
		                  '</tr>'+
		               '</thead>'+
		          '</table></div></div>';

						$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Post Budget Item'+'</h4>');
						$('#GlobalModalLarge .modal-body').html(html);
						$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
		              '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>');
						$('#GlobalModalLarge').modal({
						    'show' : true,
						    'backdrop' : 'static'
						});

						// passing array Department These Division
						var ArrBudgetDepartment = [];
						var Div = "<?php echo $this->session->userdata('IDDepartementPUBudget') ?>";
						// filtering PostBudgetDepartment value > 0 berdasarkan budget remaining
							var PostBudgetDepartment_temp = [];
							var PostBudgetDepartment_temp = PostBudgetDepartment;
							for (var i = 0; i < PostBudgetDepartment_temp.length; i++) {
								var id_budget_left = PostBudgetDepartment_temp[i]['ID'];
								var bool = false;
								var key = 0;
								for (var j = 0; j < BudgetRemaining.length; j++) {
									var id_budget_left2 = BudgetRemaining[j]['id_budget_left'];
									if (id_budget_left2 == id_budget_left) {
										bool = true;
										key = j;
										break;
									}
								}

								if (bool) { // update
									PostBudgetDepartment_temp[i]['Value'] = parseInt(BudgetRemaining[key]['RemainingNoFormat'])
								}

							}
							

						for (var i = 0; i < PostBudgetDepartment_temp.length; i++) {
							if (PostBudgetDepartment_temp[i]['Departement'] == Div) {
								if (PostBudgetDepartment_temp[i]['Value'] > 0) {
									ArrBudgetDepartment.push(PostBudgetDepartment_temp[i]);
								}
								
							}
						}

						// console.log(ArrBudgetDepartment);

					var table = $('#example').DataTable({
					      "data" : ArrBudgetDepartment,
					      'columnDefs': [
						      {
						         'targets': 0,
						         'searchable': false,
						         'orderable': false,
						         'className': 'dt-body-center',
						         'render': function (data, type, full, meta){
						             return '<input type="checkbox" name="id[]" value="' + full.ID + '" estvalue="' + full.Value + '" namedepartement = "'+full.NameDepartement+'" departement = "'+full.Departement+'">';
						         }
						      },
						      {
						         'targets': 1,
						         'render': function (data, type, full, meta){
						             return full.PostName+'-'+full.RealisasiPostName;
						         }
						      },
						      {
						         'targets': 2,
						         'render': function (data, type, full, meta){
						             return formatRupiah(full.Value);
						         }
						      },
					      ],
					      // 'order': [[1, 'asc']]
					});

					// Handle click on checkbox to set state of "Select all" control
					$('#example tbody').on('change', 'input[type="checkbox"]', function(){
						$('input[type="checkbox"]:not(.uniform)').prop('checked', false);
						$(this).prop('checked',true);
					   
					});

					$("#ModalbtnSaveForm").click(function(){
						// clear  PostBudget Combine sesuai dengan no
						var fillItem = ev.closest('tr');
						var No = fillItem.find('td:eq(0)').text();
						ClearPostBudgetDepartmentCombine(No);

						var chkbox = $('input[type="checkbox"]:checked:not(.uniform)');
						var checked = chkbox.val();
						var estvalue = chkbox.attr('estvalue');
						var NameDepartement = chkbox.attr('namedepartement');
						var Departement = chkbox.attr('departement');
						var n = estvalue.indexOf(".");
						if (n >= 0) {
							estvalue = estvalue.substring(0, n);
						}
						var row = chkbox.closest('tr');
						var PostBudgetItem = row.find('td:eq(1)').text();
						fillItem.find('td:eq(2)').find('.PostBudgetItem').val(PostBudgetItem);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left',checked);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('remaining',estvalue);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('namedepartement',NameDepartement);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('departement',Departement);
						fillItem.find('td:eq(7)').find('.qty').trigger('change');
						$('#GlobalModalLarge').modal('hide');
					})
				} // exit IN
				else if(BudgetStatus == 'Cross')
				{
					// console.log(PostBudgetDepartment);
					// show department except Department myself
					var html = ''
					var opDepartment = '';
					var Div = "<?php echo $this->session->userdata('IDDepartementPUBudget') ?>";
					for (var i = 0; i < DepartementArr.length; i++) {
						if (DepartementArr[i]['Code'] != Div) {
							opDepartment += '<option value="'+ DepartementArr[i]['Code']  +'" '+''+'>'+DepartementArr[i]['Name2']+'</option>';
						}
						
					}
					html = '<div class = "row">'+
								'<div class = "col-md-4 col-md-offset-4">'+
									'<div class="form-group">'+
										'<label class="control-label">Department</label>'+
										'<select class = "select2-select-00 full-width-fix" id = "DepartementPostModal">'+
											opDepartment+
										'</select>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div class = "row" style = "margin-top : 10px" id = "pageContentModal">'+
							'</div>'
							;

					$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Post Budget Item'+'</h4>');
					$('#GlobalModalLarge .modal-body').html(html);
					$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
	              '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>');
					$('#GlobalModalLarge').modal({
					    'show' : true,
					    'backdrop' : 'static'
					});

					$('#DepartementPostModal').select2({
					   //allowClear: true
					});

					PostBudgetAnotherDepartment(ev);
				}

		})

		function PostBudgetAnotherDepartment(ev)
		{
			var DepartementPostModal = $("#DepartementPostModal").val();
			// get ajax post modal
				var Year = $("#Year").val();
				var url = base_url_js+"budgeting/detail_budgeting_remaining";
				var data = {
						    Year : Year,
							Departement : DepartementPostModal,
						};
				var token = jwt_encode(data,'UAP)(*');
				$.post(url,{token:token},function (resultJson) {
					var response = jQuery.parseJSON(resultJson);
					var PostBudgetDepartmentModal = response.data;
					PostBudgetDepartment = JSON.parse(localStorage.getItem("PostBudgetDepartment"));
					// PostBudgetDepartment
					if (PostBudgetDepartmentModal.length > 0) {
						// PostBudgetDepartment = PostBudgetDepartment.concat(PostBudgetDepartmentModal); 
						var P_result = Filtering_PostBudgetDepartment(PostBudgetDepartment,PostBudgetDepartmentModal);
						PostBudgetDepartment = P_result;
						localStorage.setItem("PostBudgetDepartment", JSON.stringify(PostBudgetDepartment));
					}

					var ArrBudgetDepartment = [];
					var PostBudgetDepartment_temp = [];
					var PostBudgetDepartment_temp = PostBudgetDepartmentModal;
					for (var i = 0; i < PostBudgetDepartment_temp.length; i++) {
						var id_budget_left = PostBudgetDepartment_temp[i]['ID'];
						var bool = false;
						var key = 0;
						for (var j = 0; j < BudgetRemaining.length; j++) {
							var id_budget_left2 = BudgetRemaining[j]['id_budget_left'];
							if (id_budget_left2 == id_budget_left) {
								bool = true;
								key = j;
								break;
							}
						}

						if (bool) { // update
							PostBudgetDepartment_temp[i]['Value'] = parseInt(BudgetRemaining[key]['RemainingNoFormat'])
						}

					}

					for (var i = 0; i < PostBudgetDepartment_temp.length; i++) {
						if (PostBudgetDepartment_temp[i]['Value'] > 0) {
							ArrBudgetDepartment.push(PostBudgetDepartment_temp[i]);
						}
					}


					var html = htmlPostBudgetDepartmentModal(ArrBudgetDepartment);
					$("#pageContentModal").html(html);
					var table = $('#example').DataTable({
				      "data" : ArrBudgetDepartment,
				      'columnDefs': [
					      {
					         'targets': 0,
					         'searchable': false,
					         'orderable': false,
					         'className': 'dt-body-center',
					         'render': function (data, type, full, meta){
					             return '<input type="checkbox" name="id[]" value="' + full.ID + '" estvalue="' + full.Value + '" namedepartement = "'+full.NameDepartement+'" departement = "'+full.Departement+'">';
					         }
					      },
					      {
					         'targets': 1,
					         'render': function (data, type, full, meta){
					             return full.PostName+'-'+full.RealisasiPostName;
					         }
					      },
					      {
					         'targets': 2,
					         'render': function (data, type, full, meta){
					             return formatRupiah(full.Value);
					         }
					      },
				      ],
				      // 'order': [[1, 'asc']]
					});

					// Handle click on checkbox to set state of "Select all" control
					$('#example tbody').on('change', 'input[type="checkbox"]', function(){
						$('input[type="checkbox"]:not(.uniform)').prop('checked', false);
						$(this).prop('checked',true);
					   
					});

					$("#ModalbtnSaveForm").click(function(){
						// clear  PostBudget Combine sesuai dengan no
						var fillItem = ev.closest('tr');
						var No = fillItem.find('td:eq(0)').text();
						ClearPostBudgetDepartmentCombine(No);

						var chkbox = $('input[type="checkbox"]:checked:not(.uniform)');
						var checked = chkbox.val();
						var estvalue = chkbox.attr('estvalue');
						var NameDepartement = chkbox.attr('namedepartement');
						var Departement = chkbox.attr('departement');
						var n = estvalue.indexOf(".");
						if (n >= 0) {
							estvalue = estvalue.substring(0, n);
						}
						var row = chkbox.closest('tr');
						var PostBudgetItem = row.find('td:eq(1)').text();
						fillItem.find('td:eq(2)').find('.PostBudgetItem').val(PostBudgetItem);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left',checked);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('remaining',estvalue);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('namedepartement',NameDepartement);
						fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('departement',Departement);
						fillItem.find('td:eq(7)').find('.qty').trigger('change');
						$('#GlobalModalLarge').modal('hide');
					})

					$(document).off('change', '#DepartementPostModal').on('change', '#DepartementPostModal',function(e) {
						PostBudgetAnotherDepartment(ev);
					})
				}).fail(function() {
				  toastr.info('No Result Data'); 
				}).always(function() {
				                
				});
		}

		function ClearPostBudgetDepartmentCombine(No)
		{
			// console.log('ClearPostBudgetDepartmentCombine');
			var arr = [];
			var indextr = No - 1;
			var arr_del = [];
			for (var i = 0; i < PostBudgetDepartmentCombine.length; i++) {
				if (PostBudgetDepartmentCombine[i]['No'] != No) {
					arr.push(PostBudgetDepartmentCombine[i]);
					var dt = PostBudgetDepartmentCombine[i]['dt'];
					for (var j = 0; j < dt.length; j++) {
						arr_del.push(dt[j]['id_budget_left']);
					}
				}
			}

			// console.log(arr);

			PostBudgetDepartmentCombine = arr;
			// Update Budget Remaining
				var arr2 = [];
				for (var i = 0; i < BudgetRemaining.length; i++) {
					var id_budget_left = BudgetRemaining[i]['id_budget_left'];
					var bool = false;
					for (var j = 0; j < arr_del.length; j++) {
						if (id_budget_left == arr_del[j]) {
							bool = true;
							break;
						}
					}
					
					if (!bool) { // adding to arrays 
						arr2 .push(BudgetRemaining[i]);
					}
				}

			BudgetRemaining = arr2;
			// console.log('End ClearPostBudgetDepartmentCombine');

		}

		function Filtering_PostBudgetDepartment(p1,p2)
		{
			var P_result = p1;
			var DepartementPostModal = p2[0]['Departement'];
			var bool = true;
			for (var i = 0; i < p1.length; i++) {
				var D = p1[i]['Departement'];
				if (D == DepartementPostModal) {
					bool = false;
					break;
				}
			}

			// tidak ada pada list
				if (bool) {
					P_result = P_result.concat(p2);
				}

			return P_result;	

		}

		function htmlPostBudgetDepartmentModal(PostBudgetDepartmentModal)
		{
			html ='<div class = "row">'+
					'<div class = "col-md-12">'+
						'<table id="example" class="table table-bordered display select" cellspacing="0" width="100%">'+
               '<thead>'+
                  '<tr>'+
                     // '<th><input type="checkbox" name="select_all" value="1" id="example-select-all"></th>'+
                     '<th></th>'+
                     '<th>Post Budget Item</th>'+
                     '<th>Remaining</th>'+
                  '</tr>'+
               '</thead>'+
          '</table></div></div>';
          return html;
		}

		$(document).off('change', '.BudgetStatus').on('change', '.BudgetStatus',function(e) {
			var ev = $(this).closest('tr');
			var PostBudgetItem = ev.find('td:eq(2)').find('.PostBudgetItem').val();
			var Item = ev.find('td:eq(3)').find('.Item').val();
			if (PostBudgetItem != '' || Item != '') {
				// delete
				ev
              .closest( 'tr')
              .remove();
              _BudgetRemaining();
              SortByNumbering();

              // add
              AddingTable();
			}
		})

		$(document).off('change', '.qty').on('change', '.qty',function(e) {
			var qty = $(this).val();
			var fillItem = $(this).closest('tr');
			var estvalue = fillItem.find('td:eq(8)').find('.UnitCost').val();
			estvalue = findAndReplace(estvalue, ".","");
			var SubTotal = parseInt(qty) * parseInt(estvalue);
			fillItem.find('td:eq(9)').find('.SubTotal').val(SubTotal);
			fillItem.find('td:eq(9)').find('.SubTotal').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			fillItem.find('td:eq(9)').find('.SubTotal').maskMoney('mask', '9894');
			_BudgetRemaining();
			FuncBudgetStatus();
			var id_budget_left = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left');
		})

		$(document).off('keyup', '.UnitCost').on('keyup', '.UnitCost',function(e) {
			var fillItem = $(this).closest('tr');
			var qty = fillItem.find('td:eq(7)').find('.qty').val();
			var estvalue = $(this).val();
			estvalue = findAndReplace(estvalue, ".","");
			var SubTotal = parseInt(qty) * parseInt(estvalue);
			fillItem.find('td:eq(9)').find('.SubTotal').val(SubTotal);
			fillItem.find('td:eq(9)').find('.SubTotal').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			fillItem.find('td:eq(9)').find('.SubTotal').maskMoney('mask', '9894');
			_BudgetRemaining();
			FuncBudgetStatus();
			var id_budget_left = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left');
			
		})

		// $(document).off('keyup', '#ppn').on('keyup', '#ppn',function(e) {
		// 	// delete row combine is exist 
		// 	var PostBudgetDepartmentCombine = [];
		// 	var tableRow = $('#table_input_pr td:eq(12)').filter(function() {
		// 	    return $(this).text() != 'No';
		// 	}).closest("tr");
		// 				tableRow
		// 	              .remove();
		// 	              _BudgetRemaining();
		// 	              SortByNumbering();

		// })

		function _BudgetRemaining()
		{
			loading_page('#Page_Budget_Remaining');
			BudgetRemaining = [];
			var arr_temp = [];
			var htmltotal = 0;
			var ppn = $("#ppn").val();
			$('.PostBudgetItem').each(function(){
				var id_budget_left = $(this).attr('id_budget_left');
				var fillItem = $(this).closest('tr');
				var SubTotal = fillItem.find('td:eq(9)').find('.SubTotal').val();
				var SubTotal = findAndReplace(SubTotal, ".","");
				var Persent = (parseInt(ppn) / 100) * SubTotal;
				SubTotal = parseInt(SubTotal) + parseInt(Persent);
				htmltotal += parseInt(SubTotal);
				PostBudgetItem = fillItem.find('td:eq(2)').find('.PostBudgetItem').val();
				NameDepartement = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('namedepartement');
				Departement = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('departement');
				var No = fillItem.find('td:eq(0)').text();
				// cek data di PostBudgetDepartmentCombine
					var bool = false
					var combine = [];	;
					for (var i = 0; i < PostBudgetDepartmentCombine.length; i++) {
						var NoDT = PostBudgetDepartmentCombine[i]['No'];
						var dt = PostBudgetDepartmentCombine[i]['dt'];
						if (NoDT == No) {
							for (var j = 0; j < dt.length; j++) {
								combine.push(dt[j]);
							}
							bool = true;
							
							break;
						}
					}

					combine.sort(function(a, b){
					    var keyA = new Date(a.id_budget_left),
					        keyB = new Date(b.id_budget_left);
					    // Compare the 2 dates
					    if(keyA < keyB) return -1;
					    if(keyA > keyB) return 1;
					    return 0;
					});
				
				var temp = {
					id_budget_left : id_budget_left,
					PostBudgetItem : PostBudgetItem,
					No : No,
					Departement : Departement,
					NameDepartement : NameDepartement,
					SubTotal : SubTotal,
					combine : combine,
				};

				arr_temp.push(temp);
			})

			arr_temp.sort(function(a, b){
			    var keyA = new Date(a.id_budget_left),
			        keyB = new Date(b.id_budget_left);
			    // Compare the 2 dates
			    if(keyA < keyB) return -1;
			    if(keyA > keyB) return 1;
			    return 0;
			});


			PostBudgetDepartment = JSON.parse(localStorage.getItem("PostBudgetDepartment"));
			for (var i = 0; i < arr_temp.length; i++) {
				var id_budget_left = arr_temp[i]['id_budget_left'];
				var Remaining = 0;
				// process remaining
				for (var j = 0; j < PostBudgetDepartment.length; j++) {
					var B_id_budget_left = PostBudgetDepartment[j].ID;
					if (B_id_budget_left == id_budget_left) {
						Remaining = PostBudgetDepartment[j].Value;
						break;
					}
				}

				// cek bantuan combine
				var Combine = arr_temp[i]['combine'];
				if (Combine.length > 0) {
					for (var j = 0; j < Combine.length; j++) {
						cost = Combine[j]['cost'];
						Remaining = parseInt(Remaining) + parseInt(cost);
					}
				}


				Remaining = parseInt(Remaining) - parseInt(arr_temp[i]['SubTotal']);
				// search id_budget_left yang sama
				for (var j = i+1; j < arr_temp.length; j++) {
					var id_budget_left2 = arr_temp[j]['id_budget_left'];
					if (id_budget_left == id_budget_left2) {
						// cek bantuan combine
						var Combine = arr_temp[j]['combine'];
						if (Combine.length > 0) {
							for (var k = 0; k < Combine.length; k++) {
								cost = Combine[k]['cost'];
								Remaining = parseInt(Remaining) + parseInt(cost);
							}
						}

						Remaining = Remaining - parseInt(arr_temp[j]['SubTotal']);
						break;
					}
				}

				// save arr budgeting
				var dataarr = {
					PostBudgetItem : arr_temp[i]['PostBudgetItem'],
					Remaining : formatRupiah(Remaining),
					id_budget_left : arr_temp[i]['id_budget_left'],
					RemainingNoFormat : Remaining,
					NameDepartement : arr_temp[i]['NameDepartement'],
					Departement : arr_temp[i]['Departement'],
				}

				BudgetRemaining.push(dataarr);

				// search budget combine
					if (Combine.length > 0) {
						// sort combine first
						var key = '';
						// get last key
						for (var j = 0; j < Combine.length; j++) {
							id_budget_left_combine1 = Combine[j]['id_budget_left'];
							key = j;
							var bool = true;
							for (var k = j+1; k < Combine.length; k++) {
								var id_budget_left_combine2 = Combine[k]['id_budget_left'];
								if (id_budget_left_combine1 == id_budget_left_combine2) {
									key = k;
								}

								j = k;
							}

							var dataarr = {
								PostBudgetItem : Combine[key]['value'],
								Remaining : formatRupiah( parseInt(Combine[key]['estvalue']) - parseInt(Combine[key]['value']) ),
								id_budget_left : Combine[key]['id_budget_left'],
								RemainingNoFormat : parseInt(Combine[key]['estvalue']) - parseInt(Combine[key]['value']),
								NameDepartement : Combine[key]['NameDepartement'],
								Departement : Combine[key]['Departement'],
							}

							BudgetRemaining.push(dataarr);	
						}
					}

			}

			$("#phtmltotal").html('Total : '+formatRupiah(htmltotal));
			loadShowBUdgetRemaining(BudgetRemaining);
		}

		// function _BudgetRemaining()
		// {
		// 	// loadingStart();
		// 	loading_page('#Page_Budget_Remaining');
		// 	console.log('awal _BudgetRemaining');
		// 	console.log(BudgetRemaining);console.log('---');

		// 	console.log('clear');
		// 	BudgetRemaining = [];
		// 	console.log(BudgetRemaining);console.log('---');
		// 	PostBudgetDepartment = JSON.parse(localStorage.getItem("PostBudgetDepartment"));
		// 	var arr_id_budget_left = [];
		// 	$('.PostBudgetItem').each(function(){
		// 		var id_budget_left = $(this).attr('id_budget_left');
		// 		arr_id_budget_left.push(id_budget_left);
		// 	})

		// 	var uniqueArray = function(arrArg) {
		// 	  return arrArg.filter(function(elem, pos,arr) {
		// 	    return arr.indexOf(elem) == pos;
		// 	  });
		// 	};

		// 	arr_id_budget_left = uniqueArray(arr_id_budget_left);
		// 	// console.log(arr_id_budget_left);

		// 	var htmltotal = 0;
		// 	var ppn = $("#ppn").val();
		// 	for (var i = 0; i < arr_id_budget_left.length; i++) {
		// 		var total = 0;
		// 		var PostBudgetItem = '';
		// 		var NameDepartement = '';
		// 		var Departement = '';
		// 		var Remaining = 0;
		// 		var GetNO = i + 1;
		// 		var id_budget_left = arr_id_budget_left[i];
		// 		var RemainingNoFormat = 0;
		// 		var remainingTxt = 0;
		// 		$('.PostBudgetItem[id_budget_left="'+id_budget_left+'"]').each(function(){
		// 			var fillItem = $(this).closest('tr');
		// 			var SubTotal = fillItem.find('td:eq(9)').find('.SubTotal').val();
		// 			var SubTotal = findAndReplace(SubTotal, ".","");
		// 			PostBudgetItem = fillItem.find('td:eq(2)').find('.PostBudgetItem').val();
		// 			NameDepartement = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('namedepartement');
		// 			Departement = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('departement');
		// 			remainingTxt = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('remaining');
		// 			var Persent = (parseInt(ppn) / 100) * SubTotal;
		// 			SubTotal = parseInt(SubTotal) + parseInt(Persent);
		// 			total += parseInt(SubTotal);
		// 		})
				
		// 		htmltotal += parseInt(total);
		// 		// console.log(PostBudgetDepartment);
		// 		for (var l = 0; l < PostBudgetDepartment.length; l++) { // find Value awal
		// 			var B_id_budget_left = PostBudgetDepartment[l].ID;
		// 			if (B_id_budget_left == id_budget_left) {
		// 				// check data exist di PostBudgetDepartmentCombine
		// 					var boolCombine = false;
		// 					var ValuePostBudget = PostBudgetDepartment[l].Value;
		// 					console.log(PostBudgetDepartmentCombine);
		// 					for (var n = 0; n < PostBudgetDepartmentCombine.length; n++) {
		// 						var dt = PostBudgetDepartmentCombine[n]['dt'];
		// 						var NoDT = PostBudgetDepartmentCombine[n]['No'];
		// 						if (GetNO == NoDT) {
		// 							console.log(dt);
		// 							for (var o = 0; o < dt.length; o++) {
		// 								var estvalue = dt[o]['estvalue'];
		// 								var cost = dt[o]['cost'];
		// 								ValuePostBudget += parseInt(cost);
		// 								break;
		// 							}
		// 						}
								
		// 					}

		// 				Remaining = parseInt(ValuePostBudget) - parseInt(total);
		// 				// console.log(Remaining);
		// 				var dataarr = {
		// 					PostBudgetItem : PostBudgetItem,
		// 					Remaining : formatRupiah(Remaining),
		// 					No : GetNO,
		// 					id_budget_left : id_budget_left,
		// 					RemainingNoFormat : Remaining,
		// 					NameDepartement : NameDepartement,
		// 					Departement : Departement,
		// 				}

		// 				// check id_budget_left existing in BudgetRemaining
		// 				// for (var k = 0; k < BudgetRemaining.length; k++) {
		// 				// 	if (BudgetRemaining[k].id_budget_left == id_budget_left) {
		// 				// 		var removeItem = k;
		// 				// 		BudgetRemaining = $.grep(BudgetRemaining, function(value,index) {
		// 				// 		  return index != removeItem;
		// 				// 		});
		// 				// 		break;
		// 				// 	}
		// 				// }
		// 				var bool = false;
		// 				for (var m = 0; m < BudgetRemaining.length; m++) {
		// 					var id_budget_left_remaining = BudgetRemaining[m]['id_budget_left'];
		// 					if (id_budget_left == id_budget_left_remaining) {
		// 						BudgetRemaining[m] = dataarr;
		// 						bool = true;
		// 						break;
		// 					}
		// 				}
		// 				if (!bool) {
		// 					BudgetRemaining.push(dataarr);
		// 				}
						
		// 				break;
		// 			}
		// 		}					
		// 	}

		// 	$("#phtmltotal").html('Total : '+formatRupiah(htmltotal));
		// 	// if combine budget
		// 		console.log('PostBudgetDepartmentCombine');
		// 		console.log(PostBudgetDepartmentCombine);console.log('---');
		// 		// if (PostBudgetDepartmentCombine.length > 0) {
		// 		// 	for (var i = 0; i < PostBudgetDepartmentCombine.length; i++) {
		// 		// 		var No = PostBudgetDepartmentCombine[i]['No'];
		// 		// 		// update Post Budget yang di combine
		// 		// 			var tableRow = $("td").filter(function() {
		// 		// 			    return $(this).text() == No;
		// 		// 			}).closest("tr");
		// 		// 			var id_budget_left_by_number = tableRow.find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left');
		// 		// 			var value_budget = tableRow.find('td:eq(2)').find('.PostBudgetItem').attr('remaining');
		// 		// 			var SubTotal = tableRow.find('td:eq(9)').find('.SubTotal').val();
		// 		// 			SubTotal = findAndReplace(SubTotal, ".","");
		// 		// 			var Persent = (parseInt(ppn) / 100) * SubTotal;
		// 		// 			SubTotal = parseInt(SubTotal) + parseInt(Persent);

		// 		// 		var dt =  PostBudgetDepartmentCombine[i]['dt'];
		// 		// 		for (var j = 0; j < dt.length; j++) {
		// 		// 			var id_budget_left_get = dt[j]['id_budget_left'];
		// 		// 			// check in exist budget remaining
		// 		// 				var bool = false;
		// 		// 				for (var k = 0; k < BudgetRemaining.length; k++) {
		// 		// 					var id_budget_left = BudgetRemaining[k]['id_budget_left'];
		// 		// 					if (id_budget_left_get == id_budget_left) {
		// 		// 						console.log('i : '+i);
		// 		// 						console.log('j : '+j);
		// 		// 						console.log('k : '+k);
		// 		// 						console.log('PostBudgetItem');console.log(BudgetRemaining[k]['PostBudgetItem']);console.log('---');
		// 		// 						bool = true;
		// 		// 						break;
		// 		// 					}
		// 		// 				}

		// 		// 			if (!bool) {
		// 		// 				var dataarr = {
		// 		// 					PostBudgetItem : dt[j]['value'],
		// 		// 					Remaining : formatRupiah( ( parseInt(dt[j]['estvalue']) - parseInt(dt[j]['cost'])  ) ),
		// 		// 					No : No,
		// 		// 					id_budget_left : id_budget_left_get,
		// 		// 					RemainingNoFormat : parseInt(dt[j]['estvalue']) - parseInt(dt[j]['cost']),
		// 		// 					NameDepartement : dt[j]['NameDepartement'],
		// 		// 					Departement : dt[j]['Departement'],
		// 		// 				}
		// 		// 				BudgetRemaining.push(dataarr);
		// 		// 			}

		// 		// 			for (var k = 0; k < BudgetRemaining.length; k++) {
		// 		// 				var id_budget_left = BudgetRemaining[k]['id_budget_left'];
		// 		// 				if (id_budget_left == id_budget_left_by_number) {
		// 		// 					// console.log('PostBudgetItem');console.log(BudgetRemaining[k]['PostBudgetItem']);console.log('---');
		// 		// 					// console.log('dtjcost');
		// 		// 					// console.log(dt[j]['cost']);console.log('---');
		// 		// 					// console.log('value_budget');
		// 		// 					// console.log(value_budget);console.log('---');
		// 		// 					var Cost = parseInt(dt[j]['cost']) + parseInt(value_budget);
		// 		// 					// BudgetRemaining[k]['RemainingNoFormat'] = parseInt(Cost) - parseInt(SubTotal) ;
		// 		// 					// console.log('Cost');
		// 		// 					// console.log(Cost);console.log('---');
		// 		// 					// console.log('SubTotal');
		// 		// 					// console.log(SubTotal);console.log('---');
		// 		// 					BudgetRemaining[k]['RemainingNoFormat'] = parseInt(Cost) - parseInt(SubTotal) ;
		// 		// 					BudgetRemaining[k]['Remaining']  = formatRupiah(BudgetRemaining[k]['RemainingNoFormat']);
		// 		// 					break;
		// 		// 				}
		// 		// 			}
		// 		// 		}
						
		// 		// 	}

		// 			// update Budget Remaining 
		// 				// for (var i = 0; i < PostBudgetDepartmentCombine.length; i++) {
		// 				// 	var dt =  PostBudgetDepartmentCombine[i]['dt'];
		// 				// 	var key1 = '';
		// 				// 	var key2 = '';
		// 				// 	for (var j = 0; j < dt.length; j++) {
		// 				// 		var id_budget_left_get = dt[j]['id_budget_left'];
		// 				// 		// get last to update dt
		// 				// 		for (var k = i+1; k < PostBudgetDepartmentCombine.length; k++) {
		// 				// 			var dt2 =  PostBudgetDepartmentCombine[k]['dt'];
		// 				// 			for (var l = 0; l < dt2.length; l++) {
		// 				// 				var id_budget_left_get2 = dt2[l]['id_budget_left'];
		// 				// 				if (id_budget_left_get == id_budget_left_get2) {
		// 				// 					// update
		// 				// 						for (var m = 0; m < BudgetRemaining.length; m++) {
		// 				// 							var id_budget_left_remaining = BudgetRemaining[m]['id_budget_left'];
		// 				// 							if (id_budget_left_remaining == id_budget_left_get) {
		// 				// 								BudgetRemaining[m]['RemainingNoFormat'] = parseInt(dt2[l]['estvalue'] ) - parseInt(dt2[l]['cost']) ;
		// 				// 								BudgetRemaining[m]['Remaining']  = formatRupiah(BudgetRemaining[m]['RemainingNoFormat']);
		// 				// 								break;
		// 				// 							}
													
		// 				// 						}
		// 				// 				}
		// 				// 			}
		// 				// 		}
		// 				// 	}
		// 				// }

		// 		//}
		// 	// endif combine budget	
		// 	console.log(BudgetRemaining);
		// 	loadShowBUdgetRemaining(BudgetRemaining);
		// 	// loadingEnd(500)

		// }

		$(document).off('click', '.SearchItem').on('click', '.SearchItem',function(e) {
			$(".uniform").prop('disabled', true);
			var ev = $(this);
			var html = '';
			html ='<div class = "row">'+
					'<div class = "col-md-12">'+
						'<table id="example" class="table table-bordered display select" cellspacing="0" width="100%">'+
               '<thead>'+
                  '<tr>'+
                     // '<th><input type="checkbox" name="select_all" value="1" id="example-select-all"></th>'+
                     '<th></th>'+
                     '<th>Item</th>'+
                     '<th>Desc</th>'+
                     '<th>Estimate Value</th>'+
                     '<th>Photo</th>'+
                     '<th>DetailCatalog</th>'+
                  '</tr>'+
               '</thead>'+
          '</table></div></div>';
			$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Catalog'+'</h4>');
			$('#GlobalModalLarge .modal-body').html(html);
			$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
	        '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>');
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});

			var url = base_url_js+'rest/Catalog/__Get_Item';
			var data = {
				action : 'choices',
				auth : 's3Cr3T-G4N',
				department : $("#DepartementPost").val(),
				approval : 1,
			};
		    var token = jwt_encode(data,"UAP)(*");
			var table = $('#example').DataTable({
			      'ajax': {
			         'url': url,
			         'type' : 'POST',
			         'data'	: {
			         	token : token,
			         },
			         dataType: 'json'
			      },
			      'columnDefs': [{
			         'targets': 0,
			         'searchable': false,
			         'orderable': false,
			         'className': 'dt-body-center',
			         'render': function (data, type, full, meta){
			             return '<input type="checkbox" name="id[]" value="' + full[6] + '" estvalue="' + full[7] + '">';
			         }
			      }],
			      'order': [[1, 'asc']]
			   });

			   // Handle click on checkbox to set state of "Select all" control
			   $('#example tbody').on('change', 'input[type="checkbox"]', function(){
			   	$('input[type="checkbox"]:not(.uniform)').prop('checked', false);
			   	$(this).prop('checked',true);
			      
			   });

			   $("#ModalbtnSaveForm").click(function(){
			   		var chkbox = $('input[type="checkbox"]:checked:not(.uniform)');
			   		var checked = chkbox.val();
			   		var estvalue = chkbox.attr('estvalue');
			   		var n = estvalue.indexOf(".");
			   		estvalue = estvalue.substring(0, n);
			   		var row = chkbox.closest('tr');
			   		var Item = row.find('td:eq(1)').text();
			   		var Desc = row.find('td:eq(2)').text();
			   		var Est = row.find('td:eq(3)').text();
			   		var Photo = row.find('td:eq(4)').html();
			   		var DetailCatalog =  row.find('td:eq(5)').html();
			   		var arr = Item+'@@'+Desc+'@@'+Est+'@@'+Photo+'@@'+DetailCatalog;
			   		var fillItem = ev.closest('tr');
			   		fillItem.find('td:eq(3)').find('.Item').val(Item);
			   		fillItem.find('td:eq(3)').find('.Item').attr('savevalue',checked);
			   		fillItem.find('td:eq(3)').find('.Item').attr('estvalue',estvalue);
			   		fillItem.find('td:eq(4)').find('.Detail').attr('data',arr);
			   		fillItem.find('td:eq(8)').find('.UnitCost').val(estvalue);
			   		fillItem.find('td:eq(8)').find('.UnitCost').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			   		fillItem.find('td:eq(8)').find('.UnitCost').maskMoney('mask', '9894');

			   		fillItem.find('td:eq(7)').find('.qty').prop('disabled', false);
			   		fillItem.find('td:eq(8)').find('.UnitCost').prop('disabled', false);

			   		fillItem.find('td:eq(7)').find('.qty').trigger('change');
			   		$('#GlobalModalLarge').modal('hide');
			   })
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
	               			html += '<td>'+arr[i]+'</td>';
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

		$(document).off('click', '.btn-delete-item').on('click', '.btn-delete-item',function(e) {
			var fillItem = $(this).closest('tr');
			var id_budget_left = fillItem.find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left');

			// delete budget berdasarkan No
				var No = fillItem.find('td:eq(0)').text();
				var arr = [];
				for (var i = 0; i < PostBudgetDepartmentCombine.length; i++) {
					var No2 = PostBudgetDepartmentCombine[i]['No'];
					if (No != No2) {
						arr.push(PostBudgetDepartmentCombine[i]);
					} 
				}

				PostBudgetDepartmentCombine = arr;

			$( this )
              .closest( 'tr')
              .remove();
              _BudgetRemaining();
              SortByNumbering();



		})

		function loadShowBUdgetRemaining(BudgetRemaining)
		{
			// console.log(BudgetRemaining);
			setTimeout(function () {
	           $("#Page_Budget_Remaining").empty();
	           var html = '<div class = "row">'+
	           				'<div class = "col-md-12">'+
	           					'<table class="table table-bordered tableData" id ="tableData3">'+
	           						'<thead>'+
	           							'<tr>'+
	           								'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
	           								'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Post Budget Item</th>'+
	           								'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Department</th>'+
	           								'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Remaining</th>'+
	           							'</tr>'+
	           						'</thead><tbody>';
	           							
	           for (var i = 0; i < BudgetRemaining.length; i++) {
	           	var No = i + 1;
	           	html += '<tr>'+
	           				'<td>'+No+'</td>'+
	           				'<td>'+BudgetRemaining[i].PostBudgetItem+'</td>'+
	           				'<td>'+BudgetRemaining[i].NameDepartement+'</td>'+
	           				'<td>'+BudgetRemaining[i].Remaining+'</td>'+
	           			'</tr>';	
	           }

	           html += '</tbody>'+
	           		'</table>'+
	           		'</div>'+
	           		'</div>';		

	           $("#Page_Budget_Remaining").html(html);
			},1000);
			
		}

		function SortByNumbering()
		{
			var no = 1;
			$("#table_input_pr tbody tr").each(function(){
				var a = $(this);
				a.find('td:eq(0)').html(no);
				no++;
			})
		}

		function FuncBudgetStatus(BudgetStatus = null)
		{
			$('.PostBudgetItem').each(function(){
				var fillItem = $( this ).closest( 'tr');
				var id_budget_left = $( this ).attr('id_budget_left');
				var GetBudgetRemaining = function(id_budget_left,BudgetRemaining){
					var Remaining = 0;
					for (var i = 0; i < BudgetRemaining.length; i++) {
						if (id_budget_left == BudgetRemaining[i].id_budget_left) {
							Remaining = BudgetRemaining[i].RemainingNoFormat;
							break;
						}
					}
					return Remaining;
				};

				var Remaining = GetBudgetRemaining(id_budget_left,BudgetRemaining);
				// console.log(Remaining);

				var OP = [
						{
							name  : 'IN',
							color : 'green'
						},
						{
							name  : 'Exceed',
							color : 'red'
						},
						{
							name  : 'Cross',
							color : 'yellow'
						},
					];

				// check option value IF Cross maka jangan di rubah
					var OPexist = fillItem.find('td:eq(1)').find('.BudgetStatus').val();
					if (OPexist != 'Cross') {
						var DefaultName = (Remaining >= 0) ? 'IN' : 'Exceed';
						
						var disabled = (DefaultName == 'Exceed') ? 'disabled' : '';
						var html = '<select class = "form-control BudgetStatus"  '+disabled+'>';

						
						for (var i = 0; i < OP.length; i++) {
							if (DefaultName == 'IN') {
								if (OP[i].name == 'Exceed') {
									continue;
								}
							}
							var selected = (DefaultName == OP[i].name) ? 'selected' : '';
							html += '<option value = "'+OP[i].name+'"'+selected+'>'+OP[i].name+'</option>';
						}
						html += '</select>';
						fillItem.find('td:eq(1)').html(html);
					}

					// detected exceed for combine budget
						if ( !(Remaining >= 0) ) {
							// show button combine budget
								// jika id_budget_left lebih dari satu maka button combine akan muncul pada last number
									var NofillItem = fillItem.find('td:eq(0)').text();
									var Last = 0;
									$('.PostBudgetItem[id_budget_left = "'+id_budget_left+'"]').each(function(){
										var tr = $(this).closest('tr');
										last = tr.find('td:eq(0)').text();
									});
									if (NofillItem == last) {
										fillItem.find('td:eq(12)').html('<button class = "btn btn-default  ShowModalCombine">Combine</button>');
										fillItem.find('td:eq(12)').attr('align','center');
									}
									else
									{
										fillItem.find('td:eq(12)').html('No');
										fillItem.find('td:eq(12)').attr('align','center');
									}
						}
						else
						{
							var No = fillItem.find('td:eq(0)').text();
							var bool = true;
							for (var i = 0; i < PostBudgetDepartmentCombine.length; i++) {
								var No2 = PostBudgetDepartmentCombine[i]['No'];
								if (No2 == No) {
									bool = false; 	
									break;
								}
							}

							if (bool) {
								fillItem.find('td:eq(12)').html('No');
								fillItem.find('td:eq(12)').attr('align','center');
							}
							
						} 
					    

			})
			
		}

		$(document).off('click', '.ShowModalCombine').on('click', '.ShowModalCombine',function(e) {
			// show modal choice budget
			var ev = $(this);
			var No = $(this).closest('tr').find('td:eq(0)').text();
				var html = ''
				var opDepartment = '';
				for (var i = 0; i < DepartementArr.length; i++) {
					opDepartment += '<option value="'+ DepartementArr[i]['Code']  +'" '+''+'>'+DepartementArr[i]['Name2']+'</option>';
				}

				html = 	'<div class = "row" style = "margin-top : 10px" id = "pageContentModalCombine">'+
						'</div>'
						;

				$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Post Budget Item'+'</h4>');
				$('#GlobalModalLarge .modal-body').html(html);
				$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
	          '<button type="button" id="ModalbtnSaveForm2" class="btn btn-success">Save</button>');
				$('#GlobalModalLarge').modal({
				    'show' : true,
				    'backdrop' : 'static'
				});

				ShowPostBudgetCombine(ev,No);	

		})

		function ShowPostBudgetCombine(ev,No)
		{
			var Year = $("#Year").val();
			var url = base_url_js+"budgeting/detail_budgeting_remaining_All";
			var data = {
					    Year : Year,
					};
			var token = jwt_encode(data,'UAP)(*');
			$.post(url,{token:token},function (resultJson) {
				var response = jQuery.parseJSON(resultJson);
				var PostBudgetDepartmentModal = response.data;
				// PostBudgetDepartment
				if (PostBudgetDepartmentModal.length > 0) {
					// PostBudgetDepartment = PostBudgetDepartment.concat(PostBudgetDepartmentModal); 
					var P_result = Filtering_PostBudgetDepartment(PostBudgetDepartment,PostBudgetDepartmentModal);
					PostBudgetDepartment = P_result;
					localStorage.setItem("PostBudgetDepartment", JSON.stringify(PostBudgetDepartment));
				}
				// Filtering PostBudgetDepartmentModal Besar dari kekurangannya
					// check pada variable BudgetRemaining
						var id_budget_left = ev.closest('tr').find('td:eq(2)').find('.PostBudgetItem').attr('id_budget_left');
						var kekurangan = 0;
						for (var i = 0; i < BudgetRemaining.length; i++) {
							var id_budget_left_1 = BudgetRemaining[i]['id_budget_left'];
							if (id_budget_left == id_budget_left_1) {
								kekurangan = BudgetRemaining[i]['RemainingNoFormat'];
								kekurangan = kekurangan * (-1); // to make positif
								break;
							}
						}
						// console.log(PostBudgetDepartmentModal);
						// edit PostBudgetDepartmentModal
							if (kekurangan > 0) {
								var arr = [];
								for (var i = 0; i < PostBudgetDepartmentModal.length; i++) {
									var Cost = PostBudgetDepartmentModal[i]['Value'];
									if (Cost >= kekurangan) {
										// check data PostBudgetDepartmentModal dengan data BudgetRemaining untuk update value /cost datanya
										var ID = PostBudgetDepartmentModal[i]['ID'] 
										for (var j = 0; j < BudgetRemaining.length; j++) {
											var id_budget_left = BudgetRemaining[j]['id_budget_left'];
											if (ID == id_budget_left) {
												PostBudgetDepartmentModal[i]['Value'] = BudgetRemaining[j]['RemainingNoFormat']
											}
										}

										arr.push(PostBudgetDepartmentModal[i]);
									}
								}
								PostBudgetDepartmentModal = arr;
							}
					$("#ModalbtnSaveForm2").attr('kekurangan',kekurangan);		
					$("#ModalbtnSaveForm2").attr('no',No);	
					$("#DepartementPostModalCombine").attr('no',No);		
				var html = htmlPostBudgetDepartmentModal(PostBudgetDepartmentModal);
				$("#pageContentModalCombine").html(html);
				var table = $('#example').DataTable({
			      "data" : PostBudgetDepartmentModal,
			      'columnDefs': [
				      {
				         'targets': 0,
				         'searchable': false,
				         'orderable': false,
				         'className': 'dt-body-center',
				         'render': function (data, type, full, meta){
				             return '<input type="checkbox" name="id[]" value="' + full.ID + '" estvalue="' + full.Value + '" namedepartement = "'+full.NameDepartement+'" departement = "'+full.Departement+'">';
				         }
				      },
				      {
				         'targets': 1,
				         'render': function (data, type, full, meta){
				             return full.PostName+'-'+full.RealisasiPostName+'['+full.NameDepartement+']';
				         }
				      },
				      {
				         'targets': 2,
				         'render': function (data, type, full, meta){
				             return formatRupiah(full.Value);
				         }
				      },
			      ],
			      // 'order': [[1, 'asc']]
				});

				tbl_element = table;

			}).fail(function() {
			  toastr.info('No Result Data'); 
			}).always(function() {
			                
			});
		}

		$(document).off('click', '#ModalbtnSaveForm2').on('click', '#ModalbtnSaveForm2',function(e) {
			// console.log('asd');
			var  chkbox = [];
			var kekurangan_temp = $(this).attr('kekurangan');
			kekurangan_temp = parseInt(kekurangan_temp);
			var No = $(this).attr('no');
			// find in the table
				var tableRow = $("td").filter(function() {
				    return $(this).text() == No;
				}).closest("tr");
				var fillItem = tableRow;
				var ev = fillItem.find('td:eq(12)').find('.ShowModalCombine');
			// var indextr = No - 1;
			// var ev = $("#table_input_pr tbody").eq(indextr).find('td:eq(12)').find('.ShowModalCombine');
			// var fillItem = $("#table_input_pr tbody").eq(indextr);

			// $('input[type="checkbox"]').each(function(){ // multiple combine choice budgeting
			// $('#example tbody').on('each', 'input[type="checkbox"]', function(){
			tbl_element.$('input[type="checkbox"]').each(function(){	
				if ($(this).is(':checked')) {
					var id_budget_left = $(this).val();
					var estvalue = $(this).attr('estvalue');
					var NameDepartement = $(this).attr('namedepartement');
					var Departement = $(this).attr('departement');
					var n = estvalue.indexOf(".");
					if (n >= 0) {
						estvalue = estvalue.substring(0, n);
					}
					// estvalue = estvalue.substring(0, n);
					var row = $(this).closest('tr');
					var PostBudgetItem = row.find('td:eq(1)').text();
					if (estvalue >= kekurangan_temp) {

						var temp = {
							id_budget_left : id_budget_left,
							cost : kekurangan_temp,
							value : PostBudgetItem,
							Departement : Departement,
							NameDepartement : NameDepartement,
							estvalue : estvalue,
						}
						chkbox.push(temp);
						return false;
					}
					else{
						var temp = {
							id_budget_left : id_budget_left,
							cost : estvalue,
							value : PostBudgetItem,
							Departement : Departement,
							NameDepartement : NameDepartement,
							estvalue : estvalue,
						}
						chkbox.push(temp);
						kekurangan_temp = estvalue - kekurangan_temp;
					}
				}
			})

			if (tbl_element.$('input[type="checkbox"]:checked:not(.uniform)').length == chkbox.length) {
				var temp = {
					No : No,
					dt : chkbox,
				};
				PostBudgetDepartmentCombine.push(temp);
				fillItem.find('td:eq(7)').find('.qty').trigger('change');

				// isi kolom Combine Budgeting
				var htmlWr = '';
				for (var i = 0; i < chkbox.length; i++) {
					htmlWr += '<li>'+chkbox[i]['value']+' : '+formatRupiah(chkbox[i]['cost'])+'</li>';
				}
				fillItem.find('td:eq(12)').attr('align','left');
				fillItem.find('td:eq(12)').html(htmlWr);
				$('#GlobalModalLarge').modal('hide');
			}
			else
			{
				toastr.error('Your Budget more than Sub Total','!!!Failed');
			}

			
		})
		
		$(document).off('click', '#SaveBudget').on('click', '#SaveBudget',function(e) {
			var htmltext = $(this).text();
			loading_button('#SaveBudget');
			// Budget Status
				if ($('.BudgetStatus').length) {
					// check Budget status tidak boleh exceeds
						var bool = true;
						$(".BudgetStatus").each(function(){
							if ($(this).val() == 'Exceed') {
								bool = false;
								return false;
							}
						})

						if (!bool) {
							toastr.error('Budget Status having value is Exceed','!!!Error');
							$('#SaveBudget').prop('disabled',false).html(htmltext);
						}
						else
						{
							// ok
							var validation = validation_input();
							var action = $(this).attr('action');
							var PRCode = $(this).attr('prcode');
							PRCode = (PRCode == undefined) ? '' : PRCode;
							if (validation) {
								SubmitPR(PRCode,action,'#SaveBudget');
							}
							else
							{
								$('#SaveBudget').prop('disabled',false).html(htmltext);
							}
							
						}
				}
				else
				{
					toastr.error('Budget Status is required','!!!Error');
					$('#SaveBudget').prop('disabled',false).html(htmltext);
				}
		})


		function validation_input()
		{
			var find = true;
			var Total = 0
			var ppn = $("#ppn").val();
			$(".PostBudgetItem").each(function(){
				var fillItem = $(this).closest('tr');
				var PostBudgetItem = $(this).val();
				if (PostBudgetItem == '') {
					find = false;
					toastr.error("Post Budget Item is required",'!!!Error');
					return false;
				}

				var Item = fillItem.find('td:eq(3)').find('.Item').val();
				if (Item == '') {
					find = false;
					toastr.error("Item is required",'!!!Error');
					return false;
				}

				// find subtotal to check maxlimit
					var SubTotal = fillItem.find('td:eq(9)').find('.SubTotal').val();
					SubTotal = findAndReplace(SubTotal, ".","");
					var Persent = (parseInt(ppn) / 100) * SubTotal;
					SubTotal = parseInt(SubTotal) - parseInt(Persent);
					Total += parseInt(SubTotal);


			})
			
			if (Total > MaxLimit) {
				// var WrMaxLimit = findAndReplace(MaxLimit, ".","");
				toastr.error("You have authorize Max Limit : "+ formatRupiah(MaxLimit),'!!!Error');
				return false;
			}

			$(".BrowseFile").each(function(){
				var IDFile = $(this).attr('id');
				if (!file_validation2(IDFile) ) {
				  $("#SaveBudget").prop('disabled',true);
				  find = false;
				  return false;
				}
			})

			$(".BrowseFileSD").each(function(){
				var IDFile = $(this).attr('id');
				if (!file_validation2(IDFile) ) {
				  $("#SaveBudget").prop('disabled',true);
				  find = false;
				  return false;
				}
			})

			return find;

		}

		function file_validation2(ID_element)
		{
		    var files = $('#'+ID_element)[0].files;
		    var error = '';
		    var msgStr = '';
		    var max_upload_per_file = 4;
		    if (files.length > max_upload_per_file) {
		      msgStr += '1 Document should not be more than 4 Files<br>';

		    }
		    else
		    {
		      for(var count = 0; count<files.length; count++)
		      {
		       var no = parseInt(count) + 1;
		       var name = files[count].name;
		       var extension = name.split('.').pop().toLowerCase();
		       if(jQuery.inArray(extension, ['jpg' ,'png','jpeg','pdf','doc','docx']) == -1)
		       {
		        msgStr += 'File Number '+ no + ' Invalid Type File<br>';
		        //toastr.error("Invalid Image File", 'Failed!!');
		        // return false;
		       }

		       var oFReader = new FileReader();
		       oFReader.readAsDataURL(files[count]);
		       var f = files[count];
		       var fsize = f.size||f.fileSize;
		       // console.log(fsize);

		       if(fsize > 2000000) // 2mb
		       {
		        msgStr += 'File Number '+ no + ' Image File Size is very big<br>';
		        //toastr.error("Image File Size is very big", 'Failed!!');
		        //return false;
		       }
		       
		      }
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

		function SubmitPR(PRCode,Action,ID_element)
		{
			// console.log(PRCode);
			var Year = $("#Year").val();
			var Departement = $("#DepartementPost").val();
			var PPN = $("#ppn").val();
			var Notes = $("#Notes").val();
			var FormInsertDetail = [];
			var form_data = new FormData();
			var PassNumber = 0;
			$(".PostBudgetItem").each(function(){
				var ID_budget_left = $(this).attr('id_budget_left');
				var fillItem = $(this).closest('tr');
				var ID_m_catalog = fillItem.find('td:eq(3)').find('.Item').attr('savevalue');
				var Spec_add = fillItem.find('td:eq(5)').find('.SpecAdd').val();
				var Need = fillItem.find('td:eq(6)').find('.Need').val();
				var Qty = fillItem.find('td:eq(7)').find('.qty').val();
				var UnitCost = fillItem.find('td:eq(8)').find('.UnitCost').val();
				UnitCost = findAndReplace(UnitCost, ".","");
				var No = fillItem.find('td:eq(0)').text();
				var SubTotal = fillItem.find('td:eq(9)').find('.SubTotal').val();
				SubTotal = findAndReplace(SubTotal, ".","");
				var DateNeeded = fillItem.find('td:eq(10)').find('#tgl'+No).val();
				var BudgetStatus = fillItem.find('td:eq(1)').find('.BudgetStatus').val();

				if ( $( '#'+'BrowseFileSD').length ) {
					var UploadFile = $('#'+'BrowseFileSD')[0].files;
					for(var count = 0; count<UploadFile.length; count++)
					{
					 form_data.append("Supporting_documents[]", UploadFile[count]);
					}
				}

				if ( $( '#'+'BrowseFile'+No ).length ) {
					var UploadFile = $('#'+'BrowseFile'+No)[0].files;
					for(var count = 0; count<UploadFile.length; count++)
					{
					 form_data.append("UploadFile"+PassNumber+"[]", UploadFile[count]);
					}
				}

				 var data = {
				 	ID_budget_left : ID_budget_left,
				 	ID_m_catalog : ID_m_catalog,
				 	Spec_add : Spec_add,
				 	Need : Need,
				 	Qty : Qty,
				 	UnitCost : UnitCost,
				 	SubTotal : SubTotal,
				 	DateNeeded : DateNeeded,
				 	BudgetStatus : BudgetStatus,
				 }
				 var token = jwt_encode(data,"UAP)(*");
				 FormInsertDetail.push(token);
				 PassNumber++

			})
			var token = jwt_encode(FormInsertDetail,"UAP)(*");
			form_data.append('token',token);

			form_data.append('Action',Action);

			token = jwt_encode(PRCode,"UAP)(*");
			form_data.append('PRCode',token);

			token = jwt_encode(Year,"UAP)(*");
			form_data.append('Year',token);

			token = jwt_encode(Departement,"UAP)(*");
			form_data.append('Departement',token);

			token = jwt_encode(PPN,"UAP)(*");
			form_data.append('PPN',token);

			token = jwt_encode(Notes,"UAP)(*");
			form_data.append('Notes',token);

			var url = base_url_js + "budgeting/submitpr"
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
			    switch (Action)
			    {
			       case "0":
			       		if (data == '') {
			       			$("#pageContent select").each(function(){
			       				$(this).attr('readonly',true);
			       				$(this).attr('disabled',true);
			       			})
			       			$("#pageContent input").each(function(){
			       				$(this).attr('readonly',true);
			       				$(this).attr('disabled',true);
			       			})
			       			setTimeout(function () {
			       				toastr.error('PRCode cannot to create, Page will be redirect in two seconds');
			       				loading_page("#pageContent");
			       			},2000);
			       			setTimeout(function () {
			       				LoadPage('form');
			       			},2000);

			       		}
			       		else
			       		{
			       			if ($("#p_prcode").length) {
			       				$("#p_prcode").html('PRCode : '+data)
			       			}
			       			else
			       			{
			       				$(".thumbnail").find('.row:first').before('<p style = "color : red" id = "p_prcode">PRCode : '+data+'</p>');
			       			}
			       			
			       			var rowPullright = $(ID_element).closest('.pull-right');
			       			rowPullright.empty();
			       			rowPullright.append('<button class="btn btn-success" id="SaveBudget" action="0" PRCode = "'+data+'">Save to Draft</button>'+ '&nbsp&nbsp'+'<button class="btn btn-primary" id="BtnIssued" action="1" PRCode = "'+data+'">Issued</button>');
			       		}
			       		break;
			       case "1":
			       		if ($("#p_prcode").length) {
			       			$("#p_prcode").html('PRCode : '+data['PRCode']);
			       		}
			       		else
			       		{
			       			$(".thumbnail").find('.row:first').before('<p style = "color : red" id = "p_prcode">PRCode : '+data['PRCode']+'</p>');
			       		}
			       		
			       		var rowPullright = $(ID_element).closest('.pull-right');
			       		rowPullright.empty();
			       		rowPullright.append('<button class="btn btn-default" id="pdfprint" PRCode = "'+data['PRCode']+'"> <i class = "fa fa-file-pdf-o"></i> Print PDF</button>'+ '&nbsp&nbsp'+'<!--<button class="btn btn-default" id="excelprint" PRCode = "'+data['PRCode']+'"><i class = "fa fa-file-excel-o"></i> Print Excel</button>-->');

			       		$('button:not([id="pdfprint"]):not([id="excelprint"]):not([id="btnBackToHome"])').prop('disabled', true);
			       		$(".Detail").prop('disabled', false);
			       		$("input").prop('disabled', true);
			       		$("select").prop('disabled', true);
			       		$("textarea").prop('disabled', true);
			       		$(".input-group-addon").remove();

			       		// update tableData_selected
			       			var js = jQuery.parseJSON(data['JsonStatus']);
			       			JsonStatus = js;
			       			Get_tableData_selected(JsonStatus);

			       		break;
			       case "larry": 
			           alert('Hey');
			       default: 
			           alert('Default case');
			    }

			  },
			  error: function (data) {
			    toastr.error("Connection Error, Please try again", 'Error!!');
			    var nmbtn = '';
			    if (ID_element == '#SaveBudget') {
			    	nmbtn = 'Save to Draf';
			    }
			    else if(ID_element == '#BtnIssued')
			    {
			    	nmbtn = 'Issued';
			    }
			    $(ID_element).prop('disabled',false).html(nmbtn);
			  }
			})

		}

		function Get_tableData_selected(JsonStatus)
		{
			console.log(JsonStatus);
			var TD0 = $("#tableData_selected tbody").find('tr:first').find('td:eq(0)').html();
			var TD1 = $("#tableData_selected tbody").find('tr:first').find('td:eq(1)').html();
			var TD2 = $("#tableData_selected tbody").find('tr:first').find('td:eq(2)').html();
			var TD3 = 'Issued & Approval Process';
			$("#tableData_selected tbody").find('tr:first').find('td:eq(3)').html(TD3);	
			var stTd = 5; // start dari td ke 5 untuk approval
			// JsonStatus = jQuery.parseJSON(JsonStatus);
			for (var i = 0; i < JsonStatus.length; i++) {
				var html = '';
				switch(JsonStatus[i]['Status']) {
				  case 0:
				  case '0':
				   var stjson = '-';
				    break;
				  case 1:
				  case '1':
				    var stjson = '<i class="fa fa-check" style="color: green;"></i>';
				    break;
				  case 2:
				  case '2':
				    var stjson =  '<i class="fa fa-times" aria-hidden="true" style="color: red;"></i>';;
				    break;  
				  default:
				    var stjson = '-';
				}
				html += stjson+'<br>'+'Approver : '+JsonStatus[i]['NameApprovedBy']+'<br>'+'Approve At : '+JsonStatus[i]['ApproveAt'];
				$("#tableData_selected tbody").find('tr:first').find('td:eq('+stTd+')').html(html);	
				stTd++;
			}

			for (var i = 0; i < G_ApproverLength-JsonStatus.length; i++) {
				$("#tableData_selected tbody").find('tr:first').find('td:eq('+stTd+')').html('-');	
				stTd++;
			}
		}

		$(document).off('click', '#BtnIssued').on('click', '#BtnIssued',function(e) {
			loading_button('#BtnIssued');
			// Budget Status
				if ($('.BudgetStatus').length) {
					// check Budget status tidak boleh exceeds
						var bool = true;
						$(".BudgetStatus").each(function(){
							if ($(this).val() == 'Exceed') {
								bool = false;
								return false;
							}
						})

						if (!bool) {
							toastr.error('Budget Status having value is Exceed','!!!Error');
							$('#SaveBudget').prop('disabled',false).html('Save to Draft');
						}
						else
						{
							// ok
							var validation = validation_input();
							var action = $(this).attr('action');
							var PRCode = $(this).attr('PRCode');
							PRCode = (PRCode == undefined) ? 1 : PRCode;
							if (validation) {
								$("#pageContent select").each(function(){
									$(this).attr('readonly',true);
									$(this).attr('disabled',true);
								})
								$("#pageContent input").each(function(){
									$(this).attr('readonly',true);
									$(this).attr('disabled',true);
								})
								if (confirm("Are you sure ?") == true) {
									SubmitPR(PRCode,action,'#BtnIssued');
								}
								else
								{
									$('#BtnIssued').prop('disabled',false).html('Issued');
								}	
							}
							else
							{
								$('#BtnIssued').prop('disabled',false).html('Issued');
							}
							
						}
				}
				else
				{
					toastr.error('Budget Status is required','!!!Error');
					$('#BtnIssued').prop('disabled',false).html('Issued');
				}
		})

		$(document).off('click', '#pdfprint').on('click', '#pdfprint',function(e) {
			var url = base_url_js+'save2pdf/print/prdeparment';
			var PRCode = $(this).attr('prcode');
			data = {
			  PRCode : PRCode,
			}
			var token = jwt_encode(data,"UAP)(*");
			FormSubmitAuto(url, 'POST', [
			    { name: 'token', value: token },
			]);
		})

		$(document).off('click', '#approve').on('click', '#approve',function(e) {
			var representednip = $(this).attr('representednip');
			var representedname = $(this).attr('representedname');
			var confirmadd = '';
			if ( !(representednip == undefined)  ) {
				confirmadd = ' to represented : '+representednip + ' || '+ representedname;
			}

			if (confirm('Are you sure '+confirmadd+' ?')) {
				loading_button('#approve');
				var PRCode = $(this).attr('prcode');
				var useraccess = $(this).attr('useraccess');
				var represented = (confirmadd != '') ? representednip : '';
				var url = base_url_js + 'rest/__approve_pr';
				var data = {
					PRCode : PRCode,
					useraccess : useraccess,
					NIP : "<?php echo $this->session->userdata('NIP') ?>",
					action : 'approve',
					auth : 's3Cr3T-G4N',
					represented : represented,
				}

				var token = jwt_encode(data,"UAP)(*");
				$.post(url,{ token:token },function (resultJson) {
					if (resultJson == '') {
						$(".menuEBudget li").removeClass('active');
						$(".pageAnchor[page='data']").parent().addClass('active');
						LoadPage('data');
					}
					else
					{
						$('#approve').prop('disabled',false).html('<i class="fa fa-handshake-o"> </i> Approve');
					}
				}).fail(function() {

				  // toastr.info('No Result Data');
				  toastr.error('The Database connection error, please try again', 'Failed!!');
				}).always(function() {
				    $('#approve').prop('disabled',false).html('<i class="fa fa-handshake-o"> </i> Approve');
				});
			}
			

		})

		$(document).off('click', '#reject').on('click', '#reject',function(e) {
			var representednip = $(this).attr('representednip');
			var representedname = $(this).attr('representedname');
			var confirmadd = '';
			if ( !(representednip == undefined)  ) {
				confirmadd = ' to represented : '+representednip + ' || '+ representedname;
			}
			if (confirm('Are you sure '+confirmadd+' ?')) {
				// loading_button('#reject');
				var PRCode = $(this).attr('prcode');
				var useraccess = $(this).attr('useraccess');
				var represented = (confirmadd != '') ? representednip : '';
				// show modal insert reason
				$('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Please Input Reason ! </b> <br>' +
				    '<input type = "text" class = "form-group" id ="NoteDel" style="margin: 0px 0px 15px; height: 30px; width: 329px;" maxlength="30"><br>'+
				    '<button type="button" id="confirmYes" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
				    '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
				    '</div>');
				$('#NotificationModal').modal('show');

				$("#confirmYes").click(function(){
					var NoteDel = $("#NoteDel").val();
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

					var url = base_url_js + 'rest/__approve_pr';
					var data = {
						PRCode : PRCode,
						useraccess : useraccess,
						NIP : "<?php echo $this->session->userdata('NIP') ?>",
						action : 'reject',
						auth : 's3Cr3T-G4N',
						NoteDel : NoteDel,
						represented : represented,
					}

					var token = jwt_encode(data,"UAP)(*");
					$.post(url,{ token:token },function (resultJson) {
						if (resultJson == '') {
							$(".menuEBudget li").removeClass('active');
							$(".pageAnchor[page='data']").parent().addClass('active');
							LoadPage('data');
						}
						else
						{
							// $('#reject').prop('disabled',false).html('<i class="fa fa-handshake-o"> </i> Approve');
						}
						$('#NotificationModal').modal('hide');
					}).fail(function() {
					  // toastr.info('No Result Data');
					  toastr.error('The Database connection error, please try again', 'Failed!!');
					  $('#NotificationModal').modal('hide');
					}).always(function() {
					    // $('#reject').prop('disabled',false).html('<i class="fa fa-handshake-o"> </i> Approve');
					    $('#NotificationModal').modal('hide');
					});
				})	
			}
			

		})
	}); // exit document Function
</script>
