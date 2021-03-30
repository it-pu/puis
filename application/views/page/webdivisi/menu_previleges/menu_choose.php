<?php $PositionMain = $this->session->userdata('PositionMain')?>
<div class="" style="margin-top: 30px;">
     <div class="col-md-12">
        <div class="tabbable tabbable-custom tabbable-full-width">
            <ul class="nav nav-tabs">
                <?php if ($PositionMain['IDDivision'] == 12): ?>
                    <li class="<?php echo ($this->uri->segment(2) == 'setting' &&  ($this->uri->segment(3) == '' || $this->uri->segment(3) == null)  ) ? 'active' : '' ?>">
                        <a href="<?php echo base_url('prodi/setting'); ?>">Menu</a>
                    </li>
                    <li class="<?php echo ($this->uri->segment(2) == 'setting' &&  $this->uri->segment(3) == 'submenu'  ) ? 'active' : '' ?>">
                        <a href="<?php echo base_url('prodi/setting/submenu'); ?>">Sub Menu</a>
                    </li>
                <?php endif ?>
                <li class="<?php echo ($this->uri->segment(2) == 'setting' &&  $this->uri->segment(3) == 'user_access'  ) ? 'active' : '' ?>">
                    <a href="<?php echo base_url('prodi/setting/user_access'); ?>">User Access</a>
                </li>
            </ul>
            <div style="padding-top: 30px;border-top: 1px solid #cccccc">
                <?php echo $page; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var IDDivision = "<?php echo $PositionMain['IDDivision'] ?>";
    var disAccess = (IDDivision != 12) ? 'disabled' : '';
    var hideClass = (IDDivision != 12) ? 'hide' : '';
</script>
