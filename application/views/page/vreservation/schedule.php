<style type="text/css">
	.imgtd {
	    position: relative;
	    text-align: center;
	    color: white;
	}
	/* Centered text */
	.centeredimgtd {
	    position: absolute;
	    top: 50%;
	    left: 50%;
	    transform: translate(-50%, -50%);
	}

	.imgtd img {
	    max-width: 80px;
	}

	.panel-red {
		font-size: 10px;
		color: #fff;
		font-weight: bold;
		text-align: center;
		padding: 1px;
		background: #e98180;
	    /*max-width: 100px;*/
	    height: 45px;
	    border: 1px dotted #333;
	}

	.panel-green {
		font-size: 10px;
		color: #fff;
		font-weight: bold;
		text-align: center;
		padding: 1px;
		background: #20c51b;
	    /*max-width: 100px;*/
	    height: 45px;
	    border: 1px dotted #333;
	}
	.panel-blue {
		font-size: 10px;
		color: #fff;
		font-weight: bold;
		text-align: center;
		padding: 1px;
		background: #6ba5c1;
	    /*max-width: 100px;*/
	    height: 45px;
	    border: 1px dotted #333;
	}
	.panel-orange {
		font-size: 10px;
		color: #fff;
		font-weight: bold;
		text-align: center;
		padding: 1px;
		background: #ffb848;
	    /*max-width: 100px;*/
	    height: 45px;
	    border: 1px dotted #333;
	}

	/*.table-responsive {
	  
	  height: auto !important;  
	  max-height: 450px;
	  overflow-y: auto;
	}*/

	.pointer {cursor: pointer;}


	#tblFreeze tbody {
	    display:block;
	    height:520px;
	    overflow:auto;
	}
	#tblFreeze thead,#tblFreeze tbody tr {
	    display:table;
	    width:100%;
	    table-layout:fixed; /* even columns width , fix width of table too*/
	}
	#tblFreeze thead {
	    /*width: calc( 100% - 1em ) scrollbar is average 1em/16px width, remove it from thead width */
	    <?php $aa = count($getRoom);?>
	    	
	    <?php if ($aa > 10): ?>
	    	width: calc( 100% - 1.2em )
	    <?php else: ?>
	    	width: calc( 100% - 0em )	
	    <?php endif ?>
	     
	}
	#tblFreeze table {
	    width:400px;
	}

</style>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-xs-2" style="margin-top: 40px">
				<button id = "PreviousDate" class="btn btn-success dateSearch" date = "<?php echo $PreviousDate ?>"> << Previous</button>
			</div>
			<div class="col-xs-3 col-md-offset-2">
				<div class="form-group">
					<label>Category Room</label>
					<select class="form-control" id = "OpCategoryRoom">
						<option value="0">No Selected</option>
						<?php for($i = 0; $i < count($OpCategory); $i++): ?>

							<option value="<?php echo $OpCategory[$i]['ID'] ?>"><?php echo $OpCategory[$i]['NameEng'] ?></option>	
						<?php endfor ?>  
					</select>
				</div>
			</div>
			<div class="col-xs-2 col-md-offset-3" align="right" style="margin-top: 40px">
				<button id = "NextDate" class="btn btn-success dateSearch" date = "<?php echo $NextDate ?>"> Next >></button>
			</div>
		</div>
	</div>
