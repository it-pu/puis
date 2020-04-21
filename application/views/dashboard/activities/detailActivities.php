<style type="text/css">
	#detail-attendance .tabulasi-emp > ul > li.active > a{background:#428bca;color:#fff;border:1px solid #428bca;}
    #detail-attendance .cursor{cursor: pointer;}
    #detail-attendance .cursor-disable{cursor: no-drop;}
    #detail-attendance .profile-info > h3{margin:0px;padding: 5px}
    #detail-attendance .profile-info > h3:first-child{font-weight: bold;text-transform: uppercase; }
    .half-circle{border-radius: 10px;background: #eee;font-weight: bold;font-size: 12px;padding: 2px 5px;margin: 0px 8px }
    .half-circle.blue{background: #2fa4e7;color: #fff}
    .half-circle.orange{background: #dd5600;color: #fff}
    .bgx{border:1px solid #ddd;padding: 6px 13px;font-weight: normal;border: 1px solid rgba(0, 0, 0, 0.13);}
    .bgx.green{background-color: #51a351;color:#fff;}
    .bgx.red{background-color: #bd362f;color:#fff;}
    .bgx.blue{background-color: #3968c6;color:#fff;}
</style>
<div id="detail-attendance">
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-1">
                            <?php $imgPr = (!empty($employee->Photo) && file_exists('./uploads/employees/'.$employee->Photo)) ? base_url('uploads/employees/'.$employee->Photo) : base_url('images/icon/userfalse.png'); ?>
                            <img class="img-thumbnail" id="imgThumbnail" src="<?php echo $imgPr; ?>" style="max-width: 100px;width: 100%;">                            
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-info">
                                <h3><?=(!empty($employee->TitleAhead) ? $employee->TitleAhead.' ' : '').$employee->Name.(!empty($employee->TitleBehind) ? ', '.$employee->TitleBehind : '')?></h3>
                                <h3><?=$employee->NIP?></h3>
                                <h3><?=$employee->EmailPU?></h3>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-info">
                                <h3>Division <?=$employee->DivisionMain_?></h3>
                                <h3><?=$employee->PositionMain_?></h3>                                
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-right">
                                <span class="bgx <?=(($employee->StatusEmployeeID == 2) ? 'green': ( ($employee->StatusEmployeeID == 1) ? 'blue':'red' ) )?>">
                                <i class="fa fa-handshake-o"></i> <?=strtoupper($employee->EmpStatus)?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading" style="padding:0px">
					<div class="row">
						<div class="col-sm-10">
							<h4 class="panel-title" style="padding:10px 15px"><i class="fa fa-bars"></i> List Activity</h4>							
						</div>
						<div class="col-sm-2" style="border-left:1px solid #ddd;padding:10px 15px">
							<b>TOTAL ACTIVITY : <?=count($TotalActivity);?></b>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered" id="table-attendance">
							<thead>
								<tr>
									<th width="5%">No</th>
									<th>Access Date</th>
									<th>Activity Path</th>
								</tr>
							</thead>
							<tbody>
							<?php if(!empty($attendance)){ $no=1;
							foreach ($attendance as $v) { ?>
								<tr>
									<td><?=$no++?></td>
									<td><?=$v->AccessedOn?></td>
									<td><?=$v->URL?></td>
								</tr>
							<?php } }else{ echo "<tr><td colspan='3'>Empty data</td></tr>"; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("#table-attendance").DataTable();
	});
</script>