
<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">
        <!--=== Navigation ===-->
        <ul id="nav">
            <!-- list menu -->

            <li class="<?php if($this->uri->segment(1)=='rectorat' && $this->uri->segment(2)=='master_data') {echo"current";}?>">
                <a href="<?php echo base_url('rectorat/master_data/config_jabatan_per_sks');?>">
                    <i class="fa fa-globe"></i> Master Data
                </a>
            </li>
            <li class="<?php if($this->uri->segment(1)=='requestdocument'){echo"current";}?>">
                <a href="<?php echo base_url('rectorat/legalitas/prodi');?>">
                    <i class="fa fa-university"></i> Legalitas Universitas
                </a>
            </li>
            <li class="<?php if($this->uri->segment(1)=='requestdocument'){echo"current";}?>">
                <a href="<?php echo base_url('rectorat/requestdocument');?>">
                    <i class="fa fa-files-o"></i> Request Document
                </a>
            </li>
            <li class="<?php if($this->uri->segment(1)=='rectorat' && $this->uri->segment(2)=='reqsuratmengajar'){echo"current";}?>">
                <a href="<?php echo base_url('rectorat/reqsuratmengajar');?>">
                    <i class="fa fa-book"></i> Surat Mengajar
                </a>
            </li>
            <li class="<?php if($this->uri->segment(1)=='rectorat' && $this->uri->segment(2)=='file_academic_employee'){echo"current";}?>">
                <a href="<?php echo base_url('rectorat/file_academic_employee');?>">
                    <i class="fa fa-file-pdf-o"></i> File Academic Employee
                </a>
            </li>

            <?php $sw = ($_SERVER['SERVER_NAME']=='localhost') ? '' : 'hide'; ?>
            <li class="<?php echo $sw.' '; if($this->uri->segment(1)=='rectorat' && $this->uri->segment(2)=='document-generator'){echo"current";}?>">
                <a href="<?php echo base_url('rectorat/document-generator');?>">
                    <i class="fa fa-archive"></i> Document Generator
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
