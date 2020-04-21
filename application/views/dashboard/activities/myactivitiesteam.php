<script type="text/javascript">
	function fetchActivities() {
		var filtering = $("#form-filter").serialize();		

        var token = jwt_encode({Filter : filtering},'UAP)(*');
        var dataTable = $('#fetch-data-tables #table-list-data').DataTable( {
            "destroy": true,
            "retrieve":true,
            "processing": true,
            "serverSide": true,
            "iDisplayLength" : 10,
            "ordering" : false,
            "responsive": true,
            "language": {
                "searchPlaceholder": "NIP, or Name"
            },
            "lengthMenu": [[10, 25, 50], [10, 25, 50]],
            "ajax":{
                url : base_url_js+'my-team/fetchActivities', // json datasource
                ordering : false,
                data : {token:token},
                type: "post",  // method  , by default get
                error: function(jqXHR){  // error handling
                    loading_modal_hide();
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
            },
            "columns": [
            	{
            		"data":"ID",
            		render: function (data, type, row, meta) {
				        return meta.row + meta.settings._iDisplayStart + 1;
				    }
            	},
            	{
            		"data":"NIP",
            		"render": function (data, type, row, meta) {
            			var label = "";
            			return data;
            		}
            	},
            	{
            		"data":"Name"            		
            	},
            	{
            		"data":"DivisionMain_",
            		"render": function (data, type, row, meta) {
            			var label = data+"-"+row.PositionMain_;
            			return label;
            		}            		
            	},
            	{
            		"data":"TotalActivity"            		
            	},
            	{
            		"data":"FirstLoginPortal"            		
            	},
            	{
            		"data":"LastLoginPortal"            		
            	},
            	{
            		"data":"NIP",
            		"render": function (data, type, row, meta) {
            			var label = '<button class="btn btn-info btn-detail" data-date="'+row.FirstLoginPortal+'" data-id="'+data+'"><i class="fa fa-folder-open"></i></button>';
            			return label;
            		}
            	},
        	]
        });
	}
	$(document).ready(function(){
		fetchActivities();
		$("#attendance_start").datepicker({
	        dateFormat: 'dd-mm-yy',
	        changeYear: true,
	        changeMonth: true
	    });
	    $('#form-filter').on('keyup keypress', function(e) {
	      var keyCode = e.keyCode || e.which;
	      if (keyCode === 13) { 
	        e.preventDefault();
	        return false;
	      }
	    });
	    $(".show-more-filter").click(function(){
	      var isOpen = $(this).attr("aria-expanded");
	      if(isOpen == "false"){
	        $(this).attr("aria-expanded",true);
	        $(this).find("span").text("Show less");
	        $(this).find("i.fa").toggleClass("fa-angle-double-down fa-angle-double-up");
	      }else{
	        $(this).attr("aria-expanded",false);
	        $(this).find("span").text("Advance filter");        
	        $(this).find("i.fa").toggleClass("fa-angle-double-up fa-angle-double-down");
	      }
	    });

	    $("#form-filter select[name=division]").change(function(){
	      var value = $(this).val();
	      if($.trim(value) != ''){
	        $("#form-filter select[name=position]").prop("disabled",false);
	      }
	    });
	    $("#form-filter select[name=position]").change(function(){
	      var division = $("#form-filter select[name=division]").val();
	      if($.trim(division) == ''){
	        division.addClass("required");
	        alert("Please fill up field Division");
	      }
	    });
	    $("#form-filter .btn-filter").click(function(){
	    	$('body #my-team-activity #fetch-data-tables #table-list-data').DataTable().destroy();
	        fetchActivities();
	        var startDate = $("#form-filter input[name=attendance_start]").val();
	        $("#attendance-temporary .result .panel-title >span").text(startDate).addClass("bg-success");
	    });

	    $("#table-list-data").on("click",".btn-detail",function(){
	    	var itsme = $(this);
	    	var NIP = itsme.data("id");
	    	var DATE = itsme.data("date");
	    	if($.trim(NIP).length > 0){
	    		var data = {
	                NIP : NIP,
	                DATE : DATE
	            };
	            var token = jwt_encode(data,'UAP)(*');
	    		$.ajax({
	                type : 'POST',
	                url : base_url_js+"my-team/detailActivities",
	                data: {token:token},
	                dataType : 'html',
	                beforeSend :function(){
	                    itsme.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
	                },error : function(jqXHR){
	                	itsme.html('<i class="fa fa-folder-open"></i>');
	                    $("body #GlobalModal .modal-header").html("<h1>Error notification</h1>");
	                    $("body #GlobalModal .modal-body").html(jqXHR.responseText);
	                    $("body #GlobalModal").modal("show");
	                },success : function(response){
	                	itsme.html('<i class="fa fa-folder-open"></i>');
				        $('#GlobalModal .modal-dialog').css({"width":"80%"});       
	                    $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
				            '<h4 class="modal-title">Detail attendance </h4>');
				        $('#GlobalModal .modal-body').html(response);        
				        $('#GlobalModal').modal({
				            'show' : true,
				            'backdrop' : 'static'
				        });
	                }
	            });
	    	}
	    	
	    });
	});
