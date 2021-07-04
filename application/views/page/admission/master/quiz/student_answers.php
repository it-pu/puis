<style>
    .panel-answer {
        border-top: 1px solid #ccc;
        background: #F5F5F5;
        padding: 10px 15px 10px 15px;
        margin-top: 15px;
        border-bottom-right-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    .checkbox input[type="checkbox"]:disabled + label, .radio input[type="radio"]:disabled + label {
        opacity: 1;
    }
</style>
<div class="row">
	<div class="col-md-10 col-md-offset-1" style="margin-bottom: 50px;">

	    <div class="alert alert-info text-center" id="showCourse"></div>
	    
	    <div id="loadDataTable"></div>

	</div>
</div>

<script type="text/javascript">
	const token = "<?= $token; ?>";
	const dataToken = jwt_decode(token);
	const dataQuiz = dataToken['dataQuiz']['Quiz'][0];
	const module_url = "<?php echo $module_url ?>";
	let CorrectionButton = 1;
console.log(dataToken);
	const load_default = async() => {
		let showDuration = '';
		if (dataQuiz['DurationType'] == 'Flexi') {
			showDuration = dataQuiz['DurationFlexi'] +' Minutes';
		}
		else
		{
			showDuration = dataQuiz['DurationFixedStart'] + ' - '+dataQuiz['DurationFixedEnd'];
		}

		$('#showCourse').html('<h3>'+dataToken.TAText+' || '+dataToken.Course+' || '+dataToken.Session+' || '+showDuration+'</h3>');

		loadDataAnswers();
		
	}

	const loadDataAnswers = async() => {
		const data = {
			QuizID : dataToken.QuizID,
		};

		var token = jwt_encode(data,'UAP)(*');
		let url = module_url + 'loadDataAnswers';

		try{
			const jsonResult =  await AjaxSubmitFormPromises(url,token);
			var tr = '';
			$.each(jsonResult,function (i,v) {

				var StartSession = (v.StartSession!=null && v.StartSession!='')
				    ? moment(v.StartSession).format('DD MMM YYYY HH:mm') : '';

				var WorkDuration = (v.WorkDuration!='' && v.WorkDuration!=null)
				    ? timeConvert(v.WorkDuration) : '-';

				var viewScore = (v.Score!='' && v.Score!=null) ? v.Score : '-';
				var ShowScore = (parseInt(v.ShowScore)>0) ? viewScore
				    : '<span class="label label-warning">waiting</span>';


				var SubmittedAt = (v.SubmittedAt!=null && v.SubmittedAt!='')
				    ? moment(v.SubmittedAt).format('DD MMM YYYY HH:mm') : "hasn't sent a response";

				var trSubmittedAt = (v.SubmittedAt!=null && v.SubmittedAt!='') ? ''
				    : 'style="background: #fff1c6;color: #a76300;"';

				var btnLabel = (parseInt(v.ShowScore)>0) ? 'View Details'
				    : 'Correction';


				v.CorrectionButton = CorrectionButton;

				var tokenDetail = jwt_encode(v,'UAP)(*');

				var btnCorrection = '-';


				var btnCorrection = (v.SubmittedAt!=null && v.SubmittedAt!='')
				? '<button data-token="'+tokenDetail+'" id="btnCorrection_'+v.QuizStudentID+'" class="btn btn-default btn-sm btnCorrection">'+btnLabel+'</button>'
				: '-';

				var showStatus = '';
				if (v.Pass == 0) {
					showStatus = '<i class="fa fa-question-circle" aria-hidden="true"></i>';
				}
				else if(v.Pass == 1){
					showStatus = '<i class="fa fa-check-circle" style="color: green;"></i>';
				}
				else
				{
					showStatus = '<i class="fa fa-minus-circle" style="color: red;"></i>';
				}

				var showKaprodi = '<span style="color:#d29b37;">Not Review</span>';
				if (v.KaprodiReview == 1) {
					showKaprodi = '<textarea class = "form-control" disabled>'+v.KaprodiNote+'</textarea>';
				}


				tr = tr+'<tr>' +
				    '<td>'+(i+1)+'</td>' +
				    '<td style="text-align: left;"><b>'+v.Name+'</b><br/>'+v.Email+'<br/><span style ="color:blue;">Token('+v.token_beasiswa+')</span>'+'<br/>'+v.SchoolName+'<br/>'+v.Mobile+'</b>'+'</td>' +
				    '<td>'+StartSession+'</td>' +
				    '<td '+trSubmittedAt+'>'+SubmittedAt+'</td>' +
				    '<td>'+WorkDuration+'</td>' +
				    '<td id="showScode_'+v.QuizStudentID+'">'+ShowScore+'</td>' +
				    '<td>'+showStatus+'</td>'+
				    '<td>'+showKaprodi+'</td>'+
				    '<td>'+btnCorrection+'</td>' +
				    '</tr>';
			});

			$('#loadDataTable').html('<div class="table-responsive">' +
			    '        <table class="table table-bordered table-striped table-centre" id="tableList">' +
			    '            <thead>' +
			    '            <tr style="background: #eceff1;">' +
			    '                <th style="width: 1%;">No</th>' +
			    '                <th>Student</th>' +
			    '                <th style="width: 15%;">Start On</th>' +
			    '                <th style="width: 15%;">Submitted At</th>' +
			    '                <th style="width: 15%;">Work Duration</th>' +
			    '                <th style="width: 10%;">Score</th>' +
			    '                <th style="width: 5%;">Status</th>' +
			    '                <th style="width: 10%;">Kaprodi Review</th>' +
			    '                <th style="width: 10%;">Action</th>' +
			    '            </tr>' +
			    '            </thead>' +
			    '            <tbody id="listStudents">'+tr+'</tbody>' +
			    '        </table>' +
			    '    </div>');

				LoaddataTable('#tableList');



		}
		catch(err){
			console.log(err);
		}

	}

	const selectOpModalPass = (PassValue) => {
		let html ='';
		let arrOption = [
				{
					label : 'Not Set',
					value : 0
				},
				{
					label : 'Pass',
					value : 1
				},
				{
					label : 'Not Pass',
					value : -1
				},

		];

		let disabled = (PassValue == 0 ) ? '' : 'disabled';
		html = '<select class = "form-control fillPass" '+disabled+'>;'
		for (var i = 0; i < arrOption.length; i++) {
			let selected = (arrOption[i].value == PassValue) ? 'selected' : '';

			html +='<option value="'+arrOption[i].value+'" '+selected+' >'+arrOption[i].label+'</option>';
		}

		html += '</select>';

		return html;
	}

	const onClickCorrection = async(itsme) => {
		var token = itsme.attr('data-token');
		var d = jwt_decode(token);
		var ID = d.QuizStudentID;

		console.log(d);

		var data = {
		    QuizStudentID : ID,
		};

		var token = jwt_encode(data,'UAP)(*');
		var url = module_url + 'getDataAnswersDetails';
		try{
			const jsonResult = await AjaxSubmitFormPromises(url,token);
			var panelAns = '';
			var no = 1;
			var formEssay = 0;
			$.each(jsonResult,function (i,v) {

			    var viewAnswer = '';
			    if(v.Options.length>0){
			        var viewOption = '';
			        var arrAnswer = v.Answer;
			        $.each(v.Options,function (i2,v2) {

			            var ck = ($.inArray(v2.ID,arrAnswer)!==-1) ? 'checked' : '';
			            var colorOption =  ($.inArray(v2.ID,arrAnswer)!==-1) ? 'style="color:#03A9F4;"' : '';
			            var IsTheAnswer = '';
			            if(v.QTID==1 || v.QTID=='1'){
			                IsTheAnswer = (v2.IsTheAnswer==1 || v2.IsTheAnswer=='1')
			                    ? '<i class="fa fa-check-circle margin-right" style="color: green;"></i> '
			                    : '';
			            } else {
			                IsTheAnswer = (v2.IsTheAnswer==1 || v2.IsTheAnswer=='1')
			                    ? '<i class="fa fa-check-circle margin-right" style="color: green;"></i> ' +
			                    ' <b style="color: green !important;">'+v2.Point+'</b> | '
			                    : '<b style="color: red !important;">'+v2.Point+'</b> | ';
			            }

			            viewOption = viewOption+'<div class="checkbox checkbox-primary disabled" '+colorOption+'>' +
			                '      <input type="checkbox" name="opt_ck_'+no+'" id="opt_'+v2.ID+'" value="'+v2.ID+'" '+ck+' disabled>'+
			                '<label for="opt_'+v2.ID+'">'+IsTheAnswer+v2.Option+'</label>' +
			                '</div>';


			            // if(v.QTID == 1){
			            //     viewOption = viewOption+'<div class="radio radio-primary disabled" '+colorOption+'>' +
			            //         '    <input type="radio" name="opt_radio_'+no+'" id="opt_'+v2.ID+'" value="'+v2.ID+'" '+ck+' disabled>'+
			            //         '<label for="opt_'+v2.ID+'">'+IsTheAnswer+v2.Option+'</label>' +
			            //         '</div>';
			            // } else {
			            //     viewOption = viewOption+'<div class="checkbox checkbox-primary disabled" '+colorOption+'>' +
			            //         '      <input type="checkbox" name="opt_ck_'+no+'" id="opt_'+v2.ID+'" value="'+v2.ID+'" '+ck+' disabled>'+
			            //         '<label for="opt_'+v2.ID+'">'+IsTheAnswer+v2.Option+'</label>' +
			            //         '</div>';
			            // }


			        });

			        viewAnswer = '<div class="panel-answer">'+viewOption+'</div>';
			    }
			    else {
			        var viewGivenPoint = '';
			            if(v.PointAnswer=='' || v.PointAnswer==null){
				            formEssay = formEssay+1;
				            viewGivenPoint = '<div class="input-group">' +
				                '  <span class="input-group-addon">Set points</span>' +
				                '  <input type="number" class="form-control input-point-form-essay" data-maxpoint="'+v.Point+'" ' +
				                '   data-no="'+formEssay+'" id="fmEssay_'+formEssay+'" style="max-width: 150px;">' +
				                '</div>' +
				                '<div id="viewAlert_'+formEssay+'"></div>' +
				                '  <input class="hide" value="'+v.Point+'" id="fmEssay_mxpoint_'+formEssay+'">' +
				                '  <input class="hide" value="'+v.QuizStudentsDetailsID+'" id="QuizStudentsDetailsID_'+formEssay+'">';
			        	}

			        var viewEssayAnswer = (v.EssayAnswer!='' && v.EssayAnswer!=null) ? v.EssayAnswer : '';
			        viewAnswer = '<div class="panel-answer" style="margin-bottom: 15px;">'+viewEssayAnswer+'</div>'+viewGivenPoint;


			    }

			    var viewPoint = (v.PointAnswer!='' && v.PointAnswer!=null)
			        ? v.PointAnswer
			        : (parseInt(v.QTID)==3)
			            ? '<span style="color: red;">waiting</span>'
			            : 0 ;

			    panelAns = panelAns+'<div class="panel panel-default">' +
			        '    <div class="panel-heading">' +
			        '        <h4 class="panel-title">Question '+(i+1)+' - '+v.QT_Description+'' +
			        '           <div class="pull-right">Answer Points : '+viewPoint+' of '+v.Point+'</div>   ' +
			        '        </h4>' +
			        '    </div>' +
			        '    <div class="panel-body">' +
			        '      '+v.Question+viewAnswer+
			        '    </div>' +
			        '</div>';

			    no+=1;
			});

			var btnSubmit = (parseInt(formEssay)>0 || d.Pass == 0) ? '' : 'hide';
			var showAlertCorrection = '<div class = "row" style = "margin:10px;"><div class = "col-md-12"><div class="alert alert-warning" role="alert">You cannot change the points given</div></div></div>';

			//console.log(d);
			var showPassOrNot = '<div class = "row"><div class = "col-md-3">'+
									'<label>Set Status</label>'+
								'</div>'+
								'<div class = "col-md-6">'+
									selectOpModalPass(d.Pass)+
								'</div></div>';

			//$('#GlobalModalLarge .modal-header .modal-title').html(d.Name);
			$('#GlobalModalLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">'+d.Name+' ( '+d.token_beasiswa+')</h4>');

			$('#GlobalModalLarge .modal-footer').html('' +
			    '<input class="hide" id="totalEssay" value="'+formEssay+'">' +
			    '<button class="btn btn-success '+btnSubmit+'" id="btnSubmit">Submit</button>' +
			    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
			$('#GlobalModalLarge .modal-body').html('<input class="hide" value="'+ID+'" id="QuizStudentID" />'+panelAns+'' +showPassOrNot+showAlertCorrection);


			$('#GlobalModalLarge').modal({
			    'backdrop' : 'static',
			    'show' : true
			});
		}
		catch(err){
			console.log(err);
		}

	}

	$(document).on('click','.btnCorrection',function(e){
		const itsme =  $(this);
		onClickCorrection(itsme);
	})

	const checkPointEssay = (i) => {
		var v = $('#fmEssay_'+i).val();
		var maxPoint = $('#fmEssay_'+i).attr('data-maxpoint');

		if(v!=''){
		    v = parseFloat(v);
		    maxPoint = parseFloat(maxPoint);

		    if(v>maxPoint){
		        $('#viewAlert_'+i).html('<div style="margin-top: 5px;color: red;">*) The point given must be less than or equal to '+maxPoint+'</div>');
		    } else {
		        $('#viewAlert_'+i).html('');
		    }

		}
	}

	$(document).on('keyup','.input-point-form-essay',function () {
	    var i = $(this).attr('data-no');
	    checkPointEssay(i);
	});

	const onSubmit = async(itsme) =>{
		if (confirm('You cannot change the points given!, Are you sure?')) {
			var submitPoint = true;
			var dataPoint = [];
			var totalEssay = $('#totalEssay').val();
			if(parseInt(totalEssay)>0){
			    for(var i=1;i<=parseInt(totalEssay);i++){
			        var maxPoint = $('#fmEssay_mxpoint_'+i).val();

			        var fmPoint = $('#fmEssay_'+i).val();
			        var Point = (fmPoint!='' && fmPoint!=null) ? fmPoint : 0;

			        if(parseFloat(maxPoint)>=parseFloat(Point)){
			            $('#viewAlert_'+i).empty();
			            var QuizStudentsDetailsID = $('#QuizStudentsDetailsID_'+i).val();
			            var arr = {
			                QuizStudentsDetailsID : QuizStudentsDetailsID,
			                Point : Point
			            };
			            dataPoint.push(arr);
			        } else {
			            $('#viewAlert_'+i).html('<div style="margin-top: 5px;color: red;">*) The point given must be less than or equal to '+maxPoint+'</div>');
			            submitPoint = false;
			        }

			    }
			}
			if (submitPoint) {
				var QuizStudentID = $('#QuizStudentID').val();
				var data = {
				    QuizStudentID : QuizStudentID,
				    dataPoint : dataPoint,
				    Pass : $('.fillPass option:selected').val(),
				};

				var token = jwt_encode(data,'UAP)(*');
				var url = module_url + 'setPointOfEssay';
				let htmlBtn = itsme.html();
				loading_button2(itsme);
				try{
					const jsonResult = await AjaxSubmitFormPromises(url,token);
					// $('#showScode_'+QuizStudentID).html(jsonResult.NewScore);
					// $('#btnCorrection_'+QuizStudentID).html('View Details');
					await loadDataAnswers();
					$('#GlobalModalLarge').modal('hide');

				}
				catch(err){
					console.log(err);
				}

				end_loading_button2(itsme,htmlBtn);

			}
			
		}
	}

	$(document).on('click','#btnSubmit',function(e){
		const itsme = $(this);
		onSubmit(itsme);	
	})

	const timeConvert = (n) => {
	    var num = n;
	    var hours = (num / 60);
	    var rhours = Math.floor(hours);
	    var minutes = (hours - rhours) * 60;
	    var rminutes = Math.round(minutes);

	    var result =  '-';
	    if(parseFloat(rhours)<=0 && parseFloat(rminutes)>0){
	        result =  rminutes + " minute(s)"
	    } else if(parseFloat(rhours)>0 && parseFloat(rminutes)<=0){
	        result =  rhours + " hour(s)";
	    }  else if(parseFloat(rhours)>0 && parseFloat(rminutes)>0) {
	        result =  rhours + " hour(s) and " + rminutes + " minute(s)";
	    }

	    return result;
	}

	$(document).ready(function(e){
		load_default();
	})
</script>