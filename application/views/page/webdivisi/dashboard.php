
<div id="sidebar" class="sidebar-fixed">

    <!--=== Navigation ===-->
    <div id="sidebar-content" class="" >
        <!--=== Navigation ===-->
        <ul id="nav">
            <!-- <li  class="dropdown <?php if($this->uri->segment(3,0)=='dashboard'){echo "current";} ?>">
                <a href="<?php echo base_url('dashboard'); ?>" class="dropdown-toggle" data-toggle="dropdown"> -->
                    <li class="<?= ($this->uri->segment(2)=='beranda') ? 'current open' : ''; ?>">
                        <a href="javascript:void(0);">
                            <i class="fa fa-tachometer"></i>
                            Home

                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(3)=='slide'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/beranda/slide'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Slider
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(3)=='why-choose-us'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/beranda/why-choose-us'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Why Choose us?
                                </a>
                            </li>
                        <!-- <li class="<?php if($this->uri->segment(3)=='calltoaction'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/beranda/calltoaction'); ?>">
                                <i class="icon-angle-right"></i>
                                Call to action
                                </a>
                            </li> -->

                        <!-- <li class="<?php if($this->uri->segment(3)=='testimoni'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/beranda/testimoni'); ?>">
                                <i class="icon-angle-right"></i>
                                Testimonials
                                </a>
                            </li> -->
                            <!-- <li class="<?php if($this->uri->segment(3)=='client'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/beranda/partner'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Partner Collaborations
                                </a>
                            </li> -->
                        </ul>
                    </li>
                    <li class="<?= ($this->uri->segment(2)=='about') ? 'current open' : ''; ?>">
                        <a href="javascript:void(0);">
                            <i class="fa fa-diamond"></i>
                            About

                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(3)=='overview'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/about/overview'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Greetings
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(3)=='visimisi'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/about/vision'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Vision dan Mission
                                </a>
                            </li>
                        <!-- <li class="<?php if($this->uri->segment(3)=='struktur'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/about/sambutan'); ?>">
                                <i class="icon-angle-right"></i>
                                Organizational Structure
                                </a>
                            </li> -->
                            <li class="<?php if($this->uri->segment(3)=='lecturer'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/about/lecturer'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Team
                                </a>
                            </li>
                        <!-- <li class="<?php if($this->uri->segment(3)=='facilities'){echo "current";} ?>">
                                <a href="<?php echo base_url('webdivisi/about/facilities'); ?>">
                                <i class="icon-angle-right"></i>
                                Facilities
                                </a>
                            </li> -->

                        </ul>
                    </li>
            <!-- <li class="<?php if($this->uri->segment(3)=='choose'){echo "penelitian";} ?>">
                <a href="<?php echo base_url('penelitian'); ?>">
                    <i class="fa fa-flask"></i>
                    Research
                    
                </a>
            </li>
            <li class="<?php if($this->uri->segment(3)=='berita'){echo "current";} ?>">
                <a href="<?php echo base_url('berita'); ?>">
                    <i class="fa fa-newspaper-o"></i>
                    News
                    
                </a>
            </li>
            <li class="<?php if($this->uri->segment(3)=='mahasiswa'){echo "current";} ?>">
                <a href="<?php echo base_url('mahasiswa'); ?>">
                    <i class="fa fa-graduation-cap"></i>
                    Student
                    
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('Penelitian'); ?>">
                    <i class="fa fa-tachometer"></i>
                    Alumni
                    
                </a>
            </li>
            <li class="<?php if($this->uri->segment(3)=='galeri'){echo "current";} ?>">
                <a href="<?php echo base_url('galeri'); ?>">
                    <i class="fa fa-th-large"></i>
                    Gallery
                    
                </a>
            </li> -->
            <li class="<?php if($this->uri->segment(2)=='contact'){echo "current";} ?>">
                <a href="<?php echo base_url('webdivisi/contact'); ?>">
                    <i class="fa fa-phone"></i>
                    Contact
                    
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
    <div id="divider" class="resizeable">

    </div>
    
</div>
<!--<h1>Dashboard</h1>-->

<!--<legend>Data Session</legend>-->
<!--<pre>-->
<!--    --><?php //print_r($this->session->all_userdata()); ?>
<!--</pre>-->
<!-- <pre><?php print_r($this->session->userdata('menu_admission_grouping')) ?></pre> -->
<div class="col-md-8 col-md-offset-2 animated flipInX" style="text-align: center;margin-top: 50px;">
    <img src="<?php echo base_url('images/logo-l.png'); ?>" style="max-width: 200px;">
    <hr/>
    <h4>Dashboard Coming Soon <?php echo $department,- $this->session->userdata('IDdepartementNavigation'); ?></h4>
</div>
