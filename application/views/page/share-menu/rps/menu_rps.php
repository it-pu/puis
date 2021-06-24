
<div class="" style="margin-top: 30px;">
    <div class="col-md-12">
        <div class="tabbable tabbable-custom tabbable-full-width">
            <?php $activeMenu = $this->uri->segment(1); ?>
            <ul class="nav nav-tabs">
                <li class="<?= ($this->uri->segment(2) == 'data-curriculum' || $this->uri->segment(2) == 'review-RPS' || $this->uri->segment(2) == 'manage-CPL' || $this->uri->segment(2) == 'list-CPMK' || $this->uri->segment(2) == 'desc-MK' || $this->uri->segment(2) == 'bahan-kajian' || $this->uri->segment(2) == 'list-rps') ? 'active' : '' ?>">
                    <a href="<?php echo base_url('rps/data-curriculum'); ?>">Data Curriculum</a>
                </li>
                <li class="hide <?= ($this->uri->segment(2) == 'list-rps') ? 'active' : '' ?>">
                    <a href="<?php echo base_url('rps/list-rps'); ?>">List RPS</a>
                </li>
                <li class=" <?= ($this->uri->segment(2) == 'master-CPL' ) ? 'active' : '' ?>">
                    <a href="<?php echo base_url('rps/master-CPL'); ?>">Master CPL</a>
                </li>
                <li class="hide <?= ($this->uri->segment(2) == 'create-question') ? 'active' : '' ?>">
                    <a href="<?php echo base_url('survey/create-question'); ?>">Create Question</a>
                </li>
                <li class="hide <?= ($this->uri->segment(2) == 'list-CPMK') ? 'active' : '' ?>">
                    <a href="<?php echo base_url('rps/list-CPMK'); ?>">List CPMK</a>
                </li>
                <li class="hide <?= ($this->uri->segment(2) == 'desc-MK') ? 'active' : '' ?>">
                    <a href="<?php echo base_url('rps/desc-MK'); ?>">Deskripsi MK</a>
                </li>
                <li class="hide <?= ($this->uri->segment(2) == 'bahan-kajian') ? 'active' : '' ?>">
                    <a href="<?php echo base_url('rps/bahan-kajian'); ?>">Bahan Kajian</a>
                </li>
            </ul>

            <div style="padding-top: 30px;border-top: 1px solid #cccccc">
                <?php echo $page; ?>
            </div>

        </div>
    </div>
</div>





