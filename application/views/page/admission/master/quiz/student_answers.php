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

		}
		catch(err){
			console.log(err);
		}

	}

	$(document).ready(function(e){
		load_default();
	})
</script>