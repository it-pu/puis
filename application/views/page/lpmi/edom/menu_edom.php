


<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
        <li class="<?php if($this->uri->segment(3)=='list-lecturer' || $this->uri->segment(3)=='') { echo 'active'; } ?>">
            <a href="<?php echo base_url('lpmi/lecturer-evaluation/list-lecturer'); ?>">List Lecturer</a>
        </li>
        <li class="<?php if($this->uri->segment(3)=='question') { echo 'active'; } ?>">
            <a href="<?php echo base_url('lpmi/lecturer-evaluation/list-lecturer'); ?>">Question</a>
        </li>
        <!--        <li class="--><?php //if($this->uri->segment(3)=='permanent-lecturer') { echo 'active'; } ?><!--">-->
        <!--            <a href="--><?php //echo base_url('human-resources/monitoring-attendance/permanent-lecturer'); ?><!--">Resume (Coming Soon)</a>-->
        <!--        </li>-->
    </ul>
    <div style="padding-top: 30px;border-top: 1px solid #cccccc">

        <div class="row">
            <div class="col-md-12">
                <?php echo $page; ?>
            </div>
        </div>

    </div>
</div>