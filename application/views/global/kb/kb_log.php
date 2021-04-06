<?php $filter = $this->session->userdata('tbl_kb_log'); ?>
<div class="row">
	<div class="col-md-12">
			<table class = "table table-bordered" id = "tbl_kb_log">
				<thead>
					<tr class="column">
					    <?php
					    foreach ($tbl_kb_log['columns'] as $key => $column) {
					        echo '<th style="' . (isset($column['width']) ? 'width:' . $column['width'] : '') . '" ' . (isset($column['id']) ? 'id="' . $column['id'] . '"' : '') . ' class="' . (isset($column['class']) ? $column['class'] : '') . '" data-data="' . $key . '" data-sort="' . (isset($column['sort']) ? $column['sort'] : '') . '">' . $column['title'] . '</th>';
					    }
					    ?>
					</tr>
					<tr class="filterSearch_log_content">
					    <?php foreach ($tbl_kb_log['columns'] as $key => $column) { ?>
					        <td>
					            <?php if ($column['filter']) { ?>
					                <?php if ($column['filter']['type'] == 'text') : ?>
					                    <div class="input-group">
					                        <span class="input-group-addon"><i class="icon-search"></i></span>
					                        <input type="text" name="<?php echo $key; ?>" class="form-control form-control-sm" autocomplete="off" value="<?php echo ($filter && isset($filter[$key])) ? $filter[$key] : ''; ?>">
					                    </div>
					                <?php elseif ($column['filter']['type'] == 'dropdown') : ?>
					                    <?php echo form_dropdown($key, $column['filter']['options'], ($filter && isset($filter[$key])) ? $filter[$key] : NULL, ['class' => 'col-md-12']); ?>
					                <?php elseif ($column['filter']['type'] == 'action') : ?>
					                    <button type="button" class="btn btn-primary btn-sm"><i class="icon-filter3"></i></button>
					                    <?php endif; ?>
					                <?php } ?>
					        </td>
					    <?php } ?>
					</tr>
				</thead>
				<tbody>

				</tbody>
		   </table>
	</div>
</div>

<script type="text/javascript">
	const dateNowLogContent = '<?php echo date('Y-m-d') ?>';
</script>

<script type="text/javascript" src="<?php echo base_url('js/kb/'); ?>kb_log.js"></script>