</div>
<div class="row" style="margin-left: 0px;margin-right: 0px; margin-top: 10px">
	<div class="table-responsive table-area" style="overflow-x:auto;">
		<table class="table table-bordered table2" id = "tblFreeze">
		    <thead>
		    	<tr>
			    	<th width="6%">
			    		Room
			    	</th>
			    	<?php for($i = 0; $i < count($arrHours); $i= $i + 2): ?>
			    		<th colspan="2" style="text-align: left;"><?php echo $arrHours[$i] ?></th>
			    	<?php endfor ?>
			    </tr>	   
		    </thead>
		    <tbody>
		    	<?php for($i = 0; $i < count($getRoom); $i++): ?>
		    		<tr>
		    			<td width="6%"><?php echo $getRoom[$i]['Room'] ?></td>
						<?php $countTD =  count($arrHours) ?>
							<?php for($j = 0; $j < $countTD; $j++): ?>
								<?php $bool = false ?>
								<?php for($k = 0; $k < count($data_pass); $k++): ?>
									<?php $implode = implode('@@', $data_pass[$k]) ?>
									<?php $converDTS = date("h:i a", strtotime($data_pass[$k]['start'])); ?>
									<?php $converDTSWr = date("h:i a", strtotime($data_pass[$k]['startWr'])); ?>
									<?php $converDTE = date("h:i a", strtotime($data_pass[$k]['end'])); ?>
									<?php $converDTEWr = date("h:i a", strtotime($data_pass[$k]['endWr'])); ?>
									<?php if ($data_pass[$k]['room'] == $getRoom[$i]['Room'] && $converDTS == $arrHours[$j]): ?>
										<?php if ($data_pass[$k]['approved'] == 1): ?>
											<?php 
												$bc = '#e98180';
												$bc2 = 'panel-red';
												if ($data_pass[$k]['NIP'] != 0) {
													$bc = '#20c51b';
													$bc2 = 'panel-green';
													$NamaMataKuliah = '';
													$NamaDosen = '';
													$jumlahMHS = '';
													//$Details = $data_pass[$k]['user'];
													$Details = '';
												}
												else
												{
													$NamaMataKuliah = $data_pass[$k]['NamaMataKuliah'];
													$NamaDosen = $data_pass[$k]['NamaDosen'];
													$jumlahMHS = $data_pass[$k]['jumlahMHS'];
													// $Details = $NamaMataKuliah.' / '.$NamaDosen.' / '.$jumlahMHS;
													$Details = '';
													if ($data_pass[$k]['user'] == 'Academic TimeTables EX') {
														//$Details = $data_pass[$k]['user'].'<br>'.$NamaMataKuliah.' / '.$NamaDosen.' / '.$jumlahMHS;
														$Details = '';
													}
												}
											?>
											<td class="<?php echo $bc2 ?> pointer" style="background-color: <?php echo $bc ?>;" room = "<?php echo $getRoom[$i]['Room'] ?>" colspan="<?php echo $data_pass[$k]['colspan'] ?>" room = "<?php echo $getRoom[$i]['Room'] ?>" id = "draggable" title="<?php echo $converDTSWr ?>-<?php echo $converDTEWr?>&#013;<?php echo 'Request : '.$data_pass[$k]['user'] ?>&#013;Agenda : <?php echo $data_pass[$k]['agenda'] ?>" user = "<?php echo $data_pass[$k]['user'] ?>" NIP = "<?php echo $data_pass[$k]['NIP'] ?>" data = "<?php echo $implode; ?>">
												<?php echo $Details ?>
											</td>
											<?php $bool = true ?>
											<?php $j = $j + (int)$data_pass[$k]['colspan'] - 1 ?>
											<?php break; ?>
										<?php else: ?>
											<td class="panel-orange pointer" style="background-color: #ffb848;" room = "<?php echo $getRoom[$i]['Room'] ?>" colspan="<?php echo $data_pass[$k]['colspan'] ?>" room = "<?php echo $getRoom[$i]['Room'] ?>" title="<?php echo $converDTS ?>-<?php echo $converDTE?>&#013;<?php echo 'Request : '.$data_pass[$k]['user'] ?>&#013;Agenda : <?php echo $data_pass[$k]['agenda'] ?>" user = "<?php echo $data_pass[$k]['user'] ?>" NIP = "<?php echo $data_pass[$k]['NIP'] ?>" data = "<?php echo $implode; ?>"><span><?php //echo $data_pass[$k]['user'] ?></span>
											</td>
											<?php $bool = true ?>
											<?php $j = $j + (int)$data_pass[$k]['colspan'] - 1 ?>
											<?php break; ?>	
										<?php endif ?>
									<?php endif ?>
								<?php endfor ?>
								<?php if (!$bool): ?>
									<td style="background-color: #6ba5c1;" class="panel-blue pointer" id = "droppable" room = "<?php echo $getRoom[$i]['Room'] ?>" title="<?php echo $arrHours[$j]?>">
									</td>			
								<?php endif ?>	
							<?php endfor ?>		
		    		</tr>
		    	<?php endfor ?>   
		    </tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	getRoom = <?php echo json_encode($getRoom)  ?>;
	arrHours = <?php echo json_encode($arrHours)  ?>;
	data_pass = <?php echo json_encode($data_pass)  ?>;
	var clickChk = "<?php echo $chkDate ?>"
	$( function() {
    	$(".panel-red").hover();
    	$(".panel-blue").hover();
    	$(".panel-orange").hover();
  	} );

  	$(document).ready(function(){
  		FuncSearchBtnDate();
  		if (clickChk == 0) {
  			$('.panel-blue').click(function(){return false;});
  			//$('.panel-orange').click(function(){return false;});
  		}

  		// get CategoryRoom
  			$("#OpCategoryRoom option").filter(function() {
  			   //may want to use $.trim in here
  			   return $(this).val() == "<?php echo $PostCategory ?>"; 
  			 }).prop("selected", true);
  			
	  		$("#OpCategoryRoom").change(function(){
	  			var divHtml = $("#schedule");
	  			var value = $(this).val();
	  			loading_page("#classroom_view");
	  			if ($("#datetime_deadline1").length) {
	  				var dateD = $("#datetime_deadline1").val();
	  			}
	  			else
	  			{
	  				var dateD = '<?php echo $date ?>';
	  			}
	  			loadDataSchedule(divHtml,dateD,value);
	  		})
  	});

  	function FuncSearchBtnDate()
  	{
  		$(".dateSearch").click(function(){
  			var get = $(this).attr('date');
  			var OpCategory = $("#OpCategoryRoom").val();
  			var divHtml = $("#schedule");
  			loading_page("#classroom_view");
  			loadDataSchedule(divHtml,get,OpCategory);
  			if ($("#datetime_deadline1").length) {
  				$("#datetime_deadline1").val(get);
  			}
  			$("#schdate").html('<i class="icon-calendar"></i> Schedule Date : '+ get);
  		})
  	}
</script>