<style>
    #listOption td:first-child {
        text-align: center;
    }

</style>
<div class="row btn-read">
	<div class="col-md-10 col-md-offset-1">
		<div class="alert alert-warning">
		    <lu>
		        <li>Cut, Copy and Paste <b>not allowed</b></li>
		        <li><b>Disabled</b> right click</li>
		    </lu>
		</div>
	</div>
</div>

<div class="row btn-read">
	<div class="col-md-12">
		<div class="panel panel-default" id="panelInput">
		    <div class="panel-heading">
		        <h4 class="panel-title">Action : Create</h4>
		    </div>
		    <div class="panel-body" style="min-height: 250px;">

		        <div class="form-group">

		            <div class="row">
		                <div class="col-md-8 hide">
		                    <label>Course</label>
		                    <div id="loadCourse"></div>
		                </div>
		                <div class="col-md-4">
		                    <label>Type Question</label>
		                    <select class="form-control" id="formTypeQuestion" style="max-width: 250px;"></select>
		                    <input class="hide" value="1" id="formTQID" />
		                </div>
		            </div>


		        </div>

		        <div class="form-group">
		            <label>Question</label>
		            <textarea class="form-control form-question area-summernote" id="formQuestion"></textarea>
		            <input class="hide" id="formSummernoteID" />
		        </div>
		        <div id="showMultiple"></div>
		        <div class="form-group">
		            <label>Note to this question (Optional)</label>
		            <textarea class="form-control form-question" id="formNote" rows="5"></textarea>
		        </div>
		    </div>
		    <div class="panel-footer text-right">
		        <button class="btn btn-success btn-add" id="btnSaveQuestion">Save</button>
		    </div>
		</div>
	</div>
</div>

