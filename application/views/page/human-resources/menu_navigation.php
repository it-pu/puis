<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">

        <!--=== Navigation ===-->

        <ul id="nav">


            <li class="<?php if($this->uri->segment(2)=='employees'){echo"current";}?>">
                <a href="<?php echo base_url('human-resources/employees');?>">
                    <i class="fa fa-users"></i>
                    Master Employees
                </a>
            </li>
<!--            <li class="--><?php //if($this->uri->segment(2)=='human-resources'){echo"current";}?><!--">-->
<!--                <a href="--><?php //echo base_url('human-resources/lecturers');?><!--">-->
<!--                    <i class="fa fa-download"></i>-->
<!--                    Master Dosen-->
<!--                </a>-->
<!--            </li>-->
<!--            <li class="">-->
<!--                <a href="#">-->
<!--                    <i class="fa fa-download"></i>-->
<!--                    Presensi Dosen-->
<!--                </a>-->
<!--            </li>-->
            <li class="<?php if($this->uri->segment(2)=='monitoring-attendance'){echo"current";}?>">
                <a href="<?php echo base_url('human-resources/monitoring-attendance/with-range-date'); ?>">
                    <i class="fa fa-line-chart"></i>
                    Monitoring Attendance
                </a>
            </li>


            <li class="<?php if($this->uri->segment(2)=='academic_employees'){echo"current";}?>">
                <a href="<?php echo base_url('human-resources/academic_employees');?>">
                    <i class="fa fa-id-card"></i>
                    Master Academic
                </a>
            </li>

            <li class="<?php if($this->uri->segment(2)=='setting_academic_hrd'){echo"current";}?>">
                <a href="<?php echo base_url('human-resources/setting_academic_hrd');?>">
                    <i class="fa fa-cog"></i>
                    Setting Academic
                </a>
            </li>

            <!-- ADDED BY FEBRI @ JAN 2020 -->
            <li class="<?php if($this->uri->segment(2)=='master_insurance_company'){echo"current";}?>">
                <a href="<?php echo base_url('human-resources/master_insurance_company');?>" title="Master Insurance Company">
                    <i class="fa fa-medkit"></i>
                    Insurance Company
                </a>
            </li>
            <!-- END ADDED BY FEBRI @ JAN 2020 -->

            <!-- ADDED BY FEBRI @ FEB 2020 -->
            <li class="<?= ($this->uri->segment(2)=='master-aphris') ? 'current open' : ''?>">
                <a href="javascript:void(0);">
                    <i class="fa fa-user-circle"></i>
                    Master Aphris
                    <i class="arrow <?= ($this->uri->segment(2)=='master-aphris') ? 'icon-angle-down' : 'icon-angle-left'?>"></i></a>
                    <?php $tablename = array("master_status","master_level","master_industry_type","master_company"); ?>
                    <ul class="sub-menu">
                        <?php foreach ($tablename as $tb) { ?>
                        <li class="<?= ($this->uri->segment(3)==$tb) ? 'current' : ''?>">
                            <a href="<?=site_url('human-resources/master-aphris/'.$tb)?>">
                                <i class="icon-angle-right"></i>
                                <?php $expl = explode("master_", $tb); echo str_replace("_", " ", strtoupper($expl[1]));?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
            </li>
            <!-- END ADDED BY FEBRI @ FEB 2020 -->


        </ul>



        <div class="sidebar-widget align-center">
            <div class="btn-group" data-toggle="buttons" id="theme-switcher">
                <label class="btn active">
                    <input type="radio" name="theme-switcher" data-theme="bright"><i class="fa fa-sun-o"></i> Bright
                </label>
                <label class="btn">
                    <input type="radio" name="theme-switcher" data-theme="dark"><i class="fa fa-moon-o"></i> Dark
                </label>
            </div>
        </div>

    </div>
    <div id="divider" class="resizeable"></div>
</div>
<!-- /Sidebar -->
