

<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
        <!--        <li class="--><?php //if($this->uri->segment(3)=='list-student') { echo 'active'; } ?><!--">-->
        <!--            <a href="--><?php //echo base_url('academic/final-project/list-student'); ?><!--">Final Project</a>-->
        <!--        </li>-->
        <li class="<?php if($this->uri->segment(3)=='why-choose-us' && ($this->uri->segment(4) == '' || $this->uri->segment(4) == null ) ) { echo 'active'; } ?>">
            <a href="<?php echo base_url('webdivisi/beranda/why-choose-us'); ?>">Why Choose</a>
        </li>
        <li class="<?php if($this->uri->segment(4)=='about') { echo 'active'; } ?>">
            <a href="<?php echo base_url('webdivisi/beranda/why-choose-us/about'); ?>">About</a>
        </li>
        <!-- <li class="<?php if($this->uri->segment(4)=='excellence') { echo 'active'; } ?>">
            <a href="<?php echo base_url('webdivisi/beranda/why-choose-us/excellence'); ?>">Excellence</a>
        </li> -->

        <!-- <li class="<?php if($this->uri->segment(4)=='graduate-profile') { echo 'active'; } ?>">
            <a href="<?php echo base_url('webdivisi/beranda/why-choose-us/graduate-profile'); ?>">Graduate Profile</a>
        </li> -->
        <!-- <li class="<?php if($this->uri->segment(4)=='career-opportunities') { echo 'active'; } ?>">
            <a href="<?php echo base_url('webdivisi/beranda/why-choose-us/career-opportunities'); ?>">Career Opportunities</a>
        </li> -->
    </ul>
    <div style="border-top: 1px solid #cccccc">

        <div class="row">
            <div class="col-md-12">
                <?php echo $page; ?>
            </div>
        </div>

    </div>
</div>