<div class="row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "dtContent">
	
</div>
<script type="text/javascript">
	var ClassDt = {
		BudgetRemaining : [],
		PRCodeVal : "<?php echo $PRCodeVal ?>",
		Year : "<?php echo $Year ?>",
		Departement : "<?php echo $Departement ?>",
		RuleAccess : [],
		PostBudgetDepartment : [],
	};

	var S_Table_example_budget = '';
	var S_Table_example_catalog = '';
	var S_Table_example_combine = '';

	$(document).ready(function() {
		LoadFirstLoad();
	})

	function LoadFirstLoad()
	{
		// check Rule for Input
		var url = base_url_js+"budgeting/checkruleinput";
		var data = {
			NIP : NIP,
		};
		if (ClassDt.PRCodeVal != '') {
			data = {
				NIP : NIP,
				Departement : ClassDt.Departement,
				PRCodeVal : ClassDt.PRCodeVal,
			};
		}
		var token = jwt_encode(data,"UAP)(*");
		$.post(url,{ token:token },function (resultJson) {
			var response = jQuery.parseJSON(resultJson);

			var access = response['access'];
			if (access.length > 0) {
				ClassDt.RuleAccess = response;
				load_htmlPR();
			}
			else
			{
				$("#pageContent").empty();
				$("#pageContent").html('<h2 align = "center">Your not authorize these modul</h2>');
			}
			
		})
	}

	function load_htmlPR()
	{
		// check data edit or new
		if (ClassDt.PRCodeVal != '') {
			// edit
			var PRCode = ClassDt.PRCodeVal;
			var url = base_url_js+'budgeting/GetDataPR';
			var data = {
			    PRCode : PRCode,
			};
			var token = jwt_encode(data,"UAP)(*");
			$.post(url,{ token:token },function (data_json) {
				var response = jQuery.parseJSON(resultJson);
				console.log(response);
			}).fail(function() {
			  toastr.info('No Result Data'); 
			}).always(function() {
			                
			});	
		}
		else
		{
			// Load Budget Department
			var Year = ClassDt.Year;
			var Departement = ClassDt.Departement;
			var url = base_url_js+"budgeting/detail_budgeting_remaining";
			var data = {
					    Year : Year,
						Departement : Departement,
					};
			var token = jwt_encode(data,'UAP)(*');
			$.post(url,{token:token},function (resultJson) {
				var response = jQuery.parseJSON(resultJson);
				ClassDt.PostBudgetDepartment = response.data;
				localStorage.setItem("PostBudgetDepartment", JSON.stringify(ClassDt.PostBudgetDepartment));
				// new
				makeDomAwal();
			}).fail(function() {
			  toastr.info('No Result Data'); 
			}).always(function() {
			                
			});
			
		}
	}

	function makeDomAwal()
	{
		var html = '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;">';
			html += '<div class="col-md-4">'+
						'<p id = "labelPeriod">Period : <label>'+ClassDt.Year+'/'+(parseInt(ClassDt.Year)+1 )+'</label></p>'+
						'<p id = "labelDepartment">Department : '+DivSessionName+'</p>'+
						'<p id = "labelPrcode"></p>'+
						'<p id = "Status"></p>'+
					'</div>'+
					'<div class="col-md-4">'+
						'<div class="well">'+
							'<div style="margin-top: -15px">'+
								'<label>Budget Remaining</label>'+
							'</div>'+
							'<div id = "Page_Budget_Remaining">'+
								''+
							'</div>'+
						'</div>'+
					'</div>';
			html += '</div>';

        var htmlBtnAdd = '<div class = "row" style = "margin-left : 0px">'+
							'<div class = "col-md-3">'+
								'<button type="button" class="btn btn-default btn-add-pr"> <i class="icon-plus"></i> Add</button>'+
							'</div>'+
						'</div>';
		var htmlInputPR = '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "Page_PR">'+
							'<div class = "col-md-12">'+
								'<div class="table-responsive">'+
									'<table class="table table-bordered tableData" id ="table_input_pr">'+
									'<thead>'+
									'<tr>'+
										'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Budget</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Catalog</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Desc</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Spec+</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Need</th>'+
			                            '<th width = "4%" style = "text-align: center;background: #20485A;color: #FFFFFF;width : 78px;">Qty</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Cost</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 78px;">PPH(%)</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;width : 150px;">Sub Total</th>'+
			                            '<th width = "150px" style = "text-align: center;background: #20485A;color: #FFFFFF;">Date Needed</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">File</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Combine Budget</th>'+
			                            '<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Action</th>'+
									'</tr></thead>'+
									'<tbody></tbody></table>'+
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

		var htmlInputFooter = '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "Page_Footer">'+
							Notes+Supporting_documents+
						  '</div>';

		var htmlApproval = '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "Page_Approval">'+
						  '</div>';	

		var htmlButton = '<div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 5px;" id = "Page_Button">'+
						  '</div>';

		$('#dtContent').html(html+htmlBtnAdd+htmlInputPR+htmlInputFooter+htmlApproval+htmlButton);	
		MakeButton();
	}

	function MakeButton()
	{
		var dt = ClassDt.RuleAccess;
		if (ClassDt.PRCodeVal != '') { 
			// edit
		}
		else
		{
			var html = '<div class = "col-md-6 col-md-offset-6" align = "right">'+
						'<button class = "btn btn-success" id = "SaveSubmit" id_pr_create = "" prcode = "" action = "1">Submit</button>'+
					   '</div>';
			var r_access = dt['access'];
			var rule = dt['rule'];
			// allow access dengan ID_m_userrole: "1"
			var bool = false;
			for (var i = 0; i < r_access.length; i++) {
				var ID_m_userrole = r_access[i].ID_m_userrole;
				// search rule Entry = 1
				for (var j = 0; j < rule.length; j++) {
					var ID_m_userrole_ = rule[j].ID_m_userrole;
					if (ID_m_userrole == ID_m_userrole_) {
						var Entry = rule[j].Entry
						if (Entry == 1) {
							bool = true;
							break;
						}
					}
				}
			}

			if (bool) {
				$('#Page_Button').html(html);
			}
			else
			{
				// check rule entry
				$('.btn-add-pr,input[type="file"]').prop('disabled',true);
			}		   
		}
		
	}

	$(document).off('click', '.btn-add-pr').on('click', '.btn-add-pr',function(e) {
		// before adding row lock all input in last tr
		var row = $('#table_input_pr tbody tr:last');
		row.find('td').find('input,select,button:not(.Detail),textarea').prop('disabled',true);
		row.find('td:eq(13)').find('button').prop('disabled',false);
		AddingTable();
	})

	$(document).off('click', '.btn-delete-item').on('click', '.btn-delete-item',function(e) {
		var tr = $(this).closest('tr');
		tr.remove();
		MakeAutoNumbering();
		var row = $('#table_input_pr tbody tr:last');
		row.find('td').find('input:not(.UnitCost):not(.SubTotal),select,button,textarea').prop('disabled',false);
		row.find('td:eq(13)').find('button').prop('disabled',false);
		__BudgetRemaining(); 
	})	

	function AddingTable()
	{
		action = '<td><button type="button" class="btn btn-danger btn-delete btn-delete-item"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button></td>';
		var html = '<tr>'+
					'<td></td>'+
					'<td>'+
						'<div class="input-group">'+
							'<input type="text" class="form-control PostBudgetItem" readonly>'+
							'<span class="input-group-btn">'+
								'<button class="btn btn-default SearchPostBudget" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>'+
							'</span>'+
						'</div>'+
						'<label class = "lblBudget"></label>'+
					'</td>'+
					'<td>'+
						'<div class="input-group">'+
							'<input type="text" class="form-control Item" readonly>'+
							'<span class="input-group-btn">'+
								'<button class="btn btn-default SearchItem" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>'+
							'</span>'+
						'</div>'+
						'<label class = "lblCatalog"></label>'+
					'</td>'+
					'<td><button class = "btn btn-primary Detail">Detail</button></td>'+
					'<td>'+
						'<textarea class = "form-control SpecAdd" rows = "2"></textarea>'+
					'</td>'+
					'<td>'+
						'<textarea class = "form-control Need" rows = "2"></textarea>'+
					'</td>'+
					'<td><input type="number" min = "1" class="form-control qty"  value="1" disabled></td>'+
					'<td><input type="text" class="form-control UnitCost" disabled></td>'+
					'<td><input type="number" class="form-control PPH" value = "10"></td>'+
					'<td><input type="text" class="form-control SubTotal" disabled value = "0"></td>'+
					'<td>'+
						'<div class="input-group input-append date datetimepicker">'+
                            '<input data-format="yyyy-MM-dd" class="form-control" type=" text" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
                            '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
                		'</div>'+
                	'</td>'+
                	'<td><input type="file" data-style="fileinput" class = "BrowseFile" multiple accept="image/*,application/pdf" style = "width : 97px;"></td>'+
                	'<td>No</td>'+
                	action
                '</tr>';
        $('#table_input_pr tbody').append(html);

        MakeAutoNumbering();        	
	}

	function MakeAutoNumbering()
	{
		var no = 1;
		$("#table_input_pr tbody tr").each(function(){
			var a = $(this);
			a.find('td:eq(0)').html(no);
			no++;
		})
	}

	$(document).off('click', '.SearchPostBudget').on('click', '.SearchPostBudget',function(e) {
		var ev = $(this);
		var dt = ClassDt.PostBudgetDepartment;
		// show all Budget yang memiliki nilai besar dari 0
		dt = __Selection_BudgetDepartment(dt);	
		var html = '';
		html ='<div class = "row">'+
				'<div class = "col-md-12">'+
					'<table id="example_budget" class="table table-bordered display select" cellspacing="0" width="100%">'+
           '<thead>'+
              '<tr>'+
                 '<th>No</th>'+
                 '<th>Post Budget Item</th>'+
                 '<th>Remaining</th>'+
              '</tr>'+
           '</thead>'+
      '</table></div></div>';

		$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Budget'+'</h4>');
		$('#GlobalModalLarge .modal-body').html(html);
		$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>');
		$('#GlobalModalLarge').modal({
		    'show' : true,
		    'backdrop' : 'static'
		});

		var table = $('#example_budget').DataTable({
		      "data" : dt,
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
			             return full.NameHeadAccount+'-'+full.RealisasiPostName;
			         }
			      },
			      {
			         'targets': 2,
			         'render': function (data, type, full, meta){
			             return formatRupiah(full.Value-full.Using);
			         }
			      },
		      ],
		      'createdRow': function( row, data, dataIndex ) {
		      		$(row).attr('CodePost', data.CodePost);
		      		$(row).attr('CodeHeadAccount', data.CodeHeadAccount);
		      		$(row).attr('CodePostRealisasi', data.CodePostRealisasi);
		      		$(row).attr('money', (data.Value - data.Using) );
		      		$(row).attr('id_budget_left', data.ID);
		      		$(row).attr('NameHeadAccount', data.NameHeadAccount);
		      		$(row).attr('RealisasiPostName', data.RealisasiPostName);
		      },
		      // 'order': [[1, 'asc']]
		});

		table.on( 'order.dt search.dt', function () {
		        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
		            cell.innerHTML = i+1;
		        } );
		    } ).draw();

		S_Table_example_budget = table;

		S_Table_example_budget.on( 'click', 'tr', function (e) {
			var row = $(this);
			var CodePost = row.attr('CodePost');
			var CodeHeadAccount = row.attr('CodeHeadAccount');
			var CodePostRealisasi = row.attr('CodePostRealisasi');
			var money = row.attr('money');
			var id_budget_left = row.attr('id_budget_left');
			var NameHeadAccount = row.attr('NameHeadAccount');
			var RealisasiPostName = row.attr('RealisasiPostName');
			var fillItem = ev.closest('tr');
			fillItem.find('td:eq(1)').find('.PostBudgetItem').val(RealisasiPostName);
			fillItem.find('td:eq(1)').find('.lblBudget').html(RealisasiPostName);
			fillItem.find('td:eq(1)').find('.PostBudgetItem').attr('id_budget_left',id_budget_left);
			fillItem.find('td:eq(1)').find('.PostBudgetItem').attr('remaining',money);
			fillItem.find('td:eq(6)').find('.qty').trigger('change');
			$('#GlobalModalLarge').modal('hide');
		} );
	})

	function __Selection_BudgetDepartment(dt,Min = 0)
	{
		var arr =[];
		for (var i = 0; i < dt.length; i++) {
			var v = parseInt(dt[i].Value) - parseInt(dt[i].Using);
			if (v > Min) {
				arr.push(dt[i]);
			}
		}
		return arr;
	}

	function __Selection_OneHeadAccount(dt,G_PostBudgetItem)
	{

		var arr =[];
		var id_budget_left = G_PostBudgetItem;
		for (var i = 0; i < dt.length; i++) {
			var id_budget_left_ = dt[i].ID;
			if (id_budget_left == id_budget_left_) {
				var CodeHeadAccount = dt[i].CodeHeadAccount;
				for (var j = 0; j < dt.length; j++) {
					var CodeHeadAccount_ = dt[j].CodeHeadAccount;
					if (CodeHeadAccount == CodeHeadAccount_) {
						arr.push(dt[j]);
					}
				}
				break;
			}
		}

		return arr;
	}

	$(document).off('click', '.SearchItem').on('click', '.SearchItem',function(e) {
		var ev = $(this);
		var html = '';
			html ='<div class = "row">'+
					'<div class = "col-md-12">'+
						'<table id="example_catalog" class="table table-bordered display select" cellspacing="0" width="100%">'+
               '<thead>'+
                  '<tr>'+
                     '<th>No</th>'+
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
			$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>');
			$('#GlobalModalLarge').modal({
			    'show' : true,
			    'backdrop' : 'static'
			});

			var url = base_url_js+'rest/Catalog/__Get_Item';
			var data = {
				action : 'choices',
				auth : 's3Cr3T-G4N',
				department : DivSession,
				approval : 1,
			};
		    var token = jwt_encode(data,"UAP)(*");
			var table = $('#example_catalog').DataTable({
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
			             return '';
			         }
			      }],
			      'createdRow': function( row, data, dataIndex ) {
			      		$(row).attr('id_m_catalog', data[6]);
			      		$(row).attr('estprice', data[7]);
			      	
			      },
			      // 'order': [[1, 'asc']]
			   });

		table.on( 'order.dt search.dt', function () {
		        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
		            cell.innerHTML = i+1;
		        } );
		    } ).draw();
		S_Table_example_catalog = table;

		S_Table_example_catalog.on( 'click', 'tr', function (e) {
			var row = $(this);
			var fillItem = ev.closest('tr');
			var id_m_catalog = row.attr('id_m_catalog');
			var estprice = row.attr('estprice');
			var n = estprice.indexOf(".");
			estprice = estprice.substring(0, n);

			var Item = row.find('td:eq(1)').text();
			var Desc = row.find('td:eq(2)').text();
			var Est = row.find('td:eq(3)').text();
			var Photo = row.find('td:eq(4)').html();
			var DetailCatalog =  row.find('td:eq(5)').html();
			var arr = Item+'@@'+Desc+'@@'+Est+'@@'+Photo+'@@'+DetailCatalog;
			
			fillItem.find('td:eq(2)').find('.Item').val(Item);
			fillItem.find('td:eq(2)').find('.lblCatalog').html(Item);
			fillItem.find('td:eq(2)').find('.Item').attr('id_m_catalog',id_m_catalog);
			fillItem.find('td:eq(2)').find('.Item').attr('estprice',estprice);
			fillItem.find('td:eq(3)').find('.Detail').attr('data',arr);
			fillItem.find('td:eq(7)').find('.UnitCost').val(estprice);
			fillItem.find('td:eq(7)').find('.UnitCost').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			fillItem.find('td:eq(7)').find('.UnitCost').maskMoney('mask', '9894');
			fillItem.find('td:eq(6)').find('.qty').prop('disabled', false);
			if (estprice == 0) {
				fillItem.find('td:eq(7)').find('.UnitCost').prop('disabled', false);
			}

			fillItem.find('td:eq(6)').find('.qty').trigger('change');
			$('#GlobalModalLarge').modal('hide');
		} );    	
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

	$(document).off('keyup', '.UnitCost').on('keyup', '.UnitCost',function(e) {
		var tr = $(this).closest('tr');
		CountSubTotal_table(tr);
	})	

	$(document).off('change', '.qty').on('change', '.qty',function(e) {
		var tr = $(this).closest('tr');
		CountSubTotal_table(tr);
	})

	$(document).off('change', '.PPH').on('change', '.PPH',function(e) {
		var tr = $(this).closest('tr');
		CountSubTotal_table(tr);
	})

	function CountSubTotal_table(tr)
	{
		var qty = tr.find('.qty').val();
		qty = findAndReplace(qty, ".","");
		var UnitCost = tr.find('.UnitCost').val();
		UnitCost = findAndReplace(UnitCost, ".","");
		var hitung = qty * UnitCost;
		var PPH = (tr.find('.PPH').val() / 100 ) * hitung;
		hitung = hitung + PPH;
		tr.find('.SubTotal').val(hitung);
		tr.find('.SubTotal').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
		tr.find('.SubTotal').maskMoney('mask', '9894');
		__BudgetRemaining(); 
	}

	function __BudgetRemaining()
	{
		ClassDt.BudgetRemaining = [];
		var Budget = [];
		var Budget =  JSON.parse(localStorage.getItem("PostBudgetDepartment"));
		var GrandTotal = 0;
		
		var BudgetRemaining_arr = [];
		$('.PostBudgetItem').each(function(){
			var arr = [];
			var tr = $(this).closest('tr');
			var id_budget_left =  $(this).attr('id_budget_left');
			var SubTotal = tr.find('.SubTotal').val();
			SubTotal = findAndReplace(SubTotal, ".","");
			GrandTotal = parseInt(GrandTotal) + parseInt(SubTotal);
			
			// check Combine
			var LiCombine = tr.find('.liCombine').find('li');
			if (LiCombine.length) {
				var less = tr.find('.SearchPostBudget_Combine').attr('less');
				less = Math.abs(less);
				LiCombine.each(function(){
					var id_budget_left_com = $(this).attr('id_budget_left');
					for (var i = 0; i < Budget.length; i++) {
						var id_budget_left_ = Budget[i].ID;
						if (id_budget_left_com == id_budget_left_) {
							var Remaining = Budget[i].Value - Budget[i].Using;
							if (less > Remaining) {
								var Using = Remaining;
							}
							else
							{
								var Using = less;
							}
							less = parseInt(less) - parseInt(Remaining);
							var temp = {
								id_budget_left : id_budget_left_com,
								Using : Using,
							}
							arr.push(temp);
							break;
						}
						
					}
				})

				for (var i = 0; i < Budget.length; i++) {
					var id_budget_left_ = Budget[i].ID;
					if (id_budget_left_ == id_budget_left) {
						var Remaining = Budget[i].Value - Budget[i].Using;
						SubTotal = Remaining;
						break;
					}
				}
			}
			else
			{
				for (var i = 0; i < Budget.length; i++) {
					var id_budget_left_ = Budget[i].ID;
					if (id_budget_left_ == id_budget_left) {
						var Remaining = Budget[i].Value - Budget[i].Using;
						// show search combine seperti Post Budget Department
						var less = parseInt(Remaining) - parseInt(SubTotal);
						if (SubTotal > Remaining) {
							var InputCombine = '<button class="btn btn-default SearchPostBudget_Combine" type="button" less = "'+less+'"><i class="fa fa-search" aria-hidden="true"></i></button>';
							tr.find('td:eq(12)').html(InputCombine);
						}
						else
						{
							tr.find('td:eq(12)').html('No');
						}
						break;
					}
				}
			}


			var temp = {
				id_budget_left : id_budget_left,
				Using : SubTotal,
			}
			arr.push(temp);

			for (var i = 0; i < Budget.length; i++) {
				var id_budget_left_ = Budget[i].ID;
				var Using = Budget[i].Using;
				var bool = false;
				for (var j = 0; j < arr.length; j++) {
					var id_budget_left = arr[j].id_budget_left;
					if (id_budget_left == id_budget_left_) {
						Using = parseInt(Using) + parseInt(arr[j].Using);
						bool = true;
					}
				}
				Budget[i].Using = Using;
				if (bool) { // jika Post Budget selected
					// check Budget Remaining already exist
					var bool2 = true;
					for (var j = 0; j < BudgetRemaining_arr.length; j++) {
						id_budget_left_re = BudgetRemaining_arr[j].ID;
						if (id_budget_left_ == id_budget_left_re) {
							// Update Using
							BudgetRemaining_arr[j].Using = Budget[i].Using;
							bool2 = false;
							break;
						}
					}

					if (bool2) {
						BudgetRemaining_arr.push(Budget[i]);
					}
					
				}
			}
		})
		
		ClassDt.PostBudgetDepartment = Budget;
		// localStorage.setItem("PostBudgetDepartment", JSON.stringify(ClassDt.PostBudgetDepartment));
		ClassDt.BudgetRemaining = BudgetRemaining_arr;
		MakeTableRemaining();

		// write Grand total
		$('#phtmltotal').html('Total : '+formatRupiah(GrandTotal));
	}

	function MakeTableRemaining()
	{
		$("#Page_Budget_Remaining").empty();
		var BudgetRemaining = ClassDt.BudgetRemaining;
		var html = '<div class = "row">'+
						'<div class = "col-md-12">'+
						'<div style="overflow : auto;max-height : 200px;">'+
							'<table class="table table-bordered tableData" id ="tableData3">'+
								'<thead>'+
									'<tr>'+
										'<th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>'+
										'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Budget</th>'+
										'<th style = "text-align: center;background: #20485A;color: #FFFFFF;">Remaining</th>'+
									'</tr>'+
								'</thead><tbody>';
									
		for (var i = 0; i < BudgetRemaining.length; i++) {
			var No = i + 1;
			html += '<tr>'+
						'<td>'+No+'</td>'+
						'<td>'+BudgetRemaining[i].NameHeadAccount+'-'+BudgetRemaining[i].RealisasiPostName+'</td>'+
						'<td>'+formatRupiah(BudgetRemaining[i].Value - BudgetRemaining[i].Using)+'</td>'+
					'</tr>';	
		}

		html += '</tbody>'+
				'</table>'+
				'</div>'+
				'</div></div>';		

		$("#Page_Budget_Remaining").html(html);
	}

	$(document).off('click', '.SearchPostBudget_Combine').on('click', '.SearchPostBudget_Combine',function(e) {
		var ev = $(this);
		var dt = ClassDt.PostBudgetDepartment;
		var less = $(this).attr('less');
		less = Math.abs(less);

		// combine in one head account
			var tr = $(this).closest('tr');
			var G_PostBudgetItem = tr.find('.PostBudgetItem').attr('id_budget_left');
			dt = __Selection_OneHeadAccount(dt,G_PostBudgetItem);

		dt = __Selection_BudgetDepartment(dt);
		var html = '';
		html ='<div class = "row">'+
				'<div class = "col-md-12">'+
					'<table id="example_budget_combine" class="table table-bordered display select" cellspacing="0" width="100%">'+
           '<thead>'+
              '<tr>'+
                 '<th>No</th>'+
                 '<th>Post Budget Item</th>'+
                 '<th>Remaining</th>'+
              '</tr>'+
           '</thead>'+
      '</table></div></div>';

		$('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Budget'+'</h4>');
		$('#GlobalModalLarge .modal-body').html(html);
		$('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
		              '<button type="button" id="ModalbtnSaveForm_Combine" class="btn btn-success">Save</button>');
		$('#GlobalModalLarge').modal({
		    'show' : true,
		    'backdrop' : 'static'
		});

		var table = $('#example_budget_combine').DataTable({
		      "data" : dt,
		      'columnDefs': [
			      {
			         'targets': 0,
			         'searchable': false,
			         'orderable': false,
			         'className': 'dt-body-center',
			         'render': function (data, type, full, meta){
			             return '<input type="checkbox" name="id[]" value="' + full.ID + '" estvalue="' + (full.Value-full.Using)+'">';
			         }
			      },
			      {
			         'targets': 1,
			         'render': function (data, type, full, meta){
			             return full.NameHeadAccount+'-'+full.RealisasiPostName;
			         }
			      },
			      {
			         'targets': 2,
			         'render': function (data, type, full, meta){
			             return formatRupiah(full.Value-full.Using);
			         }
			      },
		      ],
		      'createdRow': function( row, data, dataIndex ) {
		      		$(row).attr('CodePost', data.CodePost);
		      		$(row).attr('CodeHeadAccount', data.CodeHeadAccount);
		      		$(row).attr('CodePostRealisasi', data.CodePostRealisasi);
		      		$(row).attr('money', (data.Value - data.Using) );
		      		$(row).attr('id_budget_left', data.ID);
		      		$(row).attr('NameHeadAccount', data.NameHeadAccount);
		      		$(row).attr('RealisasiPostName', data.RealisasiPostName);
		      },
		      // 'order': [[1, 'asc']]
		});

		S_Table_example_combine = table;

		$(document).off('click', '#ModalbtnSaveForm_Combine').on('click', '#ModalbtnSaveForm_Combine',function(e) {
			var checkboxArr = [];
			var tot_combine = 0;
			var bool = true;
			S_Table_example_combine.$('input[type="checkbox"]').each(function(){
			  if(this.checked){
			     var tr = $(this).closest('tr');
			     var CodePost = tr.attr('CodePost');
			     var CodeHeadAccount = tr.attr('CodeHeadAccount');
			     var CodePostRealisasi = tr.attr('CodePostRealisasi');
			     var money = tr.attr('money');
			     var id_budget_left = tr.attr('id_budget_left');
			     var NameHeadAccount = tr.attr('NameHeadAccount');
			     var RealisasiPostName = tr.attr('RealisasiPostName');
			     var temp = {
			     	RealisasiPostName : RealisasiPostName,
			     	id_budget_left : id_budget_left,
			     	money : money,
			     }

			     checkboxArr.push(temp);
			     tot_combine = parseInt(tot_combine)+parseInt(money);
			     if (tot_combine >= less && bool === true ) {
			     	bool = false;
			     }
			     else
			     {
			     	bool = true;
			     }

			  }

			}); // exit each function
			
			if (!bool) {
				  var td = ev.closest('td');
				  // check div exist
				  var aa = td.find('.liCombine');
				  if (aa.length) {
				  	aa.remove();
				  }

				 var InputLi = '<ul class = "liCombine" style = "margin-left : -21px;">';
				 var less_ = less;
				 for (var i = 0; i < checkboxArr.length; i++) {
				 	var mon = checkboxArr[i].money;
				 	var Remaining_ = mon;
				 	if (less_ > Remaining_) {
				 		var Subsidi = mon;
				 	}
				 	else
				 	{
				 		var Subsidi = less_;
				 	}
				 	less_ = parseInt(less_) - parseInt(Remaining_);
				 	InputLi += '<li id_budget_left = "'+checkboxArr[i].id_budget_left+'" money = "'+checkboxArr[i].money+'" subsidi = "'+Subsidi+'">'+checkboxArr[i].RealisasiPostName+'</li>';
				  }
					 InputLi += '</ul>';
					 td.append(InputLi);
					 td.attr('style','width : 150px;');

				$('#GlobalModalLarge').modal('hide');
				__BudgetRemaining();	
			}
			else
			{
				if (tot_combine > less) {
					toastr.error('Excess Budget','!!!Failed');
				}
				else
				{
					toastr.error('Insufficient Budget','!!!Failed');
				}
				
			}

		})
	})
	
	function __CekBudgetRemaining()
	{
		var bool = true;
		var dt = ClassDt.BudgetRemaining;
		for (var i = 0; i < dt.length; i++) {
			var v = parseInt(dt[i].Value) - parseInt(dt[i].Using);
			if (v < 0) {
				bool = false;
				toastr.error("Budget Remaining cannot be less than 0",'!!!Error');
				break;
			}
		}

		return bool;
	}
	
	$(document).off('click', '#SaveSubmit').on('click', '#SaveSubmit',function(e) {
		var htmltext = $(this).text();
		if (confirm("Are you sure ?") == true) {
			loading_button('#SaveSubmit');
			/*
				1.Cek Budget Remaining tidak boleh ada yang kurang dari 0
				2.Validation Inputan
				3.Validation Auth Max Limit
				4.Validation File Upload
			*/

			var CekBudgetRemaining = __CekBudgetRemaining();
			var validation = validation_input();
			var PRCode = $(this).attr('prcode');
			var id_pr_create = $(this).attr('id_pr_create');
			var action = $(this).attr('action');
			if (validation) {
				SubmitPR(PRCode,id_pr_create,action,'#SaveSubmit');
				// $('#SaveSubmit').prop('disabled',false).html(htmltext);
			}
			else
			{
				$('#SaveSubmit').prop('disabled',false).html(htmltext);
			}
		}

	})

	function __GetMaxLimit()
	{
		var MaxLimit = 0;
		var dt = ClassDt.RuleAccess;
		var access = dt.access;
		var rule = dt.rule;
		for (var i = 0; i < access.length; i++) {
			var NIP_ = access[i].NIP;
			if (NIP_ == NIP) {
				var ID_m_userrole = access[i].ID_m_userrole;
				// get BudgetRemaining
				var dt2 = ClassDt.BudgetRemaining;
				var temp = [];
				for (var j = 0; j < dt2.length; j++) {
					var CodePost = dt2[j].CodePost;
					// hitung paling panjang approval jika ada 2 atau lebih dari Budget Category
						var C_ = 0;
						var IndexID = 0;
						for (var k = 0; k < rule.length; k++) {
							var CodePost_ = rule[k].CodePost;
							var ID_m_userrole_ = rule[k].ID_m_userrole;
							var Approved = rule[k].Approved;
							if (CodePost == CodePost_ && ID_m_userrole == ID_m_userrole_) {
								C_++;
								IndexID = k;
							}
						}

						var temp2 = {
							CodePost : CodePost,
							Count : C_,
							MaxLimit : rule[IndexID].MaxLimit,
						}

						temp.push(temp2);
				}

				
				// var temp = [
				// 	{
				// 		MaxLimit : 20000,
				// 	},

				// 	{
				// 		MaxLimit : 100000,
				// 	},
				// ]
				// console.log(temp);

				// ambil nilai temp paling tinggi
				MaxLimit = 0;
				for (var j = 0; j < temp.length; j++) {
					// var Count = temp[j].Count;
					var MaxLimit_ = parseInt(temp[j].MaxLimit);
					for (var k = j+1; k < temp.length; k++) {
						// var Count_ = temp[k].Count;
						var MaxLimit__ = parseInt(temp[k].MaxLimit);
						if (MaxLimit__ >= MaxLimit_) {
							// j = k-1;
							break;
						}
						else
						{
							// j = k - 1;
						}

						j = k;
					}

					MaxLimit = MaxLimit_;
				}
				break;
			}
		}

		return MaxLimit;
	}

	function validation_input()
	{
		var find = true;
		var Total = 0
		var aa = $(".PostBudgetItem").length;
		if (aa == 0) {
			toastr.error("Post Budget Item is required",'!!!Error');
		}
		else
		{
			$(".PostBudgetItem").each(function(){
				var fillItem = $(this).closest('tr');
				var PostBudgetItem = $(this).val();
				if (PostBudgetItem == '') {
					find = false;
					toastr.error("Post Budget Item is required",'!!!Error');
					return false;
				}

				var Item = fillItem.find('td:eq(2)').find('.Item').val();
				if (Item == '') {
					find = false;
					toastr.error("Item is required",'!!!Error');
					return false;
				}

				// find subtotal to check maxlimit
					var SubTotal = fillItem.find('.SubTotal').val();
					SubTotal = findAndReplace(SubTotal, ".","");
					SubTotal = parseInt(SubTotal);
					Total += parseInt(SubTotal);


			})

			var MaxLimit = __GetMaxLimit();

			if (Total > MaxLimit) {
				toastr.error("You have authorize Max Limit : "+ formatRupiah(MaxLimit),'!!!Error');
				find = false;
				return false;
			}

			$(".BrowseFile").each(function(){
				var IDFile = $(this).attr('id');
				var ev = $(this);
				if (!file_validation2(ev) ) {
				  $("#SaveSubmit").prop('disabled',true);
				  find = false;
				  return false;
				}
			})

			$(".BrowseFileSD").each(function(){
				var IDFile = $(this).attr('id');
				var ev = $(this);
				if (!file_validation2(ev) ) {
				  $("#SaveSubmit").prop('disabled',true);
				  find = false;
				  return false;
				}
			})
		}
		return find;

	}

	function file_validation2(ev)
	{
	    var files = ev[0].files;
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

	function SubmitPR(PRCode,id_pr_create,Action,ID_element)
	{
		var Year = ClassDt.Year;
		var Departement = ClassDt.Departement;
		var Notes = $("#Notes").val();
		var FormInsertDetail = [];
		var form_data = new FormData();
		var PassNumber = 0;
		$(".PostBudgetItem").each(function(){
			var FormInsertCombine = [];
			var ID_budget_left = $(this).attr('id_budget_left');
				var fillItem = $(this).closest('tr');
			var ID_m_catalog = fillItem.find('.Item').attr('id_m_catalog');
			var Spec_add = fillItem.find('.SpecAdd').val();
			var Need = fillItem.find('.Need').val();
			var Qty = fillItem.find('.qty').val();
			var UnitCost = fillItem.find('.UnitCost').val();
			UnitCost = findAndReplace(UnitCost, ".","");
			var PPH = fillItem.find('.PPH').val();
			var SubTotal = fillItem.find('.SubTotal').val();
			SubTotal = findAndReplace(SubTotal, ".","");
			var DateNeeded = fillItem.find('.datetimepicker').find('input').val();

			if ( $( '#'+'BrowseFileSD').length ) {
				var UploadFile = $('#'+'BrowseFileSD')[0].files;
				for(var count = 0; count<UploadFile.length; count++)
				{
				 form_data.append("Supporting_documents[]", UploadFile[count]);
				}
			}

			if ( fillItem.find('.BrowseFile').length ) {
				var UploadFile = fillItem.find('.BrowseFile')[0].files;
				for(var count = 0; count<UploadFile.length; count++)
				{
				 form_data.append("UploadFile"+PassNumber+"[]", UploadFile[count]);
				}
			}

			// get combine
				fillItem.find('.liCombine').find('li').each(function(){
					var ID_budget_left_com = $(this).attr('id_budget_left');
					// Get Cost dari Arr BudgetRemaining field Using
						// var dt = ClassDt.BudgetRemaining;
						// var Cost = 0;
						// for (var i = 0; i < dt.length; i++) {
						// 	var ID_budget_left_com_ = dt[i].ID;
						// 	if (ID_budget_left_com == ID_budget_left_com_) {
						// 		Cost = dt[i].Using;
						// 		break;
						// 	}
						// }

						var Cost = $(this).attr('subsidi');

					var temp = {
						ID_budget_left : ID_budget_left_com,
						Cost : Cost,
					};
					FormInsertCombine.push(temp);	
				})

			 var data = {
			 	ID_budget_left : ID_budget_left,
			 	ID_m_catalog : ID_m_catalog,
			 	Spec_add : Spec_add,
			 	Need : Need,
			 	Qty : Qty,
			 	UnitCost : UnitCost,
			 	PPH : PPH,
			 	SubTotal : SubTotal,
			 	DateNeeded : DateNeeded,
			 	FormInsertCombine : FormInsertCombine,
			 	PassNumber : PassNumber,
			 }
			 var token = jwt_encode(data,"UAP)(*");
			 FormInsertDetail.push(token);
			 PassNumber++
		})

		// return;

		var token = jwt_encode(FormInsertDetail,"UAP)(*");
		form_data.append('token',token);

		form_data.append('Action',Action);

		token = jwt_encode(PRCode,"UAP)(*");
		form_data.append('PRCode',token);

		token = jwt_encode(Year,"UAP)(*");
		form_data.append('Year',token);

		token = jwt_encode(Departement,"UAP)(*");
		form_data.append('Departement',token);

		token = jwt_encode(Notes,"UAP)(*");
		form_data.append('Notes',token);

		token = jwt_encode(ClassDt.BudgetRemaining,"UAP)(*");
		form_data.append('BudgetRemaining',token);

		var BudgetLeft_awal = JSON.parse(localStorage.getItem("PostBudgetDepartment"));
		token = jwt_encode(BudgetLeft_awal,"UAP)(*");
		form_data.append('BudgetLeft_awal',token);

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
		       case "1":
		       		var St_error = data['St_error'];
		       		var msg = data['msg'];
		       		if (St_error == 0) {
		       			toastr.error(msg,'!!!Failed');
		       		}
		       		else
		       		{
		       			if (data['BudgetChange'] == 1) { // alert Budget Remaining telah di update oleh transaksi lain
		       				toastr.info('Budget Remaining already have by another');
		       			}
		       			// success
		       			$('#labelPrcode').html('PR Code : '+data['PRCode']);
		       			var Status = NameStatus(data['StatusPR']);
		       			$('#Status').html('Status : '+Status);
		       			// Update Variable ClassDt
		       			ClassDt.PRCodeVal = data['PRCode'];
		       			LoadFirstLoad();
		       		}
		       		$('#SaveSubmit').prop('disabled',false).html('Submit');
		       		// if ($("#p_prcode").length) {
		       		// 	$("#p_prcode").html('PRCode : '+data['PRCode']);
		       		// }
		       		// else
		       		// {
		       		// 	$(".thumbnail").find('.row:first').before('<p style = "color : red" id = "p_prcode">PRCode : '+data['PRCode']+'</p>');
		       		// }
		       		
		       		// var rowPullright = $(ID_element).closest('.pull-right');
		       		// rowPullright.empty();
		       		// rowPullright.append('<button class="btn btn-default" id="pdfprint" PRCode = "'+data['PRCode']+'"> <i class = "fa fa-file-pdf-o"></i> Print PDF</button>'+ '&nbsp&nbsp'+'<!--<button class="btn btn-default" id="excelprint" PRCode = "'+data['PRCode']+'"><i class = "fa fa-file-excel-o"></i> Print Excel</button>-->');

		       		// $('button:not([id="pdfprint"]):not([id="excelprint"]):not([id="btnBackToHome"])').prop('disabled', true);
		       		// $(".Detail").prop('disabled', false);
		       		// $("input").prop('disabled', true);
		       		// $("select").prop('disabled', true);
		       		// $("textarea").prop('disabled', true);
		       		// $(".input-group-addon").remove();

		       		// update tableData_selected
		       			// var js = jQuery.parseJSON(data['JsonStatus']);
		       			// JsonStatus = js;
		       			// Get_tableData_selected(JsonStatus);

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
		    if (ID_element == '#SaveSubmit') {
		    	nmbtn = 'Submit';
		    }
		    else if(ID_element == '#SaveSubmit')
		    {
		    	nmbtn = 'Submit';
		    }
		    $(ID_element).prop('disabled',false).html(nmbtn);
		  }
		})

	}

	function NameStatus(Status)
	{
		switch (Status)
	    {
	       case "1":
	       case 1:
	       	Status = 'Awaiting Approval';
	       break;
	       case "2":
	       case 2:
	       	Status = 'Done';
	       break;
	       case "3":
	       case 3:
	       	Status = 'Reject';
	       break;
	       default: 
	           alert('No Status');
	    }

	    return Status;
	} 
</script>