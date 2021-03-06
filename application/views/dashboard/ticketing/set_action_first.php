<?php $this->load->view('dashboard/ticketing/LoadCssTicketToday') ?>
<style type="text/css">
	.row {
	    margin-right: 0px;
	    margin-left: 0px;
	}
</style>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-4">
		<div class="well">
			<div style="text-align: center;">
				<img data-src="<?php echo base_url('uploads/employees/'.$DataTicket[0]['PhotoRequested']); ?>" style="margin-top: -3px;" class="img-circle img-fitter" width="100">
				<h4><b>Ticket Data</b></h4>
			</div>
			<table class="table" id="tableDetailTicket">
				<tr>
					<td style="width: 25%;">NoTicket</td>
					<td>:</td>
					<td><?php echo $DataTicket[0]['NoTicket'] ?></td>
				</tr>
				<tr>
					<td style="width: 25%;">Title</td>
					<td>:</td>
					<td><?php echo $DataTicket[0]['Title'] ?></td>
				</tr>
				<tr>
					<td style="width: 25%;">Category</td>
					<td>:</td>
					<td><?php echo $DataTicket[0]['NameDepartmentDestination'].' - '.$DataTicket[0]['CategoryDescriptions'] ?></td>
				</tr>
				<tr>
					<td style="width: 25%;">Message</td>
					<td>:</td>
					<td><?php echo nl2br($DataTicket[0]['Message']) ?></td>
				</tr>
				<tr>
					<td style="width: 25%;">Requested by</td>
					<td>:</td>
					<td><?php echo $DataTicket[0]['NameRequested'] ?></td>
				</tr>
				<tr>
					<td style="width: 25%;">Requested on</td>
					<td>:</td>
					<td><?php echo $DataTicket[0]['RequestedAt'] ?></td>
				</tr>
				<?php if ($DataTicket[0]['Files'] != null && $DataTicket[0]['Files'] != ""): ?>
				 <tr>
		          <td>Files Upload</td>
		          <td>:</td>
		          <td><a href= "<?php echo $DataTicket[0]['Files'] ?>" target="_blank">Files Upload<a></td>
		         </tr>'
				<?php endif ?>
			</table>
			<br/>
			<div id ="ShowProgressList">
				
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
			    <h4 class="panel-title">Assign To</h4>
			</div>
			<div class="panel-body" style="min-height: 100px;" id = "PageAssignTo">
				<!-- <span data-smt="" class="btn btn btn-add-assign_to">
                    <i class="icon-plus"></i> Add
                </span> -->
                <div id="FormAssignTo" style="margin-top: 10px;"></div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
			    <h4 class="panel-title">Transfer To</h4>
			</div>
			<div class="panel-body" style="min-height: 100px;">
				<span data-smt="" class="btn btn btn-add-transfer_to">
                    <i class="icon-plus"></i> Add
                </span>
                <div id="FormTransferTo" style="margin-top: 10px;"></div>
			</div>
		</div>
	</div>
</div>

<br/>
<div style="padding: 5px;">
	<button class="btn btn-block btn-success" id = "btnSetAction">Save</button>
</div>

