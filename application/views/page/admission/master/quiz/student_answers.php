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


				tr = tr+'<tr>' +
				    '<td>'+(i+1)+'</td>' +
				    '<td style="text-align: left;"><b>'+v.Name+'</b><br/>'+v.Email+'<br/><span style ="color:blue;">Token('+v.token_beasiswa+')</span>'+'<br/>'+v.SchoolName+'<br/>'+v.Mobile+'</b>'+'</td>' +
				    '<td>'+StartSession+'</td>' +
				    '<td '+trSubmittedAt+'>'+SubmittedAt+'</td>' +
				    '<td>'+WorkDuration+'</td>' +
				    '<td id="showScode_'+v.QuizStudentID+'">'+ShowScore+'</td>' +
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

	const onClickCorrection = async(itsme) => {
		var token = itsme.attr('data-token');
		var d = jwt_decode(token);
		var ID = d.QuizStudentID;

		var data = {
		    QuizStudentID : ID,
		};

		var token = jwt_encode(data,'UAP)(*');
	}

	$(document).on('click','.btnCorrection',function(e){
		const itsme =  $(this);
		onClickCorrection(itsme);
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