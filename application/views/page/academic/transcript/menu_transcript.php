
<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
        <li class="<?php if($this->uri->segment(2)=='transcript' || $this->uri->segment(2)=='setting-transcript') { echo 'active'; } ?>">
            <a href="<?php echo base_url('academic/transcript'); ?>">Transcript</a>
        </li>
    </ul>
    <div style="border-top: 1px solid #cccccc">

        <div class="row">
            <div class="col-md-12">
                <?php echo $page; ?>
            </div>
        </div>

    </div>
</div>