<div class="row">
	<div id = "loadingProcess"></div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover table-checkable datatable">
				<thead>
					<tr>
					<th style="width: 15px;">No</th>
					<th>Angkatan</th>
					<th>Formulir Code</th>
					<!-- <th>Ref</th> -->
					<!-- <th>Link</th> -->
					<th>Status Link</th>
					<th>Status Jual</th>
					<th>Print</th>
					<th>Create At</th>
					<th>Created By</th>
					<!-- <th>Action</th> -->
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>

	
<script type="text/javascript">

	$(document).ready(function() {
		var selectTahun = "<?php echo $passSelectTahun ?>";
		getJsonFormulirOnline(selectTahun);
	}); // exit document Function

	function getJsonFormulirOnline(selectTahun)
	{
		var selectTahun = $("#selectTahun").val();
		var url = base_url_js+'admission/master-registration/getJsonFormulirOffline';
		var data = {
		    selectTahun : selectTahun
		};
		var token = jwt_encode(data,"UAP)(*");
		loading_page('#loadingProcess');
		$.post(url,{token:token},function (data_json) {
			var response = jQuery.parseJSON(data_json);
			var no = 1;
			$("#loadingProcess").remove();
			for (var i = 0; i < response.length; i++) {
				var print = 'Belum di Print';
				var status = '<td style="'+
    							'color:  green;'+
								'">Not Activated'+
							  '</td>';
				if (response[i]['Status'] == 1 ) {
					status = '<td style="'+
    							'color:  red;'+
								'">Activated'+
							  '</td>';
				}
				var status_jual = '<td style="'+
    							'color:  green;'+
								'">IN'+
							  '</td>';
				if (response[i]['Status'] == 1 ) {
					status_jual = '<td style="'+
    							'color:  red;'+
								'">Sold Out'+
							  '</td>';
				}
				if (response[i]['Print'] == 1 ) {
					print = 'Sudah di Print';
				}
				var btn_print = '<span data-smt="'+response[i]['FormulirCode']+'" class="btn btn-xs btn-print btn-read" data-token = "'+response[i]['Link']+'"><i class="fa fa-print"></i> Print</span>';
				$(".datatable tbody").append(
					'<tr>'+
						'<td>'+no+'</td>'+
						'<td>'+response[i]['Years']+'</td>'+
						'<td>'+response[i]['FormulirCode']+'</td>'+
						// '<td>'+response[i]['No_Ref']+'</td>'+
						// '<td>'+response[i]['Link']+'</td>'+
						status+
						status_jual+
						'<td>'+print+'</td>'+
						'<td>'+response[i]['CreateAT']+'</td>'+
						'<td>'+response[i]['Name']+'</td>'+
						// '<td>'+btn_print+'</td>'+
					'</tr>'	
					);
				no++;
			}
		    LoaddataTable('.datatable');
		});
	}	
</script>
