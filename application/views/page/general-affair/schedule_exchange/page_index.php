
<?php

    $arr_menu = ['ga_schedule_exchange'];

?>

<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
        <li class="<?php if(in_array($this->uri->segment(1),$arr_menu)) { echo 'active'; } ?>">
            <a href="<?php echo base_url('ga_schedule_exchange'); ?>">Schedule Exchange</a>
        </li>
    </ul>
    <div style="border-top: 1px solid #cccccc">

        <div class="row">
            <div class="col-md-12" style="margin-top: 30px;">
                <?php echo $page; ?>
            </div>
        </div>

    </div>
</div>