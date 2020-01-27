<?php $message = $this->session->flashdata('message');
	if(!empty($message)){ ?>
	<script type="text/javascript">
	$(document).ready(function(){
		toastr.info("<?= $this->session->flashdata('message');?>",'Info!');
	});
	</script>
<?php } ?>
<div id="subject-type">
	<div class="row">
		<div class="col-sm-12">
			<a class="btn btn-sm btn-warning" href="<?=site_url('global-informations/message-blast')?>">
				<i class="fa fa-chevron-left"></i> Bact to message
			</a>
		</div>
		<div class="col-sm-12">
			<div class="panel panel-default"  style="margin-top:10px">
				<div class="panel-heading">
					<h4 class="panel-title">
						<i class="fa fa-filter"></i> Form Filter
					</h4>
				</div>
				<div class="panel-body">
					<form id="form-filter" action="" method="post" autocomplete="off">
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label>Subject</label>
									<input type="text" name="subject" placeholder="Subject or alternate subject" class="form-control">
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<label>Status</label>
									<select class="form-control" name="status">
										<option value="">-Choose one-</option>
										<option value="1">Active</option>
										<option value="0">Not Active</option>
									</select>
								</div>	
							</div>
							<div class="col-sm-2" style="line-height:75px">
								<div class="form-group">
									<button class="btn btn-sm btn-primary" type="button"><i class="fa fa-search"></i> Search</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="panel panel-default"  style="margin-top:10px">
				<div class="panel-heading">
					<div class="pull-right">
						<button class="btn btn-xs btn-success btn-add-new" type="button"><i class="fa fa-plus"></i> Add New Record</button>
					</div>
					<h4 class="panel-title">
						<i class="fa fa-bars"></i> List of subject type
					</h4>
				</div>
				<div class="panel-body">					
					<div class="fetch-data-tables">
						<table class="table table-bordered" id="table-list-data">
							<thead>
								<tr>
									<th width="2%">No</th>
									<th>Subject</th>
									<th>Status</th>
									<th colspan="3"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="6">Empty data</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>

<script type="text/javascript">
	function fetchingSubject() {
        //loading_modal_show();
        var filtering = $("#form-filter").serialize();
        var token = jwt_encode({Filter : filtering},'UAP)(*');

        var dataTable = $('body #fetch-data-tables #table-list-data').DataTable( {
            "destroy": true,
            "retrieve":true,
            "processing": true,
            "serverSide": true,
            "iDisplayLength" : 10,
            "ordering" : false,
            "responsive": true,
            "language": {
                "searchPlaceholder": "Subject name"
            },
            "lengthMenu": [[10, 25, 50], [10, 25, 50]],
            "ajax":{
                url : base_url_js+'global-informations/subject-type/fetching', // json datasource
                ordering : false,
                data : {token:token},
                type: "post",  // method  , by default get
                error: function(jqXHR){  // error handling
                    //loading_modal_hide();
                    $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title">Error Fetch Student Data</h4>');
                    $('#GlobalModal .modal-body').html(jqXHR.responseText);
                    $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                    $('#GlobalModal').modal({
                        'show' : true,
                        'backdrop' : 'static'
                    });
                }
            },
            "initComplete": function(settings, json) {
                //loading_modal_hide();
                console.log(json);
            },
            "columns": [
            	{
	                "data": "Subject",
	            },
	            { "data": "IsActive",
	              "render": function (data, type, row, meta) {
	              	return "<p>"+((data == 1) ? "Active":"Non Active")+"</p>";
	              }
	          	},
	          	{
	                "data": "ID",
	            },
	        ],
        });
    }
	$(document).ready(function(){
		fetchingSubject();
		$(".btn-add-new").click(function(){
			$.ajax({
			    type : 'POST',
			    url : base_url_js+"global-informations/subject-type/form",
			    dataType : 'html',
			    beforeSend :function(){
			    	loading_modal_show();
			    },error : function(jqXHR){
	            	loading_modal_hide();
                    $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title">Error Fetch Student Data</h4>');
                    $('#GlobalModal .modal-body').html(jqXHR.responseText);
                    $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                    $('#GlobalModal').modal({
                        'show' : true,
                        'backdrop' : 'static'
                    });
			    },success : function(response){
	            	loading_modal_hide();
					$("body #global-informations #subject-type").html(response);
			    }
			});
		});
	});
</script>