<script type="text/javascript">
	var Authent = <?php echo json_encode($Authent) ?>;
	// console.log(Authent);
	var DataTicket = <?php echo json_encode($DataTicket) ?>;
	// console.log(DataTicket);
	var DataCategory = <?php echo json_encode($DataCategory) ?>;
	var DataEmployees = <?php echo json_encode($DataEmployees) ?>;
	var DataReceived = <?php echo json_encode($DataReceived) ?>;

	var btnPasteHere = function (context) {
	    var ui = $.summernote.ui;

	    // create button
	    var button = ui.button({
	        contents: '<i class="fa fa-clipboard"/> Paste text',
	        tooltip: 'Paste text',
	        click: function () {
	            // invoke insertText method with 'hello' on editor module.

	            let html = 
	                      '<div class = "row">'+
	                        '<div class = "col-md-12">'+
	                          '<div class = "well">'+
	                            '<label>Paste here</label>'+
	                            '<textarea id="fillModalPaste" class="form-control" rows="10" placeholder="Paste here..."></textarea>' +
	                            '<hr/>' +
	                            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
	                            ' | <button type="button" class="btn btn-success" id="btnSaveModalPaste">Save</button> ' +
	                          '</div>'+
	                        '</div>'+
	                      '</div>'  
	              ;

	            $('#GlobalModal .modal-header').html('<h4 class="modal-title">Paste Here</h4>');
	            $('#GlobalModal .modal-body').html(html);
	            $('#GlobalModal .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>'+
	                '<button type="button" id="btnSaveModalPaste" class="btn btn-success">Save</button>');
	            $('#GlobalModal').modal({
	                'show' : true,
	                'backdrop' : 'static'
	            });

	            $('#fillModalPaste').focus()

	            $('#btnSaveModalPaste').click(function () {
	                var fillModalPaste = $('#fillModalPaste').val();
	                context.invoke('editor.insertText', fillModalPaste);
	               $('#GlobalModal').modal('hide');
	            });
	        }
	    });

	    return button.render();   // return button as jquery object
	};	

	var App_AssignTo = {
		DomContentForm : function(selector){
			var html = '';
			var valTextArea = DataReceived[0].MessageReceived;
			
			html += '<div class = "row form-assign-to">'+
						'<div class = "form-horizontal well">'+
							'<div class = "form-group">'+
								'<div class = "row">'+
									'<div class = "col-xs-3">'+
										'<label>'+'Category'+'</label>'+
									'</div>'+
									'<div class = "col-xs-6">'+
										'<select class="select2-select-00 full-width-fix input_assign_to" name = "CategoryReceivedID"></select>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div class = "form-group">'+
								'<div class = "row">'+
									'<div class = "col-xs-3">'+
										'<label>'+'Note for worker'+'</label>'+
										//'<p style = "color:red;">(not show in user)</p>'+
									'</div>'+
									'<div class = "col-xs-6">'+
										 // '<textarea class="form-control input_assign_to" rows="8" name="MessageReceived">'+valTextArea+'</textarea>'+
										 '<textarea name="MessageReceived" class="form-control input_assign_to area-summernote formTemplateMessage">'+valTextArea+'</textarea>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div class = "form-group">'+
								'<div class = "row">'+
									'<div class = "col-xs-3">'+
										'<label>'+'Due Date'+'</label>'+
									'</div>'+
									'<div class = "col-xs-4">'+
										'<div class="input-group input-append date datetimepicker">'+
				                            '<input data-format="yyyy-MM-dd" class="form-control input_assign_to" type="text" name = "DueDate" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
				                            '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
				                		'</div>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div class = "form-group">'+
								'<div class = "row">'+
									'<div class = "col-xs-3">'+
										'<label>'+'Worker'+'</label>'+
									'</div>'+
									'<div class = "col-xs-6">'+
										'<select class="select2-select-00 full-width-fix input_assign_to" multiple size="5" name="NIP">'+
										'</select>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div style = "text-align:right;" >'+
								'<button class = "btn btn-danger removeRowassignTo"><i class = "fa fa-trash"></i> Delete</button>'+
							'</div>'+
						'</div>'+
						'<hr/>'+
					'</div>';
			selector.append(html);

			var selectorCategory = $('.form-assign-to:last').find('.input_assign_to[name="CategoryReceivedID"]');
			App_set_ticket.LoadSelectOptionCategory(selectorCategory);
			var selectorEmployees = $('.form-assign-to:last').find('.input_assign_to[name="NIP"]');			 			
			App_set_ticket.LoadSelectOptionWorker(selectorEmployees);
			$('.form-assign-to:last').find('.datetimepicker').datetimepicker({
				useCurrent: false,
				format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
			});	


			$('.form-assign-to').find('.formTemplateMessage').summernote({
			    placeholder: 'Text your question...',
			    height: 250,
			    disableDragAndDrop : true,
			    toolbar: [
			        ['style', ['style']],
			        ['font', ['bold', 'underline', 'clear']],
			        ['fontname', ['fontname']],
			        ['color', ['color']],
			        ['para', ['ul', 'ol', 'paragraph']],
			        ['table', ['table']],
			        ['view', ['fullscreen', 'help']],
			        ['mybutton', ['PasteHere']]
			    ],
			    buttons: {
			        PasteHere: btnPasteHere
			    },
			    callbacks: {
			        onPaste: function(e) {
			                alert('Disabled cut copy and paste');
			                e.preventDefault();
			        }
			    }
			});

		},

		DomContentRemove : function(selector){
			var selector_closest = selector.closest('.form-assign-to');
			selector_closest.remove();
		}
	};

	var App_transfer_to = {
		DomContentForm : function(selector){
			var html = '';
			var valTextArea = '';
			html += '<div class = "row form-transfer-to">'+
						'<div class = "form-horizontal well">'+
							'<div class = "form-group">'+
								'<div class = "row">'+
									'<div class = "col-xs-3">'+
										'<label>'+'Category'+'</label>'+
									'</div>'+
									'<div class = "col-xs-6">'+
										'<select class="select2-select-00 full-width-fix input_transfer_to" name = "CategoryReceivedID"></select>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div class = "form-group">'+
								'<div class = "row">'+
									'<div class = "col-xs-3">'+
										'<label>'+'Note for department'+'</label>'+
										//'<p style = "color:red;">(not show in user)</p>'+
									'</div>'+
									'<div class = "col-xs-6">'+
										 // '<textarea class="form-control input_transfer_to" rows="8" name="MessageReceived">'+valTextArea+'</textarea>'+
										 	'<textarea name="MessageReceived" class="form-control input_transfer_to area-summernote formTemplateMessage">'+valTextArea+'</textarea>'+
									'</div>'+
								'</div>'+
							'</div>'+
							'<div style = "text-align:right;" >'+
								'<button class = "btn btn-danger removeRowtransferTo"><i class = "fa fa-trash"></i> Delete</button>'+
							'</div>'+
						'</div>'+
						'<hr/>'+
					'</div>';
			selector.append(html);

			var selectorCategory = $('.form-transfer-to:last').find('.input_transfer_to[name="CategoryReceivedID"]');
			App_set_ticket.LoadSelectOptionCategory(selectorCategory,'transfer_to');

			$('.form-transfer-to:last').find('.formTemplateMessage').summernote({
			    placeholder: 'Text your question...',
			    height: 250,
			    disableDragAndDrop : true,
			    toolbar: [
			        ['style', ['style']],
			        ['font', ['bold', 'underline', 'clear']],
			        ['fontname', ['fontname']],
			        ['color', ['color']],
			        ['para', ['ul', 'ol', 'paragraph']],
			        ['table', ['table']],
			        ['view', ['fullscreen', 'help']],
			        ['mybutton', ['PasteHere']]
			    ],
			    buttons: {
			        PasteHere: btnPasteHere
			    },
			    callbacks: {
			        onPaste: function(e) {
			                alert('Disabled cut copy and paste');
			                e.preventDefault();
			        }
			    }
			});
		},

		DomContentRemove : function(selector){
			var selector_closest = selector.closest('.form-transfer-to');
			selector_closest.remove();
		}
	};

	var App_set_ticket = {
		LoadSelectOptionCategory : function(selector,type="assign_to"){
			var CategorySelected = '';
			// get value Category
			var arr_selected_category = [];
			$('.input_assign_to[name = "CategoryReceivedID"]').each(function(){
				var v = $(this).find('option:selected').val();
				arr_selected_category.push(v);
			})

			CategorySelected = DataReceived[0].CategoryReceivedID;
			selector.empty();
			if (type == 'assign_to') {
				for (var i = 0; i < DataCategory.length; i++) {
					var check = App_set_ticket.excludeOptionCategory(DataCategory[i][3],arr_selected_category);
					if (DataCategory[i][4] == DepartmentID && check) {
						var selected = (CategorySelected == DataCategory[i][3]) ? 'selected' : '';
						selector.append(
						     '<option value = "'+DataCategory[i][3]+'" '+selected+' department = "'+DataCategory[i][7]+'" code = "'+DataCategory[i][4]+'" >'+DataCategory[i][7]+' - '+DataCategory[i][1]+'</option>'
						 );
					}
				}
			}
			else
			{
				// get value Category
				var arr_selected_category2 = [];
				$('.input_transfer_to[name = "CategoryReceivedID"]').each(function(){
					var v = $(this).find('option:selected').val();
					arr_selected_category2.push(v);
				})
				for (var i = 0; i < DataCategory.length; i++) {
					var check = App_set_ticket.excludeOptionCategory(DataCategory[i][3],arr_selected_category2);
					if (DataCategory[i][4] != DepartmentID && check) {
						var selected = (CategorySelected == DataCategory[i][3]) ? 'selected' : '';
						selector.append(
						     '<option value = "'+DataCategory[i][3]+'" '+selected+' department = "'+DataCategory[i][7]+'" code = "'+DataCategory[i][4]+'" >'+DataCategory[i][7]+' - '+DataCategory[i][1]+'</option>'
						 );
					}
				}
			}
			
			selector.select2({

			});
			
		},

		excludeOptionCategory : function(ID,arr_selected_category){
			var bool = true;
			for (var i = 0; i < arr_selected_category.length; i++) {
				if (ID == arr_selected_category[i]) {
					bool = false;
					break;
				}
			}

			return bool;
		},

		LoadSelectOptionWorker : function(selector){
			selector.empty();
			for (var i = 0; i < DataEmployees.length; i++) {
				var data = DataEmployees[i];
				selector.append('<option value="'+data.NIP+'">'+data.Name+'</option>')
			}
			selector.select2({allowClear: true});
		},

		CategoryChangeEvent  : function(selector,value,type="assign_to"){
			if (type =='assign_to') {
				var Index = $('.input_assign_to[name="CategoryReceivedID"]').index(selector);
				var bool = true;
				$('.input_assign_to[name="CategoryReceivedID"]:not(":eq('+Index+')")').each(function(){
					var v = $(this).val();
					if (value == v) {
						bool = false;
						return;
					}
				})

				if (!bool) {
					toastr.info('Category is exist, please check your value');
					App_set_ticket.LoadSelectOptionCategory(selector);
				}
			}
			else
			{
				var Index = $('.input_transfer_to[name="CategoryReceivedID"]').index(selector);
				var bool = true;
				$('.input_transfer_to[name="CategoryReceivedID"]:not(":eq('+Index+')")').each(function(){
					var v = $(this).val();
					if (value == v) {
						bool = false;
						return;
					}
				})

				if (!bool) {
					toastr.info('Category is exist, please check your value');
					App_set_ticket.LoadSelectOptionCategory(selector,'transfer_to');
				}
			}	
					
		},

		SubmitSetActionCreate :function(selector){
			var received = [];
			var received_details = [];
			var transfer_to = [];
			var postupdate_ticket = {
				ID : DataTicket[0].ID,
				action : 'update',
				data : {
						TicketStatus : 2,
						},
			};

			var update_ticket = postupdate_ticket;
			var validation = App_set_ticket.validation();
			if (validation) {
				$('.form-assign-to').each(function(){
					var itsme = $(this);
					var CategoryReceivedID =  itsme.find('.input_assign_to[name="CategoryReceivedID"] option:selected').val();
					var MessageReceived =  itsme.find('.input_assign_to[name="MessageReceived"]').val();
					var DueDate =  itsme.find('.input_assign_to[name="DueDate"]').val();
					var NIP =  itsme.find('.input_assign_to[name="NIP"]').val();
					var tempreceived= {
						DepartmentReceivedID : DepartmentID,
						CategoryReceivedID : CategoryReceivedID,
						MessageReceived : MessageReceived,
						ReceivedBy : sessionNIP,
					};

					var postreceived = {
						ID : DataReceived[0].ID,
						action : 'update',
						data : tempreceived,
					}

					received = postreceived;
					
					var tempreceived_details_arr = [];
					for (var i = 0; i < NIP.length; i++) {
						var tempreceived_details = {
							ReceivedID : DataReceived[0].ID,
							DueDate : DueDate,
							Status : '1',
							NIP : NIP[i],
						}
						tempreceived_details_arr.push(tempreceived_details);
					}

					var postreceived_details = {
						ID : '',
						action : 'insert',
						data : tempreceived_details_arr,
					};

					received_details = postreceived_details;
				})

				$('.form-transfer-to').each(function(){
					var Index = $('.form-transfer-to').index(this);
					var Index = parseInt(Index)+1;
					var itsme = $(this);
					var CategoryReceivedID =  itsme.find('.input_transfer_to[name="CategoryReceivedID"] option:selected').val();
					var DepartmentReceivedID =  itsme.find('.input_transfer_to[name="CategoryReceivedID"] option:selected').attr('code');
					var MessageReceived =  itsme.find('.input_transfer_to[name="MessageReceived"]').val();
					var tempreceived = {
						TicketID : DataTicket[0].ID,
						DepartmentReceivedID : DepartmentReceivedID,
						CategoryReceivedID : CategoryReceivedID,
						MessageReceived : MessageReceived,
						// ReceivedBy : sessionNIP,
						SetAction : '1',
						Flag : '1',
					};

					if (received.length == 0) {
						var postreceived = {
							ID : DataReceived[0].ID,
							action : 'update',
							data : {
								SetAction : '0',
								ReceivedStatus : "1",
								ReceivedBy : sessionNIP,
								DepartmentTransferToID : DepartmentReceivedID,
								// CategoryReceivedID : CategoryReceivedID,
							},
							CreatedBy : sessionNIP,
							NoTicket : DataTicket[0].NoTicket,
						}

						transfer_to.push(postreceived);
					}
					else
					{
						var postreceived = {
							ID : DataReceived[0].ID,
							action : 'update',
							data : {
								DepartmentTransferToID : DepartmentReceivedID,
								// CategoryReceivedID : CategoryReceivedID,
							},
							CreatedBy : sessionNIP,
							NoTicket : DataTicket[0].NoTicket,
						}

						transfer_to.push(postreceived);
					}

					var postreceived = {
						ID : '',
						action : 'insert',
						data : tempreceived,
						CreatedBy : sessionNIP,
					}

					transfer_to.push(postreceived);

				})

				if (confirm('Are you sure ?')) {
					loadingStart();
				    loading_button2(selector);
				    var url = base_url_js+"rest_ticketing/__event_ticketing";
				    var dataform = {
				        action : 'received',
				        auth : 's3Cr3T-G4N',
				        data : {
				        	received : received,
				        	received_details : received_details,
				        	transfer_to : transfer_to,
				        	update_ticket : update_ticket,
				        },
				        notifParams : {
				        	NoTicket : DataTicket[0].NoTicket,
				        	DepartmentHandler : DepartmentID,
				        	CreatedBy : sessionNIP,
				        },
				    };
				    // console.log(dataform);return;
				    var token = jwt_encode(dataform,'UAP)(*');
				    AjaxSubmitRestTicketing(url,token).then(function(response){
				        if (response.status == 1) {
				        	toastr.success('Success');
				        	setInterval(function(){
				        	 window.location.href = base_url_js+'ticket/ticket-today'; 
				        	}, 3000);
				            
				        }
				        else
				        {
				            toastr.error(response.msg);
				            end_loading_button2(selector);
				        }
				    }).fail(function(response){
				       toastr.error('Connection error,please try again');
				       end_loading_button2(selector);  
				       loadingEnd(1000); 
				    })
				}
			}
			

		},

		validation : function(){
			var bool = true;
			$('.form-assign-to').each(function(){
				var Index = $('.form-assign-to').index(this);
				var Index = parseInt(Index)+1;
				var itsme = $(this);
				var CategoryReceivedID =  itsme.find('.input_assign_to[name="CategoryReceivedID"] option:selected').val();
				var MessageReceived =  itsme.find('.input_assign_to[name="MessageReceived"]').val();
				var DueDate =  itsme.find('.input_assign_to[name="DueDate"]').val();
				var NIP =  itsme.find('.input_assign_to[name="NIP"]').val();
				// if (MessageReceived == '' ||  MessageReceived == undefined || NIP == null || NIP == undefined ) {
				if (NIP == null || NIP == undefined ) {
					// toastr.info('Please check input Assign To on index of '+Index);
					toastr.info('Please check input Assign To');
					bool = false;
					return;
				}
			})

			if (bool) {
				$('.form-transfer-to').each(function(){
					var Index = $('.form-transfer-to').index(this);
					var Index = parseInt(Index)+1;
					var itsme = $(this);
					var CategoryReceivedID =  itsme.find('.input_transfer_to[name="CategoryReceivedID"] option:selected').val();
					var MessageReceived =  itsme.find('.input_transfer_to[name="MessageReceived"]').val();
					if (MessageReceived == '' ||  MessageReceived == undefined) {
						toastr.info('Please check input Transfer To on index of '+Index);
						bool = false;
						return;
					}
				})
			}

			return bool;
			
		},

		Loaded : function(){
			this.Styleimgfitter();
			var selector = $('#FormAssignTo');
			if (Authent == null) {
				var selector_AssignTo = $('#PageAssignTo');
				selector_AssignTo.html('<p style ="color:red;">Your not authorize in this page, please see status in ticket data</p>');
				$('.btn-add-transfer_to').remove();
				$('#btnSetAction').remove();
				var DataGet = DataTicket[0];
				var htmlGetProgressList =  AppModalDetailTicket.tracking_list_html(DataGet);
				$('#ShowProgressList').html(htmlGetProgressList);
			}
			else
			{
				App_AssignTo.DomContentForm(selector);
			}
			
		},

		Styleimgfitter : function(){
			$('.img-fitter').imgFitter({
			    // CSS background position
			    backgroundPosition: 'center center',
			    // for image loading effect
			    fadeinDelay: 400,
			    fadeinTime: 1200
			});
		},	
	};

	$(document).ready(function(){
		App_set_ticket.Loaded();
	})

	$(document).off('click', '#btnSetAction').on('click', '#btnSetAction',function(e) {
	   var selector = $(this);
	   App_set_ticket.SubmitSetActionCreate(selector);
	})

	$(document).off('click', '.btn-add-transfer_to').on('click', '.btn-add-transfer_to',function(e) {
	   var selector = $('#FormTransferTo');
	   App_transfer_to.DomContentForm(selector);
	})

	$(document).off('click', '.btn-add-assign_to').on('click', '.btn-add-assign_to',function(e) {
	   var selector = $('#FormAssignTo');
	   if ($('.form-assign-to').length == 0) {
	   	App_AssignTo.DomContentForm(selector);
	   }
	})

	$(document).off('click', '.removeRowtransferTo').on('click', '.removeRowtransferTo',function(e) {
	   var selector = $(this);
	   App_transfer_to.DomContentRemove(selector);
	})
	
	$(document).off('click', '.removeRowassignTo').on('click', '.removeRowassignTo',function(e) {
	   var selector = $(this);
	   App_AssignTo.DomContentRemove(selector);
	})
	
	$(document).off('change', '.input_assign_to[name="CategoryReceivedID"]').on('change', '.input_assign_to[name="CategoryReceivedID"]',function(e) {
	  var selector = $(this);
	  var value = $(this).val();
	  App_set_ticket.CategoryChangeEvent(selector,value);
	})	

	$(document).off('change', '.input_transfer_to[name="CategoryReceivedID"]').on('change', '.input_transfer_to[name="CategoryReceivedID"]',function(e) {
	  var selector = $(this);
	  var value = $(this).val();
	  App_set_ticket.CategoryChangeEvent(selector,value,'transfer_to');
	})	
</script>