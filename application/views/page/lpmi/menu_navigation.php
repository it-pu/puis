<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">

        <!--=== Navigation ===-->
        <ul id="nav">
            <li class="<?php if($this->uri->segment(2)=='slider'){echo"current";}?>">
                <a href="<?php echo base_url('lpmi/slider');?>">
                    <i class="fa fa-desktop"></i>
                    Home Banner
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='vision' || $this->uri->segment(2)=='mission' || $this->uri->segment(2)=='target' || $this->uri->segment(2)=='program' || $this->uri->segment(2)=='event'){echo"current";}?>">
                <a href="">
                    <i class="fa fa-home"></i>
                    About
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='vision'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/vision');?>">
                            <i class="fa fa-low-vision"></i>
                            Vision
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='mission'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/mission');?>">
                            <i class="fa fa-lightbulb-o"></i>
                            Mission
                        </a>
                    </li>
                    <!-- <li class="<?php if($this->uri->segment(2)=='committee'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/committee');?>">
                            <i class="fa fa-group"></i>
                            SPMI Committee
                        </a>
                    </li> -->
                    <li class="<?php if($this->uri->segment(2)=='target'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/target');?>">
                            <i class="fa fa-bolt"></i>
                            Target
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='program'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/program');?>">
                            <i class="fa fa-flag"></i>
                            Program
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='event'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/event');?>">
                            <i class="fa fa-tags"></i>
                            Event
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='news'){echo"current";}?>">
                <a href="<?php echo base_url('lpmi/news');?>">
                    <i class="fa fa-drivers-license"></i>
                    News
                </a>
            </li>
            <li  class="<?php if($this->uri->segment(2)=='knowledge'){echo"current";}?>">
                <a href="#">
                    <i class="fa fa-folder"></i>
                    Document
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='knowledge'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/knowledge');?>">
                            <i class="fa fa-download"></i>
                            Knowledge Base
                        </a>
                    </li>
                    <!-- <li class="<?php if($this->uri->segment(2)=='accreditation'){echo"current";}?>">
                        <a href="<?php echo base_url('lpmi/accreditation');?>">
                            <i class="fa fa-pie-chart"></i>
                            Accriditations
                        </a>
                    </li> -->
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='testimonials'){echo"current";}?>">
                <a href="<?php echo base_url('lpmi/testimonials');?>">
                    <i class="fa fa-comments"></i>
                    Testimonials
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='partner'){echo"current";}?>">
                <a href="<?php echo base_url('lpmi/partner');?>">
                    <i class="fa fa-handshake-o"></i>
                    Partner
                </a>
            </li>


            <li class="<?php if($this->uri->segment(2)=='list-lecturer'){echo"current";}?>">
                <a href="<?php echo base_url('lpmi/lecturer-evaluation/list-lecturer');?>">
                    <i class="fa fa-pie-chart"></i>
                    Lecturer Evaluation
                </a>
            </li>

            <li class="<?php if($this->uri->segment(2)=='version'){echo"current";}?>">
                <a href="<?php echo base_url('survey/list-survey');?>">
                    <i class="fa fa-tasks"></i>
                    Survey
                </a>
            </li>
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
<?php
$this->m_menu3lpmi->checkAuth_user('db_lpmi');
?>
