
<div id="sidebar" class="sidebar-fixed">

    <!--=== Navigation ===-->
    <div id="sidebar-content" class="" >
        <!--=== Navigation ===-->
        <ul id="nav">
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
                    
                    </li>
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

                    </ul>
                </li>  
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
    <h4>Dashboard Coming Soon </h4>
</div>