</script>
<div id="my-team-activity">
	<div class="filtering">
		<div class="row">
		  <div class="col-sm-12">
		    <div class="panel panel-default">
		      <div class="panel-heading">
		        <h4 class="panel-title"><i class="fa fa-filter"></i> Form Filter</h4>
		      </div>
		      <div class="panel-body">
		        <form id="form-filter" action="" method="post" autocomplete="off">
		          <div class="row">
		            <div class="col-sm-2">
		              <div class="form-group">
		                <label>Employee</label>               
		                <input type="text" class="form-control" name="staff" placeholder="NIP or Name">               
		              </div>
		            </div>
		            <div class="col-sm-3">
		              <div class="form-group">
		                <label>Attendance Day</label>
		                <input type="text" name="attendance_start" id="attendance_start" class="form-control" placeholder="dd-mm-yyy"> 		                  
		              </div>
		            </div>
		          </div>
		          <div class="row">
		            <div class="col-sm-2">
		              <div class="form-group">
		                <label class="show-more-filter text-success" data-toggle="collapse" data-target="#advance-filter" aria-expanded="false" aria-controls="advance-filter">
		                  <span>Advance filter</span> 
		                  <i class="fa fa-angle-double-down"></i>
		                </label>
		              </div>
		            </div>
		          </div>
		          <div id="advance-filter" class="collapse">
		            <div class="row">
		              <div class="col-sm-2">
		                <div class="form-groups">
		                  <label>Status employee</label>
		                </div>
		                <?php if(!empty($statusstd)) {
		                foreach ($statusstd as $t) { ?>
		                <div class="form-group">
		                  <div class="col-sm-10">
		                    <div class="checkbox">
		                      <label>
		                        <input type="checkbox" value="<?=$t->IDStatus?>" name="statusstd[]" > <?=$t->Description?>
		                      </label>
		                    </div>
		                  </div>
		                </div>
		                <?php } } ?>
		              </div>
		              <div class="col-sm-2">
		                <div class="form-group">
		                  <label>Gender</label>
		                  <div class="form-group">
		                    <div class="col-sm-10">
		                      <div class="form-checkbox">
		                        <label>
		                            <input type="checkbox" value="L" name="gender[]"> Male
		                        </label>
		                      </div>
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <div class="col-sm-10">
		                      <div class="form-checkbox">
		                        <label>
		                            <input type="checkbox" value="P" name="gender[]" > Female
		                        </label>
		                      </div>
		                    </div>
		                  </div>

		                </div>
		              </div>
		              <div class="col-sm-4">
		                <div class="form-groups">
		                  <label>Religion</label>
		                </div>
		                <?php if(!empty($religion)){ 
		                foreach ($religion as $rg) { ?>
		                <div class="form-group" style="margin-bottom:0px">
		                  <div class="col-sm-6">
		                    <div class="checkbox">
		                      <label>
		                        <input type="checkbox" value="<?=$rg->IDReligion?>" name="religion[]"> <?=$rg->Religion?>
		                      </label>
		                    </div>
		                  </div>
		                </div>
		                <?php } } ?>

		              </div>		              	              
		              
		              <div class="col-sm-4">
		                <div class="form-group">
		                  <label>Last Education</label>
		                  <?php if(!empty($level_education)){ 
		                  foreach ($level_education as $le) { ?>
		                  <div class="form-group" style="margin-bottom:0px">
		                    <div class="col-sm-6">
		                      <div class="checkbox">
		                        <label>
		                          <input type="checkbox" value="<?=$le->ID?>" name="level_education[]"> <?=$le->Description?>
		                        </label>
		                      </div>
		                    </div>
		                  </div>
		                  <?php } } ?>
		                </div>
		              </div>
		            </div>
		          </div>
		          <div class="form-group" style="padding-top:22px">
		            <button class="btn btn-primary btn-filter" type="button"><i class="fa fa-search"></i> Search</button>
		            <a class="btn btn-default" href="">Clear Filter</a>
		          </div>		          
		        </form>
		      </div>
		    </div>
		  </div>
		</div>
		
	</div>
	<div class="result">
		<div class="row">
			<div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-heading">            
		          <h4 class="panel-title"><i class="fa fa-bars"></i> List of record activities your team <span>Today (<?= date('d F Y') ?>)</span></h4>
		        </div>
		        <div class="panel-body">
		        	<div id="fetch-data-tables">
		        		<table class="table table-bordered table-striped" id="table-list-data">
	                        <thead>
	                            <tr>
	                                <th width="5%">No</th>
	                                <th>NIP</th>
	                                <th>Employee</th>
	                                <th>Position</th>
	                                <th>Total Activity</th>
	                                <th>First Login</th>
	                                <th>Last Login</th>
	                                <th width="5%">Detail</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        	<tr><td colspan="8">Empty result</td></tr>
	                        </tbody>
	                    </table>
		        	</div>
		        </div>
	          </div>
	  		</div>
		</div>
	</div>
</div>