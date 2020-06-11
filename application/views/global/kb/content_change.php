<?php if (count($G_data) == 0): ?>
	<div class="thumbnail" style="color: red; padding: 20px;">No Result Data</div>
<?php else: ?>
	<?php for($i = 0; $i < count($G_data); $i++): ?>
		<?php $no = $i+1 ?>
        <li class="list-group-item item-head">
            <a href="javascript:void(0)" data-toggle="collapse" data-target="#<?php echo $i ?>">
                <span class="numbering"><b><?php echo $no; ?></b></span>
                <span class="info"><b><?php echo $G_data[$i]['Type']?></b></span>
            </a>
						<div id="<?php echo $i ?>" class="collapse detailKB">
								<ul class="list-group">
										<?php $data = $G_data[$i]['data'];

										?>
										<?php for($j = 0; $j < count($data); $j++): ?>
												<li class="list-group-item" data-contentid="<?=$data[$j]['ID']?>" data-type="knowledge_base"><a href="javascript:void(0)" data-toggle="collapse" data-target="#<?php echo $i.'__'.$j ?>">
																<?php echo $data[$j]['Desc'] ?>
																<span class="pull-right viewers">
																<?php if(!empty($data[$j]['CountRead']->Total)){ ?>
					                                              <span class="text-success"><i class="fa fa-check-square"></i> has bean read <span class="total-read"><?=$data[$j]['CountRead']->Total?></span> times</span>
				                                              	<?php } ?>
				                                              	</span>
														</a>
														<div id="<?php echo $i.'__'.$j ?>" class="collapse">
																<div style="margin-top: 15px;margin-bottom: 15px;">
																		<a class="btn btn-default <?php if($data[$j]['File']==''||$data[$j]['File']==null || $data[$j]['File']=='unavailabe.jpg'){echo 'hide';} ?>" style="display: inline;" href="<?php echo serverRoot.'/fileGetAny/kb-'.$data[$j]['File'] ?>" target="_blank"><i class="fa fa-download margin-right"></i> PDF File</a>
																			<?php if ($selected ==$this->session->userdata('PositionMain')['IDDivision']): ?>
																			  <a href="javascript:void(0);" class="btnActRemove" data-id="<?= $data[$j]['ID']; ?>" data-no="'+i+'">Remove</a>
																			<?php endif; ?>
																</div>
														</div>

												</li>
										<?php endfor ?>
								</ul>
						</div>
        </li>
	<?php endfor ?>
<?php endif ?>
