<div class="col-md-12">
	<div id = "divTableData"></div>
</div>
<script type="text/javascript">
	window.payment_type = <?php echo $payment_type ?>;
	window.getDataCalonMhs = <?php echo $getDataCalonMhs ?>;
	$(document).ready(function () {
		// console.log(payment_type);
		console.log(getDataCalonMhs);
		loadtable_header(loadDataTable);
		funcBtnPrint();
	});

	function funcBtnPrint()
	{
		$(".btn-print").click(function(){
			// var obj = 'Tuition_fee_'+$(this).attr('data-smt')+'.pdf';
			// window.open(base_url_js+'fileGet/'+obj,'_blank');
			var ID_register_formulir = $(this).attr('id-register-formulir');
			var url = base_url_js+'save2pdf/print/tuitionFeeAdmission';
			data = {
			  ID_register_formulir : ID_register_formulir,
			}
			var token = jwt_encode(data,"UAP)(*");
			FormSubmitAuto(url, 'POST', [
			    { name: 'token', value: token },
			]);
		})
	}

	function loadtable_header(callback)
	{
	    var div = '';
	    var enddiv = '</div>';
	    var table = '';
	    $("#divTableData").empty();
	    if(getDataCalonMhs.length == 0)
	    {
	    	div = '<div id = "tblData" class="table-responsive" align = "center">No Result Data...';
	    	$("#divTableData").html(div+table+enddiv);
	    }
	    else
	    {
	    	div = '<div id = "tblData" class="table-responsive">';
	    	table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
	    	'<thead>'+
	    	    '<tr>'+
	    	        '<th style="width: 5px;">No  <!--<input type="checkbox" class="uniform" value="nothing" id ="dataResultCheckAll">--></th>'+
	    	        '<th style="width: 55px;">Data & Created</th>'+
	    	        '<th style="width: 55px;">Formulir Code</th>'+
	    	        '<th style="width: 10%;">Beasiswa, File & Rangking</th>'
	    	for (var i = 0; i < payment_type.length; i++) {
	    	    table += '<th style="width: 75px;">'+payment_type[i].Abbreviation+'</th>' ;
	    	    //table += '<th style="width: 70px;">Pot</th>';	
	    	}
	    	table += '<th style="width: 10%;">Keterangan</th>'	
	    	table += '<th style="width: 10%;">Print</th>'		
	    	table += '</tr>' ;	
	    	table += '</thead>' ;	
	    	table += '<tbody>' ;	
	    	table += '</tbody>' ;	
	    	table += '</table>' ;	
	    	$("#divTableData").html(div+table+enddiv);
	    	callback();
	    }
	    
	}

	function loadDataTable()
	{
		var no = 1;
		for (var i = 0; i < getDataCalonMhs.length; i++) {
			var DiskonSPP = getDataCalonMhs[i]['Discount-SPP'];
			var isi_payment = '';
			for (var j = 0; j < payment_type.length; j++) {
				var value_cost = 0;
				var potonganLain = '';
					for(var key in getDataCalonMhs[i]) {
						// var keyDiscount = 
						if (key == payment_type[j]['Abbreviation']) {
							value_cost = getDataCalonMhs[i][key];
							DiskonSPP = getDataCalonMhs[i]['Discount-'+key];
							const dataPotongan = getDataCalonMhs[i]['PotonganLain-'+key];
							if (dataPotongan.length > 0) {
								potonganLain = '<br/><br/><label>Potongan Lain</label>';
								for (var zz = 0; zz < dataPotongan.length; zz++) {
									potonganLain += '<li style = "color:blue;">'+dataPotongan[zz]['DiscountName']+' : '+formatRupiah(dataPotongan[zz]['DiscountValue'])+'</li>'
								}
							}
						}
					}
				isi_payment += '<td style = "color:green;">Invoice : '+value_cost+'<br> Discount : '+DiskonSPP+'%'+potonganLain+'</td>';
				//isi_payment += '<td>'+DiskonSPP+'%</td>';
			}

			if (getDataCalonMhs[i]['getFile'] != '-') {
				showFile = '<a href="javascript:void(0)" class="show_a_href" id = "show'+getDataCalonMhs[i]['ID_register_formulir']+'" filee = "'+getDataCalonMhs[i]['getFile']+'" Email = "'+getDataCalonMhs[i]['Email']+'">Show</a>';
			}
			else
			{
				showFile = '-';
			}

			var Code = (getDataCalonMhs[i]['No_Ref'] != '') ? getDataCalonMhs[i]['FormulirCode'] + ' / ' + getDataCalonMhs[i]['No_Ref'] : getDataCalonMhs[i]['FormulirCode'];
			var Code2 = (getDataCalonMhs[i]['No_Ref'] != '') ? getDataCalonMhs[i]['No_Ref'] : getDataCalonMhs[i]['FormulirCode'];
			var Rangking = (getDataCalonMhs[i]['RangkingRapor'] != 0) ? 'Rangking : '+getDataCalonMhs[i]['RangkingRapor'] : "";
			var btn_print = '<span data-smt="'+Code2+'" class="btn btn-xs btn-print btn-read" id-register-formulir="'+getDataCalonMhs[i]['ID_register_formulir']+'"><i class="fa fa-print"></i> Print</span>';
			var RevID = (getDataCalonMhs[i]['Rev'] == 0) ? '' : '<br><a href="javascript:void(0)" class="showModal" id-register-formulir="'+getDataCalonMhs[i]['ID_register_formulir']+'">Revision '+getDataCalonMhs[i]['Rev']+'x</a>';

			// quiz beasiswa unggulan
			var showBeasiswaUnggulan = ''; // quiz
			var dataBeasiswaUnggulan = getDataCalonMhs[i]['dataBeasiswaUnggulan'];
			if (dataBeasiswaUnggulan.length > 0) {
				var tokenUnggulan = jwt_encode(dataBeasiswaUnggulan[0],"UAP)(*");
				var urlHref= base_url_js + 'page/admission/quiz/c_quiz/getQuizData/'+tokenUnggulan;
					var dRow = dataBeasiswaUnggulan[0];
				var cb = '';
				if (dRow['resultID'] == 0) {
					cb = '( '+dRow['result']+' <i class="fa fa-question-circle" aria-hidden="true"></i> )';
				}
				else if (dRow['resultID'] == 1) {
					cb = '( '+dRow['result']+' <i class="fa fa-check-circle" style="color: green;"></i> )';
				}
				else if (dRow['resultID'] == -1) {
				    cb = '( '+dRow['result']+' <i class="fa fa-minus-circle" style="color: red;"></i> )';
				}
				else
				{
				    cb = '( Haven\'t participated yet in quiz )';
				}

				showBeasiswaUnggulan = '<br/><a href = "'+urlHref+'" class = "btn btn-sm btn-default" target="_blank"><b><span style="color:blue;">Jalur Beasiswa '+cb+'</span></b></a>';
			}


			$(".tableData tbody").append(
					'<tr>'+
						'<td align= "center">'+no+'&nbsp<input type="checkbox" class="uniform" nama ="'+getDataCalonMhs[i]['Name']+'" value ="'+getDataCalonMhs[i]['ID_register_formulir']+'" </td>'+
						'<td>'+setBintangFinance(getDataCalonMhs[i]['Pay_Cond'])+'<br/>'+'<span style="color: #c77905;">'+getDataCalonMhs[i]['Name']+'</span>'+'<br>'+'<span style="color: #c77905;">'+getDataCalonMhs[i]['NamePrody']+'</span>'+'<br>'+getDataCalonMhs[i]['SchoolName']+showBeasiswaUnggulan+'<br>'+'<span style="color: #20525a;">'+getDataCalonMhs[i]['CreateAT']+'</span>'+'</td>'+
						'<td>'+Code+'</td>'+
						'<td>'+getDataCalonMhs[i]['getBeasiswa']+'<br><br>'+Rangking+'<br><br>'+showFile+'</td>'+
						isi_payment+
						'<td>'+getDataCalonMhs[i]['Desc']+RevID+'</td>'+
						'<td>'+btn_print+'</td>'+
					'</tr>' 	
			);

			no++;
		}
		pageHtml = 'tuition_fee_approved';
	}
</script>