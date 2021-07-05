<div class="col-md-12">
	<div style="padding: 10px;">
		<p style="color: red;">Note : <br/>Tagihan berdasarkan <b>Tahun Akademik Admisi</b> </p>
	</div>
	<div id = "divTableData"></div>
</div>
<script type="text/javascript">
	window.payment_type = <?php echo $payment_type ?>;
	window.getDataCalonMhs = <?php echo $getDataCalonMhs ?>;
	$(document).ready(function () {
		// console.log(payment_type);
		console.log(getDataCalonMhs);
		loadtable_header(loadDataTable);
	});

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
	    	        '<th style="width: 55px;">Nama,Prodi & Sekolah</th>'+
	    	        '<th style="width: 55px;">Formulir Code</th>'+
	    	        '<th style="width: 10%;">Beasiswa, File & Rangking</th>'
	    	        //'<th style="width: 55px;">Prodi</th>'+
	    	        //'<th style="width: 55px;">Sekolah</th>'
	    	for (var i = 0; i < payment_type.length; i++) {
	    	    table += '<th style="width: 75px;">'+payment_type[i].Abbreviation+'</th>' ;
	    	    //table += '<th style="width: 70px;">Pot</th>';	
	    	}
	    	 table += '<th style="width: 75px;">'+'Keterangan'+'</th>' ;	
	    	table += '</tr>' ;	
	    	table += '</thead>' ;	
	    	table += '<tbody>' ;	
	    	table += '</tbody>' ;	
	    	table += '</table>' ;	
	    	$("#divTableData").html(div+table+enddiv);
	    	callback();
	    }
	    
	}

	function generateIsiPayment(dataFilter,dataDiscount,DiskonSPP){
		var isi_payment = '';
		for (var j = 0; j < payment_type.length; j++) {
			var selecTOption = '<div class = "form-group" ><label>Discount </label> <select class="selecTOption getDom" id="'+payment_type[j]['Abbreviation']+dataFilter['ID_register_formulir']+'" id-formulir = "'+dataFilter['ID_register_formulir']+'" payment-type = "Discount-'+payment_type[j]['Abbreviation']+'" payment-type_ID = "'+payment_type[j]['ID']+'">';
			var value_cost = 0;
				for(var key in dataFilter) {
					if (key == payment_type[j]['Abbreviation']) {
						value_cost = dataFilter[key];
					}
				}
			$('#'+payment_type[j]['Abbreviation']+dataFilter['ID_register_formulir']).empty();			
			for (var k = 0; k < dataDiscount.length; k++) {
				if (payment_type[j]['Abbreviation'] == 'SPP') {
					var selected = (dataDiscount[k]['Discount']==DiskonSPP) ? 'selected' : '';
				}
				else
				{
					var selected = (dataDiscount[k]['Discount']==0) ? 'selected' : '';
				}

				selecTOption += '<option value="'+dataDiscount[k]['Discount']+'" '+selected+'>'+dataDiscount[k]['Discount']+'%'+'</option>';
			}	
			selecTOption += '</select></div>';
			
			if (payment_type[j]['Abbreviation'] == 'SPP') {
				value_cost = value_cost - ((DiskonSPP/100)*value_cost);
				value_cost = value_cost.toFixed(2);
			}	
			var cost = '<input class="form-control costInput getDom" id="cost_'+dataFilter['ID_register_formulir']+'" id-formulir = "'+dataFilter['ID_register_formulir']+'" value = "'+value_cost+'" payment-type = "'+payment_type[j]['Abbreviation']+'" payment-type_ID = "'+payment_type[j]['ID']+'" readonly style = "color:blue;">';

			var btnSetPotonganLain = '<div class = "row contentPotonganLain"><div class = "col-md-12"><button class = "btn btn-sm btn-primary btnSetPotonganLain" id-formulir = "'+dataFilter['ID_register_formulir']+'" payment-type = "'+payment_type[j]['Abbreviation']+'" payment-type_ID = "'+payment_type[j]['ID']+'" style = "color: #151414;background-color: #86b746;width:100%;">Set Potongan Lain</button></div></div>';

			isi_payment += '<td>'+selecTOption+'<div class = "form-group"><label style = "color:green;">Harga</label> '+cost+'</div>'+btnSetPotonganLain+'</td>';
		}

		return isi_payment;
	}

	function loadDataTable()
	{
		var no = <?php echo $no ?>;
		max_cicilan = getDataCalonMhs[0]['getMaxCicilan'];
		for (var i = 0; i < getDataCalonMhs.length; i++) {
			var DiskonSPP = getDataCalonMhs[i]['DiskonSPP'];
			var getDiscount = getDataCalonMhs[i]['getDiscount'];
			var getBeasiswa = getDataCalonMhs[i]['getBeasiswa'];
			var getDocument = getDataCalonMhs[i]['Document'];
			var isi_payment = generateIsiPayment(getDataCalonMhs[i],getDiscount,DiskonSPP);

			var selecTOption = '<select class="getBeasiswa" id-formulir = "'+getDataCalonMhs[i]['ID_register_formulir']+'" id = "getBeasiswa'+getDataCalonMhs[i]['ID_register_formulir']+'" >';
			for (var k = 0; k < getBeasiswa.length; k++) {
				if (getDataCalonMhs[i]['RangkingRapor'] != 0) {
					var selected = (getBeasiswa[k]['ID']==4) ? 'selected' : '';
				}
				else
				{
					if (k == 0) {
						selecTOption += '<option value="'+'0'+'" '+selected+'>'+'Tidak Beasiswa'+''+'</option>';
					}
				}

				selecTOption += '<option value="'+getBeasiswa[k]['ID']+'" '+selected+'>'+getBeasiswa[k]['DiscountType']+''+'</option>';
			}	
			selecTOption += '</select>';

			var selecTOption2 = '<select class="getDokumen" id-formulir = "'+getDataCalonMhs[i]['ID_register_formulir']+'" style="width: 260px;"" id = "getDokumen'+getDataCalonMhs[i]['ID_register_formulir']+'" >';
				selecTOption2 += '<option value="'+'0'+'" '+'selected'+'>'+'--File Uploaded--'+''+'</option>';
			for (var k = 0; k < getDocument.length; k++) {
				if (getDataCalonMhs[i]['RangkingRapor'] != 0) {
					var selected = (getDocument[k]['Attachment']==getDataCalonMhs[i]['Attachment']) ? 'selected' : '';
				}

				selecTOption2 += '<option value="'+getDocument[k]['ID']+'" '+selected+'>'+getDocument[k]['Attachment']+''+'</option>';
			}	

			selecTOption2 += '</select>';

			showFile = '';
			if (getDataCalonMhs[i]['RangkingRapor'] != 0) {
				showFile = '<a href="javascript:void(0)" class="show_a_href" id = "show'+getDataCalonMhs[i]['ID_register_formulir']+'" filee = "'+getDataCalonMhs[i]['Attachment']+'" Email = "'+getDataCalonMhs[i]['Email']+'">Show</a>';
			}

			var textArea = '<textarea rows="2" cols="5" name="textarea" class="limited form-control ket" data-limit="50" maxlength="50" id-formulir = "'+getDataCalonMhs[i]['ID_register_formulir']+'" id = "ket'+getDataCalonMhs[i]['ID_register_formulir']+'">'+getDataCalonMhs[i]['NoteRev']+'</textarea>Max<span id="chars'+getDataCalonMhs[i]['ID_register_formulir']+'">50</span> characters';	

			var Code = (getDataCalonMhs[i]['No_Ref'] != '') ? getDataCalonMhs[i]['FormulirCode'] + ' / ' + getDataCalonMhs[i]['No_Ref'] : getDataCalonMhs[i]['FormulirCode'];
			var Rangking = (getDataCalonMhs[i]['RangkingRapor'] != 0) ? 'Rangking : '+getDataCalonMhs[i]['RangkingRapor'] : "";

			// select option bintang
				let selectOptionBintang = '<select style = "color:orange;" class = "form-control selectBintang" id-formulir = "'+getDataCalonMhs[i]['ID_register_formulir']+'">';
				const dataSchemaPayment =  getDataCalonMhs[i].SchemaPayment;
				for (var z = 0; z < dataSchemaPayment.length; z++) {
					const selectedBintangDefault = (z == 0) ? 'selected' : '';
					const JumlahBintang = dataSchemaPayment[z].JumlahBintang;
					let bintang = '';
					for (var zz = 1; zz <= JumlahBintang; zz++) {
						bintang += '*';
					}
					selectOptionBintang += '<option value = "'+JumlahBintang+'" '+selectedBintangDefault+' >Schema payment '+bintang+'</option>';
				}

				selectOptionBintang += '</select>';

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
					'<tr id = "id_formulir'+getDataCalonMhs[i]['ID_register_formulir']+'">'+
						'<td align= "center">'+no+'&nbsp<input type="checkbox" nama ="'+getDataCalonMhs[i]['Name']+'" class="uniform" value ="'+getDataCalonMhs[i]['ID_register_formulir']+'" email ="'+getDataCalonMhs[i]['Email']+'">'+'</td>'+
						'<td>'+getDataCalonMhs[i]['Name']+'<br>'+getDataCalonMhs[i]['NamePrody']+'<br>'+getDataCalonMhs[i]['SchoolName']+showBeasiswaUnggulan+'</td>'+
						'<td>'+Code+'</td>'+
						'<td>'+selecTOption+'<br><br>'+selecTOption2+'<br><br>'+selectOptionBintang+'<br><br>'+Rangking+'<br><br>'+showFile+'</td>'+
						//'<td>'+getDataCalonMhs[i]['NamePrody']+'</td>'+
						//'<td>'+getDataCalonMhs[i]['SchoolName']+'</td>'+
						isi_payment+
						'<td>'+textArea+'</td>'+

					'</tr>' 	
			);

			no++;
		}

		$('.costInput').maskMoney({thousands:'.', decimal:',', precision:2,allowZero: true});
		$('.costInput').maskMoney('mask', '9894');

		EvkeyketTable();

		pageHtml = 'tuition_fee';
	}

	function EvkeyketTable()
	{
		$(".ket").keyup(function(){
			var maxLength = $(this).attr('maxlength');
			var length = $(this).val().length;
			var id_formulir = $(this).attr('id-formulir');
			var length = maxLength-length;
			$('#chars'+id_formulir).text(length);
		})
	}

	$(document).on('change','.selecTOption', function () {
		var id_formulir = $(this).attr('id-formulir');
		var payment_type = $(this).attr('payment-type');
		var valuee = $(this).val();
		var value_cost = 0;
		for (var i = 0; i < getDataCalonMhs.length; i++) {
			if (id_formulir == getDataCalonMhs[i]['ID_register_formulir']) {
				for(var key in getDataCalonMhs[i]) {
					var keyTemp = 'Discount-'+key;
					if (keyTemp == payment_type) {
						value_cost = getDataCalonMhs[i][key];
					}
				}
			}
		}
		
		value_cost = value_cost - ((valuee/100)*value_cost);
		value_cost = value_cost.toFixed(2);
		var payment_type_input = payment_type.split("-");
		$('.costInput[payment-type="'+payment_type_input[1]+'"][id-formulir = "'+id_formulir+'"]').val(value_cost);
		$('.costInput').maskMoney({thousands:'.', decimal:',', precision:2,allowZero: true});
		$('.costInput').maskMoney('mask', '9894');
	});

	$(document).on('change','.selectBintang',function(e){
		const itsme =  $(this);
		let id_formulir = $(this).attr('id-formulir');
		let selectedBintang  = $(this).val();
		let getData = getDataCalonMhs.filter(x => x.ID_register_formulir === id_formulir)
		let getIndex = getDataCalonMhs.findIndex(x => x.ID_register_formulir === id_formulir)

		let SchemaPayment = (getData[0].SchemaPayment).filter(x=>x.JumlahBintang === selectedBintang)
		let getTuitionFee = SchemaPayment[0].TuitionFee;
		
		for(key in getTuitionFee){
			getDataCalonMhs[getIndex][key] = getTuitionFee[key];
		}

		// remove
		const d = 4;
		for (var i = 0; i < 4; i++) {
			itsme.closest('tr').find('td:eq('+d+')').remove();
		}

		var DiskonSPP = getDataCalonMhs[getIndex]['DiskonSPP'];
		var getDiscount = getDataCalonMhs[getIndex]['getDiscount'];
		var isi_payment = generateIsiPayment(getDataCalonMhs[getIndex],getDiscount,DiskonSPP);
		// create it
		itsme.closest('tr').find('td:eq(3)').after(isi_payment);
		$('.costInput').maskMoney({thousands:'.', decimal:',', precision:2,allowZero: true});
		$('.costInput').maskMoney('mask', '9894');

		// clear data potongan lain
		dataInputPotonganLain = dataInputPotonganLain.filter(x => {
		    if (x.ID_register_formulir === id_formulir) {
		        return false;
		    }
		    return true;
		});

	})

</script>