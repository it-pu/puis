

<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
       
        <li class="<?php if($this->uri->segment(3)=='vision' && ($this->uri->segment(4)=='' || $this->uri->segment(4)==null ) )  { echo 'active'; } ?>">
            <a href="<?php echo base_url('prodi/about/vision'); ?>">Vision</a>
        </li>
        <li class="<?php if($this->uri->segment(4)=='mission') { echo 'active'; } ?>">
            <a href="<?php echo base_url('prodi/about/vision/mission'); ?>">Mission</a>
        </li>
        
    </ul>
    <div style="border-top: 1px solid #cccccc"> 

        <div class="row">
            <div class="col-md-12">
                <?php echo $pagevisimisi; ?>
            </div>
        </div>

    </div>
</div>