<script type="text/javascript">

	window.TotalMyQuestion = "<?= $Total; ?>";
	window.NewQuestion = parseInt(TotalMyQuestion) + 1;

	var btnPasteHere = function (context) {
	    var ui = $.summernote.ui;

	    // create button
	    var button = ui.button({
	        contents: '<i class="fa fa-clipboard"/> Paste text',
	        tooltip: 'Paste text',
	        click: function () {
	            // invoke insertText method with 'hello' on editor module.

	            $('#globalModalLarge .modal-header').removeClass('hide');
	            $('#globalModalLarge .modal-header .modal-title').html('Paste text');
	            $('#globalModalLarge .modal-footer').addClass('hide');
	            $('#globalModalLarge .modal-dialog').removeClass('modal-sm modal-lg');

	            $('#globalModalLarge .modal-body').html('<div class="row"><div class="col-md-12">' +
	                '<textarea id="fillModalPaste" class="form-control" rows="10" placeholder="Paste here..."></textarea>' +
	                '<div style="text-align: right;float: right;">' +
	                '<hr/>' +
	                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
	                ' | <button type="button" class="btn btn-success" id="btnSaveModalPaste">Save</button> ' +
	                '</div></div></div>');


	            $('#globalModalLarge').on('shown.bs.modal', function () {
	                $('#fillModalPaste').focus()
	            });

	            $('#globalModalLarge').modal({
	                'backdrop' : 'static',
	                'show' : true
	            });

	            $('#btnSaveModalPaste').click(function () {
	                var fillModalPaste = $('#fillModalPaste').val();
	                context.invoke('editor.insertText', fillModalPaste);
	                $('#globalModalLarge').modal('hide');
	            });



	        }
	    });

	    return button.render();   // return button as jquery object
	};

	const loadQuestionType = async(selected = '') => {
		var url = base_url_js+'api4/__crudQuiz';
		var token = jwt_encode({action:'getQuestionType'},'UAP)(*');
		try{
			const jsonResult = await AjaxSubmitFormPromises(url,token);
			$.each(jsonResult,function (i,v) {
			    var sc = (selected==v.ID) ? 'selected' : '';
			    $('#formTypeQuestion').append('<option value="'+v.ID+'" '+sc+'>'+v.Description+'</option>');
			});
		}
		catch(err){
			toastr.info('error load data question type');
		}
	}

	const loadMultipleChoice = (TQID) => {

	    loadingStart();

	    // var TQID = $('#formTQID').val();
	    var hidePoint = (TQID==1) ? 'hide' : '';

	    var formSummernoteID = sessionNIP+'_opt_1_'+NewQuestion;

	    var pageMultiple = (TQID==1 || TQID==2)
	        ? '        <div style="text-align: right;margin-bottom: 10px;">' +
	        '            <button class="btn btn-sm btn-default" style="color: red;" id="btnRemoveOption"><i class="fa fa-minus-circle"></i> Option</button>' +
	        '            <button class="btn btn-sm btn-default" style="color: green;" id="btnAddOption"><i class="fa fa-plus-circle"></i> Option</button>' +
	        '             <input value="1" class="hide" id="totalOption" />' +
	        '        </div>' +
	        '<div class="alert alert-info '+hidePoint+'" role="alert">' +
	        '<i class="fa fa-info-circle"></i> <b>Important to know</b>' +
	        '<ul>' +
	        '<li>If the answer is an <b>incorrect answer</b>, the points must be <b>less than 0</b></li>' +
	        '<li>If the answer is the <b>correct answer</b>, the points must be <b>more than 0</b></li>' +
	        '<li>The number of correct answer points <b>must equal 100</b></li>' +
	        '</ul></div>' +
	        '        <table class="table table-bordered table-striped">' +
	        '            <thead>' +
	        '            <tr style="background: #eceff1;">' +
	        '                <th style="width: 5%;">Option</th>' +
	        '                <th>Description</th>' +
	        '                <th style="width: 15%;">Set The Answer</th>' +
	        '                <th style="width: 15%;" class="'+hidePoint+'">Point</th>' +
	        '            </tr>' +
	        '            </thead>' +
	        '            <tbody id="listOption">' +
	        '               <tr>' +
	        '                   <td>1</td>' +
	        '                   <td><textarea id="desc_1" class="form-control form-question" rows="1"></textarea>' +
	        '                       <input class="hide" id="formSummernoteID_1" value="'+formSummernoteID+'" /></td>' +
	        '                   <td>' +
	        '                       <div class="checkbox checkbox-primary">' +
	        '                           <input id="opt_1" class="setAnswer" type="checkbox">' +
	        '                           <label for="opt_1">The Answer</label>' +
	        '                       </div>' +
	        '                   </td>' +
	        '                   <td class="'+hidePoint+'" style="text-align: left;">' +
	        '                       <input id="point_1"  data-opt="1" class="form-control form-point"  max="0" type="number"/>' +
	        '                       <p id="opt_1_help" class="help-block">*) Less than 0</p>' +
	        '                   </td>' +
	        '               </tr>' +
	        '            </tbody>' +
	        '        </table>'
	        : '';

	    $('#showMultiple').html(pageMultiple);

	    if(TQID==1 || TQID==2){

	        $('#desc_1').summernote({
	            placeholder: 'Text your option...',
	            height: 200,
	            disableDragAndDrop : true,
	            toolbar: [
	                ['style', ['style']],
	                ['font', ['bold', 'underline', 'clear']],
	                ['fontname', ['fontname']],
	                ['color', ['color']],
	                ['para', ['ul', 'ol', 'paragraph']],
	                ['table', ['table']],
	                ['insert', ['link', 'picture','video']],
	                ['view', ['fullscreen','codeview', 'help']],
	                ['mybutton', ['PasteHere']]
	            ],
	            buttons: {
	                PasteHere: btnPasteHere
	            },
	            callbacks: {
	                onImageUpload: function(image) {
	                    var formSummernoteID = $('#formSummernoteID_1').val();
	                    summernote_UploadImage('#desc_1',image[0],formSummernoteID);
	                },
	                onMediaDelete : function(target) {
	                    summernote_DeleteImage(target[0].src);
	                }
	            }
	        });
	    }

	    loadingEnd(500);
	}

	const load_default = async() => {
		Global_CantAction('.btn-add');
		await loadQuestionType();
		$('#formSummernoteID').val(sessionNIP+'_question_admission_'+NewQuestion);
		loadMultipleChoice(1);

		$('#formQuestion').summernote({
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
		        ['insert', ['link', 'picture','video']],
		        ['view', ['fullscreen','codeview', 'help']],
		        ['mybutton', ['PasteHere']]
		    ],
		    buttons: {
		        PasteHere: btnPasteHere
		    },
		    callbacks: {
		        onImageUpload: function(image) {
		            var formSummernoteID = $('#formSummernoteID').val();
		            summernote_UploadImage('#formQuestion',image[0],formSummernoteID);
		        },
		        onMediaDelete : function(target) {
		            summernote_DeleteImage(target[0].src);
		        }
		    }
		});
	}

	$(document).on('change','#formTypeQuestion',function () {
	    var TQID = $('#formTypeQuestion').val();
	    $('#formTQID').val(TQID);
	    loadMultipleChoice(TQID);
	});

	$(document).on('click','#btnSaveQuestion',function () {

		const itsme =  $(this);
	    saveQuestion('',itsme);
	});

	$(document).on('click','#btnAddOption',function () {
	    var formTQID = $('#formTQID').val();
	    var hidePoint = (parseInt(formTQID)==1) ? 'hide' : '';
	    var totalOption = $('#totalOption').val();
	    totalOption = parseInt(totalOption) + 1;

	    var formSummernoteID = sessionNIP+'_opt_'+totalOption+'_'+NewQuestion;

	    $('#totalOption').val(totalOption);
	    $('#listOption').append('<tr class="tr_mtpc" id="tr_'+totalOption+'">' +
	        '<td>'+totalOption+'</td>' +
	        '<td><textarea id="desc_'+totalOption+'" class="form-control form-question" rows="1"></textarea>' +
	        '<input class="hide" id="formSummernoteID_'+totalOption+'" value="'+formSummernoteID+'" /></td>' +
	        '<td>' +
	        '<div class="checkbox checkbox-primary">' +
	        '   <input id="opt_'+totalOption+'" class="setAnswer" type="checkbox">' +
	        '   <label for="opt_'+totalOption+'">The Answer</label>' +
	        '</div>' +
	        '</td>' +
	        '<td class="'+hidePoint+'" style="text-align: left;">' +
	        '<input id="point_'+totalOption+'" data-opt="'+totalOption+'" class="form-control form-point" type="number" />' +
	        '<p id="opt_'+totalOption+'_help" class="help-block">*) Less than 0</p>' +
	        '</td>' +
	        '</tr>');

	    $('#desc_'+totalOption).summernote({
	        placeholder: 'Text your option...',
	        height: 250,
	        disableDragAndDrop : true,
	        toolbar: [
	            ['style', ['style']],
	            ['font', ['bold', 'underline', 'clear']],
	            ['fontname', ['fontname']],
	            ['color', ['color']],
	            ['para', ['ul', 'ol', 'paragraph']],
	            ['table', ['table']],
	            ['insert', ['link', 'picture','video']],
	            ['view', ['fullscreen','codeview', 'help']],
	            ['mybutton', ['PasteHere']]
	        ],
	        buttons: {
	            PasteHere: btnPasteHere
	        },
	        callbacks: {
	            onImageUpload: function(image) {
	                var formSummernoteID = $('#formSummernoteID_'+totalOption).val();
	                summernote_UploadImage('#desc_'+totalOption,image[0],formSummernoteID);
	            },
	            onMediaDelete : function(target) {
	                summernote_DeleteImage(target[0].src);
	            }
	        }
	    });


	});

	$(document).on('click','#btnRemoveOption',function () {
	    var totalOption = $('#totalOption').val();
	    totalOption = parseInt(totalOption);

	    if(totalOption>1){

	        loadingStart();

	        var SummernoteID = $('#formSummernoteID_'+totalOption).val();
	        var data = {
	            action : 'removeOptionInQuestion',
	            SummernoteID : SummernoteID
	        };
	        var token = jwt_encode(data,'UAP)(*');
	        var url = base_url_js+'api4/__crudQuiz';

	        $.post(url,{token:token},function (jsonResult) {
	            $('#tr_'+totalOption).remove();
	            totalOption = totalOption - 1;
	            $('#totalOption').val(totalOption);

	            loadingEnd(500);

	        });

	    } else {
	        alert('Firs option can not remove');
	    }

	});

	$(document).on('change','.setAnswer',function () {

	    var formTQID = $('#formTQID').val();
	    if(parseInt(formTQID)==1){
	        $('.setAnswer').prop('checked',false);
	        $(this).prop('checked',true);
	    } else if(parseInt(formTQID)==2) {
	        var ID = $(this).attr('id');
	        var opt = ID.split('opt_');
	        var kethelp = ($(this).is(':checked')) ? '*) More than 0' : '*) Less than 0';
	        $('#'+ID+'_help').html(kethelp);
	        $('#point_'+opt[1]).val('');
	    }

	    $('.setAnswer').blur();

	});

	$(document).on('keyup','.form-point',function () {

	    var opt = $(this).attr('data-opt');
	    var va = $(this).val();
	    // cek apakah point untuk jawaban benar / salah
	    if($('#opt_'+opt).is(':checked')){
	        if(parseFloat(va)<=0){
	            $(this).val(1);
	        }

	    } else {
	        if(parseFloat(va)>=0){
	            $(this).val(-1);
	        }
	    }

	});

	$(document).ready(function(e){
		load_default();
		// $('#panelInput').bind('cut copy paste', function (e) {
		//     alert('Disabled cut copy and paste');
		//     e.preventDefault();
		// });

		// //Disable mouse right click
		// $("#panelInput").on("contextmenu",function(e){
		//     alert('Disabled right click');
		//     return false;
		// });


	})

	const saveQuestion = async(action='',itsme) => {

		if (confirm('Are you sure ?')) {
			var formID = $('#formID').val();

			var formTQID = $('#formTQID').val();
			var formQuestion = $('#formQuestion').val();
			var formNote = $('#formNote').val();

			var submitQ = true;

			var dataOption = [];

			var errMsg = 'Please check the required form';

			if(formTQID==3){
			    submitQ = (formQuestion!='' && formQuestion!=null) ? true : false;
			}
			else if(formTQID==1){
			    var totalOption = $('#totalOption').val();
			    for(var i=1;i<=totalOption;i++){
			        var des = $('#desc_'+i).val();
			        submitQ = (des!='' && des!=null) ? true : false;
			        var IsTheAnswer = ($('#opt_'+i).is(':checked')) ? '1' : '0';
			        var formSummernoteID = $('#formSummernoteID_'+i).val();
			        var arrOpt = {
			            SummernoteID : formSummernoteID,
			            Option : des,
			            IsTheAnswer : IsTheAnswer
			        };

			        dataOption.push(arrOpt);
			    }
			}
			else if(formTQID==2){
			    var totalOption = $('#totalOption').val();
			    var totalPointBenar = 0;
			    var totalPointSalah = 0;
			    for(var i=1;i<=totalOption;i++){
			        var des = $('#desc_'+i).val();
			        var Point = $('#point_'+i).val();
			        submitQ = (des!='' && des!=null) ? true : false;
			        var IsTheAnswer = ($('#opt_'+i).is(':checked')) ? '1' : '0';
			        var formSummernoteID = $('#formSummernoteID_'+i).val();
			        var arrOpt = {
			            SummernoteID : formSummernoteID,
			            Option : des,
			            IsTheAnswer : IsTheAnswer,
			            Point : (Point!='' && Point!=null) ? Point : 0
			        };

			        if($('#opt_'+i).is(':checked')){
			            totalPointBenar = parseFloat(totalPointBenar) + parseFloat(Point);
			        } else {
			            totalPointSalah = parseFloat(totalPointSalah) + parseFloat(Point);
			        }

			        dataOption.push(arrOpt);
			    }

			    if(totalPointBenar==100 && totalPointSalah==-100){
			        submitQ = true;
			    } else {
			        errMsg = 'The number of points for correct answers must be equal to 100 ' +
			            'and the number of points for correct answers must be equal to -100';
			        submitQ = false;
			    }

			}

			var data = {
			    action : 'saveQuestion',
			    ID : (formID!='' && formID!=null) ? formID : '',
			    SummernoteID : $('#formSummernoteID').val(),
			    NIP : sessionNIP,
			    dataQustion : {
			        QTID : formTQID,
			        Question : formQuestion,
			        Note : (formNote!='' && formNote!=null) ? formNote : ''
			    },
			    dataOption : dataOption
			};

			var token = jwt_encode(data,'UAP)(*');
			var url = '<?php echo $module_url.'save' ?>';

			loadingStart();
			try{
				const response = await AjaxSubmitFormPromises(url,token);
				if (response == 1) {
					// save & add to quiz
					toastr.success('Data saved','Success');

					setTimeout(function () {
					    window.location.href='';
					},500);
				}
				else
				{
					toastr.info('error save data'); 
				}
				
			}
			catch(err){
				console.log(err)
				toastr.info('error save data'); 
			}

			loadingEnd(500);
		}

	}
</script>